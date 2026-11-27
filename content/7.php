<?php
	echo '
		<h2>Kontakt:</h2>
		<br>Problem, Hinweis, Idee oder sonst was ?<br>
		Schreibe uns einfach eine Nachricht: <br>
		<form action="index_save.php?m_id=7" method="post" id="form_kon" onsubmit="return checkform(3)">
			<textarea id="nachricht" name="nachricht" rows="4" cols="50"></textarea>
			<input type="hidden" name="lin_index" value="'.$lid_id.'">
			<input value="senden" class="button" type="submit">
		</form>';

?>