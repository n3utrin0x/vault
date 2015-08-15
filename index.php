<?php include_once('header.php'); ?>

<?php include_once('menu.php'); ?>

<div class="content">
	<?php
		$pdo = dbcon();
		$sql = "SELECT id,title,risk,start,end,service,(SELECT GROUP_CONCAT(server) FROM servers WHERE changeid=changes.id) AS server,user,description,status,(SELECT vote FROM votes WHERE user=? AND changeid=changes.id) AS vote FROM changes ORDER BY id DESC";
		if(isset($_GET['q']) && $_GET['q'] != "") 
		{
			$sql .= " WHERE id = ? OR title LIKE ? OR description LIKE ? OR start = ? OR end = ?";
			$query = $pdo->prepare($sql);
			$query->execute(["{$_GET['q']}", "%{$_GET['q']}%", "%{$_GET['q']}%",strtotime("{$_GET['q']}"), strtotime("{$_GET['q']}")]);
		}
		else 
		{
			$query = $pdo->prepare($sql);
			$query->execute([$eid]);
		}
		if(!($row = $query->fetch()))
			echo "<form class='sysmod'><div class='left'><h2>...</h2></div></form><div class='clear'></div>";
		else
			do
			{
				
			?>
				<form class="sysmod">
					<div class="left">
						<input type="hidden" class="id" name="id" value="<?php echo $row['id']; ?>">
						<a class="link" href="change.php?id=<?php echo $row['id']; ?>"><h2><span class="idcolor <?php statusColor($row['status']); ?>">[<?php echo $row['id']; ?>]</span> <span class="title"><?php echo $row['title']; ?></span></h2></a>
						<p><b>For</b> <span> <?php echo $row['service']; ?></span> <b>On</b> <?php echo $row['server']; ?></p>
						<p><b>By</b><span> <?php echo $row['user']; ?></span> <b>From </b><span class="start"><?php echo date('m/d/Y g:i A',$row['start']); ?> </span><b> To </b><span class="end"><?php echo date('m/d/Y g:i A',$row['end']); ?></span></p>
						<p><span>Risk: </span><?php echo toRisk($row['risk']); ?></p>
						<p class="description"><?php echo $row['description']; ?></p>
					</div>
					<div class="right">
						<?php if($row['user'] == $eid && $row['status'] < 1) { ?><button class="btn btn-primary btnedit">Edit</button><button class="btn btn-danger btndelete">Delete</button><?php } ?>
						<?php if($row['user'] == $eid && $row['status'] == 1) { ?><button class="btn btn-success btndone" style="margin-right: 10px">Done</button><?php } ?>
						<br>
						<span class="response">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						<br>
						<div class="clear"></div>
						<?php if($row['status'] == 0 && $row['user'] != $eid) { ?>
						<span class="glyphicon glyphicon-thumbs-down thumbs-down <?php if($row['vote'] == 2) echo 'red'; ?>"></span>
						<span class="glyphicon glyphicon-thumbs-up thumbs-up <?php if($row['vote'] == 1) echo 'green'; ?>"></span>
						<?php } ?>
					</div>
					<div class="clear"></div>
					<hr>
				</form>
			<?php
			}while ($row = $query->fetch());
		$pdo = null;
	?>
	
</div>

<script>

	$(function(){
		

		$(".thumbs-up").click(function(e){
			var change = $(this).parent().parent().find(".id").get(0).value;
			$("#vote").val(1);
			$("#change").val(change);

			if($(this).hasClass("green")) {
				$(this).removeClass("green");

				if($("#comment-dropdown").is(':visible')) 
					$("#comment-dropdown-a").trigger("click");

				$.post("functions.php?function=submit_vote", { change: change, vote: "0" });

			}
			else {

				$(this).addClass("green");

				if($(this).prev().hasClass("red")) 
					$(this).prev().removeClass("red");
				else 
					if(!$("#comment-dropdown").is(':visible')) $("#comment-dropdown-a").trigger("click");

				$.post("functions.php?function=submit_vote", { change: change, vote: "1" });
				$("#comment").focus();
			}
		});

		$(".thumbs-down").click(function(e){

			var change = $(this).parent().parent().find(".id").get(0).value;
			$("#vote").val(2);
			$("#change").val(change);

			if($(this).hasClass("red")) {
				$(this).removeClass("red");

				if($("#comment-dropdown").is(':visible')) 
					$("#comment-dropdown-a").trigger("click");

				$.post("functions.php?function=submit_vote", { change: change, vote: "0" });

			}
			else {

				$(this).addClass("red");

				if($(this).next().hasClass("green")) 
					$(this).next().removeClass("green");
				else 
					if(!$("#comment-dropdown").is(':visible')) $("#comment-dropdown-a").trigger("click");

				$.post("functions.php?function=submit_vote", { change: change, vote: "2" });
				$("#comment").focus();
			}
		});

		$(".btndone").click(function(e){
			e.preventDefault();
			var idcolor = $(this).parent().parent().find(".idcolor").get(0);
			$(idcolor).removeClass("blue").addClass("green");
			$(this).hide();
			$.post("functions.php?function=change_done", $(this).parent().parent().serialize());

		});

		$(".btndelete").click(function(e){
			e.preventDefault();
			$(this).parent().parent().hide();
			$.post("functions.php?function=change_delete", $(this).parent().parent().serialize());
		});

		$(".btnedit").click(function(e){
			e.preventDefault();
			
			var id = $(this).parent().parent().find(".id").get(0);
			var idcolor = $(this).parent().parent().find(".idcolor").get(0);
			var title = $(this).parent().parent().find(".title").get(0);
			var risk = $(this).parent().parent().find(".risk").get(0);
			var start = $(this).parent().parent().find(".start").get(0);
			var end = $(this).parent().parent().find(".end").get(0);
			var description = $(this).parent().parent().find(".description").get(0);
			var link = $(this).parent().parent().find(".link").get(0);
			var response = $(this).parent().parent().find(".response").get(0);
			var btndelete = $(this).parent().parent().find(".btndelete").get(0);

			if($(this).html() == "Edit") {

				$(link).attr("href", "#");
				$(link).addClass('disabled');
				$(title).replaceWith($('<input type="text" name="title" placeholder="Title" class="title">').val(title.innerHTML.trim()));
				$(description).replaceWith($('<textarea name="description" placeholder="Description" class="description"></textarea>').html(description.innerHTML));
				$(risk).replaceWith($('<select class="risk" name="risk"><option value="0">Low</option><option value="1">Medium</option><option value="2">High</option></select>').val(deconvertRisk(risk.innerHTML)));
				$(start).replaceWith($('<input type="text" class="start" name="start">').val(start.innerHTML).datetimepicker().css("width", "140px"));
				$(end).replaceWith($('<input type="text" class="end" name="end">').val(end.innerHTML).datetimepicker().css("width", "140px"));
				$(btndelete).show();

				$('.sysmod textarea').textareaAutoSize().trigger('input');
				$(this).parent().parent().find("input,select").css("margin", "0");
				$(this).parent().parent().find("input,select").css("padding", "0");

				$(this).html("Save");
				$(this).removeClass("btn-primary").addClass("btn-success");	
			}

			else {
				$.post("functions.php?function=update_change_short", $(this).parent().parent().serialize()).done(function(result){
					response.innerHTML = result;
				});

				$(link).attr("href", "change.php?id=" + id.value);
				$(link).removeClass('disabled');
				$(title).replaceWith('<span class="title">' + title.value + '</span>');
				$(description).replaceWith($('<p class="description"></p>').html(description.value));
				$(risk).replaceWith($('<span class="risk"></span>').addClass(riskColor(risk.value)).html(convertRisk(risk.value)));
				$(start).replaceWith($('<span class="start"></span>').html(start.value));
				$(end).replaceWith($('<span class="end"></span>').html(end.value));
				$(btndelete).hide();

				$(this).html("Edit");
				$(this).removeClass("btn-success").addClass("btn-primary");
			}

		});
	});

	function convertDate(oldDate) {
		var temp = oldDate.split("/");
		return (temp[2] + "-" + temp[0] + "-" + temp[1]).split(" ").join("");
	}

	function deconvertDate(oldDate) {
		var temp = oldDate.split("-");
		return (temp[1] + "/" + temp[2] + "/" + temp[0]).split(" ").join("");
	}

	function convertRisk(riskVal) {
		if(riskVal == 0)
			return "Low";
		else if(riskVal == 1)
			return "Medium";
		else
			return "High";
	}

	function deconvertRisk(riskVal) {
		if(riskVal == "Low")
			return 0;
		else if(riskVal == "Medium")
			return 1;
		else
			return 2;
	}

	function riskColor(riskVal) {
		if(riskVal == 0)
			return "green";
		else if(riskVal == 1)
			return "yellow";
		else
			return "red";
	}

	function detectIE() {
	    var ua = window.navigator.userAgent;

	    var msie = ua.indexOf('MSIE ');
	    if (msie > 0) {
	        // IE 10 or older => return version number
	        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	    }

	    var trident = ua.indexOf('Trident/');
	    if (trident > 0) {
	        // IE 11 => return version number
	        var rv = ua.indexOf('rv:');
	        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	    }

	    var edge = ua.indexOf('Edge/');
	    if (edge > 0) {
	       // IE 12 => return version number
	       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	    }

	    // other browser
	    return false;
	}
</script>

<?php include_once('footer.php'); ?>