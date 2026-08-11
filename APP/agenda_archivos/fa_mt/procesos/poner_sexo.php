<?php  
require("../../../../Config/Conectar.inc");
echo "Hola  " . $_SERVER['PHP_SELF'] . "<br>"; 
echo "La id_lote es: " . $id_lote . "<br>";
//exit();
$dropear="drop table if exists $base_fa.tmp_fa"; 
mysql_query($dropear);

$crear="CREATE TABLE $base_fa.tmp_fa 
		 	SELECT periodo, 
		 		MID(cuit_titular,1,11) as cuit_titular, 
		 		MID(nd_fam,1,8) AS nd,ayn,  
		 		fec_incorporacion, 
		 		'1880-01-01' f_alta,
		 		'00000000000' AS cuil_fam, 
		 		TRIM(MID(ayn, 1,INSTR(ayn,' '))) AS apellido,
		 		TRIM(MID(ayn, INSTR(ayn,' ')+1,99)) AS nombre,
		  		'1880-01-01' AS fn, 
		  		'I' AS sexo, 
		  		'sin calle' calle, 
		  		'0000' numero, 
		  		' 'piso,
		  		' ' dto,
		   		'sin loca' nombreLoca, 
		   		'0000' cp, 
		   		'99' provincia,
		   		'012' pais, 
		   		'1234567890' telefono,		   		
		    	parentesco,
		    	0 AS id_parentesco,
		    	0  estado_civil,
		    	tipo,
		    	0 AS  id_domicilio 
		    	
		   FROM $base_fa.fa_historico_nuevo 
		   WHERE id_lote=$id_lote

";
mysql_query($crear); 
$alter="ALTER TABLE $base_fa.tmp_fa ADD id INT PRIMARY KEY AUTO_INCREMENT FIRST";
mysql_query($alter); 
$alter2="ALTER table $base_fa.tmp_fa modify column id INT NOT NULL AUTO_INCREMENT FIRST PRIMARY KEY";
mysql_query($alter2); 
$indx="create index qwer on $base_fa.tmp_fa (nombre)"; 
mysql_query($indx);
$indx2="create index qwer2 on $base_fa.tmp_fa (cuit_titular)"; 
mysql_query($indx2);
$pone_sexo="update $base_fa.tmp_fa fa, $base_fa.nombre_sexo ns set fa.sexo=ns.sex where fa.nombre=ns.nombre";
mysql_query($pone_sexo);
// CUIL 
mysql_query("ALTER TABLE `$base_fa`.`tmp_fa` ADD INDEX (`nd`), ADD INDEX (`sexo`)");
$sql="UPDATE $base_fa.tmp_fa SET cuil_fam=prueba.consulta_cuil(nd,sexo)";
mysql_query($sql);

?>
