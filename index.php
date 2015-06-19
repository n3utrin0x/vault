<!DOCTYPE html>
<html>
	<head>
		<title>Vault</title>
		<link href="favicon.png" rel="icon">
		<link href="style.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div id="header">
			<img src="logo.png" id="logo">
			<input type="text" id="searchtxt" placeholder="Search Vault">
			<button id="searchbtn">Search</button>
			<div class="clear"></div>
		</div>
		<div id="menu">
			<div id="content">
				<img src="menu.png" id="menubtn"><span>Notes</span>
			</div>
		</div>
		<div id="notes">
			<form id="addnote">
				<input type="text" id="addnotetitle" placeholder="Add Note">
				<textarea id="addnotebody" placeholder="Content" rows="6"></textarea>
				<img id="addnotelabel" src="list.png">
				<button id="addnotebtn">DONE</button>
			</form>
		</div>
	</body>
	<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="jquery.autoresize.js"></script>
	<script src="jquery.selectrange.js"></script>
	<script>
	$(function() {
		$("textarea").autoresize();
		$("#addnotetitle").focus(function(){
			if(!$("#addnotebody").is(":visible")) {
				$(this).css("border", "none");
				$(this).css("box-shadow", "none");
				$("#addnote").css("outline", "1px solid #DDDDDD");
				$("#addnotetitle").attr("placeholder", "Title");
				$("#addnotetitle").css("font-weight", "bold");
				$("#addnotebody").show();
				$("#addnotebody").focus();
				$("#addnotebody").css("border", "none");
				$("#addnotebtn").show();
			}
		});
		$("#addnotebtn").click(function(){
			$.post("functions.php?function=addnote", $("#addnote").serialize());
		});
	});
	</script>
</html>