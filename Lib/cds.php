<?

		include("Conectar.inc");

		include ('fpdf.php');
		$pdf = & new FPDF();
		$pdf->AddPage();

		$pdf->SetFont('Helvetica','',14);
		$pdf->Write(5, 'kashe rossi kashe');
		$pdf->Ln();

		$pdf->SetFontSize(10);
		$pdf->Write(5, '© 2002/2003 Kai Seidler, oswald@apachefriends.org, GPL');
		$pdf->Ln();

		$pdf->Ln(5);

		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(40,7,'interpret',1);
		$pdf->Cell(80,7,'titel',1);
		$pdf->Cell(40,7,'jahr',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$result=mysql_query("SELECT numafi,nombre,hc FROM afiliados where nombre is not null ORDER BY nombre limit 100;");

		while( $row=mysql_fetch_array($result) )
		{
			$pdf->Cell(40,7,$row['numafi'],1);
			$pdf->Cell(80,7,$row['nombre'],1);
			$pdf->Cell(40,7,$row['hc'],1);
			$pdf->Ln();
		}

		$pdf->Output();
		exit;
	
?>

