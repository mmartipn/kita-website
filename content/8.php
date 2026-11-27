<?php
	$lin_email = "";
	$lin_phone = "";
	$lin_email__notify = 0;
	$lin_phone__notify = 0;
	$response = callServer ($_def_general_server."kid.php?function=getUserSettings", array("data"=>array("lin_index"=>$lid_id)));
	if (isset ($response['data']))
	{
		foreach ($response['data'] as $value) 
		{	
			$lin_email = $value["lin_email"];
			$lin_phone = $value["lin_phone"];
			$lin_email__notify = $value["lin_email__notify"];
			$lin_phone__notify = $value["lin_phone__notify"];
		}
	}
	
	echo '
		<h2>Einstellungen:</h2>
		
		<br>Wie m&ouml;chte ich Benachrichtigungen und Mitteilungen erhalten:<br>
		<form action="index_save.php?m_id='.$m_id.'" method="post">
		
		<div class="table-wrapper">
			<table class="wb">
				<tr>
					<td>Email (';
					if (strlen($lin_email) == 0) echo 'keine Emailadresse hinterlegt';
					else echo $lin_email;
					echo ')</td></td>
					<td>';
					if (strlen($lin_email) > 0) echo '<input type="checkbox" id="email" name="email" value="1" '.ischk(1, $lin_email__notify).'><label for="email"></label>';
					echo '</td>
				</tr>
				<tr>
					<td>Handy (';
					if (strlen($lin_phone) == 0) echo 'keine Handynummer hinterlegt';
					else echo $lin_phone;
					echo ')</td>
					<td>';
					if (strlen($lin_phone) > 0) echo '<input type="checkbox" id="phone" name="phone" value="1" '.ischk(1, $lin_phone__notify).'><label for="phone"></label>';
					echo '</td>
				</tr>
			</table>
		</div>
			<input type="hidden" name="lin_index" value="'.$lid_id.'">
			<br><input value="speichern" class="button" type="submit">
		</form>
		<br>
		(um eine neue Handynummer oder EmailAdresse zu hinterlegen schreibe uns bitte eine Nachricht)';

?>