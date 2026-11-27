<?php
	if (($uty_id == 1) || ($uty_id == 2))
	{
	echo '<h2>kommende Termine:</h2>';
	$response = callServer ($_def_general_server."kid.php?function=getEvents", array("data"=>array("usr_index"=>$usr_id, "uty_index"=>$uty_id)));
	if (isset ($response['data']))
	{
		if (sizeof ($response['data']) > 0)
		{
			$savedate = '';
			$counter = 0;
			echo '
				<div class="table-wrapper">
				<table  class="wb">
				<tbody>';
			foreach ($response['data'] as $value) 
			{
				$counter ++;
				$hide = false;
				
				echo '<tr '; 
				if ($counter>7) 
				{
					$style = "style='display:none;'"; if (isset ($_REQUEST['allev'])) {if ($_REQUEST['allev'] == 1) $style = ""; }
					echo 'class="result_tr" '.$style; 
				}	
				echo'><td>';
				if ($value["evt_date"] != $savedate)
				{
					$savedate = $value["evt_date"];
					echo $savedate;
				}
				echo '</td>
				<td><img src="images/evc/'.$value["evt_evc_index"].'.png" height="25px" style="padding-top:5px"></td>
				<td width=100%>'.$value["evt_header"];
				if (strlen($value["evt_text"]) > 0)
				{
					if ($value["evt_header"] != $value["evt_text"]) 
					{
						$style = " display:none;"; if (isset ($_REQUEST['openup'])) {if ($_REQUEST['openup'] == $value["evt_index"]) $style = ""; }

						echo '&nbsp;<input type="button" value="..." class="small" onclick="toggledivwb(\'evt'.$value["evt_index"].'\')">
						<div class="contenttgl" id="evt'.$value["evt_index"].'" style="padding-left:10px;'.$style.'">'.$value["evt_text"];
						if (strlen($value["evt_appendix"])>0) echo '<br><br><a href="uploads/app/'.$value["evt_appendix"].'" download>'.$value["evt_appendix"].'</a>';
						
						if (is_numeric($value["evt_index"]))
						{
							$response2 = callServer ($_def_general_server."kid.php?function=getEventlists", array("data"=>array("evt_index"=>$value["evt_index"])));
							if (isset ($response2['data']))
							{
								foreach ($response2['data'] as $value2) 
								{
									echo '
									<br><input value="Liste: '.$value2["lis_name"].'" class="button small" type="button" onclick="toggledivwb2(\'lis'.$value2["lis_index"].'\')">
									<div class="overlayblock" id="olis'.$value2["lis_index"].'" style="display:none;"></div>
									<div class="modalblock"  id="mlis'.$value2["lis_index"].'" style="display:none;">
										<b>'.$value2["lis_name"].'</b><br>
										'.$value2["lis_text"].'<br>
										<div class="table-wrapper">
										<table  class="wb">
											<tbody>
												<tr>
													<td><b>Aufgabe</b></td>';
													if ($value2["lis_commentenabled"] == 1) echo '<td><b>Kommentar</b></td>';
												echo '<td><b>Kind</b></td>
												</tr>';
												$response3 = callServer ($_def_general_server."kid.php?function=getEventlistItems", array("data"=>array("lis_index"=>$value2["lis_index"])));
												if (isset ($response3['data']))
												{
													foreach ($response3['data'] as $value3) 
													{
														echo '
															<tr><form action="index_save.php?m_id='.$m_id.'" method="post">
																<td class="wb">'.$value3["lie_text"].'</td>';
																echo '
																<input type="hidden" name="lie_index" value="'.$value3["lie_index"].'">
																<input type="hidden" name="openup" value="'.$value["evt_index"].'">
																<input type="hidden" name="allev" value="'.(($counter>7)?1:0).'">';
																if ($value3["lie_usr_index"]>0)
																{
																	
																	if ($value2["lis_commentenabled"] == 1) 
																		echo '<td>'.$value3["lie_comment"].'</td>';
																	
																	echo '<td>'.$value3["usr_name"];
																	if (intval($value3["lie_usr_index"]) == $usr_id)
																		echo '&nbsp;<input type="hidden" name="action" value="del"><input value="l&ouml;schen" class="button small" type="submit">';
																	echo '</td>';
																}
																else
																{
																	if ($value2["lis_commentenabled"] == 1) 
																		echo '<td><input type="text" name="lie_comment" value=""></td>';

																	echo '<td>
																	<input type="hidden" name="action" value="upd">
																	<input type="hidden" name="usr_index" value="'.$usr_id.'">
																	<input value="eintragen" class="button small" type="submit">
																	</td>';
																}
																
															echo '</form>
															</tr>';
													}
												}
												echo '
											</tbody>
										</table> 
										</div>
										<br><input value="schliessen" class="button small" type="button" onclick="toggledivwb2(\'lis'.$value2["lis_index"].'\')">
									</div>';
								}
							}
						}	
						echo '</div>';

					}	
				}

				echo '</td>';
		//		if ($counter >1) echo '</div>';
				echo '</tr>';
				if  ($counter==7)
					echo '<tr><td colspan="3" style="border: 0px solid black;"><input type="button" value="toggle alle Events" class="small" onclick="toggledivtr(\'result_tr\')"></td></tr>';
				
			}
			echo '<br>
				</tbody></table >
				</div>
				';
		}
	}		
	}
	/*
	<input value="antworten" class="button small" onclick="togglediv('.$value["job_index"].')" id="antworten'.$value["job_index"].'" >
										<div id="job'.$value["job_index"].'" name="job'.$value["job_index"].'" style="display:none;">
										*/
	
/*
	if ($uty_id == 1) // parent
	{
		echo '
		<h2>Aktuelles:</h2>';
		$response = callServer ($_def_general_server."kid.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1)));
		if (isset ($response['data']))
		{
			if (sizeof ($response['data']) > 0)
			{
				echo '<br>kommende Abwesenheiten deines Kindes:<br><br>
				<div class="table-wrapper">
				<table  class="wb">
				<tbody>
					<tr>
						<td><b>Datum</b></td>
						<td><b>Kind (Grund)</b></td>
					</tr>';
					foreach ($response['data'] as $value) 
					{
						$append = '('.$value["abr_text"].')';
						if (($value["abs_points"]>0) && ($value["abs_points"]<7))
							$append = "kommt bis ".(15-intval($value["abs_points"]))." Uhr(".$value["abr_text"].")";
						echo '
							<tr>
							<td>'.$value["abs_date"].'</td>
							<td>'.$value["usr_name"].' '.$append.'</td>
						</tr>';
					}
				echo '
				</tbody>
				</table>
				</div>';
			}
			else
			{
				echo 'Gute Nachrichten: im Moment sind keine Termine mit Notbetreuung in Sicht, an denen du dein Kind fr&uuml;her abholen musst oder gar nicht bringen kannst.';
			}
		}
		
		echo '<br>
			<hr>';
	}
	elseif ($uty_id == 2) // erzieher
	{
		echo '
		<h2>Aktuelles:</h2>';
		$response = callServer ($_def_general_server."educator.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1)));
		if (isset ($response['data']))
		{
			if (sizeof ($response['data']) > 0)
			{
				echo '<br>meine kommenden Abwesenheiten:<br><br>
				<div class="table-wrapper">
				<table  class="wb">
				<tbody>
					<tr>
						<td><b>Datum</b></td>
						<td><b>Name (Grund)</b></td>
					</tr>';
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
							<td>'.$value["abs_date"].'</td>
							<td>'.$value["usr_name"].' ('.$value["abr_text"].')</td>
						</tr>';
					}
				echo '
				</tbody>
				</table>
				</div><br>
			<hr>';
			}
		}
	}
	*/
	elseif ($uty_id == 3) // aushilfe
	{
		echo '
		<h2>Aktuelles:</h2>';
		$response = callServer ($_def_general_server."tempstaff.php?function=getAnswers", array("data"=>array("usr_index"=>$usr_id, "type"=>"mine_positive")));
		if (isset ($response['data']))
		{
			if (sizeof ($response['data']) > 0)
			{
				echo '<br>meine kommenden Dienste:<br><br>
				<div class="table-wrapper">
				<table  class="wb">
				<tbody>
					<tr>
						<td><b>Datum</b></td>
						<td><b>Uhrzeit</b></td>
					</tr>';
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
							<td>'.$value["job_date"].'</td>
							<td>'.$value["joa_from"].' - '.$value["joa_to"].'</td>
						</tr>';
					}
				echo '
				</tbody>
				</table>
				</div>
			<hr>';
			}
		} 
		$response = callServer ($_def_general_server."tempstaff.php?function=getJobs", array("data"=>array("type"=>"open", "usr_index"=>$usr_id)));
		if (isset ($response['data']))
		{
			if (sizeof ($response['data']) > 0)
			{
				echo 'Ausschreibungen wo ich noch zu- oder ab-sagen muss:<br><br>
				<div class="table-wrapper">
				<table  class="wb">
				<tbody>
					<tr>
						<td><b>Datum</b></td>
						<td><b>Uhrzeit</b></td>
					</tr>';
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
							<td>'.$value["job_date"].'</td>
							<td>'.$value["job_from"].' - '.$value["job_to"].'</td>
						</tr>';
					}
				echo '
				</tbody>
				</table>
				</div>
			<hr>';
			}
		}
		
		
		$response = callServer ($_def_general_server."tempstaff.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1)));
		if (isset ($response['data']))
		{
			if (sizeof ($response['data']) > 0)
			{
				echo 'meine kommenden Abwesenheiten:<br><br>
				<div class="table-wrapper">
				<table  class="wb">
				<tbody>
					<tr>
						<td><b>Datum</b></td>
						<td><b>Name (Grund)</b></td>
					</tr>';
					foreach ($response['data'] as $value) 
					{
						echo '
							<tr>
							<td>'.$value["abs_date"].'</td>
							<td>'.$value["usr_name"].' ('.$value["abr_text"].')</td>
						</tr>';
					}
				echo '
				</tbody>
				</table>
				</div><br>
			<hr>';
			}
		}
	}
	if (($uty_id == 2) || ($uty_id == 1))		
	{
		$abs_date = "".date("Y-m-d"); if (isset ($_POST['abs_date'])) $abs_date = $_POST['abs_date'];
		$abs_counter = 0;
		$response = callServer ($_def_general_server."educator.php?function=getAbsenceCounter", array("data"=>array("abs_date"=>$abs_date)));
		if (isset ($response['data']))
		{
			foreach ($response['data'] as $value) 
			{
				$abs_counter = $value["abs_counter"];
			}
		}

		echo '<br>
		<h2>Anwesenheitsliste f&uuml;r Datum</h2><br>
			<div class="table-wrapper">
				<table>
				<form action="index.php?m_id='.$m_id.'" method="post" id="pmfrm" >
				<tr>
				
					<td colspan="3">
					<input type="submit" name="btnmin" id="btnmin" class="small" value="-">
					<input type="date" value="'.$abs_date.'"  name="abs_date" id="datepicker2t"  ONCHANGE="pmfrm.submit()">
					<input type="submit" name="btnpls" id="btnpls" class="small" value="+">
					</td>
				</tr>
				</form>
			</table>
			</div>
			<br>';
		
			echo '
			<script>
			document.getElementById("btnpls").addEventListener("click", function () {
				var result = new Date(new Date("'.$abs_date.'").setDate(new Date("'.$abs_date.'").getDate() + 1));
			result = result.toLocaleDateString("en-CA");
			var dp = document.getElementById("datepicker2t");
			dp.value = result;
			  form.submit();
			});
			document.getElementById("btnmin").addEventListener("click", function () {
				var result = new Date(new Date("'.$abs_date.'").setDate(new Date("'.$abs_date.'").getDate() - 1));
			result = result.toLocaleDateString("en-CA");
			var dp = document.getElementById("datepicker2t");
			dp.value = result;
			  form.submit();
			});
			</script>
			';
			
		$response = callServer ($_def_general_server."kid.php?function=getNotbetreuungSuggestion", array("data"=>array("job_date"=>$abs_date)));
			
		
		echo '
		<div class="table-wrapper">
			<table class="wb"><tbody>
				<tr>
					<td><b>Kind</b></td>
					<td><b>Status</b></td>
				</tr>';
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						$status = "kommt";
						if ($value["abs_today"] == 1) // heute krank
							$status = "kommt nicht (krank/Urlaub)";
						
						if ($value["abs_ponits_new"] > 0)
						{
							if ($value["abs_ponits_new"] == 7)	
								$status = "kommt nicht (Personalmangel)";
							else
								$status = "kommt bis ".(15-intval($value["abs_ponits_new"]))." Uhr(Personalmangel)";
							
							if ($value["hsn"] == 0) $status = $status." ?";
						}
						
						if ($abs_counter == 3) $status = "Kita geschlossen";
							echo '
								<tr>
									<td>'.$value["usr_name"].'</td>
									<td>'.$status.'</td></tr>';
					}
				} 
						
				
				echo '
			</tbody></table>
		</div>';
		
		
		$response = callServer ($_def_general_server."educator.php?function=getAbsencePreview", array("data"=>array("abs_date"=>$abs_date)));
		
		echo '<br><br>
		<div class="table-wrapper">
			<table class="wb"><tbody>
				<tr>
					<td><b>Erzieher</b></td>
					<td><b>Status</b></td>
				</tr>';
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
						$status = "kommt";
						if (($value["abs_abr_index"] == 1) || ($value["abs_abr_index"] == 2) )
						{
							$status = "kommt nicht";
							if (strlen($value["aushilfe"])>0) $status = "Vertretung: ".$value["aushilfe"];
						}
						if ($abs_counter == 3) $status = "Kita geschlossen";
							echo '
								<tr>
									<td>'.$value["usr_name"].'</td>
									<td>'.$status.'</td>
								</tr>';
					}
				} echo '
			</tbody></table>
		</div>';

	}
	
		
	

?>