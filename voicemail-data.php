<?php
header('Cache-Control: no-cache');
 header('Pragma: no-cache');
 header('Expires: 0');
include("../cfg.php");
function get_remote_file_info_voice($do_main,$id_xml) {
    $base_url = "https://".$do_main."/cansapi/voice/sizes.php?";//"https://".$do_main."/cansapi/recordings/voice_recordings.php?";
    $url = $base_url."cdr_uuid=".$id_xml."&scope=voicemail";
    $ch = curl_init();
    url_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD,"voicerecordings:4FF8E2LXMBG3RS7QQGjBxcHZJwrYa2VV");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
    $data = curl_exec($ch);
	$file = json_decode($data,TRUE);  
    $fileSize =$file['message']['size'];
        if(curl_errno($ch)){

            echo 'Error'.curl_error($ch);
        }
    curl_close($ch);
	 $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($fileSize) - 1) / 3);
    return sprintf("%d",$fileSize / pow(1024, $factor)) .@$size[$factor];
} 

function curl_get_voipmonitor_cdr($vm_datefrom, $vm_dateto) {
    
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
	
	//foreach($dic as $key => $val)
	//{
	//	echo "{$key} -> {$val}\n";
	//}
 
    return $dic;
}

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
0=>'domain_uuid', 
	1 =>'created_epoch', 
	2 => 'caller_id_number',
	3 => 'message_length',
	4 => 'voicemail_message_uuid',
//11 => 'cleartext',
//12 => 'chargeunit'

);

$dbcdr = new DB($dbhostcdr,$dbusercdr,$dbpasswordcdr,$dbnamecdr);

// getting total number records without any search
$sql = "SELECT * ";
//inner join v_extensions b on a.extension_uuid = b.extension_uuid
$sql.=" FROM vm_voicemail_messages as m, vm_voicemails as v";
//$sql.=" inner join extension_2_groups eg on (v.voicemail_id = eg.\"extension\" and eg.gid=".$_SESSION['strEXGroup'].")";
$sql.=" where m.domain_uuid = v.domain_uuid and m.voicemail_uuid = v.voicemail_uuid and ".$_SESSION['ssqla'];
$query=$dbcdr->select_query($sql);
$totalData = $dbcdr->num_row_query($query);
//$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT  * ";
//inner join v_extensions b on a.extension_uuid = b.extension_uuid
//$sql.=" FROM v_xml_cdr  a  where ".$_SESSION['ssql'];
$sql.=" FROM vm_voicemail_messages as m, vm_voicemails as v";
//$sql.=" inner join extension_2_groups eg on (v.voicemail_id = eg.\"extension\" and eg.gid=".$_SESSION['strEXGroup'].")";
$sql.=" where m.domain_uuid = v.domain_uuid and m.voicemail_uuid = v.voicemail_uuid and ".$_SESSION['ssql'];
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( start_stamp LIKE '".$requestData['search']['value']."%' ";    
	$sql.=" OR src LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR dst LIKE '".$requestData['search']['value']."%' )";
}

$sqlc = "SELECT  count(*) as allr";
//inner join v_extensions b on a.extension_uuid = b.extension_uuid
//$sqlc.=" FROM v_xml_cdr a  where ".$_SESSION['ssql'];
$sqlc.=" FROM vm_voicemail_messages as m, vm_voicemails as v";
//$sqlc.=" inner join extension_2_groups eg on (v.voicemail_id = eg.\"extension\" and eg.gid=".$_SESSION['strEXGroup'].")";
$sqlc.=" where m.domain_uuid = v.domain_uuid and m.voicemail_uuid = v.voicemail_uuid and ".$_SESSION['ssql'];
$queryc=$dbcdr->select_query($sqlc);
$rowc=$dbcdr->fetch_arr($queryc);

//mysqli_query($conn, "SET time_zone='$offset';");

//$totalFiltered = $dbcdr->num_row_query($queryc); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

$totalFiltered = $rowc[allr]; 
$_SESSION['test']=$totalFiltered;

$sql.=" ORDER BY ".$columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['length']." OFFSET ".$requestData['start']."   ";
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
    $_SESSION["pr_sql_export"]="SELECT xml_cdr_uuid,start_stamp AS tcalldate, caller_id_name, caller_id_number, destination_number, TO_CHAR((duration || ' second')::interval, 'HH24:MI:SS'), TO_CHAR((billsec || ' second')::interval, 'HH24:MI:SS'), hangup_cause from vm_xml_cdr a inner join vm_extensions b on a.extension_uuid = b.extension_uuid  where ".$_SESSION['ssql']." order by start_stamp DESC";

    $time = date('d/m/Y H:i:s', $timec); // Back to string
    $timeh = date('Y-m-d H:i:s', $timec);
    $timehtml = str_replace(array('/',':'),"",$time);
    $timehtml2 = str_replace(" ","-",$timehtml);

//set the greeting directory
    $path = $_SESSION['switch']['voicemail']['dir'].'/default/'.$_SESSION['domain_name'].'/'.$row['voicemail_id'];
        if (file_exists($path.'/msg_'.$row['voicemail_message_uuid'].'.wav')) {
            $row['file_path'] = $path.'/msg_'.$row['voicemail_message_uuid'].'.wav';
        }
        if (file_exists($path.'/msg_'.$row['voicemail_message_uuid'].'.mp3')) {
            $row['file_path'] = $path.'/msg_'.$row['voicemail_message_uuid'].'.mp3';
        }
    $row['file_size'] = filesize($row['file_path']);
//$row['file_size_label'] = byte_convert($row['file_size']);
    $row['file_ext'] = substr($row['file_path'], -3);
    $message_minutes = floor($row['message_length'] / 60);
    $message_seconds = $row['message_length'] % 60;
//use International System of Units (SI) - Source: https://en.wikipedia.org/wiki/International_System_of_Units
    $row['message_length_label'] = ($message_minutes > 0 ? $message_minutes.' min' : null).($message_seconds > 0 ? ' '.$message_seconds.' s' : null);
    $row['created_date'] = date("d/m/Y H:i:s",$row['created_epoch']);

    $bill = durationBeautify($row["billsec"]);


    $pathsu = "https://$_SERVER[HTTP_HOST]/Cpanel/downloadvoice.php?id=".$row['voicemail_message_uuid']."&t=bin";
    $path = "https://$_SERVER[HTTP_HOST]/app/voicemails/voicemail_messages.php?action=download&id=".$row['voicemail_id']."&voicemail_uuid=".$row['voicemail_uuid']."&uuid=".$row['voicemail_message_uuid'];
    $soundurl = " <a href=\"sound_formv.php?id=$row[voicemail_message_uuid]&opath=$opath\"data-toggle=\"lightbox\" data-gallery=\"hiddenimages\" data-title=\"Sound Player\" data-width=\"600\" autoplay=\"false\" class=\"btn btn-success btn-xs\"><i class='fa fa-play-circle-o fa-lg'></i> </a>".get_remote_file_info_voice($_SESSION['domain_name'],$row['voicemail_message_uuid'])."";
    $nestedData[] = "<input type=\"checkbox\" class=\"flat\" name=\"table_records[]\" id=\"table_records[]\" value=\"$row[voicemail_message_uuid]\" >";
	// $nestedData[] = $time;
	// $nestedData[] = $row["caller_id_number"];
    // $nestedData[] = $bill;
    
    $nestedData[] = $row['created_date'];
	$nestedData[] = $row['caller_id_number'];
    $nestedData[] = $row['message_length_label'];
	
    // $pathsu = "https://$_SERVER[HTTP_HOST]/Cpanel/downloadv.php?id=".$row[xml_cdr_uuid]."&t=bin";
	
    // if ($row[record_path] != '' && file_exists($row[record_path].'/'.$row[record_name])) {
    //     $dstl = str_replace("#","",$row['destination']);
	//     $soundurl = " <a href=\"sound_form.php?hdatetime=$timehtml2&calltype=$row[direction]&datetime=$timec&src=$row[caller_id_number]&dst=$row[destination_number]&id=$row[xml_cdr_uuid]&opath=$opath\" data-toggle=\"lightbox\" data-gallery=\"hiddenimages\" data-title=\"Sound Player\" data-width=\"600\" class=\"btn btn-success btn-xs\"><i class='fa fa-play-circle-o fa-lg'></i> </a>".getRemoteFilesize($pathsu)."";
    //     $soundurl2 = " <a href=\"sound_form.php?hdatetime=$timehtml2&calltype=$row[calltype]&datetime=$timec&src=$row[source]&dst=$dstl&id=$row[recfile]&opath=$opath\" data-toggle=\"lightbox\" data-gallery=\"hiddenimages\" data-title=\"Sound Player\" data-width=\"600\" class=\"btn btn-info btn-xs\"><i class='fa fa-edit fa-lg'></i> </a> Note";
	// } else {
	// 	$soundurl="";
	// }
    
    $nestedData[] = $soundurl;
    
    // $button="
    // <a data-toggle=\"lightbox\" data-gallery=\"hiddenimages\" data-title=\"Extension $row[name]\" data-width=\"800\" href=\"edit_name_form.php?action=edit&id=$row[extension_id]\"  class=\"btn btn-info btn-xs\"><i class=\"fa fa-edit\"></i> Edit Name</a>";
    // $nestedData[] = $button;

	$data[] = $nestedData;

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
