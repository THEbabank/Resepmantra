<?php

if (!defined('PARENT')) { include('index.html'); exit; }

?>
<div id="bodyArea">
<?php
if (isset($OK)) {
echo actionCompleted($add14);
}
?>
<script type="text/javascript">
function checkformAdd(form) {
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

  <p class="headLeft"><?php echo $header2.(HELP_TIPS ? ' <span style="font-weight:normal">- '.$script6.'</span>' : ''); ?>:</p>
  
  <div id="formArea">
  <form method="post" action="?p=add" onsubmit="return checkformAdd(this)" enctype="multipart/form-data">
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript46); ?>><?php echo $add; ?>:</label>
    <input class="box" type="text" name="name" value="<?php echo (isset($_POST['name']) ? cleanData($_POST['name']) : ''); ?>" />
    
    <label <?php echo hoverHelpTip($javascript47); ?>><?php echo $add2; ?>:</label>
    <select name="cat">
    <?php
    $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
              WHERE isParent = 'yes'
              AND childOf    = '0'
              ORDER BY catname") or die(mysql_error());
    while ($CATS = mysql_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"><?php echo cleanData($CATS->catname); ?></option>
    <?php
    $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                  WHERE isParent = 'no'
                  AND childOf    = '".$CATS->id."'
                  ORDER BY catname") or die(mysql_error());
    while ($CHILDREN = mysql_fetch_object($q_children)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>">- <?php echo cleanData($CHILDREN->catname); ?></option>
    <?php
    }
    }
    ?>
    </select>
    
    <label <?php echo hoverHelpTip($javascript48); ?>><?php echo $add13; ?>: <span style="font-weight:normal">(<?php echo $script12; ?>)</span></label>
    <input class="box" type="text" name="submitted_by" value="<?php echo (isset($_POST['submitted_by']) ? cleanData($_POST['submitted_by']) : ''); ?>" />
    
    <label <?php echo hoverHelpTip($javascript51); ?>><?php echo $add12; ?>:</label>
    <input type="radio" name="enRecipe" value="yes"<?php echo (isset($_POST['enRecipe']) && $_POST['enRecipe']=='yes' ? ' checked="checked""' : (!isset($_POST['enRecipe']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRecipe" value="no"<?php echo (isset($_POST['enRecipe']) && $_POST['enRecipe']=='no' ? ' checked="checked""' : ''); ?> /> <?php echo $settings7; ?>
    
    </td>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript55); ?>><?php echo $add7; ?>:</label>
    <input class="box" type="text" name="metaDesc" value="<?php echo (isset($_POST['metaDesc']) ? cleanData($_POST['metaDesc']) : ''); ?>" />
    
    <label <?php echo hoverHelpTip($javascript56); ?>><?php echo $add8; ?>:</label>
    <input class="box" type="text" name="metaKeys" value="<?php echo (isset($_POST['metaKeys']) ? cleanData($_POST['metaKeys']) : ''); ?>" />
    
    <label <?php echo hoverHelpTip($javascript49); ?>><?php echo $add10; ?>:</label>
    <input type="radio" name="enComments" value="yes"<?php echo (isset($_POST['enComments']) && $_POST['enComments']=='yes' ? ' checked="checked""' : (!isset($_POST['enComments']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enComments" value="no"<?php echo (isset($_POST['enComments']) && $_POST['enComments']=='no' ? ' checked="checked""' : ''); ?> /> <?php echo $settings7; ?>
    
    <br /><br />
    
    <label <?php echo hoverHelpTip($javascript50); ?>><?php echo $add11; ?>:</label>
    <input type="radio" name="enRating" value="yes"<?php echo (isset($_POST['enRating']) && $_POST['enRating']=='yes' ? ' checked="checked""' : (!isset($_POST['enRating']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRating" value="no"<?php echo (isset($_POST['enRating']) && $_POST['enRating']=='no' ? ' checked="checked""' : ''); ?> /> <?php echo $settings7; ?>
    
    </td>
  </tr>
  <tr>
    <td colspan="2">
    
    <label <?php echo hoverHelpTip($javascript52); ?>><?php echo $add3; ?>:</label>
    <textarea cols="40" rows="8" name="ingredients"><?php echo (isset($_POST['ingredients']) ? cleanData($_POST['ingredients']) : ''); ?></textarea>
    
    </td>
  </tr>
  <tr>
    <td colspan="2">
    
    <label <?php echo hoverHelpTip($javascript53); ?>><?php echo $add4; ?>:</label>
    <textarea cols="40" rows="8" name="instructions"><?php echo (isset($_POST['instructions']) ? cleanData($_POST['instructions']) : ''); ?></textarea>
    
    </td>
  </tr>
  <tr>
    <td colspan="2">
    
    <label <?php echo hoverHelpTip($javascript54); ?>><?php echo $add6; ?>:</label>
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
    
    <span style="display:block;text-align:center;padding:20px 0 10px 0">
     <input type="hidden" name="process" value="1" />
     <input class="button" type="submit" value="<?php echo $add5; ?>" title="<?php echo $add5; ?>" />
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
