<?php include_once('header.php'); ?>
<?php include_once('menu.php'); ?>

<div class="content">
	<?php
		$data = dbcon();
		$query = $data->prepare("SELECT id,title,risk,start,end,service,(SELECT GROUP_CONCAT(server) FROM servers WHERE changeid=changes.id) AS server,user,description,who,what,status,backout,postmodification,incident,(SELECT vote FROM votes WHERE user=changes.user AND changeid=changes.id) AS vote FROM changes WHERE changes.id=?");
		$query->execute([$_GET['id']]);
		if($row = $query->fetch())
		{
			$status = $row['status'];
			$user = $row['user'];
			?>

			<form class="sysmod">
				<div class="left">
					<input type="hidden" class="id" name="id" value="<?php echo $row['id']; ?>">
					<a class="link" href="change.php?id=<?php echo $row['id']; ?>">
						<h2>
							<span class="idcolor <?php statusColor($row['status']); ?>">[<?php echo $row['id']; ?>]</span> 
							<span class="title">
								<?php echo $row['title']; ?>
							</span>
						</h2>
					</a>
					<p><b>For</b> <span> <?php echo $row['service']; ?></span> <b>On</b> <?php echo $row['server']; ?></p>
					<p>
						<b>By</b>
						<span> <?php echo $row['user']; ?></span> 
						<b>From </b>
						<span class="start"><?php echo date('m/d/Y g:i A',$row['start']); ?> </span>
						<b> To </b>
						<span class="end"><?php echo date('m/d/Y g:i A',$row['end']); ?></span>
					</p>
					<p>
						<b>Risk: </b><?php echo toRisk($row['risk']); ?>
					</p>
					<p>
						<i class="description"><?php echo $row['description']; ?></i>
					</p>
					<p>
						<b>Incident:</b> <span class="incident"><?php echo incident($row['incident']); ?></span>
					</p>
					<p>
						<b>Impact on What:</b> <span class="what"><?php echo $row['what']; ?></span>
					</p>
					<p>
						<b>Impact on Who:</b> <span class="who"><?php echo $row['who']; ?></span>
					</p>
					<p>
						<b>Backout Process:</b> <span class="backout"><?php echo $row['backout']; ?></span>
					</p>
					<p>
						<b>Post Modification Process:</b> <span class="postmodification"><?php echo $row['postmodification']; ?></span>
					</p>
				</div>
				<div class="right">
					<?php if($row['user'] == $eid && $row['status'] < 1) { ?><button class="btn btn-primary btnedit">Edit</button><?php } ?>
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
		}

		$query = $data->prepare("SELECT * FROM votes WHERE changeid=?");
		$query->execute([$_GET['id']]);
		if(!$query->rowCount())
			echo "<p>No approval activity found!</p>";
		else
			while($row = $query->fetch()) {
				?>
				<div class="sysmod">
					<div class="left">
						<p>
							User <b><?php echo $row['user']; ?></b> <?php commentStatus($row['vote']) ?>
							At <?php echo date("l jS \of F Y h:i:s A", $row['time']); ?>
							<?php if($row['comment'] != "") echo ":<br><i>" . $row['comment'] . "</i>"; else echo "."; ?>
						</p>
					</div>
					<div class="clear"></div>
				</div>

				<?php
			}
	if($status == 0 && $user != $eid) {
	?>
	<hr>
	<form id="releaseFrm">
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
		<button class="btn btn-danger" id="releaseBtn" style="width: 100px; margin-top: 10px">Release</button><br>
		<label id="releaseRes" class="red" style="font-weight: normal; margin-top: 10px; display:none">Response will be here!</label>
	</form>
	<?php } ?>
</div>


<script>
	$(function(){

		$("#releaseBtn").click(function(e){
			e.preventDefault();
			$(this).hide();
			$(this).next().hide();
			var idcolor = $(this).parent().parent().find(".idcolor").get(0);
			$(idcolor).removeClass("yellow").addClass("blue");

			$.post("functions.php?function=submit_release", $("#releaseFrm").serialize()).done(function(res){
				$("#releaseRes").html(res).show();
				if(!$("#comment-dropdown").is(':visible')) 
					$("#comment-dropdown-a").trigger("click");
			});
		});

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

		$(".btnedit").click(function(e){
			e.preventDefault();
			
			var id = $(this).parent().parent().find(".id").get(0);
			var idcolor = $(this).parent().parent().find(".idcolor").get(0);
			var title = $(this).parent().parent().find(".title").get(0);
			var risk = $(this).parent().parent().find(".risk").get(0);
			var start = $(this).parent().parent().find(".start").get(0);
			var end = $(this).parent().parent().find(".end").get(0);
			var description = $(this).parent().parent().find(".description").get(0);
			var incident = $(this).parent().parent().find(".incident").get(0);
			var what = $(this).parent().parent().find(".what").get(0);
			var who = $(this).parent().parent().find(".who").get(0);
			var backout = $(this).parent().parent().find(".backout").get(0);
			var postmodification = $(this).parent().parent().find(".postmodification").get(0);
			var link = $(this).parent().parent().find(".link").get(0);
			var response = $(this).parent().parent().find(".response").get(0);

			if($(this).html() == "Edit") {
				
				$(link).attr("href", "#");
				$(link).addClass('disabled');
				$(title).replaceWith($('<input type="text" name="title" placeholder="Title" class="title">').val(title.innerHTML.trim()));
				$(description).replaceWith($('<textarea name="description" placeholder="Description" class="description"></textarea>').html(description.innerHTML));
				$(risk).replaceWith($('<select class="risk" name="risk"><option value="0">Low</option><option value="1">Medium</option><option value="2">High</option></select>').val(deconvertRisk(risk.innerHTML)));
				$(start).replaceWith($('<input type="text" class="start" name="start">').val(start.innerHTML));
				$(end).replaceWith($('<input type="text" class="end" name="end">').val(end.innerHTML));
				$(incident).replaceWith($('<select class="incident" name="incident"><option value="-1" selected disabled>Incident Type</option><option value="0">Planned: Outage</option><option value="1">Planned: Non-Disruptive</option><option value="2">Non-Disruptive</option><option value="3">Emergency</option></select>').val(deconvertIncident(incident.innerHTML)));
				$(what).replaceWith($('<input type="text" name="what" placeholder="Impact on What" class="what">').val(what.innerHTML.trim()));
				$(who).replaceWith($('<input type="text" name="who" placeholder="Impact on Who" class="who">').val(who.innerHTML.trim()));
				$(backout).replaceWith($('<input type="text" name="backout" placeholder="Backout Process" class="backout">').val(backout.innerHTML.trim()));
				$(postmodification).replaceWith($('<input type="text" name="postmodification" placeholder="Post Modification Process" class="postmodification">').val(postmodification.innerHTML.trim()));

				$('.sysmod textarea').textareaAutoSize().trigger('input');
				$(this).parent().parent().find("input,select").css("margin", "0");
				$(this).parent().parent().find("input,select").css("padding", "0");
				$('.start, .end').datetimepicker().css("width", "140px");
				
				if($(idcolor).hasClass("blue")) { // approved but not finished = there is a 'done' button
					var donebtn = $(this).parent().parent().find(".btndone").get(0);
					$(donebtn).replaceWith($('<button class="btn btn-danger btndelete" style="margin-right: 10px">Delete</button>'));
				}
				else {
					$(this).after('<button class="btn btn-danger btndelete" style="margin-right: 10px">Delete</button>');
				}

				$(this).html("Save");
				$(this).removeClass("btn-primary").addClass("btn-success");
			}

			else {
				$.post("functions.php?function=update_change_long", $(this).parent().parent().serialize()).done(function(result){
					response.innerHTML = result;
				});

				$(link).attr("href", "change.php?id=" + id.value);
				$(link).removeClass('disabled');
				$(title).replaceWith('<span class="title">' + title.value + '</span>');
				$(description).replaceWith($('<p class="description"></p>').html(description.value));
				$(risk).replaceWith($('<span class="risk"></span>').addClass(riskColor(risk.value)).html(convertRisk(risk.value)));
				$(start).replaceWith($('<span class="start"></span>').html(start.value));
				$(end).replaceWith($('<span class="end"></span>').html(end.value));
				$(incident).replaceWith($('<span class="incident"></span>').html(convertIncident(incident.value)));
				$(what).replaceWith($('<span class="what"></span>').html(what.value));
				$(who).replaceWith($('<span class="who"></span>').html(who.value));
				$(backout).replaceWith($('<span class="backout"></span>').html(backout.value));
				$(postmodification).replaceWith($('<span class="postmodification"></span>').html(postmodification.value));

				var deleteBtn = $(this).parent().parent().find(".btndelete").get(0);
				if($(idcolor).hasClass("blue")) {
					$(deleteBtn).replaceWith($('<button class="btn btn-success btndone" style="margin-right: 10px">Done</button>'))
				}
				else {
					$(deleteBtn).replaceWith($(''))	
				}

				$(this).html("Edit");
				$(this).removeClass("btn-success").addClass("btn-primary");
			}

		});

	});

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

	function convertIncident(val) {
		if(val == 0)
			return "Planned: Outage";
		else if(val == 1)
			return "Planned: Non-Disruptive";
		else if(val == 2)
			return "Non-Disruptive";
		else
			return "Emergency";
	}

	function deconvertIncident(val) {
		if(val == "Planned: Outage")
			return 0;
		else if(val == "Planned: Non-Disruptive")
			return 1;
		else if(val == "Non-Disruptive")
			return 2;
		else
			return 3;
	}

	function riskColor(riskVal) {
		if(riskVal == 0)
			return "green";
		else if(riskVal == 1)
			return "yellow";
		else
			return "red";
	}
</script>

<?php include_once('footer.php'); ?>