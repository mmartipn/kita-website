<?php
	header("Access-Control-Allow-Origin: *");
	header('Content-type: application/json');
	include 'functions.php';
	include 'defines.php';
	$dbconn = sqlconnect();
	$prefix = "tbl_";
	
	$function = ""; if (isset ($_REQUEST["function"]))  $function = $_REQUEST["function"];
	

	if ($function == "login")
	{
	//get data from json
		$arr = json_decode(file_get_contents("php://input"), true);
		$username = "";
		$password = "";
		$session = "";
			
		foreach ($arr as $lkey => $lvalue) 
		{
			if($lkey == "session") $session = $lvalue;
			if($lkey == "data") 
			{
			   foreach ($lvalue as $key => $value) 
				{
					if($key == "email") $username = $value;
					if($key == "password") $password = $value;
				}
			}
		 }
		

	//login
		$retval = 0;
		$msg_id = 0;
		$uid = 0;
		$tid = 0;
		$lid = 0;
		$isa = 0;
		
		//file_put_contents("log.txt", "\r\nSELECT lin_index, lin_locked, usr_index, usr_uty_index FROM ".$prefix."lin_login INNER JOIN ".$prefix."usr_user on lin_usr_index=usr_index WHERE lin_email='".$username."' and lin_password = '".$password."'");

		$result = mysqli_query ($dbconn, "SELECT lin_index, lin_locked, lin_admin, usr_index, usr_uty_index FROM ".$prefix."lin_login INNER JOIN ".$prefix."usr_user on lin_usr_index=usr_index WHERE lin_email='".$username."' and lin_password = '".$password."'");
		if (mysqli_num_rows ($result) > 0)
		{
			$data = mysqli_fetch_array ($result);
			if ($data["lin_locked"] == 1)
			{
				$retval = 2;
				$msg_id = 2;
			}
			else
			{
				$uid = $data["usr_index"];
				$tid = $data["usr_uty_index"];
				$lid = $data["lin_index"];
				$isa = $data["lin_admin"];
				
				mysqli_query ($dbconn, 'update '.$prefix.'lin_login set lin_loginfail=0 where lin_index='.$data["lin_index"]); 
				
				$result_ses = mysqli_query ($dbconn, "SELECT ses_index FROM ".$prefix."ses_session WHERE ses_lin_index='".$data["lin_index"]."' and ses_key = '".$session."'");
				if (mysqli_num_rows ($result_ses) == 0)
				{
					mysqli_query ($dbconn, 'delete from '.$prefix.'ses_session where ses_created <= (NOW() - INTERVAL 2 MONTH)'); 
					mysqli_query ($dbconn, 'insert into '.$prefix.'ses_session(ses_lin_index, ses_key) values('.$data["lin_index"].', "'.$session.'")'); 
				}
				
				$retval = 1;
				$msg_id = 0;
			}
		}
		else
		{
			$result = mysqli_query ($dbconn, "SELECT lin_index, lin_loginfail FROM ".$prefix."lin_login WHERE lin_email='".$username."'"); 
			if (mysqli_num_rows ($result) > 0)
			{
				
				$data = mysqli_fetch_array ($result);
				$lf = $data['lin_loginfail'];
				$lf ++;
				mysqli_query ($dbconn, 'update '.$prefix.'lin_login set lin_loginfail='.$lf.' where lin_index='.$data["lin_index"]); 
				if ($lf > 10)
				{
					mysqli_query ($dbconn, 'update '.$prefix.'lin_login set lin_locked=1 where lin_index='.$data["lin_index"]); 	
				}
			}
			$retval = 2;
			$msg_id = 1;
		}	
		$sdata = array("uid"=>$uid, "tid"=>$tid, "lid"=>$lid, "isa"=>$isa);
			
		$response = array();
		$response["retval"] = $retval;
		$response["msg_id"] = $msg_id;
		$response["sdata"] = $sdata;

		echo json_encode($response);	
	}

	elseif($function == "forgotPass")
	{
		
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$username = "";
			
		/*	foreach ($arr as $lkey => $lvalue) 
			{
				if($lkey == "data") 
				{
				   foreach ($lvalue as $key => $value) 
					{
						if($key == "email") $username = $value;
					}
				}
			 }*/
			 foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "email") $username = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			$result = mysqli_query ($dbconn, "SELECT lin_index FROM ".$prefix."lin_login WHERE lin_email='".$username."'");
			if (mysqli_num_rows ($result) > 0)
			{
				$data = mysqli_fetch_array ($result);
				$hash = md5(date("YmdHis").$username);
				mysqli_query ($dbconn, 'insert into '.$prefix.'pwr_passwordreset(pwr_lin_index, pwr_hash) values('.$data["lin_index"].', "'.$hash.'")');
				
				//sendemail
				$header = "Passwort Reset auf ".$_def_general_url;
				$body = "Hallo.<br><br><br>Bitte klicke auf nachfolgenden Link um ein neues Passwort zu setzen:<br><br><a href='".$_def_general_client."formular.php?m_id=3&hash=".$hash."'>".$_def_general_client."formular.php?m_id=3&hash=".$hash."</a>";
				sendmail($username, $header, $body);
				$retval = 1;
				$msg_id = 8;
			}
			else
			{
				$retval = 2;
				$msg_id = 10;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>array()));
		
	}
	
	elseif($function == "setNewPass")
	{
		
	//	file_put_contents("log.txt", "test5");
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$hash = "";
			$password = "";
			
			 foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "hash") $hash = $value;
						if($key == "password") $password = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			$result = mysqli_query ($dbconn, "SELECT pwr_lin_index FROM ".$prefix."pwr_passwordreset WHERE pwr_hash='".$hash."'");
			if (mysqli_num_rows ($result) > 0)
			{
				$data = mysqli_fetch_array ($result);
				mysqli_query ($dbconn, 'update '.$prefix.'lin_login set lin_password="'.$password.'" where lin_index='.$data["pwr_lin_index"]);
				mysqli_query ($dbconn, 'delete from '.$prefix.'pwr_passwordreset where pwr_hash="'.$hash.'"');
								
				$retval = 1;
				$msg_id = 9;
			}
			else
			{
				$retval = 2;
				$msg_id = 10;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>array()));
		
	}
?>