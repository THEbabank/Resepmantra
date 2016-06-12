<?php
// Log in check..
function isWebmasterLoggedIn($login=false) {
  global $database,$cmd;
  $skey = encrypt($database['cookieKey']);
  if (!isset($_SESSION[$skey]) && !isset($_COOKIE[$skey.$database['cookieName']]) && $cmd!='login') {
    header("Location: ?p=login");
    exit;
  }
  if (isset($_SESSION[$skey]) || isset($_COOKIE[$skey.$database['cookieName']])) {
    if ($cmd=='login') {
      header("Location: index.php");
      exit;
    }
  }
}

// Get first recipe..
function getFirstRecipe() {
  global $database;
  $query = mysql_query("SELECT * FROM ".$database['prefix']."recipes ORDER BY name LIMIT 1") or die(mysql_error());
  $row = mysql_fetch_object($query);
  return $row->id;
}

// Display box if action is done..
function actionCompleted($text) {
  if (!ENABLE_ACTION_MESSAGES) {
    return '';
  }
  global $script11;
  return '
  <div id="actionComplete">
    <span>'.$text.' (<a href="#" onclick="closeThisDiv(\'actionComplete\');return false" title="'.$script11.'">'.$script11.'</a>)</span>
    <img src="templates/images/action.png" alt="'.$text.'" title="'.$text.'" />
  </div>
  ';
}

// Admin pagination..
function pageNumbers($count,$limit,$page,$todisplay=25,$stringVar='next') {
  global $script7,$script8;
  $PaginateIt = new PaginateIt();
  $PaginateIt->SetCurrentPage($page);
  $PaginateIt->SetItemCount($count);
  $PaginateIt->SetItemsPerPage($limit);
  $PaginateIt->SetLinksToDisplay($todisplay);
  $PaginateIt->SetQueryStringVar($stringVar);
  $PaginateIt->SetLinksFormat('&laquo; '.$script7,
                              ' &bull; ',
                              $script8.' &raquo;'
  );

  return '<div id="pages">'.$PaginateIt->GetPageLinks().'</div>';
}

?>
