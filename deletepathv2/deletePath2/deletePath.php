<?php
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: 0');
include("DBConfig.php");
include("Function.php");
include("fpdf.php");
include("Class.php");
date_default_timezone_set('Asia/Bangkok');
$date = date_create(date('Y-M-d,H:i:s'));
$date_T = date_format($date,"Y/M/d H:i:s");
$Time = date_format($date,"H:i:s");
$datefile = date_format($date,"YMd");
$nameFile = "DeleteReport".$datefile.".pdf";
##prepare data and delete file
$Updb = new DB($Ho,$Da,$Us,$Pa);
$seLect_ans= $Updb->query("SELECT answer_stamp,record_name,record_path FROM v_xml_cdr ");// where xml_cdr_uuid = 'da075135-4bb5-4e00-b083-5b1ef02da2a8'");
for($i=0;$i<count($seLect_ans);$i++){
    $answer = date_create($seLect_ans[$i]['answer_stamp']);
    $File = $seLect_ans[$i]['record_name'];
    $record_path = $seLect_ans[$i]['record_path'];
    $Year = date_format($answer,"Y");
    $Mount = date_format($answer,"M");
    $Day = date_format($answer,"d");
   // $Path = "/mnt/recording/voice_backup/recordings/".$HTTP_HOST."/"."archive/".$Year."/".$Mount."/".$Day;
	$date_Format = date_format($answer,"M j, Y ,H:i:s ");
    $record_p = "/var/lib/freeswitch/recordings/".$HTTP_HOST."/archive"."/".$Year."/".$Mount."/".$Day;
    $record_py = "/var/lib/freeswitch/recordings/".$HTTP_HOST."/archive";
	$thirty = (30*24*60*60) ;
	$wat = strtotime(date('Y-M-d'));
    $filename = $record_path."/".$File;
    $F_D= filesize($filename);
    $File_D = convert_filesize($F_D);
	$unixtime = $wat - strtotime($date_Format);
    if(($unixtime >= $thirty) and  ($File != null) and (file_exists($filename))){
		$CDR_hi[$i]= array($File,$record_path,$File_D,$date_Format);
       // $Delete=unlink($filename);
        $status = "COMPLETE";
    }
    elseif(($unixtime >= $thirty) and  ($File = null) and (!file_exists($filename))){
       $CDR_hi= null;
        $status ="NOT COMPLETE";
    }
}
##Create pdf
$Count_folder = RemoveEmptySubFolders($record_py);
$count_file = count($CDR_hi);
$pdf = new PDF();
$title = 'Daily Report Delete Voice Record Files';
$pdf->SetTitle($title);
$header = array('Record Name', 'Record Path', 'Size', 'Date');
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->Cell(0,6,'HOST : '.$HTTP_HOST,0,'L');
$pdf->Cell(0,6,'Date : '.$date_T,0,1,'R');
$pdf->Ln(2);
$pdf->SetFillColor(0,102,102);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,5,'Summary',0,1,'C',true);
$pdf->Ln(2);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Status : '.$status,0,0,'L');
$pdf->Cell(0,5,'Total Nunber Of Files Delete : '.$count_file,0,1,'R');
$pdf->Ln(2);
$pdf->FancyTable($header,$CDR_hi);
$pdf->Output('Deletepath.pdf','F');


##send pdf

$slacktoken = "xoxp-284400513266-2692658439952-3023372550646-a1053b7de91c7c2a8a79767c758193b7";
        $header = array();
        $header[] = 'Content-Type: multipart/form-data';
        $file = new CurlFile('Deletepath.pdf', 'application/pdf');
        $postitems =  array(
            'token' => $slacktoken,
            'file' =>  $file,
            'text' => $HTTP_HOST,
            'title' => "Delete Report ".$date_T,
            'filename' => $nameFile,
            'channels' => "server-alert"

        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/files.upload");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$postitems);
        $send = curl_exec($curl);

$pdf = null;      
$Updb = null ;

?>
