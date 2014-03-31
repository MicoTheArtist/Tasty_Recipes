<?php
## network.php -- this page will list out all of the login members of this current members network as well as their actual bookmarks
session_start();

## functions

	## private functions
	require('../tasty_include/tasty_utilities.inc.php');
		## fetch_login_members -- this function will fetch the profile for all of the members that are linked to the logged in member
	
	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## fetch_members_bookmarks -- this function will fetch all of the bookmarks that correspond to the indivual profiles that the logged in member has requested bookmarks from
		## fecth_members_profile -- this function will fetch the profile for the logged in member.
		
		
## hosting
$db = public_db_connect();
	

## variables
$customer_id = $_SESSION['customer_id'];	## customer_id as a session
$profileID = $_GET['profileID'];			## profileID as a Get
if (!($profileID)) {	## If the get value for the profileID has not been passed then set it to the customer_id
	$profileID = $customer_id;
}

## make sure that customer_id and profileID are both numeric.  If they are not then redirect to index.php page.
if (!(is_numeric($profileID) || is_numeric($customer_id))) {
	header("Location:index.php");
}

####################################
## entire $member_profile Section ##

## set up the $member_profile_array
$member_profile_array = array();

## $member_profile_array to the fetch_profile function
$member_profile_array = fetch_profile ($customer_id, $db);

## entire $member_profile Section ##
####################################



## header
include("../tasty_include/tasty_header.inc");
?>


<table>
	<tr>
     	<td valign="top">
<!-- start left hand column list for the network bookmarks -->
<div class="left_col">

<!-- <h2>Members Bookmark Network</h2> -->
<h2><? echo $member_profile_array['login']; ?>'s Network Bookmarks</h2>
<!--  <h2><? echo $member_profile_array['login']; ?> Network Bookmarks</h2> -->

<ol>
<?
## set up the network $bookmark_array to the function fetch_network_members ($customer_id, $db)
$bookmark_array = fetch_members_bookmarks ($profileID, $db);
## set up the $array_count to the $bookmark_array as a trigger to decide weather to print out "No bookmarks in network yet." or not.
$array_count = count($bookmark_array);
## use the while each loop to print all of the iteration value sets from the $bookmark_array;
while (list($key, $this_book_array) = each ($bookmark_array)) {
?>
	<li><a href="<? echo $this_book_array['url']; ?>"><? echo $this_book_array['title']; ?></a><br />
    				  <? echo $this_book_array['notes']; ?><br /><hr />
<?
}
## print out No bookmarks in network yet if $array_count is equal to zero.
if ($array_count == 0) {
	echo "No bookmarks in network yet.";
}
?>
</ol>
</div>
<!-- end left hand column list for the network bookmarks -->
        </td>
        <td valign="top">
<!-- start Right hand column list for the network bookmarks -->
<div class="right_col" >
<br /><br /><br />
<h4>Members Bookmark Network</h4>


    <ul>
    	<li>
			<?
            ## set the $login_member_array to the function fetch_login_member ($customer_id, $db)
            $login_member_array = fetch_login_member ($profileID, $db);	
            ## $array_login_count to the count of the $login_member_array	
            $array_login_count = count($login_member_array);
            ## use the while each loop to print out the itterations from the $login_member_array	
            while (list($key, $this_login_member) = each($login_member_array)) {
            ?>
                <li><a href="profile.php?profileID=<? echo $this_login_member['app_member']; ?>"><? echo $this_login_member['login']; ?></a><br />
            <?
            }
            
            ## if $array_login_count is equal to zero than print out "No login members in your network yet."
            ?>
        <span style="color:red; font-size:12px;">
            <?
            if ($array_login_count == 0) {
                echo "No login members in your network yet.";
            }
            ?>
        </span>    
        </li>
    </ul>


</div>
<!-- end Right hand column list for the network bookmarks -->
        </td>
	</tr>
</table>    

<?php
## footer
include("../tasty_include/tasty_footer.inc");
?>