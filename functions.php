<?php
/* >>> functions.php <<< this file contains all of the server side operations */
/* initialization */
include_once('init.php');
/* call the function whose name was passed via the 'function' query string */
if(isset($_GET['function']))
	call_user_func($_GET['function']);
/* adds a new change entry to the "changes" table */
function add_change()
{
	/* init database connection */
	$pdo = dbcon();
	$sql = "INSERT INTO changes(";
	$args = ['title', 'risk', 'start', 'end', 'service', 'incident', 'user', 'description', 'who', 'what', 'backout', 'postmodification'];
	$argnames = ['Title', 'Risk', 'Start Time', 'End Time', 'Service', 'Incident', 'User', 'Description', 'Impact on Who', 'Impact on What', 'Backout Process', 'Post Modification Process'];
	/* verify the args above and build the sql query with them */
	for($x = 0; $x < sizeof($args); $x++)
	{
		$arg = $args[$x];
		if(isset($_POST[$arg]))
		{
			if($_POST[$arg] == '')
				die($argnames[$x] . " is required.");
			elseif($arg == 'end' || $arg == 'start')
				$args[$x] = strtotime($_POST[$arg]);
			else
				$args[$x] = $_POST[$arg];
			$sql .= "$arg,";
		}
		else 
		{
			die($argnames[$x] . " is required.");
		}
	}
	if($args[2] > $args[3])
		die("Starting time must be before ending time.");
	if($args[2] < time() || $args[3] < time())
		die("Change can't be scheduled before today.");
	/* verify server. it's stored in another database and is not part of the initial query so can't be verified like the rest. */
	if(isset($_POST['server'])) {
		if($_POST['server'] == "")
			die("Server is required.");
	}
	else {
		die("Server is required.");	
	}

	/* trim the last comma */
	$sql = substr($sql, 0, -1);
	/* finish up query */
	$sql .= ") VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
	/* execute query */
	$query = $pdo->prepare($sql);
	$query->execute($args);
	/* add the servers */
	$changeid = $pdo->lastInsertId();
	foreach($_POST['server'] as $server)
	{
		$query = $pdo->prepare("INSERT INTO servers(changeid,server) VALUES(?,?)");
		$query->execute([$changeid, $server]);
	}
	$pdo = null;
	die("Change successfully added!");
}	
/* updates an entry in the "changes" table */
/* quick version. only includes a few fields. called from index.php */
function update_change_short()
{
	/* init database connection */
	$pdo = dbcon();
	$sql = "UPDATE changes SET ";
	$args = ['title', 'risk', 'start', 'end', 'description'];
	/* verify the args above and build the sql query with them */
	for($x = 0; $x < sizeof($args); $x++)
	{
		$arg = $args[$x];
		if(isset($_POST[$arg])) 
		{
			if($_POST[$arg] == '')
				die("Argument $arg is missing.");
			elseif($arg == 'end' || $arg == 'start')
				$args[$x] = strtotime($_POST[$arg]);
			else
				$args[$x] = $_POST[$arg];
			$sql .= "$arg=?,";
		}
		else 
		{
			die("Argument $arg not set: add_change()");
		}
	}
	/* trim the last comma */
	$sql = substr($sql, 0, -1);
	/* finish up the query */
	$sql .= " WHERE id=" . $_POST['id'];
	/* execute query */
	$query = $pdo->prepare($sql);
	$query->execute($args);
	$pdo = null;
	die("Change successfully updated!");
}
/* updates an entry in the "changes" table */
/* full version. includes all fields. called from change.php */
function update_change_long()
{
	/* init database connection */
	$pdo = dbcon();
	$sql = "UPDATE changes SET ";
	$args = ['title', 'risk', 'start', 'end', 'description', 'what', 'who', 'incident', 'backout', 'postmodification'];
	/* verify the args above and build the sql query with them */
	for($x = 0; $x < sizeof($args); $x++)
	{
		$arg = $args[$x];
		if(isset($_POST[$arg])) 
		{
			if($_POST[$arg] == '')
				die("Argument $arg is missing.");
			elseif($arg == 'end' || $arg == 'start')
				$args[$x] = strtotime($_POST[$arg]);
			else
				$args[$x] = $_POST[$arg];
			$sql .= "$arg=?,";
		}
		else 
		{
			die("Argument $arg not set: add_change()");
		}
	}
	/* trim the last comma */
	$sql = substr($sql, 0, -1);
	/* finish up the query */
	$sql .= " WHERE id=" . $_POST['id'];
	/* execute query */
	$query = $pdo->prepare($sql);
	$query->execute($args);
	$pdo = null;
	die("Change successfully updated!");
}
/* adds/updates an entry to/in the "votes" table */
function submit_vote()
{
	global $eid;
	$pdo = dbcon();
	/* check to see if this user has already submitted a vote entry regarding this change */
	$sql = "SELECT COUNT(id) as count, id FROM votes WHERE changeid=? AND user=?";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['change'], $eid]);
	$row = $query->fetch();

	/* if not add a new entry to the table */
	if($row['count'] == 0)
	{
		$sql = "INSERT INTO votes(changeid, vote, user, comment, time) VALUES(?,?,?,?,?)";
		$query = $pdo->prepare($sql);
		$query->execute([$_POST['change'], $_POST['vote'], $eid, isset($_POST['comment']) ? $_POST['comment'] : "", time()]);
	}
	/* otherwise update his vote */
	else
	{
		if(isset($_POST['comment'])) {
			$sql = "UPDATE votes SET vote=?, comment=?, time=? WHERE id=?";
			$query = $pdo->prepare($sql);
			$query->execute([$_POST['vote'], $_POST['comment'], time(), $row['id']]);
		}
		else {
			$sql = "UPDATE votes SET vote=?, time=? WHERE id=?";
			$query = $pdo->prepare($sql);
			$query->execute([$_POST['vote'], time(), $row['id']]);	
		}
	}
	/* clean up */
	$pdo = null;
	die("Comment successfully posted!");
}

function change_done()
{
	global $eid;
	$pdo = dbcon();
	$sql = "UPDATE changes SET status=2 WHERE id=?";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id']]);
	$sql = "INSERT INTO votes(changeid, user, vote, time) VALUES(?,?,?,?)";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id'], $eid, 4, time()]);
	$pdo = null;
	die("success");
}

function change_delete()
{
	$pdo = dbcon();

	$sql = "DELETE FROM changes WHERE id=?";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id']]);

	$sql = "DELETE FROM votes WHERE changeid=?";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id']]);

	$sql = "DELETE FROM servers WHERE changeid=?";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id']]);

	$pdo = null;
	die("success");
}


function submit_release()
{
	global $eid;
	$pdo = dbcon();
	/* check to see if this user has already submitted a vote entry regarding this change */
	$sql = "UPDATE changes SET status=1 WHERE id=?";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id']]);
	
	$sql = "INSERT INTO votes(changeid, user, vote, time) VALUES(?,?,?,?)";
	$query = $pdo->prepare($sql);
	$query->execute([$_POST['id'], $eid, 3, time()]);

	die("Change successfully released!");
}

?>