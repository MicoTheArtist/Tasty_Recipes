<?php
## header
session_start();

## make sure the member is logged in.  If not than redirect to the index.php page.
if (!($_SESSION['customer_id'])) {
	header("Location:index.php");
}

## functions

	## private functions
	require('../tasty_include/tasty_utilities.inc.php');
	
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## valid_input function for checking if a value works when compared to a regular function
	
## valide
$customer_id = $_SESSION['customer_id'];
$profileID = $_GET['profileID'];
if (!($profileID)) {
	$profileID = $_SESSION['customer_id'];
}

##########################################################
## process the form only if the $_POST array is present ##
if (count($_POST) > 0) {

## hosting
$db = login_db_connect();		

## variables
$login = $_POST['login'];	## login
	
## regular expressions
$good_login = "[A-Za-z0-9]";	# $good_login

	## make sure the login is not greater than 250 charachters and is a valid login made up of only letters and numbers.
	if ((strlen($login) > 250) || !(valid_input($login, $good_login))) {
		$error_message = "Make sure the login is not greater than 250 charachters and is a valid login made up of only letters and numbers.";
	}
	else {
	########################################################################
	## all checks have passed -- time to process the sql and transactions ##
	
	#######################
	## transaction set 1 ##
	
	## success - default the success as true;
	$success = true;

	## set autocommit=0
	$command = "set autocommit=0;";
	$result = mysql_query($command);
	## begin
	$command = "begin;";
	$result = mysql_query($command);
	## transaction set 1 ##
	#######################
			
	
	##########################################################################################################################################
	## This will check the customer_logins table and see if the login already exists, or needs to be inserted. into the tasty_network table ##
	
	## command -- check and see weather the login already exists or not
	$command = "select customer_id from customer_logins where login = '".addslashes($login)."';";	
	$result = mysql_query($command);
	if (!($data = mysql_fetch_object($result))) {
		## if unsuccessful print an error mesage
		$error_message = "Please re-enter you login. The current login does not exist.";
	}	
	else {	
		## command -- check weather this member is already in the process of requesting approval from this member or not.
		$command = "select friend_id from tasty_network where req_member = ".addslashes($customer_id)." and app_member = ".addslashes($profileID).";";
		$result = mysql_query($command);
		if ($data = mysql_fetch_object($result)) {
			## print out error_message 
			$error_message = "You ar already requesting to add the member to your network.";
		}
		else {			
		## else 
			
			## command -- if the login does already exist than insert the information within the tasty_networks table as a requesting member
			$command = "insert into tasty_network (req_member, req_date, app_member) values (".addslashes($customer_id).", now(), ".addslashes($profileID).");";
			$result = mysql_query($command);
			if (($result == false) || (mysql_affected_rows() == 0)) {
				$success = false;
				$error_message = "We are sorry, their has been an error.  Please enter your information once again.";
			}
		}				
	}
	## This will check the customer_logins table and see if the login already exists, or needs to be inserted. into the tasty_network table ##
	##########################################################################################################################################
	
	
	#######################
	## transaction set 2 ##
	if (!($success)) {
		## rollback
		$command = "rollback;";
		$result = mysql_query($command);
	}
	else {	
		## commit
		$command = "commit;";
		$result = mysql_query($command);
	}
	## transaction set 2 ##
	#######################
	
	
	#######################
	## transaction set 3 ##
	
	## set autocommit=1
	$command = "set autocommit;";
	$result = mysql_query($command);
	## transaction set 3 ##
	#######################
	

		## if $success, re-direct to the network.php page
		if ($success) {
			header ("Location:network.php");
		}
	## all checks have passed -- time to process the sql and transactions ##
	########################################################################
	}
}
## process the form only if the $_POST array is present ##
##########################################################

## header
include("../tasty_include/tasty_header.inc");
?>
<h2>Add a Member</h2>

<span style="color:red; font-size:12px;">
<?php
## print out the error_message
if ($error_message) {
	echo $error_message;
}
?>
</span>

<!-- start of the add member to network form -->
<form method="post" action="add.php?profileID=<? echo $profileID; ?>">
<table>
	<tr>
    	<td align="right">
            <!-- User Name: -->
            User Name:
		</td>            
    	<td align="right">       
            <!-- input t=text s=50 m=250 n=login v=login -->
            <input type="text" size="50" maxlength="250" name="login" value="<? echo htmlentities($login); ?>" />
		</td>            
	</tr>
    <tr>
    	<td align="right" colspan="2">
    		<!-- submit button -->
            <input type="submit" value="SUBMIT" />
		</td>            
	</tr>            
    
</table>
</form>    
<!-- end of the add member to network form -->

<?php
## footer
include("../tasty_include/tasty_footer.inc");
?>
