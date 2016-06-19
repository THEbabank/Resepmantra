<?php
// Error reporting level...E_ALL for development..
error_reporting(0);

// Set path to recipe folder
define ('PATH', dirname(__FILE__).'/');
define ('PARENT', 1);

// Database connection..
include(PATH.'control/connect.inc.php');
 
// Collation..
@mysql_query("SET CHARACTER SET 'utf8'");
@mysql_query("SET NAMES 'utf8'");

// Load Settings Data..
// We can mask the error thrown here and redirect index file to installer..
$SETTINGS = @mysql_fetch_object(
             mysql_query("SELECT * FROM ".$database['prefix']."settings LIMIT 1")
             );
             
// Check installer..
if (!isset($SETTINGS->language)) {
  header("Location: install/index.php");
  exit;
}    

// Load include files..
include(PATH.'control/functions.php');
include(PATH.'control/Savant2.php');
include(PATH.'classes/class_mail.inc.php');
include(PATH.'classes/recipes.php');
include(PATH.'classes/ratings.php');
include(PATH.'classes/flash-header.php');
include(PATH.'classes/class_rss.inc.php');
include(PATH.'control/defined.inc.php');
include(PATH.'classes/PaginateIt.php');
 
//Load language file
include(PATH.'lang/'.$SETTINGS->language);

// Check .htaccess file is in place if mod_rewrite is enabled..
if ($SETTINGS->modr=='yes' && !file_exists(PATH.'.htaccess')) {
  echo $script22;
  exit;
} 

// Variables
$cmd                  = (isset($_GET['p']) ? strip_tags($_GET['p']) : 'home');
$page                 = (isset($_GET['next']) && ctype_digit($_GET['next']) ? $_GET['next'] : '1');
$limit                = $page * $SETTINGS->total - ($SETTINGS->total);
$count                = 0;
$loadJS               = array();
$formErrors           = array();
$eImgError            = array();

// Create class objects..
$MRMAIL               = new mailClass();
$MRREC                = new recipes();
$MRRAT                = new ratings();
$MRFLASH              = new flashHeader();
$MRFEED               = new rssFeed();
$MRREC->prefix        = $database['prefix'];
$MRREC->settings      = $SETTINGS;
$MRRAT->prefix        = $database['prefix'];
$MRRAT->settings      = $SETTINGS;
$MRFLASH->settings    = $SETTINGS;
$MRFEED->prefix       = $database['prefix'];
$MRFEED->settings     = $SETTINGS;
$MRFEED->thisFeedUrl  = $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'rss-feed.html' : '?p=rss-feed');
$MRMAIL->smtp         = $SETTINGS->smtp;
$MRMAIL->smtp_host    = $SETTINGS->smtp_host;
$MRMAIL->smtp_user    = $SETTINGS->smtp_user;
$MRMAIL->smtp_pass    = $SETTINGS->smtp_pass;
$MRMAIL->smtp_port    = $SETTINGS->smtp_port;
$MRMAIL->addTag('{WEBSITE_NAME}',$SETTINGS->website);
$MRMAIL->addTag('{WEBSITE_URL}',$SETTINGS->install_path);
$MRMAIL->addTag('{WEBSITE_EMAIL}',$SETTINGS->email);
$MRMAIL->addTag('{DATE}',date("j F Y",strtotime(SERVER_TIME_ADJUSTMENT)));
$MRMAIL->addTag('{IP}', getRealIPAddr());

// Default text for title..
$headerTitleText   = str_replace('{website_name}',cleanData($SETTINGS->website),$ps_header9);

// If comments are enabled and being added, a post request is sent via ajax..
// Tell system which area to load..
if (isset($_POST['r'])) {
  $cmd='add-comments';
}

// Main processing switch statement..
switch ($cmd) {
   // templates/home.tpl.php
   case 'home':
   case '404':
   
   // Redirect for search..
   if (isset($_POST['search'])) {
     // Clean the form input..
     $_POST    = multiDimensionalArrayMap('cleanEvilTags',$_POST);
     $newKeys  = $_POST['keys'];
     if ($newKeys && $newKeys!=$ps_header13) {
       if ($SETTINGS->modr=='yes') {
         header("Location: ".$SETTINGS->install_path."search-free-recipes/".urlencode(cleanData($newKeys))."/all/1/index.html");
       } else {
         header("Location: ?p=search-free-recipes&keys=".urlencode(cleanData($newKeys))."&cat=all");
       }
       exit;
     }
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   include(PATH.'control/header.inc.php');

   $tpl = new Savant2();
   $tpl->assign('WELCOME_TXT', ($cmd=='404' ? $ps_index6 : str_replace('{website_name}',cleanData($SETTINGS->website),$ps_index)));
   $tpl->assign('WELCOME_MSG', ($cmd=='404' ? $ps_index7 : $ps_index2));
   $tpl->assign('CATEGORIES_TXT', $ps_index3);
   $tpl->assign('CATEGORIES_LEFT', $MRREC->buildCategories('left'));
   $tpl->assign('CATEGORIES_RIGHT', $MRREC->buildCategories('right'));
   $tpl->assign('LATEST_RECIPES_TXT', $ps_index4);
   $tpl->assign('LATEST_RECIPES', $MRREC->showLatestAndMostPopularLinks('latest'));
   $tpl->assign('MOST_POPULAR_TXT', $ps_index5);
   $tpl->assign('MOST_POPULAR', $MRREC->showLatestAndMostPopularLinks('popular'));
   $tpl->assign('FILTER_BY_TXT', $ps_search4);
   $tpl->assign('LOAD_CONTRIBUTORS', $MRREC->loadContributorsList(''));
   $tpl->assign('HEAD_CLASS', ($cmd=='404' ? 'errorHeadLeft' : 'headLeft'));
   $tpl->display('templates/index.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/recipe.tpl.php
   case 'recipe':
   case 'rating':
   case 'add-comments':
   
   // Load ibox...
   $loadJS[] = 'recipes';
   
   // Overwrite var if voting..
   if ($cmd=='rating') {
     $_GET['recipe'] = $_GET['r'];
   }
   
   // Overwrite var if adding comment..
   if ($cmd=='add-comments') {
     $_GET['recipe'] = $_POST['r'];
   }
   
   if (!isset($_GET['recipe'])) {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   // Check query var digit..
   checkDigit($_GET['recipe']);
   
   // Get recipe/category information..
   $RECIPE  = getTableData('recipes','id',$_GET['recipe']);
   $CAT     = getTableData('categories','id',$RECIPE->cat);
   
   // Does recipe exist..
   if (!isset($RECIPE->id)) {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
     exit;
   }
   
   // Increment hit count..
   if (ENABLE_HIT_COUNT) {
     $MRREC->hitCounter();
   }
   
   // Is category parent..
   if ($CAT->isParent=='no') {
     $thisParent = getTableData('categories','id',$CAT->childOf);
   }
   
   // Is recipe enabled..if not, go back to homepage..
   // You can pop an alternative redirect here or display a 404..
   if ($RECIPE->enRecipe=='no' || $RECIPE->isApproved=='yes') {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   // Overwrite meta data..
   // Recipe overwrites category, which in turn overwrites default..
   $metaKeys = ($RECIPE->metaKeys ? $RECIPE->metaKeys : $CAT->metaKeys);
   $metaDesc = ($RECIPE->metaDesc ? $RECIPE->metaDesc : $CAT->metaDesc);
   if ($metaKeys) {
     $overRideMetaKeys = cleanData($metaKeys);
   }
   if ($metaDesc) {
     $overRideMetaDesc = cleanData($metaDesc);
   }
   
   // Are comments enabled?
   // This handles the ajax responses and sends back the data in xml format..
   if ($CAT->enComments=='yes' && $RECIPE->enComments=='yes') {
     if (isset($_POST['r'])) {
       if ($_POST['name']=='' || $_POST['comments']=='' || $_POST['ctct']=='' || (isset($_POST['code']) && $_POST['code']=='')) {
         $MRREC->xmlResponse('<field>all-fields</field>
                              <ibox>'.(USE_IBOX_FOR_COMMENTS ? 'yes' : 'no').'</ibox>
                              <captcha>'.(isset($_POST['code']) ? 'yes' : 'no').'</captcha>
                              <message>'.htmlspecialchars($javascript66).'</message>'); ++$count;
       } else {
         if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $_POST['ctct'])) {
           $MRREC->xmlResponse('<field>invalid-email</field>
                                <ibox>'.(USE_IBOX_FOR_COMMENTS ? 'yes' : 'no').'</ibox>
                                <captcha>'.(isset($_POST['code']) ? 'yes' : 'no').'</captcha>
                                <message>'.htmlspecialchars($javascript67).'</message>'); ++$count;
         } else {
           if (isset($_POST['code'])) {
             include(PATH.'captcha/securimage.php');
             $img    = new Securimage();
             $valid  = $img->check($_POST['code']);
             if($valid == false) {
               $MRREC->xmlResponse('<field>invalid-word</field>
                                    <ibox>'.(USE_IBOX_FOR_COMMENTS ? 'yes' : 'no').'</ibox>
                                    <captcha>'.(isset($_POST['code']) ? 'yes' : 'no').'</captcha>
                                    <message>'.htmlspecialchars($javascript69).'</message>'); ++$count;
             }
           }
         }
       }
       if ($count==0) {
         // Add comments to database..
         $MRREC->addCommentsToDatabase();
         // Send e-mails..
         foreach ($_POST AS $key => $value) {
           $MRMAIL->addTag('{'.strtoupper($key).'}',$value);
         }
         $RECIPE  = getTableData('recipes','id',$_POST['r']);
         $MRMAIL->addTag('{TITLE}', $RECIPE->name);
         // Send to webmaster..
         $MRMAIL->sendMail($SETTINGS->website,
                           $SETTINGS->email,
                           $_POST['name'],
                           $_POST['ctct'],
                           '['.$SETTINGS->website.'] '.$ps_recipe37,
                           $MRMAIL->template(PATH.'templates/email/new-comment.txt')
                           );
         // Send auto responder if enabled..
         if (ENABLE_COMMENT_AUTO_RESPONDER) {
           $MRMAIL->sendMail($_POST['name'],
                             $_POST['ctct'],
                             $SETTINGS->website,
                             $SETTINGS->email,
                             '['.$SETTINGS->website.'] '.$ps_recipe38,
                             $MRMAIL->template(PATH.'templates/email/auto-comments'.($SETTINGS->enCommApp=='yes' ? '-approve' : '').'.txt')
                             );     
         } 
         // Send xml ok message..
         $MRREC->xmlResponse('<field>ok</field>
                              <ibox>'.(USE_IBOX_FOR_COMMENTS ? 'yes' : 'no').'</ibox>
                              <captcha>'.(isset($_POST['code']) ? 'yes' : 'no').'</captcha>
                              <message>'.($SETTINGS->enCommApp=='yes' ? htmlspecialchars($javascript71) : htmlspecialchars($javascript70)).'</message>');
       }
       exit;
     }
     // Load javascript for comments..
     $loadJS[]        = 'comments';
     // Load comment div..
     $loadCommentDiv  = true;
   }
   
   // Are ratings enabled?
   if ($CAT->enRating=='yes' && $RECIPE->enRating=='yes') {
     // Load ratings js/css..
     $loadJS[] = 'ratings';
     if ($cmd=='rating') {
       switch ($_GET['rsystem']) {
         case 'reg':
         echo $MRRAT->outputRating();
         break;
         case 'rbar':
         echo $MRRAT->registerRating();
         break;
       }
       exit;
     }
   }
   
   // Rewrite page title..
   $headerTitleText = str_replace(array('{recipe}','{category}','{website_name}'),
                                  array(cleanData($RECIPE->name),cleanData($CAT->catname),$SETTINGS->website),
                                  $ps_header11
                                  );
                                  
   // Load pictures js..                               
   if (rowCount('pictures', ' WHERE recipe = \''.$_GET['recipe'].'\'')>0) {
     $loadJS[] = 'pictures';
   }                           
   
   // Breadcrumb links for categories..
   if (isset($thisParent->catname)) {
     $breadCrumbs = str_replace(array('{purl}','{parent}','{curl}','{child}'),
                                array($SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'category/'.seoUrl(cleanData($thisParent->catname)).'/'.$thisParent->id.'/1/index.html' : '?p=category&amp;cat='.$thisParent->id),
                                      cleanData($thisParent->catname),
                                      $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'category/'.seoUrl(cleanData($thisParent->catname)).'/'.seoUrl(cleanData($CAT->catname)).'/'.$CAT->id.'/1/index.html' : '?p=category&amp;cat='.$CAT->id),
                                      cleanData($CAT->catname)
                                ),
                                $ps_recipe42);
   } else {
     $breadCrumbs = str_replace(array('{url}','{cat}'),
                                array($SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'category/'.seoUrl(cleanData($CAT->catname)).'/'.$CAT->id.'/1/index.html' : '?p=category&amp;cat='.$CAT->id),
                                      cleanData($CAT->catname)
                                ),
                                $ps_recipe41);
   }
   
   $openGraph = '';
   $qOG       = mysql_query("SELECT `picPath` FROM ".$database['prefix']."pictures
                WHERE recipe = '".(int)$_GET['recipe']."'
                ORDER BY rand()
				LIMIT 1
                ");
   $OG_PIC = mysql_fetch_object($qOG);
   if (isset($OG_PIC->picPath)) {
     $openGraph = $SETTINGS->install_path.'templates/images/recipes/'.$OG_PIC->picPath;
   }
   
   include(PATH.'control/header.inc.php');
   
   // For contributor url..
   if ($SETTINGS->modr=='yes') {
     $conUrl = $SETTINGS->install_path.'search-free-recipes/'.urlencode(cleanData($RECIPE->submitted_by)).'/all/1/index.html';
   } else {
     $conUrl = '?p=search-free-recipes&amp;keys='.urlencode(cleanData($RECIPE->submitted_by)).'&amp;cat=all';
   }

   $tpl = new Savant2();
   $tpl->assign('RECIPE', $RECIPE);
   $tpl->assign('CAT', $CAT);
   $tpl->assign('RECIPE_LINKS_TXT', $ps_recipe);
   $tpl->assign('RATING_SYSTEM', ($CAT->enRating=='yes' && $RECIPE->enRating=='yes' ? $MRRAT->buildRatingSystem($ps_recipe2,$_GET['recipe']) : ''));
   $tpl->assign('ADD_COMMENT', ($CAT->enComments=='yes' && $RECIPE->enComments=='yes' ? $MRREC->buildAddCommentsForm($ps_recipe3) : ''));
   $tpl->assign('JUMP_TO_CATEGORY_TXT', $ps_recipe4);
   $tpl->assign('RECIPE_SELECTION_TXT', $ps_recipe5);
   $tpl->assign('PICTURES_TXT', $ps_recipe6);
   $tpl->assign('SHOW_PICTURES', $MRREC->buildRecipePictures($ps_recipe18));
   $tpl->assign('VISITOR_COMMENTS', ($CAT->enComments=='yes' && $RECIPE->enComments=='yes' ? $MRREC->buildRecipeComments($ps_recipe15,$ps_recipe16,$ps_recipe7) : ''));
   $tpl->assign('CLOUD_TAGS', ($SETTINGS->enCloudTags=='yes' ? $MRREC->buildRecipeCloudTags($ps_recipe17) : ''));
   $tpl->assign('HITS_TXT', (ENABLE_HIT_COUNT ? $ps_recipe19 : '&nbsp;'));
   $tpl->assign('HITS', (ENABLE_HIT_COUNT ? $MRREC->buildRecipeHitCount($RECIPE->hits) : ''));
   $tpl->assign('CONTACT_US_TXT', $ps_recipe8);
   $tpl->assign('PRINT_FRIENDLY_TXT', $ps_recipe10);
   $tpl->assign('TELL_A_FRIEND_TXT', $ps_recipe9);
   $tpl->assign('DATE_BAR_TXT', ($RECIPE->submitted_by ? $ps_recipe34.' <span class="highlight"><a href="'.$conUrl.'" title="'.cleanData($RECIPE->submitted_by).'">'.cleanData($RECIPE->submitted_by).'</a></span> | ' : '').$ps_recipe35.' <span class="highlight">'.$RECIPE->adate.'</span><br />'.$breadCrumbs);
   $tpl->assign('INGREDIENTS_TXT', $ps_recipe11);
   $tpl->assign('INSTRUCTIONS_TXT', $ps_recipe12);
   $tpl->assign('SUBMIT_TXT', $ps_recipe14);
   $tpl->assign('S_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'add-recipe/'.$RECIPE->cat.'/index.html' : '?p=add-recipe&amp;cat='.$RECIPE->cat));
   $tpl->assign('C_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'contact-recipe/'.$_GET['recipe'].'/index.html' : '?p=contact-recipe&amp;recipe='.$_GET['recipe']));
   $tpl->assign('F_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'tell-a-friend/'.$_GET['recipe'].'/index.html' : '?p=tell-a-friend&amp;recipe='.$_GET['recipe']));
   $tpl->assign('P_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'print/'.$_GET['recipe'].'/index.html' : '?p=print&amp;recipe='.$_GET['recipe']));
   $tpl->assign('LOAD_CATEGORIES', $MRREC->loadCategorySelect());
   $tpl->assign('OTHER_RECIPES_IN_CATEGORY', $MRREC->otherRecipesInThisCategory($ps_recipe13,$RECIPE->cat));
   $tpl->display('templates/recipe.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/category.tpl.php
   case 'category':
   
   // Check query var digit..
   checkDigit($_GET['cat']);
   
   // Get cat data..
   $CAT = getTableData('categories','id',$_GET['cat']);
   
   // Does cat exist..
   if (!isset($CAT->id)) {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
     exit;
   }
   
   // Is category enabled..
   if ($CAT->enCat=='no') {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
     exit;
   }
   
   // Is category parent..
   if ($CAT->isParent=='no') {
     $thisParent = getTableData('categories','id',$CAT->childOf);
     // Is category enabled..
     if ($thisParent->enCat=='no') {
       header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
       exit;
     }
   }
   
   // Overwrite meta data..
   if ($CAT->metaKeys) {
     $overRideMetaKeys = cleanData($CAT->metaKeys);
   }
   if ($CAT->metaDesc) {
     $overRideMetaDesc = cleanData($CAT->metaDesc);
   }
   
   // Display based on whether cat is parent or child..
   $headerTitleText = (isset($thisParent->catname) ? 
                       str_replace(array('{cat}','{subcat}'),array(cleanData($CAT->catname),cleanData($thisParent->catname)),$ps_category2) : 
                       str_replace('{cat}',cleanData($CAT->catname),$ps_category)).' '.$headerTitleText;
   
   // Recipe count..
   $rCount = rowCount('recipes',' WHERE cat = \''.$_GET['cat'].'\' AND enRecipe = \'yes\' AND isApproved = \'no\'');
   
   // Also include child recipe count for parents?
   if (!isset($thisParent->catname) && PARENT_WITH_CHILD_COUNT) {
     $ourChildren = $MRREC->getChildrenOfParent($_GET['cat']);
     $rCount = $rCount+(!empty($ourChildren) ? rowCount('recipes',' WHERE cat IN ('.implode(',',$ourChildren).') AND enRecipe = \'yes\' AND isApproved = \'no\'') : 0);
   }
   
   // Determine pagination..
   if ($SETTINGS->modr=='yes') {
     if (isset($thisParent->catname)) {
       $link = $SETTINGS->install_path.'category/'.seoUrl(cleanData($thisParent->catname)).'/'.seoUrl(cleanData($CAT->catname)).'/'.$CAT->id.'/{page}/index.html';
     } else {
       $link = $SETTINGS->install_path.'category/'.seoUrl(cleanData($CAT->catname)).'/'.$CAT->id.'/{page}/index.html';
     }
     $pNumbers = publicPageNumbers($rCount,$SETTINGS->total,$page,$link);
   } else {
     $pNumbers = publicPageNumbers($rCount,$SETTINGS->total,$page,'?p=category&amp;cat='.$_GET['cat'].'&amp;next={page}');
   }
   
   include(PATH.'control/header.inc.php');

   $tpl = new Savant2();
   $tpl->assign('VIEWING_CAT_TXT', str_replace(array('{cat}','{count}'),array((isset($thisParent->catname) ? cleanData($thisParent->catname).' / ' : '').cleanData($CAT->catname),$rCount),$ps_category3));
   $tpl->assign('PLEASE_CHOOSE_TXT', ($rCount>0 ? ($CAT->comments ? cleanData($CAT->comments) : $ps_category6) : $ps_category7));
   $tpl->assign('RECIPES', $MRREC->displayCategoryRecipes());
   $tpl->assign('CAT_RSS', buildCategoryRSSLink($_GET['cat']));
   $tpl->assign('COUNT',$rCount);
   $tpl->assign('PAGE_NUMBERS', $pNumbers);
   $tpl->assign('OTHER_CATS_TXT', $ps_category4);
   $tpl->assign('OTHER_CATS', $MRREC->showRelatedCategories($_GET['cat']));
   $tpl->assign('RECIPE_SELECTION_TXT', $ps_category5);
   $tpl->assign('RECIPE_SELECTION', $MRREC->showMostPopularRecipesForCategoryGroup($_GET['cat']));
   $tpl->display('templates/category.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/add-recipe.tpl.php
   case 'add-recipe':
   
   // Is this enabled..
   if (!$MRREC->checkAddRecipeRule()) {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   if (isset($_POST['process'])) {
     // Remove nasty tags..
     $_POST  = multiDimensionalArrayMap('cleanEvilTags', $_POST);
     // Clean data if magic quotes are on..
     $_POST  = multiDimensionalArrayMap('cleanData', $_POST);
     // Assign instructions and ingredients to new array..
     $s_ingredients   = $_POST['ingredients'];
     $s_instructions  = $_POST['instructions'];
     // Error check..
     if ($_POST['name']=='') {
       $formErrors['name'] = $ps_contact;
     }
     if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $_POST['ctct'])) {
       $formErrors['ctct'] = $ps_contact2;
     }
     if ($_POST['rname']=='') {
       $formErrors['rname'] = $ps_addrecipe21;
     }
     if ($_POST['ingredients']=='') {
       $formErrors['ingredients'] = $ps_addrecipe15;
     }
     if ($_POST['instructions']=='') {
       $formErrors['instructions'] = $ps_addrecipe16;
     }
     // Image error checks..
     if ($SETTINGS->maxImages>0) {
       // If the array count is bigger than the allowed amount of images, something is wrong..
       // It should never be, but you never know..
       // Just redirect back to homepage if something dodgy is going on..
       if (count($_FILES['img']['tmp_name'])>$SETTINGS->maxImages) {
         header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
         exit;
       }
       for ($i=0; $i<count($_FILES['img']['tmp_name']); $i++) {
         $temp  = $_FILES['img']['tmp_name'][$i];
         $name  = $MRREC->filterUploadName($_FILES['img']['name'][$i]);
         $size  = $_FILES['img']['size'][$i];
         $ext   = substr($name,strpos($name,'.')+1,strlen($name));
         if (is_uploaded_file($temp) && $name && $size>0) {
           // Check extension..
           if ($SETTINGS->validImages) {
             $valid = array_map('trim',explode('|',str_replace('.','',strtolower($SETTINGS->validImages))));
             if (!in_array($ext,$valid)) {
               $eImgError['ext'] = 'Faye'; // Value doesn`t matter..
               @unlink($temp);
             }
           }
           // Check file size..
           if ($SETTINGS->maxFileSize>0) {
             if ($size>$SETTINGS->maxFileSize) {
               $eImgError['size'] = 'Ayumi'; // Value doesn`t matter..
               @unlink($temp);
             }
           }
         }
       }
     }
     if (isset($_POST['code'])) {
       include(PATH.'captcha/securimage.php');
       $img    = new Securimage();
       $valid  = $img->check($_POST['code']);
       if($valid == false) {
         $formErrors['word'] = '<span class="formError">'.$ps_contact4.'</span>';
       }
     }
     if (empty($formErrors) && empty($eImgError)) {
       // Add to database..
       $newID = $MRREC->addNewRecipe();
       // Now upload images..
       if ($SETTINGS->maxImages>0) {
         for ($i=0; $i<count($_FILES['img']['tmp_name']); $i++) {
           $temp  = $_FILES['img']['tmp_name'][$i];
           $name  = $MRREC->filterUploadName($_FILES['img']['name'][$i]);
           $size  = $_FILES['img']['size'][$i];
           if ($temp && $name && $size>0 && $newID>0) {
             $MRREC->uploadPicture($temp,$name,$size,$newID);
             ++$count;
           }
         }
       }
       // Build cloud tags..
       buildCloudTags($s_ingredients,$s_instructions,$newID);
       // Send e-mails..
       foreach ($_POST AS $key => $value) {
         $MRMAIL->addTag('{'.strtoupper($key).'}',$value);
       }
       $MRMAIL->addTag('{INGREDIENTS}',$s_ingredients);
       $MRMAIL->addTag('{INSTRUCTIONS}',$s_instructions);
       $CAT = getTableData('categories','id',$_POST['cat']);
       if ($CAT->isParent=='no') {
         $thisParent = getTableData('categories','id',$CAT->childOf);
       }
       $MRMAIL->addTag('{CAT}', (isset($thisParent->catname) ? $thisParent->catname.'/' : '').$CAT->catname);
       $MRMAIL->addTag('{IMG_COUNT}', $count);
       // Send to webmaster..
       $MRMAIL->sendMail($SETTINGS->website,
                         $SETTINGS->email,
                         $_POST['name'],
                         $_POST['ctct'],
                         '['.$SETTINGS->website.'] '.$ps_addrecipe22,
                         $MRMAIL->template(PATH.'templates/email/new-recipe.txt')
                         );
       // Send auto responder if enabled..
       if (ENABLE_RECIPE_AUTO_RESPONDER) {
         $MRMAIL->sendMail($_POST['name'],
                           $_POST['ctct'],
                           $SETTINGS->website,
                           $SETTINGS->email,
                           '['.$SETTINGS->website.'] '.$ps_addrecipe23,
                           $MRMAIL->template(PATH.'templates/email/auto-recipe'.($SETTINGS->enRecApp=='yes' ? '-approve' : '').'.txt')
                           );     
       }
       $OK = true;
     }
   }
   
   $headerTitleText = $ps_header6.': '.$headerTitleText;
   
   include(PATH.'control/header.inc.php');

   $tpl = new Savant2();
   $tpl->assign('IS_SENT', (isset($OK) ? true : false));
   $tpl->assign('A_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'add-recipe.html' : '?p=add-recipe'));
   $tpl->assign('WELCOME_MSG', $ps_addrecipe);
   $tpl->assign('ADD_RECIPE_TXT', $ps_header6);
   $tpl->assign('ADD_RECIPE_TXT2', $ps_addrecipe3);
   $tpl->assign('ADD_RECIPE_TXT3', $ps_addrecipe4);
   $tpl->assign('ADD_RECIPE_TXT4', $ps_addrecipe5);
   $tpl->assign('ADD_RECIPE_TXT5', $ps_addrecipe6);
   $tpl->assign('CATEGORIES', $MRREC->loadCategorySelect(true));
   $tpl->assign('CAPTCHA', buildContactUsCaptcha($ps_contact10,$ps_contact11,(array_key_exists('word',$formErrors) ? $formErrors['word'] : '')));
   $tpl->assign('NAME_TXT', $ps_contact6);
   $tpl->assign('EMAIL_TXT', $ps_contact7.' '.$ps_addrecipe2);
   $tpl->assign('RECIPE_NAME_TXT', $ps_addrecipe7);
   $tpl->assign('CAT_TXT', $ps_addrecipe8);
   $tpl->assign('INGREDIENTS_TXT', $ps_addrecipe9);
   $tpl->assign('INSTRUCTIONS_TXT', $ps_addrecipe10);
   $tpl->assign('SETTINGS', $SETTINGS);
   $tpl->assign('ERRORS', $formErrors);
   $tpl->assign('NAME_ERROR', $ps_contact);
   $tpl->assign('EMAIL_ERROR', $ps_contact2);
   $tpl->assign('RNAME_ERROR', $ps_addrecipe21);
   $tpl->assign('INGREDIENTS_ERROR', $ps_addrecipe15);
   $tpl->assign('INSTRUCTIONS_ERROR', $ps_addrecipe16);
   $tpl->assign('IMG_ERROR', str_replace('{extensions}',str_replace('.','',strtoupper($SETTINGS->validImages)),$ps_addrecipe17));
   $tpl->assign('CODE_ERROR', $ps_contact4);
   $tpl->assign('FORM_ERRORS', htmlspecialchars($javascript3));
   $tpl->assign('PICTURES', ($SETTINGS->maxImages>0 && $SETTINGS->validImages ? $MRREC->buildAddRecipePictures($ps_addrecipe6,$eImgError) : ''));
   $tpl->assign('MESSAGE_SENT', $ps_addrecipe18);
   $tpl->assign('MESSAGE_SENT2', str_replace('{website_name}',cleanData($SETTINGS->website),($SETTINGS->enRecApp=='yes' ? $ps_addrecipe19 : $ps_addrecipe20)));
   $tpl->assign('SEND_TXT', $ps_header6);
   $tpl->display('templates/add-recipe.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/about-us.tpl.php
   case 'about-us':
   
   $headerTitleText = $ps_header7.': '.$headerTitleText;
   
   include(PATH.'control/header.inc.php');

   $tpl = new Savant2();
   $tpl->assign('ABOUT_US_TXT', $ps_header7);
   $tpl->assign('LATEST_RECIPES_TXT', $ps_index4);
   $tpl->assign('LATEST_RECIPES', $MRREC->showLatestAndMostPopularLinks('latest'));
   $tpl->assign('MOST_POPULAR_TXT', $ps_index5);
   $tpl->assign('MOST_POPULAR', $MRREC->showLatestAndMostPopularLinks('popular'));
   $tpl->display('templates/about-us.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/search.tpl.php
   case 'search-free-recipes':
   
   // Get filtering is done by default, so nothing else needed here..
   if (isset($_GET['keys']) && $_GET['keys']) {
     $searchKeys = rawurldecode(cleanData($_GET['keys']));
   } else {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   // Get count..
   $sCount = rowCount('recipes',
                      ' WHERE MATCH(name,instructions,ingredients,submitted_by,metaDesc,metaKeys) AGAINST(\''.safeImport($searchKeys).'\' IN BOOLEAN MODE) AND enRecipe = \'yes\' AND isApproved = \'no\'
                      '.(ctype_digit($_GET['cat']) ? 'AND cat = \''.$_GET['cat'].'\'' : ''));
   
   // Determine pagination..
   if ($SETTINGS->modr=='yes') {
     $link      = $SETTINGS->install_path.'search-free-recipes/'.urlencode($searchKeys).'/'.$_GET['cat'].'/{page}/index.html';
     $pNumbers  = publicPageNumbers($sCount,$SETTINGS->total,$page,$link);
   } else {
     $pNumbers  = publicPageNumbers($sCount,$SETTINGS->total,$page,'?p=search-free-recipes&amp;keys='.urlencode($searchKeys).'&amp;cat='.$_GET['cat'].'&amp;next={page}');
   }
   
   $headerTitleText = $ps_search.': '.$searchKeys.' - '.$headerTitleText;
   
   include(PATH.'control/header.inc.php');

   $tpl = new Savant2();
   $tpl->assign('SEARCH_TXT', $ps_search);
   $tpl->assign('SEARCH_RESULTS_TXT', str_replace(array('{keys}','{count}'),array($searchKeys,$sCount),$ps_search2));
   $tpl->assign('SEARCH_RESULTS', $MRREC->displayCategoryRecipes($searchKeys));
   $tpl->assign('COUNT', $sCount);
   $tpl->assign('PAGE_NUMBERS', $pNumbers);
   $tpl->assign('FILTER_BY_TXT', $ps_search3);
   $tpl->assign('LOAD_CATEGORIES', $MRREC->loadCategorySearchSelect($searchKeys));
   $tpl->assign('FILTER_BY_TXT2', $ps_search4);
   $tpl->assign('LOAD_CONTRIBUTORS', $MRREC->loadContributorsList($searchKeys));
   $tpl->display('templates/search.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/contact-us.tpl.php
   case 'contact-us':
   case 'contact-recipe':
   
   if (isset($_POST['process'])) {
     // Remove nasty tags..
     $_POST  = multiDimensionalArrayMap('cleanEvilTags', $_POST);
     // Clean data if magic quotes are on..
     $_POST  = multiDimensionalArrayMap('cleanData', $_POST);
     // Error check..
     if ($_POST['name']=='') {
       $formErrors['name'] = $ps_contact;
     }
     if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $_POST['ctct'])) {
       $formErrors['ctct'] = $ps_contact2;
     }
     if ($_POST['comments']=='') {
       $formErrors['comments'] = $ps_contact3;
     }
     if (isset($_POST['code'])) {
       include(PATH.'captcha/securimage.php');
       $img    = new Securimage();
       $valid  = $img->check($_POST['code']);
       if($valid == false) {
         $formErrors['word'] = '<span class="formError">'.$ps_contact4.'</span>';
       }
     }
     // End error check..if array empty, process and send..
     if (empty($formErrors)) {
       foreach ($_POST AS $key => $value) {
         $MRMAIL->addTag('{'.strtoupper($key).'}',$value);
       }
       // Send to webmaster..
       $MRMAIL->sendMail($SETTINGS->website,
                         $SETTINGS->email,
                         $_POST['name'],
                         $_POST['ctct'],
                         '['.$SETTINGS->website.'] '.$ps_contact14,
                         $MRMAIL->template(PATH.'templates/email/webmaster.txt')
                         );
       // Send auto responder if enabled..
       if (ENABLE_CONTACT_US_AUTO_RESPONDER) {
         $MRMAIL->sendMail($_POST['name'],
                           $_POST['ctct'],
                           $SETTINGS->website,
                           $SETTINGS->email,
                           '['.$SETTINGS->website.'] '.$ps_contact15,
                           $MRMAIL->template(PATH.'templates/email/auto-responder.txt')
                           );     
       }             
       $OK = true;
     }
   }
   
   // Change data if coming from recipe page..
   if ($cmd=='contact-recipe' && isset($_GET['recipe'])) {
     $RECIPE          = getTableData('recipes','id',$_GET['recipe']);
     // Get link..
     if ($SETTINGS->modr=='yes') {
       $CAT   = getTableData('categories','id',$RECIPE->cat);
       $link  = $SETTINGS->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPE->name).'/'.$RECIPE->id.'/index.html';
     } else {
       $link  = $SETTINGS->install_path.'?p=recipe&amp;recipe='.$RECIPE->id;
     }
     $headerTitleText = $ps_recipe8.': '.cleanData($RECIPE->name).' - '.$headerTitleText;
     // Auto fill in the comments box with the recipe name...
     $_POST['comments'] = str_replace(array('{recipe}','{url}'),
                                      array(cleanData($RECIPE->name),$link),
                                      $ps_contact17);
   } else {
     $headerTitleText = $ps_header8.': '.$headerTitleText;
   }
   
   include(PATH.'control/header.inc.php');

   $tpl = new Savant2();
   if ($cmd=='contact-recipe') {
   $tpl->assign('C_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'contact-recipe/'.$_GET['recipe'].'/index.html' : '?p=contact-recipe&amp;recipe='.$_GET['recipe']));
   } else {
   $tpl->assign('C_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'contact-us.html' : '?p=contact-us'));
   }
   $tpl->assign('NAME_ERROR', $ps_contact);
   $tpl->assign('EMAIL_ERROR', $ps_contact2);
   $tpl->assign('COMMENTS_ERROR', $ps_contact3);
   $tpl->assign('CODE_ERROR', $ps_contact4);
   $tpl->assign('FORM_ERRORS', htmlspecialchars($javascript3));
   $tpl->assign('CONTACT_TXT', (isset($_GET['recipe']) ? str_replace('{recipe}',cleanData($RECIPE->name),$ps_contact16) : $ps_contact5));
   $tpl->assign('NAME_TXT', $ps_contact6);
   $tpl->assign('EMAIL_TXT', $ps_contact7);
   $tpl->assign('COMMENTS_TXT', $ps_contact8);
   $tpl->assign('CAPTCHA', buildContactUsCaptcha($ps_contact10,$ps_contact11,(array_key_exists('word',$formErrors) ? $formErrors['word'] : '')));
   $tpl->assign('SETTINGS', $SETTINGS);
   $tpl->assign('ERRORS', $formErrors);
   $tpl->assign('IS_SENT', (isset($OK) ? true : false));
   $tpl->assign('MESSAGE_SENT', $ps_contact12);
   $tpl->assign('MESSAGE_SENT2', str_replace('{website_name}',cleanData($SETTINGS->website),$ps_contact13));
   $tpl->assign('SEND_TXT', $ps_contact9);
   
   $tpl->display('templates/contact-us.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // templates/printer-friendly.tpl.php
   case 'print':
   
   if (!isset($_GET['recipe'])) {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   checkDigit($_GET['recipe']);
   
   // Get recipe information..
   $RECIPE  = getTableData('recipes','id',$_GET['recipe']);
   
   // Is recipe enabled..
   if ($RECIPE->enRecipe=='no' || $RECIPE->isApproved=='yes') {
     exit;
   }
   
   $tpl = new Savant2();
   $tpl->assign('BASE_PATH', $SETTINGS->install_path);
   $tpl->assign('CHARSET', $charset);
   $tpl->assign('RECIPE', $RECIPE);
   $tpl->assign('BY', str_replace(array('{website_name}','{website_url}'),array(cleanData($SETTINGS->website),$SETTINGS->install_path),$ps_recipe36));
   $tpl->assign('INGREDIENTS_TXT', $ps_recipe11);
   $tpl->assign('INSTRUCTIONS_TXT', $ps_recipe12);
   $tpl->assign('DATE_BAR_TXT', ($RECIPE->submitted_by ? $ps_recipe34.' <span class="highlight">'.cleanData($RECIPE->submitted_by).'</span> | ' : '').$ps_recipe35.' <span class="highlight">'.$RECIPE->adate.'</span>');
   $tpl->display('templates/printer-friendly.tpl.php');

   break;
   
   // templates/tell-a-friend.tpl.php
   case 'tell-a-friend':
   
   if (!isset($_GET['recipe'])) {
     header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
     exit;
   }
   
   checkDigit($_GET['recipe']);
   
   // Get recipe information..
   $RECIPE  = getTableData('recipes','id',$_GET['recipe']);
   
   // Get link..
   if ($SETTINGS->modr=='yes') {
     $CAT   = getTableData('categories','id',$RECIPE->cat);
     $link  = $SETTINGS->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPE->name).'/'.$RECIPE->id.'/index.html';
   } else {
     $link  = $SETTINGS->install_path.'?p=recipe&amp;recipe='.$RECIPE->id;
   }
   
   // Process form..
   if (isset($_POST['process'])) {
     // Remove nasty tags..
     $_POST  = multiDimensionalArrayMap('cleanEvilTags', $_POST);
     // Clean data if magic quotes are on..
     $_POST  = multiDimensionalArrayMap('cleanData', $_POST);
     // Error check..
     if ($_POST['name']=='') {
       $formErrors['name'] = $ps_contact;
     }
     if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $_POST['ctct'])) {
       $formErrors['ctct'] = $ps_contact2;
     }
     if ($_POST['fname']=='') {
       $formErrors['fname'] = $ps_contact20;
     }
     if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $_POST['fctct'])) {
       $formErrors['fctct'] = $ps_contact21;
     }
     if ($_POST['comments']=='') {
       $formErrors['comments'] = $ps_contact3;
     }
     if (isset($_POST['code'])) {
       include(PATH.'captcha/securimage.php');
       $img    = new Securimage();
       $valid  = $img->check($_POST['code']);
       if($valid == false) {
         $formErrors['word'] = '<span class="formError">'.$ps_contact4.'</span>';
       }
     }
     // End error check..if array empty, process and send..
     if (empty($formErrors)) {
       foreach ($_POST AS $key => $value) {
         $MRMAIL->addTag('{'.strtoupper($key).'}',$value);
       }
       $MRMAIL->addTag('{RECIPE}', $RECIPE->name);
       $MRMAIL->addTag('{URL}', $link);
       // Send to friend..
       $MRMAIL->sendMail($_POST['fname'],
                         $_POST['fctct'],
                         $_POST['name'],
                         $_POST['ctct'],
                         '['.$SETTINGS->website.'] '.str_replace('{name}',$_POST['name'],$ps_contact24),
                         $MRMAIL->template(PATH.'templates/email/friend.txt')
                         );
       // Send auto responder if enabled..
       if (ENABLE_TELL_A_FRIEND_AUTO_RESPONDER) {
         $MRMAIL->sendMail($_POST['name'],
                           $_POST['ctct'],
                           $SETTINGS->website,
                           $SETTINGS->email,
                           '['.$SETTINGS->website.'] '.$ps_contact25,
                           $MRMAIL->template(PATH.'templates/email/auto-friend.txt')
                           );     
       }   
       // Send report to webmaster if enabled..
       if (ENABLE_TELL_A_FRIEND_REPORT) {
         $MRMAIL->sendMail($SETTINGS->website,
                           $SETTINGS->email,
                           $_POST['name'],
                           $_POST['ctct'],
                           '['.$SETTINGS->website.'] '.$ps_contact26,
                           $MRMAIL->template(PATH.'templates/email/friend-report.txt')
                           ); 
       }         
       $OK = true;
     }
   }
   
   // Is recipe enabled..
   if ($RECIPE->enRecipe=='no' || $RECIPE->isApproved=='yes') {
     exit;
   }
   
   // Change data if coming from recipe page..
   $headerTitleText = $ps_recipe9.': '.cleanData($RECIPE->name).' - '.$headerTitleText;
   // Auto fill in the comments box with the recipe name & url...
   $_POST['comments'] = str_replace(array('{recipe}','{url}'),
                                    array(cleanData($RECIPE->name),$link),
                                    $ps_contact23);
   
   include(PATH.'control/header.inc.php');
   
   $tpl = new Savant2();
   $tpl->assign('F_URL', $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'tell-a-friend/'.$_GET['recipe'].'/index.html' : '?p=tell-a-friend&amp;recipe='.$_GET['recipe']));
   $tpl->assign('NAME_ERROR', $ps_contact);
   $tpl->assign('EMAIL_ERROR', $ps_contact2);
   $tpl->assign('FRIEND_NAME_ERROR', $ps_contact20);
   $tpl->assign('FRIEND_EMAIL_ERROR', $ps_contact21);
   $tpl->assign('COMMENTS_ERROR', $ps_contact3);
   $tpl->assign('CODE_ERROR', $ps_contact4);
   $tpl->assign('FORM_ERRORS', htmlspecialchars($javascript3));
   $tpl->assign('CONTACT_TXT', str_replace('{recipe}',cleanData($RECIPE->name),$ps_contact22));
   $tpl->assign('NAME_TXT', $ps_contact6);
   $tpl->assign('EMAIL_TXT', $ps_contact7);
   $tpl->assign('FRIEND_NAME_TXT', $ps_contact18);
   $tpl->assign('FRIEND_EMAIL_TXT', $ps_contact19);
   $tpl->assign('COMMENTS_TXT', $ps_contact8);
   $tpl->assign('CAPTCHA', buildContactUsCaptcha($ps_contact10,$ps_contact11,(array_key_exists('word',$formErrors) ? $formErrors['word'] : '')));
   $tpl->assign('SETTINGS', $SETTINGS);
   $tpl->assign('ERRORS', $formErrors);
   $tpl->assign('IS_SENT', (isset($OK) ? true : false));
   $tpl->assign('MESSAGE_SENT', $ps_contact12);
   $tpl->assign('MESSAGE_SENT2', (isset($OK) ? str_replace(array('{website_name}','{friend}'),array(cleanData($SETTINGS->website),cleanData($_POST['fname'])),$ps_contact27) : ''));
   $tpl->assign('SEND_TXT', $ps_contact9);
   $tpl->display('templates/tell-a-friend.tpl.php');

   include(PATH.'control/footer.inc.php');
   break;
   
   // RSS feed..
   case 'rss-feed':
   case 'rss-cat-feed':
   // Is cat defined..
   $url = '';
   if (isset($_GET['cat'])) {
     $CAT = getTableData('categories','id',$_GET['cat']);
     if ($CAT->enCat=='no') {
       header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
       exit;
     }
     if ($CAT->isParent=='no') {
       $thisParent = getTableData('categories','id',$CAT->childOf);
       if ($thisParent->enCat=='no') {
         header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
         exit;
       }
     }
     if (!isset($CAT->id) && !isset($thisParent->id)) {
       header("Location: ".$SETTINGS->install_path.($SETTINGS->modr=='yes' ? '404.html' : 'index.php?p=404'));
       exit;
     }
     // Cat name..
     $CATNAME = (isset($thisParent->catname) ? cleanData($thisParent->catname).'/'.cleanData($CAT->catname) : cleanData($CAT->catname)); 
     // Cat url..
     if ($SETTINGS->modr=='yes') {
       $url = $SETTINGS->install_path.'category/'.(isset($thisParent->catname) ? seoUrl(cleanData($thisParent->catname)).'/' : '').seoUrl(cleanData($CAT->catname)).'/'.$_GET['cat'].'/1/index.html';
     } else {
       $url = $SETTINGS->install_path.'?p=category&amp;cat='.$_GET['cat'];
     }
     $MRFEED->thisFeedUrl  = $SETTINGS->install_path.($SETTINGS->modr=='yes' ? 'rss-cat-feed/'.$_GET['cat'].'/.html' : '?p=rss-cat-feed&amp;cat='.$_GET['cat']);
   }
   // Define vars..
   $rss_feed    = ''; 
   // Open channel..
   $rss_feed = $MRFEED->open_channel();
   // Feed info..
   if (isset($_GET['cat'])) {
     $rss_feed .= $MRFEED->feed_info(str_replace(array('{website_name}','{category}'),array($SETTINGS->website,$CATNAME),$msg_rss4),
                                     $url,
                                     RSS_BUILD_DATE_FORMAT,
                                     str_replace("{website_name}",$SETTINGS->website,$msg_rss2),
                                     $SETTINGS->website
                                     );
   } else {
     $rss_feed .= $MRFEED->feed_info(str_replace("{website_name}",$SETTINGS->website,$msg_rss),
                                     ($SETTINGS->modr=='yes' ? $SETTINGS->install_path.'index.html' : $SETTINGS->install_path.'index.php'),
                                     RSS_BUILD_DATE_FORMAT,
                                     str_replace("{website_name}",$SETTINGS->website,$msg_rss2),
                                     $SETTINGS->website
                                     );
   }
   // Get latest recipes..
   $rss_feed .= $MRFEED->getLatestRecipes(RSS_BUILD_DATE_FORMAT,(isset($_GET['cat']) ? $_GET['cat'] : 0));
   // Close channel..
   $rss_feed .= $MRFEED->close_channel();
   // Display RSS feed..
   header('Content-Type: text/xml');
   echo cleanData(trim($rss_feed));
   break;
   
   // Flash header..
   case 'flash':
   header('Content-Type: text/xml');
   echo $MRFLASH->buildFlashHeader();
   break;
   
   case 'comment-captcha':
   case 'contact-us-captcha':
   // Change parameters to suit your own design/colors..
   // Additional parameters in 'captcha/secureimage.php'..
   include (PATH.'captcha/securimage.php');
   $img                     = new securimage();
   switch ($cmd) {
     // For recipe comments captcha..
     case 'comment-captcha':
     $img->image_width      = 120;
     $img->image_height     = 20;
     $img->use_wordlist     = false;
     $img->font_size        = 12;
     $img->code_length      = 4;
     $img->image_bg_color   = '#eee9b1';
     $img->text_color       = '#000000';
     $img->use_multi_text   = false;
     $img->line_color       = '#c5c089';
     $img->show();
     break;
     // For contact us page captcha..
     case 'contact-us-captcha':
     $img->image_width      = 200;
     $img->image_height     = 50;
     $img->use_wordlist     = true;
     $img->font_size        = 20;
     $img->image_bg_color   = '#f1ebb7';
     $img->text_color       = '#000000';
     $img->use_multi_text   = false;
     $img->line_color       = '#fffbcb';
     $img->arc_linethrough  = true;
     $img->arc_line_colors  = '#ffffff';
     $img->show();
     break;
   }
   break;
}

?>
