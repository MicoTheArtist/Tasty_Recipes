<?php
## tasty_public.inc -- a public library for t.as.ty recipes

	## -- this is the connection for the public database
	function public_db_connect() {		
		## returns a database handle whcih allows only select on customer_bookmarks and bookmark_urls
		
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
	function fetch_bookmarks ($flag, $db) {
		$bookmark_array = array();
		$command = "select cb.bookmark_id,
						   cb.title,
						   cb.notes,
						   unix_timestamp(min(cb.date_posted)) as date_posted,
						   bu.url,
						   count(cb.customer_id) as popularity
						   from bookmark_urls bu, customer_bookmarks cb
						   where bu.bookmark_id = cb.bookmark_id 
						   group by cb.bookmark_id";
		if ($flag == "popular") {
			$command .= " order by popularity desc";
		}
		else if ($flag == "recent") {
			$command .= " order by date_posted desc;";
		}
		$result = mysql_query($command);
		while ($this_bookmark_array = mysql_fetch_assoc($result)) {
			array_push($bookmark_array, $this_bookmark_array);
		}
		return $bookmark_array;		   
	}
	
	## valid_input function for checking if a value works when compared to a regular function
	function valid_input ($myinput, $good_input) {
	
		if (preg_match("/$good_input/i",$myinput)) {
		
			return true;
		}
		else {
		
			return false;
		}

	}
	
	
	
?>