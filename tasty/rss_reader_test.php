<?
session_start();

## INCLUDE
	require_once('XML/RSS.php');										// require_once -- pulls in the pear library by requiring the XML/RSS.php library

## VARIABLES
$rss_feed = "http://mespinosa.userworld.com/tasty/tasty_recipes.rss";	// $rss_feed
$rss = new XML_RSS($rss_feed);											// instantiate $rss to the XML_RSS($rss_feed) class
$rss->parse();															// use the $parse() function to convert the rss XML into a php readable form

$channel = $rss->getChannelInfo();										// set $channel to the array returned by the function $getChannelInfo()

## PRINT HTML -- using the the $channel array values
?>
<html>
<head>
	<title><? echo $channel['title']; ?></title>
</head>
<body>
	<h1><a href="<? echo $channel['link']; ?>"><? echo $channel['title']; ?></a></h1>
    <h2><? echo $channel['description']; ?></h2>
<?
foreach ($rss->getItems() as $item) {									// use the foreach loop to set $item to the array <item></item> element values returned by the function $getItems()

	echo "<li><a href='".$item['link']."'>".$item['title']."</a><br />".$item['description']."</li>";
}
?>
</body>
</html>