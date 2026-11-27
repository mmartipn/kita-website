<?php
	include 'checkuser.php';
	include 'functions.php';
	include 'defines.php';
		
	$lid_id = 0; if (isset ($_SESSION['loggy_lid_id'])) $lid_id = $_SESSION['loggy_lid_id']; 
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
		<META HTTP-EQUIV="Refresh" CONTENT="2; index.php">
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
								echo '<a href="formular.php?m_id=5"'; if ($m_id == 5) echo ' class="menactive"'; echo '>Impressum</a>';
							?>	
						</nav>
						<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
					</section>
				<!-- Main -->
					<section id="main">
						<?php
								echo "
                                <br><br>falls du nicht innherlab von 5 Sekunden autmoatisch weitergeleitet wirst, klicke bitte <a href='index.php'>hier</a>
                                <form action='tokenupdate.php' method='post' id='tokenform' name='tokenform'>
                                    <input type='hidden' name='token' id='token' value='x'>
                                </form>";
						?>	
					</section>
				
				<!-- Footer -->	
					<section id="footer">
					</section>
			</div>							
		</div>
		<!-- Scripts -->
                <script language="JavaScript">
                    function dataFromArgumentToHTML(argument) 
                    {
                        /*jsdata.innerHTML = "JS function dataFromArgumentToHTML(): " + argument || ""*/
                        document.getElementById("token").value = argument;
						document.tokenform.submit();
                    }
                </script>';

	</body>
</html>