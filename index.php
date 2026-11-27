<?php
	include 'checkuser.php';
	include 'functions.php';
	include 'defines.php';

	$m_id = 1; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	
	if ($m_id == 100)
	{
		if (isset ($_SESSION['loggy_lid_id'])) unset($_SESSION['loggy_lid_id']); 
		if (isset ($_SESSION['loggy_usr_id'])) unset($_SESSION['loggy_usr_id']); 
		if (isset ($_SESSION['loggy_uty_id'])) unset($_SESSION['loggy_uty_id']);
		header ("Location: formular.php");
		exit();
	}
		
	$lid_id = 0; if (isset ($_SESSION['loggy_lid_id'])) $lid_id = $_SESSION['loggy_lid_id']; 
	$usr_id = 0; if (isset ($_SESSION['loggy_usr_id'])) $usr_id = $_SESSION['loggy_usr_id']; 
	$uty_id = 0; if (isset ($_SESSION['loggy_uty_id'])) $uty_id = $_SESSION['loggy_uty_id'];
	$usr_ia = 0; if (isset ($_SESSION['loggy_usr_ia'])) $usr_ia = $_SESSION['loggy_usr_ia']; 
	
	$msg_id = 0; 	if (isset($_REQUEST["msg_id"])) $msg_id = $_REQUEST["msg_id"];
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
								echo '<a href="index.php?m_id=1"'; if ($m_id == 1) echo ' class="menactive"'; echo '>Aktuelles</a>';
								echo '<a href="index.php?m_id=2"'; if ($m_id == 2) echo ' class="menactive"'; echo '>Abwesenheit</a>';
								if ($uty_id == 1) 
								{	
									echo '<a href="index.php?m_id=3"'; if ($m_id == 3) echo ' class="menactive"'; echo '>Tauschen</a>';
									echo '<a href="index.php?m_id=4"'; if ($m_id == 4) echo ' class="menactive"'; echo '>Statistik</a>';
									echo '<a href="index.php?m_id=9"'; if ($m_id == 9) echo ' class="menactive"'; echo '>W&auml;schedienst</a>';
								}
								elseif ($uty_id == 2) 
								{	
									echo '<a href="index.php?m_id=3"'; if ($m_id == 3) echo ' class="menactive"'; echo '>Aushilfen</a>';
								//	echo '<a href="index.php?m_id=4"'; if ($m_id == 4) echo ' class="menactive"'; echo '>Statistik</a>';
									echo '<a href="index.php?m_id=9"'; if ($m_id == 9) echo ' class="menactive"'; echo '>W&auml;schedienst</a>';
								}
								elseif ($uty_id == 3) 
								{	
									echo '<a href="index.php?m_id=3"'; if ($m_id == 3) echo ' class="menactive"'; echo '>Ausschreibungen</a>';
								//	echo '<a href="index.php?m_id=4"'; if ($m_id == 4) echo ' class="menactive"'; echo '>Statistik</a>';
								}
								elseif ($uty_id == 4) 
								{	
									echo '<a href="index.php?m_id=5"'; if ($m_id == 5) echo ' class="menactive"'; echo '>Personalmangel</a>';
									echo '<a href="index.php?m_id=4"'; if ($m_id == 4) echo ' class="menactive"'; echo '>Statistik</a>';
								}				
								echo '<a href="index.php?m_id=10"'; if ($m_id == 10) echo ' class="menactive"'; echo '>Dokumente</a>';	
								echo '<a href="index.php?m_id=8"'; if ($m_id == 8) echo ' class="menactive"'; echo '>Einstellungen</a>';
								echo '<a href="index.php?m_id=101"'; if ($m_id == 101) echo ' class="menactive"'; echo '>Impressum</a>';
								echo '<a href="#" target="_blank">Datenschutz</a>';
								if ($usr_ia == 1)
								{
									echo '<a href="admin.php">Admin</a>';
								}
								echo '<a href="index.php?m_id=100">Ausloggen</a>';
							?>	
						</nav>
						<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
					</section>
				<!-- Main -->
					<section id="main">
						<?php
							if ($msg_id > 0)
							{
								$msg_txt = ""; 
								if ($msg_id == 1001) $msg_txt = "Nachricht erfolgreich verschickt";
								if ($msg_id == 1002) $msg_txt = "Fehler: Laut Dienstplan an diesem Tag kein Dienst";
								if ($msg_id == 1003) $msg_txt = "Fehler: es exisiteirt bereits eine Abwesenheit fuer diesen Tag";
								if ($msg_id == 1004) $msg_txt = "Fehler: dieser Tag ist eine eingetragener Schliesstag";
								if ($msg_id == 1005) $msg_txt = "Fehler: dieser Tag ist an einem Wochenende";
								if ($msg_id == 1006) $msg_txt = "Fehler: an diesem Tag ist Notdienst.<br>Deshlab bitte stattdessen ein Tauschangebot erstellen.";
								if ($msg_id == 1007) $msg_txt = "Fehler: ´ich biete´ und ´ich suche´ sind identisch!";
								if ($msg_id == 1008) $msg_txt = "Fehler: kein Datum ausgewaehlt.";
								if ($msg_id == 1009) $msg_txt = "Glueckwunsch: Es gab bereits ein Angebot das zu deinem Angebot passt, deshalb wurde der Tausch direkt angenommen.";
								if ($msg_id == 1010) $msg_txt = "Vorsicht: Bitte denk daran der Aushilfe die du angegeben hattest abzusagen !";
								echo '
								<div class="overlayblock" id="o_modal1"></div>
								<div class="modalblock"  id="m_modal1"><br>
								<p>'.$msg_txt.'</p>
								<input value="schliessen" class="button small" type="button" onclick="toggledivwb2(\'_modal1\')">';
							}
							if (file_exists('content/'.$m_id.'.php')) 
								include_once 'content/'.$m_id.'.php';
							else
								echo "content missing";
						?>	
					</section>
				
				<!-- Footer -->	
					<section id="footer">
						<div class="table-wrapper">
						<table class="alt">
						<tbody>
							<tr>
								<?php
									echo '<td><a href="index.php?m_id=1" class="footerbutton'; if ($m_id == 1) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-home fsbig"></i><br>Aktuelles</div></a></td>';
									echo '<td><a href="index.php?m_id=2" class="footerbutton'; if ($m_id == 2) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-minus-circle fsbig"></i><br>Abwesenheit</div></a></td>';
									if ($uty_id == 1) 
									{	
										echo '<td><a href="index.php?m_id=3" class="footerbutton'; if ($m_id == 3) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-arrows-h fsbig"></i><br>Tauschen</a></td>';
										echo '<td><a href="index.php?m_id=4" class="footerbutton'; if ($m_id == 4) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-bar-chart fsbig"></i><br>Statistik</a></td>';
									}
									elseif ($uty_id == 2) 
									{	
										echo '<td><a href="index.php?m_id=3" class="footerbutton'; if ($m_id == 3) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-arrows-h fsbig"></i><br>Aushilfen</a></td>';
									//	echo '<td><a href="index.php?m_id=4" class="footerbutton'; if ($m_id == 4) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-bar-chart fsbig"></i><br>Statistik</a></td>';
									}
									elseif ($uty_id == 3) 
									{	
										echo '<td><a href="index.php?m_id=3" class="footerbutton'; if ($m_id == 3) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-arrows-h fsbig"></i><br>Ausschreibungen</a></td>';
									//	echo '<td><a href="index.php?m_id=4" class="footerbutton'; if ($m_id == 4) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-bar-chart fsbig"></i><br>Statistik</a></td>';
									}
									elseif ($uty_id == 4) 
									{	
										echo '<td><a href="index.php?m_id=5" class="footerbutton'; if ($m_id == 5) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-arrows-h fsbig"></i><br>Personalmangel</a></td>';
										echo '<td><a href="index.php?m_id=4" class="footerbutton'; if ($m_id == 4) echo ' menactive'; echo '"><div class="footerbutton"><i class="icon fa-bar-chart fsbig"></i><br>Statistik</a></td>';
									}
								?>
							</tr>
						</tbody>	
						</table>
						</div>
					</footer>
			</div>							
		</div>
		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/checkform.js"></script>
			<script src="assets/js/datepicker_datetime.min.js"></script>
			<script src="assets/js/datepicker_core.min.js"></script>
			<script src="assets/js/datepicker_baseplugin.min.js"></script>
			<script src="assets/js/datepicker_rangeplugin.min.js"></script>
			<script>
			  const picker = new easepick.create({
				element: document.getElementById('datepicker'),
				css: [
				  'assets/css/datepicker.min.css',
				],
				lang: 'de-DE',
				format: "DD.MM.YYYY",
				plugins: ['RangePlugin'],
				RangePlugin: {
				  tooltip: true,
				   locale: {
					one: 'Tag',
					other: 'Tage',
				  },
				},
			  });
			</script>
			<script>
			  const picker2 = new easepick.create({
				element: document.getElementById('datepicker2'),
				css: [
				  'assets/css/datepicker.min.css',
				],
				lang: 'de-DE',
				format: "DD.MM.YYYY",
			  });
			</script>
			<script language="JavaScript">
			function togglediv(id) {
				var divname = ""+id;
				var buttonname = "antworten"+id;
				var x = document.getElementById(divname);
				if (x.style.display === "none") {
					x.style.display = "block";
					(document.getElementById(buttonname)).style.display = "none";
				} else {
					x.style.display = "none";
				}
			}
			</script>
			<script language="JavaScript">
			function toggledivwb(id) {
				var divname = ""+id;
				var x = document.getElementById(divname);
				if (x.style.display === "none") {
					x.style.display = "block";
				} else {
					x.style.display = "none";
				}
			}
			</script>
			
			<script language="JavaScript">
			function toggledivwb2(id) {
				var divnamem = "m"+id;
				var divnameo = "o"+id;
				var x = document.getElementById(divnameo);
				var y = document.getElementById(divnamem);
				if (x.style.display === "none") {
					x.style.display = "block";
					y.style.display = "block";
				} else {
					x.style.display = "none";
					y.style.display = "none";
				}
			}
			</script>
			
			<script language="JavaScript">
			function toggledivtr(id) 
			{
				var divname = ""+id;
				var x = document.getElementsByClassName(divname);
				
			   for(var index=0;index < x.length;index++)
			   {
				   if (x[index].style.display === "none") 
				   {
						x[index].style.display = "table-row";
					} 
					else 
					{
						x[index].style.display = "none";
					}
				}
				
				
			}
			</script>
			

	</body>
</html>