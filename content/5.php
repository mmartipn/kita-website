<?php

	if ($uty_id == 4) // Admin
	{
		$abs_date = "".date("d.m.Y");
		echo '
		
		<h2>Personalmangel-Tag festlegen:</h2>
		<form id="pmfrm" action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="action" value="ins">
		<div class="table-wrapper">
			<table>
				<tr><td>Datum</td><td><input type="text" name="abs_date" id="datepicker2" value="'.$abs_date.'"/></td></tr>
				<tr>
					<td>Kinder gar nicht</td><td><input type="text" name="abs_points_7" value="0"/></td></tr>
					<td>Kinder um 12</td><td><input type="text" name="abs_points_3" value="0"/></td></tr>
				</tr>		
			</table>
		</div>
		<input value="speichern" class="button" type="submit">
		</form>
		<br>
		<hr>
		<br>
		<h2>Abwesenheiten:</h2>	
		<div class="table-wrapper">
			<table class="wb"><tbody>
				<tr>
					<td><b>Datum</b></td>
					<td><b>Kinder</b></td>
				</tr>';
				$response = callServer ($_def_general_server."kid.php?function=getPersonalmangelDates", array("data"=>array()));
				if (isset ($response['data']))
				{
					foreach ($response['data'] as $value) 
					{
							echo '
								<tr>
									<td>'.$value["abs_date"].'</td>
									<td><u>NICHT</u>: '.$value["kids_nicht"].'<br><u>BIS 12</u>:'.$value["kids_bis12"].'</td>
								</tr>';
					}
				} echo '
			</tbody></table>
		</div>';
	}
	

?>