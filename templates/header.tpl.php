<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo $this->CHARSET; ?>" />
<title><?php echo $this->TITLE; ?></title>
<meta name="description" content="<?php echo $this->META_DESC; ?>" />
<meta name="keywords" content="<?php echo $this->META_KEYS; ?>" />
<base href="<?php echo $this->BASE_PATH; ?>" />
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/swfobject.js"></script>
<?php
// Loads RSS meta link if enabled..
echo $this->RSS_HEAD_LINK; 
// Loads javascript modules..
// This is important. DO NOT remove it..
echo $this->JAVASCRIPT; 
// Facebook open graph tag for recipes..
if ($this->FBOG) {
?>
<meta property="og:image" content="<?php echo $this->FBOG; ?>" />
<?php
}
?>
</head>

<body >

<!-- Start Site Wrapper -->
<div id="wrapper">

<!-- Top Bar -->
<div id="topBar">
  <p><?php echo $this->CURRENTLY_LISTING; ?></p>
  <?php 
  // Loads RSS link if enabled..
  echo $this->RSS; 
  ?>
</div>  
<!-- End Top Bar -->

<!-- Start Header -->
<div id="header">
 <img src="templates/images/logo.png" alt="<?php echo $this->TITLE; ?>" title="<?php echo $this->TITLE; ?>" />
</div>
<!-- End Header -->

<!-- Start Menu Bar -->
<div id="menu">
  <table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><a href="<?php echo $this->H_URL; ?>" title="<?php echo $this->HOME; ?>"<?php echo $this->H_FIRST; ?>><?php echo strtoupper($this->HOME); ?></a></td>
    <td><a href="<?php echo $this->A_URL; ?>" title="<?php echo $this->ADD_RECIPE; ?>"<?php echo $this->A_FIRST; ?>><?php echo strtoupper($this->ADD_RECIPE); ?></a></td>
    <td><a href="<?php echo $this->AB_URL; ?>" title="<?php echo $this->ABOUT_US; ?>"<?php echo $this->AB_FIRST; ?>><?php echo strtoupper($this->ABOUT_US); ?></a></td>
    <td><a href="<?php echo $this->C_URL; ?>" title="<?php echo $this->CONTACT_US; ?>"<?php echo $this->C_FIRST; ?>><?php echo strtoupper($this->CONTACT_US); ?></a></td>
    <td class="search">
    
    <!-- Search Form -->
    <form method="post" action="<?php echo $this->H_URL; ?>">
    <div>
    <input type="hidden" name="search" value="1" />
    <input type="text" name="keys" class="box" value="<?php echo $this->KEYWORDS; ?>" onclick="this.value=''" /> <input class="button" type="submit" value="<?php echo $this->SEARCH; ?>" title="<?php echo $this->SEARCH; ?>" />
    </div>
    </form>
    <!-- End Search Form -->
    
    </td>
  </tr>
  </table>
</div>
<!-- End Menu Bar -->

<!-- Start Content Wrapper -->
<div id="contentWrapper">

<!-- Flash Bar -->
<?php
/* This displays random images in a flash slideshow..
   Parameters are set in 'classes/flash-header.php'..
   Add jpeg images to the 'templates/images/flash-header/' directory..
   Or remove tbis div if you prefer not to have the flash header
   
   You can also use a standard single image if preferred..
   <img src="templates/images/flash-header/header1.jpg" alt="" title="" />
   
   Another alternative would be random images like this..(assuming default naming structure)
   <img src="templates/images/flash-header/header<?php echo rand(1,6); ?>.jpg" alt="" title="" />
   
   You can also display different things for different pages using if statements or a switch statement. 
   The $this->IS_CMD value holds the current page. ie, for the homepage:
   
   if ($this->IS_CMD=='home') {
   } 
   
   or
   
   switch ($this->IS_CMD) {
     case 'home':
     do something..
     break;
     case 'add-recipe':
     do something..
     break;
   }
   
   etc etc
*/
?>
<div id="flashBar">
  <div id="flash" style="padding:0px;margin:0px"><?php echo $this->NO_FLASH; ?></div>
    <script type="text/javascript">
	   var flash = new SWFObject("flash-header.swf?xml_path=index.php?p=flash", "flash", "914", "200", "8", "#f1ebb7", true)
	   flash.addParam("movie", "flash-header.swf?xml_path=index.php?p=flash");
	   flash.addParam("menu", "false");
	   flash.addParam("wmode", "opaque");
	   flash.addParam("scale", "noscale");
	   flash.write("flash")
    </script>    
</div>
<!-- End Flash Bar -->
