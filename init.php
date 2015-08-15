<?php 
if(preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']))
    die("<p>Sorry fellas. Internet Explorer is not supported due to lack of compliance with W3C standards. Chrome or Firefox are advised.</p><p>~ Darren</p>");

error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('America/Chicago');

function dbcon()
{
	include_once('../dbinfo.php');
	
	try {
		return new PDO($dsn, $dbuser, $dbpass);
	} catch (PDOException $e) {
    	echo 'Connection failed: ' . $e->getMessage();
    	exit;
	}
}

function toRisk($risk, $span = true)
{
	if($span)
		if($risk == 0)
			return '<span class="risk green">Low</span>';
		elseif($risk == 1)
			return '<span class="risk yellow">Medium</span>';
		else
			return '<span class="risk red">High</span>';
	else
		if($risk == 0)
			return 'Low';
		elseif($risk == 1)
			return 'Medium';
		else
			return 'High';
}

function statusColor($status)
{
	if($status == 0)
		echo 'yellow';
	elseif($status == 1)
		echo 'blue';
	else
		echo 'green';
}

function commentStatus($status)
{
	if($status == 1) 
		echo "<span class='green'>Approved</span> this change"; 
	elseif($status == 2) 
		echo "<span class='red'>Denied</span> this change"; 
	elseif($status == 3)
		 echo "<span class='orange'>Released</span> this change"; 
	elseif($status == 4)
		 echo "<span class='yellow'>Finished</span> this change"; 
	else
		echo "Commented";
}

function incident($incident)
{
	if($incident == 0)
		echo "Planned: Outage"; 
	elseif($incident == 1) 
		echo "Planned: Non-Disruptive"; 
	elseif($incident == 2) 
		echo "Non-Disruptive"; 
	else
		echo "Emergency";
}

function toIncident($incident)
{
	if($incident == 0)
		return "Planned: Outage"; 
	elseif($incident == 1) 
		return "Planned: Non-Disruptive"; 
	elseif($incident == 2) 
		return "Non-Disruptive"; 
	else
		return "Emergency";	
}

function activity($status)
{
	if($status == 1) 
		return "Approve"; 
	elseif($status == 2) 
		return "Deny"; 
	elseif($status == 3)
		return "Release"; 
	elseif($status == 4)
		return "Complete"; 
	else
		return "Comment";
}


?>