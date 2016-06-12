<?php
class systemFunctions {

var $prefix;

// Update settings..
function updateSettings() {
  $_POST = multiDimensionalArrayMap('safeImport', $_POST);
  mysql_query("UPDATE ".$this->prefix."settings SET
  website       = '".$_POST['website']."',
  email         = '".$_POST['email']."',
  language      = '".$_POST['language']."',
  install_path  = '".$_POST['install_path']."',
  total         = '".$_POST['total']."',
  smtp          = '".(isset($_POST['smtp']) ? $_POST['smtp'] : 'no')."',
  smtp_host     = '".$_POST['smtp_host']."',
  smtp_user     = '".$_POST['smtp_user']."',
  smtp_pass     = '".$_POST['smtp_pass']."',
  smtp_port     = '".$_POST['smtp_port']."',
  modr          = '".(isset($_POST['modr']) ? $_POST['modr'] : 'no')."',
  server_path   = '".$_POST['server_path']."',
  metaDesc      = '".$_POST['metaDesc']."',
  metaKeys      = '".$_POST['metaKeys']."',
  enCommApp     = '".(isset($_POST['enCommApp']) ? $_POST['enCommApp'] : 'no')."',
  enRecApp      = '".(isset($_POST['enRecApp']) ? $_POST['enRecApp'] : 'no')."',
  enSpam        = '".(isset($_POST['enSpam']) ? $_POST['enSpam'] : 'no')."',
  enRSS         = '".(isset($_POST['enRSS']) ? $_POST['enRSS'] : 'no')."',
  enCloudTags   = '".(isset($_POST['enCloudTags']) ? $_POST['enCloudTags'] : 'no')."',
  maxImages     = '".$_POST['maxImages']."',
  validImages   = '".$_POST['validImages']."',
  autoResize    = '".$_POST['autoResize']."',
  maxFileSize   = '".$_POST['maxFileSize']."'
  LIMIT 1
  ") or die(mysql_error());
}

}

?>
