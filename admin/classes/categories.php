<?php
class cats {

var $prefix;

// Add category..
function addCat() {
  $_POST = multiDimensionalArrayMap('safeImport', $_POST);
  mysql_query("INSERT INTO ".$this->prefix."categories (
  catname,
  comments,
  isParent,
  childOf,
  metaDesc,
  metaKeys,
  enComments,
  enRecipes,
  enRating,
  enCat
  ) VALUES (
  '".$_POST['catname']."',
  '".$_POST['comments']."',
  '".($_POST['type']=='new' ? 'yes' : 'no')."',
  '".($_POST['type']=='new' ? '0' : $_POST['type'])."',
  '".$_POST['metaDesc']."',
  '".$_POST['metaKeys']."',
  '".(isset($_POST['enComments']) ? $_POST['enComments'] : 'yes')."',
  '".(isset($_POST['enRecipes']) ? $_POST['enRecipes'] : 'yes')."',
  '".(isset($_POST['enRating']) ? $_POST['enRating'] : 'yes')."',
  '".(isset($_POST['enCat']) ? $_POST['enCat'] : 'yes')."'
  )") or die(mysql_error());
}

// Update category..
function updateCat() {
  $_POST = multiDimensionalArrayMap('safeImport', $_POST);
  mysql_query("UPDATE ".$this->prefix."categories SET
  catname     = '".$_POST['catname']."',
  comments    = '".$_POST['comments']."',
  isParent    = '".($_POST['type']=='new' ? 'yes' : 'no')."',
  childOf     = '".($_POST['type']=='new' ? '0' : $_POST['type'])."',
  metaDesc    = '".$_POST['metaDesc']."',
  metaKeys    = '".$_POST['metaKeys']."',
  enComments  = '".(isset($_POST['enComments']) ? $_POST['enComments'] : 'yes')."',
  enRecipes   = '".(isset($_POST['enRecipes']) ? $_POST['enRecipes'] : 'yes')."',
  enRating    = '".(isset($_POST['enRating']) ? $_POST['enRating'] : 'yes')."',
  enCat       = '".(isset($_POST['enCat']) ? $_POST['enCat'] : 'yes')."'
  WHERE id    = '".$_POST['edit']."'
  LIMIT 1
  ") or die(mysql_error());
  
  // If parent category is moved to children and this parent already had children, move them to same new parent..
  if (ctype_digit($_POST['type'])) {
    mysql_query("UPDATE ".$this->prefix."categories SET
    childOf        = '".$_POST['type']."'
    WHERE childOf  = '".$_POST['edit']."'
    ") or die(mysql_error());
  }
}

// Delete category..
function deleteCat($SETTINGS) {
  // Remove categories..
  mysql_query("DELETE FROM ".$this->prefix."categories
  WHERE id = '".(int)$_GET['del']."'
  OR childOf   = '".(int)$_GET['del']."'
  ") or die(mysql_error());
  $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
  WHERE cat = '".(int)$_GET['del']."'
  OR cat    = '".(int)$_GET['del']."'
  ") or die(mysql_error());
  while ($RECIPE = mysql_fetch_object($query)) {
    // Remove recipe images..
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
  }
  // Remove recipes..
  mysql_query("DELETE FROM ".$this->prefix."recipes
  WHERE cat = '".(int)$_GET['del']."'
  OR cat    = '".(int)$_GET['del']."'
  ") or die(mysql_error());
}

}


?>
