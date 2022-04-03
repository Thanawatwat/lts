<?php

include("config.inc.php");

include("class/class.db.php");

$db = new DB($dbhost,$dbuser,$dbpassword,$dbname);
//if (permission_exists('xml_cdr_view')) {
$uuid = $_REQUEST[id];
				//get call recording from database
$sql = "SELECT  * ";
                //inner join v_extensions b on a.extension_uuid = b.extension_uuid
                //$sql.=" FROM v_xml_cdr  a  where ".$_SESSION['ssql'];
	if (!is_null($uuid)) {
						// $sql = "select record_name, record_path from v_xml_cdr ";
						// $sql .= "where xml_cdr_uuid = '$uuid' ";
        $sql = "select * from vm_voicemail_messages as m, v_voicemails as v, v_domains as d ";
		$sql .= "where m.domain_uuid = v.domain_uuid and 
        m.voicemail_uuid = v.voicemail_uuid and m.domain_uuid=d.domain_uuid and m.voicemail_message_uuid = '$uuid'";
        $parameters['domain_uuid'] = $uuid;
		$queryc=$db->select_query($sql);
        $row=$db->fetch_arr($queryc);
			if (is_array($row)) {
							// $record_name = $row['record_name'];
							// $record_path = $row['record_path'];
				$voicemail_uuid = $row['voicemail_uuid'];
				$voicemail_message_uuid = $row['voicemail_message_uuid'];
                $voicemail_id = $row['voicemail_id'];
				$voicemail_duuid = $row['domain_name'];
				$voicemail_time = $row['created_epoch'];
			}
			unset ($sql, $parameters, $row);
	}
	
$pathfu_vo = '/var/www/fusionpbx/Cpanel/wavvoice.wav';
/*
date_default_timezone_set('Asia/Bangkok');
$time_now = strtotime(date('Y-M-d'));
$thirty = (30*24*60*60);
$checktime = $time_now - $voicemail_time;
	if($checktime <= $thirty){
		$path = '/var/lib/freeswitch/storage/voicemail/default/'.$voicemail_duuid .'/'.$voicemail_id;
	}
	elseif($checktime > $thirty){
		$path = '/mnt/recording/voice_backup/storage/voicemail/default/'.$voicemail_duuid .'/'.$voicemail_id;
	}
	*/
						
				//build full path
					// $record_file = $record_path.'/'.$record_name;
					//$record_file = $_SESSION['switch']['voicemail']['dir'].'/default/'.$_SESSION['domain_name'].'/'.$voicemail_id;
                   // $path = '/var/lib/freeswitch/storage/voicemail/default/'.$voicemail_duuid .'/'.$voicemail_id;

    if (file_exists($path.'/msg_'.$voicemail_message_uuid.'.wav')) {
        $row['file_path'] = $path.'/msg_'.$voicemail_message_uuid.'.wav';
    }
    elseif (file_exists($path.'/msg_'.$voicemail_message_uuid.'.mp3')) {
        $row['file_path'] = $path.'/msg_'.$voicemail_message_uuid.'.mp3';
    }
	elseif (file_exists($pathfu_vo) && !file_exists($path.'/msg_'.$voicemail_message_uuid.'.wav') ){ 
		$audio_wav = call_wav_voice($voicemail_duuid,$uuid);
		$row['file_path'] = '/var/www/fusionpbx/Cpanel/wavvoice.wav';		
	}
$record_file = $row['file_path'];
//exit();
                //echo $record_file;
				//download the file
					if (file_exists($record_file)) {
						//content-range
						if (isset($_SERVER['HTTP_RANGE']) && $_GET['t'] != "bin")  {
							range_download($record_file);
						}
						ob_clean();
						$fd = fopen($record_file, "rb");
						if ($_GET['t'] == "bin") {
							header("Content-Type: application/force-download");
							header("Content-Type: application/octet-stream");
							header("Content-Type: application/download");
							header("Content-Description: File Transfer");
							//header("Content-Transfer-Encoding: binary"); 
							
						$file_ext = pathinfo($record_file, PATHINFO_EXTENSION);
						switch ($file_ext) {
							case "wav" : header('Content-Disposition: attachment; filename="msg_'.$uuid.'.wav"'); break;
							case "mp3" : header('Content-Disposition: attachment; filename="msg_'.$uuid.'.mp3"'); break;
							case "ogg" : header('Content-Disposition: attachment; filename="msg_'.$uuid.'.ogg"'); break;
						}
						}
						else {
							// $file_ext = pathinfo($record_name, PATHINFO_EXTENSION);
							$file_ext = pathinfo($record_file, PATHINFO_EXTENSION);
							switch ($file_ext) {
								case "wav" : header("Content-Type: audio/x-wav"); break;
								case "mp3" : header("Content-Type: audio/mpeg"); break;
								case "ogg" : header("Content-Type: audio/ogg"); break;
							}
						}
						// $record_name = preg_replace('#[^a-zA-Z0-9_\-\.]#', '', $record_name);
						// header('Content-Disposition: attachment; filename="'.$record_name.'"');
						header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
					    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // date in the past
						if ($_GET['t'] == "bin") {
							header("Content-Length: ".filesize($record_file));
						}
						ob_clean();
						fpassthru($fd);
					}
			//}



function range_download($file) {
			$fp = @fopen($file, 'rb');

			$size   = filesize($file); // File size
			$length = $size;           // Content length
			$start  = 0;               // Start byte
			$end    = $size - 1;       // End byte
			// Now that we've gotten so far without errors we send the accept range header
			/* At the moment we only support single ranges.
			* Multiple ranges requires some more work to ensure it works correctly
			* and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
			*
			* Multirange support annouces itself with:
			* header('Accept-Ranges: bytes');
			*
			* Multirange content must be sent with multipart/byteranges mediatype,
			* (mediatype = mimetype)
			* as well as a boundry header to indicate the various chunks of data.
			*/
			header("Accept-Ranges: 0-$length");
			// header('Accept-Ranges: bytes');
			// multipart/byteranges
			// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
			if (isset($_SERVER['HTTP_RANGE'])) {

				$c_start = $start;
				$c_end   = $end;
				// Extract the range string
				list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				// Make sure the client hasn't sent us a multibyte range
				if (strpos($range, ',') !== false) {
					// (?) Shoud this be issued here, or should the first
					// range be used? Or should the header be ignored and
					// we output the whole content?
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$size");
					// (?) Echo some info to the client?
					exit;
				}
				// If the range starts with an '-' we start from the beginning
				// If not, we forward the file pointer
				// And make sure to get the end byte if spesified
				if ($range0 == '-') {
					// The n-number of the last bytes is requested
					$c_start = $size - substr($range, 1);
				}
				else {
					$range  = explode('-', $range);
					$c_start = $range[0];
					$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
				}
				/* Check the range and make sure it's treated according to the specs.
				* http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
				*/
				// End bytes can not be larger than $end.
				$c_end = ($c_end > $end) ? $end : $c_end;
				// Validate the requested range and return an error if it's not correct.
				if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$size");
					// (?) Echo some info to the client?
					exit;
				}
				$start  = $c_start;
				$end    = $c_end;
				$length = $end - $start + 1; // Calculate new content length
				fseek($fp, $start);
				header('HTTP/1.1 206 Partial Content');
			}
			// Notify the client the byte range we'll be outputting
			header("Content-Range: bytes $start-$end/$size");
			header("Content-Length: $length");

			// Start buffered download
			$buffer = 1024 * 8;
			while(!feof($fp) && ($p = ftell($fp)) <= $end) {
				if ($p + $buffer > $end) {
					// In case we're only outputtin a chunk, make sure we don't
					// read past the length
					$buffer = $end - $p + 1;
				}
				set_time_limit(0); // Reset time limit for big files
				echo fread($fp, $buffer);
				flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
			}

			fclose($fp);
		}
function call_wav_voice($do_main,$id_xml){
//$user_pass = utf8_encode("voicerecordings:4FF8E2LXMBG3RS7QQGjBxcHZJwrYa2VV");
//$baseUSER =base64_encode($user_pass);
$base_url = 'https://'.$do_main.'/cansapi/voice/voicemails.php?';//"https://".$do_main."/cansapi/recordings/voice_recordings.php?";
$url = $base_url."cdr_uuid=".$id_xml;
unlink('wavvoice.wav');
$fp = fopen('wavvoice.wav', 'w');
$ch = curl_init();
//curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_HTTPGET,TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD,"voicerecordings:4FF8E2LXMBG3RS7QQGjBxcHZJwrYa2VV");
curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE );
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
$output = curl_exec($ch);
if(curl_errno($ch)){

echo 'Error'.curl_error($ch);
}
curl_close($ch);

return $output;

}

?>
