<?php
require_once "XML/RSS.php";

//Be sure to replace mespinosa with your Sandbox login
$rss_feed = "http://rss.cnn.com/rss/cnn_space.rss";
$rss =& new XML_RSS($rss_feed);
$rss->parse();

$channel = $rss->getChannelInfo();
?>

<html>
<head>
    	<title><? echo $channel['title']; ?></title>
</head>
<body>

	<h1><a href="<? echo $channel['link']; ?>">		<? echo $channel['title']; ?>		</a></h1>

	<h2><? echo $channel['description']; ?></h2>
    
<ul>

<?
foreach ($rss->getItems() as $item) {
	
	echo "<li><a href=\"".$item['link']."\">".$item['title']."</a><br />".$item['description']."</li>\n";
}
?>

</ul>
</body>
</html>