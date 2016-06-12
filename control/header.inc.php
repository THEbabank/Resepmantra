<?php

/*++++++++++++++++++++++++++++++++++++++++

  Script: Maian Recipe v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: http://www.maianscriptworld.co.uk
  This file: Public Header Control File
  Written by: David Ian Bennett

  ++++++++++++++++++++++++++++++++++++++++*/
  
if (!defined('PARENT')) {
  exit;
} 

$tpl_header  = new Savant2();
$tpl_header->assign('CHARSET', $charset);
$tpl_header->assign('BASE_PATH', $SETTINGS->install_path);
$tpl_header->assign('TITLE', $headerTitleText.(ENABLE_POWERED_BY_LINK ? ' - '.$script3.' '.$script.' '.$script2 : ''));
$tpl_header->assign('JAVASCRIPT', $MRREC->loadJSFunctions($loadJS));
$tpl_header->assign('KEYWORDS', (isset($_GET['keys']) ? cleanData($_GET['keys']) : $ps_header13));
$tpl_header->assign('IS_CMD', $cmd);
$tpl_header->assign('FBOG',(isset($openGraph) && $openGraph ? $openGraph : ''));
$tpl_header->assign('NO_FLASH', $ps_header12);
$tpl_header->assign('META_DESC', (isset($overRideMetaDesc) ? $overRideMetaDesc : cleanData($SETTINGS->metaDesc)));
$tpl_header->assign('META_KEYS', (isset($overRideMetaKeys) ? $overRideMetaKeys : cleanData($SETTINGS->metaKeys)));
$tpl_header->assign('RSS_HEAD_LINK', ($SETTINGS->enRSS=='yes' ? '<link rel="alternate" type="application/rss+xml" title="'.$ps_header7.'" href="'.($SETTINGS->modr=='yes' ? 'rss-feed.html' : '?p=rss-feed').'" />'.defineNewline() : ''));
$tpl_header->assign('RSS', ($SETTINGS->enRSS=='yes' ? '<span class="rss"><a href="'.($SETTINGS->modr=='yes' ? 'rss-feed.html' : '?p=rss-feed').'" title="'.$ps_header10.'">'.$ps_header10.'</a></span>'.defineNewline() : '<span class="no-rss">&nbsp;</span>'));
$tpl_header->assign('CURRENTLY_LISTING', str_replace(array('{rcount}','{ccount}'),array(number_format(rowCount('recipes',' WHERE isApproved = \'no\' AND enRecipe = \'yes\'')),rowCount('categories',' WHERE enCat = \'yes\'')),$ps_header5));
$tpl_header->assign('HOME', $ps_header);
$tpl_header->assign('ADD_RECIPE', $ps_header6);
$tpl_header->assign('ABOUT_US', $ps_header7);
$tpl_header->assign('CONTACT_US', $ps_header8);
$tpl_header->assign('SEARCH', $ps_header4);
$tpl_header->assign('H_URL', ($SETTINGS->modr=='yes' ? 'index.html' : 'index.php'));
$tpl_header->assign('A_URL', ($MRREC->checkAddRecipeRule() ? ($SETTINGS->modr=='yes' ? 'add-recipe.html' : '?p=add-recipe') : 'javascript:alert(\''.$javascript73.'\')'));
$tpl_header->assign('AB_URL', ($SETTINGS->modr=='yes' ? 'about-us.html' : '?p=about-us'));
$tpl_header->assign('C_URL', ($SETTINGS->modr=='yes' ? 'contact-us.html' : '?p=contact-us'));
$tpl_header->assign('H_FIRST', ($cmd=='home' ? ' class="first"' : ''));
$tpl_header->assign('A_FIRST', ($cmd=='add-recipe' ? ' class="first"' : ''));
$tpl_header->assign('AB_FIRST', ($cmd=='about-us' ? ' class="first"' : ''));
$tpl_header->assign('C_FIRST', ($cmd=='contact-us' ? ' class="first"' : ''));
$tpl_header->display('templates/header.tpl.php');

?>
