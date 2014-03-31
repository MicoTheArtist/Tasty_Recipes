<?php
require_once '../../Calendar/Calendar/Month.php';

$Month = new Calendar_Month(2003, 10); // October 2003

$Month->build(); // Build the days in the month

// Loop through the days...
while ($Day = $Month->fetch()) {
    echo $Day->thisDay().'<br />';
}
?> 