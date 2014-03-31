<?
session_start();

## variables
	
	## figure out the mode
	$navigation_flag = $_SESSION['navigation'];
	
	## figure out who is logged in
	$customer_id = $_SESSION['customer_id'];
	$customer_login = $_SESSION['customer_login'];
	
	## what customer is this page in reference to	
	$profileID = "";
	if (is_numeric($_GET['profileID'])) {
		$profileID = intval($_GET['profileID']);
	}
			
	
	## what is the name of the page the member is currently on.
	$page_flag = "my recipes";
	if ($_GET['page_name']) {
		$page_flag = $_GET['page_name'];
	}		


## functions

	## print_navigation -- this function will decide weather to print out the mode as either a string or a button
	## NOTE: the active variables that will be input into the function will be --- $nav_flag, $nav_title, $nav_url, $limti = false
	function print_navigation ($nav_flag, $nav_title, $nav_url, $last = false) {
		## $good_url regular expression
		$good_url = "[a-z]+.php(\?[a-z]+=[a-zA-Z0-9%]+)?";
		## validate the url $good_url
		if (preg_match("/$good_url/", $nav_url)) {
			## $class_name -- set the default fo $class_name to "nav"
			$class_name = "nav";
			## if $limit is true
			if ($limit) {
				## set $class_name to "limit"
				$class_name = "limit";
			}
			## if $nav_flag == $nav_title
			if ($nav_flag == $nav_title) {
				## print out the mode ($nav_title) as a string
				?>
                <b class="<? echo $class_name; ?>"><? echo $nav_title; ?></b>
                <?
			}
			## else
			else {						
				## print out the mode ($nav_title) as a button keeping in mind that the url will utilize the mode value
				?>
                <a class="<? echo $class_name; ?>" href="<? echo $nav_url; ?>"><? echo $nav_title; ?></a>
                <?
			}
		}
	}
	
	## join_or_profile_navi -- when a member is logged in the navigation in the header under the logo will switch from a join navigation to a profile navigation.
	function join_or_profile_navi ($customer_id, $profileID, $page_flag, $page_name, $nav_url, $last = false) {
	
		$good_page = "[A-Za-z0-9]";
		
		if (is_numeric($customer_id) && (preg_match("/$good_page/", $page_flag))) {
			
			$class_name = "nav";
			if ($last) {
				$class_name = "last";
			}
			if (($page_flag == $page_name) && ($customer_id == $profileID)) {
				?>
                	<!-- this button appears when we are logged in and the $page_name matches  -->
                    <b class="<? echo $class_name; ?>"><? echo $page_name; ?></b>
                <?
			}
			else {
				?>
                	<!-- this button appears when we are logged in and we are not on the page -->
                    <a class="<? echo $class_name; ?>" href="<? echo $nav_url; ?>"><? echo $page_name; ?></a>
                <?
			}

		}
	
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>t.as.ty recipes: social cook-bookmarking</title>

<link type="text/css" rel="stylesheet" rev="stylesheet" href="css/styles.css" />

</head>

<body>

<div id="page">
    
	<div id="tasty_header">
		<div class="top">
			<div class="inside">
				<table style="width:100%;"><tr>
					<td rowspan=2 style="padding-right:10px;width:60px;">
					<img src="images/pie.jpg" height="60" alt="t.as.ty recipes" />
				</td>
				<td style="align:left;">
					<a href="index.php?profileID">t.as.ty recipes</a>
				</td>
				<td align=right valign=top style="padding-right:8px;">
                <?
                ####################################################
				## decide weather to use the login or logout pair ##
				
				## if $customer_id exists
				if ($customer_id) {
					## print out "logged in as John, logout"
					?>
                    <span class="nav">logged in as <b><? echo $customer_login; ?></b></span>
                    <a class="last" href="logout.php">logout</a>
                    <?
				}
				## else 
				else {
					## print out "login, join"					
                ?>
                        <a class="nav" href="login.php">login</a>
                            <a class="last" href="join.php">join</a>	
				<?
				}               
				## decide weather to use the login or logout pair ##
                ####################################################				
				?>                    
				</td>
				</tr>
				<tr><td colspan=2 style="align:left;font-size:20px;">
					social cook-bookmarking
				</td></tr></table>
			</div>
		</div>
		<div class="bot">
			<div class="inside">
				<table style="width:100%;"><tr>
				<td style="font-weight:bold;align:left;">
                
                
                						
                                        <?
                                        ##########################################################################################
										## decide which header navigation to print out useing the join_or_profile_navi function ##
										
										## if a $customer_id is true than a member is logged in
										if ($customer_id) {
											
											## my recipes --- decide weather to print my recipes as a button or a string
											join_or_profile_navi ($customer_id, $profileID, $page_flag, "my recipes", "profile.php?page_name=my recipes&profileID=".$customer_id."");
											## post recipes bookmark --- decide weather to print "post recipes bookmark" as a button or a string
											join_or_profile_navi ($customer_id, $profileID, $page_flag, "post recipes bookmark", "bookmark.php?page_name=post recipes bookmark&profileID=".$customer_id."");
											## view network --- decide weather to print "view network" as a button or a string
											join_or_profile_navi ($customer_id, $profileID, $page_flag, "view network", "network.php?page_name=view network&profileID=".$customer_id."");
											## add to network --- decide weather to print "add to network" as a button or a string
											join_or_profile_navi ($customer_id, $profileID, $page_flag, "add to network", "add.php?page_name=add to network&profileID=".$customer_id."");
											## edit profile -- decide weather to print "edit profile" as a button or a string.
											join_or_profile_navi ($customer_id, $profileID, $page_flag, "edit profile", "edit.php?page_name=edit profile&profileID=".$customer_id."", "");
											## suggestions -- decide weather to print "suggestions" as a button or a string
											join_or_profile_navi ($customer_id, $profileID, $page_flag, "suggestions", "rss_reader.php?page_name=suggestions&profileID=".$customer_id."", true);
										}
										## else -- their is not member logged in
										else {
										?>
                                        	<!-- This navigation appears when a member is not logged in -->
                                        	<a href="join.php">store</a> your recipe bookmarks...<a href="join.php">browse</a> other recipes...<a href="join.php">join</a> today, it's easy!
                                        <?
										}
										## decide which header navigation to print out useing the join_or_profile_navi function ##																			
                                        ##########################################################################################
										?>
                                        
                                </td>
                                <td align=right valign=top>
                                <?
								##############################################################################################################
								## print out the mode (popular or recent) as either a button or a string using the functon print_navigation ##
								
								## if the mode ($navigation_flag) is popular, print out popular as either a button or a string
								print_navigation ($navigation_flag, "popular", "index.php?mode=popular");
								
								## if the mode ($navigation_flag) is recent, print out popular as either a button or a string
								print_navigation ($navigation_flag, "recent", "index.php?mode=recent", true);
								
								## print out the mode (popular or recent) as either a button or a string using the functon print_navigation ##															
								##############################################################################################################
								?>
				</td>
				</tr></table>
			</div>
		</div>
	</div><!-- /header -->
        
    
	<div id="tasty_content">
	
