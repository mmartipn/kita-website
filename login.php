<?php
	session_start ();
	include 'functions.php';
	include 'defines.php';
	
	$action =""; if (isset($_POST["action"])) $action = $_POST["action"];
	$tokenh =""; if (isset($_POST["tokenh"])) $tokenh = $_POST["token"];

	if ($action == "lgn")
	{
		$response = callServer ($_def_general_server."login.php?function=login", array( "data"=>array("email"=>trim(strtolower($_POST["name"])), "password"=>md5(trim($_POST["password"])))));			

		$retval = 0;
		$msg_id = 0;
		$usr_id = 0;
		$uty_id = 0;
		$lid_id = 0;
		$usr_ia = 0;
		
		foreach ($response as $key => $value) 
		{
			if($key == "retval") $retval = $value;
			if($key == "msg_id") $msg_id = $value;
			if($key == "sdata") 
			{
				foreach ($value as $skey => $svalue) 
				{
					if($skey == "uid") $usr_id = $svalue;
					if($skey == "tid") $uty_id = $svalue;
					if($skey == "lid") $lid_id = $svalue;
					if($skey == "isa") $usr_ia = $svalue;
				}
			}
		}
		
		if ($retval == 1)
		{
			$_SESSION["loggy_usr_id"] = $usr_id;
			$_SESSION["loggy_uty_id"] = $uty_id;
			$_SESSION["loggy_lid_id"] = $lid_id;
			$_SESSION["loggy_usr_ia"] = $usr_ia;

			header ("Location: token.php");
			exit();
		}
		elseif ($retval == 2)
		{
			header ("Location: formular.php?msg_id=".$msg_id);
			exit();
		}
		else
		{
			header ("Location: formular.php");
			exit();
		}
	}
	elseif ($action == "fgt")
	{
		$response = callServer ($_def_general_server."login.php?function=forgotPass", array( "data"=>array("email"=>trim(strtolower($_POST["name"])))));	
		$retval = 0;
		$msg_id = 0;
				
		if (isset ($response['retval']))$retval = $response['retval'];
		if (isset ($response['msg_id']))$msg_id = $response['msg_id'];
		
		
		header ("Location: formular.php?m_id=2&msg_id=".$msg_id);
		
	}
	elseif ($action == "stn")
	{
		$response = callServer ($_def_general_server."login.php?function=setNewPass", array( "data"=>array("hash"=>$_POST["hash"], "password"=>md5(trim($_POST["password"])))));
		/*foreach ($response as $key => $value) 
		{
			if($key == "retval") $retval = $value;
			if($key == "msg_id") $msg_id = $value;
		}
		*/
		if (isset ($response['retval']))$retval = $response['retval'];
		if (isset ($response['msg_id']))$msg_id = $response['msg_id'];
		header ("Location: formular.php?m_id=3&msg_id=".$msg_id);
	}
	else
	{
		header ("Location: formular.php");
		exit();
	}
?>