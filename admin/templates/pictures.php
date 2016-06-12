<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  Written by David Ian Bennett
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Admin - Manage Pictures
----------------------------------------------*/

if (!defined('PARENT')) { include('index.html'); exit; }
$thisRecipe = getTableData('recipes','id',(int)$_GET['recipe']);
?>
<!-- Start Body Area -->
<div id="bodyArea">
<?php
if (isset($OK)) {
echo actionCompleted($imgRun.' '.$pictures7);
}
if (isset($OK2)) {
echo actionCompleted($pictures8);
}
if (isset($OK3)) {
echo actionCompleted($pictures11);
}
?>
<script type="text/javascript">
function checkformPictures(form) {
  var loop    = false;
  for (i=0; i<form.elements['image[]'].length; i++) { 
    if (form.elements['image[]'][i].value!= '') { 
     return true; 
    } 
  } 
  if (loop==false) {
    alert('<?php echo $javascript3; ?>\n\n<?php echo $javascript59; ?>');
    return false;
  }
}
</script>
<div class="mainHead">

  <span class="headLeft" style="margin-bottom:10px">
  
  <span class="quickJump"><?php echo $script15; ?>:
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="0">---</option>
  <option value="?p=edit&amp;id=<?php echo $_GET['recipe']; ?>"><?php echo $edit; ?></option>
  <?php
  if ($thisRecipe->isApproved=='no') {
  ?>
  <option value="?p=comments&amp;recipe=<?php echo $_GET['recipe']; ?>" selected="selected"><?php echo $comments19; ?></option>
  <?php
  }
  ?>
  <option value="?p=pictures&amp;recipe=<?php echo $_GET['recipe']; ?>" selected="selected"><?php echo $pictures9; ?></option>
  </select>
  </span>
  
  <?php echo str_replace('{url}',($thisRecipe->isApproved=='yes' ? '?p=approve-recipes' : '?p=recipes'),$pictures2); ?>
  
  </span>

  <span class="headLeft" style="background:#fff;color:#4e626c"><?php echo $pictures; ?>:
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <?php
  $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
            WHERE isParent = 'yes'
            AND childOf    = '0'
            ORDER BY catname") or die(mysql_error());
  while ($CATS = mysql_fetch_object($q_cats)) {
  $q_recipes = mysql_query("SELECT * FROM ".$database['prefix']."recipes
               WHERE cat = '$CATS->id'
               ".($thisRecipe->isApproved=='yes' ? 'AND id = \''.$_GET['recipe'].'\'' : 'AND isApproved = \'no\'')."
               ORDER BY name
               ") or die(mysql_error());
  if (mysql_num_rows($q_recipes)>0) {
  ?>
  <optgroup label="<?php echo cleanData($CATS->catname); ?>">
  <?php
  while ($RECIPES = mysql_fetch_object($q_recipes)) {
  ?>
  <option value="?p=pictures&amp;recipe=<?php echo $RECIPES->id ; ?>"<?php echo (isset($_GET['recipe']) && $_GET['recipe']==$RECIPES->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($RECIPES->name) ; ?> (<?php echo rowCount('pictures',' WHERE recipe = \''.$RECIPES->id.'\''); ?>)</option>
  <?php
  }
  ?>
  </optgroup>
  <?php
  }
  $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                WHERE isParent = 'no'
                AND childOf    = '".$CATS->id."'
                ORDER BY catname") or die(mysql_error());
  while ($CHILDREN = mysql_fetch_object($q_children)) {
  $q_recipes = mysql_query("SELECT * FROM ".$database['prefix']."recipes
               WHERE cat = '$CHILDREN->id'
               ORDER BY name
               ") or die(mysql_error());
  if (mysql_num_rows($q_recipes)>0) {
  ?>
  <optgroup label="<?php echo cleanData($CHILDREN->catname); ?>">
  <?php
  while ($RECIPES = mysql_fetch_object($q_recipes)) {
  ?>
  <option value="?p=pictures&amp;recipe=<?php echo $RECIPES->id ; ?>"<?php echo (isset($_GET['recipe']) && $_GET['recipe']==$RECIPES->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($RECIPES->name) ; ?> (<?php echo rowCount('pictures',' WHERE recipe = \''.$RECIPES->id.'\''); ?>)</option>
  <?php
  }
  ?>
  </optgroup>
  <?php
  }
  }
  }
  ?>
  </select>
  
  </span>
  
  <?php
  if (isset($_GET['recipe'])) {
  ?>
  
  <span class="headLeft" style="margin:10px 0 10px 0"><?php echo $pictures3; ?>:</span>
  
  <form method="post" action="?p=pictures&amp;recipe=<?php echo $_GET['recipe']; ?>" enctype="multipart/form-data" onsubmit="return checkformPictures(this)">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <?php
    $loop = 0;
    for ($i=1; $i<ADMIN_UPLOAD_PIC_BOXES+1; $i++) {
    ++$loop;
    ?>
    <td style="width:33%<?php echo ($i>3 ? ';padding-top:10px' : ''); ?>"><input class="filebox" type="file" name="image[]" /></td>
    <?php
    if ($i%3==0) {
      echo ($loop!=ADMIN_UPLOAD_PIC_BOXES ? '</tr>'."\n" : '');
      echo ($loop!=ADMIN_UPLOAD_PIC_BOXES ? '<tr>'."\n" : '');
    }
    }
    ?>
    </tr>
    </table>
    <p>
    
    <span style="display:block;text-align:center;padding:30px 0 10px 0">
     <input type="hidden" name="process" value="1" />
     <input class="button" type="submit" value="<?php echo $pictures5; ?>" title="<?php echo $pictures5; ?>" />
    </span>
    </p>
    </form>
  
  <span class="headLeft" style="margin:10px 0 10px 0">
  
  <?php
  if (rowCount('pictures',' WHERE recipe = \''.$thisRecipe->id.'\'')>0) {
  ?>
  <span class="allPics"><a href="?p=pictures&amp;recipe=<?php echo $_GET['recipe']; ?>&amp;allpics=1" onclick="return confirmMessage('<?php echo $javascript; ?>')" title="<?php echo $pictures10; ?>"><?php echo $pictures10; ?></a></span>
  <?php 
  }
  echo $pictures4; ?>
  </span>
  
  <?php
  $q_pics = mysql_query("SELECT * FROM ".$database['prefix']."pictures
            WHERE recipe = '".(int)$_GET['recipe']."'
            ") or die(mysql_error());
  if (mysql_num_rows($q_pics)>0) {
  ?>
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
  <?php
  while ($PICS = mysql_fetch_object($q_pics)) {
  $RECIPE = getTableData('recipes','id',$PICS->recipe);
  $run    = ++$count;
  ?>
  <td style="padding:5px;width:<?php echo (mysql_num_rows($q_pics)<4 ? round(100/mysql_num_rows($q_pics)) : '25'); ?>%;text-align:center"><img class="pic" onclick="enlarge(this);" longdesc="../templates/images/recipes/<?php echo $PICS->picPath; ?>" src="../templates/images/recipes/<?php echo $PICS->picPath; ?>" alt="<?php echo cleanData($RECIPE->name); ?>" title="<?php echo cleanData($RECIPE->name); ?>" />
  <span style="display:block;padding-top:3px"><a href="?p=pictures&amp;picture=<?php echo $PICS->picPath; ?>&amp;id=<?php echo $PICS->id; ?>&amp;recipe=<?php echo $PICS->recipe; ?>" title="<?php echo $script14; ?>" onclick="return confirmMessage('<?php echo $javascript; ?>')"><?php echo $script14; ?></a></span>
  </td>
  <?php
  if ($run%4==0) {
    echo ($run!=mysql_num_rows($q_pics) ? '</tr>'."\n" : '');
    echo ($run!=mysql_num_rows($q_pics) ? '<tr>'."\n" : '');
  }
  }
  ?>
  </tr>
  </table>
  <?php
  } else {
  ?>
  <span class="noData"><?php echo $pictures6; ?></span>
  <?php
  }
  
  }
  ?>

</div>

<br class="break" />

</div>
<!-- End Body Area -->
