<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/tag_styles.css"  />
<?

// include 
include('../tasty_include/tag_cloud.inc.php');

// $randomWords -- set this to a randomly worded array()
$randomWords = array (
						'webmasterworld', 'Computer', 'Skateboardking','PC',
						'music','music','music',
						'music','PHP','C','XHTML','eminem',
						'programming','forums','webmasterworld',
						'Chill out','email','forums','Computer','GTA','css','mysql',
						'sql','css','mysql','sql',
						'forums','internet','class','object','method','music','music',
						'music','music','gui','encryption'
					  );

// instantiate cloud to wordCloud($word)
$cloud = new wordCloud($randomWords);
$cloud->addWord('music',12);
$cloud->addWord('downloads',8);
$cloud->addWord('internet',17);
$cloud->addWord('PHP',22);
$cloud->addWord('CSS',32);
echo $cloud->showCloud();

// print out the array $wordsArray
echo "<pre>";
print_r($cloud->wordsArray);
echo "</pre>";

echo "Cloud Size:".$cloud->getCloudSize()."<br>";

$cloud->shuffleCloud();

echo "<pre>";
print_r($cloud->wordsArray);
echo "<pre>";


?>