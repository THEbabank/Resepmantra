<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  Written by David Ian Bennett
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Category Management
----------------------------------------------*/

if (!defined('PARENT')) { include('index.html'); exit; }

// Are we in edit mode?
if (isset($_GET['edit'])) {
  $EDIT = true;
  // Get category data and populate the post array..
  $CAT = getTableData('categories','id',(int)$_GET['edit']);
  if (!isset($_POST['edit'])) {
    foreach ($CAT AS $key => $value) {
      if ($key=='isParent' && $value=='yes') {
        $_POST['type'] = 'new';
      } elseif ($key=='childOf' && ctype_digit($value) && $value>0) {
        $_POST['type'] = $value;
      } else {
        $_POST[$key] = $value;
      }
    }
  }
}

?>
<!-- Start Body Area -->
<div id="bodyArea">
<script type="text/javascript">
function checkformCats(form) {
  var message = '';
  if (form.catname.value=='') {
    message += '- <?php echo $javascript2; ?>\n';
  }
  if (message) {
    alert('<?php echo $javascript3; ?>\n\n'+message);
    return false;
  }
}
</script>
<?php
if (isset($OK)) {
echo actionCompleted($cats19);
}
if (isset($OK2)) {
echo actionCompleted($cats20);
}
if (isset($OK3)) {
echo actionCompleted($cats21);
}
?>
<div class="mainHead">

  <span class="headLeft"><?php echo (isset($EDIT) ? $cats11 : $cats2).(HELP_TIPS ? ' <span style="font-weight:normal">- '.$script6.'</span>' : ''); ?>:</span>
  
  <div id="formArea">
  <form method="post" action="?p=cats<?php echo (isset($EDIT) ? '&amp;edit='.$CAT->id : ''); ?>" onsubmit="return checkformCats(this)">
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript6); ?>><?php echo $cats3; ?>:</label>
    <input class="box" type="text" maxlength="200" name="catname" value="<?php echo (isset($_POST['catname']) ? cleanData($_POST['catname']) : ''); ?>" />
    
    <label <?php echo hoverHelpTip($javascript7); ?>><?php echo $cats7; ?>: <span style="font-weight:normal">(<?php echo $script12; ?>)</span></label>
    <input class="box" type="text" name="metaDesc" value="<?php echo (isset($_POST['metaDesc']) ? cleanData($_POST['metaDesc']) : ''); ?>" /> 
    
    <label <?php echo hoverHelpTip($javascript8); ?>><?php echo $cats10; ?>: <span style="font-weight:normal">(<?php echo $script12; ?>)</span></label>
    <input class="box" type="text" name="metaKeys" value="<?php echo (isset($_POST['metaKeys']) ? cleanData($_POST['metaKeys']) : ''); ?>" /> 
    
    <label <?php echo hoverHelpTip($javascript9); ?>><?php echo $cats14; ?>:</label>
    <select name="type">
    <option value="new"><?php echo $cats; ?></option>
    <?php
    $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
              WHERE isParent = 'yes'
              AND childOf    = '0'
              ".(isset($EDIT) ? 'AND id!= \''.$CAT->id.'\'' : '')."
              ORDER BY catname") or die(mysql_error());
    if (mysql_num_rows($q_cats)>0) {
    ?>
    <optgroup label="<?php echo $cats13; ?>">
    <?php
    while ($CATS = mysql_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"<?php echo (isset($_POST['type']) && $_POST['type']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($CATS->catname); ?></option>
    <?php
    }
    ?>
    </optgroup>
    <?php
    }
    ?>
    </select>
    
    </td>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript11); ?>><?php echo $cats15; ?>:</label>
    <input type="radio" name="enComments" value="yes"<?php echo (isset($_POST['enComments']) && $_POST['enComments']=='yes' ? ' checked="checked"' : (!isset($_POST['enComments']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enComments" value="no"<?php echo (isset($_POST['enComments']) && $_POST['enComments']=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <br /><br />
    
    <label <?php echo hoverHelpTip($javascript12); ?>><?php echo $cats16; ?>:</label>
    <input type="radio" name="enRating" value="yes"<?php echo (isset($_POST['enRating']) && $_POST['enRating']=='yes' ? ' checked="checked"' : (!isset($_POST['enRating']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRating" value="no"<?php echo (isset($_POST['enRating']) && $_POST['enRating']=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <br /><br />
    
    <label <?php echo hoverHelpTip($javascript13); ?>><?php echo $cats17; ?>:</label>
    <input type="radio" name="enRecipes" value="yes"<?php echo (isset($_POST['enRecipes']) && $_POST['enRecipes']=='yes' ? ' checked="checked"' : (!isset($_POST['enRecipes']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRecipes" value="no"<?php echo (isset($_POST['enRecipes']) && $_POST['enRecipes']=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <br /><br />
    
    <label <?php echo hoverHelpTip($javascript14); ?>><?php echo $cats18; ?>:</label>
    <input type="radio" name="enCat" value="yes"<?php echo (isset($_POST['enCat']) && $_POST['enCat']=='yes' ? ' checked="checked"' : (!isset($_POST['enCat']) ? ' checked="checked"' : '')); ?> /> <?php echo $settings6; ?> <input type="radio" name="enCat" value="no"<?php echo (isset($_POST['enCat']) && $_POST['enCat']=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    </td>
  </tr>
  <tr>
    <td colspan="2">
    
    <label <?php echo hoverHelpTip($javascript10); ?>><?php echo $cats4; ?>: <span style="font-weight:normal">(<?php echo $script12; ?>)</span></label>
    <textarea cols="40" rows="8" name="comments"><?php echo (isset($_POST['comments']) ? cleanData($_POST['comments']) : ''); ?></textarea>
    
    <span style="display:block;text-align:center;padding:10px 0 10px 0">
     <input type="hidden" name="<?php echo (isset($EDIT) ? 'edit' : 'process'); ?>" value="<?php echo (isset($EDIT) ? $CAT->id : '1'); ?>" />
     <input class="button" type="submit" value="<?php echo (isset($EDIT) ? $cats11 : $cats2); ?>" title="<?php echo (isset($EDIT) ? $cats11 : $cats2); ?>" /><?php echo (isset($EDIT) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="button2" type="button" onclick="window.location=\'?p=cats\'" value="'.$script5.'" title="'.$script5.'" />' : ''); ?>
    </span>
    
    </td>
  </tr>
  </table>
  </form>
  </div>
  
  <span class="headLeft"><?php echo $cats6; ?>:</span>
  
  <?php
  $q_cats = mysql_query("SELECT * FROM ".$database['prefix']."categories 
            WHERE isParent = 'yes'
            AND childOf    = '0'
            ORDER BY catname") or die(mysql_error());
  if (mysql_num_rows($q_cats)>0) {
    while ($CATS = mysql_fetch_object($q_cats)) {
    ?>
    <div class="catWrap">
     <div class="catLeft"><a href="?p=cats&amp;edit=<?php echo $CATS->id; ?>" title="<?php echo $script13.': '.cleanData($CATS->catname); ?>"><?php echo ($CATS->enCat=='no' ? '<span style="color:#730406;font-weight:bold">'.cleanData($CATS->catname).'</span>' : cleanData($CATS->catname)); ?></a> [<a style="color:red" href="?p=cats&amp;del=<?php echo $CATS->id; ?>" title="<?php echo $script14.': '.cleanData($CATS->catname); ?>" onclick="return confirmMessage('<?php echo $javascript4; ?>')">X</a>]</div>
     <div class="catRight">
     <?php
     $q_children = mysql_query("SELECT * FROM ".$database['prefix']."categories 
                   WHERE isParent = 'no'
                   AND childOf    = '".$CATS->id."'
                   ORDER BY catname") or die(mysql_error());
     if (mysql_num_rows($q_children)>0) {
     $rCount = 0;
     while ($CHILDREN = mysql_fetch_object($q_children)) {
     ?>
     <a href="?p=cats&amp;edit=<?php echo $CHILDREN->id; ?>" title="<?php echo $script13.': '.cleanData($CHILDREN->catname); ?>"><?php echo ($CHILDREN->enCat=='no' ? '<span style="color:#730406;font-weight:bold">'.cleanData($CHILDREN->catname).'</span>' : cleanData($CHILDREN->catname)); ?></a> [<a style="color:red" href="?p=cats&amp;del=<?php echo $CHILDREN->id; ?>" title="<?php echo $script14.': '.cleanData($CHILDREN->catname); ?>" onclick="return confirmMessage('<?php echo $javascript; ?>')">X</a>]
     <?php
     if (++$rCount!=mysql_num_rows($q_children)) {
     echo ', ';
     }
     }
     } else {
       echo $cats8;
     }
     ?>
     </div>
     <br class="break" />
    </div>
    <?php
    }
  } else {
  ?>
  <span class="noData"><?php echo $cats5; ?></span>
  <?php
  }
  ?>

</div>

<br class="break" />

</div>
<!-- End Body Area -->
