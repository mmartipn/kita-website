<?php
	header("Access-Control-Allow-Origin: *");
	header('Content-type: application/json');
	include 'functions.php';
	$dbconn = sqlconnect();
	$prefix = "tbl_";
	$session = false;
	
	$function = ""; if (isset ($_REQUEST["function"]))  $function = $_REQUEST["function"];

	if ($function == "getTempstaff")
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
				$filter = "where usr_index=".$usr_index; if ($alleducators == 1) $filter = "where usr_uty_index=3 order by usr_name asc";
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
					$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE abs_date".$direction."CURDATE() and abs_uty_index=3 and abs_status=0 ORDER BY abs_date asc");
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
						$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index WHERE usr_index=".$usr_index." and abs_date=".$abs_date." and abs_uty_index=3 and abs_status=0 ORDER BY abs_date asc");
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
						$result = mysqli_query ($dbconn, "SELECT abs_index, DATE_FORMAT(abs_date, '%d.%m.%Y') as abs_date_s, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abr_text, usr_name FROM ".$prefix."usr_user INNER JOIN ".$prefix."abs_absence on usr_index=abs_ref_index INNER JOIN ".$prefix."abr_absencereason on abr_index=abs_abr_index WHERE usr_index=".$usr_index." and abs_date".$direction."CURDATE() and abs_uty_index=3 and abs_status=0 ORDER BY abs_date asc");
						while ($row = mysqli_fetch_array($result))
						{
							$data[]= array("abs_index"=>$row["abs_index"], "abs_date"=>$row["abs_date_s"], "usr_index"=>$row["abs_ref_index"], "abs_abr_index"=>$row["abs_abr_index"], "abr_text"=>$row["abr_text"], "usr_name"=>$row["usr_name"]);
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
							//besteht für diesen erzieher an diesem tag schon eine abw ?
							$result = mysqli_query ($dbconn, "SELECT abs_index, abs_date, abs_ref_index, abs_uty_index, abs_status, abs_abr_index FROM ".$prefix."abs_absence WHERE abs_ref_index=".$usr_index." and abs_date='".$date."' and abs_uty_index=3 and abs_status=0");
							if (mysqli_num_rows($result) == 0) 
							{
								mysqli_query ($dbconn, "INSERT INTO ".$prefix."abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index) VALUES ('".$date."', ".$usr_index.", 3, ".$abr_index.")");
							}
							else
							{
								//schon abw
							}
						}
						else
						{
							//schliesstag
						}
					}	
					else
					{
						//wochenende
					}
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>array()));
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
				$result = mysqli_query ($dbconn, "SELECT abs_index FROM ".$prefix."abs_absence WHERE abs_date='".$abs_date."' and abs_uty_index=1 and abs_status=0 and abs_abr_index=3");
				while ($row = mysqli_fetch_array($result)) $personalmangel = true;
				
				if ($personalmangel)
				{
					if ($uty_index == 4)
					{
						mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_index);
						mysqli_query ($dbconn, "DELETE FROM ".$prefix."job_joboffer WHERE job_abs_index=".$abs_index);
					}
				}
				else
				{
					mysqli_query ($dbconn, "UPDATE ".$prefix."abs_absence set abs_status=1 WHERE abs_index=".$abs_index);
					mysqli_query ($dbconn, "DELETE FROM ".$prefix."job_joboffer WHERE job_abs_index=".$abs_index);
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
	elseif ($function == "getAnswers")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$job_index = 0;
			$usr_index = 0;
			$type = "";
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "job_index") $job_index = $value;
						if($key == "usr_index") $usr_index = $value;
						if($key == "type") $type = $value;
					}
				}
			}
			
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				if ($job_index>0)
				{
					$result = mysqli_query ($dbconn, "SELECT usr_index, joa_index, joa_job_index, joa_from, joa_to, joa_status, joa_usr_index, usr_name, usr_index
														FROM ".$prefix."usr_user 
														LEFT JOIN ".$prefix."joa_jobanswer on joa_usr_index=usr_index and joa_job_index=".$job_index."
														WHERE usr_uty_index=3");
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("joa_index"=>$row["joa_index"], "joa_job_index"=>$row["joa_job_index"],"joa_usr_index"=>$row["joa_usr_index"],"joa_from"=>$row["joa_from"], "joa_to"=>$row["joa_to"], "joa_status"=>$row["joa_status"], "usr_name"=>$row["usr_name"], "usr_index"=>$row["usr_index"]);
						$retval = 1;
					}
				}
				elseif ($usr_index>0)
				{
					if ($type == "mine")
					{
						$result = mysqli_query ($dbconn, "SELECT joa_index, joa_job_index, joa_usr_index, joa_from, joa_to, joa_status, DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s, job_status
															FROM ".$prefix."joa_jobanswer 
															INNER JOIN ".$prefix."job_joboffer on joa_job_index=job_index 
															INNER JOIN ".$prefix."usr_user on joa_usr_index=usr_index
															WHERE joa_usr_index=".$usr_index." and job_date>=CURDATE() and job_status<3");
						while ($row = mysqli_fetch_array($result))
						{
							$data[]= array("joa_index"=>$row["joa_index"], "joa_job_index"=>$row["joa_job_index"],"joa_usr_index"=>$row["joa_usr_index"],"joa_from"=>$row["joa_from"], "joa_to"=>$row["joa_to"], "joa_status"=>$row["joa_status"], "job_date"=>$row["job_date_s"], "job_status"=>$row["job_status"]);
							$retval = 1;
						}
					}
					elseif ($type == "mine_positive")
					{
						$result = mysqli_query ($dbconn, "SELECT joa_index, joa_job_index, joa_usr_index, joa_from, joa_to, joa_status, DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s
															FROM ".$prefix."joa_jobanswer 
															INNER JOIN ".$prefix."job_joboffer on joa_job_index=job_index 
															INNER JOIN ".$prefix."usr_user on joa_usr_index=usr_index
															WHERE joa_usr_index=".$usr_index." and job_date>=CURDATE() and job_status=1");
						while ($row = mysqli_fetch_array($result))
						{
							$data[]= array("joa_index"=>$row["joa_index"], "joa_job_index"=>$row["joa_job_index"],"joa_usr_index"=>$row["joa_usr_index"],"joa_from"=>$row["joa_from"], "joa_to"=>$row["joa_to"], "joa_status"=>$row["joa_status"], "job_date"=>$row["job_date_s"]);
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
	elseif ($function == "deleteAnswer")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$joa_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "joa_index") 
						{
							$joa_index = $value;
						}
					}
				}
			}	
		//logic
			$retval = 0;
			if ($session)
			{
				$retval = 1;
				mysqli_query ($dbconn, "DELETE FROM ".$prefix."joa_jobanswer  WHERE joa_index=".$joa_index);
				
				//if joa was selected, reactive job and joas:
				$result = mysqli_query ($dbconn, "SELECT job_index FROM ".$prefix."job_joboffer WHERE job_joa_index=".$joa_index);
				while ($row = mysqli_fetch_array($result))
				{
					mysqli_query ($dbconn, "UPDATE ".$prefix."job_joboffer set job_joa_index=0, job_status=0 where job_index=".$row["job_index"]);
					mysqli_query ($dbconn, "UPDATE ".$prefix."joa_jobanswer set joa_status=0 where joa_job_index=".$row["job_index"]);
				}
			}
			else
			{
				$retval = 3;
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>array()));
	}
	elseif ($function == "insertAnswer")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$job_index = 0;
			$usr_index = 0;
			$joa_from = "";
			$joa_to = "";
			$joa_status = 2;
						
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "job_index") $job_index = $value;
						if($key == "usr_index") $usr_index = $value;
						if($key == "joa_from") $joa_from = $value;
						if($key == "joa_to") $joa_to = $value;
						if($key == "joa_status") $joa_status = $value;
					}
				}
			}	
			
		//logic
			$retval = 0;
			if ($session)
			{
				$retval = 1;
				
				//gibt es schon eine antwort vom selben user auf diesen Job?
				$result = mysqli_query ($dbconn, "SELECT joa_index FROM ".$prefix."joa_jobanswer WHERE joa_usr_index=".$usr_index." and joa_job_index=".$job_index." and joa_status<3");
				while ($row = mysqli_fetch_array($result)) mysqli_query ($dbconn, "DELETE FROM ".$prefix."joa_jobanswer WHERE joa_index=".$row["joa_index"]);
				
				//insert
				mysqli_query ($dbconn, "INSERT INTO ".$prefix."joa_jobanswer(joa_job_index, joa_usr_index, joa_from, joa_to, joa_status) VALUES (".$job_index.", ".$usr_index.", '".$joa_from."', '".$joa_to."', ".$joa_status.")");
				$joa_index=mysqli_insert_id($dbconn);
				
			//	calculate2($dbconn, $job_index, $joa_index);
			}
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>0, "data"=>array()));
	}
	
	elseif ($function == "getJobs")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$job_index = 0;
			$usr_index = 0;
			$type = "";
			
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") $session = checksession($dbconn, $dataValue);
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "job_index") $joa_index = $value;
						if($key == "usr_index") $usr_index = $value;
						if($key == "type") $type = $value;
					}
				}
			}
			
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				/*if ($joa_index>0)
				{
					$result = mysqli_query ($dbconn, "SELECT joa_index, joa_job_index, joa_usr_index, joa_from, joa_to, joa_status FROM ".$prefix."joa_jobanswer WHERE=".$joa_index);
					while ($row = mysqli_fetch_array($result))
					{
						$data[]= array("joa_index"=>$row["joa_index"]);
						$retval = 1;
					}
				}
				else
				{*/
					if ($type == "open")
					{
						$result = mysqli_query ($dbconn, "SELECT job_index, job_joa_index, job_date, DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s, job_from, job_to, job_status FROM ".$prefix."job_joboffer WHERE job_date>=CURDATE() and job_status=0 order by job_date asc");
						while ($row = mysqli_fetch_array($result))
						{
							$result_joa = mysqli_query ($dbconn, "SELECT joa_index, joa_job_index, joa_usr_index, joa_from, joa_to, joa_status FROM ".$prefix."joa_jobanswer WHERE joa_job_index=".$row["job_index"]." and joa_status<3 and joa_usr_index=".$usr_index);
							if (mysqli_num_rows($result_joa) == 0)
							{
								$data[]= array("job_index"=>$row["job_index"], "job_date"=>$row["job_date"],"job_date_s"=>$row["job_date_s"],"job_from"=>$row["job_from"],"job_to"=>$row["job_to"], "job_status"=>$row["job_status"]);
							}
							$retval = 1;
						}
					}
					if ($type == "all")
					{
						$result = mysqli_query ($dbconn, "SELECT job_index, job_joa_index, job_date, DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s, job_from, job_to, job_status FROM ".$prefix."job_joboffer WHERE job_date>=CURDATE() and job_status<2  order by job_date asc");
						while ($row = mysqli_fetch_array($result))
						{
							$joas = "";
							$result_joa = mysqli_query ($dbconn, "SELECT joa_from, joa_to, usr_name FROM ".$prefix."joa_jobanswer INNER JOIN ".$prefix."usr_user on usr_index=joa_usr_index WHERE joa_status=1 and joa_job_index=".$row["job_index"]);
							while ($row_joa = mysqli_fetch_array($result_joa))
							{
								if (strlen($joas) > 0) $joas = $joas."<br>";
								$joas = $joas.$row_joa["usr_name"]." (".$row_joa["joa_from"]."-".$row_joa["joa_to"].")";
							}
							$data[]= array("job_index"=>$row["job_index"], "job_date"=>$row["job_date"], "job_date_s"=>$row["job_date_s"],"job_from"=>$row["job_from"],"job_to"=>$row["job_to"], "job_status"=>$row["job_status"], "joas"=>$joas);
							$retval = 1;
						}
					}
			//	}
			}
			else
			{
				$retval = 3;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
			
	}
	

?>