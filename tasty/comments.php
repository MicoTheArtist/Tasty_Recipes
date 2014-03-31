<?
## comments -- this page prints out all comments pertaining to each bookmark.
session_start();


## functions
	
	// private functions
	require('../tasty_include/tasty_utilities.inc.php');
	
	
	// public functions
	include('../tasty_include/tasty_public.inc.php');

		// Note: fetch_bookmark_comments ($bookmark_id, $db);  -- This function retrievs all of the bookmark comments for a specific bookmark_id and orders them by descending date.
	

## variables
	
	$bookmark_id = $_GET['bookmark_id'];
	$page = $_GET['page'];	// page	
	$mode = $_GET['mode'];	// mode (recent or post)
	

## hosting

	// use the tasty_member connection from the public functions library.
	$db = public_db_connect();


## validate 

	// if bookmark_id is not numeric
	if (!(is_numeric($bookmark_id))) {
		// use the header function to redirect the user to the index.php page
		header("location:index.php");
	}
?>

<?
## header
include('../tasty_include/tasty_header.inc');
?>

<h2>title of the bookmark</h2>
<h4><a href="index.php?mode=<? echo $mode; ?>&page=<? echo $page; ?>">&lt;&lt; back</a></h4>
<table>
	<tr>
    	<td>
        <?
		## fetch_bookmark_comments ($bookmark_id, $db); -- use this function to figure out the array for $bookmark_comments_array = 
		$fetch_bookmark_array = fetch_bookmark_comments ($bookmark_id, $db);
		## figure out the $array_count -- this will be a trigger to tell the page to print out that their are no bookmarks.
		$array_count = ($fetch_bookmark_array);
        ## use the while each loop on $bookmark_comments to loop out the different comments available
		while (list($key, $these_notes) = each($fetch_bookmark_array)) {
		?>
        
            <!-- ################### -->
            <!-- start out container -->
            <div class="">
            
                            <!-- body copy -->
                            <div class="comment_cleaer_container">
                                <? echo $these_notes['notes']; ?>
                            </div>
                            
                    <!-- login name of the person giving the comment -->        
                    <div class="bookmark_comment_by">Comment by, <? echo $these_notes['login']; ?></div>
                        <?  ## set up the date for this  ?>
                        <div class="date_style">posted: <? echo date("M j, Y, g:i a", $these_notes['date_posted']); ?></div>
                            
            </div>                        
            <!-- end out container -->
            <!-- ################# -->
        
        <?
		}
		## if $array_count is zero
		if ($array_count == 0) {
			## print out that their are no comments for this bookmark at this time.
			echo "No Comments Yet.";
		}
		?>
        </td>
	</tr>    
</table>


<?
## footer
include('../tasty_include/tasty_footer.inc');
?>

</body>
</html>
