	function checkform(index)
	{
		var fehlen = "";
			
		if (index == 1) //neues pw setzen
		{	
			if (document.forms["form_pwdf"].elements["password"].value == "" ){ 
				fehlen = "Passwort 1"; }
			if (document.forms["form_pwdf"].elements["password2"].value == "" ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Passwort 2";  }
			if (document.forms["form_pwdf"].elements["password"].value != document.forms["form_pwdf"].elements["password2"].value ){ 
				if (fehlen.length > 0) fehlen = fehlen + ", ";	
				fehlen = fehlen + "Die beiden Passwoerter stimmen nicht ueberein";  }
		}	
		
		if (index == 2) //abwesenheit eintragen
		{
			if (document.forms["form_abs"].elements["datefromto"].value == "" ){ 
				fehlen = "Datum"; }
			
			var str = document.forms["form_abs"].elements["datefromto"].value;
			var count = (str.match(/\./g) || []).length;
			if (count < 4 ){ 
				fehlen = "Datum"; }		
		}	
		
		if (index == 3) //kontakt
		{
			if (document.forms["form_kon"].elements["nachricht"].value == "" ){ 
				fehlen = "Nachricht"; }
		}	
		
		if (fehlen.length == 0)
		{
			return true;
		}
		else
		{
			fehlen = "Bitte vervollstaendigen Sie die Angaben folgender Felder:\n" + fehlen;
			alert(fehlen);
			return false;
		}	
			
	}
