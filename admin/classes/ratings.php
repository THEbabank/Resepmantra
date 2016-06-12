<?php
class ratings {

var $prefix;
var $settings;

// Drawns initial rating bars..
function ratingBar($id,$recipe=0) { 
  global $ps_recipe25,$ps_recipe26,$ps_recipe27,$ps_recipe28,$ps_recipe29,$ps_recipe30,$ps_recipe31;
  //set some variables
  $ip     = $_SERVER['REMOTE_ADDR'];
  $units  = MAXIMUM_RATING_SCORE;

  // get votes, values, ips for the current rating bar
  $query = mysql_query("SELECT total_votes, total_value, used_ips FROM ".$this->prefix."ratings 
           WHERE id    = '$id'
           AND recipe  = '$recipe'
           ")or die(" Error: ".mysql_error());
  $numbers        = mysql_fetch_assoc($query);
  $count          = ($numbers['total_votes'] < 1 ? 0 : $numbers['total_votes']);
  $current_rating = $numbers['total_value']; //total number of rating added together and stored
  $tense          = ($count==1) ? $ps_recipe25 : $ps_recipe26; //plural form votes/vote

  // now draw the rating bar
  $rating_width = @number_format($current_rating/$count,2)*RATING_UNIT_IMAGE_WIDTH;
  $rating1      = @number_format($current_rating/$count,1);
  $rating2      = @number_format($current_rating/$count,2);

  $static_rater = array();
	$static_rater[] .= defineNewline().'<div class="ratingblock">'.defineNewline();
	$static_rater[] .= '<div id="unit_long'.$id.'">'.defineNewline();
	$static_rater[] .= '<p class="static">'.$ps_recipe28.': <b> '.$rating1.'</b>/'.$units.' ('.number_format($count).' '.$tense.' '.$ps_recipe31.')</p>'.defineNewline();
	$static_rater[] .= '<ul id="unit_ul'.$id.'" class="unit-rating" style="width:'.RATING_UNIT_IMAGE_WIDTH*$units.'px;">'.defineNewline();
	$static_rater[] .= '<li class="current-rating" style="width:'.$rating_width.'px;">'.$ps_recipe27.' '.$rating2.'/'.$units.'</li>'.defineNewline();
	$static_rater[] .= '</ul>'.defineNewline();
	$static_rater[] .= '</div>'.defineNewline();
	$static_rater[] .= '</div>'.defineNewline().defineNewline();
	return join("\n", $static_rater);
}

}

?>
