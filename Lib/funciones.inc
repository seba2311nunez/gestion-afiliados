<?php
function startTransaction()
    {
    mysql_query("START TRANSACTION");
    }
 
function breakTransaction($str = "")
    {
    $msg = "Transaccion abortada debido a un error: ".mysql_error();
    mysql_query("ROLLBACK");
    die("$str $msg");
    }
 
function commitTransaction()
    {
    mysql_query("COMMIT");
    }

function cab_php()
{



}


function hono_y_gas($nom, $pre)
{
	include("Conectar.inc");
	$gas=0;
	$hon=0;
	echo " aca la 19: $pre   $nom </br>";

		$sql= "select * from conv_esp where prestador = " . $pre . " and pracnom='" . $nom . "'";
		$rs = mysql_query ($sql);
		$filas = mysql_num_rows($rs);


		if ($filas > 0)
		{
			$data = mysql_fetch_object($rs);
			$gas = $data->gastos;
			$hon = $data->honorarios;
			mysql_free_result($rs);
		}
		else
		{
		mysql_free_result($rs);

		$sql = "select * from  convenios_normales where prestador ='" . $pre . "' and " . $nom . " between practica_desde and practica_hasta ";
		$rs = mysql_query ($sql);
		$filas = mysql_num_rows($rs);


		if ($filas > 0)
		{

			$data = mysql_fetch_object($rs);
			$sql = "select * from nomenclador where cod='" . $nom . "'";

			$rs2 = mysql_query ($sql);
			$filas2 = mysql_num_rows($rs2);

			if ($filas2 > 0)
				{
				$data2 = mysql_fetch_object($rs2);
				$hon = $data2->honorarios * $data->coef_HonorariosBioq;
				if ($data2->tip == "CL")
				{
					$gas = $data2->gastos * $data->coef_gastoscl;
					$hon = $data2->honorarios * $data->coef_honorariosmed;
				}
				if ($data2->tip  == "RX")
				{
					$gas = $data2->gastos * $data->coef_gastosrx;
					$hon = $data2->honorarios * $data->coef_honorariosmed;
				}

				if ($data2->tip ==  "OG")
				{
					$gas = $data2->gastos * $data->coef_gastosog;
					$hon = $data2->honorarios * $data->coef_honorariosmed;
				}

				if ($data2->tip ==  "BM")
				{
					$gas = $data2->gastos * $data->coef_gastosbm;
					$hon = $data2->honorarios * $data->coef_honorariosmed;
				}
				if ($data2->tip ==  "BB")
				{
					$gas = $data2->gastos * $data->coef_gastosbb;
					$hon = $data2->honorarios * $data->coef_honorariosmed;
				}
				if ($data2->tip ==  "OP")
				{
					$gas = $data2->gastos * $data->coef_gastosop;
					$hon = $data2->honorarios * $data->coef_honorariosmed;
				}

			}
			mysql_free_result($rs);
		}

		}


		return array($hon, $gas);
}



function tablas($can,$tit, $tab, $con, $sql)
{
include ("Conectar.inc");


$tabla = "<table width=100% border=1><tr bgcolor=Silver>";
	for ($i=0; $i < $can; $i++)
	{
		$tabla = $tabla . "<td><font face=verdana size=1>". $tit[$i][0] . "</td>";
	
	}
$tabla = $tabla . "</tr>";



if($sql == "") $sql = "select * from " . $tab . " where " . $con;
$rs = mysql_query ($sql);
$filas = mysql_num_rows($rs);
if ($filas == 0 ) 
	{	
	echo "SE HA PRODUCIDO UN ERROR, LOS PARAMETROS HAN SIDO MAL INGRESADOS";
	exit();
	}

	for($i = 0;$i < $filas;$i++) 
	{ 
		$data = mysql_fetch_object($rs); 
		$tabla =  $tabla . "<tr>"; 
		for ($j=0; $j < $can; $j++)
			{	 	 
				$tabla = $tabla . "<td><font face=verdana size=1>&nbsp;";
				if ($tit[$j][2] != "") $condicion = $tit[$j][2];
				if ($tit[$j][3] != "") $condicion = $condicion . $data->$tit[$tit[$j][3]][1];
				if ($condicion != "") $tabla = $tabla . "<a href=" . $condicion . ">";
				$condicion ="";
				$tabla = $tabla .  $data->$tit[$j][1]; 
				if ($tit[$j][2] != "") $tabla= $tabla . "</a>";
				$tabla = $tabla . "</td>";	
		} 
		$tabla = $tabla . "</tr>";
	}
	$tabla = $tabla . "</table>";

return $tabla;
}


function cab_html($tit, $for)
{
$cab = "<HTML>
<BODY>
<form action=" . $for . " id=form1 name=form1>
<br>
<center>
<font face=Tahoma size=2 color=DarkBlue><b>" . $tit . "</b></font>
<br><br>";

return $cab;
}


function valida_usuario($ip, $pag,$usu)
{
	include "Conectar.inc";
  $sql = "insert into  seguridad (ip, pagina, hora, usuario) values ('$ip','$pag','" . Date("Y-m-d h:i") . "','$usu')";
  $rs = mysql_query ($sql);
 // mysql_free_result($rs);


  $sql = "select * from seguridad  where ip= '$ip' and pagina='dolog' order by hora desc ";
  $rs = mysql_query ($sql);
  $data = mysql_fetch_object($rs);
  $filas = mysql_num_rows($rs);

        if ($filas == 0)
        {
                echo "No podras ingresar al sistema...";
                exit();
        }
  mysql_free_result($rs);
  $hor=left($data->hora,16);

  $sql = "delete from horas";
  $rs = mysql_query ($sql);
 // mysql_free_result($rs);

  $sql = "insert into horas (fec1,fec2, ip) values ('" . Date("Y-m-d h:i") . "', '$hor','$ip')";

  $rs = mysql_query ($sql);
  //mysql_free_result($rs);

  $sql = "select fec1-fec2 as fe from horas where fec1-fec2 < '01:15' and ip='$ip'";
  $rs = mysql_query ($sql);
  $filas = mysql_num_rows($rs);
  $data = mysql_fetch_object($rs);

        if ($filas == 0)
        {
                echo "No podras ingresar al sistema";
                exit();
        }

mysql_free_result($rs);
}

function diffechas($a, $b)
{
	$date1=strtotime($a);
	$date2=strtotime($b);
	$dife= (($date2-$date1)/86400);
	return $dife + 1;
}



function right($a, $b)
{
	$der = substr ($a,strlen($a) - $b,$b);
	return $der;
}


function left($a, $b)
{
	$izq = substr ($a,0,$b);

	return $izq;
}



function t_fec($a)
{
	
	 $fecha = split ("/",$a);
         $b = $fecha[1] . "-" . $fecha[0] . "-" . $fecha[2];

return $b;
}



function t_fec1($a)
{
	
	 $fecha = split ("-",$a);
         $b = $fecha[2] . "/" . $fecha[1] . "/" . $fecha[0];

return $b;
}

function t_fec2($a)
{
	
	 $fecha = split ("/",$a);
         $b = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0];

return $b;
}




function bus_vade($com, $tro, $nom, $lab, $act)
{
	if ($com != "") 
	{
		$donde= "where monodroga like '" . $com.  "%' "; 
		$a=1;	
	}

	if ($tro != "") 
	{
		if ($a == 1) $donde= $donde . " and ";
		else $donde = " where ";

		$donde=$donde . " troquel like '" . $tro . "%' "; 
		$a = 1;
	}



	if ($nom != "")
	{
		if ($a == 1) $donde= $donde . " and ";
		else $donde = " where ";

		$donde= $donde .  " medicamento like '" . $nom . "%' ";
		$a=1;
	}


	if ($lab != "0")
	{
		if ($a == 1) $donde= $donde . " and ";
		else $donde = " where ";

		$donde= $donde . " laboratorio = '" . $lab . "' ";
		$a = 1;
	}

	if ($act != "0")
	{
		if ($a == 1) $donde= $donde . " and ";
		else $donde = " where ";

		$donde= $donde . "accionterapeutica = '" . $act . "' ";
	}


	$sql="select * FROM vademecum " . $donde ;
	return $sql;

}

function bus_afi($nom, $afi, $pro)
{
$a=0;
	if ($nom != "")
	{
		$donde= " where apellido like '" . $nom.  "%' ";
		$a=1;
	}

	if ($afi != "")
	{
		$donde=" where numafi like '" . $afi . "%' ";
		$a=1;
	}

	if ($afi != "" && $nom != "")
	{
		$donde=" where numafi like '" . $afi . "%' and apellido like '". $nom . "%' ";
		$a=1;
	}

	if($pro != 0)
	{
	if($a==1) $donde=$donde . " and "; else
	$donde =$donde . " where ";
        $donde= $donde . " provincia = '" . $pro . "'";
	}


	$sql="select * FROM vempleados " . $donde . "";
	return $sql;
}

function val_int($a)
{
/*
LA FUNCION EN PLPGSQL, ESTA HECHA PERO NO ANDA, CUANDO ANDE LO UNICO QUE TENGO Q HACER ES LLAMAR A LA FUNCION
DESDE EL OTRO PROGRAMA Y LISTO, MIENTRAS TANTO....
*/

include ("Conectar.inc");

	$err=0;
	$sql = "SELECT * FROM empleados where numafi = '" . $a . "' and estado <>'BAJ'";
	$rs = mysql_query ($sql);
	$filas = mysql_num_rows($rs);

	if ($filas == 0)
	{
		$err=1;

	}
	mysql_free_result($rs);

	$sql = "SELECT * FROM ingreso where numafi='" . $a . "' and estado='NEW'";
	$rs = mysql_query ($sql);
	$filas = mysql_num_rows($rs);

	if ($filas != 0)
	{
		$err = 2;	
	}
	mysql_free_result($rs);  
	return $err;	
	mysql_close($conexion);

}

function autonumerico($tabla, $campo, $donde)
{
  include("Conectar.inc");
  if ($donde != "") $donde = " where " . $donde; 

  $sql = "select max(" . $campo . ") from " . $tabla . " " . $donde . "";
  $rs = mysql_query ($sql);
  $filas = mysql_num_rows($rs);
  $data= mysql_fetch_object($rs);
  if ($filas == 0 ) $id = 1; else
  $id = $data->max + 1; 
  mysql_free_result($rs);
  return $id;
  	mysql_close($conexion);
}

function dev_egr($num, $par)
{
	include ("Conectar.inc");
	$sql="SELECT * FROM smcodegrdig" . $num . " where dig ='" . $par . "'";
	$rs1 = mysql_query ($sql);
	$fila1 = mysql_num_rows($rs1);
	$data1 = mysql_fetch_object($rs1);
	return $data1->nom;
	mysql_free_result($rs1);
	mysql_close($conexion);	
}

function meses($numero)
{


	if ($numero == 1 ) $nom = "Enero";
	if ($numero == 2 ) $nom = "Febrero";
	if ($numero == 3 ) $nom = "Marzo";
	if ($numero == 4 ) $nom = "Abril";
	if ($numero == 5 ) $nom = "Mayo";
	if ($numero == 6 ) $nom = "Junio";
	if ($numero == 7 ) $nom = "Julio";
	if ($numero == 8 ) $nom = "Agosto";
	if ($numero == 9 ) $nom = "Septiembre";
	if ($numero == 10 ) $nom = "Octubre";
	if ($numero == 11 ) $nom = "Noviembre";
	if ($numero == 12 ) $nom = "Diciembre";

return $nom;


}

function autorizar($afiliado, $practica, $prestador){
	include "../Conectar.inc";
	$rta="AUTORIZADO";
	$plan=mysql_result(mysql_query("select pla from empleados where numafi='$afiliado'"), 'pla');
	//CONTROLO SI LA PRACTICA ESTA EXCLUIDA POR EL PLAN
	$sql="select * from exclusiones_x_plan where plan='$plan' and '$practica' between practica_desde and practica_hasta";
	$rs=mysql_query($sql);
	$filas=mysql_num_rows($rs);
	if($filas>0){
		$rta="Practica Excluida del plan del afiliado";
		}
	return $rta;
}

function color_estado($estado){
	switch ($estado) {
		case 'ALTA':
			$result="bgcolor='#81F781'";
			break;
		case 'PENDIENTE':
			$result="bgcolor='yellow'";
			break;
		case 'BAJA':
			$result="bgcolor='red'";
			break;
		case 'EN TRANSITO':
			$result="bgcolor='orange'";
			break;
		default:
			$result="";
			break;
	}
	return $result;
}
?>
