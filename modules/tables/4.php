<?
		
	$exestatus = exec('tasklist /FI "IMAGENAME eq '.$gameexe.'" /FO CSV');
	$exestatus = explode(",", strtolower($exestatus));
	$exestatus = $exestatus[0];
	$exestatus = str_replace('"', "", $exestatus);
	
	if ($exestatus == strtolower($gameexe)){
		$serverrunning = true;
		$delresult .= '<div id="message-red">
				<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="red-left">Server is online!</td>
					<td class="red-right"><a class="close-red"><img src="'.$path.'images/table/icon_close_red.gif" alt="" /></a></td>
				</tr>
				</table>
				</div>';
	} else {
		$serverrunning = false;
		$delresult .= '<div id="message-yellow">
				<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="yellow-left">Server is offline!</td>
					<td class="yellow-right"><a class="close-yellow"><img src='.$path.'images/table/icon_close_yellow.gif" alt="" /></a></td>
				</tr>
				</table>
				</div>';
	}
	
	//////
	

	////
	
	if (isset($_POST["vehicle"])){
		$aDoor = $_POST["vehicle"];
		$N = count($aDoor);
		for($i=0; $i < $N; $i++)
		{
			$query2 = "SELECT * FROM objects WHERE id = ".$aDoor[$i].""; 
			$res2 = mysql_query($query2) or die(mysql_error());
			while ($row2=mysql_fetch_array($res2)) {
				$query2 = "INSERT INTO `logs`(`action`, `user`, `timestamp`) VALUES ('DELETE VEHICLE: ".$row2['otype']." - ".$row2['uid']."','{$_SESSION['login']}',NOW())";
				$sql2 = mysql_query($query2) or die(mysql_error());
				$query2 = "DELETE FROM `objects` WHERE id='".$aDoor[$i]."'";
				$sql2 = mysql_query($query2) or die(mysql_error());
				$delresult .= '<div id="message-green">
				<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="green-left">Vehicle '.$row2['otype'].' - '.$row2['uid'].' successfully removed!</td>
					<td class="green-right"><a class="close-green"><img src="'.$path.'images/table/icon_close_green.gif" alt="" /></a></td>
				</tr>
				</table>
				</div>';
			}		
			//echo($aDoor[$i] . " ");
		}
		//echo $_GET["deluser"];
	}
	
	
	error_reporting (E_ALL ^ E_NOTICE);
	
	$res = mysql_query($query) or die(mysql_error());
	$pnumber = mysql_num_rows($res);			

	if(isset($_GET['page']))
	{
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	$maxPage = ceil($pnumber/$rowsPerPage);			

	for($page = 1; $page <= $maxPage; $page++)
	{
	   if ($page == $pageNum)
	   {
		  $nav .= " $page "; // no need to create a link to current page
	   }
	   else
	   {
		  $nav .= "$self&page=$page";
	   }
	}

			
	$query = $query." LIMIT ".$offset.",".$rowsPerPage;
	$res = mysql_query($query) or die(mysql_error());
	$number = mysql_num_rows($res);
	
	$chbox = "";
	
	if (!$serverrunning){ 
		$chbox = "<th class=\"table-header-repeat line-left\"><a href=\"\">Delete</a></th>";
		$formhead = '<form action="index.php?view=table&show=4" method="post">';
		$formfoot = '<input type="submit" class="submit-login"  /></form>';
	} 
	
	$tableheader = '
		<tr>'.$chbox.'
		<th class="table-header-repeat line-left"><a href="">ID</a></th>
		<th class="table-header-repeat line-left minwidth-1"><a href="">Classname</a>	</th>
		<th class="table-header-repeat line-left minwidth-1"><a href="">Object UID</a></th>
		<th class="table-header-repeat line-left"><a href="">Damage</a></th>
		<th class="table-header-repeat line-left"><a href="">Position</a></th>
		<th class="table-header-repeat line-left"><a href="">Inventory</a></th>
		<th class="table-header-repeat line-left"><a href="">Hitpoints</a></th>
		</tr>';
		
	while ($row=mysql_fetch_array($res)) {
		$Worldspace = str_replace("[", "", $row['pos']);
		$Worldspace = str_replace("]", "", $Worldspace);
		$Worldspace = str_replace("|", ",", $Worldspace);
		$Worldspace = explode(",", $Worldspace);
		if (!$serverrunning){ 
			$chbox = "<td><input name=\"vehicle[]\" value=\"".$row['id']."\" type=\"checkbox\"/></td>";
		}
		$tablerows .= "<tr>".$chbox."
			<td><a href=\"index.php?view=info&show=4&id=".$row['id']."\">".$row['id']."</a></td>
			<td><a href=\"index.php?view=info&show=4&id=".$row['id']."\">".$row['otype']."</a></td>			
			<td><a href=\"index.php?view=info&show=4&id=".$row['id']."\">".$row['uid']."</a></td>
			<td>".$row['damage']."</td>
			<td>top:".round((154-($Worldspace[2]/100)))." left:".round(($Worldspace[1]/100))."</td>
			<td>".substr($row['inventory'], 0, 40) . "...</td>
			<td>".substr($row['health'], 0, 40) . "...</td>
		</tr>";
		}
	include ('paging.php');
?>