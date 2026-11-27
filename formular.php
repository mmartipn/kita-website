<?php
	session_start ();
	include 'functions.php';
	include 'defines.php';
	
	$m_id = 1; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	$msg_id = 0; if (isset($_REQUEST["msg_id"])) $msg_id = $_REQUEST["msg_id"];
	$msg = ""; 
		if ($msg_id == 1) $msg = $_def_login_incorrect;  
		elseif ($msg_id == 2) $msg = $_def_login_locked; 
		elseif ($msg_id == 8) $msg = $_def_login_pwr_emailgesendet; 
		elseif ($msg_id == 9) $msg = $_def_login_pwr_ok; 
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
		<link rel="stylesheet" href="assets/css/main3.css" />
	</head>
	<body>
		<div class="page-wrap">
			<div class="wrsec">
				<!-- Nav -->
					<section id="header">
						<img src="assets/logo.png">
						<nav id="nav">
							<?php
								echo '<a href="formular.php?m_id=1"'; if ($m_id == 1) echo ' class="menactive"'; echo '>Login</a>';
								echo '<a href="formular.php?m_id=2"'; if ($m_id == 2) echo ' class="menactive"'; echo '>Passwort vergessen</a>';
								echo '<a href="formular.php?m_id=101"'; if ($m_id == 101) echo ' class="menactive"'; echo '>Impressum</a>';
								echo '<a href="#" target="_blank">Datenschutz</a>';
							?>	
						</nav>
						<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
					</section>
				<!-- Main -->
					<section id="main">
						<?php
							if ($m_id == 1)
							{
								echo '<h2>Login</h2>
										<form action="login.php" method="post"><input type="hidden" name="action" value="lgn">
										<br>'.$msg.'
										<div class="table-wrapper">
										<table>
										<tr><td>Emailadresse</td><td><input name="name" type="text" class="fuck" placeholder="Emailadresse"></td></tr>
										<tr><td>Passwort</td><td><input name="password" type="password" class="fuck"  placeholder="Passwort">
										</td></tr>
										<tr><td colspan=2><input value="login" class="button" type="submit" onclick="callc()"></td></tr>
										</table>
										</div>
										</form>';	

								echo '<br><hr><br><h2>Willkommen</h2>
								...in der offiziellen App der Kl&ouml;zechen.<br>
								Wir sind eine Eltern-Kind-Initiative in München-Laim, die 1998 als eingetragener Verein gegründet wurde. 
								In unserer Kinderkrippe werden 14 Kinder im Alter von 1½ bis 3 Jahren liebevoll, in familiärer Umgebung betreut.<br>
								Und das hier ist unsere App :)<br>
								Hier kannst du Abwesenheiten eintragen, dich in die diverse Veranstaltungs- oder Aufgaben-Listen eintragen und 
								findest alle Termine und Informationen für den Kita Alltag in unserer Einrichtung.<br><br><br>
								Das bedeutet diese App ist für die Eltern, Erzieher uns Aushilfen unserer Eincrichtung gedacht - 
								Wenn du stattdessen auf der Suche nach allgemeinen Informationen zu unserer Einrichtung 
								bist oder uns kennen lernen möchtest, besuche einfach unsere Webseite: <a href="#" target="_blank">www.beispiel-kita.example</a>';
							}
							elseif ($m_id == 2)
							{
								echo '<h2>Passwort vergessen ?</h2><br>'.$msg;
								if (strlen($msg) == 0) 
									echo '
									<form action="login.php" method="post"><input type="hidden" name="action" value="fgt">
									Bitte gib hier deine Emailadresse an.<br>
									Sollte diese im System bekannt sein, bekommst du eine Email mit einem Link zum zurr&uuml;cksetzen deines Passworts.<br>
									(Bitte Spam Ordner kontrollieren)
									<div class="table-wrapper">
									<table>
									<tr><td>Emailadresse</td><td><input name="name" type="text" class="fuck" placeholder="Emailadresse"></td></tr>
									<tr><td colspan=2><input value="senden" class="button" type="submit"></td></tr>
									</table>
									</div>
									</form>';	
								else
									echo '<br><br><a href="formular.php">Hier</a> geht es zur&uuml;ck zum login.';
							}
							elseif ($m_id == 3)
							{
								$hash = ""; if (isset($_REQUEST["hash"])) $hash = $_REQUEST["hash"];
								echo '<h2>Passwort neu setzen</h2><br>'.$msg;
								if (strlen($msg) == 0) 
									echo '
										<form action="login.php" method="post" id="form_pwdf" onsubmit="return checkform(1)"><input type="hidden" name="action" value="stn">
										<input name="hash" type="hidden" value="'.$hash.'">
										<br>
										<div class="table-wrapper">
										<table>
										<tr><td>neues Passwort</td><td><input name="password" type="password" class="fuck"  placeholder="Passwort"></td></tr>
										<tr><td>neues Passwort (nochmal)</td><td><input name="password2" type="password" class="fuck"  placeholder="Passwort"></td></tr>
										<tr><td colspan=2><input value="&auml;ndern" class="button" type="submit"></td></tr>
										</table>
										</div>
										</form>';	
								else
									echo '<br><br><a href="formular.php">Hier</a> geht es zur&uuml;ck zum login.';
							}
							elseif ($m_id == 5)
							{

							}

							

						?>	
					</section>
				<!-- Footer -->	
					<section id="footer">
					</div>
			</div>							
		</div>
		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/checkform.js"></script>		
	</body>
</html>