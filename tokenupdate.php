<?php
	include 'checkuser.php';
	include 'functions.php';
	include 'defines.php';
		
	$lid_id = 0; if (isset ($_SESSION['loggy_lid_id'])) $lid_id = $_SESSION['loggy_lid_id']; 
	$token = ''; if (isset ($_POST['token'])) $token = $_POST['token']; 

    //echo $token." ### ".$lid_id ; 
    if ((strlen($token)>0) && ($lid_id>0))
    {

        $response = callServer ($_def_general_server."kid.php?function=updateToken", array("data"=>array("token"=>$token, "lid_id"=>$lid_id)));
		if (isset ($response['data']))
		{
			foreach ($response['data'] as $value) 
			{
			}	
		}
    }

	header ("Location: index.php");
?>