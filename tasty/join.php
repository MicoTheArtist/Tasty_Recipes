<?php
session_start();
## functions - Private functions only

	## private functions
	require('../tasty_include/tasty_utilities.inc.php');
	
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## valid_input function for checking if a value works when compared to a regular function
		
	## pear funcitons
	require_once('HTML/Form.php');
	
	
#####################################################################################################################	
## if $_POST is > 0 this means that the form has been submitted, theirfor it is time to begin processing the form. ##
if (count($_POST) >0) {

## hosting
$db = login_db_connect();


## variables
$email = $_POST['email'];
$login = $_POST['login'];
$password = $_POST['password'];
$verify_password = $_POST['password2'];

## regular strings
$good_email = "[a-zA-Z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}";
$good_login = "[a-zA-Z0-9_]{3,12}";
$good_password = $good_login;

	## check weather all of the form fields have been submitted
	if (!($email && $login && $password && $verify_password)) {
		$error_message = "Check weather all of the form fields have been submitted";
	}
	## make sure that email is not > thatn 50 charachters and is a valid email.
	else if ((strlen($email) > 50) || !(valid_input($email, $good_email))) {
		$error_message = "Make sure that email is not > thatn 50 charachters and is a valid email.";
	}
	## make sure that login is between 3 and 12 charachters and is made up of only letters, numbers and (_)'s.
	else if ((strlen($login) < 3) || (strlen($login) > 12) || !(valid_input($login, $good_login))) {
		$error_message = "Make sure that login is between 3 and 12 charachters and is made up of only letters, numbers and (_)'s.";
	}
	## make sure password is between 3 and 12 charachters and is make up on only letters, numbers and (_)'s.
	else if ((strlen($password) < 3) || (strlen($password) > 12)  || !(valid_input($password, $good_password))) {
		$error_messsage = "Make sure password is between 3 and 12 charachters and is make up on only letters, numbers and (_)'s.";
	}	
	## make sure that both passwords are matching
	else if (!($password == $verify_password)) {
		$error_message = "Make sure that both passwords are matching";
	}
	## make sure that email does not already exist
	else {
		$command = "select * from customer_info where email = '".addslashes($email)."';";
		$result = mysql_query($command);
		if ($data = mysql_fetch_object($result)) {
			$error_message = "This email alread exists.";
		}
		else {
		## make sure that login does not already exist
			$command = "select * from customer_logins where login = '".addslashes($login)."';";
			$result = mysql_query($command);
			if ($data = mysql_fetch_object($result)) {
				$error_message = "The login you have entered already exists.";
			}
			else {
			#################################################################################################################
			## All fields have passed -- time to insert the field values into the customer_login and customer_info tables. ##
			
			#########################
			## transactions part 1 ##
			
			## variables for the transactions
			$success = true;
			$customer_id = '';

			## set autocommit=0
			$command = "set autocommit=0;";
			$result = mysql_query($command);
			
			## begin
			$command = "begin;";
			$result = mysql_query($command);
			
			## transactions part 1 ##
			#########################
			
			## insert the values (customer_id, login, password) into the customer_login table
			$command = "insert into customer_logins (customer_id, login, password) values ('', '".addslashes($login)."', password('".addslashes($password)."') );";
			$result = mysql_query($command);
			## check $result and mysql_affected_rows()
			if (($result == false) || (mysql_affected_rows() == 0)) {
						
				## success = false;
				$success = false;
				
			}
			else {
				## inside the variable customer_id capture the customer_id value from the previous insert sql commane above.
				$customer_id = mysql_insert_id();
				## insert the values (customer_id, email, date_enrolled) into the customer_info table
				$command = "insert into customer_info (customer_id, email, date_enrolled) values (".addslashes($customer_id).", '".addslashes($email)."', now());";
				## validate $result and check the amount of mysql_affected_rows()
				if (($result == false) || (mysql_affected_rows() == 0)) {
					$success = false;
				}
			}

			#########################
			## transactions part 2 ##
			if (!$success) {
				## rollback
				$command = "rollback;";
				$result = mysql_query($command);
				$error_message = "We are sorry, however, your entry was not entered correctly.  Please try again.";
			}
			else {
				## commit
				$command = "commit;";
				$result = mysql_query($command);
				## set the $_SESSION variables for customer_id and customer_login
				$_SESSION['customer_id'] = $customer_id;
				$_SESSION['customer_login'] = $login;
			}
				
			## transactions part 2 ##
			#########################


			#########################
			## transactions part 3 ##

			## set autocommit=1
			$command = "set autocommit=1;";
			$result = mysql_query($command);
			
			## transactions part 3 ##
			#########################
			
			
			
			## All fields have passed -- time to insert the field values into the customer_login and customer_info tables. ##
			#################################################################################################################
			}						
		}						
	}			
}
## if $_POST is > 0 this means that the form has been submitted, theirfor it is time to begin processing the form. ##
#####################################################################################################################	


## header
include("../tasty_include/tasty_header.inc");

###################################################################################
## if a member is logged in $_SESSION['member_id'] than do not sho the join form ##
if ($_SESSION['customer_id']) {
?>

<h4>Welcome <?php echo $_SESSION['customer_login']; ?>! <a href="profile.php">Click here</a> to go to your bookmarks, or <a href="logout.php">click here</a> to log out.</h4>

<?php
}
else {
## else if a member is not logged in than show the join form
?>
<h4>Join now and start bookmarking.  It's easty and free!</h4>
<span style="color:red; font-size:12px;">
	<?php
    ## error_message
    if ($error_message) {
		echo $error_message;
	}
    ?>
</span>
<!-- start of join form -->
<?
	$form = new HTML_Form('join.php','post');
	
	$form -> addText("email", "Your Email Address: ", $email, 25, 50);
	$form -> addText("login", "Choose a Login: ", $login, 25, 50);
	$form -> addPasswordOne("password", "Choose a Password: ", $password, 12, 12);
	$form -> addPasswordOne("password2", "Please retype your Password: ", $verify_password, 12, 12);
	$form -> addSubmit("submit", "Submit");
	$form -> addPlainText("Already a member?", "<a href='login.php'>Click here</a> to log in!");
	
	$form -> display();

?>
<!-- end of join form -->

<?php
}
## if a member is logged in $_SESSION['member_id'] than do not sho the join form ##
###################################################################################


## footer
include("../tasty_include/tasty_footer.inc");
/*
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
*/
?>