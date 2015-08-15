<div class="side-menu">
    
	<nav class="navbar navbar-default" role="navigation">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	        <div class="brand-wrapper">
	            <!-- Hamburger -->
	            <button type="button" class="navbar-toggle">
	                <span class="sr-only">Toggle navigation</span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	            </button>

	            <div class="brand-name-wrapper">
	                <a class="navbar-brand" href="index.php">
	                    Sysmod
	                </a>
	            </div>

	            <a data-toggle="collapse" href="#search" class="btn btn-default" id="search-trigger">
	                <span class="glyphicon glyphicon-search"></span>
	            </a>

	            <div id="search" class="panel-collapse collapse">
	                <div class="panel-body">
	                    <form class="navbar-form" role="search" action="index.php">
	                        <div class="form-group">
	                            <input type="text" class="form-control" name="q" placeholder="Search">
	                        </div>
	                        <button type="submit" class="btn btn-default "><span class="glyphicon glyphicon-ok"></span></button>
	                    </form>
	                </div>
	            </div>
	        </div>

	    </div>

	    <!-- Main Menu -->
	    <div class="side-menu-container">
	        <ul class="nav navbar-nav">

	            <li class="active panel panel-default" id="dropdown">
	            	<a data-toggle="collapse" href="#changes-dropdown"><span class="glyphicon glyphicon-pencil"></span>Create<span class="caret"></span></a>
	            	<div id="changes-dropdown" class="panel-collapse collapse">
		            	<form class="panel-body" id="changeform">
		            		<input type="hidden" name="user" value="<?php echo $eid; ?>">
		                    <ul class="nav navbar-nav">
		                        <li>
		                        	<input type="text" class="form-control" placeholder="Summary" name="title" title="Summary">
		                        </li>
		                        <li>
			                        <select class="form-control" id="service" name="service" title="Service">
										<?php 
											$url = "services.xml";
											$xml = simplexml_load_file($url);
											foreach($xml->xpath('//d:Title') as $service)
											{
											 	echo "<option>$service</option>";
											}
										?>
									</select>
								</li>
		                        <li>
		                        	<select class="form-control" id="server" name="server[]" multiple="multiple" title="Servers">
										<?php 
											$url = "servers.xml";
											$xml = simplexml_load_file($url);
											foreach($xml->xpath("//m:properties[d:DeviceTypeValue = 'Server' or d:DeviceTypeValue = 'Virtual Server']/d:Device_Name") as $server)
											{
											 	echo "<option>$server</option>";
											}
										?>
									</select>
								</li>
		                        <li>
		                        	<select class="form-control" id="risk" name="risk" title="Risk">
										<option value="-1" selected disabled>Risk</option>
										<option value="0">Low</option>
										<option value="1">Medium</option>
										<option value="2">High</option>
									</select>
		                        </li>
		                        <li>
		                        	<select class="form-control" id="incident" name="incident" title="Incident Type">
										<option value="-1" selected disabled>Incident Type</option>
										<option value="0">Planned: Outage</option>
										<option value="1">Planned: Non-Disruptive</option>
										<option value="2">Non-Disruptive</option>
										<option value="3">Emergency</option>
									</select>
		                        </li>
		                        <li>
				                    <input type='text' class="form-control" id="start" name="start" placeholder="Start Time" title="Start Time">
		                        </li>
		                        <li>
		                        	<input type="text" class="form-control" id="end" name="end" placeholder="End Time" title="End Time">
		                        </li>
								<li>
		                        	<textarea class="form-control" id="description" name="description" placeholder="Change Description" title="Change Description"></textarea>
								</li>
								<li>
		                        	<textarea class="form-control" id="what" name="what" placeholder="Impact on What" title="Impact on What"></textarea>
								</li>
								<li>
		                        	<textarea class="form-control" id="who" name="who" placeholder="Impact on Who" title="Impact on Who"></textarea>
								</li>
								<li>
		                        	<textarea class="form-control" id="backout" name="backout" placeholder="Backout Process" title="Backout Process"></textarea>
								</li>
								<li>
		                        	<textarea class="form-control" id="postmodification " name="postmodification" placeholder="Post Modification Process" title="Post Modification Process"></textarea>
								</li>
								<li>
		                        	<button class="form-control btn btn-primary" id="add" name="add">Submit</button>
		                        	<span class="success-msg" id="change-msg"></span>
		                        </li>
		                    </ul>
	                    </form>
                    </div>
	            </li>
	            <li class="active panel panel-default" id="dropdown">
	            	<a data-toggle="collapse" href="#comment-dropdown" id="comment-dropdown-a"><span class="glyphicon glyphicon-comment"></span>Comment<span class="caret"></span></a>
	            	<div id="comment-dropdown" class="panel-collapse collapse">
		            	<form class="panel-body" id="commentform">
		                    <ul class="nav navbar-nav">
		                        <li>
		                        	<input type="text" class="form-control" placeholder="Change ID" name="change" id="change">
		                        </li>
		                        <li>
		                        	<select class="form-control" id="vote" name="vote">
										<option value="-1" selected disabled>Status</option>
										<option value="0">Neutral</option>
										<option value="1">Approved</option>
										<option value="2">Denied</option>
										<option value="3">Stamp</option>
									</select>
		                        </li>
		                        <li>
		                        	<textarea class="form-control" id="comment" name="comment" placeholder="Comment"></textarea>
		                        </li>
								<li>
		                        	<button class="form-control btn btn-primary" id="commentbtn" name="add">Submit</button>
		                        	<span class="success-msg" id="comment-msg"></span>
		                        </li>
		                    </ul>
	                    </form>
                    </div>
	            </li>
	            <li class="active">
	            	<a href="report.php"><span class="glyphicon glyphicon-list-alt"></span>Report</a>
	            </li>
	            <li class="active">
	            	<a href="#"><span class="glyphicon glyphicon-calendar"></span>Calendar</a>
	            </li>
	            <li class="active">
	            	<a href="https://utdirect.utexas.edu/security-443/logoff.cgi?goto=https://utw10194.utweb.utexas.edu"><span class="glyphicon glyphicon-log-out"></span>Logout</a>
	            </li>
	        </ul>
	    </div>
	</nav>

</div>

<script src="jquery.autosize.js"></script>

<script>
	$(function(){

		var options = $('#server option');
		var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
		arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
		options.each(function(i, o) {
			o.value = arr[i].v;
			$(o).text(arr[i].t);
		});

		var options = $('#service option');
		var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
		arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
		options.each(function(i, o) {
			o.value = arr[i].v;
			$(o).text(arr[i].t);
		});
		$("#service").prepend("<option selected disabled>Service</option>");

		$("#server").multiselect({
			buttonWidth: '100%',
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Servers';
                }
                else {
                    return options.length + ' Servers Selected';
                }
           }
		});

		$("#service").multiselect({
			buttonWidth: '100%',
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true
		});

		$("#risk").multiselect({
			buttonWidth: '100%',
		});

		$("#incident").multiselect({
			buttonWidth: '100%',
		});

		$(".multiselect-container .input-group").after("<div class='multiselect-container-spacer'></div>");

		$('textarea').textareaAutoSize();
		$('#start, #end').datetimepicker();
		$("#add").click(function(event){
			event.preventDefault();
			$.post("functions.php?function=add_change", $("#changeform").serialize()).done(function(msg){
				$("#change-msg").html(msg);
				$("#change-msg").slideDown();
			});
		});
		$("#commentbtn").click(function(event){
			event.preventDefault();
			$.post("functions.php?function=submit_vote", $("#commentform").serialize()).done(function(msg){
				$("#comment-msg").html(msg);
				$("#comment-msg").slideDown();
			});
		});
	});
</script>