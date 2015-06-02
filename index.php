<!DOCTYPE html>
<html>
	<head>
		<title>Vault</title>
		<link href="favicon.png" rel="icon">
		<link href="style.css" rel="stylesheet">
		<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div id="header">
			<img src="logo.png" id="logo">
			<input type="text" id="searchtxt" caption="Search Vault">
			<button id="searchbtn">Search</button>
			<div class="clear"></div>
		</div>
		<div id="menu">
			<div id="content">
				<img src="menu.png" id="menubtn"><span>Notes</span>
			</div>
		</div>
		<div id="notes">
			<div id="addnote">
				<input type="text" id="addnotetitle" caption="Add Note">
				<textarea id="addnotebody"></textarea>
			</div>
		</div>
	</body>
	<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script>
	$(function() {
		$("input[type='text']").each(function(key,val){
			$(val).val($(val).attr("caption"));
		});

		$("input[type='text']").focus(function(){
			if($(this).val() == $(this).attr("caption")) {
				$(this).val("");
			}
		});

		$("input[type='text']").focusout(function(){
			if($(this).val() == "") {
				$(this).val($(this).attr("caption"));
			}
		});
	})
	</script>
</html>