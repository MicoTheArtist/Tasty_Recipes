<?php
## index.php -- this displays all of the bookmarks from by recent to most popular.
session_start();

## functions - Private functions only

	## private functions
	#require('../tasty_include/tasty_utilities.inc.php');
	require('../tasty_include/tasty_utilities.inc.php');
	#require('tasty_utilities.inc.php');

	## public funcitons
	include('../tasty_include/tasty_public.inc.php');
		## fetch_bookmarks -- this function will fetch all of the bookmarks and arrange them by recent or most popular in descending order.
		
## classes 

	include("../tasty_include/tag_cloud.inc.php");
	
## host
$db = public_db_connect(); 


## variables
	
	## figure out the current mode
	$bookmark_flag = $_GET['mode'];	## bookmark_flag set to get
	if (!($bookmark_flag)) {	## if bookmark_flag is not set by the get mode than automaticly set to recent	
		$bookmark_flag = "recent";
	}
	
	## set the mode for the gloabl session array
	$_SESSION['navigation'] = $bookmark_flag;
	
	## what is the $page number
	$page = 1;
	if ($_GET['page']) {
		$page = $_GET['page'];
	}
		
#################################
## flag insertion section      ##
## open up if $_GET['spam_id'] ##
if (is_numeric($_GET['spam_id'])) {
 
 	## set the funciton flag_bookmarks ($bookmark_id, $db, $customer_id = '')
	flag_bookmarks ($_GET['spam_id'], $db, $_SESSION['customer_id']);
	
	## set $message
	$message = "You have successfully flagged a bookmark as inappropriate. You will no longer see the bookmark in this list.";
}
## flag insertion section      ##
## open up if $_GET['spam_id'] ##
#################################


############################
## block member insertion ##
if (is_numeric($_GET['block_id'])) {

			## set the function for blocking a member
			blocked_member ($_GET['block_id'], $db, $_SESSION['customer_id']);
			
}
## block member insertion ##
############################


## header
include('../tasty_include/tasty_header.inc');
	


?>
<div class="left_col">
<h2><?php echo $bookmark_flag; ?> recipe bookmarks</h2>
<div class="subscribe"><a class="rss1" href="http://mespinosa.userworld.com/tasty/tasty_rss.php">Subscribe to our RSS Feed!</a></div>
<?

?>
<span style="color:red; font-size:12px;" >
<?
if ($message) {
	echo $message;
}
?>
</span>
<br />
<?
## set $bookmark_array to the function fetch_bookmarks
$bookmark_array = fetch_bookmarks ($bookmark_flag, $db, $page);
## set $array_count to the count of the $bookmark_array
$array_count = count($bookmark_array);

	#################
	## back button ##

	## if $page <= 1
	if ($page <= 1) {
		## print out the back as a string
		echo "&lt;&lt; back |";
	}
	## else
	else {
		## print out back as a button		
		?>
        <a href="index.php?mode=<? echo $bookmark_flag; ?>&page=<? echo ($page - 1); ?>">&lt;&lt; back</a>|
        <?
	}
	## back button ##
	#################
	

	#################
	## next button ##

	## if $array_count < 5
	if ($array_count < 5) {
		## print out next as a string
		echo "next &gt;&gt;";
	}
	## else 
	else {
		## print out next as a button
		?>
        <a href="index.php?mode=<? echo $bookmark_flag; ?>&page=<? echo ($page + 1); ?>">next &gt;&gt;</a>
        <?		
	}
	## next button ##
	#################
	
	
	
?>
<ol>
<?
## use the while each loop to 
while (list($key, $this_bookmark) = each($bookmark_array)) {
?>
<li><a href="<? echo $this_bookmark['url']; ?>">
<? echo $this_bookmark['title']; ?></a><br />

		<span class="info">
		<?
		##############################################################
		## print out either the "# of popular" or the "date posted" ## 
		
		## if mode is posted
		if ($bookmark_flag == "popular") {
		
			## print out the "# of Posts: "
			echo "# of posts: ".$this_bookmark['popularity']." ------- <a href=\"comments.php?profileID=".$this_bookmark['customer_id']."&mode=".$bookmark_flag."&page=".$page."&bookmark_id=".$this_bookmark['bookmark_id']."&title=".$this_bookmark['title']."\">Comments</a>";
		}
		## else
		else {
			## prin out the "date posted: "
			echo "date posted ".date("M j, Y, g:i a", $this_bookmark['date_posted'])." ------- <a href=\"profile.php?profileID=".$this_bookmark['customer_id']."&title=".$this_bookmark['title']."\">posted by:".$this_bookmark['login']."</a>";
		}
		## print out either the "# of posted" or the "date posted" ## 
		#############################################################
		?>
		</span><br />

<? echo $this_bookmark['notes']; ?><br />

<!-- start flag and save button -->

    <!-- Flag As Innapropriate button -->
    <span class="move_over"><a class="grey_font" href="index.php?spam_id=<? echo $this_bookmark['bookmark_id']; ?>">Flag As Innapropriate</a></span>
    
	<!-- Block a member -->
    <span class="move_over"><a class="grey_font" href="index.php?block_id=<? echo $this_bookmark['customer_id']; ?>">Block This Member</a></span>
    
    
    <?
	###########################################################
	## save button --- only show up if a member is logged in ##
	if (!($this_bookmark['customer_id'] == $customer_id) && (is_numeric($customer_id))) {
	?>
    <span class="move_over2_save"><a class="grey_font" href="bookmark.php?save_id=<? echo $this_bookmark['bookmark_id']; ?>">Save</a></span>
    <?
	}
	## else
	else if (is_numeric($customer_id)) {
	?>
    <span class="move_over3_save"><div class="grey_font_light" >Save</div></span>
    <?
	}
	## save button --- only show up if a member is logged in ##
	###########################################################
	?>
    
        
<!-- end flag and save button -->


<br />

<hr />
<?
}
## if $array_count is set to zero than print out "No bookmarks yet."
if ($array_count == 0) {
	echo "No bookmarks yet.";
}
?>
</ol>
</div>

<div class="right_col">
 <div class="box">
  <div class="top">
   <div class="inside">
        <div class="title">
         <div class="left">
         <?
		  ####################################################
		  ## entire show as list or show as a cloud section ##
		  if ($_GET['show'] == 'list') {
		 ?>
         	<!-- show as cloud -->
          	tags - <a class="show_as_button" href="index.php?show=cloud"><font color="white">( Show as Cloud )</font></a>
         <?
		 }
		 else {
		 ?>
            <!-- show as list -->
          	tags - <a  class="show_as_button" href="index.php?show=list"><font color="white"> ( Show as List )</font></a>
         <?
		 }
		  ## entire show as list or show as a cloud section ##		 
		  ####################################################
		 ?>
         </div>
         <div class="clear_both"></div>
        </div>
	
   </div>
  </div>
  <div class="bot">
  
  
  
   <div class="inside"> 
<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/tag_styles.css" />
<?
if ($_SESSION['customer_id']) {
	$bookmark_array = fetch_bookmark_tags ($_SESSION['customer_id'], $db, '','show_all'); 	// set array for all existing bookmark_tags
}
else {
	$bookmark_array = fetch_bookmark_tags (16, $db, '','show_all'); 	// set array for all existing bookmark_tags
}
$cloud = new wordCloud($bookmark_array, $_GET['show']);										// instantiatiate $cloud to wordCloud
	echo $cloud->showCloud();																// print out all of the words for the cloud using the function showCloud()
?>
   </div>
  </div>
 </div>
</div>
<div class="clear_both"></div>

<?php
## footer
include("../tasty_include/tasty_footer.inc");
?>