<?php
class PDF extends FPDF
{
   
    function Header()
    {
        global $title;
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(0,102,102);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->Cell(0,6,$title,0,1,'C',true);
        $this->Ln(2);
        // Save ordinate
        $this->y0 = $this->GetY();
    }
    
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Text color in gray
        $this->SetTextColor(128);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
    function ChapterBody($file)
{
    // Read text file
    $txt = file_get_contents($file);
    // Font
    $this->SetFont('Times','',12);
    // Output text in a 6 cm width column
    $this->MultiCell(60,5,$txt);
    $this->Ln();
    // Mention
    $this->SetFont('','I');
    $this->Cell(0,5,'(end of excerpt)');
    // Go back to first column
    $this->SetCol(0);
}

function PrintChapter($num, $title, $file)
{
    // Add chapter
    $this->AddPage();
    $this->ChapterTitle($num,$title);
    $this->ChapterBody($file);
}
function FancyTable($header, $datatable)
{
    // Colors, line width and bold font
    $this->SetFillColor(0,102,102);
    $this->SetTextColor(255);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Header
    $w = array(60, 70, 30, 30);
    $this->Cell(0,6,'Information',1,1,'C',true);
    for($i=0;$i<count($header);$i++)
    $this->Cell($w[$i],6,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('Arial','',6);
    
    // Data
    $fill = false;
    foreach($datatable as $row)
    {
        $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
        $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
        $this->Cell($w[2],6,$row[2],'LR',0,'R',$fill);
        $this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);
        $this->Ln();
        $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
}
function FancyTable2($header, $datatable)
{
    // Colors, line width and bold font
    $this->SetFillColor(0,102,102);
    $this->SetTextColor(255);
    $this->SetDrawColor(0,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Header
    $w = array(25, 115, 25, 25);
    $this->Cell(0,6,'Information',1,1,'C',true);
    for($i=0;$i<count($header);$i++)
    $this->Cell($w[$i],6,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('Arial','',6);
    
    // Data
    $fill = false;
    foreach($datatable as $row)
    {
        $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
        $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
        $this->Cell($w[2],6,$row[2],'LR',0,'R',$fill);
        $this->Cell($w[3],6,$row[3],'LR',0,'R',$fill);
        $this->Ln();
        $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
}

}
?>
