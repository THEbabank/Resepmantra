<!-- Start Body Area -->
<div id="bodyArea">

<div class="left">

  <h1 class="<?php echo $this->HEAD_CLASS; ?>"><?php echo $this->WELCOME_TXT; ?>:</h1>

  <p class="categories">
    <?php echo $this->WELCOME_MSG; ?>
  </p>

  <h2 class="headLeft"><?php echo $this->CATEGORIES_TXT; ?>:</h2>
  
  <div id="categories">
    <div class="leftCatsList">
      <?php echo $this->CATEGORIES_LEFT; ?>
    </div> 
    <div class="rightCatsList">
      <?php echo $this->CATEGORIES_RIGHT; ?>
    </div> 
    <br class="break" /> 
  </div>
  
</div>

<div class="right">

  <!--<span class="headRight"><?php echo $this->FILTER_BY_TXT; ?>:</span>-->
  
<!-- PENULIS <p class="rightP">
    <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="0">- - - - - - - - - - - - - - - - - -</option>
    <?php echo $this->LOAD_CONTRIBUTORS; ?>
    </select>
  </p>-->

  <span class="headRightMid"><?php echo $this->MOST_POPULAR_TXT; ?>:</span>
  
  <p class="rightP">
    <?php echo $this->MOST_POPULAR; ?>
  </p>

  <span class="headRightMid"><?php echo $this->LATEST_RECIPES_TXT; ?>:</span>
  
  <p class="rightP">
    <?php echo $this->LATEST_RECIPES; ?>
  </p>

</div>

<br class="break" />

<p>&nbsp;</p>
<p>&nbsp;</p>

</div>
<!-- End Body Area -->
