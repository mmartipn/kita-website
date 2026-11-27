<?php

	if ($uty_id == 1) // parent
	{
		echo '
		<h2>Abwesenheit melden:</h2>
		<form action="index_save.php?m_id='.$m_id.'" method="post" id="form_abs" onsubmit="return checkform(2)"><input type="hidden" name="action" value="ins" >
		<div class="table-wrapper">
			<table>
				<tr>
					<td>Kind</td>
					<td>
						<div class="select-wrapper">
							<select name="usr_index">';
							$response = callServer ($_def_general_server."kid.php?function=getKidsFromParent", array("data"=>array("usr_index"=>$usr_id)));
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["usr_index"].'">'.$value["usr_name"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>Grund</td>
					<td>
						<div class="select-wrapper">
							<select name="abr_index">';
							$response = callServer ($_def_general_server."kid.php?function=getAbsenceReason", array("data"=>array("abr_adminonlyselect"=>0)));
							
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["abr_index"].'">'.$value["abr_text"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr><td>Datum von - bis</td><td><input type="text" name="datefromto" id="datepicker"/></td></tr>
			</table>
		</div>
		<input value="speichern" class="button" type="submit">
		</form>
		<br>
		<hr>
		<br>
		<h2>geplante Abwesenheiten:</h2>
		<div class="table-wrapper">
			<table class="wb"><tbody>
				<tr>
					<td><b>Kind</b></td>
					<td><b>Grund</b></td>
					<td><b>Datum</b></td>
					<td><b>Aktion</b></td>
				</tr>';
				$response = callServer ($_def_general_server."kid.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						echo '
						<tr>
							<td>'.$value["usr_name"].'</td>
							<td>'.$value["abr_text"].'</td>
							<td>'.$value["abs_date"].'</td>
							<td>';
							if ($value["abs_abr_index"] < 3) echo '<form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form>';
							echo '</td>
						</tr>';

						/*if ( $value["abs_abr_index"] == 3)
						{
							$swi_index=0;
							$response2 = callServer ($_def_general_server."kid.php?function=getSwitch", array("data"=>array("abs_index"=>$value["abs_index"])));
							if (isset ($response2['data'])) foreach ($response2['data'] as $value2) { $swi_index = $value2["swi_index"]; }
							echo '
								<tr>
									<td>'.$value["usr_name"].'</td>
									<td>'.$value["abr_text"].'</td>
									<td>'.$value["abs_date"].'</td>
									<td>';
										if ($swi_index>0) echo 'Tauschanfrage gestellt';
										else echo '<form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="swi"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="tauschanfrage stellen" class="button small" type="submit"></form>';
									echo '
									</td>
								</tr>';
						}
						else
						{
							echo '
								<tr>
									<td>'.$value["usr_name"].'</td>
									<td>'.$value["abr_text"].'</td>
									<td>'.$value["abs_date"].'</td>
									<td><form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>
								</tr>';
						}*/
					}
				} echo '
			</tbody></table>
		</div>
		<br>
		<hr>
		<br>
		<h2>vergangene Abwesenheiten:</h2>
		<div class="table-wrapper">
		<table class="wb"><tbody>
				<tr>
					<td><b>Kind</b></td>
					<td><b>Grund</b></td>
					<td><b>Datum</b></td>
				</tr>';
				$response = callServer ($_def_general_server."kid.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>0)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
								<td>'.$value["usr_name"].'</td>
								<td>'.$value["abr_text"].'</td>
								<td>'.$value["abs_date"].'</td>
							</tr>';
					}
				} echo '
			</tbody></table>
		</div>';
		
	}
	elseif ($uty_id == 2) // Erzieher
	{
		echo '
		<h2>Abwesenheit melden:</h2>
		<form action="index_save.php?m_id='.$m_id.'" method="post" id="form_abs" onsubmit="return checkform(2)"><input type="hidden" name="action" value="ins" >
		<div class="table-wrapper">
			<table>
				<tr>
					<td>Name</td>
					<td>
						<div class="select-wrapper">
							<select name="usr_index">';
							$response = callServer ($_def_general_server."educator.php?function=getEducators", array("data"=>array("usr_index"=>$usr_id)));
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["usr_index"].'">'.$value["usr_name"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>Grund</td>
					<td>
						<div class="select-wrapper">
							<select name="abr_index">';
							$response = callServer ($_def_general_server."educator.php?function=getAbsenceReason", array("data"=>array("abr_adminonlyselect"=>0)));
							
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["abr_index"].'">'.$value["abr_text"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>Vertretung</td>
					<td>
						<div class="select-wrapper">
							<select name="usr_index__ver">
								<option value="0">noch keine</option>';
							$response = callServer ($_def_general_server."educator.php?function=getEducators", array("data"=>array("alleducators"=>2)));
							
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["usr_index"].'">'.$value["usr_name"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr><td>Datum von - bis</td><td><input type="text" name="datefromto" id="datepicker"/></td></tr>
			</table>
		</div>
		<input value="speichern" class="button" type="submit">
		</form>
		<br>
		<hr>
		<br>
		<h2>geplante Abwesenheiten:</h2>
		<div class="table-wrapper">
			<table class="wb"><tbody>
				<tr>
					<td><b>Name</b></td>
					<td><b>Grund</b></td>
					<td><b>Datum</b></td>
					<td><b>Aktion</b></td>
				</tr>';
				$response = callServer ($_def_general_server."educator.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
								<td>'.$value["usr_name"].'</td>
								<td>'.$value["abr_text"].'</td>
								<td>'.$value["abs_date"].'</td>
								<td>'; 
								if ($value["joa_count"] == 0) echo '<form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form>';
								echo '
								</td>
							</tr>';
					}
				} echo '
			</tbody></table>
		</div>
		<br>
		<hr>
		<br>
		<h2>vergangene Abwesenheiten:</h2>
		<div class="table-wrapper">
		<table class="wb"><tbody>
				<tr>
					<td><b>Name</b></td>
					<td><b>Grund</b></td>
					<td><b>Datum</b></td>
				</tr>';
				$response = callServer ($_def_general_server."educator.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>0)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
								<td>'.$value["usr_name"].'</td>
								<td>'.$value["abr_text"].'</td>
								<td>'.$value["abs_date"].'</td>
							</tr>';
					}
				} echo '
			</tbody></table>
		</div>';
		
	}
	elseif ($uty_id == 3) // Aushilfe
	{
		echo '
		<h2>Abwesenheit melden:</h2>
		<form action="index_save.php?m_id='.$m_id.'" method="post" id="form_abs" onsubmit="return checkform(2)"><input type="hidden" name="action" value="ins" >
		<div class="table-wrapper">
			<table>
				<tr>
					<td>Name</td>
					<td>
						<div class="select-wrapper">
							<select name="usr_index">';
							$response = callServer ($_def_general_server."tempstaff.php?function=getTempstaff", array("data"=>array("usr_index"=>$usr_id)));
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["usr_index"].'">'.$value["usr_name"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>Grund</td>
					<td>
						<div class="select-wrapper">
							<select name="abr_index">';
							$response = callServer ($_def_general_server."educator.php?function=getAbsenceReason", array("data"=>array("abr_adminonlyselect"=>0)));
							
							if (isset ($response['data']))
							{
								foreach ($response['data'] as $value) 
								{
									echo '<option value="'.$value["abr_index"].'">'.$value["abr_text"].'</option>';
								}	
							}
							echo '
							</select>
						</div>
					</td>
				</tr>
				<tr><td>Datum von - bis</td><td><input type="text" name="datefromto" id="datepicker"/></td></tr>
			</table>
		</div>
		<input value="speichern" class="button" type="submit">
		</form>
		<br>
		<hr>
		<br>
		<h2>geplante Abwesenheiten:</h2>
		<div class="table-wrapper">
			<table class="wb"><tbody>
				<tr>
					<td><b>Name</b></td>
					<td><b>Grund</b></td>
					<td><b>Datum</b></td>
					<td><b>Aktion</b></td>
				</tr>';
				$response = callServer ($_def_general_server."tempstaff.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
								<td>'.$value["usr_name"].'</td>
								<td>'.$value["abr_text"].'</td>
								<td>'.$value["abs_date"].'</td>
								<td><form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>
							</tr>';
					}
				} echo '
			</tbody></table>
		</div>
		<br>
		<hr>
		<br>
		<h2>vergangene Abwesenheiten:</h2>
		<div class="table-wrapper">
		<table class="wb"><tbody>
				<tr>
					<td><b>Name</b></td>
					<td><b>Grund</b></td>
					<td><b>Datum</b></td>
				</tr>';
				$response = callServer ($_def_general_server."tempstaff.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>0)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
								<td>'.$value["usr_name"].'</td>
								<td>'.$value["abr_text"].'</td>
								<td>'.$value["abs_date"].'</td>
							</tr>';
					}
				} echo '
			</tbody></table>
		</div>';
		
	}
	elseif ($uty_id == 4) // Admin
	{	
	}
	

?>