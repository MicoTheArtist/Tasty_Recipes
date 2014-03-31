<?
// require_once
require_once('XML/RSS.php');										

// VARIABLES
$rss_feed = "http://mespinosa.userworld.com/tasty/tasty_recipes.rss";									// $rss_feed

// instantiate $rss to the class called XML/RSS.php	
$rss = new XML_RSS($rss_feed);												
$rss->parse();																							// use the the $parse() function to convert the rss xml into a php readable form

$channel = $rss->getChannelInfo();																		// set $channel to the array returned by the $getChannelInfo() array

?>
<html>
<head>
	<title><? echo $channel['title']; ?></title>
</head>
<body>
	
    <h1><a href="<? echo $channel['link']; ?>"><? echo $channel['title']; ?></a></h1>
    <h2><? echo $channel['description']; ?></h2>

<?
foreach($rss->getItems() as $item) {																	// use the foreach loop to set each of the array item elements returned by the $getItems() to $item

	echo "<li><a href='".$item['link']."'>".$item['title']."</a><br>".$item['description']."</li>";		// print out title, link & description
	
}
?>
</body>
</html>