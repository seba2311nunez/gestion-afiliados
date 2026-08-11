<?php 
require(__DIR__."/../../../../Config/Conectar.inc");

mysql_query("CALL $base_fa.fa_proceso('$fproceso')"); 
echo "Termino $fproceso"; exit();

// CUIL 
$sql="UPDATE $base_fa.tmp_fa SET cuit_fam=prueba.consulta_cuil(nd,sexo)";
mysql_query($sql);

$sql2="UPDATE $base_fa.tmp_fa fa 
		LEFT JOIN $base_fa.dni_anio dn ON dn.doc3= MID(fa.nd,1,3)
		SET fa.fn=DATE_FORMAT(CONCAT(dn.anio,'-',CEIL(12*RAND()),'-',CEIL(12*RAND())),'%Y-%m-%d')
		";
mysql_query($sql2); 


// PARENTESCO
$sql3="UPDATE $base_fa.tmp_fa
		SET cod_parentesco=CASE
			WHEN parentesco=1 THEN '1'
			WHEN parentesco=2 THEN '3'
			END ";
mysql_query($sql3);


//Estado Civil
$sql4="UPDATE $base_fa.tmp_fa 
			SET estado_civil='2'
		";
mysql_query($sql4);

$sql4="UPDATE $base_fa.tmp_fa 
			SET estado_civil='1'
			WHERE cod_parentesco='1'";
mysql_query($sql4);
//Hijos mayores de 21 y menores de 25
$sql5="UPDATE $base_fa.tmp_fa
	SET cod_parentesco='4'
	WHERE cod_parentesco = '3'
		AND TIMESTAMPDIFF(YEAR,fn,CURDATE()) BETWEEN 20 AND 25";
mysql_query($sql5);
//Familiares a Cargo (Mayores de 25)
$sql6="UPDATE $base_fa.tmp_fa
			SET cod_parentesco='8'
			WHERE cod_parentesco = '3'
			AND TIMESTAMPDIFF(YEAR,fn,CURDATE())>25";
mysql_query($sql6);
//Extranjeros
$sql7="UPDATE $base_fa.tmp_fa fa
		SET fa.fn=CONCAT(IF(fa.estado_civil='1','1980-01-01','2005-01-10'))
		WHERE fa.nd LIKE '9%' 
		AND LENGTH(fa.nd)=8"; 
mysql_query($sql7);

$sql8="UPDATE $base_fa.tmp_fa fa, $base_padron.persona p 
	SET fa.id_domicilio=p.id_domicilio WHERE cuit_titular COLLATE latin1_general_ci =p.cuil
"; 
mysql_query($sql8);

?>
