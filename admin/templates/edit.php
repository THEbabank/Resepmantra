<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  Written by David Ian Bennett
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Admin - Edit Recipe
----------------------------------------------*/

if (!defined('PARENT')) { include('index.html'); exit; }

$RECIPE = getTableData('recipes','id',(int)$_GET['id']);

?>
<div id="bodyArea">
<?php
if (isset($OK)) {
echo actionCompleted($edit2);
}
?>
<script type="text/javascript">
function checkformEdit(form) {
  var message = '';
  if (form.name.value=='') {
    message += '- <?php echo $javascript43; ?>\n';
  }
  if (form.ingredients.value=='') {
    message +='- <?php echo $javascript44; ?>\n';
  }
  if (form.instructions.value=='') {
    message +='- <?php echo $javascript45; ?>\n';
  }
  if (message) {
    alert('<?php echo $javascript3; ?>\n\n'+message);
    return false;
  }
}
</script>

<div class="mainHead">

  <p class="headLeft">
  
  <span class="quickJump"><?php echo $script15; ?>:
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="0">---</option>
  <option value="?p=edit&amp;id=<?php echo $_GET['id']; ?>" selected="selected"><?php echo $edit; ?></option>
  <?php
  if ($RECIPE->isApproved=='no') {
  ?>
  <option value="?p=comments&amp;recipe=<?php echo $_GET['id']; ?>"><?php echo $comments19; ?></option>
  <?php
  }
  ?>
  <option value="?p=pictures&amp;recipe=<?php echo $_GET['id']; ?>"><?php echo $pictures9; ?></option>
  </select>
  </span>
  
  <?php echo $edit.(HELP_TIPS ? ' <span style="font-weight:normal">- '.$script6.'</span>' : ''); ?>: <a href="<?php echo ($RECIPE->isApproved=='yes' ? '?p=approve-recipes' : '?p=recipes'); ?>" title="<?php echo $script5; ?>"><?php echo $script5; ?></a></p>
  
  <div id="formArea">
  <form method="post" action="?p=edit&amp;id=<?php echo $_GET['id']; ?>" onsubmit="return checkformEdit(this)">
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript46); ?>><?php echo $add; ?>:</label>
    <input class="box" type="text" name="name" value="<?php echo cleanData($RECIPE->name); ?>" />
    
    <label <?php echo hoverHelpTip($javascript47); ?>><?php echo $add2; ?>:</label>
    <select name="cat">
    <?php
    $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
              WHERE isParent = 'yes'
              AND childOf    = '0'
              ORDER BY catname") or die(mysql_error());
    while ($CATS = mysql_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"<?php echo ($RECIPE->cat==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($CATS->catname); ?></option>
    <?php
    $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                  WHERE isParent = 'no'
                  AND childOf    = '".$CATS->id."'
                  ORDER BY catname") or die(mysql_error());
    while ($CHILDREN = mysql_fetch_object($q_children)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>"<?php echo ($RECIPE->cat==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo cleanData($CHILDREN->catname); ?></option>
    <?php
    }
    }
    ?>
    </select>
    
    <label <?php echo hoverHelpTip($javascript48); ?>><?php echo $add13; ?>: <span style="font-weight:normal">(<?php echo $script12; ?>)</span></label>
    <input class="box" type="text" name="submitted_by" value="<?php echo cleanData($RECIPE->submitted_by); ?>" />
    <?php
    if ($RECIPE->isApproved=='no') {
    ?>
    <label <?php echo hoverHelpTip($javascript51); ?>><?php echo $add12; ?>:</label>
    <input type="radio" name="enRecipe" value="yes"<?php echo ($RECIPE->enRecipe=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRecipe" value="no"<?php echo ($RECIPE->enRecipe=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    <?php
    } else {
    ?>
    <label <?php echo hoverHelpTip($javascript51); ?>><?php echo $add12; ?>:</label>
    <span style="color:#4e616c"><?php echo $recipes40; ?></span>
    <?php
    }
    ?>
    </td>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript55); ?>><?php echo $add7; ?>:</label>
    <input class="box" type="text" name="metaDesc" value="<?php echo cleanData($RECIPE->metaDesc); ?>" />
    
    <label <?php echo hoverHelpTip($javascript56); ?>><?php echo $add8; ?>:</label>
    <input class="box" type="text" name="metaKeys" value="<?php echo cleanData($RECIPE->metaKeys); ?>" />
    
    <label <?php echo hoverHelpTip($javascript49); ?>><?php echo $add10; ?>:</label>
    <input type="radio" name="enComments" value="yes"<?php echo ($RECIPE->enComments=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enComments" value="no"<?php echo ($RECIPE->enComments=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <br /><br />
    
    <label <?php echo hoverHelpTip($javascript50); ?>><?php echo $add11; ?>:</label>
    <input type="radio" name="enRating" value="yes"<?php echo ($RECIPE->enRating=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRating" value="no"<?php echo ($RECIPE->enRating=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    </td>
  </tr>
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript57); ?>><?php echo $edit5; ?>:</label>
    <input class="box" type="text" name="hits" value="<?php echo cleanData($RECIPE->hits); ?>" style="width:20%" />
    
    </td>
    
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript58); ?>><?php echo $edit4; ?>:</label>
    <input class="box" type="text" name="addDate" value="<?php echo cleanData($RECIPE->addDate); ?>" onclick= "scwShow(scwID('addDate'), event);" style="width:30%" />
    
    </td>
  </tr> 
  <tr>
    <td colspan="2">
    
    <label <?php echo hoverHelpTip($javascript52); ?>><?php echo $add3; ?>:</label>
    <textarea cols="40" rows="8" name="ingredients"><?php echo cleanData($RECIPE->ingredients); ?></textarea>
    
    </td>
  </tr> 
  <tr>
    <td colspan="2">
    
    <label <?php echo hoverHelpTip($javascript53); ?>><?php echo $add4; ?>:</label>
    <textarea cols="40" rows="8" name="instructions"><?php echo cleanData($RECIPE->instructions); ?></textarea>
    
    <span style="display:block;text-align:center;padding:20px 0 10px 0">
     <input type="hidden" name="process" value="1" />
     <input class="button" type="submit" value="<?php echo $edit; ?>" title="<?php echo $edit; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input class="button2" type="button" onclick="window.location='<?php echo ($RECIPE->isApproved=='yes' ? '?p=approve-recipes' : '?p=recipes'); ?>'" value="<?php echo $script5; ?>" title="<?php echo $script5; ?>" />
    </span>
    
    </td>
  </tr>
  </table>
  </form>
  
  </div>  

</div>

<br class="break" />

</div>
<!-- End Body Area --> 
