<?php

/*++++++++++++++++++++++++++++++++++++++++

  Script: Maian Recipe v2.0
  E-Mail: N/A
  Website: http://www.maianscriptworld.co.uk
  This file: User Defined Variables
  Written by: David Ian Bennett
  
  ++++++++++++++++++++++++++++++++++++++++*/
  
/*
  If enabled displays help tips in the admin area
  1 = Enabled, 0 = Disabled
*/      
define ('HELP_TIPS', 1);
  
/*
  If your setup isn`t displaying the correct time, you may 
  have a server in a different location. Set the adjustment in hours here.
  This supports PHP`s strtotime function
  DO NOT change this unless you are familiar with this function
*/    
define ('SERVER_TIME_ADJUSTMENT', '+0 hours');

/*
  Do you wish to enable the hit count on the recipe page?
  1 = Enabled, 0 = Disabled
*/  
define ('ENABLE_HIT_COUNT', 1);

/*
  The hit counter can be a minimum amount of digits
  For example, set to 6 the display would start 000001
  If you only want to display the exact count, set this to 0
  Otherwise set a minimum amount of digits
*/  
define ('HIT_COUNT_DIGITS_MIN', 6);

/*
  When cloud tags are created, you can skip words that are not a certain length
  For example, 5 only creates tags that has words 5 characters or more
*/  
define ('CLOUD_TAG_WORD_LIMIT', 5);

/*
  How many random cloud tags do you want to display on each recipe page?
  To display all, set to a high number
*/   
define ('CLOUD_TAGS_TO_DISPLAY', 20);

/*
  How do you want the recipes to display?
  By default this is recipe name ascending (a-z), however you can change the appearance below
  name       = By recipe name Ascendng
  name DESC  = By recipe name Descending
  id         = By recipe id Ascending
  id DESC    = By recipe id Descending
*/
define('RECIPE_DISPLAY_ORDER', 'name');

/*
  Some pages display the most popular recipes in the right menu
  How many most popular recipes do you wish to show?
*/  
define ('MOST_POPULAR_DISPLAY_LIMIT', 10);

/*
  Some pages display the latest recipes in the right menu
  How many most latest recipes do you wish to show?
*/  
define ('LATEST_RECIPES_DISPLAY_LIMIT', 10);

/*
  How many latest recipes do you wish to show in the RSS feed if enabled?
*/  
define ('RSS_FEED_LIMIT', 50);

/*
  Default build date for RSS feeds..
*/  
define ('RSS_BUILD_DATE_FORMAT', date('D, j M Y H:i:s',strtotime(SERVER_TIME_ADJUSTMENT)).' GMT');

/*
  When viewing a recipe, a link in the right menu shows other recipes in the same category
  How many recipes do you wish to show?
*/  
define ('OTHER_RECIPES_IN_CAT_DISPLAY_LIMIT', 5);

/*
  The homepage shows the categories/sub categories
  How many sub categories do you want to display beneath each parent category?
  Set to a high number for all
*/  
define ('SUB_CATEGORY_DISPLAY_COUNT', 10);

/*
  If you enable this, the total amount of recipes is shown next to each category
  on the homepage. ie: Fish (20), Meat (13) etc
  1 = Enabled, 0 = Disabled
*/  
define ('SHOW_CATEGORY_RECIPE_COUNT', 1);

/*
  Should parent category count also include children recipe count?
  ie: If a parent has 3 recipes and in total the children have 10, this would
  show a count of 13 for the parent
  1 = Enabled, 0 = Disabled
*/  
define ('PARENT_WITH_CHILD_COUNT', 0);

/*
  Do you wish to use the ibox for comments?
  When a comment is added successfully a fancy box appears. This is the ibox.
  If you disable this, standard javascript boxes are used
  You can also use the ibox for all message displays.
  See the comments in the 'templates/js/comments.js' file
  If this is too advanced for you, leave this as it is
*/  
define ('USE_IBOX_FOR_COMMENTS', 1);

/*
  On the 'Add Recipe' page, if both image restrictions are set they appear between 
  a seperator. This is the seperator. You can use HTML if you wish.
*/  
define ('IMG_RESTRICTION_SEPERATOR', '/');

/*
  The amount of upload boxes to show per row on the 'Add Recipe' page.
  If you adjust your layout you may need to alter this. Bear in mind
  Firefox 3 has styling problems with file boxes.
  If it breaks your layout, revert back to default
*/    
define ('MAX_UPLOAD_BOXES_PER_ROW', 3);

/*
  The ratings image width. If you create new ratings images, specify their width here
  to avoid breaking your rating display
*/  
define ('RATING_UNIT_IMAGE_WIDTH', 32);

/*
  The maximum rating between 1 & 10. 5 is suitable so you may not want to
  change this
*/  
define ('MAXIMUM_RATING_SCORE', 5); 

/*
  Do you wish to enable the contact us e-mail auto responder?
  1 = Enabled, 0 = Disabled
*/  
define ('ENABLE_CONTACT_US_AUTO_RESPONDER', 1);

/*
  Do you wish to enable the add comment e-mail auto responder?
  1 = Enabled, 0 = Disabled
*/ 
define ('ENABLE_COMMENT_AUTO_RESPONDER', 1);

/*
  Do you wish to enable the add recipe e-mail auto responder?
  1 = Enabled, 0 = Disabled
*/ 
define ('ENABLE_RECIPE_AUTO_RESPONDER', 1);

/*
  Do you wish to enable the tell a friend e-mail auto responder?
  1 = Enabled, 0 = Disabled
*/ 
define ('ENABLE_TELL_A_FRIEND_AUTO_RESPONDER', 1);

/*
  Do you wish to receive notification if tell a friend system is used?
  1 = Enabled, 0 = Disabled
*/ 
define ('ENABLE_TELL_A_FRIEND_REPORT', 1);

/*
  MySQL Date format for mysql date display. Converts the YYYY-MM-DD format
  into your preferred format. For more info see: 
  http://dev.mysql.com/doc/refman/5.0/en/date-and-time-functions.html
*/  
define ('MYSQL_DATE_FORMAT', '%e %b %Y');

/*
  Displays the 'Powered by Maian Recipe' link in your browser title bar
  You may disable this if you wish. Note that disabling this does NOT
  alter the page footer text. That should remain.
  1 = Enabled, 0 = Disabled
*/  
define ('ENABLE_POWERED_BY_LINK', 1);

?>
