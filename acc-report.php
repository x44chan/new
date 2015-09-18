<?php 
	session_start();
	include("conf.php");
	if(isset($_SESSION['acc_id'])){
		if($_SESSION['level'] != 'ACC'){
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
		$("#hideuser").on("click", function(){
			$("#userlist").hide();
	});
</script>
<div align = "center" style = "margin-bottom: 30px;">
	<div class="alert alert-success"><br>
		Welcome <strong><?php echo $_SESSION['name'];?> !</strong><br>
		<?php echo date('l jS \of F Y h:i A'); ?> <br>	<br>	
		<div class="btn-group btn-group-lg">
			<a  type = "button"class = "btn btn-primary" href = "/new">Home</a>		
			<div class="btn-group btn-group-lg">
				<button type="button" class="btn btn-primary dropdown-toggle"  data-toggle="dropdown">New Request <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu">
				  <li><a href="#" id = "newovertime">Overtime Request</a></li>
				  <li><a href="#" id = "newoffb">Official Business Request</a></li>
				  <li><a href="#" id = "newleave">Leave Of Absense Request</a></li>				  
				  <li><a href="#" id = "newundertime">Undertime Request Form</a></li>
				</ul>
			</div>			
			<a type = "button" class = "btn btn-primary  active" href = "acc-report.php" id = "showapproveda">Cutoff Summary</a>
			<a  type = "button"class = "btn btn-primary"  href = "acc-req-app.php"> Approved Request</a>		
			<a type = "button"class = "btn btn-primary"  href = "acc-req-dapp.php">Dispproved Request</a>		
			<a href = "logout.php" class="btn btn-danger" onclick="return confirm('Do you really want to log out?');"  role="button">Logout</a>
		</div>
		<br><br>
		<div class = "btn-group btn-group-justified">
			<a class = "btn btn-success" href = "?rep=ot"> Overtime Reports </a>
			<a  type = "button"class = "btn btn-success" href = "?rep=ob"> Official Business Reports </a>
			<a  type = "button"class = "btn btn-success" href = "?rep=lea"> Leave Reports </a>			
			<a  type = "button"class = "btn btn-success" href = "?rep=undr"> Undertime Reports </a>	
		</div>
	</div>
</div>

<div id = "userlist" <?php if(isset($_GET['acc_id'])){ echo 'style = "display: none;"';}?>>
<?php 
if(isset($_GET['norec'])){
	echo'<div align = "center" class="alert alert-warning">No O.T Record';
	echo '</div>';
}	
if(!isset($_GET['rep'])){
	$_GET['rep'] = 'ot';
	$title = "OT";
	echo '<div align = "center"><h3> Overtime Reports </h3></div>';
}else if($_GET['rep'] == 'ot'){
	$title = "OT";
	echo '<div align = "center"><h3> Overtime Reports </h3></div>';
}else if($_GET['rep'] == 'ob'){
	$title = "Official Business";
	echo '<div align = "center"><h3> Official Reports </h3></div>';
}else if($_GET['rep'] == 'undr'){
	$title = "Undertime";
	echo '<div align = "center"><h3> Undertime Reports </h3></div>';
}else if($_GET['rep'] == 'lea'){
	$title = "Leave";
	echo '<div align = "center"><h3> Leave Reports </h3></div>';
}
?>
	<form action = "acc-report.php" method = "">
		<table class = "table table-hover">
			<thead>
				<th width = "30%">Account ID</th>
				<th width = "40%">Employee Name</th>
				<th width = "30%">Action</th>
			</thead>
			<tbody>	
			<?php
				
				include("conf.php");
				$sql = "SELECT * FROM login where level != 'Admin'";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						echo '<tr style = "font-size: 15px"><td>'.$row['account_id'].'<input type = "hidden" name = "acc_id" value ="' .$row['account_id'].'"/></td>
							<td style = "font-size: 15px">'.$row['fname'].' '.$row['lname'] .'</td>
							<td style = "font-size: 15px"><a type = "button" class = "btn btn-primary" href = "?rep='.$_GET['rep'].'&acc_id='.$row['account_id'] .'"name = "submit"> '.$title.' Report </a></td></tr>
							';
					}
				}
			?>
			</tbody>
		</table>
	</form>
</div>
<div id = "report">
<?php
	include("conf.php");
	$date17 = date("d");
	$dated = date("m");
	$datey = date("Y");
	if($date17 == 1){
		$date17 = 16;
		$dated = date("m") - 1;
	}
	if($date17 > 16){
		$forque = 16;
		$endque = 32;
	}else{
		$forque = 1;
		$endque = 16;
	}
	if(!isset($_GET['rep'])){
		$_GET['rep'] = "";
	}
	if(!isset($_GET['acc_id'])){
		$_GET['acc_id'] = 0;
	}
	if($_GET['rep'] == 'ot' && $_GET['acc_id'] > 0){
		$accid = $_GET['acc_id'];
		include("conf.php");
		$cutoffdate = date("Y-m-d");
		$sql = "SELECT * FROM overtime where overtime.account_id = $accid and state = 'AAdmin' and DAY(dateofot) >= $forque and DAY(dateofot) < $endque and MONTH(dateofot) = $dated and YEAR(dateofot) = $datey ORDER BY datefile ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get" style = "margin-top: -30px">
		<table class = "table table-hover" align = "center">
			<thead>
				<tr>
					<td colspan = 7 align = center><h2> Overtime Report </h2></td>
				</tr>
				<tr>
					<th>Date File</th>					
					<th>Name of Employee</th>
					<th>Date of Overtime</th>
					<th>From - To (Overtime)</th>
					<th>Reason</th>
					<th>Offical Work Schedule</th>
					<th>State</th>
				</tr>
			</thead>
			<tbody>
	<?php
		$cutofftime2 = 0;	
		while($row = $result->fetch_assoc()){
			//end of computation
			$date17 = date("d");
			$dated = date("m");
			$datey = date("Y");		
	
			$originalDate = date($row['datefile']);
			$newDate = date("F j, Y", strtotime($originalDate));
			
			echo
				'<tr>
					<td>'.$newDate.'</td>
					<td>'.$row["nameofemp"].'</td>
					<td>'.date("F j, Y", strtotime($row["dateofot"])).'</td>
					<td>'.$row["startofot"] . ' - ' . $row['endofot']. ' / <strong>OT: '. $row['approvedothrs'].'</strong></td>					
					<td width = 300 height = 70>'.$row["reason"].'</td>
					<td>'.$row["officialworksched"].'</td>					
					<td><b>';
						if($row['state'] == 'UA'){
							echo 'Pending';
						}else if($row['state'] == 'AHR'){
							echo '<p><font color = "green">Approved by HR</p>';
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</p>';
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</p>';
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</p>';
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</p>';
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</p>';
						}
					echo '</td></tr>';
		}
		?>
		</tbody>
	</table>
</form>
<?php	
	$conn->close();
?>
<?php
	include("conf.php");
	$date17 = date("d");
	$dated = date("m");
	$datey = date("Y");
	if($date17 == 1){
		$date17 = 16;
		$dated = date("m") - 1;
	}
	if($date17 > 16){
		$forque = 16;
		$endque = 31;
	}else{
		$forque = 1;
		$endque = 16;
	}
	$sql = "SELECT * FROM overtime where overtime.account_id = $accid and DAY(dateofot) >= $forque and DAY(dateofot) < $endque and MONTH(dateofot) = $dated and YEAR(dateofot) = $datey ";
	$result = $conn->query($sql);
	if($result->num_rows > 0){
		$cutofftime2 = 0;	
		$hours12 = 0;
		$minutes12 = 0;
		$seconds1 = 0;
		while($row = $result->fetch_assoc()){
		//hrs:minutes computation
		$time1 = substr($row['startofot'],0,4);
		$time2 = substr($row['endofot'],0,4);
		list($hours, $minutes) = explode(':', $time1);
		$startTimestamp = mktime($hours, $minutes);
		list($hours, $minutes) = explode(':', $time2);
		$endTimestamp = mktime($hours, $minutes);
		$seconds = $endTimestamp - $startTimestamp;
		$minutes = ($seconds / 60) % 60;
		$hours = floor($seconds / (60 * 60));
		$dated = date("F");
		$cutoffs = date("Y-m-16");
		
		if($row['state'] == 'AAdmin' && $row['dateofot'] >= $cutoffs){	
			$cutoffdate = '16 - 31';	
			$hrs1 = substr($row['approvedothrs'],0,4);
			$min1 = substr($row['approvedothrs'],0,4);
			list($hours1, $minutes1) = explode(':', $hrs1);
			$startTimestamp1 = mktime($hours1, $minutes1);
			list($hours1, $minutes1) = explode(':', $min1);
			$endTimestamp1 = mktime($hours1, $minutes1);
			$seconds1 =$seconds1 + $endTimestamp1 - $startTimestamp1;
			$minutes1 =$minutes1 + ($seconds1 / 60) % 60;
			$hours1 = $hours1 +floor($seconds1 / (60 * 60));
			$hours12 += $hours1;
			$minutes12 += $minutes1;
		}else if($row['state'] == 'AAdmin' && $row['dateofot'] < $cutoffs){
			$cutoffdate = '1 - 15';
			$hrs1 = substr($row['approvedothrs'],0,4);
			$min1 = substr($row['approvedothrs'],0,4);
			list($hours1, $minutes1) = explode(':', $hrs1);
			$startTimestamp1 = mktime($hours1, $minutes1);
			list($hours1, $minutes1) = explode(':', $min1);
			$endTimestamp1 = mktime($hours1, $minutes1);
			$seconds1 =$seconds1 + $endTimestamp1 - $startTimestamp1;
			$minutes1 =$minutes1 + ($seconds1 / 60) % 60;
			$hours1 = $hours1 +floor($seconds1 / (60 * 60));
				
			$hours12 += $hours1;
			$minutes12 += $minutes1;
			}
		}
		$date17 = date("d");
		if($date17 == 1){
		$date17 = 16;
			$dateda = date("Y-m-d");
			$datade = date('F',strtotime($dateda . "-1 Month"));
		}else{
			$datade = date("F") ;
		}
		$hours12 = $hours12 * 60;
		$minutetosec = $minutes12 * 60;
		$totalmin = $hours12 + $minutes12;
		$totalothrs = date('H : i', mktime(0,$totalmin));
		echo '<div align = "center" style = "font-size: 19px;">Total OT: <strong>'. substr($totalothrs,0,2) . ' Hour/s' . substr($totalothrs,4,5). ' Min/s</strong> for '.$datade.' '.$cutoffdate.', '.$datey.'<br>';
		echo '<a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
	}
	}else{
		if($date17 >= 16){
			$cutoff = date('F 16-31, Y');
		}else{
			$cutoff = date('F 1-15, Y');
		}
		echo '<div align = "center">No Records for this Cutoff: <strong>'.$cutoff.'</strong><br>';
		echo '<a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
	}
}
	
?>

<?php
	include("conf.php");
	$date17 = date("d");
	$dated = date("m");
	$datey = date("Y");
	if($date17 >= 16){
		$forque = 16;
		$endque = 31;
	}else{
		$forque = 1;
		$endque = 15;
	}
	if(!isset($_GET['rep'])){
		$_GET['rep'] = "";
	}
	if(!isset($_GET['acc_id'])){
		$_GET['acc_id'] = 0;
	}
	if($_GET['rep'] == 'ob' && $_GET['acc_id'] > 0){
		$accid = $_GET['acc_id'];
		include("conf.php");
		$cutoffdate = date("Y-m-d");
		$sql = "SELECT * FROM officialbusiness where officialbusiness.account_id = $accid and state = 'AAdmin' and DAY(obdatereq) >= $forque and DAY(obdatereq) <= $endque and MONTH(obdatereq) = $dated and YEAR(obdatereq) = $datey ORDER BY obdate ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get" style = "margin-top: -30px;">
		<table class = "table table-hover" align = "center">
			<thead>
				<tr>
					<td colspan = 9 align = center><h2> Official Business Report </h2></td>
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
		$cutofftime2 = 0;	
		while($row = $result->fetch_assoc()){
			//end of computation
			$date17 = date("d");
			$dated = date("F");
			$datey = date("Y");	
			$originalDate = date($row['obdate']);
			$newDate = date("F j, Y", strtotime($originalDate));
			echo
				'<tr>
					<td>'.$newDate.'</td>
					<td>'.$row["obename"].'</td>
					<td>'.$row["obpost"].'</td>
					<td >'.$row["obdept"].'</td>
					<td>'.date("F j, Y",strtotime($row['obdatereq'])).'</td>					
					<td>'.$row["obtimein"] . ' - ' . $row['obtimeout'].'</td>
					<td>'.$row["officialworksched"].'</td>				
					<td >'.$row["obreason"].'</td>					
					<td><b>';
						if($row['state'] == 'UA'){
							echo 'Pending';
						}else if($row['state'] == 'AHR'){
							echo '<p><font color = "green">Approved by HR</p>';
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</p>';
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</p>';
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</p>';
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</p>';
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</p>';
						}
					echo '<td></tr>';
		}
		?>
		</tbody>
	</table>
</form>
<?php	echo '<div align = "center"><a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
		}else{
			if($date17 >= 16){
			$cutoff = date('F 16-31, Y');
			}else{
				$cutoff = date('F 1-15, Y');
			}
			echo '<div align = "center">No Records for this Cutoff: <strong>'.$cutoff.'</strong><br>';
			echo '<a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
		}
	}
	$conn->close();
?>
	
<?php
	include("conf.php");
	$date17 = date("d");
	$dated = date("m");
	$datey = date("Y");
	if($date17 >= 16){
		$forque = 16;
		$endque = 31;
	}else{
		$forque = 1;
		$endque = 15;
	}
	if(!isset($_GET['rep'])){
		$_GET['rep'] = "";
	}
	if(!isset($_GET['acc_id'])){
		$_GET['acc_id'] = 0;
	}
	if($_GET['rep'] == 'lea' && $_GET['acc_id'] > 0){
		$accid = $_GET['acc_id'];
		include("conf.php");
		$cutoffdate = date("Y-m-d");
		$sql = "SELECT * FROM nleave where nleave.account_id = $accid and state = 'AAdmin' and DAY(datefile) >= $forque and DAY(datefile) <= $endque and MONTH(datefile) = $dated and YEAR(datefile) = $datey ORDER BY datefile ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get" style = "margin-top: -30px;">
		<table class = "table table-hover" align = "center">
			<thead>
					<tr>
						<td colspan = 10 align = center><h2> Leave Report </h2></td>
					</tr>
					<tr>
						<th width = "170">Date File</th>
						<th width = "170">Name of Employee</th>
						<th width = "170">Date Hired</th>
						<th>Department</th>
						<th>Position</th>
						<th width = "250">Date of Leave (Fr - To)</th>
						<th width = "100"># of Day/s</th>
						<th width = "170">Type of Leave</th>
						<th>Reason</th>
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
					<td>'.date("F j, Y", strtotime($row["datehired"])).'</td>
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
							echo '<p><font color = "green">Approved by HR</p>';
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</p>';
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</p>';
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</p>';
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</p>';
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</p>';
						}
					echo '<td></tr>';
		}
		?>
		</tbody>
	</table>
</form>
<?php	
		echo '<div align = "center"><a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
		}else{
			if($date17 >= 16){
				$cutoff = date('F 16-31, Y');
			}else{
				$cutoff = date('F 1-15, Y');
			}
			echo '<div align = "center">No Records for this Cutoff: <strong>'.$cutoff.'</strong><br>';
			echo '<a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
	}
}
$conn->close();
?>
	
<?php
	include("conf.php");
	$date17 = date("d");
	$dated = date("m");
	$datey = date("Y");
	if($date17 >= 16){
		$forque = 16;
		$endque = 31;
	}else{
		$forque = 1;
		$endque = 15;
	}
	if(!isset($_GET['rep'])){
		$_GET['rep'] = "";
	}
	if(!isset($_GET['acc_id'])){
		$_GET['acc_id'] = 0;
	}
	if($_GET['rep'] == 'undr' && $_GET['acc_id'] > 0){
		$accid = $_GET['acc_id'];
		include("conf.php");
		$cutoffdate = date("Y-m-d");
		$sql = "SELECT * FROM undertime where undertime.account_id = $accid and state = 'AAdmin' and DAY(dateofundrtime) >= $forque and DAY(dateofundrtime) <= $endque and MONTH(dateofundrtime) = $dated and YEAR(dateofundrtime) = $datey ORDER BY datefile ASC";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
	?>
	<form role = "form" action = "approval.php"    method = "get" style = "margin-top: -30px;">
		<table class = "table table-hover" align = "center">
			<thead>				
				<tr>
					<td colspan = 7 align = center><h2> Undertime Report </h2></td>
				</tr>
				<tr >
					<th>Date File</th>
					<th>Date of Undertime</th>
					<th>Name of Employee</th>
					<th>Reason</th>
					<th>From - To (Overtime)</th>
					<th>Number of Hrs/Minutes</th>
					<th>State</th>
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
							echo '<p><font color = "green">Approved by HR</p>';
						}else if($row['state'] == 'AACC'){
							echo '<p><font color = "green">Approved by Accounting</p>';
						}else if($row['state'] == 'AAdmin'){
							echo '<p><font color = "green">Approved by Dep. Head</p>';
						}else if($row['state'] == 'DAHR'){
							echo '<p><font color = "red">Dispproved by HR</p>';
						}else if($row['state'] == 'DAACC'){
							echo '<p><font color = "red">Dispproved by Accounting</p>';
						}else if($row['state'] == 'DAAdmin'){
							echo '<p><font color = "red">Dispproved by Dep. Head</p>';
						}
					echo '<td></tr>';
		}
		?>
		</tbody>
	</table>
</form>
<?php	
	echo '<div align = "center"><a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';	
	}else{
		if($date17 >= 16){
			$cutoff = date('F 16-31, Y');
		}else{
			$cutoff = date('F 1-15, Y');
		}
		echo '<div align = "center" id = "userlist">No Records for this Cutoff: <strong>'.$cutoff.'</strong><br>';
		echo '<a class = "btn btn-primary" href = "acc-report.php?&rep='.$_GET['rep'].'">Back</a></div>';
	}
}
	$conn->close();
?>
</div>
<?php include("req-form.php");?>
<?php include('footer.php');?>