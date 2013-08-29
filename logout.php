<?

    // require common code
    require_once("includes/common.php"); 

    // log out current user, if any
    logout();

?>

<!DOCTYPE html>
<html>
  <head><? $html_head_title = 'C$50 Finance: Log Out'; require_once('includes/html_head.php'); ?>
  </head>
  <body>
    <div id="top">
      <a href="index.php"><img alt="C$50 Finance" src="images/logo.gif" /></a>
    </div>
    <div id="middle">
      kthxbai
    </div>
    <div id="bottom">
      <a href="login.php">log in</a> again
    </div>
  </body>
</html>
