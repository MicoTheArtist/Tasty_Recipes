<?
## profile.php -- this page lists all of a members bookmarks
session_start();

## function

	## private functions
	require('../tasty_include/tasty_utilities.inc.php');
	
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## valid_input function for checking if a value works when compared to a regular function
	
## variables
	
	## customer_id
	$customer_id = $_SESSION['customer_id'];
		
	## profileID	
	if ($_GET['profileID']) {
		$profileID = $_GET['profileID'];
	}
	else if ($_POST['profileID']) {
		$profileID = $_POST['profileID'];
	}
	else {
		$profileID = $customer_id;
	}
	
				
	$bookmark_id = $_GET['bookmark_id'];
	$delete_bookmark_id = $_GET['delete_bookmark_id'];

	## what is the page number
	$page = 1;
	if (is_numeric($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	
	
	


## make sure that customer_id and profileID are both numeric. If not than have them return to the home page
if (!(is_numeric($_SESSION['customer_id']) || is_numeric($profileID))) {
	header("Location:index.php");
}

## hosting
$db = member_db_connect();

####################################
## bookmark deletion via flagging ##
if ($delete_bookmark_id) {

	## use the delete_customer_bookmark function to delete a bookmark via flagging
	delete_customer_bookmark ($customer_id, $db, $delete_bookmark_id);

	$message = "The bookmark has been deleted successfully";
}
## bookmark deletion via bookmark ##
####################################	


###############################
## entire $myprofile section ##	

## set the $myprofile_arra
$myprofile_array = array();
## if $profileID
if ($profileID) {
	$myprofile_array = fetch_profile ($profileID, $db);
}
## if $customer_id and $myprofile_array is not set
if ($customer_id && (count($myprofile_array) <= 0)) {
	$myprofile_array = fetch_profile ($customer_id, $db);
		$profileID = $customer_id;
}

## if after both of the above checks their is still no $myprofile_array set, than re-direct the user to the home page
if (count($myprofile_array) <= 0) {
	# header("Location:/tasty/index.php");
}
			
## entire $myprofile section ##	
###############################

## header
include("../tasty_include/tasty_header.inc");
?>
<div class="left_col">
<h2>
Recipes for <? echo $myprofile_array['login']; ?>
<? ## if the $myprofile_array['name'] exists than print out the following below. 
if ($myprofile_array['name']) {
	 echo "-- ".$myprofile_array['name'];
}
?>
</h2>
<?
## set the $bookmark_array to the function fetch_customer_bookmark
$bookmark_array = fetch_customer_bookmarks ($profileID, $db, $page,$bookmark_id ,$customer_id);
## set $array_count to the count of $bookmark_array as a trigger for deciding weather to print "No bookmarks yet." or not
$array_count = count($bookmark_array);

	#################
	## Next button ##
	
	## if $page <= 1
	if ($page <= 1) {
		## print out the back string
		?>
		&lt;&lt; back |
		<?	
	}
	## else 
	else {
		## print out the back button
		?>
        <a href="profile.php?profileID=<? echo $profileID; ?>&page_name=<? echo $page_name; ?>&page=<? echo ($page - 1); ?>">&lt;&lt; back</a> |
        <?
	}
	## Next button ##
	#################


	#################
	## Back button ##
	
	## if $array_count < 5
	if ($array_count < 5) {
		## print out the next string
		?>
		next &gt;&gt;
		<?
	}
	## else 
	else {
		## print out the next button
		?>
        <a href="profile.php?profileID=<? echo $profileID; ?>&page_name=<? echo $page_name; ?>&page=<? echo ($page + 1); ?>">next &gt;&gt;</a> |
        <?
	}
	## Back button ##
	#################
	
	
	##################
	## current page ##
	?>
	<span class="current_page_spacer">Current Page =  <span class="red_font" ><? echo $page; ?></span></span>
	<?
	## current page ##
	##################


	#################
	## Total pages ##
	$total = fetch_total_profile_bookmarks ($profileID, $db);
	?>	
	<span class="current_page_spacer">Total Page = <span class="red_font"><? echo $total; ?></span></span>
	<?
	## Total pages ##
	#################



?>
<ol>
<?
## use the while each loop to print out all of the itterations from the $bookmark_array
while (list($key, $this_bookmark) = each($bookmark_array)) {
?>
<li><a href="<? echo $this_bookmark['url']; ?>">
<? echo $this_bookmark['title']; ?></a><br />
<? echo $this_bookmark['notes']; ?><br />

<?
## add the edit button if a member is on his/her own profile
if ($customer_id == $profileID) {
?>
	<span class="move_over"><a class="grey_font" href="bookmark.php?bookmark_id=<? echo $this_bookmark['bookmark_id']; ?>&page=<? echo $page; ?>"  >Edit</a></span>
<?
}
?>

<?
################################################################################################################################################
## save button --- display if the member is logged in and if the logged in member does not already have the bookmark saved in his/her profile ##
if ((is_numeric($customer_id)) && !($customer_id == $profileID)) {
	
	if (!(if_member_has_bookmark ($customer_id, $db, $this_bookmark['bookmark_id']))) {
	
?>
    <span class="move_over2_save"><a class="grey_font" href="bookmark.php?save_id=<? echo $this_bookmark['bookmark_id']; ?>&profileID=<? echo $profileID; ?>">Save</a></span>
<?
	}
	else {
	?>
    	<span class="move_over3_save"><div class="grey_font_light" >Save</div></span>
	<?
	}
}
## save button --- display if the member is logged in and if the logged in member does not already have the bookmark saved in his/her profile ##
################################################################################################################################################



###########################################################################################
## delete button --- only show if the logged in member is looking at his/her own profile ##
if ($customer_id == $profileID) {
?>
	<span class="move_over2_save">
    	<a class="grey_font" href="profile.php?delete_bookmark_id=<? echo $this_bookmark['bookmark_id']; ?>&profileID=<? echo $_GET['profileID']; ?>&page_name=my recipes">
        	Delete
		</a>
	</span>
<?
}
## delete button --- only show if the logged in member is looking at his/her own profile ##
###########################################################################################

?>
<br />
<hr />

<?
}
## print out "No bookmarks yet." if $array_count is less than or equal to zero.
if ($array_count == 0) {
	echo "No bookmarks yet.";
}
?>
</ol>
<br /><br />
</div>

<div class="right_col">
 <div class="box">
  <div class="top">
   <div class="inside">
        <div class="title">
         <div class="left">
          tags
         </div>
         <div class="clear_both"></div>
        </div>
   </div>
  </div>
  <div class="bot">
   <div class="inside">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/tag_styles.css" />
<?
include('../tasty_include/tag_cloud.inc.php');
$bookmark_array = fetch_bookmark_tags($customer_id, $db);
$cloud = new wordCloud($bookmark_array);
$cloud->addWord("throw", 2);

echo $cloud->showCloud();
?>
   </div>
  </div>
 </div>
</div>
<div class="clear_both"></div>
<?
## Add the current member to

?>
&nbsp;&nbsp;&nbsp;&nbsp;  <a href="network.php?profileID=<? echo $profileID; ?>">View this Members Network</a>
<?

?>

<?
## Add the current member to
if ($profileID == $customer_id) {
?>
&nbsp;&nbsp;&nbsp;&nbsp;  <a href="add.php?profileID=<?php echo $profileID; ?>">Add Member to Network</a>
<?
}
?>



<?
## footer
include("../tasty_include/tasty_footer.inc");

echo "////////////";
echo "<br><br><br>";
echo "<pre>";
print_r($bookmark_array);
echo "</pre>";
echo "////////////";

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