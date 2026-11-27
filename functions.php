<?php
	
	function konform($s)
	{
		$new_s = trim($s);
		$new_s = str_replace('"', '', $new_s);
		$new_s = str_replace('\"', '', $new_s);
		$new_s = str_replace('\'', ' ', $new_s);
		$new_s = str_replace('&nbsp;', ' ', $new_s);
		$new_s = str_replace('<p>', ' ', $new_s);
		$new_s = str_replace('</p>', ' ', $new_s);
	//	$new_s = str_replace('<', '', $new_s);
	//	$new_s = str_replace('>', '', $new_s);
		$new_s = trim($new_s);

		return $new_s;
	}
	
	function issel($v, $z)
	{
		$s = "";
		if ($v == $z) $s = 'selected="selected"';
		return $s;	
	}
	
		
	function isseltime($t1, $t2)
	{
		$s = "";
		$t1_a=explode(":",$t1);
		$t2_a=explode(":",$t2);
		
		if ((intval($t1_a[0]) == intval($t2_a[0])) && (intval($t1_a[1]) == intval($t2_a[1]))) 
			$s = 'selected="selected"';
		
		return $s;	
	}
	
	function ischk($v, $z)
	{
		$s = "";
		if ($v == $z) $s = ' checked ';
		return $s;	
	}
	
	function logError($text)
	{
		file_put_contents("logError.txt", "\r\n".date()." - ".$text);	
	}
	function callServer($url, $arrayData)
	{
		$arrayData["session"]=session_id();
		//$arrayData["session"]="1234";
		$headers = array('Content-Type: application/json');
		$jsonData = json_encode($arrayData);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		$json_response = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($json_response, true);
		if (isset ($response['retval']))
		{
			if ($response['retval'] == 3)
			{
				if (isset ($_SESSION['loggy_lid_id'])) unset($_SESSION['loggy_lid_id']); 
				if (isset ($_SESSION['loggy_usr_id'])) unset($_SESSION['loggy_usr_id']); 
				if (isset ($_SESSION['loggy_uty_id'])) unset($_SESSION['loggy_uty_id']);
				header ("Location: formular.php");
				exit();
			}
		}
		
		return $response;
	}
	
	function uploadfile($datei, $path)
	{
		if (strlen($datei['name']) > 0)
		{			
			$newfilename = date("Ymdhis")."_".preg_replace('/[^A-Za-z0-9\_]/', '', pathinfo($datei['name'], PATHINFO_FILENAME)).".".pathinfo($datei['name'], PATHINFO_EXTENSION);	
			$dateiname = "uploads/".$path."/".$newfilename;
			
			move_uploaded_file($datei['tmp_name'],$dateiname);
			chmod($dateiname,0666);
			
		}	
		return $newfilename;
	}
	
	function date_range($first, $last, $uty_index, $step = '+1 day', $output_format = 'Y-m-d', $if=false) 
	{
		$dates = array();
		$today = strtotime("today");
		$current = strtotime($first);
		$last = strtotime($last);

		while( $current <= $last ) 
		{
			if (($current>=$today) || $if) $dates[] = date($output_format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}
		
	function translateAnswer($value)
	{
		$text = "-";
		if ($value == 0) $text = "evtl";
		if ($value == 1) $text = "ja";
		if ($value == 2) $text = "nein";
		return $text;
	}
	
	
	function sendmail($email, $header, $body)
	{
		include_once('phpmailer/PHPMailerAutoload.php');
			
		$mail = new PHPMailer(); 
		date_default_timezone_set('Etc/UTC');
		$mail->isSMTP();
		//$mail->SMTPDebug = 2;
		//$mail->Debugoutput = 'html';
		include_once __DIR__ . '/config.php';
		$mail->Host = env('SMTP_HOST', '');
		$mail->Port = intval(env('SMTP_PORT', '587'));
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->IsHTML(true);
		$mail->Username = env('SMTP_USER', '');
		$mail->Password = env('SMTP_PASSWORD', '');
		$mail->SetFrom(env('SMTP_FROM', 'noreply@example.com'), env('SMTP_FROM', 'noreply@example.com'));
		$mail->addReplyTo(env('SMTP_FROM', 'noreply@example.com'), env('SMTP_FROM', 'noreply@example.com'));
		$mail->addAddress($email, '');
		$mail->Subject = $header;
		$mail->Body = $body;
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
	
	
	function abspoints2Text($abs_points)
	{
		$text = "";
		if ($abs_points == 0) $text = "normal bringen";
		if ($abs_points == 3) $text = "um 12 abholen";
		if ($abs_points == 7) $text = "nicht bringen";
		return $text;
		
	}
	
	function getWoTa($x)
	{
		$s = "tag";
		if ($x == 1) $s = 'Montag';
		if ($x == 2) $s = 'Dienstag';
		if ($x == 3) $s = 'Mittwoch';
		if ($x == 4) $s = 'Donnertsag';
		if ($x == 5) $s = 'Freitag';
		if ($x == 7) $s = 'Samstag';
		if ($x == 0) $s = 'Sonntag';
		return $s;	
	}
?>