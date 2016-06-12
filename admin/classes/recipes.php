<?php
class recipes {

var $prefix;

// Activate comment..
function activateComment($id) {
  mysql_query("UPDATE ".$this->prefix."comments SET
  isApproved  = 'no'
  WHERE id    = '$id'
  LIMIT 1
  ") or die(mysql_error());
  $COMMENT = getTableData('comments','id',$id);
  $RECIPE  = getTableData('recipes','id',$COMMENT->recipe);
  mysql_query("UPDATE ".$this->prefix."recipes SET
  comCount  = (comCount+1)
  WHERE id  = '$RECIPE->id'
  LIMIT 1
  ") or die(mysql_error());
}

// Reject comment..
function rejectComment($id) {
  mysql_query("DELETE FROM ".$this->prefix."comments
  WHERE id = '$id'
  LIMIT 1
  ") or die(mysql_error());
}

// Delete comments..
function deleteComments() {
  mysql_query("DELETE FROM ".$this->prefix."comments
  WHERE id IN (".implode(',',$_POST['comment']).")
  ") or die(mysql_error());
  mysql_query("UPDATE ".$this->prefix."recipes SET
  comCount  = (comCount-".count($_POST['comment']).")
  WHERE id  = '".(int)$_GET['recipe']."'
  LIMIT 1
  ") or die(mysql_error());
}

// Activate recipe..
function activateRecipe($id) {
  mysql_query("UPDATE ".$this->prefix."recipes SET
  isApproved  = 'no'
  WHERE id    = '$id'
  LIMIT 1
  ") or die(mysql_error());
}

// Reject recipe..
function rejectRecipe($id,$SETTINGS) {
  // Remove any pictures..
  $q_pic = mysql_query("SELECT * FROM ".$this->prefix."pictures
           WHERE recipe = '$id'
           ") or die(mysql_error());
  if (mysql_num_rows($q_pic)>0) {
    while ($IMG = mysql_fetch_object($q_pic)) {
      if (file_exists($SETTINGS->server_path.'templates/images/recipes/'.$IMG->picPath)) {
        @unlink($SETTINGS->server_path.'templates/images/recipes/'.$IMG->picPath);
      }
      mysql_query("DELETE FROM ".$this->prefix."pictures
      WHERE id = '".$IMG->id."'
      LIMIT 1
      ") or die(mysql_error());
    }
  }
  // Delete cloud tags..
  mysql_query("DELETE FROM ".$this->prefix."cloudtags
  WHERE recipe = '$id'
  ") or die(mysql_error());
  // Delete recipe..
  mysql_query("DELETE FROM ".$this->prefix."recipes
  WHERE id = '$id'
  LIMIT 1
  ") or die(mysql_error());
}

// Update comment..
function updateComment() {
  $_POST = multiDimensionalArrayMap('safeImport', $_POST);
  mysql_query("UPDATE ".$this->prefix."comments SET
  recipe    = '".$_POST['recipe']."',
  comment   = '".$_POST['comment']."',
  leftBy    = '".$_POST['leftBy']."',
  email     = '".$_POST['email']."'
  WHERE id  = '".(int)$_GET['id']."'
  LIMIT 1
  ") or die(mysql_error());
}

// Rebuild cloud tags..
function rebuildAdminCloudTags($words,$words2,$id) {
  global $database;
  // Remove new line characters and html code..
  $words   = str_replace(array(defineNewline(),defineNewline().defineNewline()),array(' ',' '),strip_tags($words));
  $words2  = str_replace(array(defineNewline(),defineNewline().defineNewline()),array(' ',' '),strip_tags($words2));
  // Assign arrays..
  $skipWords   = array_map('trim',file(REL_PATH.'control/cloud-tags-skip-file.txt'));
  $wordBlock1  = ($words ? array_map('trim',explode(' ',$words)) : array());
  $wordBlock2  = ($words2 ? array_map('trim',explode(' ',$words2)) : array());
  mysql_query("DELETE FROM ".$database['prefix']."cloudtags
  WHERE recipe = '$id'
  ") or die(mysql_error());
  // Loop through first word block..
  if (!empty($wordBlock1)) {
    foreach ($wordBlock1 AS $wd) {
      // Run words through filter..we can use the seourl function..
      $wd  = seoUrl($wd);
      // If word contains a hyphen from the previous filter, only take first part of word..
      if (strpos($wd,'-')!==FALSE) {
        $wd = substr($wd,0,strlen(strpos($wd,'-')));
      }
      // Prepare for safe importing into dd..
      $wd  = safeImport($wd);
      if (!in_array($wd,$skipWords) && strlen($wd)>=CLOUD_TAG_WORD_LIMIT) {
        if (rowCount('cloudtags',' WHERE cloud_word = \''.$wd.'\' AND recipe = \''.$id.'\'')>0) {
          mysql_query("UPDATE ".$database['prefix']."cloudtags SET
          cloud_count       = (cloud_count+1)
          WHERE cloud_word  = '$wd' AND recipe = '$id'
          LIMIT 1
          ") or die(mysql_error());
        } else {
          mysql_query("INSERT INTO ".$database['prefix']."cloudtags (
          cloud_word,cloud_count,recipe
          ) VALUES (
          '$wd','1','$id'
          )") or die(mysql_error());
        }
      }
    }
  }
  // Loop through second word block..
  if (!empty($wordBlock2)) {
    foreach ($wordBlock2 AS $wd) {
      // Run words through filter..we can use the seourl function..
      $wd  = seoUrl($wd);
      // If word contains a hyphen from the previous filter, only take first part of word..
      if (strpos($wd,'-')!==FALSE) {
        $wd = substr($wd,0,strlen(strpos($wd,'-')));
      }
      // Prepare for safe importing into dd..
      $wd  = safeImport($wd);
      if (!in_array($wd,$skipWords) && strlen($wd)>CLOUD_TAG_WORD_LIMIT) {
        if (rowCount('cloudtags',' WHERE cloud_word = \''.$wd.'\' AND recipe = \''.$id.'\'')>0) {
          mysql_query("UPDATE ".$database['prefix']."cloudtags SET
          cloud_count       = (cloud_count+1)
          WHERE cloud_word  = '$wd' AND recipe = '$id'
          LIMIT 1
          ") or die(mysql_error());
        } else {
          mysql_query("INSERT INTO ".$database['prefix']."cloudtags (
          cloud_word,cloud_count,recipe
          ) VALUES (
          '$wd','1','$id'
          )") or die(mysql_error());
        }
      }
    }
  }
}

// Add new picture..
function addNewPicture($name,$temp,$id,$SETTINGS) {
  $ext      = strrchr(strtolower($name), '.');
  $picPath  = $id.'-'.$this->getNextPictureID($id).$ext;
  if (is_uploaded_file($temp) && is_writeable($SETTINGS->server_path.'templates/images/recipes')) {
    move_uploaded_file($temp,$SETTINGS->server_path.'templates/images/recipes/'.$picPath);
    if (file_exists($SETTINGS->server_path.'templates/images/recipes/'.$picPath)) {
      // Make file removeable via FTP..
      // Not supported by all servers, so mask error..
      @chmod($SETTINGS->server_path.'templates/images/recipes/'.$picPath,0644);
      mysql_query("INSERT INTO ".$this->prefix."pictures (
      recipe,picPath
      ) VALUES (
      '$id','$picPath'
      )") or die(mysql_error());
    }
  }
}

// Get next picture id..
function getNextPictureID($id) {
  $query = mysql_query("SELECT * FROM ".$this->prefix."pictures 
                        WHERE recipe = '$id'
                        ") or die(mysql_error());
  return (mysql_num_rows($query)>0 ? (mysql_num_rows($query)+1) : 1); 
}

// Delete pictures..
function deletePicture($SETTINGS) {
  if (file_exists($SETTINGS->server_path.'templates/images/recipes/'.$_GET['picture'])) {
    @unlink($SETTINGS->server_path.'templates/images/recipes/'.$_GET['picture']);
  }
  mysql_query("DELETE FROM ".$this->prefix."pictures
  WHERE id = '".(int)$_GET['id']."'
  LIMIT 1
  ") or die(mysql_error());
}

// Delete all pictures..
function deleteAllPictures($SETTINGS) {
  // Delete pictures..
  $q_pic = mysql_query("SELECT * FROM ".$this->prefix."pictures
           WHERE recipe = '".(int)$_GET['recipe']."'
           ") or die(mysql_error());
  if (mysql_num_rows($q_pic)>0) {
    while ($IMG = mysql_fetch_object($q_pic)) {
      if (file_exists($SETTINGS->server_path.'templates/images/recipes/'.$IMG->picPath)) {
        @unlink($SETTINGS->server_path.'templates/images/recipes/'.$IMG->picPath);
      }
      mysql_query("DELETE FROM ".$this->prefix."pictures
      WHERE id = '".$IMG->id."'
      LIMIT 1
      ") or die(mysql_error());
    }
  }
}

// Add new recipe..
function addRecipe() {
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
  ratingCount,
  ipAddresses,
  rss_date
  ) VALUES (
  '".safeImport($_POST['name'])."',
  '".$_POST['cat']."',
  '".safeImport($_POST['ingredients'])."',
  '".safeImport($_POST['instructions'])."',
  '".safeImport($_POST['submitted_by'])."',
  '".date("Y-m-d",strtotime(SERVER_TIME_ADJUSTMENT))."',
  '0',
  '".safeImport($_POST['metaDesc'])."',
  '".safeImport($_POST['metaKeys'])."',
  '".(isset($_POST['enComments']) ? $_POST['enComments'] : 'yes')."',
  '".(isset($_POST['enRating']) ? $_POST['enRating'] : 'yes')."',
  '".(isset($_POST['enRecipe']) ? $_POST['enRecipe'] : 'yes')."',
  'no',
  '0',
  '0',
  '".getRealIPAddr()."',
  '".RSS_BUILD_DATE_FORMAT."'
  )") or die(mysql_error());
  return mysql_insert_id();
}

// Update recipe..
function updateRecipe() {
  mysql_query("UPDATE ".$this->prefix."recipes SET
  name          = '".safeImport($_POST['name'])."',
  cat           = '".$_POST['cat']."',
  ingredients   = '".safeImport($_POST['ingredients'])."',
  instructions  = '".safeImport($_POST['instructions'])."',
  submitted_by  = '".safeImport($_POST['submitted_by'])."',
  addDate       = '".$_POST['addDate']."',
  hits          = '".$_POST['hits']."',
  metaDesc      = '".safeImport($_POST['metaDesc'])."',
  metaKeys      = '".safeImport($_POST['metaKeys'])."',
  enComments    = '".(isset($_POST['enComments']) ? $_POST['enComments'] : 'yes')."',
  enRating      = '".(isset($_POST['enRating']) ? $_POST['enRating'] : 'yes')."',
  enRecipe      = '".(isset($_POST['enRecipe']) ? $_POST['enRecipe'] : 'yes')."'
  WHERE id      = '".(int)$_GET['id']."'
  LIMIT 1
  ") or die(mysql_error());
}

// Delete recipe..
function deleteRecipe($SETTINGS,$cats=false) {
  if ($cats) {
    if (in_array('all',$_POST['cats'])) {
      $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
      ") or die(mysql_error());
    } else {
      $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
      WHERE cat IN (".implode(',',$_POST['cats']).")
      ") or die(mysql_error());
    }
  } else {
    $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
    WHERE id IN (".implode(',',$_POST['recipe']).")
    ") or die(mysql_error());
  }
  while ($RECIPE = mysql_fetch_object($query)) {
    // Delete comments..
    mysql_query("DELETE FROM ".$this->prefix."comments
    WHERE recipe = '$RECIPE->id'
    ") or die(mysql_error());  
    // Delete pictures..
    $q_pic = mysql_query("SELECT * FROM ".$this->prefix."pictures
             WHERE recipe = '$RECIPE->id'
             ") or die(mysql_error());
    if (mysql_num_rows($q_pic)>0) {
      while ($IMG = mysql_fetch_object($q_pic)) {
        if (file_exists($SETTINGS->server_path.'templates/images/recipes/'.$IMG->picPath)) {
          @unlink($SETTINGS->server_path.'templates/images/recipes/'.$IMG->picPath);
        }
        mysql_query("DELETE FROM ".$this->prefix."pictures
        WHERE id = '".$IMG->id."'
        LIMIT 1
        ") or die(mysql_error());
      }
    }
    // Delete cloud tags..
    mysql_query("DELETE FROM ".$this->prefix."cloudtags
    WHERE recipe = '$RECIPE->id'
    ") or die(mysql_error());
    // Delete recipes..
    mysql_query("DELETE FROM ".$this->prefix."recipes
    WHERE id = '$RECIPE->id'
    ") or die(mysql_error());
  }
}

// Reset recipe hits..
function resetRecipeHits() {
  if (in_array('all',$_POST['cats'])) {
    mysql_query("UPDATE ".$this->prefix."recipes SET
    hits = '0'
    ") or die(mysql_error());
  } else {
    mysql_query("UPDATE ".$this->prefix."recipes SET
    hits = '0'
    WHERE cat IN (".implode(',',$_POST['cats']).")
    ") or die(mysql_error());
  }
}

// Reset recipe ratings..
function resetRecipeRatings() {
  if (in_array('all',$_POST['cats'])) {
    mysql_query("TRUNCATE TABLE ".$this->prefix."ratings") or die(mysql_error());
  } else {
    $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
    WHERE cat IN (".implode(',',$_POST['cats']).")
    ") or die(mysql_error());
    while ($RECIPE = mysql_fetch_object($query)) {
      mysql_query("DELETE FROM ".$this->prefix."ratings
      WHERE recipe = '$RECIPE->id'
      ") or die(mysql_error());
    }
  }
}

// Delete all member comments..
function deleteAllRecipeComments() {
  $ids = array();
  if (in_array('all',$_POST['cats'])) {
    mysql_query("TRUNCATE TABLE ".$this->prefix."comments") or die(mysql_error());
    mysql_query("UPDATE ".$this->prefix."recipes SET comCount = '0'") or die(mysql_error());
  } else {
    $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
    WHERE cat IN (".implode(',',$_POST['cats']).")
    ") or die(mysql_error());
    while ($RECIPE = mysql_fetch_object($query)) {
      $ids[] = $RECIPE->id;
    }
    if (!empty($ids)) {
      mysql_query("DELETE FROM ".$this->prefix."comments
      WHERE recipe IN (".implode(',',$ids).")
      ") or die(mysql_error());
      mysql_query("UPDATE ".$this->prefix."recipes SET
      comCount = '0'
      WHERE id IN (".implode(',',$ids).")
      ") or die(mysql_error());
    }
  }
}

}

?>
