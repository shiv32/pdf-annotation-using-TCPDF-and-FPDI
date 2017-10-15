
<?php

//ob_start();
// just require TCPDF instead of FPDF
//require_once('tcpdf.php');
//require_once('fpdi.php');
require_once('tcpdf.php');

//require_once('FPDI/src/autoload.php');
require_once('fpdi/fpdi.php');

$scale = 0.235185333;
echo $x1 = $_POST['X1']*$scale; echo ',';
echo $y1 = $_POST['Y1']*$scale; echo ',';
 $x2 = $_POST['X2']*$scale; 
echo $x2 = $x2 - $x1; echo ',';

 $y2 = $_POST['Y2']*$scale;
 echo $y2 = $y2 -$y1; echo ',';

echo $pageNo = $_POST['pageNo'];

//echo $x1 = $_POST['X1']; echo ',';
//echo $y1 = $_POST['Y1']; echo ',';
 //$x2 = $_POST['X2']; 
//echo $x2 = $x2 - $x1; echo ',';

 //$y2 = $_POST['Y2'];
 //echo $y2 = $y2 -$y1;

class AlphaPDF extends FPDI
{
	var $extgstates = array();

	// alpha: real value from 0 (transparent) to 1 (opaque)
	// bm:    blend mode, one of the following:
	//          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
	//          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
	function SetAlpha($alpha, $bm='Normal')
	{
		// set alpha for stroking (CA) and non-stroking (ca) operations
		$gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
		$this->SetExtGState($gs);
	}

	function AddExtGState($parms)
	{
		$n = count($this->extgstates)+1;
		$this->extgstates[$n]['parms'] = $parms;
		return $n;
	}

	function SetExtGState($gs)
	{
		$this->_out(sprintf('/GS%d gs', $gs));
	}

	function _enddoc()
	{
		if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
			$this->PDFVersion='1.4';
		parent::_enddoc();
	}

	function _putextgstates()
	{
		for ($i = 1; $i <= count($this->extgstates); $i++)
		{
			$this->_newobj();
			$this->extgstates[$i]['n'] = $this->n;
			$this->_out('<</Type /ExtGState');
			$parms = $this->extgstates[$i]['parms'];
			$this->_out(sprintf('/ca %.3F', $parms['ca']));
			$this->_out(sprintf('/CA %.3F', $parms['CA']));
			$this->_out('/BM '.$parms['BM']);
			$this->_out('>>');
			$this->_out('endobj');
		}
	}

	function _putresourcedict()
	{
		parent::_putresourcedict();
		$this->_out('/ExtGState <<');
		foreach($this->extgstates as $k=>$extgstate)
			$this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
		$this->_out('>>');
	}

	function _putresources()
	{
		$this->_putextgstates();
		parent::_putresources();
	}
}



function generatePDF($source, $output, $text, $image,$x1,$y1,$x2,$y2,$pageNo){

$pdf = new AlphaPDF('Portrait','mm','A4');

//$pdf->AddPage();

//Set the source PDF file
 
$pages_count = $pdf->setSourceFile($source);


//new code

for($i = 1; $i <= $pages_count; $i++)
{
    $pdf->AddPage(); 

    $tplIdx = $pdf->importPage($i);

    $pdf->useTemplate($tplIdx, 0, 0); 
    //$pdf->Image($image,0,0,50,50); // X start, Y start, X width, Y width in mm

    //$pdf->SetFont('','',20); 
    //$pdf->SetTextColor(255,0,0); 
    //$pdf->SetXY(25, 25); 
    //$pdf->Write(0, $text); 
 if($i == $pageNo){
     $pdf->SetAlpha(0.3);
     $pdf->SetFillColor(230,230,0);
     
     $pdf->Rect($x1,$y1,$x2,$y2,'F');

     
}
}

ob_end_clean();
//$pdf->Output('/var/www/html/tcpdf-v/'.$output,'F');
$pdf->Output(dirname(__FILE__).'/'.$output,'F');

echo 'Success !';
}

generatePDF("test.pdf", "test.pdf", "Hello Shiv !", "rt.png",$x1,$y1,$x2,$y2,$pageNo);
//generatePDF("test.pdf", "test.pdf", "Hello Shiv !", "rt.png",10,10,100,100,1);
?>


