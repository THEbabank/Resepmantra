<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo $this->CHARSET; ?>" />
<title><?php echo cleanData($this->RECIPE->name); ?></title>
<base href="<?php echo $this->BASE_PATH; ?>" />
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
  background:#fff;
}
</style>
</head>

<body onload="window.print()">

<!-- Start Body Area -->
<div id="bodyArea" style="text-align:left">

<b class="printHead"><?php echo cleanData($this->RECIPE->name); ?>:</b>
  
<span class="printBar"><?php echo $this->DATE_BAR_TXT; ?></span>

<b class="printHead"><?php echo $this->INGREDIENTS_TXT; ?>:</b>

<p class="ingredients"><?php echo nl2br(cleanData($this->RECIPE->ingredients)); ?></p>

<b class="printHead"><?php echo $this->INSTRUCTIONS_TXT; ?>:</b>
  
<p class="instructions"><?php echo nl2br(cleanData($this->RECIPE->instructions)); ?></p>

<p class="printFoot"><?php echo $this->BY; ?>:</p>

<br class="break" />

</div>
<!-- End Body Area -->

</body>
</html>
