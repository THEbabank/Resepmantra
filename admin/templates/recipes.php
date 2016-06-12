<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  Written by David Ian Bennett
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Admin - Recipes/Search
----------------------------------------------*/

if (!defined('PARENT')) { include('index.html'); exit; }
$SQL_SEARCH = '';
if (isset($_GET['keywords'])) {
  if (ctype_digit($_GET['keywords'])) {
    $SQL_SEARCH = "AND id = '".$_GET['keywords']."'";
  } else {
    $SQL_SEARCH = "AND MATCH(name,instructions,ingredients,submitted_by) AGAINST('".mysql_real_escape_string($_GET['keywords'])."' IN BOOLEAN MODE)";
  }
}
if (isset($_GET['cont']) && $_GET['cont']!='all') {
  $SQL_SEARCH = 'AND submitted_by LIKE \'%'.mysql_real_escape_string(urldecode($_GET['cont'])).'%\'';
}
?>
<!-- Start Body Area -->
<div id="bodyArea">
<?php
if (isset($OK)) {
echo actionCompleted($recipes42);
}
if (isset($OK2)) {
echo actionCompleted($script10);
}
?>
<div class="left">
  
  <?php
  if ($SQL_SEARCH) {
  ?>
  <span class="headLeft"><?php echo $recipes35; ?>:</span>
  <?php
  } else {
  ?>
  <span class="headLeft"><?php echo $header3.(rowCount('recipes',' WHERE isApproved = \'no\'')>0 ? ' - <span style="font-weight:normal">'.(isset($_GET['filter']) ? str_replace('{count}',rowCount('recipes',' WHERE cat = \''.(int)$_GET['filter'].'\' AND isApproved = \'no\''),$recipes8) : str_replace(array('{count}','{cats}'),array(rowCount('recipes',' WHERE isApproved = \'no\''),rowCount('categories')),$recipes6)).'</span>' : ''); ?>:</span>
  <?php
  }
  ?>
  <form method="post" id="form" action="?p=recipes" onsubmit="return confirmMessage('<?php echo $javascript; ?>')">
  <?php
  if (rowCount('recipes')>0) {
  ?>
  <p>
  <span class="noData" style="text-align:left;font-size:12px"><span class="floatRight"><a href="#" onclick="infoBlock('show','<?php echo PER_PAGE; ?>');return false" title="<?php echo $recipes52; ?>"><?php echo $recipes52; ?></a> | <a href="#" onclick="infoBlock('hide','<?php echo PER_PAGE; ?>');return false" title="<?php echo $recipes53; ?>"><?php echo $recipes53; ?></a></span>&nbsp;<input type="checkbox" name="log" onclick="selectAll()" /> <?php echo $comments9; ?></span>
  </p>
  <?php
  }
  $orderBy = 'name';
  if (isset($_GET['orderby'])) {
    switch ($_GET['orderby']) {
      case 'name':
      $orderBy = 'name';
      break;
      case 'hits_asc':
      $orderBy = 'hits';
      break;
      case 'hits_desc':
      $orderBy = 'hits DESC';
      break;
      case 'comments_asc':
      $orderBy = 'comCount';
      break;
      case 'comments_desc':
      $orderBy = 'comCount DESC';
      break;
      case 'date_asc':
      $orderBy = 'addDate';
      break;
      case 'date_desc':
      $orderBy = 'addDate DESC';
      break;
      case 'disabled':
      $orderBy = 'enRecipe DESC';
      break;
    }
  }

  $q_all = mysql_query("SELECT *,DATE_FORMAT(adddate,'".MYSQL_DATE_FORMAT."') AS aDate
           FROM ".$database['prefix']."recipes
           WHERE isApproved = 'no'
           ".(isset($_GET['filter']) && $_GET['filter']!='all' ? 'AND cat = \''.(int)$_GET['filter'].'\'' : $SQL_SEARCH)."
           ORDER BY $orderBy
           LIMIT $limit,".PER_PAGE."
           ") or die(mysql_error());
  if (mysql_num_rows($q_all)>0) {
  while ($RECIPES = mysql_fetch_object($q_all)) {
  $CAT     = getTableData('categories','id',$RECIPES->cat);
  $RATING  = getTableData('ratings','recipe',$RECIPES->id);
  ?>
  <div class="<?php echo ($RECIPES->enRecipe=='yes' ? 'recipe' : 'recipeDisabled'); ?>">
    <p><a href="#" onclick="toggle_box('recipe<?php echo $RECIPES->id; ?>');return false" title="<?php echo $recipes10; ?>"><?php echo $recipes10; ?></a></p>
    <input style="vertical-align:middle" type="checkbox" name="recipe[]" value="<?php echo $RECIPES->id; ?>" />&nbsp;&nbsp;&nbsp;<a href="?p=edit&amp;id=<?php echo $RECIPES->id; ?>" title="<?php echo $script13.': '.cleanData($RECIPES->name); ?>"><?php echo cleanData($RECIPES->name); ?></a> <a href="<?php echo ($SETTINGS->modr=='yes' ? $SETTINGS->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RECIPES->name).'/'.$RECIPES->id.'/index.html' : '../?p=recipe&amp;recipe='.$RECIPES->id); ?>" title="<?php echo $ratings10; ?>" onclick="window.open(this);return false"><img src="templates/images/view.png" alt="" title="" /></a>
  </div>
  
  <div class="recipeInfo" id="recipe<?php echo $RECIPES->id; ?>"<?php echo (!SHOW_RECIPE_INFO_BOXES ? ' style="display:none"' : ''); ?>>
    <b><?php echo $recipes18; ?></b>: <?php echo $RECIPES->aDate; ?> /<?php echo ($RECIPES->submitted_by ? ' <b>'.$recipes19.'</b>: '.cleanData($RECIPES->submitted_by) : ''); ?><br />
    <b><?php echo $add2; ?></b>: <?php echo cleanData($CAT->catname); ?><br />
    <b><?php echo $recipes33; ?></b>: <a href="?p=pictures&amp;recipe=<?php echo $RECIPES->id; ?>" title="<?php echo $recipes36; ?>"><?php echo rowCount('pictures',' WHERE recipe = \''.$RECIPES->id.'\''); ?></a><?php echo (isset($RATING->total_value) && isset($RATING->total_votes) && $RATING->total_votes>0 ? ' / <b>'.$recipes20.'</b>: '.number_format($RATING->total_value/$RATING->total_votes,1).' '.$recipes54.' '.MAXIMUM_RATING_SCORE.' / ' : ' / '); ?><b><?php echo $recipes21; ?></b>: <?php echo number_format($RECIPES->hits); ?> / <b><?php echo $recipes22; ?></b>: <a href="?p=comments&amp;recipe=<?php echo $RECIPES->id; ?>" title="<?php echo $recipes41; ?>"><?php echo rowCount('comments',' WHERE recipe = \''.$RECIPES->id.'\' AND isApproved = \'no\''); ?></a> / <b><?php echo $recipes55; ?></b>: <?php echo ($RECIPES->enRecipe=='yes' ? $recipes56 : $recipes57); ?>
  </div>
  <?php
  }
  } else {
  ?>
  <span class="noData"><?php echo $recipes9; ?></span>
  <?php
  }
  if (rowCount('recipes',(isset($_GET['filter']) && $_GET['filter']!='all' ? ' WHERE cat = \''.$_GET['filter'].'\'' : ' WHERE isApproved = \'no\' '.$SQL_SEARCH))>0) {
  ?>
  <p>
  <span style="display:block;padding-top:10px"><input type="hidden" name="process" value="1" />
  <input class="button" type="submit" value="<?php echo $recipes32; ?>" title="<?php echo $recipes32; ?>" />
  </span>
  </p>
  
  </form>
  
  <?php 
    echo pageNumbers(rowCount('recipes',(isset($_GET['filter']) && $_GET['filter']!='all' ? ' WHERE cat = \''.$_GET['filter'].'\'' : ' WHERE isApproved = \'no\' '.$SQL_SEARCH)),PER_PAGE,$page);
  }
  ?>
  
  <p>&nbsp;</p>
  
</div>

<div class="right">

  <span class="headRight"><?php echo $recipes; ?>:</span>
  
  <p class="rightP">
  
  <span class="link"><a class="add" href="?p=add" title="<?php echo $recipes7; ?>"><?php echo $recipes7; ?></a></span>
  
  <span class="link"><a class="pictures" href="?p=pictures&amp;recipe=<?php echo getFirstRecipe(); ?>" title="<?php echo $recipes34; ?>"><?php echo $recipes34; ?></a></span>
  
  <span class="link"><a class="comments" href="?p=comments&amp;recipe=<?php echo getFirstRecipe(); ?>" title="<?php echo $recipes51; ?>"><?php echo $recipes51; ?></a></span>
  
  <span class="link"><a class="ratings" href="?p=ratings" title="<?php echo $recipes49; ?>"><?php echo $recipes49; ?></a></span>
  
  <span class="rline">&nbsp;</span>
  
  <span class="link"><a class="recipes" href="?p=approve-recipes" title="<?php echo $recipes14; ?>"><?php echo $recipes14; ?></a></span>
  
  <span class="link"><a class="acomments" href="?p=approve-comments" title="<?php echo $recipes11; ?>"><?php echo $recipes11; ?></a></span>
  
  </p>

  <span class="headRightMid"><?php echo $recipes2; ?>: <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript19,'LEFT'); ?> /></span>
  
  <p class="rightP">
  
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=recipes&amp;filter=all<?php echo (isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>"><?php echo $recipes16; ?></option>
  <?php
  $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
            WHERE isParent = 'yes'
            AND enCat      = 'yes'
            AND childOf    = '0'
            ORDER BY catname") or die(mysql_error());
  while ($CATS = mysql_fetch_object($q_cats)) {
  ?>
  <option value="?p=recipes&amp;filter=<?php echo $CATS->id; ?><?php echo (isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($CATS->catname); ?></option>
  <?php
  $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                WHERE isParent = 'no'
                AND enCat      = 'yes'
                AND childOf    = '".$CATS->id."'
                ORDER BY catname") or die(mysql_error());
  while ($CHILDREN = mysql_fetch_object($q_children)) {
  ?>
  <option value="?p=recipes&amp;filter=<?php echo $CHILDREN->id; ?><?php echo (isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo cleanData($CHILDREN->catname); ?></option>
  <?php
  }
  }
  ?>
  </select><br /><br />
  
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=recipes&amp;cont=all<?php echo (isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : ''); ?>"><?php echo $recipes50; ?></option>
  <?php
  $q_cont = mysql_query("SELECT * FROM ".$database['prefix']."recipes 
            WHERE enRecipe    = 'yes'
            AND isApproved    = 'no'
            AND submitted_by != ''
            GROUP BY submitted_by
            ORDER BY submitted_by
            
            ") or die(mysql_error());
  while ($CONT = mysql_fetch_object($q_cont)) {
  ?>
  <option value="?p=recipes&amp;cont=<?php echo urlencode(cleanData($CONT->submitted_by)); ?><?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : ''); ?><?php echo (isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['cont']) && $_GET['cont']==urldecode(cleanData($CONT->submitted_by)) ? ' selected="selected"' : ''); ?>><?php echo cleanData($CONT->submitted_by); ?></option>
  <?php
  }
  ?>
  </select><br /><br />
  
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=name&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name' ? ' selected="selected"' : ''); ?>><?php echo $recipes29; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=hits_desc&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='hits_desc' ? ' selected="selected"' : ''); ?>><?php echo $recipes23; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=hits_asc&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='hits_asc' ? ' selected="selected"' : ''); ?>><?php echo $recipes24; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=comments_desc&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='comments_desc' ? ' selected="selected"' : ''); ?>><?php echo $recipes25; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=comments_asc&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='comments_asc' ? ' selected="selected"' : ''); ?>><?php echo $recipes26; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=date_desc&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='date_desc' ? ' selected="selected"' : ''); ?>><?php echo $recipes30; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=date_asc&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='date_asc' ? ' selected="selected"' : ''); ?>><?php echo $recipes31; ?></option>
  <option value="?p=recipes<?php echo (isset($_GET['filter']) ? '&amp;filter='.$_GET['filter'] : '').(isset($_GET['cont']) ? '&amp;cont='.$_GET['cont'] : ''); ?>&amp;orderby=disabled&amp;page=<?php echo $page; ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='disabled' ? ' selected="selected"' : ''); ?>><?php echo $recipes57; ?></option>
  </select>
  
  </p>
  
  <span class="headRightMid"><?php echo $recipes17; ?>: <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript20,'LEFT'); ?> /></span>
  
  <form method="post" action="?p=recipes" onsubmit="return checkform(this,'<?php echo $javascript21; ?>','<?php echo $javascript22; ?>','<?php echo $javascript3; ?>','<?php echo $javascript; ?>')">
  <p class="rightP">
  
  <select name="cats[]" multiple="multiple" style="height:150px">
  <option value="all"<?php echo (!isset($_GET['filter']) ? ' selected="selected"' : ''); ?>><?php echo $recipes16; ?></option>
  <?php
  $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
            WHERE isParent = 'yes'
            AND childOf    = '0'
            AND enCat      = 'yes'
            ORDER BY catname") or die(mysql_error());
  while ($CATS = mysql_fetch_object($q_cats)) {
  ?>
  <option value="<?php echo $CATS->id; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($CATS->catname); ?></option>
  <?php
  $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                WHERE isParent = 'no'
                AND childOf    = '".$CATS->id."'
                AND enCat      = 'yes'
                ORDER BY catname") or die(mysql_error());
  while ($CHILDREN = mysql_fetch_object($q_children)) {
  ?>
  <option value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo cleanData($CHILDREN->catname); ?></option>
  <?php
  }
  }
  ?>
  </select>
  
  <span style="display:block;padding-top:10px;line-height:20px"><input style="vertical-align:middle" type="checkbox" name="hits" id="hits" value="1" onclick="if (this.checked) { resetOptions('hits') }" /> <?php echo $recipes4; ?> <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript15,'LEFT'); ?> /><br />
  <input style="vertical-align:middle" type="checkbox" name="ratings" id="ratings" value="1" onclick="if (this.checked) { resetOptions('ratings') }" /> <?php echo $recipes3; ?> <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript16,'LEFT'); ?> /><br />
  <input style="vertical-align:middle" type="checkbox" name="delcom" id="delcom" value="1" onclick="if (this.checked) { resetOptions('delcom') }" /> <?php echo $recipes12; ?> <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript17,'LEFT'); ?> /><br />
  <input style="vertical-align:middle" type="checkbox" name="delrec" id="delrec" value="1" onclick="if (this.checked) { resetOptions('delrec') }" /> <b><?php echo $recipes13; ?> <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript18,'LEFT'); ?> /></b>
  </span>
  
  <span style="display:block;padding-top:10px"><input type="hidden" name="reset" value="1" />
  <input class="button" type="submit" value="<?php echo $recipes5; ?>" title="<?php echo $recipes5; ?>" />
  </span>
  
  </p>
  </form>
  
</div>

<br class="break" />

</div>
<!-- End Body Area -->
