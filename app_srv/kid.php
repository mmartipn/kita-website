<?php
	header("Access-Control-Allow-Origin: *");
	header('Content-type: application/json');
	include 'functions.php';
	$dbconn = sqlconnect();
	$prefix = "tbl_";
	$session = false;
	
	$function = ""; if (isset ($_REQUEST["function"]))  $function = $_REQUEST["function"];

	if ($function == "getKidsFromParent")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$allkids = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "usr_index") $usr_index = $value;
						if($key == "allkids") $allkids = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				$filter = "where usr_index=".$usr_index; if ($allkids == 1) $filter = "where usr_uty_index=1 order by usr_name asc";
				$result = mysqli_query ($dbconn, "SELECT usr_index, usr_name FROM ".$prefix."usr_user ".$filter);
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
	elseif ($function == "insertAbsence")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$abr_index = 0;
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
					}
				}
			}	
			//file_put_contents("log.txt",54);
		//logic		
		$msg_id = 0;
		
			$retval = 0;
			if ($session)
			{	
		
//mysqli_query ($dbconn, "INSERT INTO ".$prefix."log_log (log_text) values('session')");
				$retval = 1;
				foreach ($dates as $date)
				{
					
//mysqli_query ($dbconn, "INSERT INTO ".$prefix."log_log (log_text) values('".$date."')");
					//nicht wochenende
					if (!((date('w', strtotime($date)) == 0) || (date('w', strtotime($date)) == 6)))
					{
//mysqli_query ($dbconn, "INSERT INTO ".$prefix."log_log (log_text) values('nicht wochenende')");
						//nicht schliestage
						$result_closedday = mysqli_query ($dbconn, "SELECT clo_index FROM ".$prefix."clo_closedday WHERE clo_date='".$date."'");
						if (mysqli_num_rows($result_closedday) == 0) 
						{
							//besteht für dieses kind an diesem tag schon eine abw ?
							$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."abs_absence WHERE abs_ref_index=".$usr_index." and abs_date='".$date."' and abs_uty_index=1 and abs_status=0");
							if (mysqli_num_rows($result) == 0) 
							{
//mysqli_query ($dbconn, "INSERT INTO ".$prefix."log_log (log_text) values('noch keine abw wochenende')");
								//gibt es pm ?
								$kids_personalmangel = array();
								$result = mysqli_query ($dbconn, "SELECT abs_ref_index FROM ".$prefix."abs_absence WHERE abs_date='".$date."' and abs_uty_index=1 and abs_status=0 and abs_abr_index=3");
								while ($row = mysqli_fetch_array($result)) $kids_personalmangel[] = $row["abs_ref_index"];
								if (sizeof($kids_personalmangel)>0)
								{
									$msg_id = 1006;
			//todonow: insert switch
									/*
									//suche kind mit abr3 und den meisten punkten
										$abs_kid = 0;
										$abs_points = 0;
										$abt_date = date("Y-m-d");
										for ($i=0;$i<sizeof($i);$i++)
										{
											$result = mysqli_query ($dbconn, "SELECT 
																				usr_index, 
																				ifnull(sum(a.abs_points),0) as abs_points__sum,
																				(SELECT at.abs_date FROM ".$prefix."abs_absence at WHERE at.abs_ref_index=usr_index and at.abs_status=0 and at.abs_uty_index=1 and at.abs_abr_index=3 order by at.abs_date desc limit 1) as abt_date
																			from ".$prefix."usr_user 
																				left join ".$prefix."abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1 
																			where 
																				usr_index=".$kids_personalmangel[$i]." 
																				and usr_uty_index=1
																			group by 
																				usr_index 
																			order by 
																				abs_points__sum asc, 
																				abt_date asc,
																				usr_index asc");
											while ($row = mysqli_fetch_array($result))
											{
												if ($row["abs_points__sum"] > $abs_points)
												{
													$abs_points = $row["abs_points__sum"];
													$abs_kid = $row["usr_index"];
													$abt_date = $row["abt_date"];
												}
												elseif ($row["abs_points__sum"] == $abs_points)
												{
													if ($row["abt_date"] < $abt_date)
													{
														$abs_points = $row["abs_points__sum"];
														$abs_kid = $row["usr_index"];
														$abt_date = $row["abt_date"];
													}
												}
											}
										}	
										
										
										$abs_usr_index = 0;
										$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."abs_absence WHERE abs_date='".$date."' and abs_uty_index=1 and abs_status=0 and abs_abr_index=3 and abs_ref_index=".$abs_kid);
										while ($row = mysqli_fetch_array($result)) $abs_usr_index = $row["abs_index"];
										mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_usr_index);
										mysqli_query ($dbconn, "DELETE FROM ".$prefix."swi_switch WHERE swi_abs_index__from=".$abs_usr_index);
										
										
										//todo? sendsignal wenn notbetruung abr 3 aufgehiben wird.	
									*/
								}
								else
								{
									//mysqli_query ($dbconn, "INSERT INTO ".$prefix."log_log (log_text) values('".$q."')");
									mysqli_query ($dbconn, "INSERT INTO ".$prefix."abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index) VALUES ('".$date."', ".$usr_index.", 1, ".$abr_index.")");
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
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>array()));
	}
	elseif ($function == "getAbsence")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$allkids = 0;
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
						if($key == "allkids") $allkids = $value;
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
				if ($allkids == 1)
				{
					$direction = ">="; if ($upcoming == 0) $direction = "<";
					$order = "asc"; if ($upcoming == 0) $order = "desc";
					$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_points, abs_abr_index, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE abs_date".$direction."CURDATE() and abs_uty_index=1 and abs_status=0 ORDER BY abs_date asc");
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("abs_index"=>$row["abs_index"], "abs_date"=>$row["abs_date_s"], "usr_index"=>$row["abs_ref_index"], "abs_abr_index"=>$row["abs_abr_index"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"], "abs_points"=>$row["abs_points"]);
						$retval = 1;
					}
				}
				else
				{
					if (strlen($abs_date)>0)
					{
						$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index WHERE usr_index=".$usr_index." and abs_date=".$abs_date." and abs_uty_index=1 and abs_status=0 ORDER BY abs_date asc");
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
						$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abs_points, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE usr_index=".$usr_index." and abs_date".$direction."CURDATE() and abs_uty_index=1 and abs_status=0 ORDER BY abs_date asc");
						while ($row = mysqli_fetch_array($result))
						{
							$data[]= array("abs_index"=>$row["abs_index"], "abs_date"=>$row["abs_date_s"], "usr_index"=>$row["abs_ref_index"], "abs_abr_index"=>$row["abs_abr_index"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"], "abs_points"=>$row["abs_points"]);
							$retval = 1;
						}
					}
				}

			/*	$filter = "job_status>0 AND job_date>=DATE(NOW())";
				$result = mysqli_query ($dbconn, "SELECT distinct (job_date) FROM tbl_job_joboffer WHERE ".$filter);
				while ($row = mysqli_fetch_array($result))
				{
					$direction = ">="; if ($upcoming == 0) $direction = "<";
						$order = "asc"; if ($upcoming == 0) $order = "desc";
						$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abs_points, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE usr_index=".$usr_index." and abs_date".$direction."CURDATE() and abs_uty_index=1 and abs_status=0 ORDER BY abs_date asc");
						while ($row = mysqli_fetch_array($result))
						{
							$data[]= array("abs_index"=>$row["abs_index"], "abs_date"=>$row["abs_date_s"], "usr_index"=>$row["abs_ref_index"], "abs_abr_index"=>$row["abs_abr_index"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"], "abs_points"=>$row["abs_points"]);
							$retval = 1;
						}





					if ($usr_index > 0)
					{
						$abs_index = 0;
						$result_abs = mysqli_query ($dbconn, "SELECT abs_index FROM tbl_abs_absence WHERE abs_date='".$row["job_date"]."' and abs_ref_index=".$usr_index." and abs_status=0");
						while ($row_abs = mysqli_fetch_array($result_abs)) $abs_index = $row_abs["abs_index"];
						
						if ($abs_index == 0)
							$data[]= array("job_date"=>$row["job_date"]);
					}
					else
					{
						$data[]= array("job_date"=>$row["job_date"]);
					}
					$retval = 1;
				}
				*/
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
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
			$retval = 0;
			if ($session)
			{
				$retval = 1;
				//check: gibt es notbetreuung ? wenn ja: dann nicht erlaubt oder nur wenn admin loescht
				$abs_date = "";
				$personalmangel = false;
				$result = mysqli_query ($dbconn, "SELECT abs_date FROM ".$prefix."abs_absence WHERE abs_index=".$abs_index);
				while ($row = mysqli_fetch_array($result)) $abs_date = $row["abs_date"];
				/*$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."abs_absence WHERE abs_date='".$abs_date."' and abs_uty_index=1 and abs_status=0 and abs_abr_index=3");
				while ($row = mysqli_fetch_array($result)) $personalmangel = true;*/
				$result = mysqli_query ($dbconn, "SELECT job_index FROM ".$prefix."job_joboffer WHERE job_date='".$abs_date."' and job_status<3");
				while ($row = mysqli_fetch_array($result)) $personalmangel = true;
				
				if ($personalmangel)
				{
					//todo:retval 0
					if ($uty_index == 4)
					{
						mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_index);
						mysqli_query ($dbconn, "DELETE FROM ".$prefix."swi_switch WHERE swi_abs_index__from=".$abs_index);
					}
				}
				else
				{
					mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_index);
					mysqli_query ($dbconn, "DELETE FROM ".$prefix."swi_switch WHERE swi_abs_index__from=".$abs_index);
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>array()));
			
		//todo? sendsignal wenn notbetruung abr 3 aufgehiben wird.	
	}
	elseif ($function == "createSwitch")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$post = array();
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "post") $post = $value;
					}
				}
			}	
		//logic
			$msg_id = 0;
			$retval = 0;
			if ($session)
			{
				$retval = 1;
				if (isset($post['swi_date']))
				{
					$my_abs_points_today=0;
					$result = mysqli_query ($dbconn, "SELECT abs_points FROM ".$prefix."abs_absence WHERE abs_ref_index=".$post['usr_index']." and abs_date='".$post['swi_date']."' 
					and abs_uty_index=1 and abs_status=0");
					while ($row = mysqli_fetch_array($result)) $my_abs_points_today = $row["abs_points"];

					//gibt es einen switch den ich direkt annhemen kann ?
					$swi_exists = false;
					$result = mysqli_query ($dbconn, "SELECT swi_index, swi_date, swi_usr_index__from, swi_abs_points__from, swi_abs_points__to, swi_status
										FROM tbl_swi_switch 
										WHERE swi_date='".$post['swi_date']."' and swi_abs_points__from=".$post['swi_abs_points__to']." and swi_abs_points__to=".$my_abs_points_today." and swi_status=0 order by swi_index asc limit 1");
					while ($row = mysqli_fetch_array($result))
					{
						$swi_exists = true;
						
						doswitch($dbconn,$row["swi_index"],$post["usr_index"],$post["swi_date"],$row["swi_abs_points__from"],$row["swi_abs_points__to"],$row["swi_usr_index__from"]);

						$msg_id = 1009;
					}
					if (!$swi_exists)
					{
						if(strlen($post['swi_date']) > 0)
						{
							if ($my_abs_points_today <> $post['swi_abs_points__to'])
							{
								mysqli_query ($dbconn, "INSERT INTO tbl_swi_switch(swi_date, swi_usr_index__from, swi_abs_points__from, swi_abs_points__to, swi_status) 
								VALUES ('".$post['swi_date']."',".$post['usr_index'].",".$my_abs_points_today.",".$post['swi_abs_points__to'].",0)");
							}
							else
							{
								$msg_id = 1007;
							}
						}
						else
						{
							$msg_id = 1008;
						}
					}
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>array()));
	}

	elseif ($function == "updateSwitch")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$post = array();
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "post") $post = $value;
					}
				}
			}	
		//logic
			$retval = 0;
			if ($session)
			{
				/*$swi_index = $post["swi_index"];
				$usr_index__my = $post["usr_index"];
				$swi_date = $post["swi_date"];
				$swi_abs_points__from = $post["swi_abs_points__from"];
				$swi_abs_points__to = $post["swi_abs_points__to"];
				$swi_usr_index__from = $post["swi_usr_index__from"];
*/
				doswitch($dbconn,$post["swi_index"],$post["usr_index"],$post["swi_date"],$post["swi_abs_points__from"],$post["swi_abs_points__to"], $post["swi_usr_index__from"]);
				
				$retval = 1;
			}
			else
			{
				$retval = 3;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>array()));
	}
	elseif ($function == "getSwitchDates")
	{
		//get data from json
		$arr = json_decode(file_get_contents("php://input"), true);
		$usr_index = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "usr_index") $usr_index = $value;
				}
			}
		}
	//logic
		$retval = 0;
		$msg_id = 0;
		$data = array();
		if ($session)
		{
			$filter = "job_date>=DATE(NOW()) and job_status>0";
			$result = mysqli_query ($dbconn, "SELECT distinct (job_date) FROM tbl_job_joboffer WHERE ".$filter);
			while ($row = mysqli_fetch_array($result))
			{
				//gibt es schon einen swi ?
				$swi_index = 0;
				$result_swi = mysqli_query ($dbconn, "SELECT swi_index FROM tbl_swi_switch WHERE swi_usr_index__from=".$usr_index." and swi_status=0 and swi_date='".$row["job_date"]."'");
				//$result_swi = mysqli_query ($dbconn, "SELECT swi_index FROM tbl_swi_switch WHERE swi_usr_index__from=".$usr_index." and swi_status=0");
				while ($row_swi = mysqli_fetch_array($result_swi)) $swi_index = $row_swi["swi_index"];
				
				if ($swi_index == 0)
				{
					$abs_points = 0;
					$result_abs = mysqli_query ($dbconn, "SELECT abs_index, abs_points FROM tbl_abs_absence WHERE abs_date='".$row["job_date"]."' and abs_ref_index=".$usr_index." and abs_status=0 order by abs_date asc");
					while ($row_abs = mysqli_fetch_array($result_abs)) $abs_points = $row_abs["abs_points"];

					$data[]= array("swi_date"=>$row["job_date"], "swi_abs_points__from"=>$abs_points);
				}

				
			}
			
			$retval = 1;
		}
		
		echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>$data));
	}

	elseif ($function == "deleteSwitch")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$swi_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "swi_index") 
						{
							$swi_index = $value;
						}
					}
				}
			}	
		//logic
			$retval = 0;
			if ($session)
			{
				$retval = 1;
				mysqli_query ($dbconn, "DELETE FROM ".$prefix."swi_switch  WHERE swi_index=".$swi_index);
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>array()));
	}
	elseif ($function == "getSwitch")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$swi_date = '';
			$usr_index = 0;
			$type = "";
			
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "swi_date") $swi_date = $value;
						if($key == "usr_index") $usr_index = $value;
						if($key == "type") $type = $value;
					}
				}
			}
			
		//logic SELECT swi_index, swi_date, swi_usr_index__from, swi_abs_points__from, swi_abs_points__to, swi_status FROM tbl_swi_switch
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				/*if ($type == "date")
				{
					$filter = "swi_date='".$swi_date."'"; if ($usr_index>0) $filter = $filter." AND swi_usr_index__from=".$usr_index; 
					$result = mysqli_query ($dbconn, "SELECT swi_index, swi_date, swi_usr_index__from, swi_abs_points__from, swi_abs_points__to, swi_status FROM tbl_swi_switch where ".$filter);
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("swi_index"=>$row["swi_index"], "swi_date"=>$row["swi_date"], "swi_usr_index__from"=>$row["swi_usr_index__from"], "swi_abs_points__from"=>$row["swi_abs_points__from"], "swi_abs_points__to"=>$row["swi_abs_points__to"]);
						$retval = 1;
					}
				}*/
				if ($type == "mine")
				{
					$result = mysqli_query ($dbconn, "SELECT swi_index, swi_date,  DATE_FORMAT(swi_date, '%d.%m.%Y') as swi_date_s, swi_usr_index__from, swi_abs_points__from, swi_abs_points__to, swi_status, usr_name FROM ".$prefix."swi_switch 
															INNER JOIN ".$prefix."usr_user on swi_usr_index__from=usr_index
														WHERE usr_index=".$usr_index." and swi_date>=CURDATE() and swi_status=0");
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("swi_index"=>$row["swi_index"], "usr_name"=>$row["usr_name"],"swi_date"=>$row["swi_date_s"],"swi_date_real"=>$row["swi_date"], "swi_usr_index__from"=>$row["swi_usr_index__from"],
						"swi_abs_points__from"=>$row["swi_abs_points__from"], "swi_abs_points__to"=>$row["swi_abs_points__to"]);
					}
					$retval = 1;
				}
				elseif ($type == "open")
				{
					$result = mysqli_query ($dbconn, "SELECT swi_index, swi_date, DATE_FORMAT(swi_date, '%d.%m.%Y') as swi_date_s, swi_usr_index__from, swi_abs_points__from, swi_abs_points__to, swi_status, usr_name FROM ".$prefix."swi_switch
															INNER JOIN ".$prefix."usr_user on swi_usr_index__from=usr_index
														WHERE usr_index!=".$usr_index." and swi_date>=CURDATE() and swi_status=0");
					while ($row = mysqli_fetch_array($result))
					{
						//wie ist mein abs_points an diesem tag?
						$my_abs_points = 0;
						$result_abs = mysqli_query ($dbconn, "SELECT abs_points FROM tbl_abs_absence where abs_ref_index=".$usr_index." and abs_date='".$row["swi_date"]."' and abs_status=0");
						while ($row_abs = mysqli_fetch_array($result_abs)) $my_abs_points = $row_abs["abs_points"];

						if ($my_abs_points == $row["swi_abs_points__to"])
						{
							$data[]= array("swi_index"=>$row["swi_index"], "usr_name"=>$row["usr_name"],"swi_date"=>$row["swi_date_s"],"swi_date_real"=>$row["swi_date"],  "swi_usr_index__from"=>$row["swi_usr_index__from"],
							"swi_abs_points__from"=>$row["swi_abs_points__from"], "swi_abs_points__to"=>$row["swi_abs_points__to"]);
						}
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
	elseif ($function == "getTable")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			//$swi_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						//if($key == "swi_index") $swi_index = $value;
					}
				}
			}			
			
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				$result = mysqli_query ($dbconn, "SELECT usr_index, usr_name, ifnull(sum(a.abs_points),0) as abs_points__sum,
				(SELECT at.abs_date FROM ".$prefix."abs_absence at WHERE at.abs_ref_index=usr_index and at.abs_status=0 and at.abs_uty_index=1 and at.abs_abr_index=3 and abs_date<DATE(NOW()) order by at.abs_date desc limit 1) as abt_date 
				from ".$prefix."usr_user 
				left join ".$prefix."abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1 
				where usr_uty_index=1
				group by usr_index 
				order by abs_points__sum asc, abt_date asc, usr_index asc");

				while ($row = mysqli_fetch_array($result))
				{
					//convert last absence
					$abt_date = "-";
					if (!is_null($row["abt_date"]))
					{
						$t_a = explode ("-",$row["abt_date"]);
						if (sizeof($t_a)==3) $abt_date = $t_a[2].".".$t_a[1].".".$t_a[0];
					}
					
					$data[]= array("usr_index"=>$row["usr_index"], "usr_name"=>$row["usr_name"],"abs_points"=>$row["abs_points__sum"],"abt_date"=>$abt_date);		
				}
				$retval = 1;
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>$data));
	}
	
	elseif ($function == "getAbsenceReason")
	{
		//file_put_contents("log.txt",5);
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
						if($key == "abr_adminonlyselect") 
						{
							$abr_adminonlyselect = $value;
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
	
	elseif ($function == "getAbsencePreview")
	{
		//file_put_contents("log.txt",5);
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
		//	file_put_contents("log.txt",$abs_date);
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
					$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, ifnull (abs_abr_index,0) as abs_abr_index_s, abr_text, usr_name, ifnull(abs_points,0) as abs_points_s 
														FROM ".$prefix."usr_user LEFT JOIN ".$prefix."abs_absence on usr_index=abs_ref_index and abs_date='".$abs_date."' and abs_uty_index=1 and abs_status=0 LEFT JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index 
														WHERE usr_uty_index=1 and (DATE_FORMAT('".$abs_date."', '%w') not in (6,0))
														ORDER BY abs_date asc, abs_abr_index_s desc");
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("abs_date"=>$row["abs_date_s"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"], "abs_points"=>$row["abs_points_s"], "abs_abr_index"=>$row["abs_abr_index_s"]);
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
	
	elseif ($function == "getPersonalmangelDates")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			}
			
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				$result = mysqli_query ($dbconn, "SELECT distinct DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_date FROM ".$prefix."abs_absence where abs_abr_index=3 and abs_status=0 order by abs_date desc");  
				while ($row = mysqli_fetch_array($result))
				{
					$nicht = ""; 
					$bis12 = ""; 
					$result_sub = mysqli_query ($dbconn, "SELECT usr_name, abs_points FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index where abs_date='".$row['abs_date']."' and abs_status=0 order by abs_points desc");  
					while ($row_sub = mysqli_fetch_array($result_sub))
					{
						if ($row_sub["abs_points"] == 8)
						{
								if (strlen($nicht)>0) $nicht = $nicht.", ";
								$nicht = $nicht.$row_sub["usr_name"];
						}
						elseif ($row_sub["abs_points"] == 4)
						{
								if (strlen($bis12)>0) $bis12 = $bis12.", ";
								$bis12 = $bis12.$row_sub["usr_name"];
						}
					}
					$data[]= array("abs_date"=>$row["abs_date_s"], "kids_nicht"=>$nicht, "kids_bis12"=>$bis12);	
				}
				$retval = 1;
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}

/*
	elseif ($function == "createPersonalmangel")
	{
		//file_put_contents("log.txt",5);
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$abs_date = ""; 
			$abs_points_7 = 0; 
			$abs_points_3 = 0; 
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "abs_date") 
						{
							$abs_date_a = explode(".", $value);
							$abs_date = "'".$abs_date_a[2]."-".$abs_date_a[1]."-".$abs_date_a[0]."'";
						}
						if($key == "abs_points_7") $abs_points_7 = $value;
						if($key == "abs_points_3") $abs_points_3 = $value;
					}
				}
			}
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			
			$data = array();
			if ($session)
			{
				//gibt es an dem Tag schon Personalmangel ?
				
				$kids7 = array();
				$kids3 = array();
				
				$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."abs_absence where abs_abr_index=3 and abs_status=0 and abs_date=".$abs_date);  
				if (mysqli_num_rows($result) == 0)
				{
					$counter = 0;
					$result_tab = mysqli_query ($dbconn, "SELECT 
															usr_index, 
															usr_name, 
															ifnull(sum(a.abs_points),0) as abs_points__sum,
															(SELECT at.abs_date FROM ".$prefix."abs_absence at WHERE at.abs_ref_index=usr_index and at.abs_status=0 and at.abs_uty_index=1 and at.abs_abr_index=3 and abs_date<NOW()order by at.abs_date desc limit 1) as abt_date
															from ".$prefix."usr_user left join ".$prefix."abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1 
															where usr_uty_index=1
															group by usr_index order by abs_points__sum asc, abt_date asc, usr_index asc");
					while ($row_tab = mysqli_fetch_array($result_tab))
					{
						$result_kid = mysqli_query ($dbconn, "SELECT abs_ref_index FROM ".$prefix."abs_absence where abs_date=".$abs_date." and abs_ref_index=".$row_tab['usr_index']." and abs_uty_index=1 and abs_status=0");
						if (mysqli_num_rows($result_kid) == 0)
						{
							if ($abs_points_7 > 0)
							{
								mysqli_query ($dbconn, "INSERT INTO ".$prefix."abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index, abs_status, abs_points) VALUES (".$abs_date.", ".$row_tab['usr_index'].", 1, 3, 0, 7)");
								$abs_points_7 --;
								$kids7[] = $row_tab['usr_name'];
							}
							else
							{
								if($abs_points_3 > 0)
								{
									mysqli_query ($dbconn, "INSERT INTO ".$prefix."abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index, abs_status, abs_points) VALUES (".$abs_date.", ".$row_tab['usr_index'].", 1, 3, 0, 3)");
									$abs_points_3 --;
									$kids3[] = $row_tab['usr_name'];
								}
							}
						}
					}
					$retval = 1;
				}
			}
			else
			{
				$retval = 3;
			}
		//return
		
		// sendsignal
			$text = "PERSONALMANGEL am ".germanDateAsText($abs_date)."!!!\r\n";
			if (sizeof($kids7)>0) 
			{
				$text = $text."Folgende ".sizeof($kids7)." Kinder muessen GANZ zu Hause bleiben:\r\n";
				for ($i=0; $i<sizeof($kids7); $i++) $text = $text.$kids7[$i]."\r\n";
			}
			if (sizeof($kids3)>0) 
			{
				$text = $text."Folgende ".sizeof($kids3)." Kinder muessen um 12 Uhr geholt werden:\r\n";
				for ($i=0; $i<sizeof($kids3); $i++) $text = $text.$kids3[$i]."\r\n";
			}
			mysqli_query ($dbconn, "INSERT INTO ".$prefix."mes_message (mes_text, mes_status) VALUES ('".$text."',0)");
									
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
	*/
	elseif ($function == "sendemail")
	{
		//file_put_contents("log.txt",5);
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$lin_index = 0;
			$nachricht = "";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "lin_index") $lin_index = $value;
						if($key == "nachricht") $nachricht = $value;
					}
				}
			}
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$absender = "";
				$result = mysqli_query ($dbconn, "SELECT lin_email, lin_name from tbl_lin_login where lin_index=".$lin_index);
				while ($row = mysqli_fetch_array($result))$absender = $row["lin_name"]." (".$row["lin_email"].")";
				
				if ($lin_index == 0)
				$absender = "noname";
				
				include_once("defines.php");
				$result = mysqli_query ($dbconn, "SELECT lin_email from tbl_lin_login where lin_index in (".$_def_general_admin_usrids.")");
				while ($row = mysqli_fetch_array($result))
				{
					sendmail($row["lin_email"], "Frage aus der WebApp", "".$absender." fragt:<br>".$nachricht);
					
					$msg_id = 1001;
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
	
	
	elseif ($function == "getUserSettings")
	{
			//file_put_contents("log.txt",5);
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$lin_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "lin_index") $lin_index = $value;
					}
				}
			}
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result = mysqli_query ($dbconn, "SELECT lin_email, lin_phone, lin_email__notify, lin_phone__notify FROM tbl_lin_login WHERE lin_index=".$lin_index);
				while ($row = mysqli_fetch_array($result))
				{
					$data[]= array("lin_email"=>$row["lin_email"], "lin_phone"=>$row["lin_phone"], "lin_email__notify"=>$row["lin_email__notify"], "lin_phone__notify"=>$row["lin_phone__notify"]);
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
	
	elseif ($function == "updateUserSettings")
	{
			//file_put_contents("log.txt",5);
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$lin_index = 0;
			$phone = 0;
			$email = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "lin_index") $lin_index = $value;
						if($key == "phone") $phone = $value;
						if($key == "email") $email = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				mysqli_query ($dbconn, "UPDATE tbl_lin_login set lin_email__notify=".$email.", lin_phone__notify=".$phone." WHERE lin_index=".$lin_index);
				$retval = 1;
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
	elseif ($function == "getUsers")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$uty_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "uty_index") $uty_index = $value;
					}
				}
			}
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result = mysqli_query ($dbconn, "SELECT lin_index, lin_usr_index, lin_name, lin_email, lin_phone, lin_email__notify, lin_phone__notify, lin_active, lin_password, lin_loginfail, lin_locked, lin_admin, usr_index, usr_name, usr_uty_index FROM tbl_lin_login INNER JOIN tbl_usr_user on lin_usr_index=usr_index where usr_uty_index=".$uty_index." order by usr_index asc, usr_name asc");
				while ($row = mysqli_fetch_array($result))
				{
					$data[]= array("lin_index"=>$row["lin_index"], "lin_usr_index"=>$row["lin_usr_index"], "lin_name"=>$row["lin_name"], "lin_email"=>$row["lin_email"], "lin_phone"=>$row["lin_phone"], "lin_email__notify"=>$row["lin_email__notify"]
					, "lin_phone__notify"=>$row["lin_phone__notify"], "lin_active"=>$row["lin_active"], "lin_loginfail"=>$row["lin_loginfail"], "lin_locked"=>$row["lin_locked"], "lin_admin"=>$row["lin_admin"], "usr_index"=>$row["usr_index"], "usr_name"=>$row["usr_name"], "usr_uty_index"=>$row["usr_uty_index"]);
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
	
	elseif ($function == "deleteUser")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$lin_index = 0;
			$usr_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "lin_index") $lin_index = $value;
						if($key == "usr_index") $usr_index = $value;
					}
				}
			}
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
			//	$result_abs = mysqli_query ($dbconn, "SELECT abs_index from tbl_abs_absence where abs_ref_index=".$usr_index);
			//	if (mysqli_num_rows($result_abs) == 0)
		//		{
					mysqli_query ($dbconn, "DELETE FROM tbl_lin_login where lin_index=".$lin_index);
					mysqli_query ($dbconn, "DELETE FROM tbl_tok_token where tok_lin_index=".$lin_index);
					
					//war es der letzte lin zu diesem usr ?
					$result_lin = mysqli_query ($dbconn, "SELECT lin_index from tbl_lin_login where lin_usr_index=".$usr_index);
					if (mysqli_num_rows($result_lin) == 0)
					{
						mysqli_query ($dbconn, "DELETE FROM tbl_usr_user where usr_index=".$usr_index);
						mysqli_query ($dbconn, "DELETE FROM tbl_abs_absence where abs_ref_index=".$usr_index);
						mysqli_query ($dbconn, "DELETE FROM tbl_lau_laundry where lau_usr_index=".$usr_index);
						mysqli_query ($dbconn, "DELETE FROM tbl_joa_jobanswer where joa_usr_index=".$usr_index);
						mysqli_query ($dbconn, "UPDATE tbl_joa_jobanswer set where joa_usr_index=0 where joa_usr_index=".$usr_index);
					}
		//		}
				$retval = 1;
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
	
	elseif ($function == "insertUser")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$uty_index = 0;
			$usr_name = "";
			$lin_name = "";
			$lin_email = "";
			$lin_phone = "";
			$lin_benach = 0;
			$lin_email_n = 0;
			$lin_phone_n = 0;
			$lin_admin = 0;
			$usr_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "uty_index") $uty_index = $value;
						if($key == "usr_name") $usr_name = $value;
						if($key == "lin_name") $lin_name = $value;
						if($key == "lin_email") $lin_email = $value;
						if($key == "lin_phone") $lin_phone = $value;
						if($key == "lin_benach") {$lin_benach = $value; if ($lin_benach == 1) $lin_phone_n=1; elseif ($lin_benach == 2) $lin_email_n=1;  elseif ($lin_benach == 3) {$lin_email_n=1; $lin_phone_n=1;}}
						if($key == "lin_admin") $lin_admin = $value;
					}
				}
			}
			
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				if ($uty_index == 1)
				{
					//gibt es das kind schon ?
					$result = mysqli_query ($dbconn, "SELECT usr_index from tbl_usr_user where usr_name like ('".$usr_name."')");
					while ($row = mysqli_fetch_array($result)) $usr_index = $row["usr_index"];
				}
				
				if ($usr_index == 0)
				{
					if ($usr_name == "") $usr_name = $lin_name;
					mysqli_query ($dbconn, "INSERT INTO tbl_usr_user ( usr_name, usr_uty_index) VALUES ('".$usr_name."', ".$uty_index.")");
					$usr_index = mysqli_insert_id($dbconn);
				}
				
				mysqli_query ($dbconn, "INSERT INTO tbl_lin_login (lin_usr_index, lin_name, lin_email, lin_phone, lin_email__notify, lin_phone__notify, lin_active, lin_password, lin_loginfail, lin_locked, lin_admin) 
				VALUES (".$usr_index.", '".$lin_name."', '".$lin_email."', '".$lin_phone."', '".$lin_email_n."', '".$lin_phone_n."', 1, '".md5("Start1234")."', 0, 0, 0)");
			
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
	elseif ($function == "updateUser")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$lin_index = 0;
			$usr_index = 0;
			$usr_name = "";
			$lin_name = "";
			$lin_email = "";
			$lin_phone = "";
			$lin_benach = 0;
			$lin_email_n = 0;
			$lin_phone_n = 0;
			$lin_admin = 0;
			$lin_locked = 0;
			$lin_active = 0;
			$lin_password = "";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "lin_index") $lin_index = $value;
						if($key == "usr_index") $usr_index = $value;
						if($key == "usr_name") $usr_name = $value;
						if($key == "lin_name") $lin_name = $value;
						if($key == "lin_email") $lin_email = $value;
						if($key == "lin_phone") $lin_phone = $value;
						if($key == "lin_benach") {$lin_benach = $value; if ($lin_benach == 1) $lin_phone_n=1; elseif ($lin_benach == 2) $lin_email_n=1;  elseif ($lin_benach == 3) {$lin_email_n=1; $lin_phone_n=1;}}
						if($key == "lin_admin") $lin_admin = $value;
						if($key == "lin_locked") $lin_locked = $value;
						if($key == "lin_active") $lin_active = $value;
						if($key == "lin_password") $lin_password = $value;
					}
				}
			}			
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				if (strlen(trim($lin_password)) > 0) mysqli_query ($dbconn, "UPDATE tbl_lin_login SET lin_password = '".md5($lin_password)."' WHERE lin_index=".$lin_index);
				
				mysqli_query ($dbconn, "UPDATE tbl_lin_login SET 
				lin_name = '".$lin_name."', 
				lin_email = '".$lin_email."',
				lin_phone = '".$lin_phone."', 
				lin_email__notify = ".$lin_email_n.", 
				lin_phone__notify = ".$lin_phone_n.", 
				lin_active = ".$lin_active.", 
				lin_loginfail = 0, 
				lin_locked = ".$lin_locked.", 
				lin_admin = ".$lin_admin."
				WHERE lin_index=".$lin_index);
				
				mysqli_query ($dbconn, "UPDATE tbl_usr_user SET 
				usr_name = '".$usr_name."'
				WHERE usr_index=".$usr_index);
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
	elseif ($function == "getRoster")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$uty_index = 0;
			$usr_index = 0;
			$lin_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "uty_index") $uty_index = $value;
						if($key == "usr_index") $usr_index = $value;
						if($key == "lin_index") $lin_index = $value;
					}
				}
			}
		//	file_put_contents("log.txt",$abs_date);
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result = mysqli_query ($dbconn, "SELECT ros_index, ros_dayofweek, ros_usr_index, ros_from, ros_to FROM tbl_ros_roster WHERE ros_usr_index='".$usr_index."' ORDER BY ros_dayofweek asc, ros_from asc");
				while ($row = mysqli_fetch_array($result))
				{
					$data[]= array("ros_index"=>$row["ros_index"], "ros_dayofweek"=>$row["ros_dayofweek"], "ros_usr_index"=>$row["ros_usr_index"], "ros_from"=>$row["ros_from"], "ros_to"=>$row["ros_to"]);
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

	elseif ($function == "resetAll")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
					}
				}
			}			
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				mysqli_query ($dbconn, "truncate table tbl_abs_absence");
				mysqli_query ($dbconn, "truncate table tbl_swi_switch");
				mysqli_query ($dbconn, "truncate table tbl_job_joboffer");
				mysqli_query ($dbconn, "truncate table tbl_joa_jobanswer");
				mysqli_query ($dbconn, "truncate table tbl_lau_laundry");
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}	
	
	elseif ($function == "insSonderfall")
	{
		$arr = json_decode(file_get_contents("php://input"), true);
				
		$a_da = '';
		$a_gn = 0;
		$a_12 = 0;
		$a_14 = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "a_da") $a_da = $value;
					if($key == "a_gn") $a_gn = $value;
					if($key == "a_12") $a_12 = $value;
					if($key == "a_14") $a_14 = $value;
				}
			}
		}
		
		$sick_filter = "";
		$sick_counter = 0;
		$already_abs = array();
		$result_sick = mysqli_query ($dbconn, "SELECT abs_ref_index FROM tbl_abs_absence where abs_date='".$a_da."' and abs_uty_index=1 and abs_abr_index<3 and abs_status=0");
		while ($row_sick = mysqli_fetch_array($result_sick))
		{
			if (strlen($sick_filter)>0) $sick_filter = $sick_filter.",";
			$sick_filter = $sick_filter.$row_sick["abs_ref_index"];
			$sick_counter ++;
		}
		if (strlen($sick_filter)>0) $sick_filter = " and usr_index not in (".$sick_filter.") ";
		
	/*	if ($a_gn>0) generateAbsences($dbconn, $a_da, $sick_filter, $already_abs, 7, $a_gn);
		if ($a_12>0) generateAbsences($dbconn, $a_da, $sick_filter, $already_abs, 3, $a_12);
		if ($a_14>0) generateAbsences($dbconn, $a_da, $sick_filter, $already_abs, 1, $a_14);
		*/
		if ($a_gn>0)
		{
		$how_many_kids = $a_gn-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = $a_gn; $sick_counter=$sick_counter-$a_gn;} else { $sick_counter=0;}
		//file_put_contents("test2.txt", $how_many_kids.", ".$sick_filter);
		$already_abs[] = generateAbsences($dbconn, $a_da, $sick_filter, $already_abs, 8, $how_many_kids);
		}
		if ($a_12>0)
		{
		$how_many_kids = $a_12-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = $a_12; $sick_counter=$sick_counter-$a_12;} else { $sick_counter=0;}
		$already_abs[] = generateAbsences($dbconn, $a_da, $sick_filter, $already_abs, 4, $how_many_kids);
		}
		if ($a_14>0)
		{
		$how_many_kids = $a_14-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = $a_14; $sick_counter=$sick_counter-$a_14;} else { $sick_counter=0;}
		$already_abs[] = generateAbsences($dbconn, $a_da, $sick_filter, $already_abs, 1, $how_many_kids);
		}
		
		$text = "";
		$kids_gn = array();
		$kids_12 = array();
		$kids_14 = array();
		$result_tab = mysqli_query ($dbconn, "SELECT usr_name, a.abs_points 
												from tbl_usr_user inner join tbl_abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1 and abs_abr_index=3 
												where abs_date='".$a_da."' order by usr_index asc");
		while ($row_tab = mysqli_fetch_array($result_tab))
		{
			if ($row_tab["abs_points"] == 8) $kids_gn[] = $row_tab["usr_name"];
			if ($row_tab["abs_points"] == 4) $kids_12[] = $row_tab["usr_name"];
			if ($row_tab["abs_points"] == 1) $kids_14[] = $row_tab["usr_name"];
		}
		//if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."Bringen gn 12 14=".sizeof($kids_gn)." ".sizeof($kids_12)." ".sizeof($kids_14), FILE_APPEND);
		
		if ((sizeof ($kids_gn) == 0) && (sizeof ($kids_12) == 0) && (sizeof ($kids_14) == 0))
			$text = "Sonderfall INFO: Keine Notbetreuung am ".$a_da.". Es konnten ausreichend Aushilfen gefunden werden.";
		else
			$text = "Sonderfall INFO: Notbetreuung am ".$a_da."\r\n";
		
		if (sizeof ($kids_gn) > 0)
		{
			if (sizeof ($kids_gn) >= (14-$sick_counter))
			{
				//geschlossen. 
				$text = "Sonderfall INFO:\r\nDie Kita bleibt am ".$a_da." geschlossen!";
			}
			else
			{
				$text = $text."Folgende Kinder müssen am ".$a_da." zu Hause bleiben: \r\n";
				for ($i=0; $i<sizeof($kids_gn); $i++) $text = $text.$kids_gn[$i]."\r\n";
			}
		}
		if (sizeof ($kids_12) > 0)
		{
			$text = $text."Folgende Kinder müssen am ".$a_da." um 12 Uhr abgeholt werden: \r\n";
			for ($i=0; $i<sizeof($kids_12); $i++) $text = $text.$kids_12[$i]."\r\n";
		}
		if (sizeof ($kids_14) > 0)
		{
			$text = $text."Folgende Kinder müssen heute um 14 Uhr abgeholt werden: \r\n";
			for ($i=0; $i<sizeof($kids_14); $i++)  $text = $text.$kids_14[$i]."\r\n";
		}
		//sendmessage($dbconn, "", $text, 0);
		//sendmessage($dbconn, "", $text, -1);
		
	}
	
	elseif ($function == "getNotbetreuungDates")
	{ 
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$usr_index = 0;
			$past = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "usr_index") $usr_index = $value;
						if($key == "past") $past = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				//$filter = "job_status=0"; if ($past == 1) $filter = "job_status>0";
				$filter = "WHERE job_date>=DATE(NOW())";
				if ($past == 1) $filter = "order by job_date desc";
				//if ($past == 1) $filter = $filter." and job_status>0";
				$result = mysqli_query ($dbconn, "SELECT distinct (job_date) FROM tbl_job_joboffer ".$filter);
				while ($row = mysqli_fetch_array($result))
				{
					if ($usr_index > 0)
					{
						$abs_index = 0;
						$result_abs = mysqli_query ($dbconn, "SELECT abs_index FROM tbl_abs_absence WHERE abs_date='".$row["job_date"]."' and abs_ref_index=".$usr_index." and abs_status=0 order by abs_date asc");
						while ($row_abs = mysqli_fetch_array($result_abs)) $abs_index = $row_abs["abs_index"];
						
						if ($abs_index == 0)
							$data[]= array("job_date"=>$row["job_date"]);
					}
					else
					{
						$data[]= array("job_date"=>$row["job_date"]);
					}
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
	
	elseif ($function == "getNotbetreuungAtDate")
	{ 
			$arr = json_decode(file_get_contents("php://input"), true);
			$job_date = "";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "job_date") $job_date = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result_usr = mysqli_query ($dbconn, "SELECT usr_index, usr_name, ros_from, ros_to FROM tbl_usr_user INNER JOIN tbl_ros_roster on ros_usr_index=usr_index WHERE ros_dayofweek=DATE_FORMAT('".$job_date."', '%w') and usr_uty_index=2");
				while ($row_usr = mysqli_fetch_array($result_usr))
				{
					$usr_name = $row_usr["usr_name"];
					$ros_from = $row_usr["ros_from"];
					$ros_to = $row_usr["ros_to"];
					$abs_index = 0;
					$result_abs = mysqli_query ($dbconn, "SELECT abs_index from tbl_abs_absence WHERE abs_ref_index=".$row_usr["usr_index"]." and abs_date='".$job_date."' and abs_status=0");
					while ($row_abs = mysqli_fetch_array($result_abs)) $abs_index = $row_abs["abs_index"];
					
					if ($abs_index > 0)
					{
						$usr_name = "<s>".$row_usr["usr_name"]."</s>";
						$ros_from = "<s>".$row_usr["ros_from"]."</s>";
						$ros_to = "<s>".$row_usr["ros_to"]."</s>";
						$job_index = 0;
						$job_status = 0;
						$result_job = mysqli_query ($dbconn, "SELECT job_index, job_status, job_joa_index FROM tbl_job_joboffer WHERE job_abs_index=".$abs_index." and job_status>0");
						while ($row_job = mysqli_fetch_array($result_job)) 
						{
							if ($row_job["job_status"] == 1)
							{
								$result_joa = mysqli_query ($dbconn, "SELECT joa_index, joa_job_index, joa_usr_index, usr_name, joa_from, joa_to, joa_status FROM tbl_joa_jobanswer INNER JOIN tbl_usr_user on usr_index=joa_usr_index WHERE joa_index=".$row_job["job_joa_index"]);
								while ($row_joa = mysqli_fetch_array($result_joa)) 
								{
									$usr_name = $usr_name." -> ".$row_joa["usr_name"];
									$ros_from = $ros_from." -> ".$row_joa["joa_from"];
									$ros_to = $ros_to." -> ".$row_joa["joa_to"];
								}
							}
						}
					}
						
					$data[]= array("usr_name"=>$usr_name, "ros_from"=>$ros_from, "ros_to"=>$ros_to);
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

	
	elseif ($function == "getNotbetreuungSuggestion")
	{ 
			$arr = json_decode(file_get_contents("php://input"), true);
			$job_date = "";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "job_date") $job_date = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			$staff = array();
			
							$open = true;
				$result_clo = mysqli_query ($dbconn, "SELECT clo_index from tbl_clo_closedday where clo_date='".$job_date."'");
				while ($row = mysqli_fetch_array($result_clo)) $open = false;
				$dayOfWeek = date('w', strtotime($job_date));
				if ($dayOfWeek == 0 || $dayOfWeek == 6) $open = false;
			if ($open)
			{
			if ($session)
			{


			//hole staff
				$result_usr = mysqli_query ($dbconn, "SELECT usr_index, usr_name, usr_sta_atleasttwo, ros_from, ros_to FROM tbl_usr_user INNER JOIN tbl_ros_roster on ros_usr_index=usr_index WHERE ros_dayofweek=DATE_FORMAT('".$job_date."', '%w') and usr_uty_index=2 order by ros_to desc");
				while ($row_usr = mysqli_fetch_array($result_usr))
				{
					
					$abs_index = 0;
					$vollzeit =  !($row_usr["ros_to"] == "13:00");
					$krank = false;
					$aushilfe = false;
					$joajobdiff = 0;
					$notalonewithkids = 0;
					
					$result_abs = mysqli_query ($dbconn, "SELECT abs_index from tbl_abs_absence WHERE abs_ref_index=".$row_usr["usr_index"]." and abs_date='".$job_date."' and abs_status=0");
					while ($row_abs = mysqli_fetch_array($result_abs)) $abs_index = $row_abs["abs_index"];
					
					if ($abs_index > 0)
					{
						$krank = true;
						$result_job = mysqli_query ($dbconn, "SELECT job_index, job_status, job_joa_index FROM tbl_job_joboffer WHERE job_abs_index=".$abs_index." and job_status>0");
						while ($row_job = mysqli_fetch_array($result_job)) 
						{
							if ($row_job["job_status"] == 1)
							{
								$aushilfe = true;
								$joajobdiff = 0;
								$result_joa = mysqli_query ($dbconn, "SELECT joa_index, joa_job_index, joa_usr_index, usr_name, joa_from, joa_to, joa_status FROM tbl_joa_jobanswer INNER JOIN tbl_usr_user on usr_index=joa_usr_index WHERE joa_index=".$row_job["job_joa_index"]);
								while ($row_joa = mysqli_fetch_array($result_joa)) 
								{
									$joajobdiff = getjobjoadiff($dbconn, $row_joa["joa_index"]);
								}
							}
							else if ($row_job["job_status"] == 2)
							{
								$aushilfe = false;
							}
						}
					}
						
					$staff[]= array("vollzeit"=>$vollzeit, "krank"=>$krank, "aushilfe"=>$aushilfe, "joajobdiff"=>$joajobdiff, "atleasttwo"=>$row_usr["usr_sta_atleasttwo"]);
					
			/*	file_put_contents("log.txt", "\r\n".date("H:i"). "\r\n\t"."vollzeit:".$vollzeit
		. "\r\n\t"."krank:".$krank
		. "\r\n\t"."aushilfe:".$aushilfe
		. "\r\n\t"."joajobdiff:".$joajobdiff, FILE_APPEND);
		*/
				}
				
				
	
			
			//vorschlag
				$kids_gn = array();
				$kids_12 = array();
				$kids_14 = array();
				
				$kids_gn_counter = 0;
				$kids_12_counter = 0;
				$kids_14_counter = 0;

				$atleasttwo = 0;
				$fulltimecounter = 0;
				$parttimecounter = 0;
				
				for ($i=0; $i<sizeof($staff); $i++)
				{
					if($staff[$i]["krank"])
					{
						if($staff[$i]["aushilfe"])
						{
							if($staff[$i]["vollzeit"])
							{
								if ($staff[$i]["joajobdiff"] == 1) $kids_14_counter = $kids_14_counter + 5;
								if ($staff[$i]["joajobdiff"] > 1) $kids_12_counter = $kids_12_counter + 5;
								$fulltimecounter ++;
							}
							else
							{
								$parttimecounter ++;
							}
						}
						else
						{
							if($staff[$i]["vollzeit"])
							{
								$kids_gn_counter = $kids_gn_counter + 5;
								$kids_12_counter = $kids_12_counter + 5;
							}
							else
							{
								$kids_gn_counter = $kids_gn_counter + 5;
							}
						}
					}
					else
					{
						if (($staff[$i]["atleasttwo"]) && ($staff[$i]["vollzeit"]))
						{	
							$atleasttwo ++;
						}
						else
						{
							if($staff[$i]["vollzeit"])
								$fulltimecounter ++;
							else
								$parttimecounter ++;
						}
					}
				}
				

				//Sonderfall: Erzieher bleibt nicht alleine mit Kindern
					if ($atleasttwo > 0)
					{
						if (($fulltimecounter == 1) && ($parttimecounter == 1))
						{}
						elseif (($fulltimecounter == 1) && ($parttimecounter == 0))
						{}
						elseif (($fulltimecounter == 0) && ($parttimecounter == 1))
						{
							//5 normal müssen auch um 12
							$kids_12_counter = $kids_12_counter + 5;
						}
						elseif (($fulltimecounter == 0) && ($parttimecounter == 0))
						{
							//5 normal dürfen gar nicht
							$kids_gn = $kids_gn + 5;
						}
					}
					//file_put_contents("log.txt", "\r\nalt:".$atleasttwo."_".$fulltimecounter);

				
					/*file_put_contents("log.txt", "\r\n".date("H:i"). "\r\n\t"."kids_gn_counter:".$kids_gn_counter
		. "\r\n\t"."kids_12_counter:".$kids_12_counter
		. "\r\n\t"."kids_14_counter:".$kids_14_counter, FILE_APPEND);
				}*/
				
				
			//hole kids laut rangliste
				//gibt es heute schon nbt ? Wenn ja lade vorschlag
				$hsn = 0;
				$result_hsn = mysqli_query ($dbconn, "SELECT count(usr_index) as usr_count from tbl_usr_user inner join tbl_abs_absence on abs_uty_index=1 and abs_ref_index=usr_index and abs_abr_index=3 where usr_uty_index=1 and abs_date='".$job_date."'"); 
				while ($row_hsn = mysqli_fetch_array($result_hsn)) $hsn = $row_hsn["usr_count"];
				
				if ($hsn > 0)
				{
					$result_tab = mysqli_query ($dbconn, "SELECT 
								usr_index, 
								usr_name, 
								ifnull(sum(a.abs_points),0) as abs_points__sum,
								(SELECT at.abs_date FROM tbl_abs_absence at WHERE at.abs_ref_index=usr_index and at.abs_status=0 and at.abs_uty_index=1 and at.abs_abr_index=3 and abs_date<DATE(NOW()) order by at.abs_date desc limit 1) as abt_date,
								ifnull((SELECT count(td.abs_index) FROM tbl_abs_absence td WHERE td.abs_ref_index=usr_index and td.abs_status=0 and td.abs_uty_index=1 and abs_date='".$job_date."' and abs_points=0 limit 1),0) as abs_today,
								ifnull((SELECT tdp.abs_points FROM tbl_abs_absence tdp WHERE tdp.abs_ref_index=usr_index and tdp.abs_status=0 and tdp.abs_uty_index=1 and abs_date='".$job_date."' limit 1),0) as abs_points_today
								from tbl_usr_user left join tbl_abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1
								where usr_uty_index=1 group by usr_index order by abs_today desc, abs_points_today desc, abs_points__sum asc, abt_date asc, usr_index asc");
					while ($row_tab = mysqli_fetch_array($result_tab))
					{
							$data[] = array("usr_index"=>$row_tab["usr_index"], "usr_name"=>$row_tab["usr_name"], "abs_points__sum"=>$row_tab["abs_points__sum"], "abt_date"=>$row_tab["abt_date"], "abs_today"=>$row_tab["abs_today"], "abs_ponits_new"=>$row_tab["abs_points_today"], "hsn"=>$hsn);
					}
					
					$retval = 2;
				}
				else
				{
					$result_tab = mysqli_query ($dbconn, "SELECT 
								usr_index, 
								usr_name, 
								ifnull(sum(a.abs_points),0) as abs_points__sum,
								(SELECT at.abs_date FROM tbl_abs_absence at WHERE at.abs_ref_index=usr_index and at.abs_status=0 and at.abs_uty_index=1 and at.abs_abr_index=3 and abs_date<DATE(NOW()) order by at.abs_date desc limit 1) as abt_date,
								ifnull((SELECT count(td.abs_index) FROM tbl_abs_absence td WHERE td.abs_ref_index=usr_index and td.abs_status=0 and td.abs_uty_index=1 and abs_date='".$job_date."' limit 1),0) as abs_today
								from tbl_usr_user left join tbl_abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1
								where usr_uty_index=1 group by usr_index order by abs_today desc, abs_points__sum asc, abt_date asc, usr_index asc");
					while ($row_tab = mysqli_fetch_array($result_tab))
					{
						if ($kids_gn_counter > 0)
						{
							$kids_gn_counter --;
							if ($row_tab["abs_today"] != 0) $kids_gn [] = $row_tab["usr_index"];
							$data[] = array("usr_index"=>$row_tab["usr_index"], "usr_name"=>$row_tab["usr_name"], "abs_points__sum"=>$row_tab["abs_points__sum"], "abt_date"=>$row_tab["abt_date"], "abs_today"=>$row_tab["abs_today"], "abs_ponits_new"=>(($row_tab["abs_today"] == 0)?8:0), "hsn"=>$hsn);
							//$data[]= array("abs_date"=>$row["abs_date_s"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"], "abs_points"=>$row["abs_points_s"], "abs_abr_index"=>$row["abs_abr_index_s"]);
							
						}
						else
						{
							if ($kids_12_counter > 0)
							{
								$kids_12_counter --;
								if ($row_tab["abs_today"] != 0) $kids_12 [] = $row_tab["usr_index"];
								$data[] = array("usr_index"=>$row_tab["usr_index"], "usr_name"=>$row_tab["usr_name"], "abs_points__sum"=>$row_tab["abs_points__sum"], "abt_date"=>$row_tab["abt_date"], "abs_today"=>$row_tab["abs_today"], "abs_ponits_new"=>(($row_tab["abs_today"] == 0)?4:0), "hsn"=>$hsn);
							}
							else
							{
								if ($kids_14_counter > 0)
								{
									$kids_14_counter --;
									if ($row_tab["abs_today"] != 0) $kids_14 [] = $row_tab["usr_index"];
									$data[] = array("usr_index"=>$row_tab["usr_index"], "usr_name"=>$row_tab["usr_name"], "abs_points__sum"=>$row_tab["abs_points__sum"], "abt_date"=>$row_tab["abt_date"], "abs_today"=>$row_tab["abs_today"], "abs_ponits_new"=>(($row_tab["abs_today"] == 0)?1:0), "hsn"=>$hsn);
								}
								else
								{	
									$data[] = array("usr_index"=>$row_tab["usr_index"], "usr_name"=>$row_tab["usr_name"], "abs_points__sum"=>$row_tab["abs_points__sum"], "abt_date"=>$row_tab["abt_date"], "abs_today"=>$row_tab["abs_today"], "abs_ponits_new"=>0, "hsn"=>$hsn);
								}
							}
						}
					}
					
					$retval = 1;
				}
							
			}
			else
			{
				$retval = 3;
			}
			}
			else
				$retval = 1;
		//return
		
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
		
	}
	
	elseif ($function == "setNotbetreuungKids")
	{ 
		// alle kinder inkl zeit und status
			$arr = json_decode(file_get_contents("php://input"), true);
			$abs_date = "";
			$post = array();
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "abs_date") $abs_date = $value;
						if($key == "post") $post = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			$staff = array();
			
			$um12 = array();
			$um14 = array();
			$gani = array();
			$sick = array();
			
			$text = "";
			
			if ($session)
			{
				$result_usr = mysqli_query ($dbconn, "SELECT usr_index, usr_name FROM tbl_usr_user WHERE usr_uty_index=1");
				while ($row_usr = mysqli_fetch_array($result_usr))
				{
					$usr_index = $row_usr["usr_index"];
					$absent = false;
					$abr_index = 0;
					$abs_points = 0;

					if (isset($post['absusr_k_'.$usr_index])) 
					{ 
						if ($post['absusr_k_'.$usr_index] == 1) 
						{
							$abr_index = 1; 
							$abs_points = 0; 
							$sick[] = $row_usr["usr_name"];
							
							//holday not sick ?
							$result_abs = mysqli_query ($dbconn, "SELECT abs_index, abs_abr_index FROM tbl_abs_absence WHERE abs_ref_index=".$usr_index." AND abs_date='".$abs_date."' AND abs_status=0");
							while ($row_usr = mysqli_fetch_array($result_abs))
							{
								if ($row_abs["abs_abr_index"] == 2) $abr_index = 2; 
							}
						}
					}
					if (isset($post['absusr_0_'.$usr_index])) { if ($post['absusr_0_'.$usr_index] == 1) {$abr_index = 3; $abs_points = 8; $gani[] = $row_usr["usr_name"];}}
					if (isset($post['absusr_12_'.$usr_index])) { if ($post['absusr_12_'.$usr_index] == 1) {$abr_index = 3; $abs_points = 4; $um12[] = $row_usr["usr_name"];}}
					if (isset($post['absusr_14_'.$usr_index])) { if ($post['absusr_14_'.$usr_index] == 1) {$abr_index = 3; $abs_points = 1; $um14[] = $row_usr["usr_name"];}}

					mysqli_query ($dbconn, "delete from tbl_abs_absence where abs_ref_index=".$usr_index." and abs_date='".$abs_date."'");
					if ($abr_index > 0)	
						mysqli_query ($dbconn, "insert into tbl_abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index, abs_status, abs_points) 
											values('".$abs_date."', ".$usr_index.", 1, ".$abr_index.", 0, ".$abs_points.")");	
																			
				}
				
				if ((sizeof($gani) > 0) || (sizeof($um12) > 0) || (sizeof($um14) > 0))
				{
					$text = "INFO: Notbetreuung am ".germanDateAsText($abs_date)."\r\n"; 
					
					if (sizeof($gani) > 0)
					{
						$text = $text."\r\nFolgende Kinder muessen zu Hause bleiben:\r\n"; 
						for ($i=0;$i<sizeof($gani);$i++) $text = $text.$gani[$i]."\r\n";
					}
					if (sizeof($um12) > 0)
					{
						$text = $text."\r\nFolgende Kinder muessen um 12 Uhr abgeholt werden:\r\n"; 
						for ($i=0;$i<sizeof($um12);$i++) $text = $text.$um12[$i]."\r\n";
					}
					if (sizeof($um14) > 0)
					{
						$text = $text."\r\nFolgende Kinder muessen um 14 Uhr abgeholt werden:\r\n"; 
						for ($i=0;$i<sizeof($um14);$i++) $text = $text.$um14[$i]."\r\n";
					}
					
					if (sizeof($sick) > 0)
					{
						$text = $text."\r\nFolgende Kinder sind krank/Urlaub:\r\n"; 
						for ($i=0;$i<sizeof($sick);$i++) $text = $text.$sick[$i]."\r\n";
					}
					
				//	mysqli_query ($dbconn, "INSERT INTO ".$prefix."evt_event (evt_date, evt_header, evt_text, evt_image, evt_appendix, evt_evc_index) VALUES ('".$abs_date."', 'Notbetreuung', '".str_replace("\r\n","<br>",$text)."', '', '', 1)");	
				}
				else
				{
					$text = "INFO: keine Notbetreuung am ".germanDateAsText($abs_date)."\r\nEs konnten genuegend Aushilfen gefunden werden."; 
				//	mysqli_query ($dbconn, "INSERT INTO ".$prefix."evt_event (evt_date, evt_header, evt_text, evt_image, evt_appendix, evt_evc_index) VALUES ('".$abs_date."', 'keine Notbetreuung', 'keine Notbetreuung', '', '', 1)");
							
				}
				
				
			//	sendmessage($dbconn, "", $text, 0);
			//	sendmessage($dbconn, "", $text, -1);

				//alle jobs absagen
				$result_abs = mysqli_query ($dbconn, "UPDATE tbl_job_joboffer set job_status=2 where job_status=0 and job_date='".$abs_date."'");
				
			}
	}
	
	elseif ($function == "getEvents")
	{ 
		$max_count = 5;
		$count = 0;
		$arr = json_decode(file_get_contents("php://input"), true);
		$usr_index = 0;
		$uty_index = 1;
		$no_lau_no_abs = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "usr_index") $usr_index = $value;
					if($key == "uty_index") $uty_index = $value;
					if($key == "no_lau_no_abs") $no_lau_no_abs = $value;
				}
			}
		}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result_evt = mysqli_query ($dbconn, "SELECT evt_index, DATE_FORMAT(evt_date, '%d.%m.') as evt_date_s, evt_date, evt_header, evt_text, evt_image, evt_appendix, evt_evc_index FROM tbl_evt_event WHERE evt_date>=DATE(NOW()) order by evt_date asc");
				while ($row_evt = mysqli_fetch_array($result_evt))
				{
					
					$data[] = array("evt_index"=>$row_evt["evt_index"], "evt_date"=>$row_evt["evt_date_s"], "evt_date_real"=>$row_evt["evt_date"], "evt_header"=>$row_evt["evt_header"], "evt_text"=>$row_evt["evt_text"], "evt_image"=>$row_evt["evt_image"], "evt_appendix"=>$row_evt["evt_appendix"], "evt_evc_index"=>$row_evt["evt_evc_index"]);
				}
				
				$result_abs = mysqli_query ($dbconn, "SELECT distinct(abs_date), DATE_FORMAT(abs_date, '%d.%m.') as abs_date_s, DATE_FORMAT(abs_date, '%d%m%Y') as abs_date_ss FROM tbl_abs_absence WHERE abs_uty_index=1 and abs_status=0 and abs_abr_index=3 and abs_date>=DATE(NOW())");
				while ($row_abs = mysqli_fetch_array($result_abs))
				{
					
					$data[] = array("evt_index"=>"0a".$row_abs["abs_date_ss"], "evt_date"=>$row_abs["abs_date_s"], "evt_date_real"=>$row_abs["abs_date"], "evt_header"=>"Notbetreuung", "evt_text"=>"", "evt_image"=>"", "evt_appendix"=>"", "evt_evc_index"=>1);
				}

				if ($no_lau_no_abs == 0)  //erzeiheransicht
				{
					$result_abs = mysqli_query ($dbconn, "SELECT abs_index, abs_abr_index, abs_points, abr_text,DATE_FORMAT(abs_date, '%d.%m.') as abs_date_s, abs_date from tbl_abs_absence inner join tbl_abr_absencereason on abr_index=abs_abr_index where abs_ref_index=".$usr_index." and abs_date>=DATE(NOW()) and abs_status<3");
					while ($row_abs = mysqli_fetch_array($result_abs))
					{
						if ($row_abs["abs_abr_index"] < 3)
							$data[] = array("evt_index"=>"a".$row_abs["abs_index"], "evt_date"=>$row_abs["abs_date_s"], "evt_date_real"=>$row_abs["abs_date"], "evt_header"=>"Abwesenheit deines Kindes (".$row_abs["abr_text"].")", "evt_text"=>"", "evt_image"=>"", "evt_appendix"=>"", "evt_evc_index"=>1);
						else
						{
							if ($row_abs["abs_points"] < 8)	
								$data[] = array("evt_index"=>"a".$row_abs["abs_index"], "evt_date"=>$row_abs["abs_date_s"], "evt_date_real"=>$row_abs["abs_date"], "evt_header"=>"Dein Kind muss um ".(15-$row_abs["abs_points"])." Uhr abgeholt werden", "evt_text"=>"", "evt_image"=>"", "evt_appendix"=>"", "evt_evc_index"=>1);
							else
								$data[] = array("evt_index"=>"a".$row_abs["abs_index"], "evt_date"=>$row_abs["abs_date_s"], "evt_date_real"=>$row_abs["abs_date"], "evt_header"=>"Abwesenheit deines Kindes (".$row_abs["abr_text"].")", "evt_text"=>"", "evt_image"=>"", "evt_appendix"=>"", "evt_evc_index"=>1);
						}
					}

					if ($uty_index == 2)
					{
						$result_lau = mysqli_query ($dbconn, "SELECT lau_index,DATE_FORMAT(lau_date, '%d.%m.') as lau_date_s, lau_date, usr_name  from tbl_lau_laundry inner join tbl_usr_user on usr_index=lau_usr_index where lau_date>=DATE(NOW()) order by lau_date asc limit 1");
						while ($row_lau = mysqli_fetch_array($result_lau))
							$data[] = array("evt_index"=>"l".$row_lau["lau_index"], "evt_date"=>$row_lau["lau_date_s"], "evt_date_real"=>$row_lau["lau_date"], "evt_header"=>"W&auml;schedienst: ".$row_lau["usr_name"], "evt_text"=>"", "evt_image"=>"", "evt_appendix"=>"", "evt_evc_index"=>6);
					}
					else
					{
						$result_lau = mysqli_query ($dbconn, "SELECT lau_index,DATE_FORMAT(lau_date, '%d.%m.') as lau_date_s, lau_date  from tbl_lau_laundry where lau_usr_index=".$usr_index." and lau_date>=DATE(NOW()) ");
						while ($row_lau = mysqli_fetch_array($result_lau))
							$data[] = array("evt_index"=>"l".$row_lau["lau_index"], "evt_date"=>$row_lau["lau_date_s"], "evt_date_real"=>$row_lau["lau_date"], "evt_header"=>"W&auml;schedienst", "evt_text"=>"Bitte am Wochenden die Matratzen und Betten abziehen, waschen und bis Sonntag abend wieder neu beziehen", "evt_image"=>"", "evt_appendix"=>"", "evt_evc_index"=>6);
					}	
				}
				$retval = 1;
			}
			$dt  = array_column($data, 'evt_date_real');
			array_multisort($dt, SORT_ASC, $data);
	
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
			
			
	}
	
	elseif ($function == "getEventCategories")
	{ 
		$max_count = 5;
		$count = 0;
		$arr = json_decode(file_get_contents("php://input"), true);
		$usr_index = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				
			}
		}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result_evt = mysqli_query ($dbconn, "SELECT evc_index,evc_name FROM tbl_evc_eventcategory");
				while ($row_evt = mysqli_fetch_array($result_evt))
				{
					$data[] = array("evc_index"=>$row_evt["evc_index"], "evc_name"=>$row_evt["evc_name"]);
				}
				$retval = 1;
			}
	
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
	elseif ($function == "insupdEvent")
	{
		// alle kinder inkl zeit und status
			$arr = json_decode(file_get_contents("php://input"), true);
			$action = "";
			$post = array();
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "action") $action = $value;
						if($key == "post") $post = $value;
					}
				}
			}
			
					
			$evt_index = ""; if (isset ($post['evt_index'])) $evt_index = $post['evt_index']; 
			$evt_date = "";  if (isset ($post['evt_date'.$evt_index])) $evt_date = $post['evt_date'.$evt_index];
			$evc_index = 0; if (isset ($post['evt_evc_index'.$evt_index])) $evc_index = $post['evt_evc_index'.$evt_index]; 
			$evt_header = "";  if (isset ($post['evt_header'.$evt_index])) $evt_header = $post['evt_header'.$evt_index];
			$evt_text = "";  if (isset ($post['evt_text'.$evt_index])) $evt_text = $post['evt_text'.$evt_index];
			$evt_image = "";  if (isset ($post['evt_image__name'.$evt_index])) $evt_image = $post['evt_image__name'.$evt_index];
			$evt_appendix = "";  if (isset ($post['evt_appendix__name'.$evt_index])) $evt_appendix = $post['evt_appendix__name'.$evt_index];
			$evt_delete = 0; if (isset ($post['evt_delete'.$evt_index])) $evt_delete = $post['evt_delete'.$evt_index]; 
			$evt_delete_img = 0; if (isset ($post['evt_delete_img'.$evt_index])) $evt_delete_img = $post['evt_delete_img'.$evt_index]; 
			$evt_delete_app = 0; if (isset ($post['evt_delete_app'.$evt_index])) $evt_delete_app = $post['evt_delete_app'.$evt_index]; 
			
			if ($action == "ins")
			{
				mysqli_query ($dbconn, "INSERT INTO ".$prefix."evt_event (evt_date, evt_header, evt_text, evt_image, evt_appendix, evt_evc_index) VALUES 
				('".$evt_date."', '".$evt_header."', '".$evt_text."', '".$evt_image."', '".$evt_appendix."', ".$evc_index.")");	
				$evt_index = mysqli_insert_id($dbconn);
			}
			elseif ($action == "upd")
			{
				if ($evt_delete == 1)
				{
					//unlink images and appendix
					$app = "";
					$img = "";
					$result_evt = mysqli_query ($dbconn, "SELECT evt_image,evt_appendix FROM tbl_evt_event where evt_index='".$evt_index."'");
					while ($row_evt = mysqli_fetch_array($result_evt))
					{
						$app = $row_evt["evt_appendix"];
						$img = $row_evt["evt_image"];
					}
					if (strlen($app)>0) unlink("uploads/app/".$app);
					if (strlen($img)>0) unlink("uploads/img/".$img);
					
					mysqli_query ($dbconn, "DELETE FROM ".$prefix."evt_event WHERE evt_index='".$evt_index."'");
				}	
				else
				{
					if ($evt_delete_img  == 1) 
					{
						$img = "";
						$result_evt = mysqli_query ($dbconn, "SELECT evt_image,evt_appendix FROM tbl_evt_event where evt_index='".$evt_index."'");
						while ($row_evt = mysqli_fetch_array($result_evt))
						{
							$img = $row_evt["evt_image"];
						}
						if (strlen($img)>0) unlink("uploads/img/".$img);
					}
					if ($evt_delete_app  == 1) 
					{
						$img = "";
						$result_evt = mysqli_query ($dbconn, "SELECT evt_image,evt_appendix FROM tbl_evt_event where evt_index='".$evt_index."'");
						while ($row_evt = mysqli_fetch_array($result_evt))
						{
							$app = $row_evt["evt_appendix"];
						}
						if (strlen($app)>0) unlink("uploads/app/".$img);
					}
						
					mysqli_query ($dbconn, "UPDATE ".$prefix."evt_event SET evt_date='".$evt_date."', evt_header='".$evt_header."', evt_text='".$evt_text."', evt_evc_index=".$evc_index.", evt_appendix='".$evt_appendix."', evt_image='".$evt_image."' WHERE evt_index='".$evt_index."'");	
				}										
			}

		//	if ( isset( $files['evt_image'] ) && !empty($files['evt_image']['name'] ) ) uploadfile($files['evt_image'], "img");
		//	if ( isset( $files['evt_appendix'] ) && !empty($files['evt_appendix']['name'] ) ) uploadfile($files['evt_appendix'], "app");
			
		echo json_encode(array("retval"=>1, "msg_id"=>0, "data"=>array()));
	}
	
	elseif ($function == "getLaundry")
	{ 
		$max_count = 5;
		$count = 0;
		$arr = json_decode(file_get_contents("php://input"), true);
		$usr_index = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				
			}
		}
		
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result_evt = mysqli_query ($dbconn, "SELECT lau_index, lau_usr_index, usr_name, lau_date, DATE_FORMAT(lau_date, '%d.%m.') as lau_date_s FROM tbl_lau_laundry LEFT JOIN tbl_usr_user on lau_usr_index=usr_index order by lau_date asc");
				while ($row_evt = mysqli_fetch_array($result_evt))
				{
					
					$lau_count = 0; 
					if ($row_evt["lau_usr_index"] > 0)
					{
						$result_cou = mysqli_query ($dbconn, "SELECT count(lau_index) as lau_count FROM tbl_lau_laundry WHERE lau_usr_index=".$row_evt["lau_usr_index"]." and lau_date<='".$row_evt["lau_date"]."'");
						while ($row_cou = mysqli_fetch_array($result_cou)) $lau_count = $row_cou["lau_count"];
					}				
					$data[] = array("lau_index"=>$row_evt["lau_index"], "usr_index"=>$row_evt["lau_usr_index"], "usr_name"=>$row_evt["usr_name"], "lau_date"=>$row_evt["lau_date_s"], "lau_date_real"=>$row_evt["lau_date"], "lau_count"=>$lau_count);
				}
				$retval = 1;
			}
	
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	elseif ($function == "insdelLaundry")
	{
		// alle kinder inkl zeit und status
			$arr = json_decode(file_get_contents("php://input"), true);
			$action = "";
			$post = array();
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "action") $action = $value;
						if($key == "post") $post = $value;
					}
				}
			}
					
			$lau_index = 0; if (isset ($post['lau_index'])) $lau_index = $post['lau_index']; 
			$usr_index = 0; if (isset ($post['usr_index'])) $usr_index = $post['usr_index']; 
						
			if ($action == "ins")
			{
				mysqli_query ($dbconn, "UPDATE ".$prefix."lau_laundry set lau_usr_index=".$usr_index." WHERE lau_index=".$lau_index);	
			}
			elseif ($action == "del")
			{
				mysqli_query ($dbconn, "UPDATE ".$prefix."lau_laundry set lau_usr_index=0 WHERE lau_index=".$lau_index);										
			}
			
		echo json_encode(array("retval"=>1, "msg_id"=>0, "data"=>array()));
	}	

	elseif ($function == "getEventlists")
	{
		$count = 0;
		$arr = json_decode(file_get_contents("php://input"), true);
		$evt_index = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "evt_index") $evt_index = $value;
				}	
			}
		}
		
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();

			if ($session)
			{
				$filter = ""; if ($evt_index > 0) $filter = " and lem_evt_index='".$evt_index."'";
				$result_evt = mysqli_query ($dbconn, "SELECT lis_index, lis_name, lis_text, lis_commentenabled, lem_evt_index FROM tbl_lis_list 
														INNER JOIN tbl_lem_listeventmapping on lis_index=lem_lis_index".$filter." 
															LEFT JOIN tbl_evt_event on lem_evt_index=evt_index
														WHERE evt_date>=DATE(NOW())	");
				while ($row_evt = mysqli_fetch_array($result_evt))
				{
					$data[] = array("lis_index"=>$row_evt["lis_index"], "lis_name"=>$row_evt["lis_name"], "lis_text"=>$row_evt["lis_text"], "lis_commentenabled"=>$row_evt["lis_commentenabled"], "evt_index"=>$row_evt["lem_evt_index"]);
				}
				$retval = 1;
			}
	
		echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}		

	
	elseif ($function == "getEventlistItems")
	{ 
		$count = 0;
		$arr = json_decode(file_get_contents("php://input"), true);
		$lis_index = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "lis_index") $lis_index = $value;
				}
			}
		}
		
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			
			if ($session)
			{
				$result_evt = mysqli_query ($dbconn, "SELECT lie_index, lie_lis_index, lie_text, lie_usr_index, lie_comment, usr_name FROM tbl_lie_listelement LEFT JOIN tbl_usr_user on lie_usr_index=usr_index WHERE lie_lis_index=".$lis_index);
				while ($row_evt = mysqli_fetch_array($result_evt))
				{
					$data[] = array("lie_index"=>$row_evt["lie_index"], "lie_lis_index"=>$row_evt["lie_lis_index"], "lie_text"=>$row_evt["lie_text"], "lie_usr_index"=>$row_evt["lie_usr_index"], "lie_comment"=>$row_evt["lie_comment"],"usr_name"=>$row_evt["usr_name"]);
				}
				$retval = 1;
			}
	
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}

	elseif ($function == "updateEventlistItem")
	{
		// alle kinder inkl zeit und status
			$arr = json_decode(file_get_contents("php://input"), true);
			$action = "";
			$post = array();
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "action") $action = $value;
						if($key == "post") $post = $value;
					}
				}
			}
					
			$lie_index = 0; if (isset ($post['lie_index'])) $lie_index = $post['lie_index']; 
			$usr_index = 0; if (isset ($post['usr_index'])) $usr_index = $post['usr_index']; 
			$lie_comment = ""; if (isset ($post['lie_comment'])) $lie_comment = $post['lie_comment']; 
						
			if ($action == "upd")
			{
				mysqli_query ($dbconn, "UPDATE tbl_lie_listelement set lie_usr_index=".$usr_index.", lie_comment='".$lie_comment."' WHERE lie_index=".$lie_index);	
			}
			elseif ($action == "del")
			{
				mysqli_query ($dbconn, "UPDATE tbl_lie_listelement set lie_usr_index=0, lie_comment='' WHERE lie_index=".$lie_index);										
			}
			
		echo json_encode(array("retval"=>1, "msg_id"=>0, "data"=>array()));
	}	

	elseif ($function == "insupdList")
	{
		// alle kinder inkl zeit und status
		$arr = json_decode(file_get_contents("php://input"), true);
		$action = "";
		$post = array();
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "action") $action = $value;
					if($key == "post") $post = $value;
				}
			}
		}
				
		$lis_index = ""; if (isset ($post['lis_index'])) $lis_index = $post['lis_index']; 
		$lis_name = "";  if (isset ($post['lis_name'])) $lis_name = $post['lis_name'];
		$lis_text = "";  if (isset ($post['lis_text'])) $lis_text = $post['lis_text'];

		$evt_index = 0; if (isset ($post['evt_index'])) $evt_index = $post['evt_index']; 
		$lies = array(); if (isset ($post['lies'])) $lies = explode("#",$post['lies']); 
		
		$lie_text_0 = "";  if (isset ($post['lie_text_0'])) $lie_text_0 = $post['lie_text_0'];


		$lis_commentenabled = 0; if (isset ($post['lis_commentenabled'.$lis_index])) $lis_commentenabled = $post['lis_commentenabled'.$lis_index]; 

		$lis_delete = 0; if (isset ($post['lis_delete'.$lis_index])) $lis_delete = $post['lis_delete'.$lis_index]; 
		
		if ($action == "ins")
		{
			mysqli_query ($dbconn, "INSERT INTO ".$prefix."lis_list (lis_name, lis_text, lis_commentenabled) VALUES ('".$lis_name."', '".$lis_text."', ".$lis_commentenabled.")");	
			$lis_index = mysqli_insert_id($dbconn);
			if ($evt_index > 0)
				mysqli_query ($dbconn, "INSERT INTO ".$prefix."lem_listeventmapping (lem_lis_index, lem_evt_index) VALUES (".$lis_index.", ".$evt_index.")");

			foreach($lies as $lie)	
			{
				mysqli_query ($dbconn, "INSERT INTO ".$prefix."lie_listelement (lie_lis_index, lie_text, lie_usr_index) VALUES (".$lis_index.", '".$lie."', 0)");
			}	
		}
		elseif ($action == "upd")
		{
			if ($lis_delete == 1)
			{
				mysqli_query ($dbconn, "DELETE FROM ".$prefix."lie_listelement WHERE lie_lis_index='".$lis_index."'");
				mysqli_query ($dbconn, "DELETE FROM ".$prefix."lem_listeventmapping WHERE lem_lis_index='".$lis_index."'");
				mysqli_query ($dbconn, "DELETE FROM ".$prefix."lis_list WHERE lis_index='".$lis_index."'");
			}	
			else
			{
				mysqli_query ($dbconn, "UPDATE ".$prefix."lis_list SET lis_text='".$lis_text."', lis_name='".$lis_name."', lis_commentenabled='".$lis_commentenabled."' WHERE lis_index='".$lis_index."'");	
				if ($evt_index > 0)
					mysqli_query ($dbconn, "UPDATE ".$prefix."lem_listeventmapping set lem_evt_index=".$evt_index." where lem_lis_index=".$lis_index);

				if (strlen($lie_text_0)>0)	
					mysqli_query ($dbconn, "INSERT INTO ".$prefix."lie_listelement (lie_lis_index, lie_text, lie_usr_index) VALUES (".$lis_index.", '".$lie_text_0."', 0)");
				
				$result_evt = mysqli_query ($dbconn, "SELECT lie_index, lie_lis_index, lie_text, lie_usr_index FROM tbl_lie_listelement");
				while ($row_evt = mysqli_fetch_array($result_evt))
				{
					if (isset($post['lie_text_'.$row_evt["lie_index"]]))
					{
						if (strlen($post['lie_text_'.$row_evt["lie_index"]])==0)
							mysqli_query ($dbconn, "DELETE FROM ".$prefix."lie_listelement WHERE lie_index='".$row_evt["lie_index"]."'");
						else
							mysqli_query ($dbconn, "UPDATE ".$prefix."lie_listelement set lie_text='".$post['lie_text_'.$row_evt["lie_index"]]."' where lie_index=".$row_evt["lie_index"]);	
					}
				}

			}										
		}


		
	echo json_encode(array("retval"=>1, "msg_id"=>0, "data"=>array()));
	}

	
	elseif ($function == "updateToken")
	{
		// alle kinder inkl zeit und status
		$arr = json_decode(file_get_contents("php://input"), true);
		$token = "";
		$lid_id = 0;
		
		foreach ($arr as $dataKey => $dataValue) 
		{
			if($dataKey == "session") $session = checksession($dbconn, $dataValue);
			if($dataKey == "data") 
			{
				foreach ($dataValue as $key => $value) 
				{
					if($key == "token") $token = $value;
					if($key == "lid_id") $lid_id = $value;
				}
			}
		}
		
		// Debug-Ausgabe entfernt (schrieb Push-Tokens in log.txt)
		if ((strlen($token)>0) && ($lid_id>0))
    	{ 
			//token alread exists ?
			$tok_index = 0;
			$result_tok = mysqli_query ($dbconn, "SELECT tok_index, tok_lin_index, tok_token FROM tbl_tok_token WHERE tok_token like '".$token."'");
			while ($row_tok = mysqli_fetch_array($result_tok))
			{
				if ($row_tok["tok_lin_index"] != $lid_id)
				{
					mysqli_query ($dbconn, "DELETE FROM tbl_tok_token WHERE tok_index='".$row_tok["tok_index"]."'");
				}
				else
				{
					$tok_index = $row_tok["tok_index"];
				}
			}

			if ($tok_index == 0)
			{
				mysqli_query ($dbconn, "INSERT INTO tbl_tok_token (tok_lin_index, tok_token) VALUES (".$lid_id.", '".$token."')");	
			}
		}

		echo json_encode(array("retval"=>1, "msg_id"=>0, "data"=>array()));
	}

	

?>