<?php
	header("Access-Control-Allow-Origin: *");
	header('Content-type: application/json');
	include 'functions.php';
	$dbconn = sqlconnect();
	$prefix = "tbl_";
	$session = false;
	$msg_id = 0;
	
	$function = ""; if (isset ($_REQUEST["function"]))  $function = $_REQUEST["function"];

	if ($function == "getEducators")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$alleducators = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "usr_index") $usr_index = $value;
						if($key == "alleducators") $alleducators = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				$filter = "where lin_active=1 and usr_index=".$usr_index; 
				if ($alleducators == 1) $filter = "where lin_active=1 and usr_uty_index=2 order by usr_name asc"; 
				elseif ($alleducators == 2) $filter = "where lin_active=1 and (usr_uty_index=2 OR usr_uty_index=3) order by usr_uty_index asc, usr_name asc";
				$result = mysqli_query ($dbconn, "SELECT usr_index, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."lin_login on usr_index=lin_usr_index ".$filter);
				while ($row = mysqli_fetch_array($result))
				{
					$data[]= array("usr_index"=>$row["usr_index"], "usr_name"=>$row["usr_name"]);
					$retval = 1;
				}
			}
			else
			{
				$retval = 3;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	elseif ($function == "getAbsenceReason")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$abr_adminonlyselect = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "abr_adminonlyselect") $abr_adminonlyselect = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				$filter = "where abr_adminonlyselect=".$abr_adminonlyselect." order by abr_index asc"; if ($abr_adminonlyselect==1) $filter="order by abr_adminonlyselect desc, abr_index asc";
				$result = mysqli_query ($dbconn, "SELECT abr_index, abr_text, abr_adminonlyselect FROM ".$prefix."abr_absencereason ".$filter);
				while ($row = mysqli_fetch_array($result))
				{
					$data[]= array("abr_index"=>$row["abr_index"], "abr_text"=>$row["abr_text"], "abr_adminonlyselect"=>$row["abr_adminonlyselect"]);
					$retval = 1;
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	elseif ($function == "getAbsence")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$alleducators = 0;
			$upcoming = 1;
			$abs_date = "";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "usr_index") $usr_index = $value;
						if($key == "upcoming") $upcoming = $value;
						if($key == "alleducators") $alleducators = $value;
						if($key == "abs_date") $abs_date = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				if ($alleducators == 1)
				{
					$direction = ">="; if ($upcoming == 0) $direction = "<";
					$order = "asc"; if ($upcoming == 0) $order = "desc";
					$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE abs_date".$direction."CURDATE() and abs_uty_index=2 and abs_status=0 ORDER BY abs_date asc");
					
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("abs_index"=>$row["abs_index"], "abs_date"=>$row["abs_date_s"], "usr_index"=>$row["abs_ref_index"], "abs_abr_index"=>$row["abs_abr_index"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"]);
						$retval = 1;
					}
				}
				else
				{
					if (strlen($abs_date)>0)
					{
						$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index WHERE usr_index=".$usr_index." and abs_date=".$abs_date." and abs_uty_index=2 and abs_status=0 ORDER BY abs_date asc");
						while ($row = mysqli_fetch_array($result))
						{
							$data[]= array("abs_index"=>$row["abs_index"]);
							$retval = 1;
						}
					}
					else
					{
						$direction = ">="; if ($upcoming == 0) $direction = "<";
						$order = "asc"; if ($upcoming == 0) $order = "desc";
						$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE usr_index=".$usr_index." and abs_date".$direction."CURDATE() and abs_uty_index=2 and abs_status=0 ORDER BY abs_date asc");
						
						while ($row = mysqli_fetch_array($result))
						{
							$joa_count = 0;
							$result_joa = mysqli_query ($dbconn, "SELECT joa_index FROM ".$prefix."job_joboffer INNER JOIN ".$prefix."joa_jobanswer on joa_job_index=job_index WHERE job_abs_index=".$row["abs_index"]." and job_status<3 and joa_status<3");
							while ($row_joa = mysqli_fetch_array($result_joa)) $joa_count++;
							
							$data[]= array("abs_index"=>$row["abs_index"], "abs_date"=>$row["abs_date_s"], "usr_index"=>$row["abs_ref_index"], "abs_abr_index"=>$row["abs_abr_index"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"], "joa_count"=>$joa_count);
							$retval = 1;
						}
					}
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	elseif ($function == "insertAbsence")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$abr_index = 0;
			$usr_index__ver = 0;
			$dates = array();
			
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "usr_index") $usr_index = $value;
						if($key == "dates") $dates = $value;
						if($key == "abr_index") $abr_index = $value;
						if($key == "usr_index__ver") $usr_index__ver = $value;
					}
				}
			}	
			
			$abs_name = "";
			$result_usr = mysqli_query ($dbconn, "SELECT usr_name FROM tbl_usr_user WHERE usr_index=".$usr_index);
			while ($row_usr = mysqli_fetch_array($result_usr)) $abs_name = $row_usr["usr_name"];
										
			
			
		//logic		
			$retval = 0;
			if ($session)
			{	
				$retval = 1;
				foreach ($dates as $date)
				{
					//nicht wochenende
					if (!((date('w', strtotime($date)) == 0) || (date('w', strtotime($date)) == 6)))
					{
						//nicht schliestage
						$result_closedday = mysqli_query ($dbconn, "SELECT clo_index FROM ".$prefix."clo_closedday WHERE clo_date='".$date."'");
						if (mysqli_num_rows($result_closedday) == 0) 
						{
							//besteht für diesen erzieher an diesem tag schon abwe ?
							$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."abs_absence WHERE abs_ref_index=".$usr_index." and abs_date='".$date."' and abs_uty_index=2 and abs_status=0");
							if (mysqli_num_rows($result) == 0) 
							{
								//hat der erzieher an diesem tag dienst ?
								$result_ros = mysqli_query ($dbconn, "SELECT ros_index, ros_from, ros_to FROM ".$prefix."ros_roster WHERE ros_dayofweek=DATE_FORMAT('".$date."', '%w') and ros_usr_index=".$usr_index);
								if (mysqli_num_rows($result_ros) == 0) 
								{
									//erzieher hat keinen dienst
										$msg_id = 1002;
								}
								else
								{
									while ($row = mysqli_fetch_array($result_ros))
									{										
										mysqli_query ($dbconn, "INSERT INTO ".$prefix."abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index) VALUES ('".$date."', ".$usr_index.", 2, ".$abr_index.")");
										$abs_id = mysqli_insert_id($dbconn);
										
										//kita bleibt zu ?
										$sick_counter = 0;
										$result_sick = mysqli_query ($dbconn, "SELECT count(abs_index) as abs_count FROM ".$prefix."abs_absence WHERE abs_date='".$date."' and abs_uty_index=2 and abs_status=0");
										while ($row_sick = mysqli_fetch_array($result_sick)) $sick_counter = $row_sick["abs_count"];
										
										/*if ($sick_counter == 3) //kita bleibt zu
										{
											$result_del = mysqli_query ($dbconn, "SELECT job_index from ".$prefix."job_joboffer where job_date='".$date."'");
											while ($row_del = mysqli_fetch_array($result_del))
											{
												$result_del_a = mysqli_query ($dbconn, "SELECT joa_index, joa_usr_index from ".$prefix."job_jobanswer where joa_job_index=".$row_del['job_index']);
												while ($row_del_a = mysqli_fetch_array($result_del_a))
												{
													mysqli_query ($dbconn, "UPDATE ".$prefix."job_jobanswer set joa_status=3 WHERE joa_index='".$row_del_a['joa_index']."'");
													sendmessage($dbconn, "Absage: am ".$date." bleibt die Kita geschlossen", "Hallo.\r\nAm ".$date." bleibt die Kita aufgrund von Personalmangel geschlossen (Alle drei Erzeiher sind leider krank).", $row_del_a["joa_usr_index"]);
												}
												mysqli_query ($dbconn, "DELETE FROM ".$prefix."job_joboffer WHERE job_index='".$row_del['job_index']."'");
												
											}
											sendmessage($dbconn, "", "INFO:\r\nDie Kita bleibt am ".$date." geschlossen! (Alle drei Erzieher sind leider krank)", 0);
											// lösche kids absences
											mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1, abs_points=0 where abs_date='".$date."' and abs_abr_index=3 and abs_uty_index=1");
											
										}
										else //erzeuge joboffer
										{
											*/
											//ausnhame: es gab schon einen job mit mehr stunden bei dem jemand anders mit weniger stunden schon zugesagt hat. dann entsrpechen anpassung der arbeitszeit nach hinten
											$ros_from = $row["ros_from"];
											$ros_to = $row["ros_to"];
											/*$result_oth = mysqli_query ($dbconn, "SELECT job_index, job_to
																					FROM ".$prefix."joa_jobanswer 
																						INNER JOIN ".$prefix."job_joboffer on job_index=joa_job_index
																					WHERE job_date='".$date."'
																							and job_to>'".$row["ros_to"]."'
																							and joa_to<job_to");
											while ($row_oth = mysqli_fetch_array($result_oth))
											{
												$ros_to = correctTempstaffTime($row_oth["job_to"]);
											}
											*/
											mysqli_query ($dbconn, "INSERT INTO ".$prefix."job_joboffer (job_abs_index, job_joa_index, job_date, job_from, job_to, job_status) VALUES ('".$abs_id."', 0, '".$date."', '".$ros_from."', '".$ros_to."', 0)");
											
											$new_job = mysqli_insert_id($dbconn);
												
											if ($usr_index__ver == 0)
											{
												sendmessage($dbconn, "Krankmeldung Erzieher in der Kita fuer den ".germanDateAsText($date), "WARNUNG: Fuer den ".germanDateAsText($date)." hat sich soeben ein Erzieher (".$abs_name.") abwesend gemeldet. Die Aushilfen werden kontaktiert.", 36);
											
												//mysqli_query ($dbconn, "INSERT INTO ".$prefix."evt_event (evt_date, evt_header, evt_text, evt_image, evt_appendix, evt_evc_index) VALUES ('".$date."', 'Erzieher krank (".$abs_name.")', 'Erzieher krank (".$abs_name.")', '', '', 1)");
							
												//alle aushilfen die active
											/*	$result_tst = mysqli_query ($dbconn, "SELECT usr_index, lin_index from ".$prefix."usr_user INNER JOIN ".$prefix."lin_login on usr_index=lin_usr_index WHERE usr_uty_index=3 and lin_active=1");
												while ($row_tst = mysqli_fetch_array($result_tst))
												{
													$available = false;
													//aushilfe abwesend ?
													$result_tsa = mysqli_query ($dbconn, "SELECT abs_index from ".$prefix."abs_absence WHERE abs_ref_index=".$row_tst["usr_index"]." and abs_uty_index=3 and abs_date='".$date."' and abs_status=0");
													if (mysqli_num_rows($result_tsa) == 0)
													{	
														//anderen job an diesem Tag schon zuesagt ? 
														$result_job = mysqli_query ($dbconn, "SELECT job_index FROM tbl_job_joboffer INNER JOIN tbl_joa_jobanswer on joa_job_index=job_index and joa_usr_index=".$row_tst["usr_index"]." where job_date='".$date."'and job_status=1 and joa_status=1");
														if (mysqli_num_rows($result_job) == 0) // nachricht an aushilfe
														{
															$available = true;
														//	sendmessage($dbconn, "", "Anfrage der Kita: Fuer den ".germanDateAsText($date)." hat sich soeben ein Erzieher abwesend gemeldet. Hast du Zeit ihn zu vertreten ? Bitte antworte auf https://kita.example/", $row_tst["usr_index"]);
														}
														else
														{
															$available = false;
															//insert joa
															mysqli_query ($dbconn, "INSERT INTO ".$prefix."joa_jobanswer(joa_job_index, joa_usr_index, joa_from, joa_to, joa_status) VALUES (".$new_job.",  ".$row_tst["usr_index"].", '0', '0', 2)");
															$new_joa=mysqli_insert_id($dbconn);
															calculate2($dbconn, $new_job, $new_joa);
														}
													}
												}	
											*/
												// nachricht in notdienstgruppe. 
											//	sendmessage($dbconn, "", "INFO: Fuer den ".germanDateAsText($date)." hat sich soeben ein Erzieher (".$abs_name.") abwesend gemeldet.", -1);
											}
											else
											{
												//erstelle joa
												//all eaushilfen die active
												$result_tst = mysqli_query ($dbconn, "SELECT usr_index, lin_index from ".$prefix."usr_user 
																						INNER JOIN ".$prefix."lin_login on usr_index=lin_usr_index 
																					  WHERE usr_uty_index=3 and lin_active=1 and usr_index<>".$usr_index__ver);
												while ($row_tst = mysqli_fetch_array($result_tst))
												{
													mysqli_query ($dbconn, "INSERT INTO ".$prefix."joa_jobanswer(joa_job_index, joa_usr_index, joa_from, joa_to, joa_status) VALUES (".$new_job.",  ".$row_tst["usr_index"].", '0', '0', 2)");
												}

												mysqli_query ($dbconn, "INSERT INTO ".$prefix."joa_jobanswer(joa_job_index, joa_usr_index, joa_from, joa_to, joa_status) VALUES (".$new_job.",  ".$usr_index__ver.", '".$ros_from."', '".$ros_to."', 1)");
												$new_joa = mysqli_insert_id($dbconn);
												mysqli_query ($dbconn, "UPDATE ".$prefix."job_joboffer set job_status=1, job_joa_index=".$new_joa." where job_index=".$new_job);
												//?auswirkung
											}
									//	}
									}	
								}
							}
							else
							{
								//schon abw
								$msg_id = 1003;
							}
						}
						else
						{
							//schliesstag
								$msg_id = 1004;
						}
					}	
					else
					{
						//wochenende
								$msg_id = 1005;
					}
				}
			}
			else
			{
				$msg_id = 1006;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>array()));
	}
	elseif ($function == "deleteAbsence")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$abs_index = 0;
			$uty_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "abs_index") $abs_index = $value;
						if($key == "uty_index") $uty_index = $value;
					}
				}
			}	
		//logic
			$msg_id = 0;
			$retval = 0;
			if ($session)
			{
				$retval = 1;
				//check: gibt es schon eine vertretung ? wenn ja: dann nicht erlaubt. oder nur wenn admin loescht
				$joa_exists = false;
				$result = mysqli_query ($dbconn, "SELECT joa_index FROM ".$prefix."joa_jobanswer INNER JOIN ".$prefix."job_joboffer on job_index=joa_job_index WHERE job_abs_index=".$abs_index);
				while ($row = mysqli_fetch_array($result)) $joa_exists = true;
			
				if ($joa_exists)
				{
					/*if ($uty_index == 4)
					{
						mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_index);
						$result = mysqli_query ($dbconn, "SELECT joa_index, joa_usr_index FROM ".$prefix."joa_jobanswer INNER JOIN ".$prefix."job_joboffer on job_index=joa_job_index WHERE job_abs_index=".$abs_index." and joa_status<2");
						while ($row = mysqli_fetch_array($result)) 
						{
							mysqli_query ($dbconn, "UPDATE ".$prefix."joa_jobanswer set joa_status=3 WHERE joa_index=".$row["joa_index"]);
						//	sendmessage($dbconn, "Absage: Am ".germanDateAsText($date)." keine Aushilfe in der Kita", "Absage: Am ".germanDateAsText($date)." keine Aushilfe in der Kita", $row["joa_usr_index"]);				
						}
						mysqli_query ($dbconn, "UPDATE ".$prefix."job_joboffer set job_status=2 WHERE job_abs_index=".$abs_index);
						
					}*/
					$msg_id = 1005;
				}
			//	else
			//	{
					$abs_date = '';
					$result = mysqli_query ($dbconn, "SELECT abs_date FROM ".$prefix."abs_absence WHERE abs_index=".$abs_index);
					while ($row = mysqli_fetch_array($result)) $abs_date = $row["abs_date"];
					mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_index);
					mysqli_query ($dbconn, "UPDATE ".$prefix."job_joboffer set job_status=2 WHERE job_abs_index=".$abs_index);
					
					//gibt es noch andere jobs an diesem tag ?c
					/*$result = mysqli_query ($dbconn, "SELECT job_index FROM ".$prefix."job_joboffer WHERE job_date='".$abs_date."' and job_status<2");
					while ($row = mysqli_fetch_array($result)) $more_jobs = true;
					if ((mysqli_num_rows($result) == 0) )
						sendmessage($dbconn, "", "ENTWARNUNG: Am ".germanDateAsText($date)." kein Personalmangel in der Kita", 0);
					*/
			//	}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id , "data"=>array()));
			
	}
	
	elseif ($function == "getAbsencePreview")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$abs_date = "CURDATE()";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "abs_date") 
						{
							//$abs_date_a = explode(".", $value);
							//$abs_date = "'".$abs_date_a[2]."-".$abs_date_a[1]."-".$abs_date_a[0]."'";
							$abs_date = $value;
						}
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				
				$open = true;
				$result_clo = mysqli_query ($dbconn, "SELECT clo_index from tbl_clo_closedday where clo_date='".$abs_date."'");
				while ($row = mysqli_fetch_array($result_clo)) $open = false;
				
				if ($open)
				{
					$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, ifnull (abs_abr_index,0) as abs_abr_index_s, abr_text, usr_name, ros_from, ros_to 
														FROM ".$prefix."usr_user LEFT JOIN ".$prefix."abs_absence on usr_index=abs_ref_index and abs_date='".$abs_date."' and abs_uty_index=2 and abs_status=0 LEFT JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index 
														INNER JOIN ".$prefix."ros_roster on ros_usr_index=usr_index and ros_dayofweek=DATE_FORMAT('".$abs_date."', '%w')
														WHERE usr_uty_index=2 and ros_dayofweek=DATE_FORMAT('".$abs_date."', '%w')
														ORDER BY ros_to desc, abs_date asc, abs_abr_index_s desc");
					while ($row = mysqli_fetch_array($result))
					{
						$name = $row["usr_name"]. " (".$row["ros_from"]."-".$row["ros_to"].")"; 
						$aushilfe = "";
						if ($row["abs_abr_index_s"]>0)
						{
							$name = "<s>".$name."</s>";
							$result_j = mysqli_query($dbconn,"SELECT usr_name FROM ".$prefix."job_joboffer INNER JOIN ".$prefix."joa_jobanswer on job_joa_index=joa_index INNER JOIN ".$prefix."usr_user on joa_usr_index=usr_index WHERE job_abs_index=".$row["abs_index"]." and job_status=1 and joa_status=1");
							while ($row_j = mysqli_fetch_array($result_j)) $aushilfe = $row_j["usr_name"];
						
							if (strlen($aushilfe) == 0)
							{
								$anz_aus = 0;
								$result_u = mysqli_query($dbconn,"SELECT count(usr_index) as anz_aus FROM ".$prefix."usr_user WHERE usr_uty_index=3");
								while ($row_u = mysqli_fetch_array($result_u)) $anz_aus = $row_u["anz_aus"];
							 
								$anz_ant = 0;
								$result_j = mysqli_query($dbconn,"SELECT count(joa_index) as anz_ant FROM ".$prefix."job_joboffer INNER JOIN ".$prefix."joa_jobanswer on joa_job_index=job_index WHERE job_abs_index=".$row["abs_index"]." and joa_status=2");
								while ($row_j = mysqli_fetch_array($result_j)) $anz_ant = $row_j["anz_ant"];
								
								//$name = $name." (".$anz_ant."/".$anz_aus." abs.)";
							}
						}
							
						$data[]= array("abs_date"=>$row["abs_date_s"], "abr_text"=>$row["abr_text"], "usr_name"=>$name, "abs_abr_index"=>$row["abs_abr_index_s"], "aushilfe"=>$aushilfe);
						$retval = 1;
					}
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
		
	elseif ($function == "getAbsenceCounter")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$abs_date = "CURDATE()";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "abs_date") 
						{
							//$abs_date_a = explode(".", $value);
							//$abs_date = "'".$abs_date_a[2]."-".$abs_date_a[1]."-".$abs_date_a[0]."'";
							$abs_date = $value;
						}
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$open = true;
				$result_clo = mysqli_query ($dbconn, "SELECT clo_index from tbl_clo_closedday where clo_date='".$abs_date."'");
				while ($row = mysqli_fetch_array($result_clo)) $open = false;
				
				if ($open)
				{
					$result = mysqli_query ($dbconn, "SELECT count(abs_index) as abs_counter 
														FROM ".$prefix."usr_user 
														INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index and abs_date='".$abs_date."' and abs_uty_index=2 and abs_status=0 
														INNER JOIN ".$prefix."ros_roster on ros_usr_index=usr_index and ros_dayofweek=DATE_FORMAT('".$abs_date."', '%w')
														WHERE usr_uty_index=2 and ros_dayofweek=DATE_FORMAT('".$abs_date."', '%w')");
					while ($row = mysqli_fetch_array($result))
					{	
						$data[]= array("abs_counter"=>$row["abs_counter"]);
						$retval = 1;
					}
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}

?>