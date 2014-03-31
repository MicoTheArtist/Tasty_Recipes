<?

## hosting
$host = "localhost:/users/mespinosa/mysql-php/data/mysql.sock";
$user = "tasty_member";
$pw = "trish123";
$database = "tasty_recipes";

$db = mysql_connect($host, $user, $pw)
		or die ("Cannot connect to MySQL");
mysql_select_db($database, $db)
		or die ("Cannot connect to Database");		

# include ('tasty_public.inc.php');

	function valid_input ($myinput, $good_input) {
	
		if (preg_match("/$good_input/",$myinput)) {
		
			return true;
		}
		else {
		
			return false;
		}

	}


$good_url = "[a-z]";

## $myinput = "http://www.junglesoftware.com/home/";
$url = "http://mespinosa.userworld.com/";
# $url = "123";
# $url = "dogs";


if (!(valid_input ($url, $good_url))) {
	$error_message =  "not valid";
}


echo $error_message;

echo "<br><br><br>";
echo "<pre>";
print_r($test);
echo "</pre>";

echo "<br><br><br>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<br><br><br>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<br><br><br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";


?>