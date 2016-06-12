<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Flash Header Class
  Written by David Ian Bennett
----------------------------------------------*/

class flashHeader {

// Encoding type..
var $encoding       = 'UTF-8';

// Movie width..
var $movieWidth     = '914';

// Movie height..
var $movieHeight    = '200';

// Background colour..
var $bgcolor        = 'f1ebb7';

// Image width..
var $photoWidth     = '914';

// Image height..
var $photoHeight    = '200';

// Slide duration..in seconds..
var $photoTime      = '5';

// Slideshow transitions..
// There are 67 transition effects built into the flash slideshow..
// Try different numbers for different effects..
//
// You can also specify arrays for random effects..
// Examples:
//
// For range of effects from 1 to 10..
// var $transition = array(1,2,3,4,5,6,7,8,9,10);
//
// Or specify specific effect numbers..
// var $transition = array(2,13,23,45,67);
//
var $transition     = '29';

// Enable text on pictures..
var $enableText     = false;

// If text is enabled, specify x co-ordinate..
var $photoTitleX    = '35';

// If text is enabled, specify y co-ordinate..
var $photoTitleY    = '25';

// If text is enabled, specify text size..
var $photoTextSize  = '25';

// If text is enabled, specify text font..
var $photoTextFont  = 'Verdana';

// If text is enabled, specify text colour..
var $photoTextClr   = 'ffffff';

// If text is enabled, specify text to appear on pictures..
// If you want different text on each picture, specify them as an array..
// If you have 3 pictures you want 3 slots. If a slot isn`t found it doesn`t display anything..
//
// Example:
//
// var $textTitle = array(0 => 'Text for Pic 1',
//                        1 => 'Text for pic 2',
//                        2 => 'Text for pic 3'
//                        );
//
var $textTitle      = 'Picture';

// Load settings..don`t touch!!
var $settings;

function buildFlashHeader() {
  $loop = 0;
  $xml = '<flash_parameters>
  <preferences>
        <global>
            <basic_property movieWidth="'.$this->photoWidth.'" movieHeight="'.$this->photoHeight.'" startAutoPlay="true" backgroundColor="0x'.$this->bgcolor.'" continuum="true" html_title="Title" loadStyle="Bar" anvsoftMenu="false" hideAdobeMenu="false" photoDynamicShow="false" enableURL="true" transitionArray=""/>
            <title_property photoTitle="'.($this->enableText ? 'true' : 'false').'" photoTitleX="'.$this->photoTitleX.'" photoTitleY="'.$this->photoTitleY.'" photoTitleSize="'.$this->photoTextSize.'" photoTitleFont="'.$this->photoTextFont.'" photoTitleColor="0x'.$this->photoTextClr.'"/>
            <music_property path="" stream="false" loop="true"/>
            <photo_property topPadding="0" bottomPadding="0" leftPadding="0" rightPadding="0"/>
            <properties enable="false" backgroundColor="0xffffff" backgroundAlpha="30" cssText="a:link{text-decoration: underline;} a:hover{color:#ff0000; text-decoration: none;} a:active{color:#0000ff;text-decoration: none;} .blue {color:#0000ff; font-size:15px; font-style:italic; text-decoration: underline;} .body{color:#000000;font-size:20px;}" align="top"/>
        </global>
    </preferences>
  <album>
  ';
  $dir = opendir(PATH.'templates/images/flash-header');
  while (false !== ($IMG = readdir($dir))){
    if (substr(strtolower($IMG),-4)=='.jpg' || substr(strtolower($IMG),-5)=='.jpeg') {
      $key = $loop++;
      // Are the transitions an array..
      if (is_array($this->transition)) {
        shuffle($this->transition);
        $transitionOverWrite = $this->transition[0];
      }
      // Check text titles if enabled..
      if (is_array($this->textTitle)) {
        if (array_key_exists($key,$this->textTitle)) {
          $textTitleOverwrite = $this->textTitle[$key];
        } else {
          if (isset($textTitleOverwrite)) {
            unset($textTitleOverwrite);
          }
          $this->textTitle = '';
        }
      }
      $xml .= '<slide jpegURL="'.$this->settings->install_path.'templates/images/flash-header/'.$IMG.'" d_URL="'.$this->settings->install_path.'templates/images/flash-header/'.$IMG.'" transition="'.(isset($transitionOverWrite) ? $transitionOverWrite : ($this->transition==0 ? '29' : $this->transition)).'" panzoom="1" URLTarget="0" phototime="'.$this->photoTime.'" url="" title="'.(isset($textTitleOverwrite) ? $textTitleOverwrite : $this->textTitle).'" width="'.$this->photoWidth.'" height="'.$this->photoHeight.'" />'.defineNewline();
    }
  }
  closedir($dir);
  $xml .= '</album>
  </flash_parameters>
  ';
  $xml2 = '<?xml version="1.0" encoding="'.$this->encoding.'" ?>'.defineNewline();
  return trim($xml2.$xml);
}

}

?>
