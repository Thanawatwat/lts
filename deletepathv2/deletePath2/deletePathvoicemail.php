<?php
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: 0');
include("DBConfig.php");
include("Function.php");
include("fpdf.php");
include("Class.php");
date_default_timezone_set('Asia/Bangkok');
$time_now = strtotime(date('Y-M-d H:i:s'));
$nameFilevoice = "DeleteReportvoicemail".$time_now.".pdf";

$datenow = date('Y/M/d H:i:s',$time_now);
$thirty = (30*24*60*60) ;
$pathdb = new DB($Ho,$Da,$Us,$Pa);
$seLect_voice = $pathdb->query("SELECT voicemail_id,domain_uuid,voicemail_uuid FROM v_voicemails ");
$seLect_message = $pathdb->query("SELECT voicemail_message_uuid,domain_uuid,voicemail_uuid,created_epoch FROM v_voicemail_messages ");
for($i=0;$i<count($seLect_voice);$i++){
    $voice_id =$seLect_voice[$i]['voicemail_id'];
    $voice_uuid=$seLect_voice[$i]['voicemail_uuid'];
    $voice_domain_uuid=$seLect_voice[$i]['domain_uuid'];

        for ($j = 0; $j < count($seLect_message); $j++) {
            $voice_messages =$seLect_message[$j]['voicemail_message_uuid'];
            $voice_mess_uuid=$seLect_message[$j]['voicemail_uuid'];
            $timebuildvoice =$seLect_message[$j]['created_epoch'];
            $voicemess_domain_uuid=$seLect_message[$j]['domain_uuid'];
                if(($voice_uuid == $voice_mess_uuid) and ($voice_domain_uuid == $voicemess_domain_uuid)){
                $pathvoice = "/var/lib/freeswitch/storage/voicemail/default/cns6.cans.cc/".$voice_id."/msg_".$voice_messages.".wav";
                $time_file = date('Y/M/d H:i:s',$timebuildvoice);
                $checktime = $time_now - $timebuildvoice;
                $F_Dvoice= filesize($pathvoice);
                $File_Dvoice = convert_filesize($F_Dvoice);
                if(($checktime <= $thirty) and (file_exists($pathvoice))){//($F_Dvoice != null)){
		        $datapdf= null;
                $status = "NO FILE";
                }
                elseif(($checktime > $thirty) and (file_exists($pathvoice))){ //($F_Dvoice != null)){
                    $datapdf[$j] = array($voice_id,$pathvoice, $File_Dvoice ,$time_file);
       
                 $status ="COMPLETE";
                    // unlink($pathvoice);
                 }
		elseif(!file_exists($pathvoice)){
		$status = "NO FILE";
		$datapdf= null;
		}
                }      
         }
        }
        $countvoice = count($datapdf);
        $pathvoicehost = "/var/lib/freeswitch/storage/voicemail/default/cns6.cans.cc";
        RemoveEmptySubFolders($pathvoicehost);
##Create pdf
$pdf = new PDF();
$title = 'Daily Report Delete Voicemail';
$pdf->SetTitle($title);
$header = array('Voicemail ID', 'Voicemail Path', 'Size', 'Date');
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->Cell(0,6,'HOST : '.$HTTP_HOST,0,'L');
$pdf->Cell(0,6,'Date : '.$datenow,0,1,'R');
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
$pdf->Cell(0,5,'Total Nunber Of Files Delete : '.$countvoice,0,1,'R');
$pdf->Ln(2);
$pdf->FancyTable2($header,$datapdf);
$pdf->Output('Deletepathvoice.pdf','F');

//$pdf->Output();
##send pdf


$slacktokenvoice = "xoxp-284400513266-2692658439952-3023372550646-a1053b7de91c7c2a8a79767c758193b7";
        $header = array();
        $header[] = 'Content-Type: multipart/form-data';
        $filevoice = new CurlFile('Deletepathvoice.pdf', 'application/pdf');
        $postitemsvoice =  array(
            'token' => $slacktokenvoice,
            'file' =>  $filevoice,
            'text' => $HTTP_HOST,
            'title' => "Delete Report Voicemail ".$datenow,
            'filename' => $nameFilevoice,
            'channels' => "server-alert"

        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/files.upload");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$postitemsvoice);
	 $send = curl_exec($curl);
$pdf = null;
$pathdb = null;
?>
