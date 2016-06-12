<?php if (!defined('PARENT')) { include('index.html'); exit; } 
$COMMENT = getTableData('comments','id',(int)$_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<?php echo $charset; ?>" />
<title><?php echo $pageTitle; ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templates/js/js_code.js"></script>
<style type="text/css">
body {
  background-image:none;
}
</style>
</head>

<body>

<!-- Start Content Wrapper -->
<div id="contentWrapper">

<div id="bodyArea">
<?php
if (isset($OK)) {
echo actionCompleted($comments20);
}
?>
<form method="post" action="?p=edit-comment&amp;id=<?php echo $_GET['id']; ?>">
<div class="mainHead">

  <span class="noData" style="text-align:left;font-size:12px">&quot;<?php echo nl2br(htmlspecialchars(cleanData($COMMENT->comment))); ?>&quot;</span>
  
  <div id="commentWrap">
   <div class="leftComments">
     <label><?php echo $comments15; ?></label>
     <textarea name="comment" rows="6" cols="35"><?php echo htmlspecialchars(cleanData($COMMENT->comment)); ?></textarea>
     <label><?php echo $comments18; ?></label>
     <select name="recipe">
     <?php
     $q_all = mysql_query("SELECT *,DATE_FORMAT(adddate,'".MYSQL_DATE_FORMAT."') AS aDate 
              FROM ".$database['prefix']."recipes
              WHERE isApproved = 'no'
              ORDER BY name
              ") or die(mysql_error());
     if (mysql_num_rows($q_all)>0) {
     while ($RECIPES = mysql_fetch_object($q_all)) {
     ?>
     <option value="<?php echo $RECIPES->id; ?>"<?php echo ($COMMENT->recipe==$RECIPES->id ? ' selected="selected"' : ''); ?>><?php echo cleanData($RECIPES->name); ?></option>
     <?php
     }
     }
     ?>
     </select>
   </div>
   <div class="rightComments">
     <label><?php echo $comments16; ?></label>
     <input class="box" type="text" name="leftBy" value="<?php echo cleanData($COMMENT->leftBy); ?>" style="width:85%" />
     <label><?php echo $comments17; ?></label>
     <input class="box" type="text" name="email" value="<?php echo cleanData($COMMENT->email); ?>" style="width:85%" /><br /><br />
     <input type="hidden" name="process" value="1" />
     <input type="submit" class="button" value="<?php echo $comments19; ?>" title="<?php echo $comments19; ?>" />
   </div>
   <br class="break" />
  </div>
  
</div>
</form>

</div>

</div>
<!-- End Content Wrapper -->

</body>
</html>
