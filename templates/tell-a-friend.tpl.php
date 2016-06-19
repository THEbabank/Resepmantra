<!-- Start Body Area -->
<div id="bodyArea">
<script type="text/javascript">
function checkform(form) {
  var message = '';
  if (form.name.value=='') {
    message += '- <?php echo $this->NAME_ERROR; ?>\n';
  }
  if (form.ctct.value=='') {
    message +='- <?php echo $this->EMAIL_ERROR; ?>\n';
  }
  if (form.fname.value=='') {
    message += '- <?php echo $this->FRIEND_NAME_ERROR; ?>\n';
  }
  if (form.fctct.value=='') {
    message +='- <?php echo $this->FRIEND_EMAIL_ERROR; ?>\n';
  }
  if (form.comments.value=='') {
    message +='- <?php echo $this->COMMENTS_ERROR; ?>\n';
  }
  <?php
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

<form method="post" action="<?php echo $this->F_URL; ?>" onsubmit="return checkform(this)">  
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
 <h1 class="cu"><?php echo $this->CONTACT_TXT; ?>:</h1>

 <div class="cu_name_email_wrapper">
 <div class="cu_left">

 <label><?php echo $this->NAME_TXT; ?>:</label>
 <input type="text" class="box" name="name" value="<?php echo (isset($_POST['name']) ? cleanData($_POST['name']) : ''); ?>" />
 <?php echo (array_key_exists('name',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['name'].'</span>' : ''); ?>
 
 </div>

 <div class="cu_right">
 
 <label><?php echo $this->EMAIL_TXT; ?>:</label>
 <input type="text" class="box" name="ctct" value="<?php echo (isset($_POST['ctct']) ? cleanData($_POST['ctct']) : ''); ?>" />
 <?php echo (array_key_exists('ctct',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['ctct'].'</span>' : ''); ?>
 
 </div>
 
 <br class="break" />
 
 </div>
 
 <div class="cu_name_email_wrapper">
 <div class="cu_left">

 <label><?php echo $this->FRIEND_NAME_TXT; ?>:</label>
 <input type="text" class="box" name="fname" value="<?php echo (isset($_POST['fname']) ? cleanData($_POST['fname']) : ''); ?>" />
 <?php echo (array_key_exists('fname',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['fname'].'</span>' : ''); ?>
 
 </div>

 <div class="cu_right">
 
 <label><?php echo $this->FRIEND_EMAIL_TXT; ?>:</label>
 <input type="text" class="box" name="fctct" value="<?php echo (isset($_POST['fctct']) ? cleanData($_POST['fctct']) : ''); ?>" />
 <?php echo (array_key_exists('fctct',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['fctct'].'</span>' : ''); ?>
 
 </div>
 
 <br class="break" />
 
 </div>
 
 <p class="cu_comments_wrap">
 <label><?php echo $this->COMMENTS_TXT; ?>:</label>
 <textarea name="comments" rows="8" cols="40"><?php echo (isset($_POST['comments']) ? cleanData($_POST['comments']) : ''); ?></textarea>
 <?php echo (array_key_exists('comments',$this->ERRORS) ? '<span class="formError">'.$this->ERRORS['comments'].'</span>' : ''); ?>
 </p>
 
 <?php 
 // Loads form captcha if it is enabled..
 // templates/html/captcha-contact-us.htm
 echo $this->CAPTCHA; 
 ?>
 
 <p class="cu_button_wrapper">
 <input type="hidden" name="process" value="1" />
 <input type="image" src="templates/images/contact-us.gif" alt="<?php echo $this->SEND_TXT; ?>" title="<?php echo $this->SEND_TXT; ?>" />
 </p>
 <?php
 }
 ?>
</form>

<br class="break" />

<p>&nbsp;</p>

</div>
<!-- End Body Area -->
