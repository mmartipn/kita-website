<?php
	include 'checkuser.php';
	include 'functions.php';
	include 'defines.php';
	
	$response = array();
	$m_id = 0; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	$openup = 0; if (isset ($_POST['openup'])) $openup = $_POST['openup'];
	$allev = 0; if (isset ($_POST['allev'])) $allev = $_POST['allev'];
	
	$lid_id = 0; if (isset ($_SESSION['loggy_lid_id'])) $lid_id = $_SESSION['loggy_lid_id']; 
	$usr_id = 0; if (isset ($_SESSION['loggy_usr_id'])) $usr_id = $_SESSION['loggy_usr_id']; 
	$uty_id = 0; if (isset ($_SESSION['loggy_uty_id'])) $uty_id = $_SESSION['loggy_uty_id'];
	
	if ($usr_id == 0) 
	{
		header("Location: formular.php");
		exit();
	}

	if($m_id == 1)
	{
		$response = callServer ($_def_general_server."kid.php?function=updateEventlistItem", array("data"=>array("action"=>$_POST['action'], "post"=>$_POST)));	
		 
	}
	
	elseif ($m_id == 2) // Abwesenheit
	{
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		if ($action == "ins")
		{
			if (($uty_id == 1) || ($uty_id == 4))
			{
				$usr_index = 0; if (isset ($_POST['usr_index'])) $usr_index = $_POST['usr_index']; 
				$datefromto = "";  if (isset ($_POST['datefromto'])) $datefromto = $_POST['datefromto'];
				$abr_index = 0; if (isset ($_POST['abr_index'])) $abr_index = $_POST['abr_index']; 
				
				$datefromto_a = explode("-", $datefromto);
				$datefr_str = "";
				$dateto_str = "";
				if (sizeof($datefromto_a) == 2)
				{
					$datefr_str = trim($datefromto_a[0]);
					$dateto_str = trim($datefromto_a[1]);
					
					$dates = date_range($datefr_str, $dateto_str, $uty_id); 
										
					$response = callServer ($_def_general_server."kid.php?function=insertAbsence", array("data"=>array("usr_index"=>$usr_index, "dates"=>$dates, "abr_index"=>$abr_index)));
					if (isset ($response['data']))
					{
						foreach ($response['data'] as $value) 
						{
						}	
					}
				}
			}
			elseif ($uty_id == 2)
			{
				$usr_index = 0; if (isset ($_POST['usr_index'])) $usr_index = $_POST['usr_index']; 
				$datefromto = "";  if (isset ($_POST['datefromto'])) $datefromto = $_POST['datefromto'];
				$abr_index = 0; if (isset ($_POST['abr_index'])) $abr_index = $_POST['abr_index']; 
				$usr_index__ver = 0; if (isset ($_POST['usr_index__ver'])) $usr_index__ver = $_POST['usr_index__ver']; 
				
				
				$datefromto_a = explode("-", $datefromto);
				$datefr_str = "";
				$dateto_str = "";
				if (sizeof($datefromto_a) == 2)
				{
					$datefr_str = trim($datefromto_a[0]);
					$dateto_str = trim($datefromto_a[1]);
					
					$dates = date_range($datefr_str, $dateto_str, $uty_id); 
										
					$response = callServer ($_def_general_server."educator.php?function=insertAbsence", array("data"=>array("usr_index"=>$usr_index, "dates"=>$dates, "abr_index"=>$abr_index, "usr_index__ver"=>$usr_index__ver)));
					if (isset ($response['data']))
					{
						foreach ($response['data'] as $value) 
						{
							
						}	
					}
				}
			}
			elseif ($uty_id == 3)
			{
				$usr_index = 0; if (isset ($_POST['usr_index'])) $usr_index = $_POST['usr_index']; 
				$datefromto = "";  if (isset ($_POST['datefromto'])) $datefromto = $_POST['datefromto'];
				$abr_index = 0; if (isset ($_POST['abr_index'])) $abr_index = $_POST['abr_index']; 
				
				$datefromto_a = explode("-", $datefromto);
				$datefr_str = "";
				$dateto_str = "";
				if (sizeof($datefromto_a) == 2)
				{
					$datefr_str = trim($datefromto_a[0]);
					$dateto_str = trim($datefromto_a[1]);
					
					$dates = date_range($datefr_str, $dateto_str, $uty_id); 
										
					$response = callServer ($_def_general_server."tempstaff.php?function=insertAbsence", array("data"=>array("usr_index"=>$usr_index, "dates"=>$dates, "abr_index"=>$abr_index)));
					if (isset ($response['data']))
					{
						foreach ($response['data'] as $value) 
						{
							
						}	
					}
				}
			}
		}
		elseif ($action == "del")
		{
			if (($uty_id == 1) ||  ($uty_id == 4))
			{
				$abs_index = 0; if (isset ($_POST['abs_index'])) $abs_index = $_POST['abs_index']; 
				
				$response = callServer ($_def_general_server."kid.php?function=deleteAbsence", array("data"=>array("abs_index"=>$abs_index, "uty_index"=>$uty_id )));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
			elseif ($uty_id == 2) 
			{
				$abs_index = 0; if (isset ($_POST['abs_index'])) $abs_index = $_POST['abs_index']; 
				
				$response = callServer ($_def_general_server."educator.php?function=deleteAbsence", array("data"=>array("abs_index"=>$abs_index, "uty_index"=>$uty_id )));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
			elseif ($uty_id == 3) 
			{
				$abs_index = 0; if (isset ($_POST['abs_index'])) $abs_index = $_POST['abs_index']; 
				
				$response = callServer ($_def_general_server."tempstaff.php?function=deleteAbsence", array("data"=>array("abs_index"=>$abs_index, "uty_index"=>$uty_id )));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
		}
		
		
	}	
	elseif ($m_id == 3) // Tausch
	{
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		if ($action == "ins")
		{
			if ($uty_id == 1)
			{
				$response = callServer ($_def_general_server."kid.php?function=createSwitch", array("data"=>array("post"=>$_POST)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
		}
		elseif ($action == "upd")
		{
			if ($uty_id == 1)
			{
				
				$response = callServer ($_def_general_server."kid.php?function=updateSwitch", array("data"=>array("post"=>$_POST)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
			
			if ($uty_id == 3)
			{ 
				$job_index = 0; if (isset ($_POST['job_index'])) $job_index = $_POST['job_index']; 
				$usr_index = 0; if (isset ($_POST['usr_index'])) $usr_index = $_POST['usr_index']; 
				$joa_from = ""; if (isset ($_POST['joa_from'])) $joa_from = $_POST['joa_from']; 
				$joa_to = ""; if (isset ($_POST['joa_to'])) $joa_to = $_POST['joa_to']; 
				$joa_status = 2; if (isset ($_POST['joa_status'])) $joa_status = $_POST['joa_status']; 	
				$response = callServer ($_def_general_server."tempstaff.php?function=insertAnswer", array("data"=>array("job_index"=>$job_index, "usr_index"=>$usr_index, "joa_from"=>$joa_from, "joa_to"=>$joa_to, "joa_status"=>$joa_status)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
		}
		elseif ($action == "del")
		{
			if ($uty_id == 1)
			{
				$swi_index = 0; if (isset ($_POST['swi_index'])) $swi_index = $_POST['swi_index']; 
				
				$response = callServer ($_def_general_server."kid.php?function=deleteSwitch", array("data"=>array("swi_index"=>$swi_index)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
			if ($uty_id == 3)
			{
				$joa_index = 0; if (isset ($_POST['joa_index'])) $joa_index = $_POST['joa_index']; 
				
				$response = callServer ($_def_general_server."tempstaff.php?function=deleteAnswer", array("data"=>array("joa_index"=>$joa_index)));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
					}	
				}
			}
		}
	}	
	elseif ($m_id == 5) // Personalmangel
	{
		$abs_date = ""; if (isset ($_POST['abs_date'])) $abs_date = $_POST['abs_date']; 
		$abs_points_7 = 0; if (isset ($_POST['abs_points_7'])) $abs_points_7 = $_POST['abs_points_7']; 
		$abs_points_3 = 0; if (isset ($_POST['abs_points_3'])) $abs_points_3 = $_POST['abs_points_3'];
			
		$response = callServer ($_def_general_server."kid.php?function=createPersonalmangel", array("data"=>array("abs_date"=>$abs_date, "abs_points_7"=>$abs_points_7, "abs_points_3"=>$abs_points_3)));			

	}
	
	elseif($m_id == 7)
	{
		$nachricht = ""; if (isset ($_POST['nachricht'])) $nachricht = $_POST['nachricht'];
		$lin_index = 0; if (isset ($_POST['lin_index'])) $lin_index = $_POST['lin_index'];
		$nachricht = konform($nachricht);
		
		$response = callServer ($_def_general_server."kid.php?function=sendemail", array("data"=>array("lin_index"=>$lin_index, "nachricht"=>$nachricht)));	
		
		
	}
	
	elseif($m_id == 8)
	{
		$email = 0; if (isset ($_POST['email'])) $email = $_POST['email'];
		$phone = 0; if (isset ($_POST['phone'])) $phone = $_POST['phone'];
		$lin_index = 0; if (isset ($_POST['lin_index'])) $lin_index = $_POST['lin_index'];
		$nachricht = konform($nachricht);
		
		$response = callServer ($_def_general_server."kid.php?function=updateUserSettings", array("data"=>array("lin_index"=>$lin_index, "email"=>$email, "phone"=>$phone)));	
	}
	
	elseif($m_id == 9)
	{
		$response = callServer ($_def_general_server."kid.php?function=insdelLaundry", array("data"=>array("action"=>$_POST['action'], "post"=>$_POST)));	
	}

	$msg_id = 0;
	if (isset ($response['retval']))
	{
		if ($response['msg_id'] > 0)
		{
			$msg_id = $response['msg_id'];
		}
	}
	header ("Location: index.php?m_id=".$m_id."&msg_id=".$msg_id."&openup=".$openup."&allev=".$allev);
?>