<?php
include('../control/DBConfig.php');
include('../control/Function.php');

header('Content-Type: application/json; charset=utf-8');
$dataBASe = new DB('localhost','fusionpbx','fusionpbx','86A2WSO04wmb4fsuMbzix3RE1Q');
function require_auth($contact_auth_user, $contact_auth_pass) {
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

	$is_not_authenticated = (
		!$has_supplied_credentials ||
		$_SERVER['PHP_AUTH_USER'] != $contact_auth_user ||
		$_SERVER['PHP_AUTH_PW']   != $contact_auth_pass
	);
	
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		exit;
	}
}
require_auth($CONTACT_AUTH_USER, $CONTACT_AUTH_PASS);
if (!$_REQUEST['extension']) {
	$contact_p = "<error>Not Post Extension</error>";
	echo '<?xml version="1.0" encoding="utf-8" ?>';
    echo $contact_p;
    echo '';
	exit();
}
$ext_number = $_REQUEST['extension'];
$numberpage = $_REQUEST['page'];
$ext_domain = $_SERVER['HTTP_HOST'];
$startrow = '0';
$endrow = '20';
for($p=0,$n;$p < $numberpage;$n+=20,$p++){

	$startrow = $startrow +$n ;
	$endrow= $endrow + $n;
    
}
$inputDB ="SELECT answer_epoch,domain_name,caller_id_name,caller_id_number,caller_destination,destination_number,start_stamp,answer_stamp,end_stamp,billsec 
FROM (
    SELECT answer_epoch,domain_name,caller_id_name,caller_id_number,caller_destination,destination_number,start_stamp,answer_stamp,end_stamp,billsec 
,ROW_NUMBER() OVER (ORDER BY answer_epoch DESC) AS RowNum
    FROM v_xml_cdr where caller_id_number = '$ext_number'
) AS apiTable
WHERE apiTable.RowNum BETWEEN '$startrow' and '$endrow'";
$dataBASe->query("SELECT domain_uuid FROM v_extensions where extension ='$ext_number' and user_context = '$ext_domain'");
    if($dataBASe){
		if($_SERVER['REQUEST_METHOD'] == "GET"){
			//$json_array[] = array();
			$json_array = $dataBASe->query($inputDB);
			//print_r(count($json_array));
			//$json_array[] = $dataBASe->query("SELECT billsec FROM v_xml_cdr where caller_id_number = '$ext_number'");	
			//printf(count($json_array));
			//domain_name,caller_id_name,caller_id_number,caller_destination,destination_number,start_stamp,answer_stamp,end_stamp
			
			//echo json_encode($json_array);
			for($i=0;$i<count($json_array);$i++){
				date_default_timezone_set('Asia/Bangkok');
				$DEstination_num = $json_array[$i]['destination_number'];
				$Caller_num = $json_array[$i]['caller_id_number'];
				$CountTime = secToHR($json_array[$i]['billsec']);
				
				$dateCre = date_create($json_array[$i]['answer_stamp']);
				$check_date = date_create($json_array[$i]['answer_stamp']);
				$date_format = date_format($check_date,"M j, Y ,h:i:s A");
				$Time = date_format($dateCre,"H.i");
				$Year = date_format($dateCre,"Y");
				$Date = relative_date($date_format);
					if($ext_number == $Caller_num){
						$CDR_hi[$i]['status'] = "Outgoing Call";
						$CDR_hi[$i]['answertime'] = $Time;
						$CDR_hi[$i]['date'] = $Date;
						$CDR_hi[$i]['year'] = $Year;
						$CDR_hi[$i]['conversationtime'] = $CountTime;
					}
					elseif($ext_number == $DEstination_num){
						$CDR_hi[$i]['status'] = "Incoming Call";
						$CDR_hi[$i]['answertime'] = $Time;
						$CDR_hi[$i]['date'] = $Date;
						$CDR_hi[$i]['year'] = $Year;
						$CDR_hi[$i]['conversationtime'] = $CountTime;
					}	
			}
			//echo strtotime($date_format);
			//echo "today";
			//echo strtotime('now');
			//echo date();
			//print_r(count($CDR_hi));
			echo json_encode($CDR_hi);		
		}
		elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
 			echo 'This is POST';
		}
		else {
 			http_response_code(405);
		}
    }
$dataBASe = null;
?>