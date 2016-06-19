<?php

// The footer link to Maian Script World should remain in place.
// If you wish to remove this link, please consider a copyright removal fee for your domain.
// Payment can be made via the Maian Recipe website
// Removing the link without permission invalidates any support.
// Thank you! 

?>

<!-- Footer -->
<div id="footer">
 <p class="links"><a href="<?php echo $this->H_URL; ?>" title="<?php echo $this->HOME; ?>"><?php echo $this->HOME; ?></a>&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href="<?php echo $this->A_URL; ?>" title="<?php echo $this->ADD_RECIPE; ?>"><?php echo $this->ADD_RECIPE; ?></a>&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href="<?php echo $this->M_URL; ?>" title="<?php echo $this->MOST_POPULAR; ?>"><?php echo $this->MOST_POPULAR; ?></a>&nbsp;&nbsp;&#8226;&nbsp;&nbsp;<a href="<?php echo $this->C_URL; ?>" title="<?php echo $this->CONTACT_US; ?>"><?php echo $this->CONTACT_US; ?></a></p>
 <p class="copyright"><?php echo $this->FOOTER; ?></p>
</div>
<!-- End Footer -->

</div>
<!-- End Content Wrapper -->

</div>
<!-- End Site Wrapper -->

<p>&nbsp;</p>
<?php
// This block of code loads an invisible div if comments are enabled 
// and the iBox is loading the display content..
// If you aren`t sure, do not remove it..
if ($this->LOAD_COM_DIV && USE_IBOX_FOR_COMMENTS) {
?>
<!-- For the addition of new comments ONLY -->
<div id="msgCommentsAdded">
 <p><img src="templates/images/ok.gif" alt="" title="" /></p>
 <p class="text"><?php echo $this->LOAD_COM_DIV_TXT; ?></p>
</div>
<!-- End div -->
<?php
}
?>
</body>
</html>
