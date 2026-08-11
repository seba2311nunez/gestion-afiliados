<?php 
require(__DIR__."/../../../Conectar.inc");

switch ($parametro) {

	case 'desempleo_detalle_xls':
		$filename = "detalle_desempleo__".$periodo."_.xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);
		$table="<h4>detalle_desempleo__".$periodo."</h4>";
		$table.="<table border=1>
					<thead>
						<tr>
							<th>CUIL familiar</th>
							<th>CUIL Titular</th>
							<th>Afiliado</th>
							<th>Fecha vigencia</th>
							<!--
							<th>Fec inicio rel</th>
							<th>Fecha cese</th>
							-->
							<th>Edad</th>
							<!--
							<th>Fecha proceso</th>
							-->
							<th>Sexo</th>
							<th>Convenio</th>
							<th>Importe</th>									
						</tr>
					</thead>
					<tbody>";
		
				
					$result_detalle = mysql_query("SELECT d.cuil,d.cuil_titular,d.ayn,d.fec_vigencia,fec_inicio_rel,d.fec_cese,
															edad,fproceso,d.sexo,d.convenio,dd.importe
														FROM $base_padron.tmp_desempleo d
														JOIN $base_padron.desempleo_distribucion dd ON edad BETWEEN dd.e_desde 
																							AND dd.e_hasta AND d.sexo=dd.sexo 
																							AND d.fproceso BETWEEN dd.desde AND dd.hasta
														ORDER BY convenio,cuil_titular,cuil") 
											or die(mysql_error()."<br> Error ejecutando consulta detalle");
														
					while($d_detalle = mysql_fetch_object($result_detalle)){
						
						$importe = number_format($d_detalle->importe,2,",","");

						$table.= "<tr>
									<td>$d_detalle->cuil</td>
									<td>$d_detalle->cuil_titular</td>
									<td>$d_detalle->ayn</td>
									<td>$d_detalle->fec_vigencia</td>																		
									<td style='text-align: right;'>$d_detalle->edad</td>									
									<td>$d_detalle->sexo</td>
									<td>$d_detalle->convenio</td>
									<td>$importe</td>												
								  </tr>";
							  
						$total+=$d->total ;
					}
					
			$table.="</tbody>
				</table>";
			
			echo $table;

		break;
	
	default:
		# code...
		break;
}

?>