<!-- Start Body Area -->
<div id="bodyArea">

<div class="left">

  <span class="headLeft"><?php echo cleanData($this->RECIPE->name); ?>:</span>
  
  <span class="dateBar"><?php echo $this->DATE_BAR_TXT; ?></span>

  <span class="headImgIngredients"><img src="templates/images/ingredients.gif" alt="<?php echo $this->INGREDIENTS_TXT; ?>" title="<?php echo $this->INGREDIENTS_TXT; ?>" /></span>
  
  <p class="ingredients"><?php echo nl2br(cleanData($this->RECIPE->ingredients)); ?></p>
  
  <span class="headImgInstructions"><img src="templates/images/instructions.gif" alt="<?php echo $this->INSTRUCTIONS_TXT; ?>" title="<?php echo $this->INSTRUCTIONS_TXT; ?>" /></span>
  
  <p class="instructions"><?php echo nl2br(cleanData($this->RECIPE->instructions)); ?></p>
  
  <span class="headLeft"><?php echo $this->PICTURES_TXT; ?>:</span>
  <div id="share-buttons">
<a href="http://www.facebook.com/sharer.php?u=http://pkti.lumbanbatu.com/index.php?p=recipe&recipe=3" target="_blank"><img src="http://www.simplesharebuttons.com/images/somacro/facebook.png" alt="Facebook" /></a><a href="http://twitter.com/share?url=http://pkti.lumbanbatu.com/index.php?p=recipe&recipe=3&text=Simple Share Buttons&hashtags=simplesharebuttons" target="_blank"><img src="http://www.simplesharebuttons.com/images/somacro/twitter.png" alt="Twitter" /></a>
</div>

  <!--// <?php 
  // Displays pictures if any are available..
  // templates/html/recipe-pictures.htm
  // echo $this->SHOW_PICTURES; 
  // ?>-->
  
  <?php 
  // Displays comments if enabled..
  // templates/html/visitor-comments.htm
  // templates/html/comment.htm
  echo $this->VISITOR_COMMENTS; 
  ?>
  
  <p>&nbsp;</p>

</div>

<div class="right">

  <span class="headRight"><?php echo $this->RECIPE_LINKS_TXT; ?>:</span>
  
  <p class="rightP">
    <?php
    // Check to make sure recipe submissions are allowed for this category..
    // Enable/disable in settings..
    if ($this->CAT->enRecipes=='yes') {
    ?>
    <span class="link"><a class="submit" href="<?php echo $this->S_URL; ?>" title="<?php echo $this->SUBMIT_TXT; ?>"><?php echo $this->SUBMIT_TXT; ?></a></span>
    <?php
    }
    ?>
    <!--<span class="link"><a class="contact" href="<?php echo $this->C_URL; ?>" title="<?php echo $this->CONTACT_US_TXT; ?>"><?php echo $this->CONTACT_US_TXT; ?></a></span>
    <span class="link"><a class="friend" href="<?php echo $this->F_URL; ?>" title="<?php echo $this->TELL_A_FRIEND_TXT; ?>"><?php echo $this->TELL_A_FRIEND_TXT; ?></a></span>-->
    <span class="link"><a class="print" onclick="window.open(this);return false" href="<?php echo $this->P_URL; ?>" title="<?php echo $this->PRINT_FRIENDLY_TXT; ?>"><?php echo $this->PRINT_FRIENDLY_TXT; ?></a></span>
  </p>

  <!--<?php 
  // Loads rating system template if enabled..
  // templates/html/rate-recipe.htm
  echo $this->RATING_SYSTEM; 
  ?>-->
  
  <?php 
  // Loads add comment template if enabled..
  // templates/html/add-comment.htm
  echo $this->ADD_COMMENT; 
  ?>
  
  <span class="headRightMid"><?php echo $this->JUMP_TO_CATEGORY_TXT; ?>:</span>
  
  <p class="rightP">
    <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="0">Pilih Provinsi Lain</option>
    <?php echo $this->LOAD_CATEGORIES; ?>
    </select>
  </p>
  
  <!--<span class="headRightMid"><?php echo $this->RECIPE_SELECTION_TXT; ?>:</span>
  
  <p class="rightP">
    <?php 
    // templates/html/latest-popular-link.htm
    echo $this->OTHER_RECIPES_IN_CATEGORY; 
    ?>
  </p>-->
  
  <!--<?php 
  // Loads cloud tags if enabled..
  // templates/html/cloud-tags.htm
  echo $this->CLOUD_TAGS;
  ?>--> 
  
  <div id="hitCount">
    <span class="hitCountTxt"><?php echo $this->HITS_TXT; ?></span>
    <?php echo $this->HITS; ?>
  </div>
  
  <p>&nbsp;</p>
  <p>&nbsp;</p>

</div>

<br class="break" />

</div>
<!-- End Body Area -->
