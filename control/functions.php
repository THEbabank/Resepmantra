<?php

/*---------------------------------------------
  MAIAN RECIPE v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Program Functions
  Written by David Ian Bennett
----------------------------------------------*/

// Converts recipe/category name for search engine friendly urls..
function seoUrl($title,$cleanrss=false) {
  // Convert special characters from European countries into the English alphabetic equivalent..
  $chars = array(
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y'
  );
			
  // Replace chars in array..
  $title = strtr($title, $chars);
  // Are we cleaning RSS output..
  if ($cleanrss) {
    return $title;
  }
  // Strip none alphabetic and none numeric chars..
  $title = strtolower(preg_replace('`[^\w_-]`', '-', $title));
  // Replace other data that may be passed, such as double hyphens..
  return str_replace(array('--','---','----','-amp-','-039-'),
                     array('-','-','-','-and-',''),
                     $title
                     );
}

// Build category RSS link..
function buildCategoryRSSLink($id) {
  global $SETTINGS,$ps_category10;
  $rss = '';
  if ($SETTINGS->enRSS=='yes') {
    $rss = '<span class="smallRSS"><a href="'.($SETTINGS->modr=='yes' ? $SETTINGS->install_path.'rss-cat-feed/'.$id.'/index.html' : '?p=rss-cat-feed&amp;cat='.$id).'" title=""><img src="templates/images/rss-small.png" alt="'.$ps_category10.'" title="'.$ps_category10.'" /></a></span>';
  }
  return $rss;
}

// Build contact captcha..
function buildContactUsCaptcha($txt,$txt2,$error='') {
  global $SETTINGS;
  $captcha = '';
  if ($SETTINGS->enSpam=='yes') {
    $captcha = str_replace(array('{captcha_txt}','{enter_word}','{error}'),
                           array($txt,$txt2,$error),
                           file_get_contents(PATH.'templates/html/captcha-contact-us.htm')
                           );
  }
  return $captcha;
}

// Builds tag cloud list for user submitted recipe..
function buildCloudTags($words,$words2,$id) {
  global $database;
  // Remove new line characters and html code..
  $words   = str_replace(array(defineNewline(),defineNewline().defineNewline()),array(' ',' '),strip_tags($words));
  $words2  = str_replace(array(defineNewline(),defineNewline().defineNewline()),array(' ',' '),strip_tags($words2));
  // Assign arrays..
  $skipWords   = array_map('trim',file(PATH.'control/cloud-tags-skip-file.txt'));
  $wordBlock1  = ($words ? array_map('trim',explode(' ',$words)) : array());
  $wordBlock2  = ($words2 ? array_map('trim',explode(' ',$words2)) : array());
  mysql_query("DELETE FROM ".$database['prefix']."cloudtags
  WHERE recipe = '$id'
  ") or die(mysql_error());
  // Loop through first word block..
  if (!empty($wordBlock1)) {
    foreach ($wordBlock1 AS $wd) {
      // Run words through filter..we can use the seourl function..
      $wd  = seoUrl($wd);
      // If word contains a hyphen from the previous filter, only take first part of word..
      if (strpos($wd,'-')!==FALSE) {
        $wd = substr($wd,0,strlen(strpos($wd,'-')));
      }
      // Prepare for safe importing into dd..
      $wd  = safeImport($wd);
      if (!in_array($wd,$skipWords) && strlen($wd)>=CLOUD_TAG_WORD_LIMIT) {
        if (rowCount('cloudtags',' WHERE cloud_word = \''.$wd.'\' AND recipe = \''.$id.'\'')>0) {
          mysql_query("UPDATE ".$database['prefix']."cloudtags SET
          cloud_count       = (cloud_count+1)
          WHERE cloud_word  = '$wd' AND recipe = '$id'
          LIMIT 1
          ") or die(mysql_error());
        } else {
          mysql_query("INSERT INTO ".$database['prefix']."cloudtags (
          cloud_word,cloud_count,recipe
          ) VALUES (
          '$wd','1','$id'
          )") or die(mysql_error());
        }
      }
    }
  }
  // Loop through second word block..
  if (!empty($wordBlock2)) {
    foreach ($wordBlock2 AS $wd) {
      // Run words through filter..we can use the seourl function..
      $wd  = seoUrl($wd);
      // If word contains a hyphen from the previous filter, only take first part of word..
      if (strpos($wd,'-')!==FALSE) {
        $wd = substr($wd,0,strlen(strpos($wd,'-')));
      }
      // Prepare for safe importing into dd..
      $wd  = safeImport($wd);
      if (!in_array($wd,$skipWords) && strlen($wd)>CLOUD_TAG_WORD_LIMIT) {
        if (rowCount('cloudtags',' WHERE cloud_word = \''.$wd.'\' AND recipe = \''.$id.'\'')>0) {
          mysql_query("UPDATE ".$database['prefix']."cloudtags SET
          cloud_count       = (cloud_count+1)
          WHERE cloud_word  = '$wd' AND recipe = '$id'
          LIMIT 1
          ") or die(mysql_error());
        } else {
          mysql_query("INSERT INTO ".$database['prefix']."cloudtags (
          cloud_word,cloud_count,recipe
          ) VALUES (
          '$wd','1','$id'
          )") or die(mysql_error());
        }
      }
    }
  }
}

// Help tip..
function hoverHelpTip($text,$align='CENTER') {
  if (!HELP_TIPS) {
    return '';
  }
  global $javascript5;
  $html = '<div class="toolTip">'.$text.'</div>';
  return 'onmouseover="return overlib(\''.htmlspecialchars($html).'\',\''.htmlspecialchars($javascript5).'\', '.$align.');" onmouseout="nd();"';
}

// Returns encrypted data..
function encrypt($data) {
  return (function_exists('sha1') ? sha1($data) : md5($data));
}

// Prepares date for safe mysql import..
function safeImport($data) {
  if (get_magic_quotes_gpc()) {
    $data = stripslashes($data);
  }
  return mysql_real_escape_string($data);
}

// Strips nasty tags from code..
function cleanEvilTags($data) {
  /*$data = preg_replace("/javascript/i", "j&#097;v&#097;script",$data);
  $data = preg_replace("/alert/i", "&#097;lert",$data);
  $data = preg_replace("/about:/i", "&#097;bout:",$data);
  $data = preg_replace("/onmouseover/i", "&#111;nmouseover",$data);
  $data = preg_replace("/onclick/i", "&#111;nclick",$data);
  $data = preg_replace("/onload/i", "&#111;nload",$data);
  $data = preg_replace("/onsubmit/i", "&#111;nsubmit",$data);
  $data = preg_replace("/<body/i", "&lt;body",$data);
  $data = preg_replace("/<html/i", "&lt;html",$data);
  $data = preg_replace("/document\./i", "&#100;ocument.",$data);
  $data = preg_replace("/<script/i", "&lt;&#115;cript",$data);*/
  return strip_tags(htmlspecialchars(trim($data)));
}

// CSV Clean Up..
function csvCleanUp($data) {
  return (strstr($data, ",") ? '"'.$data.'"' : $data);
}

// File size conversion..
function fileSizeConversion($size=0,$base='1048576') {
  if ($size>0) {
    if ($size>1023987) {
      return number_format($size/$base,1)."MB";
    } else if ($size<1024) {
      return $size." Bytes";
    } else {
      return number_format($size/1024,0)."KB";
    }
  } else {
    return '0KB';
  }
}

// Check digit var..
function checkDigit($id) {
  global $script21;
  if (!ctype_digit($id)) {
    header("HTTP/1.0 404 Not Found");
    echo '<h1>'.$script21.'</h1>';
    exit;
  }
}

// Gets data based on param criteria..
function getTableData($table,$row,$id,$and='') {
  global $database;
  if ($table=='recipes') {
    $query = mysql_query("SELECT *,DATE_FORMAT(addDate,'".MYSQL_DATE_FORMAT."') AS adate FROM ".$database['prefix'].$table."
             WHERE $row  = '$id'
             $and
             LIMIT 1
             ") or die(mysql_error());
  } else {
    $query = mysql_query("SELECT * FROM ".$database['prefix'].$table."
             WHERE $row  = '$id'
             $and
             LIMIT 1
             ") or die(mysql_error());
  }
  return mysql_fetch_object($query);
}

// Gets row count..
function rowCount($table,$where='') {
  global $database;
  $query = mysql_query("SELECT count(*) AS r_count FROM ".$database['prefix'].$table.$where."") or die(mysql_error());
  $row = mysql_fetch_object($query);
  return str_replace(',','',$row->r_count);
}

// Gets row count for recipes with join..
function rowCountRatings($where='') {
  global $database;
  $query = mysql_query("SELECT *,".$database['prefix']."recipes.id AS rid, 
           total_value/total_votes AS score
           FROM ".$database['prefix']."ratings 
           LEFT JOIN ".$database['prefix']."recipes
           ON ".$database['prefix']."ratings.recipe = ".$database['prefix']."recipes.id
           $where
           ") or die(mysql_error());
  return mysql_num_rows($query);
}

// Cleans output data.. 
function cleanData($data) {
  $data = str_replace(' & ', ' &amp; ', $data);
  return (get_magic_quotes_gpc() ? stripslashes($data) : $data);
}

// Loads recipe..
function getRecipe($id) {
  global $database;
  $query = mysql_query("SELECT *,DATE_FORMAT(addDate, '".DATE_FORMAT."') AS a_date 
           FROM ".$database['prefix']."recipes 
           WHERE id = '$id' 
           LIMIT 1
           ") or die(mysql_error());
  return mysql_fetch_object($query);
}

// Public pagination..
function publicPageNumbers($count,$limit,$page,$seo,$todisplay=25,$stringVar='next') {
  $PaginateIt = new PaginateIt();
  $PaginateIt->SetRawPageUrl($seo);
  $PaginateIt->SetCurrentPage($page);
  $PaginateIt->SetItemCount($count);
  $PaginateIt->SetItemsPerPage($limit);
  $PaginateIt->SetLinksToDisplay($todisplay);
  $PaginateIt->SetQueryStringVar($stringVar);
  $PaginateIt->SetLinksFormat('&laquo; ',
                              ' &bull; ',
                              ' &raquo;'
  );                            
  $PaginateIt->SetModRewrite(1);                           
  return $PaginateIt->GetPageLinks();
}

// Gets visitor IP address..
function getRealIPAddr() {
  $ip = array();
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip[] = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'],',')===TRUE) {
      $split = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
      foreach ($split AS $value) {
        $ip[] = $value;
      }
    } else {
      $ip[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
  } else {
    $ip[] = $_SERVER['REMOTE_ADDR'];
  }
  return (!empty($ip) ? implode(',',$ip) : '');
}

// Define newline..
function defineNewline() {
  $unewline = "\r\n";
  if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
    $unewline = "\r\n";
  } else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
    $unewline = "\r";
  } else {
    $unewline = "\n";
  }
  return $unewline;
}

// Recursive way of handling multi dimensional arrays..
// This cleans query string data and prevents code injections..
function multiDimensionalArrayMap($func,$arr) {
  $newArr = array();
  if (!empty($arr)) {
    foreach($arr AS $key => $value) {
      $newArr[$key] = (is_array($value) ? multiDimensionalArrayMap($func,$value) : $func($value));
    }
  }
  return $newArr;
}

// Clear query string array input..
$_GET  = multiDimensionalArrayMap('cleanEvilTags', $_GET);
$_GET  = multiDimensionalArrayMap('htmlspecialchars', $_GET);

?>
