<?php

if ( $_SESSION["usuario"] == "" ) {
	
	//echo "Su session caduco, por favor vuelva a loguearse"; exit();
	
	echo "<script> 
				
				abrirEnPestana('".DOMINIO."');
				alert('Su session caduco, por favor vuelva a loguearse');
				
				
				return false;
			
				function abrirEnPestana(url) {
					var a = document.createElement('a');
					a.target = '_blank';
					a.href = url;
					a.click();
					
				}
			</script> "; 
	exit();
}
