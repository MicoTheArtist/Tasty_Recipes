<?php
## tag_bookmarks.php -- prints out all of the bookmarks that have tag's that match the passed $_GET['tag'].
session_start();

## functions - Private functions only

	// private functions
	require('../tasty_include/tasty_utilities.inc.php');
	
	// public funcitons
	include('../tasty_include/tasty_public.inc.php');
		// fetch_bookmarks -- this function will fetch all of the bookmarks and arrange them by recent or most popular in descending order.
		
## Classes

	include('../tasty_include/tag_cloud.inc.php');

	
## host
$db = public_db_connect(); 


## variables
	
	## figure out the current mode
	$bookmark_flag = $_GET['mode'];											// bookmark_flag set to get
	if (!($bookmark_flag)) {												// if bookmark_flag is not set by the get mode than automaticly set to recent	
		$bookmark_flag = "recent";
	}
	
	## set the mode for the gloabl session array
	$_SESSION['navigation'] = $bookmark_flag;
	
	## what is the $page number
	$page = 1;
	if ($_GET['page']) {
		$page = $_GET['page'];
	}


############################
## block member insertion ##
if (is_numeric($_GET['block_id'])) {
			
	blocked_member ($_GET['block_id'], $db, $_SESSION['customer_id']);		// set the function for blocking a member
			
}
## block member insertion ##
############################


## header
include('../tasty_include/tasty_header.inc');
	


?>
<h2><?php echo $bookmark_flag; ?> recipe bookmarks</h2>
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
############################################################################
## Entire section for preparing the correct bookmark_tag for printing out ##
	
	$tag_bookmark_array = array();
	$bookmark_array = array();																					// set the initial array

	// command -- select all of the tags where the tag matches the $_GET['tag'];
	$command = "select bt.tag_id, bt.bookmark_id, bt.customer_id, tt.tag 
			from bookmark_tags bt, tasty_tags tt 
			where bt.tag_id = tt.tag_id 
			and bt.date_deleted <= 0 
			and tag = '".$_GET['tag']."' ";			
	$result = mysql_query($command);
	
	while ($this_tag_bookmark_array = mysql_fetch_assoc($result)) {												// use the while and create one dimensional arrays for $this_tag_bookmark_array
	
		array_push($tag_bookmark_array, $this_tag_bookmark_array['bookmark_id']);								// push the $tag_bookmark_array with $this_tag_bookmark_array to add on all of the array values
	}
	
	for ($i=0;$i < count($tag_bookmark_array); $i++) {															// for --- loop through the array count
			
			$bookmark_array[$i] = fetch_bookmarks ($bookmark_flag, $db, $page,'', $tag_bookmark_array[$i]);		// set $bookmark_array to the function fetch_bookmarks to create an array of all of the bookma
					
			
	}
	
	
			$array_count = count($bookmark_array);																// set $array_count to the count of the $bookmark_array
	

## Entire section for preparting the correct bookmark_tag for printing out ##
#############################################################################

	#################
	## back button ##

	
	if ($page <= 1) {													// if $page <= 1
		
		echo "&lt;&lt; back |";											// print out the back as a string
	}
	else {
		// print out back as a button		
		?>
        <a href="tag_bookmarks.php?mode=<? echo $bookmark_flag; ?>&page=<? echo ($page - 1); ?>">&lt;&lt; back</a>|
        <?
	}
	## back button ##
	#################
	

	#################
	## next button ##
	
	if ($array_count < 5) {												// if $array_count < 5
		
		echo "next &gt;&gt;";											// print out next as a string
	}
	else {
		// print out next as a button
		?>
        <a href="tag_bookmarks.php?mode=<? echo $bookmark_flag; ?>&page=<? echo ($page + 1); ?>">next &gt;&gt;</a>
        <?		
	}
	## next button ##
	#################
	
	
	
?>
<ol>
<?
for ($i=0; $i < count($bookmark_array); $i++) {							// loop through all of the values of the $bookmark_array
?>
<li><a href="<? echo $bookmark_array[$i][0]['url']; ?>">
<? echo $bookmark_array[$i][0]['title']; ?></a><br />

		<span class="info">
		<?
		##############################################################
		## print out either the "# of popular" or the "date posted" ## 
		
		## if mode is posted
		if ($bookmark_flag == "popular") {
		
			## print out the "# of Posts: "
			echo "# of posts: ".$bookmark_array[$i][0]['popularity']." ------- <a href=\"comments.php?profileID=".$bookmark_array[$i][0]['customer_id']."&mode=".$bookmark_flag."&page=".$page."&bookmark_id=".$bookmark_array[$i][0]['bookmark_id']."&title=".$bookmark_array[$i][0]['title']."\">Comments</a>";
		}
		## else
		else {
			## prin out the "date posted: "
			echo "date posted ".date("M j, Y, g:i a", $bookmark_array[$i][0]['date_posted'])." ------- <a href=\"profile.php?profileID=".$bookmark_array[$i][0]['customer_id']."&title=".$bookmark_array[$i][0]['title']."\">posted by:".$bookmark_array[$i][0]['login']."</a>";
		}
		## print out either the "# of posted" or the "date posted" ## 
		#############################################################
		?>
		</span><br />

<? echo $bookmark_array[$i][0]['notes']; ?><br />


<br />

<hr />
<?
}

if ($array_count == 0) {												// if $array_count is set to zero than print out "No bookmarks yet."
	echo "No bookmarks yet.";
}
?>
</ol>

<?php
## footer
include("../tasty_include/tasty_footer.inc");

/*
echo "//////";
echo "<pre>";
print_r($tag_bookmark_array);
echo "</pre>";
echo "//////";

echo "//////";
echo "<pre>";
print_r($bookmark_array);
echo "</pre>";
echo "//////<br>";
*/

?>