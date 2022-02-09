<?php
//header('Cache-Control: no-cache');
// header('Pragma: no-cache');
// header('Expires: 0');
include("config.inc.php");

include("class/class.db.php");

$db = new DB($dbhost,$dbuser,$dbpassword,$dbname);
//if (permission_exists('xml_cdr_view')) {
$uuid = $_REQUEST['id'];
				//get call recording from database
					if (!is_null($uuid)) {
						$sql = "select record_name, record_path,domain_name,answer_stamp from v_xml_cdr ";
						$sql .= "where xml_cdr_uuid = '$uuid' ";
						//$sql .= "and domain_uuid = '".$domain_uuid."' \n";
						$parameters['xml_cdr_uuid'] = $uuid;
						//$parameters['domain_uuid'] = $domain_uuid;
						$queryc=$db->select_query($sql);
                         $row=$db->fetch_arr($queryc);
						if (is_array($row)) {
							$record_name = $row['record_name'];
							$record_path = $row['record_path'];
							$answer_stamp = $row['answer_stamp'];
							$domain = $row['domain_name'];
						}
						unset ($sql, $parameters, $row);
					}

				//build full path
					date_default_timezone_set('Asia/Bangkok');
    					$answer = date_create($answer_stamp);
   					$Year = date_format($answer,"Y");
    					$Mount = date_format($answer,"M");
    					$Day = date_format($answer,"d");
    		
   					$Path = "/mnt/recording/voice_backup/recordings/".$domain."/"."archive/".$Year."/".$Mount."/".$Day;
    					$recover_p = "/var/lib/freeswitch/recordings/".$domain."/archive"."/".$Year."/".$Mount."/".$Day;
					$date_Format = date_format($answer,"M j, Y ,H:i:s ");
					$thirty = (30*24*60*60) ;
					$wat = strtotime(date('Y-M-d'));
					$unixtime = $wat - strtotime($date_Format);
    					if($unixtime <= $thirty){
						$record_file = $record_path.'/'.$record_name;
		  					
     					//	echo $Path;
    					}
					elseif($unixtime > $thirty){
						$record_file = $Path.'/'.$record_name;
						echo $record_file;
					}
//exit();
//$record_file = $record_path.'/'.$record_name;
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
							header('Accept-Ranges: bytes');
						}
						else {
							$file_ext = pathinfo($record_name, PATHINFO_EXTENSION);
							switch ($file_ext) {
								case "wav" : header("Content-Type: audio/x-wav"); break;
								case "mp3" : header("Content-Type: audio/mpeg"); break;
								case "ogg" : header("Content-Type: audio/ogg"); break;
							}
						}
						$record_name = preg_replace('#[^a-zA-Z0-9_\-\.]#', '', $record_name);
						header('Content-Disposition: attachment; filename="'.$record_name.'"');
						header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
						header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
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


?>
