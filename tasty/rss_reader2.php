<?
session_start();
		
## HEADER
include('../tasty_include/tasty_header.inc');		


## INCLUDE
require_once('XML/RSS.php');											// require_once -- pulls in the pear library by requiring the XML/RSS.php library

$rss_feed = "http://rss.allrecipes.com/daily.aspx?hubID=80";			// $rss_feed
$rss = new XML_RSS($rss_feed);											// instantiate $rss to the class XML_RSS($rss_feed)
$rss->parse();															// use $parse() to convert the rss XML into a php readable form

$channel = $rss->getChannelInfo();										// set $channel to the array returned by the function $getChannelInfo()

## PRINT -  html that correlates the the $channel array
?>
<html>
<head>
	<title><? echo $channel['title']; ?></title>
</head>
<body>
	<h1><a href="<? echo $channel['link']; ?>"><? echo $channel['title']; ?></a></h1>
    <h2><? echo $channel['description']; ?></h2>
<?

foreach($rss->getItems() as $item) {										// use the foreach loop to set $item to the array <item></item> elements returned by the $getItems() array

	echo "<br><li><a href='".$item['link']."'>".$item['title']."</a><br>".$item['description']."</li>";
}
?>



<?php
## footer
include("../tasty_include/tasty_footer.inc");
?>
</body>
</html>
