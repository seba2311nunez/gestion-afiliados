<?php
//header('Access-Control-Allow-Origin: *');
require_once '../Config/database.inc.php';
$conexion=database_private_connect('smadm');
   if (!$conexion) {
        echo "<CENTER>
              Problemas de conexion con la base de datos - RDS
              </CENTER>";
        exit();
   }
	
foreach($_REQUEST as $var=>$value){
 $_GLOABLS[$var] = $value;
 $$var = $value;
}
   
mysql_query("SET NAMES 'utf8'");
header("Content-Type: text/html;charset=utf-8");

$parametro = $_GET['parametro'];

switch ($parametro) {
	
	case 'liquidacion':
		
		$sql="SELECT CONVERT(proceso, CHAR(50)) proceso,convenio,SUM(aporte) AS aporte,SUM(contri) AS contri,SUM(aporte+contri) AS total  
				FROM ddd.liquidacion 
				WHERE proceso BETWEEN 201706 AND 201707
				GROUP BY proceso,convenio
				ORDER BY proceso DESC ";
				
		$sql="SELECT periodo,convenio,SUM(aporte) AS aporte,SUM(contri) AS contri,SUM(aporte+contri) AS total 
				FROM ddd.liquidacion 
				WHERE periodo BETWEEN '1706' AND '1707' AND convenio!=''
				GROUP BY periodo,convenio
				ORDER BY periodo DESC";
				
		$result = mysql_query($sql) or die(mysql_error());
		
		$json = array();
		while ($row = mysql_fetch_assoc($result)) {
		    $json[] = array(
		        		'periodo' => $row['periodo'],
		        		'convenio' => $row['convenio'],		        		
		        		'aporte' => $row['aporte'],
		        		'contri' => $row['contri'],
		        		'total' => $row['total']           
		      );
		}
		
		echo json_encode($json);
		
		break;	
		
		default:
		
			break;
}

?>
