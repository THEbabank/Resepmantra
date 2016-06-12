<?php if (!defined('PARENT')) { die('You do not have permission to view this file!!!'); } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=<?php echo $charset; ?>" />
<title><?php echo $pageTitle; ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
  body {
    background:#730406;
    padding-top:100px;
  }
</style>
<script type="text/javascript" src="templates/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>


<body onload="document.login.user.focus()">
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script type="text/javascript">
function checkform(form) {
  var message = '';
  if (form.user.value=='') {
    message += '- <?php echo $login5; ?>\n';
  }
  if (form.pass.value=='') {
    message +='- <?php echo $login6; ?>\n';
  }
  if (message) {
    alert('<?php echo $javascript3; ?>\n\n'+message);
    return false;
  }
}
</script>

<div id="loginWrapper">

<div id="loginHeader" class="tableTDHead">
 <p><?php echo $pageTitle; ?></p>
</div>

<div id="loginContent">
 <form method="post" name="login" action="?p=login" onsubmit="return checkform(this)">
 <p><label><?php echo $login; ?>:</label>
 <input class="box" type="text" name="user" value="<?php echo (isset($_POST['user']) ? cleanData($_POST['user']) : ''); ?>" />
 <?php echo (isset($U_ERROR) ? '<span class="error">[<b>X</b>] '.$login5.'</span>' : ''); ?>
 <label><?php echo $login2; ?>:</label>
 <input class="box" type="password" name="pass" value="" />
 <?php echo (isset($P_ERROR) ? '<span class="error">[<b>X</b>] '.$login6.'</span>' : ''); ?>
 <span style="display:block;margin-top:10px"><input type="checkbox" name="cookie" value="1" /> <?php echo $login4; ?> <img src="templates/images/info.png" alt="" title="" <?php echo hoverHelpTip($javascript72); ?> /></span> 
 <input type="hidden" name="process" value="1" /><br />
 <input class="button" type="submit" value="<?php echo $login3; ?> &raquo;" title="<?php echo $login3; ?>" />
 </p>
 </form>
</div>

</div>

</body>

</html>
