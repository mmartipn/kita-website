<?php

		
		echo '<h2>Dokumente:</h2>
			
					<div class="table-wrapper">
						<table class="wb"><tbody>
							<tr>
								<td><b>Dokument</b></td>
								<td><b>Download</b></td>
							</tr>';
							include 'app_srv/sqlconn.php'; $dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		$db_selected = mysqli_select_db($dbconn, $dbname);
							$result_txt = mysqli_query ($dbconn, "SELECT txt_index, txt_desc, txt_text FROM tbl_txt_text");
							while ($row_txt = mysqli_fetch_array($result_txt)) 
							{
								echo '
									<tr>
										<td>'.$row_txt["txt_desc"].'</td>
										<td><a href="../uploads/doc/'.$row_txt["txt_text"].'" download><i class="icon fa-arrow-down"></a></td>
									</tr>';
							}
							echo '
						</tbody></table>
					</div>';
	

?>