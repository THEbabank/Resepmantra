<!-- Start Body Area -->
<div id="bodyArea">
<script type="text/javascript">
function checkform(form) {
  var message = '';
  var images  = false;
  if (form.name.value=='') {
    message += '- <?php echo $this->NAME_ERROR; ?>\n';
  }
  if (form.ctct.value=='') {
    message +='- <?php echo $this->EMAIL_ERROR; ?>\n';
  }
  if (form.rname.value=='') {
    message +='- <?php echo $this->RNAME_ERROR; ?>\n';
  }
  if (form.ingredients.value=='') {
    message +='- <?php echo $this->INGREDIENTS_ERROR; ?>\n';
  }
  if (form.instructions.value=='') {
    message +='- <?php echo $this->INSTRUCTIONS_ERROR; ?>\n';
  }
  <?php
  if ($this->PICTURES) {
  // This regex checks the valid image extensions..
  // Its not 100% reliable, so server side checking is in place too..
  ?>
  for (i=0; i<form.elements['img[]'].length; i++) { 
    cvalue = form.elements['img[]'][i].value.toLowerCase(); 
    if (cvalue) {
      if(!/\.(<?php echo strtolower($this->SETTINGS->validImages); ?>)$/i.test(cvalue)) {
        message +='- <?php echo $this->IMG_ERROR; ?>\n';
      } 
    } 
  }
  <?php
  }
  // Only load code check if captcha is enabled..
  if ($this->SETTINGS->enSpam=='yes') {
  ?>
  if (form.code.value=='') {
    message +='- <?php echo $this->CODE_ERROR; ?>';
  }
  <?php
  }
  ?>
  if (message) {
    alert('<?php echo $this->FORM_ERRORS; ?>\n\n'+message);
    return false;
  }
}
</script>

<form method="post" action="<?php echo $this->A_URL; ?>"<?php echo ($this->SETTINGS->maxImages>0 ? ' enctype="multipart/form-data"' : ''); ?> onsubmit="return checkform(this)">  
 <?php
 // Has the form been submitted successfully?
 // If it has, this block of code will execute..ie the IS_SENT var will evaluate to true..
 // If not, load form..ie the IS_SENT var evaluates to false..
 // Be careful you don`t change this configuration!!!
 if ($this->IS_SENT) {
 ?>
 <h1 class="cu"><?php echo $this->MESSAGE_SENT; ?></h1>
 <span class="messageSent"><?php echo $this->MESSAGE_SENT2; ?></span>
 <?php
 } else {
 ?>
 <h1 class="cu"><?php echo $this->ADD_RECIPE_TXT; ?></h1>
 
 <p class="addMsg">
    <?php echo $this->WELCOME_MSG; ?>
  </p>

 <h2 class="cu"><?php echo $this->ADD_RECIPE_TXT2; ?></h2>
 
 <div class="add_recipe_wrapper">
 <div class="ar_left">

 <label><?php echo $this->NAME_TXT; ?>:</label>
 <input type="text" class="box" name="name" value="<?php echo (isset($_POST['name']) ? cleanData($_POST['name']) : ''); ?>" />
 <?php echo (array_key_exists('name',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['name'].'</span>' : ''); ?>
 
 </div>

 <div class="ar_right">
 
 <label><?php echo $this->EMAIL_TXT; ?>:</label>
 <input type="text" class="box" name="ctct" value="<?php echo (isset($_POST['ctct']) ? cleanData($_POST['ctct']) : ''); ?>" />
 <?php echo (array_key_exists('ctct',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['ctct'].'</span>' : ''); ?>
 
 </div>
 
 <br class="break" />
 
 </div>

 <h2 class="cu"><?php echo $this->ADD_RECIPE_TXT3; ?></h2>
 
 <div class="add_recipe_wrapper">
 <div class="ar_left">

 <label><?php echo $this->RECIPE_NAME_TXT; ?>:</label>
 <input type="text" class="box" name="rname" value="<?php echo (isset($_POST['rname']) ? cleanData($_POST['rname']) : ''); ?>" />
 <?php echo (array_key_exists('rname',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['rname'].'</span>' : ''); ?>
 
 </div>

 <div class="ar_right">
 
 <label><?php echo $this->CAT_TXT; ?>:</label>
 <select name="cat">
  <?php 
  // Loads categories that allow recipe submissions..
  echo $this->CATEGORIES; 
  ?>
 </select>
 <?php echo (array_key_exists('cat',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['cat'].'</span>' : ''); ?>
 
 </div>
 
 <br class="break" />
 
 </div>

 <h2 class="cu"><?php echo $this->ADD_RECIPE_TXT4; ?></h2>
 
 <div class="add_recipe_wrapper">
 <div class="ar_left">

 <label><?php echo $this->INGREDIENTS_TXT; ?>:</label>
 <textarea name="ingredients" rows="8" cols="40"><?php echo (isset($_POST['ingredients']) ? cleanData($_POST['ingredients']) : ''); ?></textarea>
 <?php echo (array_key_exists('ingredients',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['ingredients'].'</span>' : ''); ?>
 
 </div>

 <div class="ar_right">
 
 <label><?php echo $this->INSTRUCTIONS_TXT; ?>:</label>
 <textarea name="instructions" rows="8" cols="40"><?php echo (isset($_POST['instructions']) ? cleanData($_POST['instructions']) : ''); ?></textarea>
 <?php echo (array_key_exists('instructions',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['instructions'].'</span>' : ''); ?>
 
 </div>
 
 <br class="break" />
 
 </div>
 
 <?php 
 // Loads upload picture boxes if pictures are enabled..
 // templates/html/add-recipe-pictures.htm
 echo $this->PICTURES; 
 ?>
 
 <?php 
 // Loads form captcha if it is enabled..
 // templates/html/captcha-contact-us.htm
 echo $this->CAPTCHA; 
 ?>
 
 <p class="cu_button_wrapper">
 <input type="hidden" name="process" value="1" />
 <input type="image" src="templates/images/add-recipe.gif" alt="<?php echo $this->SEND_TXT; ?>" title="<?php echo $this->SEND_TXT; ?>" />
 </p>
 
 <?php
 }
 ?>
</form> 
<br class="break" />

<p>&nbsp;</p>

</div>
<!-- End Body Area -->
