<?php
## bookmark.php -- this page is the interface where the logged in member will add new bookmarks to his/her profile.
session_start();

## functions

	## private functions
	require('../tasty_include/tasty_utilities.inc.php');
	
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## valid_input function for checking if a value works when compared to a regular function
	
## variables - just for checking who is logged in
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
	
	## save_id
	if (is_numeric($_GET['save_id'])) {
		$save_id = $_GET['save_id'];
	}

## make sure the $customer_id is number.  If $customer_id is not a number than re-direct to the home page
if (!(is_numeric($customer_id))) {
	header("Location:index.php");
}


######################################################################
## only process the form if a $_POST has been submitted by the user ##
if (count($_POST) > 0) {

## hosting
$db = member_db_connect();

## variables
$url = $_POST['url'];		
$title = $_POST['title'];	
$notes = $_POST['notes'];	
$page_name = $_GET['page_name'];
$profileID = $_GET['profileID'];
$tags = explode(" ", $_POST['tags']);										// $tags - the explode turns the $_POST['tags'] string value into an array
	for ($i = 0; $i < count($tags); $i++) {									// loop through the $tags array

		$tags[$i] = strtolower(preg_replace("/[^a-zA-Z0-9]/","",$tags[$i]));		// make all of the values in the tag array lowercase and delete any non-alphanumeric's
	}
	
## regular expressons
## good_url
$good_url = "|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i";

	## make sure the URL is valid.
	if (!( valid_url ($url, $good_url))) {
		$error_message = "Make sure url is valid.";
	}

	## make sure all values are valid
	else if (!($url && $title)) {
		$error_message = "Make sure all values are valid";
	}
	## make sure url is valid and than url is not greater than 250 charachters
	
	else if (strlen($url) > 250) {
	
		$error_message = "Make sure url is not greater than 250 charachters";
	}
	## make sure that notes is not greater than 250 charachters
	else if (strlen($title) > 250) {
		$error_message = "Make sure that notes is not greater than 250 charachters";
	}
	else {
	#########################################################################
	## all checks have passed -- time to proccess the sql and transactions ##
	
	########################
	## transactions set 1 ##
	
	## success
	$success = true;
	## set autocommit=0
	$command = "set autocommit=0;";
	$result = mysql_query($command);
	## begin
	$command = "begin;";
	$result = mysql_query($command);
	## transactions set 1 ##
	########################
	
		##############################################################################
		## checking and inserting the url value against and into bookmark_url table ##
		
		## command -- check weather or not the url already exists
		$command = "select * from bookmark_urls where url = '".addslashes($url)."';";
		$result = mysql_query($command);
		if ($data = mysql_fetch_object($result)) {
			## bookmark_id -- if the url already exists than just set the bookmark_id
			$bookmark_id = $data->bookmark_id;
		}
		else {
			## command -- if the url does not exist than insert the title, notes, and bookmark_id into the customer_bookmark table
			$command = "insert into bookmark_urls (bookmark_id, url) values ('', '".addslashes($url)."');";
			$result = mysql_query($command);
			if (($result == false) || (mysql_affected_rows() == 0)) {
				$success = false;
			}
			else {
				$bookmark_id = mysql_insert_id();
			}
		}
		
		## if a new url was inserted, than the old one will need to be deleted ##
		if (($_POST['bookmark_id']) && ($_POST['bookmark_id'] != $bookmark_id)) {
			## set old_bookmark_id
			$old_bookmark_id = $_POST['bookmark_id'];
			## command -- use the update/deletion flag to remove the old url and bookmark_id row
			$command = "update customer_bookmarks 
						set date_deleted = now() 
						where customer_id = ".addslashes($customer_id)." 
						and bookmark_id = ".addslashes($old_bookmark_id).";";
			echo "<br><br>".$command."<br><br>";
			## result				
			$result = mysql_query($command);
		}
		
		## checking and inserting the url value against and into bookmark_url table ##
		##############################################################################
	
	
		#####################################################################################################
		## checking, inserting or updating the customer title and notes within the customer_bookmark table ##
		if ($success && (is_numeric($bookmark_id))) {
		## command -- check weather the bookmark_id and customer_id that we have already exist within the customer_bookmark table or not
		$command = "select * from customer_bookmarks where customer_id = ".addslashes($customer_id)." and bookmark_id = ".addslashes($bookmark_id).";";
		$result = mysql_query($command);
			if ($data = mysql_fetch_object($result)) {
				## command -- if both the bookmark_id and the customer_id both exist, than just update the title and notes
				$command = "update customer_bookmarks set title = '".addslashes($title)."', notes = '".addslashes($notes)."', date_posted = now() where customer_id = ".addslashes($customer_id)." and bookmark_id = ".addslashes($bookmark_id).";";
				$result = mysql_query($command);
				if (($result == false) || (mysql_affected_rows() == 0)) {
					$success = false;
				}
			}
			else {			
				## command -- of both the bookmark_id and the customer_id do not exist, than insert title, notes, bookmark_id, and customer_id into table customer_bookmarks
				$command = "insert into customer_bookmarks (customer_id, bookmark_id, title, notes, date_posted) values (".addslashes($customer_id).", '".addslashes($bookmark_id)."','".addslashes($title)."', '".addslashes($notes)."', now());";
				$result = mysql_query($command);
				if (($result == false) || (mysql_affected_rows() ==0)) {
					$success = false;
				}
			}			
		}
		## checking, inserting or updating the customer title and notes within the customer_bookmark table ##
		#####################################################################################################
		
		
		################################################################
		## entire tasty_tags and bookmark_tags add and update section ##
		
		// command - first update the bookmark_tags setting date_deleted to deleted(now()) for a record matching bookmark_id, customer_id and date_deleted <= 0
		$command = "update bookmark_tags 
					set date_deleted = now() 
					where bookmark_id = '".addslashes($bookmark_id)."' 
					and customer_id = '".addslashes($customer_id)."' 
					and date_deleted <= 0;";
		$result = mysql_query($command);
		
		for ($j=0; $j < count($tags); $j++) {											// loop through the $tags array for adding and updating the following sections
		
			if ($success && $tags[$j]) {											// validate $success and $tags[$j] before entering them into the followint tables
				
				#######################################################
				##  entire tasty_tags table add and update section   ##
				
				// command - first check to see if the tag already exists 
				$command = "select tag_id from tasty_tags where tag = '".addslashes($tags[$j])."';";
				$result = mysql_query($command);
				
				if ($data = mysql_fetch_object($result)) {							// if - the tag already exists
				
					$tag_id = $data->tag_id;										// $tag_id - just set this to the existing tag_id
				}
				else {
				
					// command - insert the new tag
					$command = "insert into tasty_tags (tag_id, tag) values ('','".addslashes($tags[$j])."');";
					$result = mysql_query($command);
						
					if (($result == false) || (mysql_affected_rows() == 0)) {		// test the $result
					
						$success = false;
					}
					else {
					
						$tag_id = mysql_insert_id();								// $tag_is - set this to get ready for the bookmark_tags table that follows
					}	
				}
				##  entire tasty_tags table add and update section   ##
				#######################################################
				
				
				#######################################################
				## entire bookmark_tags table add and update section ##
			
				if ($success && is_numeric($tag_id)) {								// validate $success and that $tag_id is numeric
				
					// command - first check and see if a tag_id already exists for tag_id, bookmark_id and customer_id
					$command = "select tag_id 
								from bookmark_tags 
								where tag_id = '".addslashes($tag_id)."' 
								and bookmark_id = '".addslashes($bookmark_id)."' 
								and customer_id = '".addslashes($customer_id)."';";
					$result = mysql_query($command);
					
					if ($data = mysql_fetch_object($result)) { 						// if a record exist
					
						// command -- just update the record so that date_deleted is not deleted (set to 0)
						$command = "update bookmark_tags 
									set date_deleted = 0 
									where tag_id = '".addslashes($tag_id)."' 
									and bookmark_id = '".addslashes($bookmark_id)."' 
									and customer_id = '".addslashes($customer_id)."';";
						$result = mysql_query($command);
						
						if ($result == false) {										// test the result
						
							$success = false;
							
						}
					}	
					else {
					
						// command - insert the new tag_id, bookmark_id, customer_id
						$command = "insert into bookmark_tags (tag_id, bookmark_id, customer_id) values 
									('".addslashes($tag_id)."','".addslashes($bookmark_id)."','".addslashes($customer_id)."');";
						$result = mysql_query($command);
						
						if (($result == false) || (mysql_affected_rows() == 0)) {	// test the result
						
							$success = false;
						}
					}
				}
				## entire bookmark_tags table add and update section ##
				#######################################################
			}	
		}
		## entire tasty_tags and bookmark_tags add and update section ##
		################################################################
		
			
	
	########################
	## transactions set 2 ##
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
	
	## transactions set 2 ##
	########################
	
	
	########################
	## transactions set 3 ##
	
	## set autocommit=1
	$command = "set autocommit=1;";
	$result = mysql_query($command);
	
	## transactions set 3 ##
	########################
	
	## if $success, then re-direct back to the profile.php page
	if ($success) {
		header("Location:profile.php?profileID=".$_SESSION['customer_id']."&page=".$_POST['page'].$_GET['page']."&page_name=my recipes");
	}
	
	## all checks have passed -- time to proccess the sql and transactions ##			
	#########################################################################
	}
}
## only process the form if a $_POST has been submitted by the user ##
######################################################################



####################################################################
## if $_GET['bookmark_id'] exsits --- the edit button was pressed ##
if (is_numeric($_GET['bookmark_id'])) {

## hosting
$db = member_db_connect();

	## set $bookmark_array = fetch_cusotmer_bookmarks
	$bookmark_array = fetch_customer_bookmarks ($_SESSION['customer_id'], $db, $page, $_GET['bookmark_id']);
	## set the list($this_bookmark_array) function to the $bookmark_array
	list ($this_bookmark_array) = $bookmark_array;
	## set the values within th list() funciton to the values_array for $this_bookmark-array	
	list ($customer_id, $bookmark_id, $title, $notes, $date_posted, $url) = array_values($this_bookmark_array);
	
	$tag_array = fetch_bookmark_tags ($_SESSION['customer_id'], $db, $_GET['bookmark_id']);		// $tag_array --- use the function to retrieve all of the specific customers bookmark tag's
	
	$tag_string = implode(" ", $tag_array);															// $tag_string -- turn the $tag_array into a string
	
	
}
## if $_GET['bookmark_id'] exsits --- the edit button was pressed ##
####################################################################

########################################################
## if $save_id -- the save button was pressed ##

if (is_numeric($_GET['save_id'])) {

## hosting
$db = public_db_connect();
	
	## set flag
	$flag = "recent";
	## set page
	$page = 1;
	## set $customer_id
	# $customer_id = $_SESSION['customer_id'];
	## set $bookmark_id
	$bookmark_id = $save_id;
	## set $bookmark_array = to the fetch_bookmarks function
	$bookmark_array = fetch_bookmarks ($flag, $db, $page, $customer_id, $bookmark_id);
	
	## set list($this_bookmark_array) function to the $bookmark_array
	list($this_bookmark_array) = $bookmark_array;
	## set the list() values to the array_values of $this_Bookmark_array
	list($customer_id, $bookmark_id, $title, $notes, $date_posted, $login, $url, $popularity, $flag_count, $block_count ) = array_values($this_bookmark_array);
}

## if $_GET['save_id'] -- the save button was pressed ##
########################################################


## header
include("../tasty_include/tasty_header.inc");
?>

<?
###################
## set the title ##
if (is_numeric($_GET['bookmark_id'])) {
?>
	<h4>Edit your bookmark:</h4>
<?
}
else if (is_numeric($save_id)) {
?>
	<h4>Save this bookmark:</h4>
<?
}
else {
?>  
	<h4>Post a new bookmark:</h4>

<?
}
## set the title ##
###################
?>

<span style="color:red; font-size:12px;">
<?  ## print the $error_message 
if ($error_message) {
	echo $error_message;
}
?>
</span>

<!-- start the bookmark form -->
<form method="post" action="bookmark.php?page_name=<? echo $_GET['page_name']; ?>&profileID=<? echo $profileID; ?>">
<table>
	<tr>
    	<td align="right">
            <!-- url: -->
            url:
		</td>
        <td align="left">            
            <!-- input t=text s=50 m=250 n=url v=url -->
            <input type="text" size="50" maxlength="250" name="url" value="<? echo htmlentities($url); ?>" />
		</td>            
	</tr>
    <tr>
    	<td align="right">           
            <!-- title: -->
            title:
		</td>
        <td align="left">            
            <!-- inpt t=text s=50 m=250 n=title v=title -->
            <input type="text" size="50" maxlength="250" name="title" value="<? echo htmlentities($title); ?>" />
		</td>            
	</tr>
    <tr>
    	<td align="right">       
            <!-- note: -->
            note:
		</td>
        <td align="left">            
            <!-- textaread --><!-- notes -->
            <textarea rows="3" cols="49" max="500" name="notes"><? echo htmlentities($notes); ?></textarea>
		</td>            
	</tr>
    <tr>
    	<td align="right">
            <!-- tags: -->
            tags:
        </td>
        <td align="left">
            <!-- inpt t=text s=50 m=250 n=tags v=tag_styles -->
            <input type="text" size="50" maxlength="250" name="tags" value="<? echo htmlentities($tag_string); ?>"  />
            
            <!-- *separate tags with spaces -->
            * separate tags with spaces
        </td>
    </tr>
    <tr>
    	<td align="right" colspan="2" >
			
            <?
			#####################################################################
			## hidden form inputs for $_GET['save_id'] or $_GET['bookmark_id'] ##
			if (is_numeric($_GET['save_id'])) {
			?>
                        
            	<!-- bookmark_id -->
                <input type="hidden" name="bookmark_id" value="<? echo htmlentities($_GET['save_id'].$_POST['save_id']); ?>"  />
                <!-- page -->
                <input type="hidden" name="page" value="<? echo htmlentities($_GET['page'].$_POST['page']); ?>"  />
                
			<?
			}
			else if (is_numeric($_GET['bookmark_id'])) {
			#### else if $_GET['bookmark_id']
			?>
            
                <!-- bookmark_id  -->
                <input type="hidden" name="bookmark_id" value="<? echo htmlentities($_GET['bookmark_id'].$_POST['bookmark_id']); ?>" />
                <!-- page -->
                <input type="hidden" name="page" value="<? echo htmlentities($_GET['page'].$_POST['page']); ?>"  />
                
    		<?
			}
			## hidden form inputs for $_GET['save_id'] or $_GET['bookmark_id'] ##
			#####################################################################
			?>
            <!-- submit button -->
            <input type="submit" value="SUBMIT"  />
		</td>            
	</tr>            
</table>    
</form>
<!-- end the bookmark from -->

<?
## footer
include("../tasty_include/tasty_footer.inc");

echo "//////";

echo "<br><br><br>";
echo "<pre>";
print_r($tag_string);
echo "</pre>";

echo "//////"

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