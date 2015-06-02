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
				<img id="addnotelabel" src="list.png">
				<button id="addnotebtn">DONE</button>
			</div>
		</div>
	</body>
	<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="jquery.autoresize.js"></script>
	<script src="jquery.selectrange.js"></script>
	<script>
	$(function() {
		$("input[type='text']").each(function(key,val){
			$(val).val($(val).attr("caption"));
		});
		$("input[type='text']").focus(function(){
			$(this).setCursorPosition(1);
		});
		$("input[type='text']").keydown(function(){
			if($(this).val() == $(this).attr("caption")) {
				$(this).val("");
			}
		});
		$("input[type='text']").focusout(function(){
			if($(this).val() == "") {
				$(this).val($(this).attr("caption"));
				$(this).css("color", "#BBBBBB");
			}
			else if($(this).val() != $(this).attr("caption")) {
				$(this).css("color", "black");
			}
		});
		$("textarea").autoresize();
		$("#addnotetitle").focus(function(){
			$(this).css("border", "none");
			$(this).css("box-shadow", "none");
			$("#addnote").css("outline", "1px solid #DDDDDD");
			$("#addnote").css("height", "200px");
		});
	});
	</script>
</html>