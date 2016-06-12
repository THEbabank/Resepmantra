<?php

/*-------------------------------------------------------------
  MAIAN RECIPE v2.0
  E-Mail: support@maianscriptworld.co.uk
  Website: www.maianscriptworld.co.uk
  This File: Rating System Class
  Originally Written by Ryan Masuga
  Adapted/Modified for Maian Recipe by David Ian Bennett
  
  -------------------------------------------------------------
  
  Original script copyright notice:
  
  ryan masuga, masugadesign.com
  ryan@masugadesign.com 
  Licensed under a Creative Commons Attribution 3.0 License.
  http://creativecommons.org/licenses/by/3.0/
  See docs/rating-licence.txt for full credit details.
  -------------------------------------------------------------
*/

class ratings {

var $prefix;
var $settings;

// Loads rating system...
function buildRatingSystem($txt,$id) {
  return str_replace(array('{rating_txt}','{vote}'),
                     array($txt,$this->ratingBar($id,MAXIMUM_RATING_SCORE,'',$id)),
                     file_get_contents(PATH.'templates/html/rate-recipe.htm')
                     );
}

// Draws new rating to browser after vote..
function outputRating() {
  global $ps_recipe25,$ps_recipe26,$ps_recipe32,$ps_recipe28,$ps_recipe31,$ps_recipe33;
  header("Cache-Control: no-cache");
  header("Pragma: nocache");
  //getting the values
  $vote_sent  = safeImport(preg_replace("/[^0-9]/","",$_GET['j']));
  $id_sent    = safeImport(preg_replace("/[^0-9a-zA-Z]/","",$_GET['q']));
  $ip_num     = safeImport(preg_replace("/[^0-9\.]/","",$_GET['t']));
  $units      = safeImport(preg_replace("/[^0-9]/","",$_GET['c']));
  $recipe     = safeImport(preg_replace("/[^0-9]/","",$_GET['r']));
  $ip         = $_SERVER['REMOTE_ADDR'];

  if ($vote_sent > $units) {
    die("Sorry, vote appears to be invalid."); // kill the script because normal users will never see this.
  }

  //connecting to the database to get some information
  $query = mysql_query("SELECT total_votes, total_value, used_ips FROM ".$this->prefix."ratings 
                        WHERE id    = '$id_sent' 
                        AND recipe  = '$recipe'
                        ")or die(" Error: ".mysql_error());
  $numbers         = mysql_fetch_assoc($query);
  $checkIP         = unserialize($numbers['used_ips']);
  $count           = $numbers['total_votes']; //how many votes total
  $current_rating  = $numbers['total_value']; //total number of rating added together and stored
  $sum             = $vote_sent+$current_rating; // add together the current vote value and the total vote value
  $tense           = ($count==1) ? $ps_recipe25 : $ps_recipe25; //plural form votes/vote

  // checking to see if the first vote has been tallied
  // or increment the current number of votes
  ($sum==0 ? $added=0 : $added=$count+1);

  // if it is an array i.e. already has entries the push in another value
  ((is_array($checkIP)) ? array_push($checkIP,$ip_num) : $checkIP = array($ip_num));
  $insertip = serialize($checkIP);

  //IP check when voting
  $voted = mysql_num_rows(mysql_query("SELECT used_ips FROM ".$this->prefix."ratings 
                          WHERE used_ips LIKE '%$ip%' 
                          AND id         = '$id_sent' 
                          AND recipe     = '$recipe'
                          ")
           );
  if(!$voted) {     //if the user hasn't yet voted, then vote normally...
    if (($vote_sent >= 1 && $vote_sent <= $units) && ($ip == $ip_num)) { // keep votes within range, make sure IP matches - no monkey business!
		  $update = "UPDATE ".$this->prefix."ratings SET 
                 total_votes  = '$added', 
                 total_value  = '$sum', 
                 used_ips     = '$insertip' 
                 WHERE id     = '$id_sent'
                 AND recipe   = '$recipe'
                 ";
		  $result = mysql_query($update);		
	  } 
  } //end for the "if(!$voted)"
  // these are new queries to get the new values!
  $newtotals       = mysql_query("SELECT total_votes, total_value, used_ips FROM ".$this->prefix."ratings 
                     WHERE id    = '$id_sent' 
                     AND recipe  = '$recipe'
                     ")or die(" Error: ".mysql_error());
  $numbers         = mysql_fetch_assoc($newtotals);
  $count           = $numbers['total_votes'];//how many votes total
  $current_rating  = $numbers['total_value'];//total number of rating added together and stored
  $tense           = ($count==1) ? $ps_recipe25 : $ps_recipe26; //plural form votes/vote

  // $new_back is what gets 'drawn' on your page after a successful 'AJAX/Javascript' vote
  $new_back = array();
  $new_back[] .= '<ul class="unit-rating" style="width:'.$units*RATING_UNIT_IMAGE_WIDTH.'px;">'.defineNewline();
  $new_back[] .= '<li class="current-rating" style="width:'.@number_format($current_rating/$count,2)*RATING_UNIT_IMAGE_WIDTH.'px;">'.$ps_recipe32.'.</li>'.defineNewline();
  $new_back[] .= '<li class="r1-unit">1</li>'.defineNewline();
  $new_back[] .= '<li class="r2-unit">2</li>'.defineNewline();
  $new_back[] .= '<li class="r3-unit">3</li>'.defineNewline();
  $new_back[] .= '<li class="r4-unit">4</li>'.defineNewline();
  $new_back[] .= '<li class="r5-unit">5</li>'.defineNewline();
  $new_back[] .= '<li class="r6-unit">6</li>'.defineNewline();
  $new_back[] .= '<li class="r7-unit">7</li>'.defineNewline();
  $new_back[] .= '<li class="r8-unit">8</li>'.defineNewline();
  $new_back[] .= '<li class="r9-unit">9</li>'.defineNewline();
  $new_back[] .= '<li class="r10-unit">10</li>'.defineNewline();
  $new_back[] .= '</ul>'.defineNewline();
  $new_back[] .= '<p class="voted">'.$ps_recipe28.': <strong>'.@number_format($sum/$added,1).'</strong>/'.$units.' ('.number_format($count).' '.$tense.' '.$ps_recipe31.') '.defineNewline();
  $new_back[] .= '<span class="thanks">'.$ps_recipe33.'</span></p>'.defineNewline();
  
  $allnewback = implode(defineNewline(), $new_back);

  //name of the div id to be updated | the html that needs to be changed
  $output = 'unit_long'.$id_sent.'|'.$allnewback;
  //echo $output;exit;
  return $output;
}

// Logs and registers rating..
function registerRating() {
  global $ps_recipe25,$ps_recipe25;
  header("Cache-Control: no-cache");
  header("Pragma: nocache");
  $vote_sent  = safeImport(preg_replace("/[^0-9]/","",$_GET['j']));
  $id_sent    = safeImport(preg_replace("/[^0-9a-zA-Z]/","",$_GET['q']));
  $ip_num     = safeImport(preg_replace("/[^0-9\.]/","",$_GET['t']));
  $units      = safeImport(preg_replace("/[^0-9]/","",$_GET['c']));
  $recipe     = safeImport(preg_replace("/[^0-9]/","",$_GET['r']));
  $ip         = $_SERVER['REMOTE_ADDR'];
  $referer    = $_SERVER['HTTP_REFERER'];

  if ($vote_sent > $units) {
    die("Sorry, vote appears to be invalid."); // kill the script because normal users will never see this.
  }

  //connecting to the database to get some information
  $query           = mysql_query("SELECT total_votes, total_value, used_ips FROM ".$this->prefix."ratings 
                     WHERE id    = '$id_sent' 
                     AND recipe  = '$recipe'
                     ")or die(" Error: ".mysql_error());
  $numbers         = mysql_fetch_assoc($query);
  $checkIP         = unserialize($numbers['used_ips']);
  $count           = $numbers['total_votes']; //how many votes total
  $current_rating  = $numbers['total_value']; //total number of rating added together and stored
  $sum             = $vote_sent+$current_rating; // add together the current vote value and the total vote value
  $tense           = ($count==1) ? $ps_recipe25 : $ps_recipe25; //plural form votes/vote

  // checking to see if the first vote has been tallied
  // or increment the current number of votes
  ($sum==0 ? $added=0 : $added=$count+1);

  // if it is an array i.e. already has entries the push in another value
  ((is_array($checkIP)) ? array_push($checkIP,$ip_num) : $checkIP = array($ip_num));
  $insertip = serialize($checkIP);

  //IP check when voting
  $voted = mysql_num_rows(mysql_query("SELECT used_ips FROM ".$this->prefix."ratings 
                          WHERE used_ips LIKE '%$ip%' 
                          AND id         = '$id_sent' 
                          AND recipe     = '$recipe'
                          "));
  if(!$voted) {     //if the user hasn't yet voted, then vote normally...
    if (($vote_sent >= 1 && $vote_sent <= $units) && ($ip == $ip_num)) { // keep votes within range
	    $update = "UPDATE ".$this->prefix."ratings SET 
                 total_votes  = '$added', 
                 total_value  = '$sum', 
                 used_ips     = '$insertip' 
                 WHERE id     = '$id_sent' 
                 AND recipe   = '$recipe'
                 ";
	    $result = mysql_query($update);		
    } 
    header("Location: $referer"); // go back to the page we came from 
    exit;
  } //end for the "if(!$voted)"
}

// Drawns initial rating bars..
function ratingBar($id,$units='',$static='',$recipe=0) { 
  global $ps_recipe25,$ps_recipe26,$ps_recipe27,$ps_recipe28,$ps_recipe29,$ps_recipe30,$ps_recipe31;
  //set some variables
  $ip = $_SERVER['REMOTE_ADDR'];
  if (!$units) {
    $units = MAXIMUM_RATING_SCORE;
  }
  if (!$static) {
    $static = FALSE;
  }

  // get votes, values, ips for the current rating bar
  $query = mysql_query("SELECT total_votes, total_value, used_ips FROM ".$this->prefix."ratings 
           WHERE id    = '$id'
           AND recipe  = '$recipe'
           ")or die(" Error: ".mysql_error());

  // insert the id in the DB if it doesn't exist already
  if (mysql_num_rows($query) == 0) {
    $sql    = "INSERT INTO ".$this->prefix."ratings (
               `id`,`total_votes`, `total_value`, `recipe`, `used_ips`
               ) VALUES (
               '$id', '0', '0', '$recipe', ''
               )";
    $result = mysql_query($sql);
  }

  $numbers = mysql_fetch_assoc($query);
  if ($numbers['total_votes'] < 1) {
	  $count = 0;
  } else {
	  $count = $numbers['total_votes']; //how many votes total
  }
  $current_rating = $numbers['total_value']; //total number of rating added together and stored
  $tense          = ($count==1) ? $ps_recipe25 : $ps_recipe26; //plural form votes/vote

  // determine whether the user has voted, so we know how to draw the ul/li
  $voted = mysql_num_rows(mysql_query("SELECT used_ips FROM ".$this->prefix."ratings 
                          WHERE used_ips LIKE '%$ip%' 
                          AND id         = '$id' 
                          AND recipe     = '$recipe'
                          ")
           ); 

  // now draw the rating bar
  $rating_width = @number_format($current_rating/$count,2)*RATING_UNIT_IMAGE_WIDTH;
  $rating1      = @number_format($current_rating/$count,1);
  $rating2      = @number_format($current_rating/$count,2);

  if ($static == 'static') {
    $static_rater = array();
		$static_rater[] .= defineNewline().'<div class="ratingblock">'.defineNewline();
		$static_rater[] .= '<div id="unit_long'.$id.'">'.defineNewline();
		$static_rater[] .= '<ul id="unit_ul'.$id.'" class="unit-rating" style="width:'.RATING_UNIT_IMAGE_WIDTH*$units.'px;">'.defineNewline();
		$static_rater[] .= '<li class="current-rating" style="width:'.$rating_width.'px;">'.$ps_recipe27.' '.$rating2.'/'.$units.'</li>'.defineNewline();
		$static_rater[] .= '</ul>'.defineNewline();
		$static_rater[] .= '<p class="static">'.$ps_recipe28.': <strong> '.$rating1.'</strong>/'.$units.' ('.number_format($count).' '.$tense.' '.$ps_recipe31.') <em>'.$ps_recipe29.'</em></p>'.defineNewline();
		$static_rater[] .= '</div>'.defineNewline();
		$static_rater[] .= '</div>'.defineNewline().defineNewline();
		return join("\n", $static_rater);
  } else {
    $rater ='';
    $rater.='<div class="ratingblock">'.defineNewline();
    $rater.='<div id="unit_long'.$id.'">'.defineNewline();
    $rater.='  <ul id="unit_ul'.$id.'" class="unit-rating" style="width:'.RATING_UNIT_IMAGE_WIDTH*$units.'px;">'.defineNewline();
    $rater.='     <li class="current-rating" style="width:'.$rating_width.'px;">'.$ps_recipe27.' '.$rating2.'/'.$units.'</li>'.defineNewline();
    for ($ncount = 1; $ncount <= $units; $ncount++) { // loop from 1 to the number of units
      //if ($this->settings->modr=='yes') {
        //$url = $this->settings->install_path.'rating/'.$recipe.'/'.$ncount.'/'.$id.'/'.$ip.'/'.$units.'/index.html';
      //} else {
        $url = 'index.php?p=rating&amp;rsystem=rbar&amp;r='.$recipe.'&amp;j='.$ncount.'&amp;q='.$id.'&amp;t='.$ip.'&amp;c='.$units;
      //}
      if(!$voted) { // if the user hasn't yet voted, draw the voting stars
        $rater .= '<li><a href="'.$url.'" title="'.$ncount.' '.$ps_recipe30.' '.$units.'" class="r'.$ncount.'-unit rater" rel="nofollow">'.$ncount.'</a></li>'.defineNewline();
      }
    }
    $ncount = 0; // resets the count
    $rater.='  </ul>'.defineNewline();
    $rater.='  <p';
    if($voted) { 
      $rater.=' class="voted"';
    }
    $rater.='>'.$ps_recipe28.': <strong> '.$rating1.'</strong>/'.$units.' ('.number_format($count).' '.$tense.' '.$ps_recipe31.')';
    $rater.='  </p>'.defineNewline();
    $rater.='</div>'.defineNewline();
    $rater.='</div>'.defineNewline();
    
    return $rater;
 }

}

}

?>
