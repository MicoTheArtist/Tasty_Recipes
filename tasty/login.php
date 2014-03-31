<?php
session_start();

## functions
	
	## private functions
	require ('../tasty_include/tasty_utilities.inc.php');
	
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## verify entered field values by comparing them again regular expessions using a valid_input function.
		

###################################################################################
## if $_POST > 0, then the form has been submitted so start processing the form. ##
if ($_POST) {

## hosting
$db = login_db_connect();

## variables
$login = $_POST['login'];
$password = $_POST['password'];

## regular expressions
$good_login = "[a-zA-Z0-9_]";
$good_password = $good_login;

	## make sure all of the expressions have been submitted.
	if (!($login && $password)) {
		$error_message = "Make sure all of the expressions have been submitted.";
	}
	## make sure the login is between 3 and 12 charachters and that it is made up of only letters, numbers and (_)'s.
	else if ((strlen($login) < 3) || (strlen($login) > 12) || !(valid_input($login, $good_login))) {
		$error_message = "Make sure the login is between 3 and 12 charachters and that it is made up of only letters, numbers and (_)'s.";
	}
	## make sure the password is between 3 and 12 charachters and that it is made up of only letters, numbers and (_)'s.
	else if ((strlen($password) < 3) || (strlen($login) > 12) || !(valid_input($password, $good_password))) {
		$error_message = "Make sure the password is between 3 and 12 charachters and that it is made up of only letters, numbers and (_)'s.";
	}
	## check that login exists
	else {
		$command = "select * from customer_logins where login = '".addslashes($login)."';";
		$result = mysql_query($command);
		if (!($data = mysql_fetch_object($result))) {
			$error_message = "Your login does not exist";
		}
		else {
		## check that the passwod exists
			$command = "select * from customer_logins where login = '".addslashes($login)."' and password = password('".addslashes($password)."');";
			$result = mysql_query($command);
			if (!($data = mysql_fetch_object($result))) {
				$error_message = "Please enter a valid password";
			}
			else {
			
				## all checks have passed -- now find out what the customer_id is.
				$command = "select * from customer_logins where login = '".addslashes($login)."' and password = password('".addslashes($password)."');";
				$result = mysql_query($command);				
				if ($data = mysql_fetch_object($result)) {
					## set the sessions $_SESSION's for customer_id and login_id.
					$_SESSION['customer_id'] = $data->customer_id;
					$_SESSION['customer_login'] = $data->login;
				}								
			}		
		}
	}
}
## if $_POST > 0, then the form has been submitted so start processing the form. ##	
###################################################################################


## header
include("../tasty_include/tasty_header.inc");
#################################################
## check weather to show the login form or not ##

## if $_SESSION['customer_id'] do not show the login form
if ($_SESSION['customer_id']) {
?>
	<!-- show the message "Welcome Mico!  Click here to go to your bookmarks, or click here to log out." -->
    <h4>Welcome <?php echo $_SESSION['customer_login']; ?>!  <a href="profile.php?page_name=my recipes&profileID=<? echo $_SESSION['customer_id']; ?>">Click here</a> to go to your bookmarks, or <a href="logout.php">click here</a> to log out.</h4>
    
<?php    
}
else {
## else if $_SESSION['customer_id'] is not present show the login form.	
?>
    <h4>Welcome! login here.</h4>

<span style="color:red; font-size:12px;">
<?php	
    ## check weather the $error_message is present.  If so display its message
	if ($error_message) {
		echo $error_message;
	}
?>
</span>


<!-- start the login form -->
<form method="post" action="login.php">
<table>
	<tr>
    	<td align="right">
            <!-- User Name: -->
            User Name:
        </td>
        <td align="left">
            <!-- input t=text s=12 m=12 n=login -->
            <input type="text" size="12" maxlength="12" name="login" value="<?php echo $_POST['login']; ?>" />
		</td>            
    </tr>
    <tr>
    	<td align="right">
            <!-- Password -->
            Password:
		</td>
        <td align="left">            
            <!-- input t=password s=12 m=12 n=password -->	
            <input type="password" size="12" maxlength="12" name="password" value="<?php echo $_POST['password']; ?>" />
		</td>            
    </tr>
    <tr>
    	<td align="center" colspan="2">
    		<!-- submit button -->
            <input type="submit" value="SUBMIT" />
		</td>            
	</tr>            
</table>
</form>
<!-- end of the login form -->

<?php
}
## check weather to show the login form or not ##
#################################################

## footer
include("../tasty_include/tasty_footer.inc");
?>