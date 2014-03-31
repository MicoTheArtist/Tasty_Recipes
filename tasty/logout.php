<?php
session_start();
$_SESSION['customer_id'] = '';
$_SESSION['customer_login'] = '';

## header
include("../tasty_include/tasty_header.inc");
?>

<h4>You have successfully logged out. <a href="index.php">Click here</a> to go back to t.as.ty recipes</h4>


<?php
## footer
include("../tasty_include/tasty_footer.inc");
?>