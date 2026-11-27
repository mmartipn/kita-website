<?php

	if ($uty_id == 1) // parent
	{
		
		//Meine Tauschanfragen
		echo '<h2>Meine Tauschangebote:</h2>';
		$response = callServer ($_def_general_server."kid.php?function=getSwitch", array("data"=>array("usr_index"=>$usr_id, "type"=>"mine")));
		if (isset ($response['data']))
		{
			if (sizeof($response['data'])>0)
			{
				echo '		
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Kind</b></td>
								<td><b>Datum</b></td>
								<td><b>hat<br>(habe ich jetzt)</b></td>
								<td><b>sucht<br>(habe ich danach)</b></td>
								<td><b>Aktion</b></td>
							</tr>';
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td class="wb">'.$value["usr_name"].'</td>
										<td>'.$value["swi_date"].'</td>
										<td>'.abspoints2Text($value["swi_abs_points__from"]).'</td>
										<td>'.abspoints2Text($value["swi_abs_points__to"]).'</td>
										<td><form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="swi_index" value="'.$value["swi_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div>';
			}
		}

		echo '
		<input value="neues Tauschangebot erstellen"  class="button small" type="button" onclick="toggledivwb(\'swi0\')">
		<div class="modal" id="swi0" aria-hidden="true" style="display:none;">
		<form action="index_save.php?m_id='.$m_id.'" id="frmswi" name="frmswi" method="post">
			<div class="modal-dialog">
				<div class="modal-body">
				<h2>Tauschangebot erstellen</h2>
					<input type="hidden" name="action" value="ins">
					<input type="hidden" name="usr_index" value="'.$usr_id.'">
					<table>
					<tr>
					<td><b>Ich biete:</b></td>
					<td>
						<div class="select-wrapper"><select name="swi_date">';
						$response = callServer ($_def_general_server."kid.php?function=getSwitchDates", array("data"=>array("usr_index"=>$usr_id)));
						if (isset ($response['data']))
						{
							foreach ($response['data'] as $value) 
							{
								echo '<option value="'.$value["swi_date"].'">am '.germanDateAsText($value["swi_date"]).' '.abspoints2Text($value["swi_abs_points__from"]).'</option>';
							}
						}
						echo '</select></div>
					</td>
					</tr>
					<tr>
					<td><b>Ich suche:</b></td>
					<td>
						<div class="select-wrapper"><select name="swi_abs_points__to">';
						echo '<option value="0">'.abspoints2Text(0).'</option>';
						echo '<option value="3">'.abspoints2Text(3).'</option>';
						echo '<option value="7">'.abspoints2Text(7).'</option>';
						echo '
						</select></div>
					</td>
					</tr>
					</table>
				</div>
				<div class="modal-footer"> 
					<input value="abbrechen"  class="button" type="button" onclick="toggledivwb(\'swi0\')">
					<input value="erstellen" class="button" type="submit">
				</div>
			</div>
			</form>
		</div>';
		
		//offene Tauschanfragen
		echo '<br>
					<hr>
					<br><h2>Offene Tauschangebote:</h2>';
		$response = callServer ($_def_general_server."kid.php?function=getSwitch", array("data"=>array("usr_index"=>$usr_id, "type"=>"open")));
		if (isset ($response['data']))
		{
			if (sizeof($response['data'])>0)
			{
				echo '
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Kind</b></td>
								<td><b>Datum</b></td>
								<td><b>hat<br>(habe ich danach)</b></td>
								<td><b>sucht<br>(habe ich jetzt)</b></td>
								<td><b>Aktion</b></td>
							</tr>';
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td class="wb">'.$value["usr_name"].'</td>
										<td>'.$value["swi_date"].'</td>
										<td>'.abspoints2Text($value["swi_abs_points__from"]).'</td>
										<td>'.abspoints2Text($value["swi_abs_points__to"]).'</td>
										<td><form action="index_save.php?m_id='.$m_id.'" method="post">
												<input type="hidden" name="action" value="upd">
												<input type="hidden" name="swi_index" value="'.$value["swi_index"].'">
												<input type="hidden" name="swi_date" value="'.$value["swi_date_real"].'">
												<input type="hidden" name="swi_abs_points__from" value="'.$value["swi_abs_points__from"].'">
												<input type="hidden" name="swi_abs_points__to" value="'.$value["swi_abs_points__to"].'">
												<input type="hidden" name="swi_date" value="'.$value["swi_date_real"].'">
												<input type="hidden" name="usr_index" value="'.$usr_id.'">
												<input type="hidden" name="swi_usr_index__from" value="'.$value["swi_usr_index__from"].'">
												<input value="tauschen" class="button small" type="submit">
											</form>
										</td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div>';
			}
		}
		
	}
	elseif ($uty_id == 2) // Erzieher
	{
		echo '<h2>Aufhilfen:</h2><br>';
		$response = callServer ($_def_general_server."tempstaff.php?function=getJobs", array("data"=>array("type"=>"all")));
		if (isset ($response['data']))
		{
			if (sizeof($response['data'])>0)
			{
				echo '
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Datum</b></td>
								<td><b>Aushilfen (Uhrzeit)</b></td>
							</tr>';
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td>'.$value["job_date"].'</td>
										<td>'.$value["joas"].'</td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div>';
			}
			else
			{
				echo 'im Moment stehen keine Termine mit Aushilfen an.';
			}
		}	
			
	}
	elseif ($uty_id == 3) // Aushilfe
	{
		//meine bevorstehenden Zusagen
		$response = callServer ($_def_general_server."tempstaff.php?function=getAnswers", array("data"=>array("usr_index"=>$usr_id, "type"=>"mine")));
		if (isset ($response['data']))
		{
			if (sizeof($response['data'])>0)
			{
				echo '<h2>Meine Zu- und Absagen:</h2>';
				echo '
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Datum</b></td>
								<td><b>Uhrzeit</b></td>
								<td><b>Status</b></td>
							</tr>';
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td>'.$value["job_date"].'</td>
										<td>'; if ($value["joa_status"] != 2) echo $value["joa_from"].' - '.$value["joa_to"]; echo '</td>
										<td>'; 
											if ($value["joa_status"] == 0) echo 'zugesagt. Warte auf Best&auml;tigung. <form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="joa_index" value="'.$value["joa_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form>';
											elseif ($value["joa_status"] == 1) echo 'zugesagt';
											else 
											{
												echo 'abgesagt ';
												if ($value["job_status"] == 0)
													echo '<form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="joa_index" value="'.$value["joa_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form>';
											}
											echo '
										</td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div><br>
				<hr>';
			}
		}
		//offene Jobangebote
		$response = callServer ($_def_general_server."tempstaff.php?function=getJobs", array("data"=>array("type"=>"open", "usr_index"=>$usr_id)));
		if (isset ($response['data']))
		{
			if (sizeof($response['data'])>0)
			{
				
				echo '
				<br><h2>Meine Antwort fehlt:</h2>
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Datum</b></td>
								<td><b>Uhrzeit</b></td>
								<td><b>Status</b></td>
							</tr>';
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td>'.$value["job_date_s"].'</td>
										<td>'.$value["job_from"].' - '.$value["job_to"].'</td>
										<td>
										<input value="antworten" class="button small" onclick="togglediv(\'job'.$value["job_index"].'\')" id="antwortenjob'.$value["job_index"].'" >
										<div id="job'.$value["job_index"].'" name="job'.$value["job_index"].'" style="display:none;">
										<form action="index_save.php?m_id='.$m_id.'" method="post">
											<input type="hidden" name="action" value="upd">
											<input type="hidden" name="joa_status" value="0">
											<input type="hidden" name="job_index" value="'.$value["job_index"].'">
											<input type="hidden" name="usr_index" value="'.$usr_id.'">
											
										  <table><tbody>
												<tr>
													<td class="nb">von</td>
													<td class="nb">
														<div class="select-wrapper">
															<select name="joa_from">';
															$from_a = explode(":", $value["job_from"]);
															for($i=intval($from_a[0]); $i<=9; $i++)
															{
																echo '<option value="'.$i.':00" '.isseltime($i.":00", $value["job_from"]).'>'.$i.':00</option>';
															}
															echo '
															</select>
														</div>
													</td>
													<td class="nb">bis</td>
													<td class="nb">';
													echo '
														<div class="select-wrapper">
															<select name="joa_to">';
															$to_a = explode(":", $value["job_to"]);
															for($i=12; $i<=intval($to_a[0]); $i++)
															{
																echo '<option value="'.$i.':00" '.isseltime($i.":00", $value["job_to"]).'>'.$i.':00</option>';
															}
															echo '
															</select>
														</div>';
														
														echo '
													</td>
												</tr>
												<tr>
													<td colspan=2 class="nb"><input value="JA" class="button small" type="submit"></form></td>
													<td colspan=2 class="nbr">
													<form action="index_save.php?m_id='.$m_id.'" method="post">
														<input type="hidden" name="action" value="upd">
														<input type="hidden" name="joa_status" value="2">
														<input type="hidden" name="job_index" value="'.$value["job_index"].'">
														<input type="hidden" name="usr_index" value="'.$usr_id.'">
														<input type="hidden" name="joa_from" value="'.$value["job_from"].'">
														<input type="hidden" name="joa_to" value="'.$value["job_to"].'">
														<input value="NEIN" class="button small" type="submit">
													</form>
													</td>	
												</tr>
											</tbody></table>
										
											</div>	';
										echo '
										</td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div>';
			}
		}
	}
	elseif ($uty_id == 4) // Admin
	{
	
	}
	

?>