<?php

/*---------------------------------------------
  Script: Maian Recipe v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: http://www.maianscriptworld.co.uk
  This File: RSS Feed Class
  Written by: David Ian Bennett
----------------------------------------------*/

class rssFeed {

var $xml_version  = '1.0';
var $encoding     = 'utf-8';
var $rss_version  = '2.0';
var $settings;
var $prefix;
var $thisFeedUrl;

// Get latest recipes..
function getLatestRecipes($build_date,$cat=0) {
  global $msg_rss3,$ps_recipe34;
  $string = '';
  $query = mysql_query("SELECT * FROM ".$this->prefix."recipes
                         WHERE enRecipe  = 'yes'
                         AND isApproved  = 'no' 
                         ".($cat>0 ? 'AND cat = \''.$cat.'\'' : '')."
                         ORDER BY id DESC 
                         LIMIT ".RSS_FEED_LIMIT."
                         ") or die(mysql_error());
  while ($RSS = mysql_fetch_object($query)) {
    $CAT     = getTableData('categories','id',$RSS->cat);
    $string .= $this->add_item($msg_rss3.$RSS->name,
                               ($this->settings->modr=='yes' ? $this->settings->install_path.'free-recipe/'.seoUrl($CAT->catname).'/'.seoUrl($RSS->name).'/'.$RSS->id.'/index.html' : $this->settings->install_path.'index.php?p=recipe&amp;recipe='.$RSS->id),
                               ($RSS->rss_date ? $RSS->rss_date : $build_date),
                               $ps_recipe34.' '.($RSS->submitted_by ? $RSS->submitted_by : 'N/A')
                               );
  }
  return trim($string);
}

// Starts RSS Channel..
function open_channel() {
  $xml_string = '<?xml-stylesheet type="text/css" href="'.$this->settings->install_path.'rss.css" ?>
  <rss version="'.$this->rss_version.'" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
  ';
  $xml2 = '<?xml version="'.$this->xml_version.'" encoding="'.$this->encoding.'" ?>'.defineNewline();
  return trim($xml2.$xml_string);
}

// Loads data into Feed..
function add_item($title='',$link='',$date='',$desc='') {
  $xml_string = '
  <item>
   <title>'.$this->render($title).'</title>
   <link>'.$link.'</link>
   <pubDate>'.$date.'</pubDate>
   <guid>'.$link.'</guid>
   <description>'.$this->render($desc,true).'</description>
  </item>
  ';

  return $xml_string;
}

// Loads Feed Info..
function feed_info($title='',$link='',$date='',$desc='',$site='') {
  $xml_string = '
  <title>'.cleanData($title).'</title>
  <link>'.$link.'</link>
  <description>'.cleanData($desc).'</description>
  <lastBuildDate>'.$date.'</lastBuildDate>
  <language>en-us</language>
  <generator>'.$this->render($site).'</generator>
  <atom:link href="'.$this->thisFeedUrl.'" rel="self" type="application/rss+xml" />
  ';

  return $xml_string;
}

// Closes RSS Channel..
function close_channel() {
  $xml_string = '
  </channel>
  </rss>
  ';

  return $xml_string;
}

// Renders Feed Data..
function render($data,$clean_tags=false) {
  if ($clean_tags) {
    $data = $this->remove_tags($data);
  }
  
  return '<![CDATA['.cleanData($data).']]>';
}

// Removes certain tags from feed..
function remove_tags($data) {
  $data =  strip_tags($data,'<b><strong><p><br><img><i><a><u>');
  // Clean foreign characters..
  return seoUrl($data,true);
}

}

?>
