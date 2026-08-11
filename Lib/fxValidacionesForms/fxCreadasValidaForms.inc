	<script type="text/javascript">
	 	function validaCuit(cuit) {
	 		if (typeof (cuit) == 'undefined')
	 			return true;
	 		cuit = cuit.toString().replace(/[-_]/g, "");
	 		if (cuit == '')
	 			return true; //No estamos validando si el campo esta vacio, eso queda para el "required"
	 		if (cuit.length != 11)
	 			return false;
	 		else {
	 			var mult = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
	 			var total = 0;
	 			for (var i = 0; i < mult.length; i++) {
	 				total += parseInt(cuit[i]) * mult[i];
	 			}
	 			var mod = total % 11;
	 			var digito = mod == 0 ? 0 : mod == 1 ? 9 : 11 - mod;
	 		}
	 		return digito == parseInt(cuit[10]);
	 	}
	 	function validarFnacim(fn) { //VALIDA MAYORIA DE EDAD 
	 		var str_aux = fn.split("-");
	 		var anio= str_aux[2]*1+16;
	 		var fechaRevisar=new Date(anio,str_aux[1]-1,str_aux[0]);
	 		var hoy = new Date();
	 		if ( (hoy.getTime() - fechaRevisar.getTime()) < 0) {	
	 			return false;//es menor de 16
	 		}else {return true;}//es mayor de 16
	 	}

	 	function validarFnacim21(fn) { //VALIDA MAYORIA DE EDAD 
	 		var str_aux = fn.split("-");
	 		var anio= str_aux[2]*1+21;
	 		var fechaRevisar=new Date(anio,str_aux[1]-1,str_aux[0]);
	 		var hoy = new Date();
	 		if ( (hoy.getTime() - fechaRevisar.getTime()) < 0) {	
	 			return false;//es menor de 18
	 		}else {return true;}//es mayor de 18
	 	}

	 	function validarFnacim21y25(fn) { //VALIDA MAYORIA DE EDAD 
	 		var str_aux = fn.split("-");
	 		var anio= str_aux[2]*1+21;
	 		var fechaRevisar=new Date(anio,str_aux[1]-1,str_aux[0]);
	 		var hoy = new Date();
	 		if ( (hoy.getTime() - fechaRevisar.getTime()) < 0) {	
	 			return false;//es menor de 21
	 		}else { //es mayor de 21 
	 			var anio= str_aux[2]*1+26;
		 		var fechaRevisar=new Date(anio,str_aux[1]-1,str_aux[0]);
		 		var hoy = new Date();
		 		if ((hoy.getTime() - fechaRevisar.getTime()) < 0) {
					return true;//es mayor de 21 y menor de 25 
				 }else{return false;}
	 		}
	 	}
		 function countChecked() {
		 	var n = $("input:checked").length;
		 	if(n>=1){$("#exportar").removeAttr('disabled');}      
		}
		 function countChecked2() {
			 	var n = $("input:checked").length;
			 	if(n>=1){$("#descontar").removeAttr('disabled');}      
		}
			
		 function validarFormatoFecha(date){//Acepta 33-33-3333, arreglarla mas adelante
		 		if((date.match( /\d{2}-\d{2}-\d{4}/ ))&& date.length==10 || date=="")
		 		{return true;}
		 		else{return false;}	
		 }	
		function validarFechaAnterior(date2){//NO ANDA, ARREGLARLA
			dArr = date2.split("-");  // ex input "2010-01-18"
			dateNew2= dArr[1]+ "/" +dArr[2]+ "/" +dArr[0].substring(2); //ex out: "18/01/10"
			f1=new Date() 
			f2=new Date(dateNew2); 
			if ((f1>f2)||(f2==f1)){return true;}else{return false;}  
		}
		function cuilDni(cuil){
			cuilSubstr=cuil.substr(2,8);
			if($("#dni").val()==cuilSubstr){return true;}else{return false;}
		}
		function dniPrimerDigito(dni){
			dniSubstr=dni.substr(0,1);
			if(dniSubstr<=9){return true;}else{return false;}
		}
		function asesorValida(cod){
			if(cod.length%2==0){return true;}else{return false;}
		}
		function dosTelefonos(tel){
			var split = tel.split('/',2);
			var num1 = split[0];
			var num2 = split[1];
			if(tel=="" || (split==tel && (tel.length==10))  || ((num1.length==10) && (num2.length==10))){return true;}else{return false;}
		} 


</script>