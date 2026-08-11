<?php
/* Declaro como indice del array los distintos identificadores de seccion que ya habia declarado como valor en la configuracion del archivo JS.
Como valores declaro otro array que contiene el nombre de la tabla donde tiene que buscar los datos de la seccion (por ejemplo tabs_tabla_1), el nombre de la columna
que actua como ID, el valor que debe contener ese ID y tambien la columna en donde esta el string de la seccion.
Para mas claridad por favor revisen la estructura de las tablas adjuntas en el .sql. */

$seccion=$_POST['seccion'];
//$para=$_POST['para1'];


if($seccion=='seccion1'){

header ("Location: ../lab_rophe/hola.php") ;

}

if($seccion=='seccion2'){
echo "Estamos en el area 2";
}

if($seccion=='seccion3'){
echo "Estamos en el area 3 " ;
}




/*
$secciones=array(
'seccion1'=>array('tabs_tabla_1', 'id', '1', 'datos'),
'seccion2'=>array('tabs_tabla_1', 'id', '2', 'datos'),
'seccion3'=>array('tabs_tabla_2', 'id', '2', 'datos')
);


$consulta=$resultado=NULL;
if(isset($secciones[$seccion]))
{
	include 'conexion.php';
	conectar();
	
	$consulta=mysql_query("SELECT {$secciones[$seccion][3]} FROM {$secciones[$seccion][0]} WHERE {$secciones[$seccion][1]}={$secciones[$seccion][2]}") or
	die (mysql_error());
	
	desconectar();
	$resultado=mysql_fetch_array($consulta);
	echo utf8_encode($resultado[0]);
}*/

?>