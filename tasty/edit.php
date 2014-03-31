<?
## --- this page is designed for editing a members personal profile profile.
session_start();

## functions
require('../tasty_include/tasty_utilities.inc.php');


include('../tasty_include/tasty_public.inc.php');

## variables (general)
	## $customer_id
	$customer_id = $_SESSION['customer_id'];

	## $profileID	
	if (is_numeric($_GET['profileID'])) {
		$profileID = $_GET['profileID'];
	}
	else if (is_numeric($_POST['profileID'])) {
		$profileID = $_POST['profileID'];
	}
	else {
		$profileID = $customer_id;
	}

## verify member is logged in
if (!($customer_id)) {
	header ('Location:index.php');
}

######################################################################################
## $_POST['update_form'] -- only process the form if the $_POST array has been sent ##
if (count($_POST) > 0) {

## variables ($_POST related)
$email = $_POST['email'];
$name = $_POST['name'];
$homepage = $_POST['homepage'];

## regular expressions
$good_homepage = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";
$good_email = "[a-zA-Z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}";
$good_name = "[a-zA-Z0-9_]{1,50}";

## hosting
$db = member_db_connect();
		
		## make sure that all form fields have been entered correctly.
		if (!($email && $name && $homepage)) {
			$error_message = "Please make sure that all form fields have been entered correctly.";
		}
		## check weather name is valid and less than 50 charachters				
		else if (!(valid_input ($name, $good_name)) || (strlen($name) > 50)) { 
			$error_message = "Please enter a valid name";
		}			
		## check weather the homepage url is valid
		else if (!(valid_url ($homepage, $good_homepage))) {
			$error_message = "Please enter a valid URL for the home page";
		}	
		## check weather email is less than 50 charachters and valid
		else if ((strlen($email) > 50) || !(valid_input ($email, $good_email))) {
			$error_message = "Please enter a valid email less than 50 charachters";
		}
		###################################################################
		## all checks have passed -- times to start the sql transactions ##
		else {
			########################
			## transactions set 1 ##
			
			## set success
			$success = true;		
			## set autocommit to false
			$command = "set autocommit=0;";
			$result = mysql_query($command);
			## begin
			$command = "begin;";
			$result = mysql_query($command);
			## transactions set 1 ##
			#########################
			
				##################################################################################
				## because the customer_id already exists, an update is all that is needed here ##
				
				## if success
				if ($success) {
					## update the customer_info table
					$command = "update customer_info set email = '".addslashes($email)."', name = '".addslashes($name)."', homepage = '".addslashes($homepage)."' where customer_id = ".addslashes($customer_id).";";
					$result = mysql_query($command);
					
						## if the result varification is false or the mysql_num_rows() == 0
						if (($result == false) || (mysql_affected_rows() == 0)) {
							## set $success to false
							$success = false;
						}
						
				}
				## because the customer_id already exists, an update is all that is needed here ##
				##################################################################################
				
			########################
			## transactions set 2 ##
			
			## if $success is not true
			if ($success == false) {
				## set rollback
				$command = "rollback;";
				$result = mysql_query($command);
			}
			## else 
			else {
				## set commit
				$command = "commit;";
				$result = mysql_query($command);
			}
			## transactions set 2 ##
			#########################
			
					
			########################
			## transactions set 3 ##
			
			## set autocomit to true			
			$command = "set autocommit = 1;";
			$result = mysql_query($command);
			
			## transactions set 3 ##
			#########################
			
			
			## if success use the header to revert back to the home page
			if ($success == true) {
				header("Location:index.php");
			}
		
		}		
		## all checks have passed -- times to start the sql transactions ##
		###################################################################
}
## $_POST['update_form'] -- only process the form if the $_POST array has been sent ##
######################################################################################

#####################################################################################################
## if $_GET['profileID'] --- only create the profile_array if the $_GET['profileID'] has been sent ##
if (is_numeric($_GET['profileID'])) {
## hosting
$db = member_db_connect();
	## set $profile_array = fetch_profile ($profileID, $db)
	$profile_array = fetch_profile ($profileID, $db);
}
## if $_GET['profileID'] --- only create the profile_array if the $_GET['profileID'] has been sent ##
#####################################################################################################


## header
include("../tasty_include/tasty_header.inc");
?>

<!-- title -->
	<h4>Edit your Profile Here:</h4>
    
<span style="color:red; font-size:12px; font-family:Arial, Helvetica, sans-serif;">
<?
## error_message
if ($error_message) {
	echo $error_message;
}
?>
</span>

        <form method="post" action="edit.php" >
        <table >
            <tr>
                <td align="right">E-mail: </td>	<td><input type="text" name="email" value="<? echo htmlentities($profile_array['email']).htmlentities($_POST['email']); ?>" size="50" maxlength="50"></td>
            </tr>
            <tr>
                <td align="right" >Name: </td>	<td><input type="text" name="name" value="<? echo htmlentities($profile_array['name']).htmlentities($_POST['name']); ?>" size="50" maxlength="50"></td>
            </tr>
            <tr>
                <td align="right" >Home Page: </td>	<td><input type="homepage" name="homepage" value="<? echo htmlentities($profile_array['homepage']).htmlentities($_POST['homepage']); ?>" size="50" maxlength="50"></td>
            </tr>
            <tr>
                <td colspan="2" align="right" style="padding-top:12px"> 
                	<!-- page_name -->
                    <input type="hidden" name="page_name" value="<? echo htmlentities($_GET['page_name']).htmlentities($_POST['page_name']); ?>" >
                	<!-- profileID -->
                   <input type="hidden" name="profileID" value="<? echo htmlentities($_GET['profileID']).htmlentities($_POST['profileID']); ?>" >
                    <!-- submit button -->
	                <input type="submit" name="submit" value="submit">
                </td>
            </tr>
            
        </table>
        </form>

<?
## footer
include("../tasty_include/tasty_footer.inc");
?>

