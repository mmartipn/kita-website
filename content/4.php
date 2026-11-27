<?php

	if ($uty_id == 1) // kind
	{
		echo '<h2>Tabelle:</h2><br>';
		$response = callServer ($_def_general_server."kid.php?function=getTable", array("data"=>array("usr_index"=>$usr_id)));
		if (isset ($response['data']))
		{
			echo '
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Platz</b></td>
								<td><b>Kind</b></td>
								<td><b>le.Abw.</b></td>
								<td><b>Punkte</b></td>
							</tr>';
							$platz = 1;
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td>'.$platz.'</td>
										<td>'.$value["usr_name"].'</td>
										<td>'.$value["abt_date"].'</td>
										<td>'.$value["abs_points"].'</td>
									</tr>';
								$platz ++;
							}
							echo '
						</tbody></table>
					</div>';
			
		}
		echo '<br>Hinweis: 
		Punkte sind hier gleichbedeutend mit Stunden. 
		Ein Tag hat immer 7 Stunden (8 Uhr bis 15 Uhr). 
		Das heisst: Wenn ein Kind aufgrund von Personalmangel einen Tag zu Hause bleiben muss, bekommt es 7 Punkte.
		Wenn es um 12 abgeholt werden muss, bekommt es 3 Punkte.<br>
		Je weiter oben das Kind in der Tabelle ist, umso wahrscheintlicher ist es, dass es beim n&auml;chsten Personalmangel nicht kommen kann oder fr&uuml;her abgeholt werden muss!<br><br>';
	}
?>