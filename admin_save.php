<?php
	include 'checkuser.php';
	include 'functions.php';
	include 'defines.php';
	
	$response = array();
	$m_id = 0; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	
	$lid_id = 0; if (isset ($_SESSION['loggy_lid_id'])) $lid_id = $_SESSION['loggy_lid_id']; 
	$usr_id = 0; if (isset ($_SESSION['loggy_usr_id'])) $usr_id = $_SESSION['loggy_usr_id']; 
	$uty_id = 0; // if (isset ($_SESSION['loggy_uty_id'])) $uty_id = $_SESSION['loggy_uty_id'];
	$a_id = 0; if (isset ($_REQUEST["a_id"])) $a_id = $_REQUEST["a_id"];
	$uty_id = $a_id;
	if ($usr_id == 0) 
	{
		header("Location: formular.php");
		exit();
	}
	
	if ($m_id == 2) // Abwesenheit
	{
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		if ($action == "ins")
		{
			if ($uty_id == 1)
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
					
					$dates = date_range($datefr_str, $dateto_str, $uty_id, '+1 day', 'Y-m-d', true);
						
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
					
					$dates = date_range($datefr_str, $dateto_str, $uty_id,'+1 day', 'Y-m-d', true); 
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
					
					$dates = date_range($datefr_str, $dateto_str, $uty_id,'+1 day', 'Y-m-d', true); 
										
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
				
				$response = callServer ($_def_general_server."kid.php?function=deleteAbsence", array("data"=>array("abs_index"=>$abs_index, "uty_index"=>4 )));
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
		elseif ($action == "swi")
		{
			if ($uty_id == 1)
			{
				$abs_index = 0; if (isset ($_POST['abs_index'])) $abs_index = $_POST['abs_index']; 
				
				$response = callServer ($_def_general_server."kid.php?function=createSwitch", array("data"=>array("abs_index"=>$abs_index)));
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
		$uty_id =3;
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		if ($action == "upd")
		{
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
	elseif ($m_id == 5) // User
	{
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		if ($action == "ins")
		{
			$lin_admin=0;  if (isset ($_POST['lin_admin'])) $lin_admin = $_POST['lin_admin'];
			$response = callServer ($_def_general_server."kid.php?function=insertUser", array("data"=>array("uty_index"=>$_POST['uty_index'], "usr_name"=>$_POST['usr_name'], "lin_name"=>$_POST['lin_name']
			, "lin_email"=>$_POST['lin_email'], "lin_phone"=>$_POST['lin_phone'], "lin_benach"=>$_POST['lin_benach'], "lin_admin"=>$lin_admin)));
		}
		elseif ($action == "upd")
		{
			$lin_active=0;  if (isset ($_POST['lin_active'])) $lin_active = $_POST['lin_active'];
			$lin_admin=0;  if (isset ($_POST['lin_admin'])) $lin_admin = $_POST['lin_admin'];
			$lin_locked=0;  if (isset ($_POST['lin_locked'])) $lin_locked = $_POST['lin_locked'];
			$lin_delete=0;  if (isset ($_POST['lin_delete'])) $lin_delete = $_POST['lin_delete'];
			
			if ($lin_delete == 1) //delete
			{
				$response = callServer ($_def_general_server."kid.php?function=deleteUser", array("data"=>array("lin_index"=>$_POST['lin_index'], "usr_index"=>$_POST['usr_index'])));
			}
			else //update
			{
				$response = callServer ($_def_general_server."kid.php?function=updateUser", array("data"=>array(
				"lin_index"=>$_POST['lin_index'], 
				"usr_index"=>$_POST['usr_index'], 
				"usr_name"=>$_POST['usr_name'], 
				"lin_name"=>$_POST['lin_name'], "lin_email"=>$_POST['lin_email'], "lin_phone"=>$_POST['lin_phone'], "lin_benach"=>$_POST['lin_benach'], 
				"lin_active"=>$lin_active,"lin_admin"=>$lin_admin,"lin_locked"=>$lin_locked,"lin_password"=>$_POST['lin_password'])));
			}
			
		}
	}
	
	elseif($m_id == 7)
	{
		$reset=0;  if (isset ($_POST['reset'])) $reset = $_POST['reset'];
		if ($reset == 1) $response = callServer ($_def_general_server."kid.php?function=resetAll", array("data"=>array()));	
	}
	
	elseif ($m_id == 8) // Sonderfall
	{
		$a_da = ''; if (isset ($_POST['a_da'])) $a_da = $_POST['a_da']; 
		$a_gn = 0; if (isset ($_POST['a_gn'])) $a_gn = $_POST['a_gn']; 
		$a_12 = 0; if (isset ($_POST['a_12'])) $a_12 = $_POST['a_12']; 
		$a_14 = 0; if (isset ($_POST['a_14'])) $a_14 = $_POST['a_14']; 
				
		$response = callServer ($_def_general_server."kid.php?function=insSonderfall", array("data"=>array("a_da"=>$a_da, "a_gn"=>$a_gn,"a_12"=>$a_12,"a_14"=>$a_14)));
		if (isset ($response['data']))
		{
			foreach ($response['data'] as $value) 
			{
			}	
		}
	}
	elseif ($m_id == 9) // Notbetreuung
	{	
		$abs_date = ''; if (isset ($_POST['abs_date'])) $abs_date = $_POST['abs_date'];
		$response = callServer ($_def_general_server."kid.php?function=setNotbetreuungKids", array("data"=>array("abs_date"=>$abs_date, "post"=>$_POST)));
		if (isset ($response['data']))
		{
			foreach ($response['data'] as $value) 
			{
				
			}	
		}
	}
	elseif ($m_id == 10) // Events
	{
		$evt_index = ""; if (isset ($_POST['evt_index'])) $evt_index = $_POST['evt_index']; 
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		$evt_image__name = ""; if( isset( $_FILES['evt_image'.$evt_index] ) && !empty($_FILES['evt_image'.$evt_index]['name'] ) ) $evt_image__name = uploadfile($_FILES['evt_image'.$evt_index], "img");
		$evt_appendix__name = ""; 	if ( isset( $_FILES['evt_appendix'.$evt_index] ) && !empty($_FILES['evt_appendix'.$evt_index]['name'] ) ) $evt_appendix__name = uploadfile($_FILES['evt_appendix'.$evt_index], "app");
		$_POST["evt_image__name".$evt_index] = $evt_image__name;
		$_POST["evt_appendix__name".$evt_index] = $evt_appendix__name;
		
		$response = callServer ($_def_general_server."kid.php?function=insupdEvent", array("data"=>array("action"=>$action, "post"=>$_POST))); 
		
					
	}
	elseif ($m_id == 11) // Lists
	{
		$action = ""; if (isset ($_POST['action'])) $action = $_POST['action']; 
		$response = callServer ($_def_general_server."kid.php?function=insupdList", array("data"=>array("action"=>$action, "post"=>$_POST))); 
					
	}
	
	elseif ($m_id == 12) //waesche
	{
		
		include 'app_srv/sqlconn.php'; $dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		$db_selected = mysqli_select_db($dbconn, $dbname);
	
		
		$new = 0; if (isset ($_REQUEST['new'])) $new = $_REQUEST['new'];  
		
		if ($new == 1)
		{
			$lau_ds = explode("#", $_POST["lau_date"]);
			foreach ($lau_ds as $lau_d) 
			{
				mysqli_query ($dbconn, "INSERT INTO tbl_lau_laundry(lau_date, lau_usr_index) VALUES ('".$lau_d."',0)");
			}
		}
		elseif ($new == 2)
		{
			$lau_index = 0; if (isset ($_POST['lau_index'])) $lau_index = $_POST['lau_index']; 
			$usr_index = 0; if (isset ($_POST['usr_index'])) $usr_index = $_POST['usr_index'];
			$lau_delete = ""; if (isset ($_POST['lau_delete'])) $lau_delete = $_POST['lau_delete']; 
		 
			if ($lau_delete == 1)
				mysqli_query ($dbconn, "DELETE FROM tbl_lau_laundry WHERE lau_index=".$lau_index);
			else
				mysqli_query ($dbconn, "UPDATE tbl_lau_laundry SET lau_usr_index=".$usr_index." WHERE lau_index=".$lau_index);
			
		}
	}
	
	elseif ($m_id == 13) //dokdum
	{
		
		include 'app_srv/sqlconn.php'; $dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		$db_selected = mysqli_select_db($dbconn, $dbname);
		
		$action = ""; if (isset ($_REQUEST['action'])) $action = $_REQUEST['action'];  
		
		if ($action == "ins")
		{
			$txt_text_name = ""; if( isset( $_FILES['txt_text'] ) && !empty($_FILES['txt_text']['name'] ) ) $txt_text_name = uploadfile($_FILES['txt_text'], "doc");
		
			mysqli_query ($dbconn, "INSERT INTO tbl_txt_text(txt_desc, txt_text) VALUES ('".$_POST['txt_desc']."','".$txt_text_name."')");
		
		}
		elseif ($action == "del")
		{
			$txt_index = 0; if (isset ($_POST['txt_index'])) $txt_index = $_POST['txt_index']; 
			
				mysqli_query ($dbconn, "DELETE FROM tbl_txt_text WHERE txt_index=".$txt_index);
			
		}
	}
	elseif ($m_id == 14) //az
	{
		
		include 'app_srv/sqlconn.php'; $dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		$db_selected = mysqli_select_db($dbconn, $dbname);
		
		$action = ""; if (isset ($_REQUEST['action'])) $action = $_REQUEST['action'];  
		
		if ($action == "ins")
		{
			$ros_from = ""; if (isset ($_POST['ros_from'])) $ros_from = $_POST['ros_from']; 
			$ros_to = ""; if (isset ($_POST['ros_to'])) $ros_to = $_POST['ros_to']; 
			$ros_dayofweek = 0; if (isset ($_POST['ros_dayofweek'])) $ros_dayofweek = $_POST['ros_dayofweek']; 
			$usr_index = 0; if (isset ($_POST['usr_index'])) $usr_index = $_POST['usr_index']; 
		
			mysqli_query ($dbconn, "INSERT INTO tbl_ros_roster(ros_dayofweek, ros_usr_index, ros_from, ros_to, ros_from__real, ros_to__real) VALUES ('".$ros_dayofweek."','".$usr_index."','".$ros_from."','".$ros_to."','".$ros_from."','".$ros_to."')");
		
		}
		elseif ($action == "del")
		{
			$ros_index = 0; if (isset ($_POST['ros_index'])) $ros_index = $_POST['ros_index']; 
			
			mysqli_query ($dbconn, "DELETE FROM tbl_ros_roster WHERE ros_index=".$ros_index);
			
		}
	}
											
	
	$msg_id = 0;
	if (isset ($response['retval']))
	{
		if ($response['msg_id'] > 0)
		{
			$msg_id = $response['msg_id'];
		}
	}
	header ("Location: admin.php?m_id=".$m_id."&msg_id=".$msg_id."&a_id=".$a_id);
?>