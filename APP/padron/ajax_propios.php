<?php 	
require("../../Config/Conectar.inc");
switch ($parametro) {
	case 'consulta1':
		$json = array();
		$sql="SELECT IF(fi.nombre IS NULL,'DESCONOCIDA',fi.nombre) AS Seccional,
					ct.c_count AS Titulares,
					cf.c_count AS Familiares,
					COUNT(*) AS Total
				FROM $base_padron.afiliados af 
				LEFT JOIN $base_padron.`filial` fi ON af.filial=fi.`id`
				LEFT OUTER JOIN (
						SELECT af.filial,
							COUNT(*) AS c_count 
						FROM $base_padron.afiliados af
						WHERE id_desreguladora=1 AND id_titular=0 
						GROUP BY af.filial
						
					) ct ON (COALESCE(ct.filial, '') = COALESCE(af.filial, '')) -- and ct.filial=af.filial
				LEFT OUTER JOIN (
						SELECT af.filial,
							COUNT(*) AS c_count 
						FROM $base_padron.afiliados af 
						WHERE id_desreguladora=1 AND id_titular!=0 
						GROUP BY af.filial
					) cf ON (COALESCE(cf.filial, '') = COALESCE(af.filial, ''))
				WHERE af.id_desreguladora=1
				GROUP BY af.`filial` ";

		$rs = mysql_query($sql) or die (mysql_error()."<br>".$sql);

		while ($row = mysql_fetch_assoc($rs)){
			
			$json[] = array(
				'seccional' => $row['Seccional'],
				'titulares' => $row['Titulares'],
				'familiares' => $row['Familiares'],
				'total' => $row['Total']
			);
		}

		echo json_encode($json);
		break;
	
	case 'consulta2':
		$json = array();

		mysql_query("DROP TEMPORARY TABLE IF EXISTS $base_padron.tmp_hijos_21_25 ");

		mysql_query("CREATE TEMPORARY TABLE $base_padron.tmp_hijos_21_25
						SELECT CONCAT(aft.nben,'/',aft.gpar) AS nben_tit, 
								CONCAT(af.nben,'/',af.gpar) AS nben_fam, 
								pt.cuil AS cuil_titular,CONCAT(p.apellido,', ',p.nombre) AS ayn,p.nd,
								p.cuil,p.sexo,p.fn,TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) AS edad,
								pa.parentesco,
								'estado@0000-00-00' AS estado,
								af.id as id_afiliado
						FROM $base_padron.`persona` p
						JOIN $base_padron.afiliados af ON af.id_persona=p.id
						JOIN $base_padron.afiliados aft ON aft.id=af.id_titular 
						JOIN $base_padron.persona pt ON pt.id=aft.id_persona
						JOIN $base_padron.`parentesco` pa ON pa.id=af.id_parentesco
						WHERE af.id_desreguladora=1 
							AND af.id_titular!=0 
							AND TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) BETWEEN 21 AND 25 
							AND af.`id_parentesco` IN (4,6,8)");

		mysql_query("UPDATE $base_padron.tmp_hijos_21_25
						SET estado=$base_padron.`estado_afiliado_nuevo_test`(id_afiliado,CURDATE())");

		$sql="SELECT * FROM $base_padron.tmp_hijos_21_25 WHERE estado LIKE 'ALTA%'";

		$rs = mysql_query($sql) or die (mysql_error()."<br>".$sql);

		switch ($param_salida) {
			case 'xls':
				// code...

				$filename = "hijos_21_a_25_mas_familiares_a_cargo_".date('Ymd') .".xls";
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=".$filename);

				$table = "<h3>Hijos entre 21 a 25 año mas familiares a cargo al ".date('d/m/Y') ."</h3>
						<table border=1>
							<tr>
								<th>Nben tit</th> 
								<th>Nben fam</th> 
								<th>CUIL tit</th> 
								<th>Ayn</th> 
								<th>Documento</th> 
								<th>CUIL</th> 
								<th>Sexo</th> 
								<th>Fecha nacimiento</th> 
								<th>Edad</th> 
								<th>Parentesco</th> 
							</tr>
						";

				while($d=mysql_fetch_object($rs)){

					$table.="<tr>
								<td>$d->nben_tit</td>
								<td>$d->nben_fam</td>
								<td>$d->cuil_titular</td>
								<td>$d->ayn</td>
								<td>$d->nd</td>
								<td>$d->cuil</td>
								<td>$d->sexo</td>
								<td>$d->fn</td>
								<td>$d->edad</td>
								<td>$d->parentesco</td>
							</tr>";
				}

				$table.="</table>";

				echo $table;

				break;
			
			default:
				// code...
				while ($row = mysql_fetch_assoc($rs)){
			
			
					$json[] = array(
						'cuil_titular' => $row['cuil_titular'],
						'nben_tit' => $row['nben_tit'],
						'nben_fam' => $row['nben_fam'],
						'ayn' => $row['ayn'],
						'cuil' => $row['cuil'],
						'edad' => $row['edad']
					);
					
				}

				echo json_encode($json);
				break;
		}

		

		
		break;

	case 'consulta3':

		//echo "string";

		
		$json = array();

		mysql_query("DROP TEMPORARY TABLE IF EXISTS $base_padron.tmp_mayores_de_25");

		
		mysql_query("CREATE TEMPORARY TABLE $base_padron.tmp_mayores_de_25
						SELECT CONCAT(aft.nben,'/',aft.gpar) AS nben_tit, 
								CONCAT(af.nben,'/',af.gpar) AS nben_fam, 
								pt.cuil AS cuil_titular,
								CONCAT(p.apellido,', ',p.nombre) AS ayn,
								p.nd,p.cuil,p.sexo,p.fn,
								TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) AS edad,
								pa.parentesco,
								'estado@0000-00-00' AS estado,
								af.id as id_afiliado
						FROM $base_padron.`persona` p
						JOIN $base_padron.afiliados af ON af.id_persona=p.id
						JOIN $base_padron.afiliados aft ON aft.id=af.id_titular 
						JOIN $base_padron.persona pt ON pt.id=aft.id_persona
						JOIN $base_padron.`parentesco` pa ON pa.id=af.id_parentesco
						WHERE af.id_desreguladora=1 
							AND af.id_titular!=0 
							AND TIMESTAMPDIFF(YEAR,p.fn,CURDATE()) > 25 
							AND af.`id_parentesco` IN (4,6,8) ")  or die(mysql_error());
		

		
		mysql_query("UPDATE $base_padron.tmp_mayores_de_25
						SET estado=$base_padron.estado_afiliado_nuevo_test(id_afiliado,CURDATE())") or die(mysql_error());
		

		
		$sql="SELECT *
				FROM $base_padron.tmp_mayores_de_25
				WHERE estado LIKE 'ALTA%'";

		$rs = mysql_query($sql) or die (mysql_error()."<br>".$sql);

		
		switch ($param_salida) {
			case 'xls':
				// code...

				$filename = "familiares_mayores_de_25_".date('Ymd').".xls";
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment; filename=".$filename);

				$table = "<h3>Hijos mayores de 25 años mas familiares a cargo al ".date('d/m/Y')."</h3>
						<table border=1>
							<tr>
								<th>Nben tit</th> 
								<th>Nben fam</th> 
								<th>CUIL tit</th> 
								<th>Ayn</th> 
								<th>Documento</th> 
								<th>CUIL</th> 
								<th>Sexo</th> 
								<th>Fecha nacimiento</th> 								
								<th>Parentesco</th> 
							</tr>
						";

				while($d=mysql_fetch_object($rs)){

					$table.="<tr>
								<td>$d->nben_tit</td>
								<td>$d->nben_fam</td>
								<td>$d->cuil_titular</td>
								<td>$d->ayn</td>
								<td>$d->nd</td>
								<td>$d->cuil</td>
								<td>$d->sexo</td>
								<td>$d->fn</td>
								
								<td>$d->parentesco</td>
							</tr>";
				}

				$table.="</table>";

				echo $table;

				break;
			
			default:
				// code...
					while ($row = mysql_fetch_assoc($rs)){
				
						$json[] = array(
							'cuil_titular' => $row['cuil_titular'],
							'nben_tit' => $row['nben_tit'],
							'nben_fam' => $row['nben_fam'],
							'ayn' => $row['ayn'],
							'cuil' => $row['cuil'],
							'parentesco' => $row['parentesco'],
							'edad' => $row['edad']
						);
					}

					echo json_encode($json);

				break;
		}
		
		
		
		break;
	
}

?>