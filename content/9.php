<?php

	if ($uty_id == 1) // parent
	{
		
		echo '<h2>W&auml;schedienste:</h2>';
		$response = callServer ($_def_general_server."kid.php?function=getLaundry", array("data"=>array("usr_index"=>$usr_id)));
		if (isset ($response['data']))
		{
			if (sizeof($response['data'])>0)
			{
				echo '		
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Datum</b></td>
								<td><b>Kind (Anz)</b></td>
								<td><b>Aktion</b></td>
							</tr>';
							foreach ($response['data'] as $value) 
							{
								echo '
									<tr>
										<td class="wb">'.$value["lau_date"].'</td>
										<td>'.$value["usr_name"];
										if (intval($value["usr_index"])>0) echo ' ('.$value["lau_count"].'/4)';
										echo '</td>
										<td><form action="index_save.php?m_id='.$m_id.'" method="post"><input type="hidden" name="usr_index" value="'.$usr_id.'"><input type="hidden" name="lau_index" value="'.$value["lau_index"].'">';
										if ($value["lau_date_real"]>=date("Y-m-d"))
										{
											if (intval($value["usr_index"])>0)
											{
												if (intval($value["usr_index"]) == $usr_id)
													echo '<input type="hidden" name="action" value="del"><input value="l&ouml;schen" class="button small" type="submit">';
											}
											else
											{
												echo '<input type="hidden" name="action" value="ins"><input value="eintragen" class="button small" type="submit">';
											}
										}
									echo '</form></td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div>';
			}
		}
		
	}

?>