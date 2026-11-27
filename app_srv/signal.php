<?php
	header("Access-Control-Allow-Origin: *");
	header('Content-type: application/json');
	include 'functions.php';
	include_once __DIR__ . '/../config.php';
	$dbconn = sqlconnect();
	$session = false;
	
	ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
	
	$function = ""; if (isset ($_REQUEST["function"]))  $function = $_REQUEST["function"];

	if ($function == "getSignalQueue")
	{
		//	mysqli_query ($dbconn, "INSERT INTO tbl_log_log (log_text) values('connected')");
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") 
				{
					if (strlen($dataValue) > 0 && $dataValue == env('SIGNAL_API_KEY', '')) $session = true;
				}
			}
			/*
			if ($session)
				mysqli_query ($dbconn, "INSERT INTO tbl_log_log (log_text) values('session ok')");
			else
				mysqli_query ($dbconn, "INSERT INTO tbl_log_log (log_text) values('session not ok')");
			*/
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				$result = mysqli_query ($dbconn, "SELECT mes_index, mes_usr_signal, mes_text, mes_usr_index FROM tbl_mes_message where mes_status=0 order by mes_index asc limit 1");
				while ($row = mysqli_fetch_array($result))
				{
					$data[]= array("mes_index"=>$row["mes_index"], "mes_text"=>$row["mes_text"], "mes_usr_index"=>$row["mes_usr_index"], "mes_usr_signal"=>$row["mes_usr_signal"]);
					$retval = 1;
				}
			}
			else
			{
				$retval = 2;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	elseif ($function == "updateSignalQueue")
	{
		//get data from json
			$arr = json_decode(file_get_contents("php://input"), true);
			$mes_index = 0;
			
			foreach ($arr as $dataKey => $dataValue) 
			{
				if($dataKey == "session") 
				{
					if (strlen($dataValue) > 0 && $dataValue == env('SIGNAL_API_KEY', '')) $session = true;
				}
				if($dataKey == "data") 
				{
					foreach ($dataValue as $key => $value) 
					{
						if($key == "mes_index") $mes_index = $value;
					}
				}
			}
		//logic
			$retval = 0;
			$msg_id = 0;
			$data = array();
			if ($session)
			{
				mysqli_query ($dbconn, "UPDATE tbl_mes_message set mes_status=1 where mes_index=".$mes_index);
				$retval = 1;
				
				//mysqli_query ($dbconn, "INSERT INTO tbl_log_log (log_text) values('save ok')");
			}
			else
			{
				$retval = 2;
			}
			
		//return
			echo json_encode(array("retval"=>$retval, "msg_id"=>$msg_id, "data"=>$data));
	}
	
?>