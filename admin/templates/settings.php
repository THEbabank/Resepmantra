<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  Written by David Ian Bennett
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Admin - Settings
----------------------------------------------*/

if (!defined('PARENT')) { include('index.html'); exit; }

?>
<div id="bodyArea">
<?php
if (isset($OK)) {
echo actionCompleted($settings28);
// Reload..
$SETTINGS = mysql_fetch_object(
            mysql_query("SELECT * FROM ".$database['prefix']."settings LIMIT 1")
            );
}
?>
<div class="mainHead">

  <p class="headLeft"><?php echo $header4.(HELP_TIPS ? ' <span style="font-weight:normal">- '.$script6.'</span>' : ''); ?>:</p>
  
  <div id="formArea">
  <form method="post" action="?p=settings">
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript25); ?>><?php echo $settings4; ?>:</label>
    <input class="box" type="text" name="website" value="<?php echo cleanData($SETTINGS->website); ?>" />
    
    <label <?php echo hoverHelpTip($javascript26); ?>><?php echo $settings22; ?>:</label>
    <input class="box" type="text" name="install_path" value="<?php echo cleanData($SETTINGS->install_path); ?>" />
    
    <label <?php echo hoverHelpTip($javascript27); ?>><?php echo $settings14; ?>:</label>
    <input class="box" type="text" name="server_path" value="<?php echo cleanData($SETTINGS->server_path); ?>" />
    
    <label <?php echo hoverHelpTip($javascript28); ?>><?php echo $settings3; ?>:</label>
    <select name="language">
    <?php
    $showlang = opendir(REL_PATH.'lang/');
    while (false !== ($read = readdir($showlang))) {
      if (!in_array($read,array('.','..','index.html'))) {
      ?>
      <option<?php echo ($read==$SETTINGS->language ? ' selected="selected"' : ''); ?>><?php echo $read; ?></option>
      <?php
      }
    }
    closedir($showlang);
    ?>
    </select>
    
    <label <?php echo hoverHelpTip($javascript29); ?>><?php echo $settings5; ?>:</label>
    <input class="box" type="text" name="total" style="width:10%" value="<?php echo $SETTINGS->total; ?>" />
    
    <label <?php echo hoverHelpTip($javascript30); ?>><?php echo $settings21; ?>:</label>
    <input type="radio" name="modr" value="yes"<?php echo ($SETTINGS->modr=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="modr" value="no"<?php echo ($SETTINGS->modr=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <label <?php echo hoverHelpTip($javascript64); ?>><?php echo $settings29; ?>:</label>
    <input type="radio" name="enRSS" value="yes"<?php echo ($SETTINGS->enRSS=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRSS" value="no"<?php echo ($SETTINGS->enRSS=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    </td>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript31); ?>><?php echo $settings; ?>:</label>
    <input class="box" type="text" name="email" value="<?php echo cleanData($SETTINGS->email); ?>" />
    
    <label <?php echo hoverHelpTip($javascript32); ?>><?php echo $settings2; ?>:</label>
    <input class="box" type="text" name="metaDesc" value="<?php echo cleanData($SETTINGS->metaDesc); ?>" />
    
    <label <?php echo hoverHelpTip($javascript33); ?>><?php echo $settings9; ?>:</label>
    <input class="box" type="text" name="metaKeys" value="<?php echo cleanData($SETTINGS->metaKeys); ?>" />
    
    <label <?php echo hoverHelpTip($javascript65); ?>><?php echo $settings30; ?>:</label>
    <input type="radio" name="enCloudTags" value="yes"<?php echo ($SETTINGS->enCloudTags=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enCloudTags" value="no"<?php echo ($SETTINGS->enCloudTags=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <label <?php echo hoverHelpTip($javascript34); ?>><?php echo $settings11; ?>:</label>
    <input type="radio" name="enCommApp" value="yes"<?php echo ($SETTINGS->enCommApp=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enCommApp" value="no"<?php echo ($SETTINGS->enCommApp=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <label <?php echo hoverHelpTip($javascript35); ?>><?php echo $settings12; ?>:</label>
    <input type="radio" name="enRecApp" value="yes"<?php echo ($SETTINGS->enRecApp=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enRecApp" value="no"<?php echo ($SETTINGS->enRecApp=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <label <?php echo hoverHelpTip($javascript36); ?>><?php echo $settings13; ?>:</label>
    <input type="radio" name="enSpam" value="yes"<?php echo ($SETTINGS->enSpam=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="enSpam" value="no"<?php echo ($SETTINGS->enSpam=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    </td>
  </tr>
  </table>
  
  <p class="headLeft" style="margin:20px 0 10px 0"><?php echo $settings23.(HELP_TIPS ? ' <span style="font-weight:normal">- '.$script6.'</span>' : ''); ?>:</p>
  
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript37); ?>><?php echo $settings24; ?>:</label>
    <input class="box" type="text" name="maxImages" style="width:10%" value="<?php echo $SETTINGS->maxImages; ?>" />
    
    <label <?php echo hoverHelpTip($javascript38); ?>><?php echo $settings25; ?>:</label>
    <input class="box" type="text" name="validImages" value="<?php echo $SETTINGS->validImages; ?>" />
    
    </td>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript39); ?>><?php echo $settings26; ?>:</label>
    <input class="box" type="text" name="autoResize" value="<?php echo $SETTINGS->autoResize; ?>" />
    
    <label <?php echo hoverHelpTip($javascript40); ?>><?php echo $settings27; ?>:</label>
    <input class="box" type="text" name="maxFileSize" value="<?php echo $SETTINGS->maxFileSize; ?>" />
    
    </td>
  </tr>
  </table>
  
  <p class="headLeft" style="margin:20px 0 10px 0"><?php echo $settings15.(HELP_TIPS ? ' <span style="font-weight:normal">- '.$script6.'</span>' : ''); ?>:</p>
  
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="width:50%">
    
    <label <?php echo hoverHelpTip($javascript41); ?>><?php echo $settings16; ?>:</label>
    <input type="radio" name="smtp" value="yes"<?php echo ($SETTINGS->smtp=='yes' ? ' checked="checked"' : ''); ?> /> <?php echo $settings6; ?> <input type="radio" name="smtp" value="no"<?php echo ($SETTINGS->smtp=='no' ? ' checked="checked"' : ''); ?> /> <?php echo $settings7; ?>
    
    <label><?php echo $settings17; ?>:</label>
    <input class="box" type="text" name="smtp_host" value="<?php echo $SETTINGS->smtp_host; ?>" />
    
    <label><?php echo $settings18; ?>:</label>
    <input class="box" type="text" name="smtp_user" value="<?php echo $SETTINGS->smtp_user; ?>" />
    
    </td>
    <td style="width:50%">
    
    <label><?php echo $settings19; ?>:</label>
    <input class="box" type="text" name="smtp_pass" value="<?php echo $SETTINGS->smtp_pass; ?>" />
    
    <label <?php echo hoverHelpTip($javascript42); ?>><?php echo $settings20; ?>:</label>
    <input class="box" type="text" name="smtp_port" style="width:10%" value="<?php echo $SETTINGS->smtp_port; ?>" />
    
    </td>
  </tr>
  </table>
  
  <p>
  <span style="display:block;text-align:center;padding:30px 0 10px 0">
    <input type="hidden" name="process" value="1" />
    <input class="button" type="submit" value="<?php echo $settings8; ?>" title="<?php echo $settings8; ?>" />
  </span>
  </p>
  
  </form>
  </div>
  
</div>

<br class="break" />

</div>
<!-- End Body Area --> 
