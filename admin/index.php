<?php
session_start();
error_reporting (0);

// Set paths/options..
define ('PATH', dirname(__FILE__).'/');
define ('REL_PATH', '../');
define ('PARENT', 1);

include(REL_PATH.'control/defined.inc.php');
include(PATH.'control/defined.inc.php');
include(PATH.'classes/PaginateIt.php');
include(REL_PATH.'control/functions.php');
include(PATH.'control/functions.php');
include(REL_PATH.'control/connect.inc.php');
include(PATH.'classes/categories.php');
include(PATH.'classes/recipes.php');
include(PATH.'classes/system.php');
include(PATH.'classes/class_mail.inc.php');
include(PATH.'classes/ratings.php');

@mysql_query("SET CHARACTER SET 'utf8'");
@mysql_query("SET NAMES 'utf8'");

$SETTINGS = mysql_fetch_object(
            mysql_query("SELECT * FROM ".$database['prefix']."settings LIMIT 1")
            );
            
// Load language file..
include(REL_PATH.'lang/'.$SETTINGS->language);

// Initialise vars..
$cmd                 = (isset($_GET['p']) ? strip_tags($_GET['p']) : 'home');
$page                = (isset($_GET['next']) ? $_GET['next'] : 1);
$limit               = $page * PER_PAGE - (PER_PAGE);
$pageTitle           = $script333.' '.$header;
$count               = 0;

// Create class objects..
$MRMAIL              = new mailClass();
$MRCAT               = new cats();
$MRRAT               = new ratings();
$MRREC               = new recipes();
$MRSYS               = new systemFunctions();
$MRCAT->prefix       = $database['prefix'];
$MRREC->prefix       = $database['prefix'];
$MRSYS->prefix       = $database['prefix'];
$MRRAT->prefix       = $database['prefix'];
$MRRAT->settings     = $SETTINGS;
$MRMAIL->smtp        = $SETTINGS->smtp;
$MRMAIL->smtp_host   = $SETTINGS->smtp_host;
$MRMAIL->smtp_user   = $SETTINGS->smtp_user;
$MRMAIL->smtp_pass   = $SETTINGS->smtp_pass;
$MRMAIL->smtp_port   = $SETTINGS->smtp_port;
$MRMAIL->addTag('{WEBSITE_NAME}',$SETTINGS->website);
$MRMAIL->addTag('{WEBSITE_URL}',$SETTINGS->install_path);
$MRMAIL->addTag('{WEBSITE_EMAIL}',$SETTINGS->email);
$MRMAIL->addTag('{DATE}',date("j F Y",strtotime(SERVER_TIME_ADJUSTMENT)));

switch ($cmd) {
  case "home":

  isWebmasterLoggedIn();

  // Run some security checks..
  $sec = array();
  if (is_dir(REL_PATH.'install/')) {
    $sec[] = $script18;
  }
  if ($database['cookieName']=='mr_cookie') {
    $sec[] = $script19;
  }
  if ($database['cookieKey']=='hfgfyf[]f[9874hg36g88sgshgyghtythfdt00kfte') {
    $sec[] = $script20;
  }

  include(PATH.'templates/header.php');
  include(PATH.'templates/home.php');
  include(PATH.'templates/footer.php');
  break;

  case "cats":

  isWebmasterLoggedIn();

  if (isset($_POST['process'])) {
    $MRCAT->addCat();
    $OK = true;
  }
  
  if (isset($_POST['edit'])) {
    $MRCAT->updateCat();
    $OK2 = true;
  }
  
  if (isset($_GET['del'])) {
    $MRCAT->deleteCat($SETTINGS);
    $OK3 = true;
  }
  
  $pageTitle = $header9.': '.$pageTitle;
  
  include(PATH.'templates/header.php');
  include(PATH.'templates/cats.php');
  include(PATH.'templates/footer.php');
  break;

  case "add":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    if($_POST['name'] && $_POST['ingredients'] && $_POST['instructions']) {
      // Add new recipe..
      $newID = $MRREC->addRecipe();
      // Update cloud tags..
      $MRREC->rebuildAdminCloudTags($_POST['ingredients'],$_POST['instructions'],$newID);
      // Upload images..
      for ($i=0; $i<count($_FILES['image']['tmp_name']); $i++) {
        $temp = $_FILES['image']['tmp_name'][$i];
        $name = $_FILES['image']['name'][$i];
        if ($temp && $name) {
          $MRREC->addNewPicture($name,$temp,$newID,$SETTINGS);
        }
      }
      $OK = true;
    } else {
      header("Location ?p=add");
      exit;
    }
  }
  
  $pageTitle = $header2.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/add.php');
  include(PATH.'templates/footer.php');
  break;

  case "edit":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    $MRREC->updateRecipe();
    // Update cloud tags..
    $MRREC->rebuildAdminCloudTags($_POST['ingredients'],$_POST['instructions'],$_GET['id']);
    $OK = true;
  }

  $calendarLoad  = true;
  $pageTitle     = $edit.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/edit.php');
  include(PATH.'templates/footer.php');
  break;
  
  case "approve-comments":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    if (!empty($_POST['comment'])) {
      foreach ($_POST['comment'] AS $value) {
        if ($_POST['approve']=='yes') {
          $MRREC->activateComment($value);
          $OK = true;
        }
        // Send e-mails..
        if ($_POST['email']=='yes') {
          $COMMENT = getTableData('comments','id',$value);
          $RECIPE  = getTableData('recipes','id',$COMMENT->recipe);
          $CAT     = getTableData('categories','id',$RECIPE->cat);
          $MRMAIL->addTag('{NAME}', $COMMENT->leftBy);
          $MRMAIL->addTag('{COMMENT}', $COMMENT->comment);
          $MRMAIL->addTag('{TITLE}', $RECIPE->name);
          if ($SETTINGS->modr=='yes') {
            $MRMAIL->addTag('{URL}', $SETTINGS->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPE->name).'/'.$RECIPE->id.'/index.html');
          } else {
            $MRMAIL->addTag('{URL}', $SETTINGS->install_path.'?p=recipe&recipe='.$RECIPE->id);
          }
          $MRMAIL->sendMail($COMMENT->leftBy,
                            $COMMENT->email,
                            $SETTINGS->website,
                            $SETTINGS->email,
                            '['.$SETTINGS->website.'] '.($_POST['approve']=='yes' ? $comments28 : $comments29),
                            $MRMAIL->template(PATH.'templates/email/'.($_POST['approve']=='yes' ? 'comments-approved.txt' : 'comments-rejected.txt'))
                            );
          // Now delete comment..
          if ($_POST['approve']=='no') {
            $MRREC->rejectComment($value);   
            $OK2 = true; 
          }              
        }
      }
      if (isset($OK)) {
        $OK = true;
      }
      if (isset($OK2)) {
        $OK2 = true;
      }
    } else {
      header("Location: ?p=approve-comments");
      exit;
    }
  }
  
  $loadGreyBox = true;
  $pageTitle   = $recipes11.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/approve-comments.php');
  include(PATH.'templates/footer.php');
  break;
  
  case "edit-comment":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    $MRREC->updateComment();
    $OK = true;
  }
  
  include(PATH.'templates/edit-comment.php');
  break;
  
  case "comments":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    if(!empty($_POST['comment'])) {
      $MRREC->deleteComments();
      $OK = true;
    } else {
      header("Location: ?p=comments&recipe=".$_GET['recipe']);
      exit;
    }
  }
  
  $loadGreyBox = true;
  $pageTitle   = $recipes11.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/comments.php');
  include(PATH.'templates/footer.php');
  break;
  
  case "ratings":

  isWebmasterLoggedIn();
  
  $ratingLoad  = true;
  $pageTitle   = $recipes49.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/ratings.php');
  include(PATH.'templates/footer.php');
  break;
  
  case "approve-recipes":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    if (!empty($_POST['recipe'])) {
      foreach ($_POST['recipe'] AS $value) {
        // Send e-mails..
        if ($_POST['email']=='yes') {
          $RECIPE  = getTableData('recipes','id',$value);
          $CAT     = getTableData('categories','id',$RECIPE->cat);
          $MRMAIL->addTag('{NAME}', $RECIPE->submitted_by);
          $MRMAIL->addTag('{TITLE}', $RECIPE->name);
          if ($SETTINGS->modr=='yes') {
            $MRMAIL->addTag('{URL}', $SETTINGS->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPE->name).'/'.$RECIPE->id.'/index.html');
          } else {
            $MRMAIL->addTag('{URL}', $SETTINGS->install_path.'?p=recipe&recipe='.$RECIPE->id);
          }
          $MRMAIL->sendMail($RECIPE->submitted_by,
                            $RECIPE->email,
                            $SETTINGS->website,
                            $SETTINGS->email,
                            '['.$SETTINGS->website.'] '.($_POST['approve']=='yes' ? $recipes47 : $recipes48),
                            $MRMAIL->template(PATH.'templates/email/'.($_POST['approve']=='yes' ? 'recipe-approved.txt' : 'recipe-rejected.txt'))
                            );
        }
        if ($_POST['approve']=='yes') {
          $MRREC->activateRecipe($value);
        } else {
          $MRREC->rejectRecipe($value,$SETTINGS);
        } 
      }
      if ($_POST['approve']=='yes') {
        $OK = true;
      } else {
        $OK2 = true;
      }
    } else {
      header("Location: ?p=approve-recipes");
      exit;
    }
  }
  
  $pageTitle = $recipes14.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/approve-recipes.php');
  include(PATH.'templates/footer.php');
  break;
  
  case "pictures":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    $imgRun = 0;
    for ($i=0; $i<count($_FILES['image']['tmp_name']); $i++) {
      $name = $_FILES['image']['name'][$i];
      $temp = $_FILES['image']['tmp_name'][$i];
      if ($name && $temp) {
        $MRREC->addNewPicture($name,$temp,$_GET['recipe'],$SETTINGS);
        ++$imgRun;
      }
    }
    if ($imgRun>0) {
      $OK = true;
    } else {
      header("Location: index.php?p=pictures");
      exit;
    }
  }
  
  if (isset($_GET['allpics'])) {
    $MRREC->deleteAllPictures($SETTINGS);
    $OK3 = true;
  }
  
  if (isset($_GET['picture'])) {
    $MRREC->deletePicture($SETTINGS);
    $OK2 = true;
  }
  
  // Load enlargeit..
  $imgLoad = true;
  
  $pageTitle = $recipes34.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/pictures.php');
  include(PATH.'templates/footer.php');
  break;

  case "settings":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    $MRSYS->updateSettings();
    $OK = true;
  }

  $pageTitle = $header4.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/settings.php');
  include(PATH.'templates/footer.php');
  break;

  case "recipes":

  isWebmasterLoggedIn();
  
  if (isset($_POST['process'])) {
    if (!empty($_POST['recipe'])) {
      $OK = true;
    } else {
      header("Location: ?p=recipes");
      exit;
    }
  }
  
  if (isset($_POST['process'])) {
    if (!empty($_POST['recipe'])) {
      $MRREC->deleteRecipe($SETTINGS);
      $OK = true;
    } else {
      header("Location: ?p=recipes");
      exit;
    }
  }
  
  if (isset($_POST['reset'])) {
    if (!empty($_POST['cats'])) {
      if (isset($_POST['hits']) || isset($_POST['ratings']) || isset($_POST['delcom']) || isset($_POST['delrec'])) { 
        if (isset($_POST['delrec'])) {
          $MRREC->deleteRecipe($SETTINGS,true);
        } else {
          if (isset($_POST['hits'])) {
            $MRREC->resetRecipeHits();
          }
          if (isset($_POST['ratings'])) {
            $MRREC->resetRecipeRatings();
          }
          if (isset($_POST['delcom'])) {
            $MRREC->deleteAllRecipeComments();
          }
        }
        $OK2 = true;
      } else {
        header("Location: ?p=recipes");
        exit;
      }
    } else {
      header("Location: ?p=recipes");
      exit;
    }
  }

  $pageTitle = $header3.': '.$pageTitle;

  include(PATH.'templates/header.php');
  include(PATH.'templates/recipes.php');
  include(PATH.'templates/footer.php');
  break;

  case "login":
  
  isWebmasterLoggedIn(true);
  
  if (isset($_POST['process'])) {
    $_POST = array_map('trim',$_POST);
    $_POST = array_map('cleanEvilTags',$_POST);
    if ($_POST['user'] && $_POST['pass']) {
      if ($_POST['user']!=USERNAME) {
        $U_ERROR = true;
        $count++;
      }
      if (encrypt($database['cookieKey'].$_POST['pass'])!=encrypt($database['cookieKey'].PASSWORD)) {
        $P_ERROR = true;
        $count++;
      }
      if ($count==0) {
        // Set session var..
        $_SESSION[encrypt($database['cookieKey'])] = encrypt($database['cookieKey'].$_POST['user']);
        // Set cookie..
        if (isset($_POST['cookie'])) {
          setcookie(encrypt($database['cookieKey']).$database['cookieName'], encrypt($database['cookieKey'].$_POST['user']), time()+60*60*24*30);
        }
        header("Location: index.php");
        exit;
      }
    } else {
      header("Location: index.php?p=login");
      exit;
    }
  }
  
  include(PATH.'templates/login.php');
  break;

  case "logout":
  
  session_unset();
  session_destroy();
  unset($_SESSION[encrypt($database['cookieKey'])]);
  unset($_SESSION);
  if (isset($_COOKIE[encrypt($database['cookieKey']).$database['cookieName']])) {
    setcookie(encrypt($database['cookieKey']).$database['cookieName'],'');
    unset($_COOKIE[encrypt($database['cookieKey']).$database['cookieName']]);
  }
  header("Location: index.php?p=login");

  break;
}

?>
