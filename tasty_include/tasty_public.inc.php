<?php
## tasty_public.inc.php -- this is a public library

## functions

	## It is ok to make these function public, because it is responsible only for listing information using the select command. Theirfore
	## their is not a high risk of having an anonymous person corrupting any of the database tables below.


	## public_db_connect() -- this is the database handle which allows for only the select values to be uesd only for customer_bookmarks and bookmark_urls
	## It is ok to make this database handle function public because it will not allow for and insetion, deletion or updating of the table values and is theirfor considered safe.
	function public_db_connect() {
		
		$host = "localhost:/users/mespinosa/mysql-php/data/mysql.sock";
		$user = "tasty_public";
		$pw = "public123";
		$database = "tasty_recipes";
		
		$db = mysql_connect($host, $user, $pw)
				or die ("Cannot connect to MySQL");
		mysql_select_db($database, $db)
				or die ("Cannot connect to Database");
				
		return $db;				
						

	}
	
	## fetch_bookmarks -- this function will fetch all of the bookmarks and arrange them by recent or most popular in descending order.
	## --- this bookmark will also make sure not to print out bookmarks a user marked as flagged and bookmarks that have been flagged 5 times.	
	function fetch_bookmarks ($flag, $db, $page, $customer_id = '', $bookmark_id = '') {
		
		## set bookmark_array
		$bookmark_array = array();
		
		## set ip_address
		$ip_address = getenv("REMOTE_ADDR");
		
		## command --- list out all of the customer bookmarks except for those flagged by the ip address and the member	
		$command = "select cb.customer_id,					
						   cb.bookmark_id, 
						   cb.title, 
						   cb.notes, 
						   unix_timestamp(cb.date_posted) as date_posted, 
						   cl.login, 
						   bu.url,
						   count(bu.url) as popularity,
						   count(fb.bookmark_id) as flag_count,
						   count(bm.blocked_customer_id) as block_count
						   
						   		from customer_bookmarks cb
								
								inner join bookmark_urls bu
								on cb.bookmark_id = bu.bookmark_id
								
								inner join customer_logins cl
								on cb.customer_id = cl.customer_id
								
								left outer join flagged_bookmarks fb
								on bu.bookmark_id = fb.bookmark_id
								
								left outer join block_member bm
								on fb.customer_id = bm.blocked_customer_id							
																							   
							   where (fb.bookmark_id is null or !(fb.customer_id = '".addslashes($customer_id)."' or fb.ip_address = '".addslashes($ip_address)."'))
							   and (bm.blocked_customer_id is null or !(bm.blocking_customer_id = '".addslashes($customer_id)."' or bm.ip_address = '".addslashes($ip_address)."')) ";
						   
		
				
		
		## if $bookmark_id is set								
		if (intval($bookmark_id) > 0) {
			$command .= " and cb.bookmark_id = ".$bookmark_id." ";
		}
		
		## add sql group by cb.bookmark_id no matter what
		$command .= " group by cb.bookmark_id ";																						
		
		## if flag is popular
		if ($flag == "popular") {
			$command .= " order by popularity desc ";
		}
		## if flag is recent
		else if ($flag == "recent") {
			$command .= " order by date_posted desc";
		}
		## if $page is not numeric or if $page < 1
		if (!(is_numeric($page)) || ($page < 1)) {
			## page = 1
			$page = 1;
		}
		## command --- add the sql " limit a,b" where a = ($page - 1) * 5; b = 5
		$command .= " limit ".(($page - 1) * 5).",5 ";			
		# result		
		$result = mysql_query($command);
		## while, mysql_fetch_assoc() loop
		while ($this_bookmark_array = mysql_fetch_assoc($result)) {
			## if flag_count < 5
			# if (($this_bookmark_array['flag_count'] < 5) && ($this_bookmark_array['block_count'] < 5)) {
			if ($this_bookmark_array['block_count'] < 5) {
				## array_push
				array_push ($bookmark_array, $this_bookmark_array);
			}				
		}
		return $bookmark_array;							
	}
	
	## flag_bookmarks -- this function will first check to see if a bookmark_url has already been inserted for this member or ip_address
	## --- and if their hasn't been a flag inserted than the flag will be inserted.
	function flag_bookmarks ($bookmark_id, $db, $customer_id = '') {
	
		## set ip_address
		$ip_address = getenv("REMOTE_ADDR");
		## if $bookmark_id is numeric
		if (is_numeric($bookmark_id)) {
			## command -- see if flag exists for the bookmark_id and the ip_addres
			$command = "select bookmark_id from flagged_bookmarks where bookmark_id = '".addslashes($bookmark_id)."' and (ip_address = '".addslashes($ip_address)."' ";
			## if $customer_id is numeric
			if (is_numeric($customer_id)) {
				## command -- add the sql " and customer_id = 11"
				$command .= " or bookmark_id = '".addslashes($bookmark_id)."'";
			}
			## command --- add the end cap
			$command .= ");";
			## result
			$result = mysql_query($command);
			## if their is not result
			if (!($data = mysql_fetch_object($result))) {
				## command --- insert the new flag
				$command = "insert into flagged_bookmarks (bookmark_id, customer_id, ip_address, date_flagged)
								values ('".addslashes($bookmark_id)."','".addslashes($customer_id)."','".addslashes($ip_address)."',now())";
				## result
				$result = mysql_query($command);
			}
		}		
	}
	
	
	## block_member -- this function will insert the variables for blocking a member from the public_proifle page.
	## --- as long as teh block does not already exist for this customer_id or ip_address.
	function blocked_member ($blocked_customer_id, $db, $blocking_customer_id = '') {			
		
		## ip_address
		$ip_address = getenv("REMOTE_ADDR");
		## if $blocked_customer_id is numeric
		if ($blocked_customer_id) {
			## command -- look for an existing blocked_customer_id using the ip_address
			$command = "select blocked_customer_id from block_member where blocked_customer_id = '".addslashes($blocked_customer_id)."' and (ip_address = '".addslashes($ip_address)."' ";
			## if $blocking_customer_id exists
			if (is_numeric($blocking_customer_id)) {
				## command --- add " and $blocking_customer_id = 11"
				$command .= " and blocking_customer_id = '".addslashes($blocking_customer_id)."' ";
			}
			## command -- add the end cap
			$command .= ");";			
			## result
			$result = mysql_query($command);
			## if their is no result
			if (!($data = mysql_fetch_object($result))) {
				## command -- insert the new block into the block_member table
				$command = "insert into block_member (blocked_customer_id, blocking_customer_id, ip_address, date_blocked) values ('".addslashes($blocked_customer_id)."', '".addslashes($blocking_customer_id)."', '".addslashes($ip_address)."', now());";
				## result
				$result = mysql_query($command);
			}		
		}
	}
	
	
	## fetch_bookmark_comments -- This function retrievs all of the bookmark comments for a specific bookmark_id and orders them by descending date.
	function fetch_bookmark_comments ($bookmark_id, $db) {
		$bookmark_comments_array = array();
		$command = "select cb.bookmark_id, 
						   cb.title, 
						   cb.notes, 
						   cb.date_posted, 
						   bu.url, 
						   cl.login 
						   from customer_bookmarks cb, bookmark_urls bu, customer_logins cl 
						   where cb.bookmark_id = bu.bookmark_id 
						   and cb.customer_id = cl.customer_id 
						   and cb.date_deleted <= 0 
						   and cl.date_deactivated <=0 
						   and cb.bookmark_id = ".addslashes($bookmark_id)." 
						   order by date_posted ;";
		$result = mysql_query($command, $db);
		while ($this_comment = mysql_fetch_assoc($result)) {
			array_push($bookmark_comments_array, $this_comment);
		}						   
		return $bookmark_comments_array;
	}
	
	

	## valid_input function for checking if a value works when compared to a regular function
	function valid_input ($myinput, $good_input) {
	
		if (preg_match("/$good_input/",$myinput)) {
		
			return true;
		}
		else {
		
			return false;
		}

	}
	
	## valid_url function for checking if a value works when compared to a regular function
	function valid_url ($myinput, $good_input) {
	
		if (preg_match("$good_input",$myinput)) {
		
			return true;
		}
		else {
		
			return false;
		}

	}

	
	## fetch_members_bookmarks -- this function will fetch all of the bookmarks that correspond to the indivual profiles that the logged in member has requested bookmarks from
	function fetch_members_bookmarks ($profileID, $db) {
		$members_book_array = array();
		if (is_numeric($profileID)) {
			$command = "select cl.login,
							   cb.customer_id,
							   cb.bookmark_id,
							   cb.title,
							   cb.notes,
							   bu.url,
							   tn.req_member,
							   tn.app_member
						from customer_logins cl, customer_bookmarks cb, bookmark_urls bu, tasty_network tn
						where cl.customer_id = cb.customer_id
						and cb.bookmark_id = bu.bookmark_id
						and (tn.app_member = cl.customer_id and tn.req_member = ".addslashes($profileID).");";
			$result = mysql_query($command);
			while ($this_members_book = mysql_fetch_assoc($result)) {
				array_push($members_book_array, $this_members_book);
			}						
		}
		return $members_book_array;						
	}
	
	
	## if_member_has_bookmark --- this function checks to see if a visitor or the profile page already has a bookmark or not.
	function if_member_has_bookmark ($customer_id, $db, $bookmark_id) {
	
		$command = "select bookmark_id from customer_bookmarks where customer_id = ".addslashes($customer_id)." and bookmark_id = ".addslashes($bookmark_id).";";	
		$result = mysql_query($command);
		if ($data = mysql_fetch_object($result)) {
			$member_has_bookmark = true;
		}
		else {
			$member_has_bookmark = false;
		}
		return $member_has_bookmark;		
	}						
	

	
	## fecth_members_profile -- this function will fetch the profile for the logged in member.
	function fetch_members_profile ($profileID, $db) {		
		$member_profile = array();
		if (is_numeric($profileID)) {
			$command = "select ci.email, 
							   ci.name, 
							   ci.homepage, 
							   cl.login
						from customer_logins cl, customer_info ci						
						where ci.customer_id = cl.customer_id 
						and ci.customer_id = 15;";
			$result = mysql_query($command);
			if ($result && (mysql_num_rows($result))) {
				$member_profile = mysql_fetch_assoc($result);
			}
		}
		return $member_profile;
	}
	
	
	## fetch_total_profile_bookmarks -- this function fetches the total number of bookmarks a member has on his/her profile.
	function fetch_total_profile_bookmarks ($profileID, $db) {
		if (is_numeric($profileID)) {
			$command = "select count(bookmark_id) as bookmark_count from customer_bookmarks where date_deleted <= 0 and  customer_id = ".$profileID.";";
			//echo "<br>".$command."<br>";
			$result = mysql_query($command, $db);
			//mysql_query($result);
			if ($data = mysql_fetch_object($result)) {
				$bookmark_count = $data->bookmark_count;
				$page_count = ceil($bookmark_count/ 5);
			}
		}
		return $page_count;
	}
	
	
		
	
?>