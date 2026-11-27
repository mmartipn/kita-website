<?php

	function sqlconnect()
	{
		include 'sqlconn.php';
		$dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		if (!$dbconn)die('keine Verbindung zur Datenbank möglich. ' . mysqli_connect_error(). mysqli_error($dbconn));
		$db_selected = mysqli_select_db($dbconn, $dbname);
		if (!$db_selected) die ('Kann Datenbank nicht benutzen : ' . mysqli_error($dbconn));
		return $dbconn;
	}
		
	function checksession($dbconn, $sessionkey)
	{
		$result_ses = mysqli_query ($dbconn, "SELECT ses_index FROM tbl_ses_session WHERE ses_key = '".$sessionkey."'");
		if (mysqli_num_rows ($result_ses) == 0)
		{
			return false;
		}
		return true;
	}

	function sendmessage($dbconn, $header, $body, $usr_index)
	{
		
		if ($usr_index == 0) //Signal an Gruppe
		{
	//		mysqli_query ($dbconn, "INSERT INTO tbl_mes_message (mes_usr_index, mes_usr_email, mes_usr_signal, mes_header, mes_text, mes_status) VALUES(0, '', 'gruppe', '".$header."', '".$body."', 0)");
		}
		elseif ($usr_index == -1) //Signal an Notdienst Gruppe
		{
	//		mysqli_query ($dbconn, "INSERT INTO tbl_mes_message (mes_usr_index, mes_usr_email, mes_usr_signal, mes_header, mes_text, mes_status) VALUES(-1, '', 'gruppe', '".$header."', '".$body."', 0)");
		}
		elseif ($usr_index == -2) //Signal an Admins
		{
	//		mysqli_query ($dbconn, "INSERT INTO tbl_mes_message (mes_usr_index, mes_usr_email, mes_usr_signal, mes_header, mes_text, mes_status) VALUES(-2, '', '".$header."', '', '".$body."', 0)");
		}
		else
		{
			$result_mes = mysqli_query ($dbconn, "SELECT usr_index, usr_name, lin_email, lin_phone, lin_email__notify, lin_phone__notify FROM tbl_lin_login INNER JOIN tbl_usr_user on lin_usr_index=usr_index where usr_index=".$usr_index);
			while ($row_mes = mysqli_fetch_array($result_mes))
			{
				$email = "";
				$signal = "";
				if ($row_mes["lin_email__notify"] == 1) 
				{
					$email = $row_mes["lin_email"];
					sendmail($email, $header, $body);
					mysqli_query ($dbconn, "INSERT INTO tbl_mes_message (mes_usr_index, mes_usr_email, mes_usr_signal, mes_header, mes_text, mes_status) VALUES(".$row_mes["usr_index"].", '".$email."', '', '".$header."', '".$body."', 2)");
				}
	/*			if ($row_mes["lin_phone__notify"] == 1)
				{
					$signal = $row_mes["lin_phone"];
					mysqli_query ($dbconn, "INSERT INTO tbl_mes_message (mes_usr_index, mes_usr_email, mes_usr_signal, mes_header, mes_text, mes_status) VALUES(".$row_mes["usr_index"].", '', '".$signal."', '".$header."', '".$body."', 0)");
				}*/
			}
		}
		
	}
	
	function sendmail($email, $header, $body)
	{
		include_once('phpmailer/PHPMailerAutoload.php');
			
		$mail = new PHPMailer(); 
		date_default_timezone_set('Etc/UTC');
		$mail->isSMTP();
		//$mail->SMTPDebug = 2;
		//$mail->Debugoutput = 'html';
		include_once __DIR__ . '/../config.php';
		$mail->Host = env('SMTP_HOST', '');
		$mail->Port = intval(env('SMTP_PORT', '587'));
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->IsHTML(true);
		$mail->Username = env('SMTP_USER', '');
		$mail->Password = env('SMTP_PASSWORD', '');
		$mail->SetFrom(env('SMTP_FROM', 'noreply@example.com'), env('SMTP_FROM', 'noreply@example.com'));
		$mail->addReplyTo(env('SMTP_FROM', 'noreply@example.com'), env('SMTP_FROM', 'noreply@example.com'));
		$mail->addAddress("".$email, '');
		$mail->Subject = "".$header;
		$mail->Body = "".$body;
		if ($mail->send()) 
			return true;
		else
			return false;	
	}
	
	
	function germanDateAsText($ukdate)
	{
		$gerdate = "";
		$ukdate = str_replace("'", "", $ukdate);
		if (strpos($ukdate, "-")>0)
		{
			$a = explode("-", $ukdate);
			$gerdate = $a[2].".".$a[1].".".$a[0];
		}
		else
			$gerdate = $ukdate;
		return $gerdate;
		
	}
	
	function getbestjoa($dbconn, $job_index)
	{
		$bestdiff = 0;
		$bestid = 0;
		$result_joa = mysqli_query ($dbconn, "SELECT joa_index, joa_from, joa_to FROM tbl_joa_jobanswer where joa_job_index=".$job_index." and joa_status<2"); 
		while ($row_joa = mysqli_fetch_array($result_joa)) 
		{
			$diff = getjoadiff($dbconn, $row_joa["joa_index"]);
			
			if ($diff > $bestdiff)
			{
				$bestdiff = $diff;
				$bestid = $row_joa["joa_index"];
			}
		}
		return $bestid;
	}
	
	function isfullycovered ($dbconn, $joa_index)
	{
		$fulltime = false;
		$result = mysqli_query ($dbconn, "SELECT joa_from, joa_to, job_from, job_to FROM tbl_joa_jobanswer INNER JOIN tbl_job_joboffer on joa_job_index=job_index WHERE joa_index=".$joa_index);
		while ($row = mysqli_fetch_array($result)) 
		{
			$job_from_a = explode(":", $row["job_from"]);
			$joa_from_a = explode(":", $row["joa_from"]);
			$job_to_a = explode(":", $row["job_to"]);
			$joa_to_a = explode(":", $row["joa_to"]);
			
			if (((intval($job_from_a[0]) == intval($joa_from_a[0])) && (intval($job_from_a[1]) == intval($joa_from_a[1])))
			   && ((intval($job_to_a[0]) == intval($joa_to_a[0])) && (intval($job_to_a[1]) == intval($joa_to_a[1]))))
			{
				$fulltime = true;
			}
		}
		return $fulltime;
	}
	
	function getjobjoadiff ($dbconn, $joa_index)
	{
		$missing = 0;
		$result = mysqli_query ($dbconn, "SELECT joa_from, joa_to, job_from, job_to FROM tbl_joa_jobanswer INNER JOIN tbl_job_joboffer on joa_job_index=job_index WHERE joa_index=".$joa_index);
		while ($row = mysqli_fetch_array($result)) 
		{
			$job_from_a = explode(":", $row["job_from"]);
			$joa_from_a = explode(":", $row["joa_from"]);
			$job_to_a = explode(":", $row["job_to"]);
			$joa_to_a = explode(":", $row["joa_to"]);
			
		/*	if (((intval($job_from_a[0]) == intval($joa_from_a[0])) && (intval($job_from_a[1]) == intval($joa_from_a[1])))
			   && ((intval($job_to_a[0]) == intval($joa_to_a[0])) && (intval($job_to_a[1]) == intval($joa_to_a[1]))))
			{
				$missing = 0;
			}*/
			/*
			if ($row_joa["joa_to"] == "15:00") $fehlende_stunden_gt = 0;
			if ($row_joa["joa_to"] == "14:00") $fehlende_stunden_gt = 1;
			if ($row_joa["joa_to"] == "13:00") $fehlende_stunden_gt = 3;
			if ($row_joa["joa_to"] == "12:00") $fehlende_stunden_gt = 3;
			*/
			
			
			$missing = intval($job_to_a[0]) - intval($joa_to_a[0]);
			if ($missing  == 2) $missing  =3;
		}
		return $missing;
	}
	
	function getjoadiff($dbconn, $joa_index)
	{
		$diff = 0.0;
		$result_joa = mysqli_query ($dbconn, "SELECT joa_index, joa_from, joa_to FROM tbl_joa_jobanswer where joa_index=".$joa_index." and joa_status<2"); 
		while ($row_joa = mysqli_fetch_array($result_joa)) 
		{
			$joa_from_a = explode(":", $row_joa["joa_from"]);
			$joa_to_a = explode(":", $row_joa["joa_to"]);
			$joa_from_n = floatval($joa_from_a[0]); if( floatval($joa_from_a[1]) == 30) $joa_from_n = $joa_from_n + 0.5;
			$joa_to_n = floatval($joa_to_a[0]); if( floatval($joa_to_a[1]) == 30) $joa_to_n = $joa_to_n + 0.5;
			
			$diff = ($joa_to_n-$joa_from_n);
		}
		return $diff;
	}
	
	/*
	function isParttimeDay($dbconn, $date) 
	{
		$parttime_is_sick = false;
		$educators_not_coming = 0;
		$weekDay = date('w', strtotime($date));
		$f_or_t = ($weekDay == 4 || $weekDay == 5);
		if ($f_or_t)
		{
			$educators_not_coming = 0;
			$result_pt = mysqli_query ($dbconn, "SELECT abs_ref_index FROM tbl_abs_absence where abs_date='".$date."' and abs_uty_index=2 and abs_abr_index<3 and abs_status=0");
			while ($row_pt= mysqli_fetch_array($result_pt)) 
			{
				$educators_not_coming ++;
				if ($row_pt["abs_ref_index"] == PARTTIME_EDUCATOR_ID) $parttime_is_sick = true;
			}
		}
		if ($parttime_is_sick) $educators_not_coming = 0;
		return $educators_not_coming;
	}*/
	
	function calculate2($dbconn, $job_index, $joa_index)	
	{
		//er hat ja gesagt: joa_status = 0
			//er hat voll zugesagt
				//->nehmen, fertig
			// er hat teilw zugesagt
				//es fehlen noch weitere antworten
					//->fertig
				//all haben geantwortet
					//beste antwort nehmen
		//er hat nein gesagt joa_status = 2
				//es fehlen noch weitere antworten
					//->fertig
				//all haben geantwortet
					//beste antwort nehmen
					
		$answer_is_fulltime = isfullycovered($dbconn, $joa_index);
		$answers_missing = false;			
		$joa_from = "";
		$joa_to = "";
		$joa_status = 0;
		$job_date = "";
		$result = mysqli_query ($dbconn, "SELECT joa_from, joa_to, joa_status, job_date FROM tbl_joa_jobanswer INNER JOIN tbl_job_joboffer on job_index=joa_job_index WHERE joa_index=".$joa_index);
		while ($row = mysqli_fetch_array($result)) 
		{
			$joa_from = $row["joa_from"];
			$joa_to = $row["joa_to"];
			$joa_status = $row["joa_status"];
			$job_date = $row["job_date"];
		}
		//haben alle aushilfen bereits zu oder abgesagt
		$result_usr = mysqli_query ($dbconn, "SELECT usr_index FROM tbl_usr_user INNER JOIN tbl_lin_login on lin_usr_index=usr_index where usr_uty_index=3 and lin_active=1");
		while ($row_usr = mysqli_fetch_array($result_usr))
		{
			$result_joa = mysqli_query ($dbconn, "SELECT joa_index FROM tbl_joa_jobanswer where joa_usr_index=".$row_usr["usr_index"]." and joa_job_index='".$job_index."' and joa_status<3");
			if (mysqli_num_rows($result_joa) == 0)
			{
				$answers_missing = true;
			}
		}
		
		if ($joa_status == 0) //er hat ja gesagt
		{
			if ($answer_is_fulltime) //er hat voll zugesagt
			{
				//nehmen. andere absagen
				book ($dbconn, $job_index, $joa_index);
			}
			else // er hat teilw zugesagt
			{
				if ($answers_missing)
				{
					//nix machen
				}
				else
				{
					//beste antwort nehmen
					book ($dbconn, $job_index, getbestjoa($dbconn, $job_index));
				}
			}
		}
		else //er hat nein gesagt
		{
			if ($answers_missing)
			{
				//nix machen
			}
			else
			{
				//joa_index__best nehmen. andere absagen
				book ($dbconn, $job_index, getbestjoa($dbconn, $job_index));
			}
		}
		/*
file_put_contents("log.txt", "\r\n".date("H:i"). "\r\n\t"."answer_is_fulltime:".$answer_is_fulltime
		. "\r\n\t"."joa_status:".$joa_status
		. "\r\n\t"."answers_missing:".$answers_missing
		. "\r\n\t"."job_index:".$job_index
		. "\r\n\t"."joa_index:".$joa_index
		. "\r\n\t"."joa_diff:".getjoadiff($dbconn, $joa_index), FILE_APPEND);
		*/
		
	}
	
	function book ($dbconn, $job_index, $joa_index)
	{
		if ($joa_index == 0) // es gibt keine zusage
		{
			mysqli_query ($dbconn, "UPDATE tbl_job_joboffer set job_status=2, job_joa_index=0 where job_index=".$job_index);
		}
		else
		{
			//bestätige job
			mysqli_query ($dbconn, "UPDATE tbl_job_joboffer set job_status=1, job_joa_index=".$joa_index." where job_index=".$job_index);
			
			$result_job = mysqli_query ($dbconn, "SELECT joa_index, joa_usr_index, joa_status, DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s FROM tbl_job_joboffer INNER JOIN tbl_joa_jobanswer on job_index=joa_job_index WHERE job_index=".$job_index);
			while ($row_job = mysqli_fetch_array($result_job)) 
			{
				if ($row_job["joa_index"] == $joa_index) //bestätige joa
				{	
					mysqli_query ($dbconn, "UPDATE tbl_joa_jobanswer set joa_status=1 where joa_index=".$joa_index);
				//	sendmessage($dbconn, "Zusage fuer den ".$row_job["job_date_s"]." in der Kita", "Zusage fuer den ".$row_job["job_date_s"]." in der Kita.", $row_job["joa_usr_index"]);
				}
				else //sage alle anderen joa ab
				{
					if ($row_job["joa_status"] == 0) //user hatte ja gesagt, aber nicht in vz
					{
						mysqli_query ($dbconn, "UPDATE tbl_joa_jobanswer set joa_status=2 where joa_index=".$joa_index);
						//todo: gibt es andere jobs am selben tag wo der user noch nicht geantwortethat ?
					//	sendmessage($dbconn, "Absage fuer den ".$row_job["job_date_s"]." in der Kita", "Vielen Dank fuer dein Angebot, aber leider hat eine andere Aushilfe laenger zeit, deshalb muessen wir die Absage fuer den ".$row_job["job_date_s"]." leider absagen.", $row_job["joa_usr_index"]);
					}
				}
			}	
			//erstelle joas für die die noch nicht geantwortet haben
			$result_usr = mysqli_query ($dbconn, "SELECT usr_index FROM tbl_usr_user WHERE usr_uty_index=3");
			while ($row_usr = mysqli_fetch_array($result_usr)) 
			{
				$joa_index = 0;
				$result_joa = mysqli_query ($dbconn, "SELECT joa_index FROM tbl_joa_jobanswer WHERE joa_job_index=".$job_index." and joa_usr_index=".$row_usr["usr_index"]);
				while ($row_joa = mysqli_fetch_array($result_joa)) $joa_index = $row_joa["joa_index"];
				
				if ($joa_index == 0)
					mysqli_query ($dbconn, "INSERT INTO tbl_joa_jobanswer(joa_job_index, joa_usr_index, joa_from, joa_to, joa_status) VALUES (".$job_index.", ".$row_usr["usr_index"].", '0:00', '0:00', '2')");
			}
			
		}
		
		/*$result_adm = mysqli_query ($dbconn, "SELECT lin_phone FROM tbl_usr_user INNER JOIN tbl_lin_login on lin_usr_index=usr_index where lin_admin=1");
		while ($row_adm = mysqli_fetch_array($result_adm)) 
		{
			sendmessage($dbconn, $row_adm["lin_phone"],  "Buchung. Bitte Notbetreuung ausloesen.", -2);
		}*/
	}

	
	function doswitch($dbconn,$swi_index,$usr_index__my,$swi_date,$swi_abs_points__from,$swi_abs_points__to,$swi_usr_index__from)
	{
		//insupd other
		$other_abs_index = 0;
		$result = mysqli_query ($dbconn, "SELECT abs_index FROM tbl_abs_absence WHERE abs_ref_index=".$swi_usr_index__from." and abs_date='".$swi_date."' and abs_uty_index=1 and abs_status=0");
		while ($row = mysqli_fetch_array($result)) $other_abs_index = $row["abs_index"];

		if ($other_abs_index == 0)
		{
			mysqli_query ($dbconn, "INSERT INTO tbl_abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index, abs_status, abs_points) 
					VALUES ('".$swi_date."', ".$swi_usr_index__from.", 1, 3, 0, ".$swi_abs_points__to.")");
		}
		else
		{
			if ($swi_abs_points__to == 0)
				mysqli_query ($dbconn, "DELETE FROM tbl_abs_absence where abs_index=".$other_abs_index);	
			else
				mysqli_query ($dbconn, "UPDATE tbl_abs_absence set abs_points=".$swi_abs_points__to." where abs_index=".$other_abs_index);	
		}

		//insupd mine
		$my_abs_index = 0;
		$result = mysqli_query ($dbconn, "SELECT abs_index FROM tbl_abs_absence 
		WHERE abs_ref_index=".$usr_index__my." and abs_date='".$swi_date."' and abs_uty_index=1 and abs_status=0");
		while ($row = mysqli_fetch_array($result)) $my_abs_index = $row["abs_index"];
		
		if ($my_abs_index == 0)
		{
			mysqli_query ($dbconn, "INSERT INTO tbl_abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index, abs_status, abs_points) 
					VALUES ('".$swi_date."', ".$usr_index__my.", 1, 3, 0, ".$swi_abs_points__from.")");
		}
		else
		{
			if ($swi_abs_points__from == 0)
				mysqli_query ($dbconn, "DELETE FROM tbl_abs_absence where abs_index=".$my_abs_index);	
			else
				mysqli_query ($dbconn, "UPDATE tbl_abs_absence set abs_points=".$swi_abs_points__from." where abs_index=".$my_abs_index);	
		}

		//update swi
		mysqli_query ($dbconn, "UPDATE tbl_swi_switch set swi_status=1 where swi_index=".$swi_index);
			
		//lösche meine switch angeboten an diesem tag
		$result = mysqli_query ($dbconn, "SELECT abs_index, abs_date, abs_ref_index, abs_uty_index, abs_status, abs_abr_index, abs_points FROM tbl_abs_absence 
		WHERE abs_ref_index=".$usr_index__my." and abs_date='".$swi_date."' and abs_uty_index=1 and abs_status=0");
		while ($row = mysqli_fetch_array($result)) 
		{
			mysqli_query ($dbconn, "DELETE FROM tbl_swi_switch where swi_usr_index__from=".$usr_index__my." and swi_date='".$swi_date."'");
		}
			
		//sendmessages
		$text = "";
		if ($swi_abs_points__from == 8) $text = "Dein Kind muss ".germanDateAsText($swi_date)." zu Hause bleiben.";
		if ($swi_abs_points__from == 4) $text = "Dein Kind muss ".germanDateAsText($swi_date)." um 12 Uhr abgeholt werden.";
		if ($swi_abs_points__from == 1) $text = "Dein Kind muss ".germanDateAsText($swi_date)." um 14 Uhr abgeholt werden.";
		if ($swi_abs_points__from == 0) $text = "Dein Kind darf ".germanDateAsText($swi_date)." heute normal kommen.";
		//sendmessage($dbconn, "", "INFO: Du hast dem Switch zugestimmt, d.h. ".$text, $usr_index__my);

		$text = "";
		if ($swi_abs_points__to == 8) $text = "Dein Kind muss ".germanDateAsText($swi_date)." zu Hause bleiben.";
		if ($swi_abs_points__to == 4) $text = "Dein Kind muss ".germanDateAsText($swi_date)." um 12 Uhr abgeholt werden.";
		if ($swi_abs_points__to == 1) $text = "Dein Kind muss ".germanDateAsText($swi_date)." um 14 Uhr abgeholt werden.";
		if ($swi_abs_points__to == 0) $text = "Dein Kind darf ".germanDateAsText($swi_date)." heute normal kommen.";
		//sendmessage($dbconn, "", "INFO: Jemand hat deinen Switch angenommen, d.h. ".$text, $swi_usr_index__from);
			

	}

	function uploadfile($datei, $path)
	{
		if (strlen($datei['name']) > 0)
		{			
			$dateiname = "uploads/".$path."/".$datei['name'].".".pathinfo($datei['name'], PATHINFO_EXTENSION);
			echo $dateiname;
			move_uploaded_file($datei['tmp_name'],$dateiname);
			chmod($dateiname,0666);
			
		}	
	}
	
	

	/*function generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, $abs_points, $howmanykids)
	{
		$result_tab = mysqli_query ($dbconn, "SELECT 
							usr_index, 
							usr_name, 
							ifnull(sum(a.abs_points),0) as abs_points__sum,
							(SELECT at.abs_date FROM tbl_abs_absence at WHERE at.abs_ref_index=usr_index and at.abs_status=0 and at.abs_uty_index=1 and at.abs_abr_index=3 and abs_date<NOW() order by at.abs_date desc limit 1) as abt_date
							from tbl_usr_user left join tbl_abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1
							where usr_uty_index=1 ".$sick_filter." group by usr_index order by abs_points__sum asc, abt_date asc, usr_index asc LIMIT ".$howmanykids);
		while ($row_tab = mysqli_fetch_array($result_tab))
		{
			if (!in_array( $row_tab['usr_index'], $already_abs))
			{
				mysqli_query ($dbconn, "INSERT INTO tbl_abs_absence (abs_date, abs_ref_index, abs_uty_index, abs_abr_index, abs_status, abs_points) VALUES ('".$job_date."', ".$row_tab['usr_index'].", 1, 3, 0, ".$abs_points.")");
				$already_abs[] = $row_tab['usr_index'];
			}
		}
		return $already_abs;
	}
*/	

	/*
	function calculate($dbconn, $job_index, $joa_index)	
	{		
		$debug = false;
		//file_put_contents("log.txt", "\r\n".date("H:i"). " - "."geht", FILE_APPEND);
		//geht diese antwort über die "volle" zeit des jobs ?
		$fulltime_or_fullresponded = false;
		$job_date = "";
		$joa_index__best = 0;
		$joa_from = "";
		$joa_to = "";
		$joa_status = 0;
		$result = mysqli_query ($dbconn, "SELECT joa_from, joa_to, joa_status FROM tbl_joa_jobanswer WHERE joa_index=".$joa_index);
		while ($row = mysqli_fetch_array($result)) 
		{
			$joa_from = $row["joa_from"];
			$joa_to = $row["joa_to"];
			$joa_status = $row["joa_status"];
		}
		if ($joa_status == 0)
		{
			
			$result = mysqli_query ($dbconn, "SELECT DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s, job_date, job_from, job_to FROM tbl_job_joboffer WHERE job_index=".$job_index);
			while ($row = mysqli_fetch_array($result)) 
			{
				$job_date = $row["job_date"];
				$job_from_a = explode(":", $row["job_from"]);
				$joa_from_a = explode(":", $joa_from);
				$job_to_a = explode(":", $row["job_to"]);
				$joa_to_a = explode(":", $joa_to);
				
				if (((intval($job_from_a[0]) == intval($joa_from_a[0])) && (intval($job_from_a[1]) == intval($joa_from_a[1])))
				   && ((intval($job_to_a[0]) == intval($joa_to_a[0])) && (intval($job_to_a[1]) == intval($joa_to_a[1]))))
				{
					$fulltime_or_fullresponded = true;
					$joa_index__best = $joa_index;
					
					if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."fulltime=yes", FILE_APPEND);
		
				}
			}
		}
		//haben alle aushilfen bereits zu oder abgesagt (oder sind abwesend oder inactive)
		if (!$fulltime_or_fullresponded)
		{
			$fulltime_or_fullresponded = true;
			$result_usr = mysqli_query ($dbconn, "SELECT usr_index FROM tbl_usr_user INNER JOIN tbl_lin_login on lin_usr_index=usr_index where usr_uty_index=3 and lin_active=1");
			while ($row_usr = mysqli_fetch_array($result_usr))
			{
				//abwesend ?
				$result_abs = mysqli_query ($dbconn, "SELECT abs_index FROM tbl_abs_absence where abs_ref_index=".$row_usr["usr_index"]." and abs_uty_index=3 and abs_date='".$job_date."' and abs_status=0");
				if (mysqli_num_rows($result_abs) == 0)
				{
					//anderen job bereits zugesagt ?
					$result_job = mysqli_query ($dbconn, "SELECT job_index FROM tbl_job_joboffer INNER JOIN tbl_joa_jobanswer on joa_job_index=job_index and joa_usr_index=".$row_usr["usr_index"]." where job_date='".$job_date."'and job_status=1 and joa_status=1");
					if (mysqli_num_rows($result_abs) == 0)
					{
						$result_joa = mysqli_query ($dbconn, "SELECT joa_index FROM tbl_joa_jobanswer where joa_usr_index=".$row_usr["usr_index"]." and joa_job_index='".$job_index."' and joa_status<3");
						if (mysqli_num_rows($result_joa) == 0)
						{
							$fulltime_or_fullresponded = false;
							if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."fulltime_or_fullresponded=false", FILE_APPEND);
							if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."fullresponded=yes", FILE_APPEND);
						}
					}
				}
			}
		}
	//	if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."fulltime_or_fullresponded=".strval($fulltime_or_fullresponded), FILE_APPEND);
		
		//wenn ja, berechne diesen job
		if ($fulltime_or_fullresponded)
		{ 
			//hole daten
			$job_date = "";
			$job_date_s = "";
			$usr_index = 0;
			$result_job = mysqli_query ($dbconn, "SELECT DATE_FORMAT(job_date, '%d.%m.%Y') as job_date_s, job_date, joa_usr_index FROM tbl_job_joboffer INNER JOIN tbl_joa_jobanswer on job_index=joa_job_index and joa_index=".$joa_index." WHERE job_index=".$job_index);
			while ($row_job = mysqli_fetch_array($result_job)) 
			{
				$job_date = $row_job["job_date"]; 
				$job_date_s = $row_job["job_date_s"]; 
				//$usr_index = $row_job["joa_usr_index"];
			}
			
	
			//wähle beste joa
			if ($joa_index__best == 0) $joa_index__best = getbestjoa($dbconn, $job_index);
			
			if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."joa_index__best=".$joa_index__best, FILE_APPEND);
			
			// absage
			if ($joa_index__best == 0)
			{
				mysqli_query ($dbconn, "UPDATE tbl_job_joboffer set job_status=2, job_joa_index=0 where job_index=".$job_index);
				mysqli_query ($dbconn, "UPDATE tbl_joa_jobanswer set joa_status=2 where joa_status<2 and joa_job_index=".$job_index);		
			}
			else
			{
				//bestätige joa
				mysqli_query ($dbconn, "UPDATE tbl_joa_jobanswer set joa_status=1 where joa_index=".$joa_index__best);
				//bestätige job
				mysqli_query ($dbconn, "UPDATE tbl_job_joboffer set job_status=1, job_joa_index=".$joa_index__best." where job_index=".$job_index);
				//sage alle anderen joa ab
				mysqli_query ($dbconn, "UPDATE tbl_joa_jobanswer set joa_status=2 where joa_index<>".$joa_index__best." and joa_status<2 and joa_job_index=".$job_index);
				
				//zusage an aushilfe
				$result_job = mysqli_query ($dbconn, "SELECT joa_usr_index FROM tbl_job_joboffer INNER JOIN tbl_joa_jobanswer on job_index=joa_job_index and joa_index=".$joa_index__best." WHERE job_index=".$job_index);
				while ($row_job = mysqli_fetch_array($result_job)) 
				{
					$usr_index = $row_job["joa_usr_index"];
				}
				
				sendmessage($dbconn, "Zusage fuer den ".$job_date_s." in der Kita", "Zusage fuer den ".$job_date_s." in der Kita.", $usr_index);
				
			}	
				//gibt es noch andere jobs an diesem Tag ?
				$more_jobs = true;
				$result_mj = mysqli_query ($dbconn, "SELECT job_index FROM tbl_job_joboffer WHERE job_date='".$job_date."' and job_status=0");
				if(mysqli_num_rows($result_mj) == 0) $more_jobs = false;
				
				if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."more_jobs=".intval($more_jobs)." ", FILE_APPEND);
				if (!$more_jobs)
				{
					//schreibe absagen an aushilfen ohne joa_satus=1 an diesem tag
					//hole alle user
					$result_usr = mysqli_query ($dbconn, "SELECT usr_index FROM tbl_usr_user INNER JOIN tbl_lin_login on lin_usr_index=usr_index where usr_uty_index=3 and lin_active=1");
					while ($row_usr = mysqli_fetch_array($result_usr)) 
					{
						//alle die an diesem tag eine antwort abgegeben haben
						$usr_has_answered = false;
						$usr_has_job = false;
						$result_joa = mysqli_query ($dbconn, "SELECT joa_index, job_joa_index FROM tbl_joa_jobanswer INNER JOIN tbl_job_joboffer on job_index=joa_job_index WHERE joa_usr_index=".$row_usr["usr_index"]." and job_date='".$job_date."' and joa_status<2");
						while ($row_joa= mysqli_fetch_array($result_joa)) 
						{
							$usr_has_answered = true;
							if ($row_joa["job_joa_index"] == $row_joa["joa_index"]) $usr_has_job = true;
						}
						
						if ($usr_has_answered && !$usr_has_job)
						{
							sendmessage($dbconn, "Absage fuer den ".$job_date_s." in der Kita", "ABSAGE fuer den ".$job_date_s." in der Kita.", $row_usr["usr_index"]);
						}
					}
					//info an admin dass alle aushilfen genatwortet haben oder es eine
					
					
					//was will ich wissen ?
						
					
					/*
					//generiere Absences
					//kranke kinder
					$sick_filter = "";
					$sick_counter = 0;
					$already_abs = array();
					$result_sick = mysqli_query ($dbconn, "SELECT abs_ref_index FROM tbl_abs_absence where abs_date='".$job_date."' and abs_uty_index=1 and abs_abr_index<3 and abs_status=0");
					while ($row_sick = mysqli_fetch_array($result_sick))
					{
						if (strlen($sick_filter)>0) $sick_filter = $sick_filter.",";
						$sick_filter = $sick_filter.$row_sick["abs_ref_index"];
						$sick_counter ++;
					}
					if (strlen($sick_filter)>0) $sick_filter = " and usr_index not in (".$sick_filter.") ";
					
					//Sonderfall Teilzeit-Erzieher
					$educators_not_coming_on_parttime_day = isParttimeDay($dbconn, $job_date);
					if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."educators_not_coming_on_parttime_day=".$educators_not_coming_on_parttime_day, FILE_APPEND);
					
					if ($educators_not_coming_on_parttime_day > 0)
					{
						if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."parttime_day=yes", FILE_APPEND);
						
						
						$ht_needed = false;
						$gt_needed = false;
						$result_job = mysqli_query ($dbconn, "SELECT job_index, job_to, job_joa_index FROM tbl_job_joboffer WHERE job_date='".$job_date."' and job_status<3");
						while ($row_job = mysqli_fetch_array($result_job)) 
						{
							if ($row_job["job_to"] == "13:00")
							{
								$ht_needed = true;
							}
							elseif  ($row_job["job_to"] == "15:00")
							{
								$gt_needed = true;
							}
						}
						
						$tempstaff_12 = 0;
						$tempstaff_14 = 0;
						$tempstaff_15 = 0;
						$ersatz_fuer_ht = !$ht_needed;
						$ersatz_fuer_gt = !$gt_needed;
						$fehlende_stunden_gt = 0;
						$result_job = mysqli_query ($dbconn, "SELECT job_index, job_to, job_joa_index FROM tbl_job_joboffer WHERE job_date='".$job_date."' and job_status=1");
						while ($row_job = mysqli_fetch_array($result_job)) 
						{
							$result_joa = mysqli_query ($dbconn, "SELECT joa_to FROM tbl_joa_jobanswer WHERE joa_index=".$row_job["job_joa_index"]." and joa_status=1");
							while ($row_joa = mysqli_fetch_array($result_joa)) 
							{
								if ($row_job["job_to"] == "13:00")
								{
									$ersatz_fuer_ht = true;
								}
								elseif  ($row_job["job_to"] == "15:00")
								{
									$ersatz_fuer_gt = true;
									if ($row_joa["joa_to"] == "15:00") $fehlende_stunden_gt = 0;
									if ($row_joa["joa_to"] == "14:00") $fehlende_stunden_gt = 1;
									if ($row_joa["joa_to"] == "13:00") $fehlende_stunden_gt = 3;
									if ($row_joa["joa_to"] == "12:00") $fehlende_stunden_gt = 3;
								}
							}
						}
						
						
					if ($debug) file_put_contents("log.txt", "\r\nersatz fuer ht=".$ersatz_fuer_ht." ersatz fuer gt=".$ersatz_fuer_gt." fhlstd=".$fehlende_stunden_gt, FILE_APPEND);
						
						if ((!$ersatz_fuer_ht) && (!$ersatz_fuer_gt))
						{
							//14 kommen gar nicht
							$how_many_kids = 14-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = 14; $sick_counter=$sick_counter-14;} else { $sick_counter=0;}
							$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 7, $how_many_kids);
						}
						elseif (($ersatz_fuer_ht) && (!$ersatz_fuer_gt))
						{
							//5 kommen gar nicht
							$how_many_kids = 5-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 5; $sick_counter=$sick_counter-5;} else { $sick_counter=0;}
							$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 7, $how_many_kids);
							//9 gehen um 12
							$how_many_kids = 9-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 9; $sick_counter=$sick_counter-9;} else { $sick_counter=0;}
							$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 3, $how_many_kids);
						}	
						elseif ((!$ersatz_fuer_ht) && ($ersatz_fuer_gt))
						{
							//5 kommen gar nicht
							$how_many_kids = 5-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 5; $sick_counter=$sick_counter-5;} else { $sick_counter=0;}
							$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 7, $how_many_kids);
							if ($fehlende_stunden_gt == 1)
							{
								// 9 gehen um 14 Uhr
								$how_many_kids = 9-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 9; $sick_counter=$sick_counter-9;} else { $sick_counter=0;}
								$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 1, $how_many_kids);
							}
							if ($fehlende_stunden_gt == 3)
							{
								// 9 gehen um 12 Uhr
								$how_many_kids = 9-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = 9; $sick_counter=$sick_counter-9;} else { $sick_counter=0;}
								$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 3, $how_many_kids);
							}
						}							
						elseif (($ersatz_fuer_ht) && ($ersatz_fuer_gt))
						{
							if ($fehlende_stunden_gt == 1)
							{
								// 14 gehen um 14 Uhr
								$how_many_kids = 14-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = 14; $sick_counter=$sick_counter-14;} else { $sick_counter=0;}
								$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 1, $how_many_kids);
							}
							if ($fehlende_stunden_gt == 3)
							{
								// 14 gehen um 12 Uhr
								$how_many_kids = 14-$sick_counter;  if ($how_many_kids < 0) {$how_many_kids = 14; $sick_counter=$sick_counter-14;} else { $sick_counter=0;}
								$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 3, $how_many_kids);
							}
						} 
					}
					
					
					else
					{						
						//jobs ohne ersatz 
						$result_job = mysqli_query ($dbconn, "SELECT job_index, job_from, job_to FROM tbl_job_joboffer WHERE job_date='".$job_date."' and job_status=2");
						while ($row_job= mysqli_fetch_array($result_job)) 
						{
							if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."job_ohneErsatz=".$row_job["job_index"].", sick-ocunter=".$sick_counter, FILE_APPEND);
							
							//5 kinder bleiben zuhause
							$how_many_kids = 5-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 5; $sick_counter=$sick_counter-5;} else { $sick_counter=0;}
							$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 7, $how_many_kids);
							
							if ($debug) file_put_contents("log.txt", "\r\nsick-counter=".$sick_counter.", how many=".$how_many_kids, FILE_APPEND);
						}
						//jobs mit ersatz
						$result_job = mysqli_query ($dbconn, "SELECT job_index, job_from, job_to, joa_to FROM tbl_job_joboffer INNER JOIN tbl_joa_jobanswer on job_joa_index=joa_index WHERE job_date='".$job_date."' and job_status=1");
						while ($row_job= mysqli_fetch_array($result_job)) 
						{
							
							if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."job_mitErsatz=".$row_job["job_index"]." ", FILE_APPEND);
							
							if ($row_job["job_to"] == "13:00") // erzieher der mittag geht ist krank
							{
								//keine notbetreuung weil immer voll ersetzt
							}
							elseif ($row_job["job_to"] == "15:00")  
							{ 
								if ($row_job["joa_to"] == "14:00")
								{
									$how_many_kids = 5-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 5; $sick_counter=$sick_counter-5;} else { $sick_counter=0;}
									$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 1, $how_many_kids);
								}
								elseif (($row_job["joa_to"] == "13:00") || ($row_job["joa_to"] == "12:00"))
								{
									$how_many_kids = 5-$sick_counter; if ($how_many_kids < 0) {$how_many_kids = 5; $sick_counter=$sick_counter-5;} else { $sick_counter=0;}
									$already_abs[] = generateAbsences($dbconn, $job_date, $sick_filter, $already_abs, 3, $how_many_kids);
								}
							}
						}
					}
					//schreibe absence nachricht in die gruppe
					if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."schreibefinal", FILE_APPEND);
					$text = "";
					$kids_gn = array();
					$kids_12 = array();
					$kids_14 = array();
					$result_tab = mysqli_query ($dbconn, "SELECT usr_name, a.abs_points 
															from tbl_usr_user inner join tbl_abs_absence a on a.abs_ref_index=usr_index and a.abs_status=0 and a.abs_uty_index=1 and abs_abr_index=3 
															where abs_date='".$job_date."' order by usr_index asc");
					while ($row_tab = mysqli_fetch_array($result_tab))
					{
						if ($row_tab["abs_points"] == 7) $kids_gn[] = $row_tab["usr_name"];
						if ($row_tab["abs_points"] == 3) $kids_12[] = $row_tab["usr_name"];
						if ($row_tab["abs_points"] == 1) $kids_14[] = $row_tab["usr_name"];
					}
					if ($debug) file_put_contents("log.txt", "\r\n".date("H:i"). " - "."Bringen gn 12 14=".sizeof($kids_gn)." ".sizeof($kids_12)." ".sizeof($kids_14), FILE_APPEND);
					
					if ((sizeof ($kids_gn) == 0) && (sizeof ($kids_12) == 0) && (sizeof ($kids_14) == 0))
						$text = "INFO: Keine Notbetreuung am ".$job_date_s.". Es konnten ausreichend Aushilfen gefunden werden.";
					else
						$text = "INFO: Notbetreuung am ".$job_date_s."\r\n";
					
					if (sizeof ($kids_gn) > 0)
					{
						if (sizeof ($kids_gn) >= (14-$sick_counter))
						{
							//geschlossen. 
							$text = $text."INFO:\r\nDie Kita bleibt am ".$job_date_s." geschlossen!";
						}
						else
						{
							$text = $text."Folgende Kinder müssen am ".$job_date_s." zu Hause bleiben: \r\n";
							for ($i=0; $i<sizeof($kids_gn); $i++) $text = $text.$kids_gn[$i]."\r\n";
						}
					}
					if (sizeof ($kids_12) > 0)
					{
						$text = $text."Folgende Kinder müssen am ".$job_date_s." um 12 Uhr abgeholt werden: \r\n";
						for ($i=0; $i<sizeof($kids_12); $i++) $text = $text.$kids_12[$i]."\r\n";
					}
					if (sizeof ($kids_14) > 0)
					{
						$text = $text."Folgende Kinder müssen heute um 14 Uhr abgeholt werden: \r\n";
						for ($i=0; $i<sizeof($kids_14); $i++)  $text = $text.$kids_14[$i]."\r\n";
					}
					sendmessage($dbconn, "", $text, 0);
					sendmessage($dbconn, "", $text, -1);
				}
				
			}
		
	}	
	*/
	
?>