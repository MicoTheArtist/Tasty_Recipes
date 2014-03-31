<?php
## tasty_utilities.inc.php -- this is a private library.

## functions

	## member_db_connect () -- this is the database handle that allows for select, insert and update for all tables except the customer_login table
	## Because this allows for the insertion and update of the tables, it is safer not to make this a public function to prevent an anonomous person
	## from courrupting the table with min information.
	function member_db_connect() {
	
			$host = "localhost:/users/mespinosa/mysql-php/data/mysql.sock";
			$user = "tasty_member";
			$pw = "trish123";
			$database = "tasty_recipes";
			
			$db = mysql_connect($host, $user, $pw)
					or die ("Cannot connect to MySQL");
			mysql_select_db($database, $db)
					or die ("Cannot connect to Database");
					
			return $db;					
						
	}												
						
	## login_db_connect () -- this is the database handle that allows for the select, insert, and update on customer_logins and tasty_network only.
	## Because this allows for the insertion and update of the tables, it is safer not to make this a public function to prevent an anonomous person
	## from courrupting the table with min information.	
	function login_db_connect() {
			
			$host = "localhost:/users/mespinosa/mysql-php/data/mysql.sock";
			$user = "tasty_login";
			$pw = "scott123";
			$database = "tasty_recipes";
			
			$db = mysql_connect($host,$user,$pw)
					or die ("Cannot connect to MySQL");
			mysql_select_db ($database, $db)
					or die ("Cannot connect to Database");		
					
			return $db;					
					
	}
	
	
	## fetch_login_members -- this function will fetch the profile for all of the members that are linked to the logged in member
	## Because this function is designed to check for a members login and password, it is better not to make it a public function,
	## to prevent an anonomous person from looping throught the customer_login table, just to see if a member exists within t.as.ty recipes.
	function fetch_login_member ($profileID, $db) {	
		$login_member_array = array();
		if (is_numeric($profileID)) {
			$command = "select cl.login,
							   tn.req_member,
							   tn.app_member
						from customer_logins cl, tasty_network tn
						where cl.date_deactivated <= 0
						and tn.date_deleted <= 0
						and (tn.app_member = cl.customer_id and tn.req_member = ".addslashes($profileID).");";
			$result = mysql_query($command);
			while ($this_login_array = mysql_fetch_assoc($result)) {
				array_push($login_member_array, $this_login_array);
			}
		}
		return $login_member_array;
	}
	
	## fetch_customer_bookmarks -- This function will fetch both all of the customers or just an individual customer bookmark
	function fetch_customer_bookmarks ($profileID, $db, $page, $bookmark_id = '') {
	
		## set bookmark_array
		$bookmark_array = array();
		## if $profileID is set
		if (is_numeric($profileID)) {
			## customer -- list out all of the customer bookmarks
			$command = "select cb.customer_id,
							   cb.bookmark_id, 
							   cb.title, 
							   cb.notes, 
							   unix_timestamp(cb.date_posted) as date_posted, 
							   bu.url
							   		from customer_bookmarks cb, bookmark_urls bu 
							   		where cb.bookmark_id = bu.bookmark_id 
									and cb.date_deleted <= 0 
									and cb.customer_id = ".addslashes($profileID)." ";
			## if $bookmark_id is present					
			if (is_numeric($bookmark_id)) {
				## command --- add the sql for select a single bookmark " and bookmark_id = 11
				$command .= " and cb.bookmark_id = ".addslashes($bookmark_id)." ";
			}			
			## if $page is not numeric or if $page < 1
			if (!(is_numeric($page)) || ($page < 1)) {
				## set $page to 1
				$page = 1;
			}
			## command --- add the sql "limit a,b" where a = ($page - 1) * 5; b = 5
			$command .= " order by date_posted desc limit ".(($page -1) * 5).", 5";
			## result
			$result = mysql_query($command, $db);
			## while, mysql_fetch_assoc() loop
			while ($this_bookmark_array = mysql_fetch_assoc($result)) {
			
				## if the $bookmark_id is not already set by $customer_id 
				
				
					## array_push
					array_push($bookmark_array, $this_bookmark_array);
			}		
		}
		return $bookmark_array;
	}
	
	
	## delete_customer_bookmark -- this function will delete bookmarks from the members own profile
	function delete_customer_bookmark ($customer_id, $db, $bookmark_id) {
	
		if (is_numeric($customer_id) && (is_numeric($bookmark_id))) {
					
			$command = "update customer_bookmarks set date_deleted = now() where customer_id = ".addslashes($customer_id)." and bookmark_id = ".addslashes($bookmark_id).";";			
			$result = mysql_query($command, $db);
			
		}				
	}
	
	
	## fetch_profile -- this function will return a one dimensional array containing the member in questions profile
	function fetch_profile ($profileID, $db) {  
		$profile_array = array();
		if (is_numeric($profileID)) {
			$command = "select ci.email, 
							   ci.name, 
							   ci.homepage, 
							   cl.login, 
							   unix_timestamp(ci.date_enrolled) as date_enrolled 
							   from customer_info ci, customer_logins cl 
							   where ci.customer_id = cl.customer_id 
							   and cl.date_deactivated <= 0 
							   and ci.customer_id = ".addslashes($profileID).";";
			$result = mysql_query($command, $db);
			if ($result && mysql_num_rows($result) > 0) {
				$profile_array = mysql_fetch_assoc($result);
			}
		}
		return $profile_array;
	}
	

	## fetch_bookmark_tags -- either retrieves all of the tags for a customer or just the tags for a customers specific bookmark_id
	function fetch_bookmark_tags ($profileID, $db, $bookmark_id = '', $showAll = '') { 
	
		$tag_array = array();														// set the initial array
		
		if ($profileID) {
		
			// command - select all of the bookmark_tags that for a customer
			$command = "select bt.tag_id, tt.tag 
						from bookmark_tags bt, tasty_tags tt 
						where bt.date_deleted <= 0 
						and bt.tag_id = tt.tag_id ";
						
			if ($showAll == '') {												// if $showAll is false than we only want to show tags for a specific customer
			
				// command --- add this if we want to show tags only for a specifci customer
				$command .= "and bt.customer_id = '".addslashes($profileID)."' ";		
			}
			if (is_numeric($bookmark_id)) {
			
				// command - than it is necessary to look for just the tags the correlate to a customers_specific bookmark_id
				$command .= " and bt.bookmark_id = '".addslashes($bookmark_id)."';";
			}								
			$result = mysql_query($command, $db);  
			while ($this_tag_array = mysql_fetch_assoc($result)) {					// loop through all of the result ans set each result as an array for $this_tag_array
			
				array_push($tag_array, $this_tag_array['tag']);						// push each new array onto $tag_array 
			}									
		}
		return $tag_array;
	}
	
	
	

							
?>
