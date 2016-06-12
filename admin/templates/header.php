<?php if (!defined('PARENT')) { include('index.html'); exit; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo $charset; ?>" />
<title><?php echo $pageTitle; ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/js_code.js"></script>
<script type="text/javascript" src="templates/js/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<?php
if (isset($imgLoad)) {
?>
<script type="text/javascript" src="templates/enlargeIt/enlargeit.js"></script>
<?php
}
if (isset($loadGreyBox)) {
?>
<script type="text/javascript" src="templates/js/jquery.js"></script>
<script type="text/javascript" src="templates/greybox/greybox.js"></script>
<link href="greybox.css" rel="stylesheet" type="text/css" media="all" />
<?php
}
if (isset($calendarLoad)) {
?>
<script src="templates/js/calendar.js" type="text/javascript"></script>
<?php
}
if (isset($ratingLoad)) {
?>
<link href="rating.css" rel="stylesheet" type="text/css" />
<?php
}
?>
</head>

<body >
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<!-- Start Site Wrapper -->
<div id="wrapper">

<!-- Top Bar -->
<div id="topBar">
  <p><?php echo date("j F Y",strtotime(SERVER_TIME_ADJUSTMENT)); ?></p>
  (<a href="?p=logout" title="<?php echo $header6; ?>" onclick="return confirmMessage('<?php echo $javascript24; ?>')"><?php echo $header6; ?></a>)
</div>  
<!-- End Top Bar -->

<!-- Start Header -->
<div id="header">
 <img src="templates/images/logo.png" alt="<?php echo $pageTitle; ?>" title="<?php echo $pageTitle; ?>" /></div>
<!-- End Header -->

<!-- Start Menu Bar -->
<div id="menu">
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><a href="index.php" title="<?php echo $header8; ?>"<?php echo ($cmd=='home' ? ' class="first"' : ''); ?>><?php echo strtoupper($header8); ?></a></td>
    <td><a href="?p=settings" title="<?php echo $header4; ?>"<?php echo ($cmd=='settings' ? ' class="first"' : ''); ?>><?php echo strtoupper($header4); ?></a></td>
    <td><a href="?p=cats" title="<?php echo $header9; ?>"<?php echo ($cmd=='cats' ? ' class="first"' : ''); ?>><?php echo strtoupper($header9); ?></a></td>
    <td><a href="?p=recipes" title="<?php echo $header3; ?>"<?php echo ($cmd=='recipes' ? ' class="first"' : ''); ?>><?php echo strtoupper($header3); ?></a></td>
    <td class="search">
    <form method="get" action="index.php">
    <div>
    <input type="hidden" name="p" value="<?php echo ($cmd=='comments' ? 'comments' : 'recipes'); ?>" />
    <?php
    if ($cmd=='comments') {
    ?>
    <input type="hidden" name="recipe" value="<?php echo $_GET['recipe']; ?>" />
    <?php
    }
    ?>
    <input type="text" name="keywords" class="box" value="<?php echo (isset($_GET['keywords']) ? cleanData($_GET['keywords']) : ($cmd=='comments' ? $header11 : $header10)); ?>" onclick="this.value=''" /> <input class="button" type="submit" value="<?php echo $ps_header4; ?>" title="<?php echo $ps_header4; ?>" />
    </div>
    </form>
    </td>
  </tr>
  </table>
</div>
<!-- End Menu Bar -->

<!-- Start Content Wrapper -->
<div id="contentWrapper">
