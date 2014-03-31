<?
session_start();

## functions - Private functions only

	## private functions
	require('../tasty_include/tasty_utilities.inc.php');
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## fetch_bookmarks -- this function will fetch all of the bookmarks and arrange them by recent or most popular in descending order.

## header
include('../tasty_include/tasty_header.inc');
?>

<html>
<head>
	<title>Subscribe to our RSS Feed!</title>
</head>
<body>
	<h1>Subscribe to our RSS Feed!</h1>
    <h2 style="font-size:16px; color:grey;">Copy URL to RSS Reader</h2>
    <a href="http://mespinosa.userworld.com/tasty/5_most_recent.rss">http://mespinosa.userworld.com/tasty/5_most_recent.rss</a>
    
    <br /><br /><br /><br /><br />
</body>	         
</html>


<?
## footer
include("../tasty_include/tasty_footer.inc");
?>