<?php
	include 'checkuser.php';
	include 'functions.php';
	include 'defines.php';

	$m_id = 1; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	
	if ($m_id == 6)
	{
		if (isset ($_SESSION['loggy_lid_id'])) unset($_SESSION['loggy_lid_id']); 
		if (isset ($_SESSION['loggy_usr_id'])) unset($_SESSION['loggy_usr_id']); 
		if (isset ($_SESSION['loggy_uty_id'])) unset($_SESSION['loggy_uty_id']);
		header ("Location: formular.php");
		exit();
	}
		
	$lid_id = 0; if (isset ($_SESSION['loggy_lid_id'])) $lid_id = $_SESSION['loggy_lid_id']; 
	$usr_id = 0; if (isset ($_SESSION['loggy_usr_id'])) $usr_id = $_SESSION['loggy_usr_id']; 
	$usr_ia = 0; if (isset ($_SESSION['loggy_usr_ia'])) $usr_ia = $_SESSION['loggy_usr_ia']; 
	
//	TODO: callserver isAdmin
	
	if ($usr_ia == 0)
	{
		header ("Location: admin.php");
		exit();
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body>
		<div class="page-wrap">
			<div class="wrsec">
				<!-- Nav -->
					<section id="header">
						<a href="admin.php" class="logo"><strong>Admin</strong></a>
						<nav id="nav">
							<?php
								echo '<a href="admin.php?m_id=1"'; if ($m_id == 1) echo ' class="menactive"'; echo '>Aktuelles</a>';
								echo '<a href="admin.php?m_id=2"'; if ($m_id == 2) echo ' class="menactive"'; echo '>Abwesenheit</a>';
								echo '<a href="admin.php?m_id=3"'; if ($m_id == 3) echo ' class="menactive"'; echo '>Auschreibungen</a>';
								echo '<a href="admin.php?m_id=4"'; if ($m_id == 4) echo ' class="menactive"'; echo '>Statistik</a>';
							//	echo '<a href="admin.php?m_id=8"'; if ($m_id == 8) echo ' class="menactive"'; echo '>Sonderfall</a>';
								echo '<a href="admin.php?m_id=9"'; if ($m_id == 9) echo ' class="menactive"'; echo '>Notbetreuung</a>';
								echo '<a href="admin.php?m_id=10"'; if ($m_id == 10) echo ' class="menactive"'; echo '>Events</a>';
								echo '<a href="admin.php?m_id=11"'; if ($m_id == 11) echo ' class="menactive"'; echo '>Listen</a>';
								echo '<a href="admin.php?m_id=12"'; if ($m_id == 12) echo ' class="menactive"'; echo '>W&auml;schedienst</a>';
								echo '<a href="admin.php?m_id=13"'; if ($m_id == 12) echo ' class="menactive"'; echo '>Dokumente</a>';
								echo '<a href="admin.php?m_id=5"'; if ($m_id == 5) echo ' class="menactive"'; echo '>Benutzer</a>';
								echo '<a href="admin.php?m_id=14"'; if ($m_id == 14) echo ' class="menactive"'; echo '>Arbeitszeiten</a>';
								echo '<a href="admin.php?m_id=7"'; if ($m_id == 7) echo ' class="menactive"'; echo '>Alles-Auf-Null</a>';
								if ($usr_ia == 1)
								{
									echo '<a href="index.php">Admin schliessen</a>';
								}
								echo '<a href="admin.php?m_id=6">Ausloggen</a>';
							?>	
						</nav>
						<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
					</section>
				<!-- Main -->
					<section id="main">
						<?php
							if ($m_id == 1) //Aktuelles
							{
								$uty_id = 1;
								include 'content/1.php';
							}
							elseif ($m_id == 2) //Abwesenheit
							{
								$a_id = 0; if (isset ($_REQUEST["a_id"])) $a_id = $_REQUEST["a_id"];
								echo '<div class="table-wrapper">
										<table class="wb"><tbody>
										<tr>
											<td class="blue'; if ($a_id == 1) echo 'active'; echo '"><b><a href="admin.php?m_id=2&a_id=1">Kind</a></b></td>
											<td class="blue'; if ($a_id == 2) echo 'active'; echo '"><b><a href="admin.php?m_id=2&a_id=2">Erzieher</a></b></td>
											<td class="blue'; if ($a_id == 3) echo 'active'; echo '"><b><a href="admin.php?m_id=2&a_id=3">Aushilfe</a></b></td>
										</tr>
										</tbody>
										</table>
									</div>';
								
								if ($a_id == 1)
								{
									echo '
									<h2>Abwesenheit Kind:</h2>
									<form action="admin_save.php?m_id='.$m_id.'&a_id='.$a_id.'" method="post"><input type="hidden" name="action" value="ins">
									<div class="table-wrapper">
										<table>
											<tr>
												<td>Kind</td>
												<td>
													<div class="select-wrapper">
														<select name="usr_index">';
														$response = callServer ($_def_general_server."kid.php?function=getKidsFromParent", array("data"=>array("usr_index"=>$usr_id, "allkids"=>1)));
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
														$response = callServer ($_def_general_server."kid.php?function=getAbsenceReason", array("data"=>array("abr_adminonlyselect"=>1)));
														
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
											<tr><td>Datum von - bis (einschliesslich)</td><td><input type="text" name="datefromto" id="datepicker"/></td></tr>
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
											$response = callServer ($_def_general_server."kid.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1, "allkids"=>1)));
											if (isset ($response['data']))
											{
												foreach ($response['data'] as $value) 
												{
													
														echo '
															<tr>
																<td>'.$value["usr_name"].'</td>
																<td>'.$value["abr_text"].'</td>
																<td>'.$value["abs_date"].'</td>
																<td><form action="admin_save.php?m_id='.$m_id.'&a_id='.$a_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>
															</tr>';
												}
											} echo '
										</tbody></table>
									</div>';
								}
								
								if ($a_id == 2)
								{
									echo '
										<h2>Abwesenheit Erzieher:</h2>
										<form action="admin_save.php?m_id='.$m_id.'&a_id='.$a_id.'" method="post" id="form_abs" onsubmit="return checkform(2)"><input type="hidden" name="action" value="ins" >
										<div class="table-wrapper">
											<table>
												<tr>
													<td>Kind</td>
													<td>
														<div class="select-wrapper">
															<select name="usr_index">';
															$response = callServer ($_def_general_server."educator.php?function=getEducators", array("data"=>array("usr_index"=>$usr_id, "alleducators"=>1)));
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
															$response = callServer ($_def_general_server."educator.php?function=getEducators", array("data"=>array("usr_index"=>$usr_id, "alleducators"=>2)));
															//$response = callServer ($_def_general_server."educator.php?function=getEducators", array("data"=>array("usr_index"=>$usr_id, "alleducators"=>1)));
															
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
												$response = callServer ($_def_general_server."educator.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1, "alleducators"=>1)));
												if (isset ($response['data']))
												{
													foreach ($response['data'] as $value) 
													{
														echo '
															<tr>
																<td>'.$value["usr_name"].'</td>
																<td>'.$value["abr_text"].'</td>
																<td>'.$value["abs_date"].'</td>
																<td><form action="admin_save.php?m_id='.$m_id.'&a_id='.$a_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>
															</tr>';
													}
												} echo '
											</tbody></table>
										</div>';
								}
								if ($a_id == 3)
								{
									echo '
										<h2>Abwesenheit Aushilfe:</h2>
										<form action="admin_save.php?m_id='.$m_id.'&a_id='.$a_id.'" method="post" id="form_abs" onsubmit="return checkform(2)"><input type="hidden" name="action" value="ins" >
										<div class="table-wrapper">
											<table>
												<tr>
													<td>Kind</td>
													<td>
														<div class="select-wrapper">
															<select name="usr_index">';
															$response = callServer ($_def_general_server."tempstaff.php?function=getTempstaff", array("data"=>array("usr_index"=>$usr_id, "alleducators"=>1)));
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
															$response = callServer ($_def_general_server."tempstaff.php?function=getAbsenceReason", array("data"=>array("abr_adminonlyselect"=>0)));
															
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
												$response = callServer ($_def_general_server."tempstaff.php?function=getAbsence", array("data"=>array("usr_index"=>$usr_id, "upcoming"=>1, "alleducators"=>1)));
												if (isset ($response['data']))
												{
													foreach ($response['data'] as $value) 
													{
														echo '
															<tr>
																<td>'.$value["usr_name"].'</td>
																<td>'.$value["abr_text"].'</td>
																<td>'.$value["abs_date"].'</td>
																<td><form action="admin_save.php?m_id='.$m_id.'&a_id='.$a_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="abs_index" value="'.$value["abs_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>
															</tr>';
													}
												} echo '
											</tbody></table>
										</div>';
								}
								
							}
							elseif ($m_id == 3) //Jobs
							{
								//Jobs
								$response = callServer ($_def_general_server."tempstaff.php?function=getJobs", array("data"=>array("type"=>"all")));
								if (isset ($response['data']))
								{
									if (sizeof($response['data'])>0)
									{
										
										echo '<br><h2>Jobs:</h2>';
										foreach ($response['data'] as $value) 
										{
											echo '
											<b>'.$value["job_date_s"].' | '.$value["job_from"].' - '.$value["job_to"].'</b><br>
											<div class="table-wrapper">
												<table class="wb"><tbody>
													<tr>
														<td><b>Aushilfe</b></td>
														<td><b>Status</b></td>
														<td><b>Aktion</b></td>
													</tr>';
											$response_a = callServer ($_def_general_server."tempstaff.php?function=getAnswers", array("data"=>array("job_index"=>$value["job_index"])));
											if (isset ($response_a['data']))
											{
												if (sizeof($response_a['data'])>0)
												{
													foreach ($response_a['data'] as $value_a) 
													{	
														echo '
															<tr>
																<td>'.$value_a["usr_name"].'</td>';
														if ($value_a["joa_status"] == "") //nng
														{
															$identifier = $value["job_index"].$value_a["usr_index"];
															 
															echo '<td>noch keine Antwort</td>';
															echo '<td>
															<input value="antworten" class="button small" onclick="togglediv(\'job'.$identifier.'\')" id="antwortenjob'.$identifier.'" >
															<div id="job'.$identifier.'" name="job'.$identifier.'" style="display:none;">
															<form action="admin_save.php?m_id='.$m_id.'" method="post">
																<input type="hidden" name="action" value="upd">
																<input type="hidden" name="joa_status" value="1">
																<input type="hidden" name="job_index" value="'.$value["job_index"].'">
																<input type="hidden" name="usr_index" value="'.$value_a["usr_index"].'">
																
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
																		<form action="admin_save.php?m_id='.$m_id.'" method="post">
																			<input type="hidden" name="action" value="upd">
																			<input type="hidden" name="joa_status" value="2">
																			<input type="hidden" name="job_index" value="'.$value["job_index"].'">
																			<input type="hidden" name="usr_index" value="'.$value_a["usr_index"].'">
																			<input type="hidden" name="joa_from" value="'.$value["job_from"].'">
																			<input type="hidden" name="joa_to" value="'.$value["job_to"].'">
																			<input value="NEIN" class="button small" type="submit">
																		</form>
																		</td>	
																	</tr>
																</tbody></table>
															
																</div></td>';
														}
														elseif ($value_a["joa_status"] == "0") //wab
														{
															echo '<td>abwarten (zugesagt: '.$value_a["joa_from"].'- '.$value_a["joa_to"].')</td>';
															echo '<td><form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="joa_index" value="'.$value_a["joa_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>';
														}
														elseif  ($value_a["joa_status"] == 1) //zugesagt
														{
															echo '<td style="background-color:green;">zugesagt ('.$value_a["joa_from"].'- '.$value_a["joa_to"].') </td>';
															echo '<td><form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="joa_index" value="'.$value_a["joa_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>';
														}
														elseif  ($value_a["joa_status"] == 2) //abgesagt
														{
															echo '<td>abgesagt</td>';
															echo '<td><form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del"><input type="hidden" name="joa_index" value="'.$value_a["joa_index"].'"><input value="l&ouml;schen" class="button small" type="submit"></form></td>';
														}	
													}
												}
											}
											echo '</tbody></table>
											</div>';
										}
									}
								}
											
							}
							elseif ($m_id == 4) //Statistik
							{
								echo '<h2>Tabelle:</h2><br>';
								$response = callServer ($_def_general_server."kid.php?function=getTable", array("data"=>array("usr_index"=>$usr_id)));
								if (isset ($response['data']))
								{
									echo '
											<div class="table-wrapper">
												<table class="wb"><tbody>
													<tr>
														<td><b>Platz</b></td>
														<td><b>Kind</b></td>
														<td><b>le.Abw.</b></td>
														<td><b>Punkte</b></td>
													</tr>';
													$platz = 1;
													foreach ($response['data'] as $value) 
													{
														echo '
															<tr>
																<td>'.$platz.'</td>
																<td>'.$value["usr_name"].'</td>
																<td>'.$value["abt_date"].'</td>
																<td>'.$value["abs_points"].'</td>
															</tr>';
														$platz ++;
													}
													echo '
												</tbody></table>
											</div>';
									
								}
								echo '<br>Hinweis: 
								Punkte sind hier gleichbedeutend mit Stunden. 
								Ein Tag hat immer 7 Stunden (8 Uhr bis 15 Uhr). 
								Das heisst: Wenn ein Kind aufgrund von Personalmangel einen Tag zu Hause bleiben muss, bekommt es 7 Punkte.
								Wenn es um 12 abgeholt werden muss, bekommt es 3 Punkte.<br>
								Je weiter oben das Kind in der Tabelle ist, umso wahrscheintlicher ist es, dass es beim n&auml;chsten Personalmangel nicht kommen kann oder fr&uuml;her abgeholt werden muss!<br><br>';
								
							}
							elseif ($m_id == 5) //Benutzer
							{	
								for ($a_id=1;$a_id<=3;$a_id++)
								{
									$typ = ""; if ($a_id==1) $typ = "Eltern"; elseif ($a_id==2) $typ = "Erzieher"; elseif ($a_id==3) $typ = "Aushilfen"; 
									echo '<h2>'.$typ.':</h2><br>
											<div class="table-wrapper">
												<table class="wb"><tbody>
													<tr>
														'; if ($a_id==1) echo '<td><b>Kind</b></td>'; echo '
														<td><b>Name</b></td>
														<td><b>Email</b></td>
														<td><b>Handy</b></td>
														<td><b>Benachrichtigung per</b></td>
														<td><b>neues PW</b></td>
														<td><b>aktiv</b></td>
														<td><b>admin</b></td>
														<td><b>gesperrt</b></td>
														<td><b>l&ouml;schen</b></td>
														<td><b>speichern</b></td>
													</tr>';
									$response = callServer ($_def_general_server."kid.php?function=getUsers",  array("data"=>array("uty_index"=>$a_id)));
									if (isset ($response['data']))
									{
										foreach ($response['data'] as $value) 
										{
											echo '
												<tr>
													<form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="upd">
													<input type="hidden" name="uty_index" value="'.$a_id.'">
													<input type="hidden" name="usr_index" value="'.$value["usr_index"].'">
													<input type="hidden" name="lin_index" value="'.$value["lin_index"].'">
														'; if ($a_id==1) echo '<td><input type="text" name="usr_name" value="'.$value["usr_name"].'"></td>'; echo '
														<td><input type="text" name="lin_name" value="'.$value["lin_name"].'"></td>
														<td><input type="text" name="lin_email" value="'.$value["lin_email"].'"></td>
														<td><input type="text" name="lin_phone" value="'.$value["lin_phone"].'"></td>
														<td><div class="select-wrapper"><select name="lin_benach"><option value="1" '.issel(1,$value["lin_phone__notify"]).'>Handy</option><option value="2" '.issel(1,$value["lin_email__notify"]).'>Email</option><option value="3" '.issel(2,($value["lin_phone__notify"]+$value["lin_email__notify"])).'>Handy & Email</option></select></div>
														<td><input type="text" name="lin_password" value=""></td>
														<td><input type="checkbox" name="lin_active" id="lin_active'.$value["lin_index"].'" '.ischk(1,$value["lin_active"]).' value="1"><label for="lin_active'.$value["lin_index"].'"></label></td>
														<td><input type="checkbox" name="lin_admin" id="lin_admin'.$value["lin_index"].'" '.ischk(1,$value["lin_admin"]).' value="1"><label for="lin_admin'.$value["lin_index"].'"></label></td>
														<td><input type="checkbox" name="lin_locked" id="lin_locked'.$value["lin_index"].'" '.ischk(1,$value["lin_locked"]).' value="1"><label for="lin_locked'.$value["lin_index"].'"></label></td>
														<td><input type="checkbox" name="lin_delete" id="lin_delete'.$value["lin_index"].'"  value="1"><label for="lin_delete'.$value["lin_index"].'"></label></td>
														<td><input type="submit" value="speichern"></td>
													</form>
												</tr>
											';
										}
									}
									echo '</tbody></table></div><br>';
								}
								echo '<h2>Neuen Nutzer anlegen:</h2><br>
										<div class="table-wrapper">
											<table class="wb"><tbody>
												<tr>
													<td><b>Typ</b></td>
													<td><b>Name Kind</b></td>
													<td><b>Name</b></td>
													<td><b>Email</b></td>
													<td><b>Handy</b></td>
													<td><b>Benachrichtigung per</b></td>
													<td><b>admin</b></td>
													<td><b>speichern</b></td>
												</tr>
												<form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="ins">
													<td><div class="select-wrapper"><select name="uty_index"><option value="1">Eltern</option><option value="2">Erzieher</option><option value="3">Aushilfe</option></select></div>
													<td><input type="text" name="usr_name" ></td>
													<td><input type="text" name="lin_name" ></td>
													<td><input type="text" name="lin_email" ></td>
													<td><input type="text" name="lin_phone" ></td>
													<td><div class="select-wrapper"><select name="lin_benach"><option value="1">Handy</option><option value="2">Email</option><option value="3">Handy & Email</option></select></div>
													<td><input type="checkbox" name="lin_admin" id="lin_admin0" value="1"><label for="lin_admin0"></label></td>
													<td><input type="submit" value="speichern"></td>
												</form>
											</tbody></table></div>	
												';
								
							}
							elseif ($m_id == 7) //Ales auf null
							{	
								echo '
									<form action="admin_save.php?m_id='.$m_id.'" method="post">
									<input type="checkbox" name="reset" id="reset" value="1">
									<label for="reset">Mit dem setzten des hakens und dem anschliessenden klick auf reset l&ouml;sche ich alle Abwesenheiten und Punkte</label><br>
									<input type="submit" value="reset">
									</form>';
							}
							
							elseif ($m_id == 8) //sonderfall
							{	
								echo 'Folgende Kinder beliben wie folgt daheim:<br>
									
										<form action="admin_save.php?m_id='.$m_id.'" method="post">
											<div class="table-wrapper">
												<table class="wb"><tbody>
													<tr>
														<td>datum</td>
														<td><input type="text" name="a_da" placeholder="dd.mm.jjjj"></td>
													</tr>
													<tr>
														<td>ganz daheim</td>
														<td><input type="text" name="a_gn"></td>
													</tr>
													<tr>
														<td>um 12 abgh.</td>
														<td><input type="text" name="a_12"></td>
													</tr>
													<tr>
														<td>um 14 abgh.</td>
														<td><input type="text" name="a_14"></td>
													</tr>
												</table>
												<br>
												<input type="submit" value="speichern">
											</div>	
										</form>';
							}
							elseif ($m_id == 9) //notbetreuung
							{	
							
								echo 'Notbetreuung:<br>';
								
								$job_date = ""; if (isset ($_REQUEST['job_date'])) $job_date = $_REQUEST['job_date'];
						
								if ($job_date == "")
								$response = callServer ($_def_general_server."kid.php?function=getNotbetreuungDates", array("data"=>array("past"=>1)));
										if (isset ($response['data']))
										{
											foreach ($response['data'] as $value) 
											{
												echo '<a href="admin.php?m_id='.$m_id.'&job_date='.$value["job_date"].'">'.$value["job_date"].'</a><br>';
											}	
										}	
								/*
								echo '<form action="admin.php?m_id='.$m_id.'" method="post">
										<div class="select-wrapper">
										<select name="job_date">';
										$response = callServer ($_def_general_server."kid.php?function=getNotbetreuungDates", array("data"=>array("past"=>0)));
										if (isset ($response['data']))
										{
											foreach ($response['data'] as $value) 
											{
												echo '<option value="'.$value["job_date"].'">'.$value["job_date"].'</option>';
											}	
										}
										echo '
										</select><br>
										<td>
													
										<input type="submit" value="anzeigen">
									</form>	
									<br>
									<hr>
									<br>';*/
								if (strlen($job_date) > 0)
								{	
									echo 'Erzieher am '.$job_date.':
									<div class="table-wrapper"><table class="wb"><tbody>
										<tr>
											<td><b>NAME</b></td>
											<td><b>VON</b></td>
											<td><b>BIS</b></td>
										</tr>';
									$response = callServer ($_def_general_server."kid.php?function=getNotbetreuungAtDate", array("data"=>array("usr_index"=>$usr_id, "job_date"=>$job_date)));
									if (isset ($response['data']))
									{
										foreach ($response['data'] as $value) 
										{
											echo '<tr>
													<td>'.$value["usr_name"].'</td>
													<td>'.$value["ros_from"].'</td>
													<td>'.$value["ros_to"].'</td>
												</tr>';
										}
									}
									
									echo '</tbody></table></div><br><hr><br>';
									
									$response = callServer ($_def_general_server."kid.php?function=getNotbetreuungSuggestion", array("data"=>array("usr_index"=>$usr_id, "job_date"=>$job_date)));
									if (isset ($response['data']))
									{	
										$retval = intval($response['retval']);
										
										if ($retval == 1) echo "<b>NEUER</b> VORSCHLAG<br><br>";
										elseif ($retval == 2) echo "<b>BESTEHENDER</b> VORSCHLAG<br><br>";
									
									echo '<form action="admin_save.php?m_id='.$m_id.'" method="post">
											<input type="hidden" name="abs_date" value="'.$job_date.'">
											<div class="table-wrapper">
												<table class="wb"><tbody>
												<tr>
													<td><b>Name</b></td>
													<td><b>le.Abw.</b></td>
													<td><b>Pkt</b></td>
													<td><b>krank</b></td>
													<td><b>0</b></td>
													<td><b>12</b></td>
													<td><b>14</b></td>
												</tr>	';
									$counter = 0;			
									
										foreach ($response['data'] as $value) 
										{
											$sick = ($value["abs_today"] == 1);
											$strike = ""; if ($sick) $strike = " class='strike'";
											echo '<tr>
													<td '.$strike.'>'.$value["usr_name"].'</td>
													<td '.$strike.'>'.germanDateAsText($value["abt_date"]).'</td>
													<td '.$strike.'>'.$value["abs_points__sum"].'</td>
													<td> <input type="checkbox"  value="1" name="absusr_k_'.$value["usr_index"].'" '.ischk($value["abs_today"], 1).'/><label for="absusr_k_'.$value["usr_index"].'"></label></td>
													<td> <input type="checkbox"  value="1" name="absusr_0_'.$value["usr_index"].'" '.ischk($value["abs_ponits_new"], 8).'/><label for="absusr_0_'.$value["usr_index"].'"></label></td>
													<td> <input type="checkbox"  value="1" name="absusr_12_'.$value["usr_index"].'" '.ischk($value["abs_ponits_new"], 4).'/><label for="absusr_12_'.$value["usr_index"].'"></label></td>
													<td> <input type="checkbox"  value="1" name="absusr_14_'.$value["usr_index"].'" '.ischk($value["abs_ponits_new"], 1).'/><label for="absusr_14_'.$value["usr_index"].'"></label></td>
												</tr>';
										}
										echo'
									</tbody>
												</table>
												<br>
												<input type="submit" value="speichern">
											</div>	
										</form>';
									}
								}
							}
							elseif ($m_id == 10) //events
							{
								$evcs = array();
								$response = callServer ($_def_general_server."kid.php?function=getEventCategories", array("data"=>array()));
								if (isset ($response['data'])) $evcs = $response['data'];
								
								echo '
									<h2>Neues Event:</h2>
									<form action="admin_save.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">
									<input type="hidden" name="action" value="ins">
									<div class="table-wrapper">
										<table class="wb">
											<tr><td>Datum(Format:yyyy-mm-dd)</td><td><input type="text" name="evt_date"value=""></td></tr>	
											<tr><td>Kategorie</td><td>
												<div class="select-wrapper">
												<select name="evt_evc_index">';
												foreach ($evcs as $evc) 
													echo '<option value="'.$evc["evc_index"].'">'.$evc["evc_name"].'</option>';
												echo '</select>
												</div>
											</td></tr>
											<tr><td>Ueberschrift</td><td><input type="text" name="evt_header" value=""></td></tr>
											<tr><td>Text</td><td><textarea name="evt_text"></textarea></td></tr>
											<tr><td>Bild</td><td><input type="file" name="evt_image"></td></tr>
											<tr><td>PDF</td><td><input type="file" name="evt_appendix"></td></tr>
											<tr><td>speichern</td><td><input type="submit" value="speichern"></td></tr>
										</table>
									</div>
									</form>';
									
									echo'<br><br><h2>Bevorstehende Events:</h2>';
									$counter = 0;
									$response = callServer ($_def_general_server."kid.php?function=getEvents", array("data"=>array("usr_index"=>0)));
									if (isset ($response['data']))
									{
										if (sizeof ($response['data']) > 0)
										{
											foreach ($response['data'] as $value) 
											{
												$counter ++;
												echo "<h3>Event #".$counter."</h3><br>";
												echo '<form action="admin_save.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">
													<input type="hidden" name="action" value="upd">
													<input type="hidden" name="evt_index" value="'.$value["evt_index"].'">
													<div class="table-wrapper">
														<table  class="wb">
															<tr><td>Datum(Format:yyyy-mm-dd)</td><td><input type="text" name="evt_date'.$value["evt_index"].'" value="'.$value["evt_date_real"].'"></td></tr>	
															<tr><td>Kategorie</td><td>
																<div class="select-wrapper">
																<select name="evt_evc_index'.$value["evt_index"].'">';
																foreach ($evcs as $evc) 
																	echo '<option value="'.$evc["evc_index"].'" '.issel($value["evt_evc_index"], $evc["evc_index"]).'>'.$evc["evc_name"].'</option>';
																echo '</select>
																</div>
															</td></tr>
															<tr><td>Ueberschrift</td><td><input type="text" name="evt_header'.$value["evt_index"].'"  value="'.$value["evt_header"].'"></td></tr>
															<tr><td>Text</td><td><textarea name="evt_text'.$value["evt_index"].'">'.$value["evt_text"].'</textarea></td></tr>
															<tr><td>Bild</td><td><input type="file" name="evt_image'.$value["evt_index"].'">';if (strlen($value["evt_image"])>0) echo 'aktuelles Bild:'.$value["evt_image"].', l&ouml;schen?<input type="checkbox" name="evt_delete_img'.$value["evt_index"].'"  value="1"><label for="evt_delete_img'.$value["evt_index"].'"></label>'; echo '</td></tr>
															<tr><td>PDF</td><td><input type="file" name="evt_appendix'.$value["evt_index"].'">';if (strlen($value["evt_appendix"])>0) echo 'aktueller Anhang:'.$value["evt_appendix"].', l&ouml;schen?<input type="checkbox" name="evt_delete_app'.$value["evt_index"].'"  value="1"><label for="evt_delete_app'.$value["evt_index"].'"></label>'; echo '</td></tr>
															<tr><td>Event loeschen</td><td><input type="checkbox" name="evt_delete'.$value["evt_index"].'"  value="1"><label for="evt_delete'.$value["evt_index"].'"></label></td></tr>
															<tr><td>speichern</td><td><input type="submit" value="speichern"></td></tr>
														</table>
													</div>
													</form>
													<br><hr><br>';
											}
										}
									}
							}
							elseif ($m_id == 11) //eventlisten
							{
								$evcs = array();
								$response = callServer ($_def_general_server."kid.php?function=getEvents", array("data"=>array("usr_index"=>0, "no_lau_no_abs"=>1)));
								if (isset ($response['data'])) $evts = $response['data'];

								echo '
									<h2>Neue Liste:</h2>
									<form action="admin_save.php?m_id='.$m_id.'" method="post">
									<input type="hidden" name="action" value="ins">
									<div class="table-wrapper">
										<table class="wb">
										<tr><td>ListenName</td><td><input type="text" name="lis_name" value=""></td></tr>
										<tr><td>Text</td><td><input type="text" name="lis_text" value=""></td></tr>
											<tr><td>Event</td><td>
												<div class="select-wrapper">
												<select name="evt_index">';
												foreach ($evts as $evt) 
													echo '<option value="'.$evt["evt_index"].'">'.$evt["evt_date"].' - '.$evt["evt_header"].'</option>';
												echo '</select>
												</div>
											</td></tr>
											<tr><td>ListenEintraege</td><td><textarea name="lies">durch # trennen</textarea></td></tr>
											<tr><td>Kommentare erlaubt</td><td><input type="checkbox" name="lis_commentenabled"  value="1"><label for="lis_commentenabled"></label></td></tr>
											<tr><td>speichern</td><td><input type="submit" value="speichern"></td></tr>
										</table>
									</div>
									</form>';
									
									echo'<h2>Bestehende Listen:</h2>';
									$counter = 0;
									$response = callServer ($_def_general_server."kid.php?function=getEventlists", array("data"=>array("evt_index"=>0)));
									if (isset ($response['data']))
									{
										if (sizeof ($response['data']) > 0)
										{
											foreach ($response['data'] as $value) 
											{
												$counter ++;
												echo "<h3>Liste #".$counter."</h3><br>";
												echo '<form action="admin_save.php?m_id='.$m_id.'" method="post">
													<input type="hidden" name="action" value="upd">
													<input type="hidden" name="lis_index" value="'.$value["lis_index"].'">
													<div class="table-wrapper">
														<table class="wb">
																<tr><td>ListenName</td><td><input type="text" name="lis_name" value="'.$value["lis_name"].'"></td></tr>
																<tr><td>Text</td><td><input type="text" name="lis_text" value="'.$value["lis_text"].'"></td></tr>
																<tr><td>Event</td><td>
																<div class="select-wrapper">
																<select name="evt_index">';
																foreach ($evts as $evt) 
																	echo '<option value="'.$evt["evt_index"].'" '.issel($value["evt_index"], $evt["evt_index"]).'>'.$evt["evt_date"].' - '.$evt["evt_header"].'</option>';
																echo '</select>
																</div>
															</td></tr>
															<tr><td><td>ListenEintraege</td>';
															$response2 = callServer ($_def_general_server."kid.php?function=getEventlistItems", array("data"=>array("lis_index"=>$value["lis_index"])));
															if (isset ($response2['data']))
															{
																if (sizeof ($response2['data']) > 0)
																{
																	foreach ($response2['data'] as $value2) 
																	{	
																		echo '<input type="text" name="lie_text_'.$value2["lie_index"].'" value="'.$value2["lie_text"].'"><br>';	
																	}
																}
															}
															echo '<input type="text" name="lie_text_0" placeholder="neu" value="">'	;
													
															echo '</td></tr>
															<tr><td>Kommentare erlaubt</td><td><input type="checkbox" name="lis_commentenabled'.$value["lis_index"].'"  value="1" '.ischk(1, $value["lis_commentenabled"]).'><label for="lis_commentenabled'.$value["lis_index"].'"></label></td></tr>
															<tr><td>loeschen</td><td><input type="checkbox" name="lis_delete'.$value["lis_index"].'"  value="1"><label for="lis_delete'.$value["lis_index"].'"></label></td></tr>
															<tr><td>speichern</td><td><input type="submit" value="speichern"></td></tr>
														</table>
													</div>
													</form>
													<br><hr><br>';
											}
										}
									}
							}
							elseif ($m_id == 12) //waesche
							{
								echo '
									<h2>Neue W&auml;schedienst-Termine anlegen:</h2>
									<form action="admin_save.php?m_id='.$m_id.'&new=1" method="post" enctype="multipart/form-data">
									<input type="hidden" name="action" value="ins">
									<div class="table-wrapper">
										<table class="wb">
											<tr><td>Datum(Format:yyyy-mm-dd, mehrere trennen durch #)</td><td><input type="text" name="lau_date" value=""></td></tr>
											<tr><td>speichern</td><td><input type="submit" value="speichern"></td></tr>
										</table>
									</div>
									</form>';
									
									$kids = callServer ($_def_general_server."kid.php?function=getKidsFromParent", array("data"=>array("usr_index"=>$usr_id, "allkids"=>1)));
																
								echo '<br><br><h2>Bestehnde W&auml;schedienst-Termine</h2>';
								$response = callServer ($_def_general_server."kid.php?function=getLaundry", array("data"=>array("usr_index"=>$usr_id)));
								if (isset ($response['data']))
								{
									if (sizeof($response['data'])>0)
									{
										echo '		
											<div class="table-wrapper">
												<table class="wb"><tbody>
													<tr>
														<td><b>Datum</b></td>
														<td><b>Kind (Anz)</b></td>
														<td><b>loeschen</b></td>
														<td><b>speichern</b></td>
													</tr>';
													foreach ($response['data'] as $value) 
													{
														echo '
															<form action="admin_save.php?m_id='.$m_id.'&new=2" method="post">
															<input type="hidden" name="lau_index" value="'.$value["lau_index"].'">
															<tr>
																<td class="wb">'.$value["lau_date"].'</td>
																<td>
																<div class="select-wrapper">
																<select name="usr_index">
																<option value="0">noch keine Zusage</option>';
																foreach ($kids['data'] as $kid) 
																	echo '<option value="'.$kid["usr_index"].'" '.issel($kid["usr_index"], $value["usr_index"]).'>'.$kid["usr_name"].'</option>';
																echo '</select>
																</div></td>
																<td>
																<input type="checkbox" name="lau_delete" id="lau_delete'.$value["lau_index"].'"  value="1"><label for="lau_delete'.$value["lau_index"].'">Termin loeschen</label>
																</td>
																<td>
																<input type="submit" value="speichern">
																</td>
															</tr>
															</form>';
													}
													echo '
												</tbody></table>
											</div>';
									}
								}
							}
							elseif ($m_id == 13) //dokumente
							{
								echo '
									<h2>Neues Dokument:</h2>
									<form action="admin_save.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">
									<input type="hidden" name="action" value="ins">
									<div class="table-wrapper">
										<table class="wb">
											<tr><td>Name</td><td><input type="text" name="txt_desc" value=""></td></tr>	
											<tr><td>PDF</td><td><input type="file" name="txt_text"></td></tr>
											<tr><td>speichern</td><td><input type="submit" value="speichern"></td></tr>
										</table>
									</div>
									</form>';
									
									echo'<br><br><h2>Bestehende Dokumente:</h2><div class="table-wrapper">
										<table class="wb">
										<tr><td><b>Name</b></td><td><b>loeschen</b></td></tr>';
									include 'app_srv/sqlconn.php'; $dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		$db_selected = mysqli_select_db($dbconn, $dbname);
								$result_txt = mysqli_query ($dbconn, "SELECT txt_index, txt_desc, txt_text FROM tbl_txt_text");
								while ($row_txt = mysqli_fetch_array($result_txt)) 
								{
									echo '
										<tr>
											<td>'.$row_txt["txt_desc"].'</td>
											<td><form action="admin_save.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">
														<input type="hidden" name="action" value="del">
														<input type="hidden" name="txt_index" value="'.$row_txt["txt_index"].'">
														<input type="submit" value="loeschen">
														</form>
											</td>
										</tr>';
								}
													echo '
												</tbody></table>
											</div>';
							}				
							elseif ($m_id == 14) //Arbeitszieten
							{	
									$response = callServer ($_def_general_server."kid.php?function=getUsers",  array("data"=>array("uty_index"=>2)));
									if (isset ($response['data']))
									{
										foreach ($response['data'] as $value) 
										{
											echo '<h2>Arbeitszeiten von '.$value["lin_name"].'</h2><br>';
											echo '<div class="table-wrapper">
												<table class="wb">
												<tr>
													<td>Wochentag</td>
													<td>von</td>
													<td>bis</td>
													<td>Aktion</td>
												</tr>';
											$response2 = callServer ($_def_general_server."kid.php?function=getRoster",  array("data"=>array("uty_index"=>2, "usr_index"=>$value["usr_index"], "lin_index"=>$value["lin_index"])));
											if (isset ($response2['data']))
											{
												foreach ($response2['data'] as $value2) 
												{
													echo '
													<tr>
													<td>'.getWoTa($value2["ros_dayofweek"]).'</td>
													<td>'.$value2["ros_from"].'</td>
													<td>'.$value2["ros_to"].'</td>
													<td>
													<form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="del">
														<input type="submit" value="l&ouml;schen">
														<input type="hidden" name="ros_index" value="'.$value2["ros_index"].'">
													</form>
													</td>
													</tr>';
												}
											}
											echo '
												<form action="admin_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="ins">
												<tr>
												<td><select name="ros_dayofweek">
													<option value="1">Mo</option>
													<option value="2">Di</option>
													<option value="3">Mi</option>
													<option value="4">Do</option>
													<option value="5">Fr</option>
													</select>
												</td>
												<td><input type="text" name="ros_from" value=""></td>
												<td><input type="text" name="ros_to" value=""></td>
												<td>
													<input type="submit" value="neu anlegen">
													<input type="hidden" name="usr_index" value="'.$value["usr_index"].'">
												</td>
												</tr>
												</form>';
											
											echo '</tbody></table></div><br>';
										}
									}
							}
							
						?>	
					</section>
				
				<!-- Footer -->	
					<section id="footer">
						<div class="table-wrapper">
						<table class="alt">
						<tbody>
							<tr>
								<?php
									echo '<td><a href="admin.php?m_id=1" class="footerbutton'; if ($m_id == 1) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-home fsbig"></i><br>Aktuelles</div></a></td>';
									echo '<td><a href="admin.php?m_id=2" class="footerbutton'; if ($m_id == 2) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-minus-circle fsbig"></i><br>Abwesenheit</div></a></td>';
									echo '<td><a href="admin.php?m_id=3" class="footerbutton'; if ($m_id == 3) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-arrows-h fsbig"></i><br>Auschreibungen</div></a></td>';
									echo '<td><a href="admin.php?m_id=4" class="footerbutton'; if ($m_id == 4) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-bar-chart fsbig"></i><br>Statistik</div></a></td>';
								?>
							</tr>
						</tbody>	
						</table>
						</div>
					</div>
			</div>							
		</div>
		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/datepicker_datetime.min.js"></script>
			<script src="assets/js/datepicker_core.min.js"></script>
			<script src="assets/js/datepicker_baseplugin.min.js"></script>
			<script src="assets/js/datepicker_rangeplugin.min.js"></script>
			<script>
			  const picker = new easepick.create({
				element: document.getElementById('datepicker'),
				css: [
				  'assets/css/datepicker.min.css',
				  'assets/css/datepicker_range.min.css',
				],
				lang: 'de-DE',
				format: "DD.MM.YYYY",
				plugins: ['RangePlugin'],
				RangePlugin: {
				  tooltip: true,
				   locale: {
					one: 'Tag',
					other: 'Tage',
				  },
				},
			  });
			</script>
			<script language="JavaScript">
	function togglediv(id) {
				var divname = ""+id;
				var buttonname = "antworten"+id;
				var x = document.getElementById(divname);
				if (x.style.display === "none") {
					x.style.display = "block";
					(document.getElementById(buttonname)).style.display = "none";
				} else {
					x.style.display = "none";
				}
			}
			</script>
			<!--
			<script language="JavaScript">
			// the selector will match all input controls of type :checkbox
// and attach a click event handler 
$("input:checkbox").on('click', function() {
  // in the handler, 'this' refers to the box clicked on
  var $box = $(this);
  if ($box.is(":checked")) {
    // the name of the box is retrieved using the .attr() method
    // as it is assumed and expected to be immutable
    var group = "input:checkbox[name='" + $box.attr("name") + "']";
    // the checked state of the group/box on the other hand will change
    // and the current value is retrieved using .prop() method
    $(group).prop("checked", false);
    $box.prop("checked", true);
  } else {
    $box.prop("checked", false);
  }
});
			</script>-->
	</body>
</html>