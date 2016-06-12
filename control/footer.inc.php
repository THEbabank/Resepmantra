<?php

/*++++++++++++++++++++++++++++++++++++++++

  Script: Maian Recipe v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: http://www.maianscriptworld.co.uk
  This file: Public Footer Control File
  Written by: David Ian Bennett
  
  ++++++++++++++++++++++++++++++++++++++++*/

if (!defined('PARENT')) {
  exit;
} 

$tpl_footer  = new Savant2();
$tpl_footer->assign('HOME', $ps_header);
$tpl_footer->assign('ADD_RECIPE', $ps_header6);
$tpl_footer->assign('MOST_POPULAR', $ps_header7);
$tpl_footer->assign('CONTACT_US', $ps_header8);
$tpl_footer->assign('LOAD_COM_DIV', (isset($loadCommentDiv) ? true : false));
$tpl_footer->assign('LOAD_COM_DIV_TXT', ($SETTINGS->enCommApp=='yes' ? $ps_recipe39 : $ps_recipe40));
$tpl_footer->assign('H_URL', ($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
$tpl_footer->assign('A_URL', ($SETTINGS->modr=='yes' ? 'add-recipe.html' : '?p=add-recipe'));
$tpl_footer->assign('M_URL', ($SETTINGS->modr=='yes' ? 'about-us.html' : '?p=about-us'));
$tpl_footer->assign('C_URL', ($SETTINGS->modr=='yes' ? 'contact-us.html' : '?p=contact-us'));
$tpl_footer->assign('FOOTER', $footerlink);
$tpl_footer->display('templates/footer.tpl.php');

?>
