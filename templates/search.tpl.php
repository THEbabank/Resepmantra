<!-- Start Body Area -->
<div id="bodyArea">

<div class="left">

  <span class="headLeft"><?php echo $this->SEARCH_TXT; ?></span>

  <span class="dateBar"><?php echo $this->SEARCH_RESULTS_TXT; ?></span>

  <?php echo $this->SEARCH_RESULTS; ?>
  
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

  <span class="headRight"><?php echo $this->FILTER_BY_TXT; ?>:</span>
  
  <p class="rightP">
    <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="0">Pilih Provinsi</option>
    <?php echo $this->LOAD_CATEGORIES; ?>
    </select>
  </p>
  
  <!--<span class="headRightMid"><?php echo $this->MOST_POPULAR_TXT; ?>:</span>
  
  <p class="rightP">
    <?php echo $this->MOST_POPULAR; ?>
  </p>-->

</div>

<br class="break" />

<p>&nbsp;</p>
<p>&nbsp;</p>

</div>
<!-- End Body Area -->
