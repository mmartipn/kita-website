<html>
	<head>
		<title></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body>
	
	Test 1:<br>
	<input value="Test1 button" class="button small" type="button" onclick="toggledivwb('lis1')">
	<div class="modal" id="lis1" style="display:none;">
		<div class="modal-dialog">
			<div class="modal-body">
				geht
				<input value="schliessen" class="button small" type="button" onclick="toggledivwb('lis1')">
			</div>
		</div>
	</div>
	<br>
	<hr>
	<br>
	Test 2 (test in test):<br>
	<input type="button" value="Schritt 1" class="small" onclick="toggledivwb('s1')">
	<div class="contenttgl" id="s1" style="padding-left:10px;display:none;">
		hier der innere test: 
		<input value="Test1 button" class="button small" type="button" onclick="toggledivwb('lis2')">
			<div class="modal" id="lis2" style="display:none;">
				<div class="modal-dialog">
					<div class="modal-body">
						geht auch
						<input value="schliessen" class="button small" type="button" onclick="toggledivwb('lis2')">
					</div>
				</div>
			</div>
	</div>


		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
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