<?php
include("../../Config/Conectar.inc");
include("../../Config/funciones.inc");
include("../../Lib/fpdf.php");
if(!$_SESSION['id_user']){
	
	echo "<h3>Su sesion caduco, por favor vuelva a loguearse.</h3> "; exit();
}

$id_usuario = $_SESSION['id_user']; 
$usuario = $_SESSION['usu']; 

switch ($sl) {
	
	case 345:
		
		$pdf = & new FPDF('L','mm','A4');

		$ancho=58;
		$alto=41;
		$espacio=12;
		$margen_izq=4;
		
		$insert_lote = "INSERT INTO $base_historicos.lotes(lote,archivo,proceso,usuario,id_usuario)
						VALUES ('$fv','$cuit','impresion_carnets','$usuario',$id_usuario)";
						
		mysql_query($insert_lote) or die(mysql_error().$insert_lote);
		
		$id_lote = mysql_insert_id();
		
		$query = "INSERT INTO $base_historicos.`credenciales_emitidas`(id_lote,id_afiliado,fecha_vencimiento,id_usuario)
						SELECT $id_lote,id_afiliado,fecha,$id_usuario
						FROM $base_historicos.tmp_credenciales
						WHERE estado='ALTA' 
						ORDER BY d,nb";
						
		mysql_query($query) or die(mysql_error().$query);
		
		mysql_query("UPDATE $base_historicos.lotes l
						SET l.`cant_registros`=( SELECT COUNT(*) FROM $base_historicos.`credenciales_emitidas` WHERE id_lote = $id_lote )
						WHERE l.id = $id_lote");
		
		$sql = "SELECT ayn,nd,fn,nb,DATE_FORMAT(fecha,'%d/%m%/%Y') AS fec_ven_formateada 
					FROM $base_historicos.tmp_credenciales
					WHERE estado='ALTA'
					ORDER BY d,nb ";
		
		$rs = mysql_query($sql) or die(mysql_error().$sql);
		
		while ($d = mysql_fetch_object($rs)) {
			# code...
			$pdf->AddPage();
			$pdf->SetMargins(5,10,5);
		
			fila_v2($pdf ,$d, $margen_izq, $ancho, $espacio);
		}
		break;
	case 678:
			
			$pdf = & new FPDF('L','mm','Legal');

			$ancho=58;
			$alto=41;
			$espacio=12;
			$margen_izq=4;
			
			$sql = "
				SELECT 
				  CONCAT(p.apellido, ' ', p.nombre) AS ayn,
				  p.nd,
				  DATE_FORMAT(p.fn, '%d/%m/%Y') AS fn,
				  CONCAT(COALESCE(f.cod_filial,''), ' / ', p.nd) AS nb,
				  DATE_FORMAT('$fv', '%d/%m%/%Y') AS fec_ven_formateada,
				  a.id AS id_afiliado,
				  CASE
				  	WHEN a.id_tipo_aporte IN (4,7)  THEN 'MONOTRIBUTISTA'
				  	WHEN a.id_tipo_aporte = 5 THEN 'SERVICIO DOMESTICO'
				  	WHEN a.id_tipo_aporte IN (2,10,11) THEN 'JUBILADO'
				  	WHEN a.id_tipo_aporte IN (8,9) THEN 'FONDO DE DESEMPLEO'
				    WHEN sub.nombre IN ('de ddjj', 'de aporte') 
				    THEN CONCAT(sub.cuit, ' - Sin Dato') 
				    ELSE sub.nombre 
				  END AS empresa
				FROM $base_padron.persona p 
				JOIN $base_padron.afiliados a ON p.id = a.id_persona 
			  LEFT JOIN $base_padron.`filial` f ON f.id=a.filial
			  LEFT JOIN (
						SELECT MAX(b) AS ma,round(e/100) as rem,MIN(b) AS mi,c AS cuit,e.`nombre`,d AS cuil 
				    FROM ".N_BASE_HISTORICOS.".`declaraciones_juradas` d 
						JOIN $base.`empresas` e ON e.`cuit` = d.`c` COLLATE latin1_general_ci 
				    WHERE d.d = '$cuil' 
				    GROUP BY c ORDER BY 1 DESC,2 DESC LIMIT 1
					) sub ON sub.cuil = p.cuil 
				WHERE p.cuil IN ('$cuil')
			";

			//echo $sql;exit();
			$rs = mysql_query($sql) or die($sql);
			
			while ($d = mysql_fetch_object($rs)) {
				//print_r($d);exit();
				//Genero un registro historico
				$query = "INSERT INTO $base_historicos.`credenciales_emitidas`(id_lote,id_afiliado,fecha_vencimiento,id_usuario)
							VALUES (0,$d->id_afiliado,'$fv',$id_usuario)";
				mysql_query($query) or die(mysql_error().$query);
							
				//fila_v2($pdf ,$d, $margen_izq, $ancho, $espacio);

				imprimir_cara($pdf ,$d, $margen_izq, $ancho, $espacio);
				imprimir_contra($pdf ,$d->id_afiliado, $fv, $ancho, $espacio,$base_padron);
			}
			
		break;
	case 679:
			
		$pdf = & new FPDF('L','mm','Legal');

		$ancho=58;
		$alto=41;
		$espacio=12;
		$margen_izq=4;
		$sql = "
			SELECT 
			  CONCAT(p.apellido, ' ', p.nombre) AS ayn,
			  p.nd,
			  DATE_FORMAT(p.fn, '%d/%m/%Y') AS fn,
			  CONCAT(COALESCE(f.cod_filial,''), ' / ', p.nd) AS nb,
			  DATE_FORMAT('2024-06-30', '%d/%m%/%Y') AS fec_ven_formateada,
			  a.id AS id_afiliado,
			  CASE
			    WHEN e.nombre IN ('de ddjj', 'de aporte') 
			    THEN CONCAT(e.cuit, ' - Sin Dato') 
			    ELSE e.nombre 
			  END AS empresa
			FROM $base_padron.persona p 
			  JOIN $base_padron.afiliados a ON p.id = a.id_persona 
			  LEFT JOIN $base_padron.`filial` f ON f.id=a.filial
			  JOIN $base_dev.`ddjj_final` sub ON sub.c = '$cuit' AND sub.b='$periodo' AND sub.d = p.cuil 
			  JOIN $base.`empresas` e ON e.`cuit` = sub.`c` COLLATE latin1_general_ci 
		";

		//echo $sql;exit();
		$rs = mysql_query($sql) or die($sql);
			
		while ($d = mysql_fetch_object($rs)) {
			imprimir_cara($pdf ,$d, $margen_izq, $ancho, $espacio);
			imprimir_contra($pdf ,$d->id_afiliado, $fv, $ancho, $espacio,$base_padron);
		}
			
		break;
	case 'filiales':

		$pdf = & new FPDF('L','mm','Legal');

		$ancho=58;
		$alto=41;
		$espacio=12;
		$margen_izq=4;


		$sql = "CALL $base_padron.genera_credenciales_filial('$cuit','$fv','$filial')";
		mysql_query($sql) or die(mysql_error()."<br>".$sql);

		$sql="SELECT * FROM $base_padron.tmp_afiliados_carnets_cuit";
		$rs = mysql_query($sql) or die(mysql_error()."<br>".$sql);

		while ($d = mysql_fetch_object($rs)) {
    	$id_afiliado = $d->id_afiliado;

			$query = "INSERT INTO $base_historicos.`credenciales_emitidas`(id_lote,id_afiliado,fecha_vencimiento,id_usuario)
							VALUES (0,$id_afiliado,'$fv',$id_usuario)";
			mysql_query($query) or die(mysql_error().$query);
			imprimir_cara($pdf ,$d, $margen_izq, $ancho, $espacio);
			imprimir_contra($pdf ,$id_afiliado, $fv, $ancho, $espacio,$base_padron);
		}

		/*
    $storedResults = array();
    while ($row = mysql_fetch_assoc($rs)) {
        $storedResults[] = $row;
    }
		mysql_free_result($rs);

    foreach ($storedResults as $d) {

    	$id_afiliado = $d['id_afiliado'];

			$query = "INSERT INTO $base_historicos.`credenciales_emitidas`(id_lote,id_afiliado,fecha_vencimiento,id_usuario)
							VALUES (0,$id_afiliado,'$fv',$id_usuario)";
			mysql_query($query) or die(mysql_error().$query);
			imprimir_cara($pdf ,$d, $margen_izq, $ancho, $espacio);
			imprimir_contra($pdf ,$id_afiliado, $fv, $ancho, $espacio,$base_padron);
    }
		*/	
		break;
	default:
		break;		
}
$pdf->Output();
exit();

function imprimir_cara($pdf, $rs,$fv, $ancho, $espacio){
	$letra_size_titulos = 7 ;
	$letra_size_info = 9 ;
	$margen_izq = 28;

	$pdf->SetMargins(8,10,8);
	$pdf->AddPage();
	
	$pdf->Cell($margen_izq,4,'',0);	
	$pdf->Ln(5);
	$pdf->SetFont('Helvetica','B',$letra_size_titulos);
	$pdf->Cell($margen_izq,4,'',0);

	$pdf->SetFont('Helvetica','B',$letra_size_info);
	$pdf->Cell($margen_izq,4,utf8_decode($rs->ayn),0,'','');	
	$pdf->Ln(5);

	$pdf->SetFont('Helvetica','B',$letra_size_titulos);
	$pdf->Cell($margen_izq,4,'',0);
	$pdf->Cell($margen_izq,4,'Empresa:',0,'','');
	$pdf->Ln();
	$pdf->SetFont('Helvetica','B',$letra_size_info);
	$pdf->Cell($margen_izq,4,'',0);
	if($_REQUEST['empresa']){
		$pdf->Cell($margen_izq,4,utf8_decode(urldecode($_REQUEST['empresa'])),0,'','');	
	}else{
		$pdf->Cell($margen_izq,4,utf8_decode($rs->empresa),0,'','');	
	}
	$pdf->Ln(5);

	$pdf->SetFont('Helvetica','B',$letra_size_titulos);
	$pdf->Cell($margen_izq,4,'',0);
	$pdf->Cell($margen_izq,4,'Documento:',0,'','');
	$pdf->Ln();
	$pdf->SetFont('Helvetica','B',$letra_size_info);
	$pdf->Cell($margen_izq,4,'',0);
	$pdf->Cell($margen_izq,4,utf8_decode($rs->nd),0,'','');	
	$pdf->Ln(5);

	
	$pdf->SetFont('Helvetica','B',$letra_size_titulos);
	$pdf->Cell($margen_izq,4,'',0);
	$pdf->Cell($margen_izq,4,'Afiliado:',0,'','');
	$pdf->Cell($margen_izq+$margen_izq-5,4,'Vto:',0,'','');
	$pdf->Ln();
	$pdf->SetFont('Helvetica','B',$letra_size_info);
	$pdf->Cell($margen_izq,4,'',0);
	$pdf->Cell($margen_izq,4,utf8_decode($rs->nb),0,'','');
	$timestamp = strtotime($_GET['fv']);
	$new_date = date("d/m/Y", $timestamp);	
	$pdf->Cell($margen_izq+$margen_izq-5,4,$new_date,0,'','');

}
function imprimir_contra($pdf, $id_afiliado, $margen_izq, $ancho, $espacio,$base_padron){
	$letra_size = 7;
	$margen_izq = 0;

	$pdf->SetMargins(2,2,2);
	$pdf->AddPage();
	$pdf->SetFont('Helvetica','B',$letra_size);

	$sql="
		SELECT 
			af.gpar * 1 as gpar,
			CONCAT(pe.apellido,', ',pe.nombre) AS ayn_persona,
			pe.cuil,pe.nd,
			DATE_FORMAT(pe.fn,'%d/%m/%Y') AS fn,
			TIMESTAMPDIFF(YEAR,pe.fn,CURDATE()) AS edad
		FROM afiliados af
		JOIN persona pe ON af.id_persona=pe.id
		JOIN parentesco pa ON af.id_parentesco=pa.id
		WHERE id_titular=$id_afiliado AND estado_afiliado_nuevo_test(af.id,curdate()) NOT LIKE 'BAJA%' 
		ORDER BY pa.id ASC
	";
	$rst = mysql_query($sql) or die(mysql_error()."<br>".$sql);

	$counter = 0;
	$pdf->Ln();
	while($d = mysql_fetch_object($rst)){
		if($d->id_titular!="0"){
			$counter++;
			//$pdf->Ln(-5);
			//$cell=$counter;
			$cell=$d->gpar;
			$pdf->Cell($pdf->GetStringWidth($cell),2,$cell,2,'L');
			$cell=' '.utf8_decode($d->ayn_persona);
			$pdf->Cell(52,2,$cell,0,'L');
			$cell=utf8_decode($d->nd);
			//pdf->Cell($pdf->GetStringWidth($cell)+16,15,'',0);
			$pdf->Cell($pdf->GetStringWidth($cell),2,$cell,0,'L');
			$cell='  '.utf8_decode($d->fn);	
			$pdf->Cell($pdf->GetStringWidth($cell),2,$cell,0,'L');
			$pdf->Ln(3);
		}
	}





}

?>

