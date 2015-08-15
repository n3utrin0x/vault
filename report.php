<?php
/* initilization */
include_once('init.php');
$output = "";
$data = dbcon();
/* check if this is an activity audit or change list audit */
/* an activity audit queries all the "votes" entries regarding a certain change */
/* a change list audit queries all the displayed "changes" entries */
/* this can be determined by checking the page on which report was called */
$referrer = preg_replace("/\?.+/", "", basename($_SERVER['HTTP_REFERER']));
$commaExceptions = ['title', 'description', 'what', 'who', 'backout', 'postmodification'];
$output = "'ID,Summary,Service,Devices,Risk,Incident Type,Starting Time,Ending Time,Change Description,Impact on What,Impcat on Who,Backout Process,Post Modification Process\r\n";

if($referrer == "change.php")
{
	$changeid = intval(preg_replace("/.+\?id=/", "", basename($_SERVER['HTTP_REFERER'])));
	$sql = "SELECT id,title,service,(SELECT GROUP_CONCAT(server SEPARATOR ' ') FROM servers WHERE changeid=changes.id) as servers,risk,incident,start,end,description,what,who,backout,postmodification FROM changes WHERE id=?";
	$query = $data->prepare($sql);
	$query->execute([$changeid]);
	$row = $query->fetch();
	foreach($commaExceptions as $c)
	{
		$row[$c] = str_replace(",", ";", $row[$c]);
	}
	$output .= "{$row['id']},{$row['title']},{$row['service']},{$row['servers']}," . toRisk($row['risk'], false) . "," . toIncident($row['incident']) . "," . date("l jS \of F Y h:i A", $row['start']) . "," . date("l jS \of F Y h:i A", $row['end']) . ",{$row['description']},{$row['what']},{$row['who']},{$row['backout']},{$row['postmodification']}";
	$output .= "\r\nApproval Activity,,,,,,,,,,,,\r\nUser,Activity,Comment,Time,,,,,,,,,\r\n";
	$query = $data->prepare("SELECT * FROM votes WHERE changeid=?");
	$query->execute([$changeid]);
	while($row = $query->fetch())
	{
		$row['comment'] = str_replace(",", ";", $row['comment']);
		$output .= "{$row['user']}," . activity($row['vote']) . ",{$row['comment']}," . date("l jS \of F Y h:i:s A", $row['time']) . ",,,,,,,,,\r\n";
	}
}
else
{
	$sql = "SELECT id,title,service,(SELECT GROUP_CONCAT(server SEPARATOR ' ') FROM servers WHERE changeid=changes.id) as servers,risk,incident,start,end,description,what,who,backout,postmodification FROM changes";

	if(isset($_GET['q']) && $_GET['q'] != "")
	{
		$sql .= " AND (title LIKE ? OR description LIKE ?)";
		$args[$c++] = "{$_GET['q']}";
		$args[$c++] = "{$_GET['q']}";
	}

	if(isset($_GET['start']) && $_GET['start'] != "") 
	{
		$sql .= " AND start=?";
		$args[$c++] = "{$_GET['start']}";
	}

	if(isset($_GET['end']) && $_GET['end'] != "") 
	{
		$sql .= " AND end=?";
		$args[$c++] = "{$_GET['end']}";
	}

	if(isset($_GET['risk']) && $_GET['risk'] != "") 
	{
		$sql .= " AND risk=?";
		$args[$c++] = "{$_GET['risk']}";
	}

	if(isset($_GET['service']) && $_GET['service'] != "") 
	{
		$sql .= " AND service=?";
		$args[$c++] = "{$_GET['service']}";
	}

	$query = $data->prepare($sql);
	$query->execute([]);
	while($row = $query->fetch())
	{
		foreach($commaExceptions as $c)
		{
			$row[$c] = str_replace(",", ";", $row[$c]);
		}
		$output .= "{$row['id']},{$row['title']},{$row['service']},{$row['servers']}," . toRisk($row['risk'], false) . "," . toIncident($row['incident']) . "," . date("l jS \of F Y h:i A", $row['start']) . "," . date("l jS \of F Y h:i A", $row['end']) . ",{$row['description']},{$row['what']},{$row['who']},{$row['backout']},{$row['postmodification']}";
	}
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="sysmod.csv"');
header('Content-Length: ' . strlen($output));
echo $output;

?>