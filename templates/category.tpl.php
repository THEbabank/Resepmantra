<!-- Start Body Area -->
<div id="bodyArea">

<div class="left">

  <span class="headLeft"><?php echo $this->CAT_RSS; ?><?php echo $this->VIEWING_CAT_TXT; ?></span>

  <span class="dateBar"><?php echo $this->PLEASE_CHOOSE_TXT; ?></span>

  <?php echo $this->RECIPES; ?>
  
  <?php
  // Only show page numbers if recipe count higher than 0..
  if ($this->COUNT>0) {
  ?>
  <p class="pages"><?php echo $this->PAGE_NUMBERS; ?></p>
  <?php
  }
  ?>
</div>

<div class="right">

  <span class="headRight"><?php echo $this->OTHER_CATS_TXT; ?>:</span>
  
  <p class="rightP">
    <?php 
    // templates/html/latest-popular-link.htm
    echo $this->OTHER_CATS; 
    ?>
  </p>
  
  <span class="headRightMid"><?php echo $this->RECIPE_SELECTION_TXT; ?>:</span>
  
  <p class="rightP">
    <?php 
    // templates/html/latest-popular-link.htm
    echo $this->RECIPE_SELECTION; 
    ?>
  </p>

</div>

<br class="break" />

<p>&nbsp;</p>
<p>&nbsp;</p>

</div>
<!-- End Body Area -->
