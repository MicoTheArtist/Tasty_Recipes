<?
$command = "select count(bookmark_id) as bookmark_count from customer_bookmarks where date_deleted <= 0 and  customer_id = ".$profileID;			
echo $command;
?>