<?php session_start ();
if (!isset ($_SESSION["loggy_usr_id"]))
{
  header ("Location: formular.php");
}
?>