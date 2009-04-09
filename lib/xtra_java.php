<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
  {
	if(document.forms[0].id.value == "0") 
   	{ 
		errors += " - You are not allowed to delete this.\n";
		alert(errors);
		return false ;
	} else {
		var agree=confirm("Are you sure?");
		if (agree)
			return true ;
		else
			return false ;
	}
  }

  
// -->
</script>
