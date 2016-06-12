<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  Written by David Ian Bennett
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Admin - Ratings
----------------------------------------------*/

if (!defined('PARENT')) { include('index.html'); exit; }

?>
<div id="bodyArea">

<div class="mainHead">

  <span class="headLeft"><?php echo $ratings; ?></span>
  
  <form method="post" id="form" action="?p=approve-comments<?php echo (isset($_GET['recipe']) ? '&amp;recipe='.$_GET['recipe'] : ''); ?>" onsubmit="return checkform3(this,'<?php echo $javascript60; ?>','<?php echo $javascript; ?>','<?php echo $javascript3; ?>')">
  <p><span class="noData" style="text-align:left;font-size:12px">
  <span class="filterByComments"><?php echo $ratings4; ?>: <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=ratings<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : ''); ?>&amp;page=<?php echo $page; ?>"><?php echo $recipes16; ?></option>
  <?php
  $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
            WHERE isParent = 'yes'
            AND childOf    = '0'
            AND enRating   = 'yes'
            AND enCat      = 'yes'
            ORDER BY catname") or die(mysql_error());
  while ($CATS = mysql_fetch_object($q_cats)) {
  ?>
  <option value="?p=ratings&amp;cat=<?php echo $CATS->id; ?><?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : ''); ?>&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($CATS->catname); ?></option>
  <?php
  $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                WHERE isParent = 'no'
                AND enCat      = 'yes'
                AND childOf    = '".$CATS->id."'
                ORDER BY catname") or die(mysql_error());
  while ($CHILDREN = mysql_fetch_object($q_children)) {
  ?>
  <option value="?p=ratings&amp;cat=<?php echo $CHILDREN->id; ?><?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : ''); ?>&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['cat']) && $_GET['cat']==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo cleanData($CHILDREN->catname); ?></option>
  <?php
  }
  }
  ?>
  </select>
  </span>
  
  <input onclick="window.location='?p=ratings&amp;filter=best<?php echo (isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : ''); ?>&amp;page=<?php echo $page; ?>'" type="radio" name="filter" value="best"<?php echo (isset($_GET['filter']) && $_GET['filter']=='best' ? ' checked="checked"' : (!isset($_GET['filter']) ? ' checked="checked"' : '')); ?> /> <?php echo $ratings2; ?> <input onclick="window.location='?p=ratings&amp;filter=worst<?php echo (isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : ''); ?>&amp;page=<?php echo $page; ?>'" type="radio" name="filter" value="worst"<?php echo (isset($_GET['filter']) && $_GET['filter']=='worst' ? ' checked="checked"' : ''); ?> /> <?php echo $ratings3; ?> <input onclick="window.location='?p=ratings&amp;filter=most<?php echo (isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : ''); ?>&amp;page=<?php echo $page; ?>'" type="radio" name="filter" value="worst"<?php echo (isset($_GET['filter']) && $_GET['filter']=='most' ? ' checked="checked"' : ''); ?> /> <?php echo $ratings8; ?> <input onclick="window.location='?p=ratings&amp;filter=least<?php echo (isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : ''); ?>&amp;page=<?php echo $page; ?>'" type="radio" name="filter" value="worst"<?php echo (isset($_GET['filter']) && $_GET['filter']=='least' ? ' checked="checked"' : ''); ?> /> <?php echo $ratings9; ?>
  </span>
  </p>
  <?php
  // Defaults..
  $filterBy = '';
  $orderBy  = 'score DESC';
  if (isset($_GET['cat'])) {
    $filterBy = 'AND cat = \''.(int)$_GET['cat'].'\'';
  }
  if (isset($_GET['filter'])) {
    switch ($_GET['filter']) {
      case 'best':
      $orderBy  = 'score DESC';
      break;
      case 'worst':
      $orderBy  = 'score';
      break;
      case 'most':
      $orderBy  = 'total_votes DESC';
      break;
      case 'least':
      $orderBy  = 'total_votes';
      break;
    }
  }
  $q_ratings = mysql_query("SELECT *,".$database['prefix']."recipes.id AS rid, 
               total_value/total_votes AS score
               FROM ".$database['prefix']."ratings 
               LEFT JOIN ".$database['prefix']."recipes
               ON ".$database['prefix']."ratings.recipe = ".$database['prefix']."recipes.id
               WHERE total_value > 0
               $filterBy
               ORDER BY $orderBy
               LIMIT $limit,".PER_PAGE."
               ") or die(mysql_error());
  if (mysql_num_rows($q_ratings)>0) {
  while ($RATINGS = mysql_fetch_object($q_ratings)) {
  if ($SETTINGS->modr=='yes') {
    $CAT = getTableData('categories','id',$RATINGS->cat);
  }
  ?>
  <div class="comment">
    <span style="display:block;margin-bottom:5px"><?php echo cleanData($RATINGS->name); ?></span>
    <?php echo ($RATINGS->enRating=='yes' ? $MRRAT->ratingBar($RATINGS->rid,$RATINGS->rid) : $ratings7); ?>
    <span class="rateEditView"><a href="<?php echo ($SETTINGS->modr=='yes' ? $SETTINGS->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RATINGS->name).'/'.$RATINGS->rid.'/index.html' : '../?p=recipe&amp;recipe='.$RATINGS->rid); ?>" title="<?php echo $ratings10; ?>" onclick="window.open(this);return false"><?php echo $ratings10; ?></a> | <a href="?p=edit&amp;id=<?php echo $RATINGS->rid; ?>" title="<?php echo $ratings11; ?>"><?php echo $ratings11; ?></a></span>
  </div>
  <?php
  }
  } else {
  ?>
  <span class="noData"><?php echo (isset($_GET['cat']) ? $ratings6 : $ratings5); ?></span>
  <?php
  }
  ?>
  </form>
  
  <?php 
  if (mysql_num_rows($q_ratings)>0) {
    echo pageNumbers(rowCountRatings('WHERE total_value > 0 '.$filterBy),PER_PAGE,$page);
  }
  ?>
  <p>&nbsp;</p>
  
</div>

</div>
