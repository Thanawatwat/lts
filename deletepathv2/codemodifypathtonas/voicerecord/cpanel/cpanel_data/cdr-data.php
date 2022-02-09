<?php
//header('Cache-Control: no-cache');
// header('Pragma: no-cache');
// header('Expires: 0');
include("../cfg.php");

/*function curl_get_voipmonitor_cdr($vm_datefrom, $vm_dateto) {
    
    $base_url = 'http://110.238.117.30/php/model/sql.php?';
    $url = $base_url . 'module=bypass_login&user=cans&pass=mXe9yB5W8qHW';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result = curl_exec($curl);
    $response =  json_decode($result);

    
    $strCookie = 'PHPSESSID=' . $response->SID . '; path=/';
    
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	//$strCookie = 'PHPSESSID=' . $response->SID . '; path=/';
	curl_setopt( $curl, CURLOPT_COOKIE, $strCookie );
    
	//$url = 'http://110.238.117.30/php/model/sql.php?task=LISTING&module=CDR&fdatefrom=2021-09-20T00:00:00&fcallid=mBF2VOPG-iPqj-4BLqaNpw..';
	$url = $base_url . 'task=LISTING&module=CDR&fdatefrom=' .$vm_datefrom. '&fdateto=' . $vm_dateto;
	
	curl_setopt($curl, CURLOPT_URL, $url);
	$result = curl_exec($curl);
	curl_close($curl);
	
	$response =  json_decode($result, true);
	//echo $response['total'];
	$dic = [];
	foreach($response['results'] as $result)
	{
	    $mos[] = $result['a_mos_f1']. "|". $result['b_mos_f1'];
	    //echo $result['a_mos_f1']. "|". $result['b_mos_f1']."<br>";
	    
	    $dic[$result['fbasename']] = $result['a_mos_f1']. "|". $result['b_mos_f1'];
	}
	
	//foreach($dic as $key => $val)
	//{
	//	echo "{$key} -> {$val}\n";
	//}
 
    return $dic;
}*/
/*function curl_get_voipmonitor_cdr($vm_datefrom, $vm_dateto, $vm_domain) {
    
    $base_url = 'http://110.238.117.30/Cpanel/mos/index.php?';
    $url = $base_url . 'action=mos&fdatefrom=' .$vm_datefrom. '&fdateto=' . $vm_dateto. '&domain=' . $vm_domain;
    
    $username = "mos";
    $password = "6nWBVeGudgQnXgxe9ffU8UujJuBz3qnn9PcUNtM";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    $result = curl_exec($curl);
    curl_close($curl);
    
    $response =  json_decode($result, true);
    
    $dic = [];
    foreach($response as $res)
    {
	//$mos[] = $res['a_mos_adapt']. "|". $res['b_mos_adapt'];
	//$dic[$res['fbasename']] = $res['a_mos_adapt_mult10']. "|". $res['b_mos_adapt_mult10'];
	
	$a_mos = (float)$res['a_mos_adapt_mult10'] / 10;
	$b_mos = (float)$res['b_mos_adapt_mult10'] / 10;
	$dic[$res['fbasename']] = $a_mos. "|". $b_mos;
	
    }
	
    //foreach($dic as $key => $val)
    //{
    //	echo "{$key} -> {$val}\n";
    //}
 
    return $dic;
}*/

 function get_format($df) {
    $str = '';
    $str .= ($df->invert == 1) ? ' - ' : '';
    if ($df->y > 0) {
        // years
        $str .= ($df->y > 1) ? $df->y . ' Years ' : $df->y . ' Year ';
    } if ($df->m > 0) {
        // month
        $str .= ($df->m > 1) ? $df->m . ' Months ' : $df->m . ' Month ';
    } if ($df->d > 0) {
        // days
        $str .= ($df->d > 1) ? $df->d . ' Days ' : $df->d . ' Day ';
    } if ($df->h > 0) {
        // hours
        $str .= ($df->h > 1) ? $df->h . ' Hours ' : $df->h . ' Hour ';
    } if ($df->i > 0) {
        // minutes
        $str .= ($df->i > 1) ? $df->i . ' Minutes ' : $df->i . ' Minute ';
    } if ($df->s > 0) {
        // seconds
        $str .= ($df->s > 1) ? $df->s . ' Seconds ' : $df->s . ' Second ';
    }
    return $str;
}

/* Database connection start 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "asterisk";*/

date_default_timezone_set('asia/bangkok');
/*$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);*/

//$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$columns = array( 
// datatable column index  => database column name
0=>'xml_cdr_uuid', 
	1 =>'start_stamp', 
	2 => 'caller_id_name',
	3 => 'caller_id_number',
	4 => 'caller_destination',
        5 => 'destination_number',
	6 => 'duration',
	7 => 'billsec',
	8 => 'accountcode',
        9 => 'hangup_cause',
        10 => 'disposition',
	11 => 'record_name',
//11 => 'cleartext',
//12 => 'chargeunit'

);

$dbcdr = new DB($dbhostcdr,$dbusercdr,$dbpasswordcdr,$dbnamecdr);

// getting total number records without any search
$sql = "SELECT * , ve.outbound_caller_id_name";
//inner join v_extensions b on a.extension_uuid = b.extension_uuid
//$sql.=" FROM v_xml_cdr a where ".$_SESSION['ssqla'];
$sql.=" FROM v_xml_cdr a ";
//$sql.=" inner join extension_2_groups eg on (a.caller_id_number = eg.\"extension\")";
$sql.=" inner join extension_2_groups eg on (a.caller_id_number = eg.\"extension\" and eg.gid=".$_SESSION['strEXGroup'].")";
$sql.=" inner join v_extensions ve on (a.caller_id_number = ve.\"extension\")";
$sql.=" where ".$_SESSION['ssqla'];

$query=$dbcdr->select_query($sql);
$totalData = $dbcdr->num_row_query($query);
//$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT  * , ve.outbound_caller_id_name";
//inner join v_extensions b on a.extension_uuid = b.extension_uuid
//$sql.=" FROM v_xml_cdr  a  where ".$_SESSION['ssql'];
$sql.=" FROM v_xml_cdr a ";
//$sql.=" inner join extension_2_groups eg on (a.caller_id_number = eg.\"extension\")";
$sql.=" inner join extension_2_groups eg on (a.caller_id_number = eg.\"extension\" and eg.gid=".$_SESSION['strEXGroup'].")";
$sql.=" inner join v_extensions ve on (a.caller_id_number = ve.\"extension\")";
$sql.=" where ".$_SESSION['ssql'];
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( start_stamp LIKE '".$requestData['search']['value']."%' ";    
	$sql.=" OR src LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR dst LIKE '".$requestData['search']['value']."%' )";
}

$sqlc = "SELECT  count(*) as allr";
//inner join v_extensions b on a.extension_uuid = b.extension_uuid
//$sqlc.=" FROM v_xml_cdr a  where ".$_SESSION['ssql'];
$sqlc.=" FROM v_xml_cdr a ";
//$sqlc.=" inner join extension_2_groups eg on (a.caller_id_number = eg.\"extension\")";
$sqlc.=" inner join extension_2_groups eg on (a.caller_id_number = eg.\"extension\" and eg.gid=".$_SESSION['strEXGroup'].")";
$sqlc.=" inner join v_extensions ve on (a.caller_id_number = ve.\"extension\")";
$sqlc.=" where ".$_SESSION['ssql'];
$queryc=$dbcdr->select_query($sqlc);
$rowc=$dbcdr->fetch_arr($queryc);

//mysqli_query($conn, "SET time_zone='$offset';");

//$totalFiltered = $dbcdr->num_row_query($queryc); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

$totalFiltered = $rowc[allr]; 


$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['length']." OFFSET ".$requestData['start']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=$dbcdr->select_query($sql);


$pathsu="";
$data = array();
while( $row=$dbcdr->fetch_arr($query) ) {  // preparing an array
	$nestedData=array(); 
//dst destination
//if ($row["dst"]!=="") {

//+ (3600*7)
$timec = strtotime($row["start_stamp"]) ;
//$_SESSION["pr_sql_export"]="SELECT xml_cdr_uuid,start_stamp AS tcalldate, caller_id_name, caller_id_number, destination_number, TO_CHAR((duration || ' second')::interval, 'HH24:MI:SS'), TO_CHAR((billsec || ' second')::interval, 'HH24:MI:SS'), hangup_cause from v_xml_cdr a inner join v_extensions b on a.extension_uuid = b.extension_uuid  where ".$_SESSION['ssql']." order by start_stamp DESC";
$_SESSION["pr_sql_export"]="SELECT xml_cdr_uuid, to_char(start_stamp , 'DD/MM/YYYY HH24:MI:SS') AS tcalldate, caller_id_name, caller_id_number, destination_number, TO_CHAR((duration || ' second')::interval, 'HH24:MI:SS'), TO_CHAR((billsec || ' second')::interval, 'HH24:MI:SS'), hangup_cause from v_xml_cdr a inner join extension_2_groups eg on a.caller_id_name = eg.extension  where ".$_SESSION['ssql']." order by start_stamp DESC";

$time = date('d/m/Y H:i:s', $timec); // Back to string
$timeh = date('Y-m-d H:i:s', $timec);
$timehtml = str_replace(array('/',':'),"",$time);
$timehtml2 = str_replace(" ","-",$timehtml);

/*if ($row["billsec"]=="0" and $row["disposition"]=="ANSWERED") {
	$dcommand = "soxi -D ".$row['recfile'];
	$fdur = (int) exec($dcommand);
	$row["billsec"]=$fdur;
}

if ($row["cleartext"]=="1") {
   $lico = "<i class='fa fa-check-circle fa-lg' style='color:green'></i>";
} else {
   $lico = "<i class='fa fa-ban fa-lg' style='color:gray'></i>";
}

if ($row["chargeunit"]=="1") {
   $leico = "<i class='fa fa-check-circle fa-lg' style='color:green'></i>";
} else {
    $leico = "<i class='fa fa-ban fa-lg' style='color:gray'></i>";
}

if (is_null($row["recfile_cloud"])) {
   $nico = "<i class='fa fa-ban fa-lg' style='color:gray'></i>";
} else {
    $nico = "<i class='fa fa-check-circle fa-lg' style='color:green'></i>";
}*/


//$devices = explode("-",$row['dstchannel']);
//$devices = $row['extension'];
//$devices = $row['destination_number'];
$dur  = durationBeautify($row["duration"]); 
$bill = durationBeautify($row["billsec"]);

if (substr($row['destination_number'],0,3) == "509" && strlen($row['destination_number']) >= 12 ){
	$devices = substr($row['destination_number'],0,12);
}
else if (substr($row['destination_number'],0,3) == "510" && strlen($row['destination_number']) >= 13 ){
	$devices = substr($row['destination_number'],0,13);
}
else {
	$devices = $row['destination_number'];
}

if (substr($row['caller_destination'],0,3) == "509" && strlen($row['caller_destination']) >= 12 ){
	$caller_destination = substr($row['destination_number'],0,12);
}
else if (substr($row['caller_destination'],0,3) == "510" && strlen($row['caller_destination']) >= 13 ){
	$caller_destination = substr($row['caller_destination'],0,13);
}
else {
	$caller_destination = $row['caller_destination'];
}


//$vm_time_datefrom = strtotime($vm_date[$x-1]);
$vm_datefrom = date("d/m/Y", $timec)."T00:00:00";
	//echo $vm_datefrom;
	
//$vm_time_dateto = strtotime($vm_date[0]);
$vm_dateto = date("d/m/Y", $timec)."T23:59:59";

/*$vm_dic = curl_get_voipmonitor_cdr($vm_datefrom, $vm_dateto, $row['domain_name']);*/

$str_vm_dic = str_replace("~","_",$row['sip_call_id']);



        $nestedData[] = "<input type=\"checkbox\" class=\"flat\" name=\"table_records[]\" id=\"table_records[]\" value=\"$row[xml_cdr_uuid]\" >";
	$nestedData[] = $time;
	if($row["outbound_caller_id_name"] != null){
		$nestedData[] = $row["outbound_caller_id_name"];
	}
	else{
		$nestedData[] = $row["caller_id_name"];
	}
	
	$nestedData[] = $row["caller_id_number"];
	//$nestedData[] = $row['caller_destination'];
	//$nestedData[] = $caller_destination;
	if($caller_destination == null){
		$nestedData[] = $devices;
	}
	else{
		$nestedData[] = $caller_destination;
	}
	

	

        $nestedData[] = $devices;
	$nestedData[] = $dur;
        $nestedData[] = $bill;
	

    /*if ($row["hangup_cause"]=="ANSWERED") {
		$ico = "<i class='fa fa-check-circle fa-lg' style='color:green'></i>";
	} else   if ($row["disposition"]=="NO ANSWER") {
		$ico = "<i class='fa fa-ban fa-lg' style='color:yellow'></i>";
	} else   if ($row["disposition"]=="BUSY") {
		$ico = "<i class='fa fa-close fa-lg' style='color:red'></i>";
	} else   if ($row["disposition"]=="FAILED") {
		$ico = "<i class='fa fa-frown-o fa-lg' style='color:red'></i>";
	} else   if ($row["disposition"]=="CONGESTION") {
		$ico = "<i class='fa fa-frown-o fa-lg' style='color:red'></i>";
	}*/

    //$trunka = explode("/",$row['dstchannel']);
	//$trunka2 = explode("-",$trunka[1]);
	//$trunk = $trunka2[0]."-".$trunka2[1];
	//if ($row["dst"][0]=="0") {
	  //$nestedData[] = $row['dstc'];
	//} else {
		$nestedData[] = $row['direction'];
	//}
/*if ($row["callback_date"]!=="0000-00-00 00:00:00") {
        
$dateb1 = new DateTime($timeh);
$dateb2 = new DateTime($row["callback_date"]);
$diff = $dateb1->diff($dateb2);
$afb = get_format($diff);
$cbd = $row["callback_date"];
} else {
$afb="";
$cbd = "";
}*/



               if ($row['direction'] == 'inbound' || $row['direction'] == 'local') {
								if ($row['answer_stamp'] != '' && $row['bridge_uuid'] != '') { $call_result = 'Answered'; }
								
								else if ($row['answer_stamp'] != '' && $row['bridge_uuid'] == '') { $call_result = 'Voicemail'; }
								else if ($row['answer_stamp'] == '' && $row['bridge_uuid'] == '' && $row['sip_hangup_disposition'] != 'send_refuse') { $call_result = 'Cancelled'; }
								
								else { $call_result = 'Failed'; }
							}
							else if ($row['direction'] == 'outbound') {
								if ($row['answer_stamp'] != '' && $row['bridge_uuid'] != '') { $call_result = 'Answered'; }
								else if ($row['answer_stamp'] == '' && $row['bridge_uuid'] != '') { $call_result = 'Cancelled'; }
								else if($row['hangup_cause'] == 'ANSWERED'){
									$call_result = 'ANSWERED';
								}
								else if($row['hangup_cause'] == 'NO ANSWER'){
									$call_result = 'NO ANSWER';
								}
								else if($row['hangup_cause'] == 'FAILED'){
									$call_result = 'FAILED';
								}
								else { $call_result = 'Failed'; }
							}
							if($row['hangup_cause'] == 'ANSWERED'){
								$call_result = 'ANSWERED';
							}
							else if($row['hangup_cause'] == 'NO ANSWER'){
								$call_result = 'NO ANSWER';
							}
							else if($row['hangup_cause'] == 'FAILED'){
								$call_result = 'FAILED';
							}
							/*else if($row['direction'] == ''){
								if($row['hangup_cause'] == 'ANSWERED'){
									$call_result = 'ANSWERED';
								}
								else if($row['hangup_cause'] == 'NO ANSWER'){
									$call_result = 'NO ANSWER';
								}
							}*/

//$nestedData[] = "$ico ".$call_result;
$nestedData[] = $call_result;
//$nestedData[] = $vm_dic[$str_vm_dic];

	//$nestedData[] = "$ico ".ucfirst(strtolower(str_replace("_"," ",$row["hangup_cause"])));
	
//$nestedData[] = $cbd;

//$nestedData[] = $afb;

       //$paths = str_replace("/var/spool/asterisk/monitor","",$row['recfile']);
		 date_default_timezone_set('Asia/Bangkok');
	   $answer = date_create($row['answer_stamp']);
		$Year = date_format($answer,"Y");
	   $Mount = date_format($answer,"M");
	   $Day = date_format($answer,"d");
	   $HTTP_HOST = "cns6.cans.cc";//$_SERVER['HTTP_HOST'];
		$Path = "/mnt/recording/voice_backup/recordings/".$HTTP_HOST."/"."archive/".$Year."/".$Mount."/".$Day;
	
		//if (is_file($paths)) { 
	//$pathsu="$cdrfile_url".$opath.$paths;
	$pathsu = "https://$_SERVER[HTTP_HOST]/Cpanel/downloadv.php?id=".$row['xml_cdr_uuid']."&t=bin";
	 //if (URL_exists($pathsu)) {
	if (($row[record_path] != '' && file_exists($row[record_path].'/'.$row[record_name])) or (file_exists($Path.'/'.$row[record_name])) or (file_exists($pathtrue3))) {
	   //if ($row["disposition"]=="ANSWERED") {
               $dstl = str_replace("#","",$row['destination']);
	   $soundurl = " <a href=\"sound_form.php?hdatetime=$timehtml2&calltype=$row[direction]&datetime=$timec&src=$row[caller_id_number]&dst=$row[destination_number]&id=$row[xml_cdr_uuid]&opath=$opath\" data-toggle=\"lightbox\" data-gallery=\"hiddenimages\" data-title=\"Sound Player\" data-width=\"600\" class=\"btn btn-success btn-xs\"><i class='fa fa-play-circle-o fa-lg'></i> </a>".getRemoteFilesize($pathsu)."";
           	   $soundurl2 = " <a href=\"sound_form.php?hdatetime=$timehtml2&calltype=$row[calltype]&datetime=$timec&src=$row[source]&dst=$dstl&id=$row[recfile]&opath=$opath\" data-toggle=\"lightbox\" data-gallery=\"hiddenimages\" data-title=\"Sound Player\" data-width=\"600\" class=\"btn btn-info btn-xs\"><i class='fa fa-edit fa-lg'></i> </a> Note";
/*} else{
	        $recf = explode("/",$row[recfile]);
		   $soundurl = "<a href='#' > </a>";
$soundurl2 = "<a href='#' > </a>";


		    
	   }
	} else {
	$soundurl = "<a href='#' ></a>";
$soundurl2 = "<a href='#' > </a>";

	}*/
	 } else {
		 $soundurl="";
	 }
	
$nestedData[] = $soundurl;
//$nestedData[] = $soundurl2;

//$nestedData[] = $lico." / ".$leico." / ".$nico;

	$data[] = $nestedData;

  //}

}

if ($requestData['draw']="") {
	$requestData['draw']=1;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data,   // total data array
			//"sql" => $sql
			);

echo json_encode($json_data);  // send data as json format

?>
