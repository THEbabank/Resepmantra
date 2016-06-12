<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Recipe Management Class
  Written by David Ian Bennett
----------------------------------------------*/

class recipes {

var $prefix;
var $settings = array();

// Are there recipe submissions enabled for at least 1 category..
function checkAddRecipeRule() {
  $query = mysql_query("SELECT * FROM ".$this->prefix."categories
  WHERE enRecipes = 'yes'
  LIMIT 1") or die(mysql_error());
  return (mysql_num_rows($query)>0 ? true : false);
}

// Displays category recipes..
function displayCategoryRecipes($searchKeys='') {
  global $limit,$ps_category8;
  $string = '';
  if ($searchKeys) {
    $sql = 'AND MATCH(name,instructions,ingredients,submitted_by,metaDesc,metaKeys) AGAINST(\''.safeImport($searchKeys).'\' IN BOOLEAN MODE)';
    $sql .= (isset($_GET['cat']) && ctype_digit($_GET['cat']) && $_GET['cat']>0 ? defineNewline().'AND cat = \''.$_GET['cat'].'\'' : '');
  } else {
    $sql = ($_GET['cat']>0 ? 'AND cat = \''.(int)$_GET['cat'].'\'' : '');
  }
  $q_recipe = mysql_query("SELECT * FROM ".$this->prefix."recipes
              WHERE enRecipe  = 'yes'
              AND isApproved  = 'no'
              $sql
              ORDER BY ".(RECIPE_DISPLAY_ORDER ? RECIPE_DISPLAY_ORDER : 'name')."
              LIMIT $limit,".$this->settings->total."
              ") or die(mysql_error());
  while ($RECIPES = mysql_fetch_object($q_recipe)) {
    // Build link data..
    if ($this->settings->modr=='yes') {
      $CAT   = getTableData('categories','id',$RECIPES->cat);
      $link  = $this->settings->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPES->name).'/'.$RECIPES->id.'/index.html';
    } else {
      $link  = $this->settings->install_path.'?p=recipe&amp;recipe='.$RECIPES->id;
    }
    // For by url..
    if ($this->settings->modr=='yes') {
      $byurl  = $this->settings->install_path.'search-free-recipes/'.urlencode(cleanData($RECIPES->submitted_by)).'/all/1/index.html';
    } else {
      $byurl  = '?p=search-free-recipes&amp;keys='.urlencode(cleanData($RECIPES->submitted_by)).'&amp;cat=all';
    }
    $string .= str_replace(array('{url}','{byurl}','{by}','{recipe}'),
                           array($link,$byurl,($RECIPES->submitted_by ? $ps_category8.': '.cleanData($RECIPES->submitted_by) : '&nbsp'),cleanData($RECIPES->name)),
                           file_get_contents(PATH.'templates/html/recipe.htm')
                           );
  }
  return $string;
}

// Adds new recipe..
function addNewRecipe() {
  $_POST = multiDimensionalArrayMap('safeImport', $_POST);
  $CAT   = getTableData('categories','id',$_POST['cat']);
  mysql_query("INSERT INTO ".$this->prefix."recipes (
  name,
  cat,
  ingredients,
  instructions,
  submitted_by,
  addDate,
  hits,
  metaDesc,
  metaKeys,
  enComments,
  enRating,
  enRecipe,
  isApproved,
  comCount,
  ipAddresses,
  email,
  rss_date
  ) VALUES (
  '".$_POST['rname']."',
  '".$_POST['cat']."',
  '".$_POST['ingredients']."',
  '".$_POST['instructions']."',
  '".$_POST['name']."',
  '".date("Y-m-d",strtotime(SERVER_TIME_ADJUSTMENT))."',
  '0',
  '',
  '',
  '".$CAT->enComments."',
  'yes',
  '".$CAT->enRating."',
  '".$this->settings->enRecApp."',
  '0',
  '".getRealIPAddr()."',
  '".$_POST['ctct']."',
  '".RSS_BUILD_DATE_FORMAT."'
  )") or die(mysql_error());
  return mysql_insert_id();
}

// Uploads recipe picture..
function uploadPicture($temp,$name,$size,$newID) {
  if (is_uploaded_file($temp) && is_writeable($this->settings->server_path.'templates/images/recipes')) {
    move_uploaded_file($temp,$this->settings->server_path.'templates/images/recipes/'.$name);
    if (file_exists($this->settings->server_path.'templates/images/recipes/'.$name)) {
      mysql_query("INSERT INTO ".$this->prefix."pictures (
      recipe,picPath
      ) VALUES (
      '$newID','$name'
      )") or die(mysql_error());
      // Required by some servers to make image viewable and accessible via FTP..
      @chmod($this->settings->server_path.'templates/images/recipes/'.$name,0644);      
      // Does image need resizing..
      if ($this->settings->autoResize!=0) {
        // Comma delimited string?..
        if (strpos($this->settings->autoResize,',')!==FALSE) {
          // Assign current and new sizes to arrays..
          $imgSizes = @getimagesize($this->settings->server_path.'templates/images/recipes/'.$name);
          $newSizes = array_map('trim',explode(',',$this->settings->autoResize));
          // If all is set and resize is required, continue..
          if (isset($newSizes[0]) && isset($newSizes[1]) && isset($imgSizes[0]) && isset($imgSizes[1]) && $newSizes[0]>0 && $newSizes[1]>0) {
            if ($imgSizes[0]>$newSizes[0] && $imgSizes[1]>$newSizes[1]) {
              $this->resizeUploadPicture($name,$newSizes);
            }
          }  
        }
      }
    }
    // Check temp file was cleared..
    if (file_exists($temp)) {
      @unlink($temp);
    }
  }
}

// Resizes uploaded jpeg..
function resizeUploadPicture($name,$nsizes) {
  $path = $this->settings->server_path.'templates/images/recipes/'.$name;
  switch (substr(strtolower($name),strpos($name,'.')+1,strlen($name))) {
    // Just jpg/jpeg supported at the moment..
    case 'jpg':
    case 'jpeg':
    if (function_exists('imagecreatefromjpeg')) {
      $img = imagecreatefromjpeg($path);
      if ($img) {
        $i_width   = imagesx($img);
        $i_height  = imagesy($img);
        $scale     = min($nsizes[0]/$i_width, $nsizes[1]/$i_height);
        // For thumbnail, maintain aspect ratio of original image
        // If image is smaller or equal to new sizes, no resize is necessary
        if ($scale<1) {
          $new_width   = floor($scale * $i_width);
          $new_height  = floor($scale * $i_height);
          $tmp_img     = imagecreatetruecolor($new_width,$new_height);
          imagecopyresampled($tmp_img,$img,0,0,0,0,$new_width,$new_height,$i_width,$i_height);
          imagejpeg($tmp_img, $path);
          imagedestroy($img);
          imagedestroy($tmp_img);
        } else {
          imagedestroy($img);
        }
      }
    }
    break;
  }
}

// Adds comments to database..
function addCommentsToDatabase() {
  $_POST = multiDimensionalArrayMap('safeImport', $_POST);
  mysql_query("INSERT INTO ".$this->prefix."comments (
  recipe,
  comment,
  leftBy,
  email,
  addDate,
  isApproved,
  ipAddresses
  ) VALUES (
  '".$_POST['r']."',
  '".$_POST['comments']."',
  '".$_POST['name']."',
  '".$_POST['ctct']."',
  '".date("Y-m-d",strtotime(SERVER_TIME_ADJUSTMENT))."',
  '".$this->settings->enCommApp."',
  '".getRealIPAddr()."'
  )") or die(mysql_error());
  // If approval isn`t set, the comment is live..
  // Increment count..
  if ($this->settings->enCommApp=='no') {
    mysql_query("UPDATE ".$this->prefix."recipes SET
    comCount  = (comCount+1)
    WHERE id  = '".$_POST['r']."'
    LIMIT 1
    ") or die(mysql_error());
  }
}

// Build comment form..
function buildAddCommentsForm($txt) {
  global $ps_recipe20,$ps_recipe21,$ps_recipe22,$ps_recipe23,$ps_recipe24,$javascript66;
  $captcha = '';
  if ($this->settings->enSpam=='yes') {
    $captcha = str_replace(array('{add_comment}','{your_name}','{your_email}','{your_comments}','{captcha}','{spam_text}','{refresh}'),
                           array($txt,$ps_recipe20,$ps_recipe21,$ps_recipe22,$captcha,$ps_recipe23,$ps_recipe24),
                           file_get_contents(PATH.'templates/html/captcha-add-comment.htm')
                           );
  }
  return str_replace(array('{add_comment}','{your_name}','{your_email}','{your_comments}','{captcha}','{recipe}','{etext}','{iscode}','{isibox}'),
                     array($txt,$ps_recipe20,$ps_recipe21,$ps_recipe22,$captcha,$_GET['recipe'],htmlspecialchars($javascript66),($captcha ? 'yes' : 'no'),(USE_IBOX_FOR_COMMENTS ? 'yes' : 'no')),
                     file_get_contents(PATH.'templates/html/add-comment.htm')
                     );
}

// Build xml response header for ajax..
function xmlResponse($tags) {
  header('Content-Type: text/xml');
  echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
  echo '<response>'.$tags.'</response>';
}

// Displays hit counter graphics..
function buildRecipeHitCount($num) {
  $additional = '';
  $images = array(
  '0' => '<img src="templates/images/digits/0.gif" alt="0" title="0" />',
  '1' => '<img src="templates/images/digits/1.gif" alt="1" title="1" />',
  '2' => '<img src="templates/images/digits/2.gif" alt="2" title="2" />',
  '3' => '<img src="templates/images/digits/3.gif" alt="3" title="3" />',
  '4' => '<img src="templates/images/digits/4.gif" alt="4" title="4" />',
  '5' => '<img src="templates/images/digits/5.gif" alt="5" title="5" />',
  '6' => '<img src="templates/images/digits/6.gif" alt="6" title="6" />',
  '7' => '<img src="templates/images/digits/7.gif" alt="7" title="7" />',
  '8' => '<img src="templates/images/digits/8.gif" alt="8" title="8" />',
  '9' => '<img src="templates/images/digits/9.gif" alt="9" title="9" />'
  );
  // Always show a min amount of 6 digits if set..
  if (HIT_COUNT_DIGITS_MIN>0) {
    if (strlen($num)<HIT_COUNT_DIGITS_MIN) {
      for ($i=0; $i<HIT_COUNT_DIGITS_MIN-strlen($num); $i++) {
        $additional .= '<img src="templates/images/digits/0.gif" alt="0" title="0" />';
      }
    }
  }
  return $additional.strtr($num,$images);
}

// Build recipe pictures scroll bar..
function buildRecipePictures($txt) {
  $string = '';
  $html   = '';
  $q_pictures = mysql_query("SELECT * FROM ".$this->prefix."pictures
                WHERE recipe = '".(int)$_GET['recipe']."'
                ORDER BY id
                ") or die(mysql_error());
  while ($PICS = mysql_fetch_object($q_pictures)) {
    $RECIPE  = getTableData('recipes','id',$PICS->recipe);
    $html   .= '<img class="pic" onclick="enlarge(this);" longdesc="templates/images/recipes/'.$PICS->picPath.'" src="templates/images/recipes/'.$PICS->picPath.'" alt="'.htmlspecialchars(cleanData($RECIPE->name)).'" title="'.htmlspecialchars(cleanData($RECIPE->name)).'" /> ';
  }
  $string = ($html ? $html : '<p class="noPictures">'.$txt.'</p>');
  return str_replace(array('{pictures}'),
                     array($string),
                     file_get_contents(PATH.'templates/html/'.($html ? 'gallery.htm' : 'recipe-pictures.htm'))
                     );
}

// Build recipe cloud tags..
// You can adjust the ranges if you want to and know what you are doing..
// Each range simply displays a different li class..
function buildRecipeCloudTags($txt) {
  global $ps_recipe43;
  $string = '';
  $tags   = '';
  $q_cloudtags = mysql_query("SELECT * FROM ".$this->prefix."cloudtags
                 WHERE recipe = '".(int)$_GET['recipe']."'
                 ORDER BY rand()
                 LIMIT ".CLOUD_TAGS_TO_DISPLAY."
                 ") or die(mysql_error());
  while ($CTAGS = mysql_fetch_object($q_cloudtags)) {
    if ($this->settings->modr=='yes') {
      $url = $this->settings->install_path.'search-free-recipes/'.urlencode(cleanData($CTAGS->cloud_word)).'/all/1/index.html';
    } else {
      $url = '?p=search-free-recipes&amp;keys='.urlencode(cleanData($CTAGS->cloud_word)).'&amp;filter=all&amp;cat=0';
    }
    // For count range 1-2 display this..
    if (in_array($CTAGS->cloud_count,array(1,2))) {
      $tags .= '<li class="tag1"><a href="'.$url.'" title="'.cleanData($CTAGS->cloud_word).'">'.cleanData($CTAGS->cloud_word).'</a></li>'.defineNewline();
    }
    // For count range 3-4 display this..
    if (in_array($CTAGS->cloud_count,array(3,4))) {
      $tags .= '<li class="tag2"><a href="'.$url.'" title="'.cleanData($CTAGS->cloud_word).'">'.cleanData($CTAGS->cloud_word).'</a></li>'.defineNewline();
    }
    // For count range 4-5 display this..
    if (in_array($CTAGS->cloud_count,array(4,5))) {
      $tags .= '<li class="tag3"><a href="'.$url.'" title="'.cleanData($CTAGS->cloud_word).'">'.cleanData($CTAGS->cloud_word).'</a></li>'.defineNewline();
    }
    // For count range 6-7 display this..
    if (in_array($CTAGS->cloud_count,array(6,7))) {
      $tags .= '<li class="tag4"><a href="'.$url.'" title="'.cleanData($CTAGS->cloud_word).'">'.cleanData($CTAGS->cloud_word).'</a></li>'.defineNewline();
    }
    // For the rest (ie, higher than 7) display this..
    if ($CTAGS->cloud_count>7) {
      $tags .= '<li class="tag5"><a href="'.$url.'" title="'.cleanData($CTAGS->cloud_word).'">'.cleanData($CTAGS->cloud_word).'</a></li>'.defineNewline();
    }
  }
  return str_replace(array('{cloud_tags_txt}','{cloud_tags}'),
                     array($txt,($tags ? $tags : '<li>'.$ps_recipe43.'</li>')),
                     file_get_contents(PATH.'templates/html/cloud-tags.htm')
                     );
}

// Build user submitted image upload boxes..
function buildAddRecipePictures($txt,$errors=array()) {
  global $SETTINGS,$ps_addrecipe11,$ps_addrecipe12,$ps_addrecipe13,$ps_addrecipe14;
  $rest   = '';
  $string = '';
  $e      = array();
  // Are there restrictions in place..
  if ($SETTINGS->validImages) {
    $rest = str_replace('{extensions}',strtoupper($SETTINGS->validImages),$ps_addrecipe11);
  }
  if ($SETTINGS->maxFileSize>0) {
    $rest = ($rest ? $rest.' '.IMG_RESTRICTION_SEPERATOR.' ' : '').str_replace('{size}',fileSizeConversion($SETTINGS->maxFileSize),$ps_addrecipe12);
  }
  // Now lets build the upload boxes..
  $min_rows = floor($SETTINGS->maxImages/MAX_UPLOAD_BOXES_PER_ROW);
  $add_rows = $SETTINGS->maxImages%MAX_UPLOAD_BOXES_PER_ROW;
  for ($i=0; $i<$min_rows; $i++) {
    $boxes = '';
    for ($j=0; $j<MAX_UPLOAD_BOXES_PER_ROW; $j++) {
      $boxes .= file_get_contents(PATH.'templates/html/add-recipe-upload-box.htm');
    }
    $string .= str_replace('{boxes}',$boxes,file_get_contents(PATH.'templates/html/add-recipe-upload-box-wrapper.htm'));
  }
  // Reset..
  $boxes = '';
  // Any extra boxes..
  for ($i=0; $i<$add_rows; $i++) {
    $boxes .= file_get_contents(PATH.'templates/html/add-recipe-upload-box.htm');
  }
  if ($boxes) {
    $string .= str_replace('{boxes}',$boxes,file_get_contents(PATH.'templates/html/add-recipe-upload-box-wrapper.htm'));
  }
  // On form processing, are errors present...
  if (!empty($errors)) {
    if (array_key_exists('ext',$errors)) {
      $e[] = $ps_addrecipe13;
    }
    if (array_key_exists('size',$errors)) {
      $e[] = str_replace('{size}',fileSizeConversion($SETTINGS->maxFileSize),$ps_addrecipe14);
    }
    $eString = implode('<br class="spaceError" />', $e);
  }
  return str_replace(array('{txt}','{restrictions}','{upload_boxes}','{error}'),
                     array(str_replace('{max}',$SETTINGS->maxImages,$txt),($rest ? '<span class="restrictions">'.$rest.'</span>' : ''),$string,(isset($eString) ? '<span class="error">'.$eString.'</span>' : '')),
                     file_get_contents(PATH.'templates/html/add-recipe-pictures.htm')
                     );
}

// Filter file upload name..
function filterUploadName($file) {
  // Convert to lower case chars..
  $file = strtolower($file);
  // Next, get original file extension
  $ext  = strrchr($file, '.');
  // Now remove the extension..
  $file = str_replace($ext,'',$file);
  // Remove problem chars and clean file name..
  $file = preg_replace('`[^\w_-]`', '', $file);
  // Finally, return filtered name with original extension..
  return trim($file).$ext;
}

// Build recipe comments..
function buildRecipeComments($txt,$txt2,$txt3) {
  $string = '';
  $html   = '';
  $q_comments = mysql_query("SELECT *,DATE_FORMAT(addDate,'".MYSQL_DATE_FORMAT."') AS cdate FROM ".$this->prefix."comments
                WHERE recipe    = '".(int)$_GET['recipe']."'
                AND isApproved  = 'no'
                ORDER BY id DESC
                ") or die(mysql_error());
  while ($COMMENTS = mysql_fetch_object($q_comments)) {
    $leftBy = str_replace(array('{name}','{date}'),array(cleanData($COMMENTS->leftBy),$COMMENTS->cdate),$txt);
    $html .= str_replace(array('{comment}','{leftby}'),
                         array(nl2br(htmlspecialchars(cleanData($COMMENTS->comment))),htmlspecialchars(cleanData($leftBy))),
                         file_get_contents(PATH.'templates/html/comment.htm')
                         );
  }
  $string = ($html ? $html : '<p class="noData">'.$txt2.'</p>');
  return str_replace(array('{comments}','{visitor_comments}','{count}'),
                     array($string,$txt3,mysql_num_rows($q_comments)),
                     file_get_contents(PATH.'templates/html/visitor-comments.htm')
                     );
}

// Loads contributors list..
function loadContributorsList($searchKeys) {
  $string = '';
  $q_cont = mysql_query("SELECT * FROM ".$this->prefix."recipes
            WHERE enRecipe    = 'yes'
            AND isApproved    = 'no'
            AND submitted_by != ''
            GROUP BY submitted_by
            ORDER BY submitted_by
            ") or die(mysql_error());
  while ($CONTR = mysql_fetch_object($q_cont)) {
    if ($this->settings->modr=='yes') {
      $url = $this->settings->install_path.'search-free-recipes/'.urlencode(cleanData($CONTR->submitted_by)).'/'.($searchKeys ? $_GET['cat'] : 'all').'/1/index.html';
    } else {
      $url = '?p=search-free-recipes&amp;keys='.urlencode(cleanData($CONTR->submitted_by)).'&amp;cat='.($searchKeys ? $_GET['cat'] : 'all');
    }
    $string .= '<option value="'.$url.'"'.($searchKeys==cleanData($CONTR->submitted_by) ? ' selected="selected"' : '').'>'.cleanData($CONTR->submitted_by).'</option>'.defineNewline();
  }
  return $string;
}

// Get children of parent..
function getChildrenOfParent($id) {
  $theChildren = array();
  $q_children = mysql_query("SELECT * FROM ".$this->prefix."categories
                WHERE enCat = 'yes'
                AND childOf = '$id'
                ") or die(mysql_error());
  while ($CHILDREN = mysql_fetch_object($q_children)) {
    $theChildren[] = $CHILDREN->id;
  }
  return $theChildren;
}

// Loads categories as a select list for search by category..
function loadCategorySearchSelect($searchKeys) {
  $string   = '';
  $q_parent = mysql_query("SELECT * FROM ".$this->prefix."categories
              WHERE enCat   = 'yes'
              AND isParent  = 'yes'
              ORDER BY catname
              ") or die(mysql_error());
  while ($PARENTS = mysql_fetch_object($q_parent)) {
    if ($this->settings->modr=='yes') {
      $url = $this->settings->install_path.'search-free-recipes/'.urldecode($searchKeys).'/'.$PARENTS->id.'/1/index.html';
    } else {
      $url = '?p=search-free-recipes&amp;keys='.urldecode($searchKeys).'&amp;cat='.$PARENTS->id;
    }
    $string .= '<option value="'.$url.'"'.(isset($_GET['cat']) && $_GET['cat']==$PARENTS->id ? ' selected="selected"' : '').'>'.cleanData($PARENTS->catname).'</option>'.defineNewline();
    $q_children = mysql_query("SELECT * FROM ".$this->prefix."categories
                  WHERE enCat = 'yes'
                  AND childOf = '$PARENTS->id'
                  ORDER BY catname
                  ") or die(mysql_error());
    while ($CHILDREN = mysql_fetch_object($q_children)) {
      if ($this->settings->modr=='yes') {
        $curl = $this->settings->install_path.'search-free-recipes/'.urldecode($searchKeys).'/'.$CHILDREN->id.'/1/index.html';
      } else {
        $curl = '?p=search-free-recipes&amp;keys='.urldecode($searchKeys).'&amp;cat='.$CHILDREN->id;
      }
      $string .= '<option value="'.$curl.'"'.(isset($_GET['cat']) && $_GET['cat']==$CHILDREN->id ? ' selected="selected"' : '').'>- '.cleanData($CHILDREN->catname).'</option>'.defineNewline();
    }
  }
  return $string;
}

// Loads categories as a select list..
function loadCategorySelect($add=false) {
  if ($add && isset($_GET['cat'])) {
    $_POST['cat'] = $_GET['cat'];
  }
  $string = '';
  $q_parent = mysql_query("SELECT * FROM ".$this->prefix."categories
              WHERE enCat   = 'yes'
              AND isParent  = 'yes'
              ".($add ? 'AND enRecipes = \'yes\'' : '')."
              ORDER BY catname
              ") or die(mysql_error());
  while ($PARENTS = mysql_fetch_object($q_parent)) {
    if ($this->settings->modr=='yes') {
      $url = ($add ? $PARENTS->id : $this->settings->install_path.'category/'.seoUrl(cleanData($PARENTS->catname)).'/'.$PARENTS->id.'/1/index.html');
    } else {
      $url = ($add ? $PARENTS->id : '?p=category&amp;cat='.$PARENTS->id);
    }
    $string .= '<option value="'.$url.'"'.(isset($_POST['cat']) && $_POST['cat']==$PARENTS->id ? ' selected="selected"' : '').'>'.cleanData($PARENTS->catname).'</option>'.defineNewline();
    $q_children = mysql_query("SELECT * FROM ".$this->prefix."categories
                  WHERE enCat = 'yes'
                  AND childOf = '$PARENTS->id'
                  ".($add ? 'AND enRecipes = \'yes\'' : '')."
                  ORDER BY catname
                  ".(!$add ? 'LIMIT '.SUB_CATEGORY_DISPLAY_COUNT : '')."
                  ") or die(mysql_error());
    while ($CHILDREN = mysql_fetch_object($q_children)) {
      if ($this->settings->modr=='yes') {
        $curl = ($add ? $CHILDREN->id : $this->settings->install_path.'category/'.seoUrl(cleanData($PARENTS->catname)).'/'.seoUrl(cleanData($CHILDREN->catname)).'/'.$CHILDREN->id.'/1/index.html');
      } else {
        $curl = ($add ? $CHILDREN->id : '?p=category&amp;cat='.$CHILDREN->id);
      }
      $string .= '<option value="'.$curl.'"'.(isset($_POST['cat']) && $_POST['cat']==$CHILDREN->id ? ' selected="selected"' : '').'>- '.cleanData($CHILDREN->catname).'</option>'.defineNewline();
    }
  }
  return $string;
}

// Displays other recipes in a given category..
function otherRecipesInThisCategory($txt,$cat) {
  $string = '';
  $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
           WHERE enRecipe  = 'yes'
           AND isApproved  = 'no'
           AND cat         = '$cat'
           AND id          != '".(int)$_GET['recipe']."'
           ORDER BY rand()
           LIMIT ".OTHER_RECIPES_IN_CAT_DISPLAY_LIMIT."
           ") or die(mysql_error());
  while ($RECIPES = mysql_fetch_object($query)) {
    // Build link data..
    if ($this->settings->modr=='yes') {
      $CAT   = getTableData('categories','id',$RECIPES->cat);
      $link  = $this->settings->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPES->name).'/'.$RECIPES->id.'/index.html';
    } else {
      $link  = $this->settings->install_path.'?p=recipe&amp;recipe='.$RECIPES->id;
    }
    $string .= str_replace(array('{link}','{recipe}'),
                           array($link,cleanData($RECIPES->name)),
                           file_get_contents(PATH.'templates/html/latest-popular-link.htm')
                           );
  }
  return ($string ? $string : $txt);
}

// Increments count for hit counter..
function hitCounter() {
  mysql_query("UPDATE ".$this->prefix."recipes SET
  hits      = (hits+1)
  WHERE id  = '".(int)$_GET['recipe']."'
  LIMIT 1
  ") or die(mysql_error());
}

// Builds homepage category/sub category list..
function buildCategories($block='left') {
  $left         = '';
  $right        = '';
  $run          = 0;
  $theChildren  = array();
  $q_parent = mysql_query("SELECT * FROM ".$this->prefix."categories
              WHERE enCat   = 'yes'
              AND isParent  = 'yes'
              ORDER BY catname
              ") or die(mysql_error());
  while ($PARENTS = mysql_fetch_object($q_parent)) {
    if (++$run%2!=0) {
      $children     = '';
      $ccount       = 0;
      $theChildren  = array();
      if ($this->settings->modr=='yes') {
        $url = $this->settings->install_path.'category/'.seoUrl(cleanData($PARENTS->catname)).'/'.$PARENTS->id.'/1/index.html';
      } else {
        $url = '?p=category&amp;cat='.$PARENTS->id;
      }
      $q_children = mysql_query("SELECT * FROM ".$this->prefix."categories
                    WHERE enCat = 'yes'
                    AND childOf = '$PARENTS->id'
                    ORDER BY catname
                    LIMIT ".SUB_CATEGORY_DISPLAY_COUNT."
                    ") or die(mysql_error());
      if (mysql_num_rows($q_children)>0) {
        $theChildren = $this->getChildrenOfParent($PARENTS->id);
      }              
      while ($CHILDREN = mysql_fetch_object($q_children)) {
        if ($this->settings->modr=='yes') {
          $curl = $this->settings->install_path.'category/'.seoUrl(cleanData($PARENTS->catname)).'/'.seoUrl(cleanData($CHILDREN->catname)).'/'.$CHILDREN->id.'/1/index.html';
        } else {
          $curl = '?p=category&amp;cat='.$CHILDREN->id;
        }
        $children .= '<a href="'.$curl.'" title="'.cleanData($PARENTS->catname).': '.cleanData($CHILDREN->catname).'">'.cleanData($CHILDREN->catname).'</a>'.(SHOW_CATEGORY_RECIPE_COUNT ? ' ('.rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat = \''.$CHILDREN->id.'\'').')' : '').(++$ccount!=mysql_num_rows($q_children) ? ', ' : (SUB_CATEGORY_DISPLAY_COUNT>mysql_num_rows($q_children) ? '' : '...'));
      }
      $left .= str_replace(array('{url}','{parent}','{children}','{count}'),
                           array($url,cleanData($PARENTS->catname),($children ? '<span class="children">'.$children.'</span>' : ''),(SHOW_CATEGORY_RECIPE_COUNT ? ' ('.(PARENT_WITH_CHILD_COUNT ? rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat = \''.$PARENTS->id.'\'')+(!empty($theChildren) ? rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat IN ('.implode(',',$theChildren).')') : 0) : rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat = \''.$PARENTS->id.'\'')).')' : '')),
                           file_get_contents(PATH.'templates/html/categories-list.htm')
                           );
    } else {
      $children     = '';
      $ccount       = 0;
      $theChildren  = array();
      if ($this->settings->modr=='yes') {
        $url = $this->settings->install_path.'category/'.seoUrl(cleanData($PARENTS->catname)).'/'.$PARENTS->id.'/1/index.html';
      } else {
        $url = '?p=category&amp;cat='.$PARENTS->id;
      }
      $q_children = mysql_query("SELECT * FROM ".$this->prefix."categories
                    WHERE enCat = 'yes'
                    AND childOf = '$PARENTS->id'
                    ORDER BY catname
                    LIMIT ".SUB_CATEGORY_DISPLAY_COUNT."
                    ") or die(mysql_error());
      if (mysql_num_rows($q_children)>0) {
        $theChildren = $this->getChildrenOfParent($PARENTS->id);
      }              
      while ($CHILDREN = mysql_fetch_object($q_children)) {
        if ($this->settings->modr=='yes') {
          $curl = $this->settings->install_path.'category/'.seoUrl(cleanData($PARENTS->catname)).'/'.seoUrl(cleanData($CHILDREN->catname)).'/'.$CHILDREN->id.'/1/index.html';
        } else {
          $curl = '?p=category&amp;cat='.$CHILDREN->id;
        }
        $children .= '<a href="'.$curl.'" title="'.cleanData($PARENTS->catname).': '.cleanData($CHILDREN->catname).'">'.cleanData($CHILDREN->catname).'</a>'.(SHOW_CATEGORY_RECIPE_COUNT ? ' ('.rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat = \''.$CHILDREN->id.'\'').')' : '').(++$ccount!=mysql_num_rows($q_children) ? ', ' : (SUB_CATEGORY_DISPLAY_COUNT>mysql_num_rows($q_children) ? '' : '...'));
      }
      $right .= str_replace(array('{url}','{parent}','{children}','{count}'),
                           array($url,cleanData($PARENTS->catname),($children ? '<span class="children">'.$children.'</span>' : ''),(SHOW_CATEGORY_RECIPE_COUNT ? ' ('.(PARENT_WITH_CHILD_COUNT ? rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat = \''.$PARENTS->id.'\'')+(!empty($theChildren) ? rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat IN ('.implode(',',$theChildren).')') : 0) : rowCount('recipes',' WHERE enRecipe = \'yes\' AND isApproved = \'no\' AND cat = \''.$PARENTS->id.'\'')).')' : '')),
                           file_get_contents(PATH.'templates/html/categories-list.htm')
                           );
    }
  }
  return ($block=='left' ? $left : $right);
}

// Displays latest and most popular links for recipes..
function showLatestAndMostPopularLinks($area) {
  $string = '';
  $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
           WHERE enRecipe  = 'yes'
           AND isApproved  = 'no'
           ORDER BY ".($area=='latest' ? 'id DESC' : 'hits DESC')."
           LIMIT ".($area=='latest' ? LATEST_RECIPES_DISPLAY_LIMIT : MOST_POPULAR_DISPLAY_LIMIT)."
           ") or die(mysql_error());
  while ($RECIPES = mysql_fetch_object($query)) {
    // Build link data..
    if ($this->settings->modr=='yes') {
      $CAT   = getTableData('categories','id',$RECIPES->cat);
      $link  = $this->settings->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPES->name).'/'.$RECIPES->id.'/index.html';
    } else {
      $link  = $this->settings->install_path.'?p=recipe&amp;recipe='.$RECIPES->id;
    }
    $string .= str_replace(array('{link}','{recipe}'),
                           array($link,cleanData($RECIPES->name)),
                           file_get_contents(PATH.'templates/html/latest-popular-link.htm')
                           );
  }
  return $string;
}

// Displays most popular recipes for category group when viewing a category..
function showMostPopularRecipesForCategoryGroup($cat) {
  $string = '';
  $ids    = $this->getCategoryAssociationIDs($cat,false);
  if ($ids) {
    $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
             WHERE enRecipe  = 'yes'
             AND isApproved  = 'no'
             AND cat IN (".$ids.")   
             ORDER BY hits DESC,name
             LIMIT ".MOST_POPULAR_DISPLAY_LIMIT."
             ") or die(mysql_error());
    while ($RECIPES = mysql_fetch_object($query)) {
      // Build link data..
      if ($this->settings->modr=='yes') {
        $CAT   = getTableData('categories','id',$RECIPES->cat);
        $link  = $this->settings->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPES->name).'/'.$RECIPES->id.'/index.html';
      } else {
        $link  = $this->settings->install_path.'?p=recipe&amp;recipe='.$RECIPES->id;
      }
      $string .= str_replace(array('{link}','{recipe}'),
                             array($link,cleanData($RECIPES->name)),
                             file_get_contents(PATH.'templates/html/latest-popular-link.htm')
                             );
    }
  }
  return $string;
}

// Get id numbers of categories associated to current category..
function getCategoryAssociationIDs($cat,$skip=true) {
  $ids = array();
  // Get cat data..
  $CAT = getTableData('categories','id',$cat);
  // Is category parent..
  if ($CAT->isParent=='no') {
    $thisParent = getTableData('categories','id',$CAT->childOf);
    $ids[]      = $thisParent->id;
  }
  if (isset($thisParent->catname)) {
    $q_cats = mysql_query("SELECT * FROM ".$this->prefix."categories
              WHERE enCat    = 'yes'
              AND   childOf  = '$thisParent->id'
              ORDER BY isParent DESC,catname
              ") or die(mysql_error());
  } else {
    $q_cats = mysql_query("SELECT * FROM ".$this->prefix."categories
              WHERE enCat  = 'yes'
              AND (id      = '$cat'
              OR childOf   = '$cat')
              ORDER BY isParent DESC,catname
              ") or die(mysql_error());
  }
  while ($CATS = mysql_fetch_object($q_cats)) {
    if ($skip) {
      if ($CATS->id!=$cat) {
        $ids[] = $CATS->id;
      }
    } else {
      $ids[] = $CATS->id;
    }
  }
  return (!empty($ids) ? implode(',',$ids) : '');
}

// Shows related category list..
function showRelatedCategories($cat,$parent=array()) {
  global $ps_category9;
  $string = '';
  $ids    = $this->getCategoryAssociationIDs($cat);
  if ($ids) {
    $q_cats = mysql_query("SELECT * FROM ".$this->prefix."categories
              WHERE id IN (".$ids.")
              ORDER BY catname
              ") or die(mysql_error());
    while ($CATS = mysql_fetch_object($q_cats)) {
      if ($this->settings->modr=='yes') {
        if (isset($parent->catname)) {
          $link = $this->settings->install_path.'category/'.seoUrl(cleanData($parent->catname)).'/'.seoUrl(cleanData($CATS->catname)).'/'.$CATS->id.'/1/index.html';
        } else {
          $link = $this->settings->install_path.'category/'.seoUrl(cleanData($CATS->catname)).'/'.$CATS->id.'/1/index.html';
        }
      } else {
        $link = '?p=category&amp;cat='.$CATS->id;
      }
      $string .= str_replace(array('{link}','{recipe}'),
                             array($link,cleanData($CATS->catname)),
                             file_get_contents(PATH.'templates/html/latest-popular-link.htm')
                             );
    }
  }
  return ($string ? $string : $ps_category9);
}

// Loads javascript/css code in header based on page load..
function loadJSFunctions($load=array()) {
  $html = '';
  if (in_array('recipes',$load) && USE_IBOX_FOR_COMMENTS) {
    $html .= defineNewline().'<script type="text/javascript" src="templates/iBox/ibox.js"></script>'.defineNewline().'<script type="text/javascript">iBox.setPath(\'templates/iBox/\');</script>'.defineNewline().'<link rel="stylesheet" href="templates/iBox/skins/lightbox/lightbox.css" type="text/css" media="screen"/>';
  }
  if (in_array('comments',$load)) {
    $html .= defineNewline().'<script type="text/javascript" src="templates/js/comments.js"></script>';
  }
  if (in_array('pictures',$load)) {
    $html .= defineNewline().'<script type="text/javascript" src="templates/enlargeIt/enlargeit.js">'.defineNewline().'</script><link rel="stylesheet" type="text/css" href="gallery.css" />'.defineNewline().'<script type="text/javascript" src="templates/js/motiongallery.js">'.defineNewline().'/***********************************************'.defineNewline().'* CMotion Image Gallery- © Dynamic Drive DHTML code library (www.dynamicdrive.com)'.defineNewline().'* Visit http://www.dynamicDrive.com for hundreds of DHTML scripts'.defineNewline().'* This notice must stay intact for legal use'.defineNewline().'* Modified by Jscheuer1 for autowidth and optional starting positions'.defineNewline().'***********************************************/'.defineNewline().'</script>';
  }
  // It is important the ratings load after the pictures. A JS conflict kills the rating ajax system if it loads before..
  if (in_array('ratings',$load)) {
    $html .= defineNewline().'<script type="text/javascript" src="templates/js/behavior.js"></script>'.defineNewline().'<script type="text/javascript" src="templates/js/rating.js"></script>'.defineNewline().'<link rel="stylesheet" type="text/css" href="rating.css" />';
  }
  return trim($html);
}

}

?>
