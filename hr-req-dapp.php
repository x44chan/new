<?php 
	session_start();
	$accid = $_SESSION['acc_id'];
	include("conf.php");
	if(isset($_SESSION['acc_id'])){
		if($_SESSION['level'] != 'HR'){
			header("location: index.php");
		}
	}else{
		header("location: index.php");
	}
	date_default_timezone_set('Asia/Manila');
	include("header.php");	
?>
<script type = "text/javascript">
	$(function(){
			$("#dappot").hide();
			$("#fordappot").on("click", function(){
				$("#dappot").show();
				$("#newuser").hide();
				$("#dappob").hide();
				$("#undertime").hide();
				$("#offb").hide();
				$("#formhidden").hide();
				$("#disappleave").hide();
				$('#disappundr').hide();
				$('#leave').hide();
			});
			
			$("#disappundr").hide();
			$("#fordappundr").on("click", function(){
				$("#disappundr").show();
				$("#dappot").hide();
				$("#undertime").hide();
				$("#dappob").hide();
				$("#formhidden").hide();
				$("#newuser").hide();
				$("#disappleave").hide();
				$('#leave').hide();
			});
			
			$("#disappleave").hide();
			$("#fordisappleave").on("click", function(){
				$("#disappleave").show();
				$("#disappundr").hide();
				$("#dappot").hide();
				$("#undertime").hide();
				$("#dappob").hide();
				$("#formhidden").hide();
				$("#newuser").hide();
				$('#leave').hide();
			});
			
			$("#dappob").hide();
			$("#fordappob").on("click", function(){	
				$("#disappundr").hide();
				$("#dappob").show();
				$("#dappot").hide();
				$("#undertime").hide();
				$("#offb").hide();
				$("#disappleave").hide();
				$("#formhidden").hide();
				$("#newuser").hide();
				$('#leave').hide();
			});

	});
</script>
<div align = "center" style = "margin-bottom: 30px; ">
	<div class="alert alert-success"><br>
		Welcome <strong><?php echo $_SESSION['name'];?> !</strong><br>
		<?php echo date('l jS \of F Y h:i A'); ?> <br>	<br>	
		<div class="btn-group btn-group-lg">
			<a  type = "button"class = "btn btn-primary" href = "hr.php?ac=penot">Home</a>	
			<div class="btn-group btn-group-lg">
				<button type="button" class="btn btn-primary dropdown-toggle"  data-toggle="dropdown">New Request <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu">
				  <li><a href="#" id = "newovertime">Overtime Request</a></li>
				  <li><a href="#" id = "newoffb">Official Business Request</a></li>
				  <li><a href="#" id = "newleave">Leave Of Absense Request</a></li>				  
				  <li><a href="#" id = "newundertime">Undertime Request Form</a></li>
				</ul>
			</div>
			<a  type = "button"class = "btn btn-primary"  href = "hr-req-app.php" >Approved Request</a>		
			<a  type = "button"class = "btn btn-primary  active"  href = "hr-req-dapp.php">Dispproved Request</a>		
			<a href = "logout.php" class="btn btn-danger" onclick="return confirm('Do you really want to log out?');"  role="button">Logout</a>
		</div>
		<br><br>
		<div class = "btn-group btn-group">
			<button  type = "button"class = "btn btn-success" id = "fordappot"> Disapproved Overtime Request </button>
			<button  type = "button"class = "btn btn-success" id = "fordappob"> Disapproved Official Business Request </button>			
			<button  type = "button"class = "btn btn-success" id = "fordisappleave"> Disapproved Leave Request </button>		
			<button  type = "button"class = "btn btn-success" id = "fordappundr"> Disapproved Undertime Request </button>	
		</div>
	</div>
</div>

<div id = "dappot" style = "margin-top: -30px; display: none;">
	<?php 
		include("conf.php");
		$sql = "SELECT * FROM overtime,login where login.account_id = overtime.account_id and state like 'DA%' ORDER BY datefile ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get">
		<table class = "table table-hover" align = "center">
			<thead>
				<tr>
					<td colspan = 7 align = center><h2> Disapproved Overtime Request </h2></td>
				</tr>
				<tr>
					<th>Date File</th>
					<th>Date of Overtime</th>
					<th>Name of Employee</th>
					<th>Reason</th>
					<th>From - To (Overtime)</th>
					<th>Offical Work Schedule</th>
					<th>State</th>
				</tr>
			</thead>
			<tbody>
	<?php
		while($row = $result->fetch_assoc()){
			$originalDate = date($row['datefile']);
			$newDate = date("F j, Y", strtotime($originalDate));
			echo
				'<tr>
					<td>'.$newDate.'</td>
					<td>'.date("F j, Y", strtotime($row["dateofot"])).'</td>
					<td>'.$row["nameofemp"].'</td>
					<td width = 300 height = 70>'.$row["reason"].'</td>
					<td>'.$row["startofot"] . ' - ' . $row['endofot'].'</td>
					<td>'.$row["officialworksched"].'</td>					
					<td><b>';
						if($row['state'] == 'UA'){
							echo 'Pending';
						}else if($row['state'] == 'AHR'){
							echo '<p><font color = "green">Approved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</font></p> '.$row['dareason'];
						}
					echo '<td></tr>';
		}
		echo '</tbody></table></form>';
	}$conn->close();
?>
</div>
<div id = "dappob" style = "margin-top: -30px; display: none;">
	<?php 
		include("conf.php");
		$sql = "SELECT * FROM officialbusiness,login where login.account_id = officialbusiness.account_id and state like 'DA%' ORDER BY obdate ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get">
		<table class = "table table-hover" align = "center">
			<thead>
				<tr>
					<td colspan = 9 align = center><h2> Disapproved Official Business Request </h2></td>
				</tr>
				<tr>
					<th>Date File</th>
					<th>Name of Employee</th>
					<th>Position</th>
					<th>Department</th>
					<th>Date of Request</th>
					<th>Time In - Time Out</th>
					<th>Offical Work Schedule</th>
					<th>Reason</th>
					<th>State</th>
				</tr>
			</thead>
			<tbody>
	<?php
		while($row = $result->fetch_assoc()){
			$originalDate = date($row['obdate']);
			$newDate = date("F j, Y", strtotime($originalDate));
			echo
				'<tr>
					<td>'.$newDate.'</td>
					<td>'.$row["obename"].'</td>
					<td>'.$row["obpost"].'</td>
					<td >'.$row["obdept"].'</td>
					<td>'.$row['obdatereq'].'</td>					
					<td>'.$row["obtimein"] . ' - ' . $row['obtimeout'].'</td>
					<td>'.$row["officialworksched"].'</td>				
					<td >'.$row["obreason"].'</td>	
					<td><b>';
						if($row['state'] == 'UA'){
							echo 'Pending';
						}else if($row['state'] == 'AHR'){
							echo '<p><font color = "green">Approved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</font></p> '.$row['dareason'];
						}
					echo '<td></tr>';
		}
		echo '</tbody></table></form></div>';
	}$conn->close();
	?>
</div>

<div id = "disappundr" style = "display: none; margin-top: -30px;">
	<?php 
		$date17 = date("d");
		$dated = date("m");
		$datey = date("Y");
		if($date17 >= 17){
			$forque = 17;
			$endque = 31;
		}else{
			$forque = 1;
			$endque = 16;
		}
		include("conf.php");
		$sql = "SELECT * FROM undertime,login where login.account_id = undertime.account_id and state like 'DA%'  ORDER BY datefile ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get">
		<table class = "table table-hover" align = "center">
			<thead>				
				<tr>
					<td colspan = 7 align = center><h2> Dispproved Undertime Request </h2></td>
				</tr>
				<tr >
					<th>Date File</th>
					<th>Date of Undertime</th>
					<th>Name of Employee</th>
					<th>Reason</th>
					<th>From - To (Overtime)</th>
					<th>Number of Hrs/Minutes</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
	<?php
		while($row = $result->fetch_assoc()){				
			$originalDate = date($row['datefile']);
			$newDate = date("F j, Y", strtotime($originalDate));		
			$datetoday = date("Y-m-d");
			echo 
				'<tr>
					<td width = 180>'.$newDate.'</td>
					<td>'.date("F j, Y", strtotime($row["dateofundrtime"])).'</td>
					<td>'.$row["name"].'</td>
					<td width = 250 height = 70>'.$row["reason"].'</td>
					<td>'.$row["undertimefr"] . ' - ' . $row['undertimeto'].'</td>
					<td>'.$row["numofhrs"].'</td>
					<td><b>';
						if($row['state'] == 'UA'){
							echo 'Pending';
						}else if($row['state'] == 'AHR'){
							echo '<p><font color = "green">Approved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</font></p> '.$row['dareason'];
						}
					echo '<td></tr>';
			}
			echo '</tbody></table></form></div>';
		}
?>
</div>

<div id = "disappleave" style = "display: none; margin-top: -30px;">
	<?php 
	include("conf.php");
	$sql = "SELECT * FROM nleave,login where login.account_id = nleave.account_id and state like 'DA%' ORDER BY datefile ASC";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
	?>	
	<form role = "form" action = "approval.php"    method = "get">
			<table class = "table table-hover" align = "center">
				<thead>
					<tr>
						<td colspan = 10 align = center><h2> Disapproved Leave Request </h2></td>
					</tr>
					<tr>
						<th width = "130">Date File</th>
						<th width = "170">Name of Employee</th>
						<th width = "100">Date Hired</th>
						<th>Department</th>
						<th>Position</th>
						<th width = "200">Date of Leave (Fr - To)</th>
						<th width = "100"># of Day/s</th>
						<th width = "170">Type of Leave</th>
						<th width = "150">Reason</th>
						<th>State</th>
					</tr>
				</thead>
				<tbody>
	<?php
			while($row = $result->fetch_assoc()){
				
				$originalDate = date($row['datefile']);
				$newDate = date("F j, Y", strtotime($originalDate));
				$datetoday = date("Y-m-d");
				if($datetoday >= $row['twodaysred'] && $row['state'] == 'UA' ){
					echo '<tr style = "color: red">';
				}else{
					echo '<tr>';
				}		
				echo 
					'<td>'.$newDate.'</td>
					<td>'.$row["nameofemployee"].'</td>
					<td>'.date("M j, Y", strtotime($row["datehired"])).'</td>
					<td >'.$row["deprt"].'</td>
					<td>'.$row['posttile'].'</td>					
					<td>Fr: '.date("F j, Y", strtotime($row["dateofleavfr"])) .'<br>To: '.date("F j, Y", strtotime($row["dateofleavto"])).'</td>
					<td>'.$row["numdays"].'</td>					
					<td >'.$row["typeoflea"]. ' : ' . $row['othersl']. '</td>	
					<td >'.$row["reason"].'</td>	
					<td><b>';
						if($row['state'] == 'UA'){
							echo 'Pending';
						}else if($row['state'] == 'AHR'){
							echo '<p><font color = "green">Approved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</font></p> '.$row['dareason'];
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</font></p> '.$row['dareason'];
						}
					echo '<td></tr>';
		}
		echo '</tbody></table></form>';
	}$conn->close();
	?>
</div>

<?php include("req-form.php");?>
<?php include('footer.php');?>