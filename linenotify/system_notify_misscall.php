<?php
//require_once('phpagi/phpagi-asmanager.php');
require_once('config.inc.php');
//require_once('class/class.phpmailer.php');
//include("apifunc.inc.php");

define('LINE_API', 'https://notify-api.line.me/api/notify'); 
//define('LINE_TOKEN', 'ZrbBfTEAjlwOSkzDgroe1kEK4zF2WSIgWzPaOkQJXsL'); 
//define('SLACK_TOKEN', 'xoxp-284563907842-350461466231-421001413232-a9e3b8e559346e81e241c0360e5ba061'); 

//$message = array( 'message' => 'ง่วงจังเลย', 'stickerPackageId' => 1, 'stickerId' => 1, ); 

function line_notify($token, $message) { 

	$header = array( 'Content-type: application/x-www-form-urlencoded', "Authorization: Bearer {$token}", ); 
	$data = http_build_query($message, '', '&'); 
	$cURL = curl_init(); 
	curl_setopt( $cURL, CURLOPT_URL, LINE_API); 
	curl_setopt( $cURL, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt( $cURL, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt( $cURL, CURLOPT_POST, 1); 
	curl_setopt( $cURL, CURLOPT_POSTFIELDS, $data); 
	curl_setopt( $cURL, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt( $cURL, CURLOPT_HTTPHEADER, $header); 
	curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, 1); 
	$result = curl_exec( $cURL ); 
	curl_close( $cURL ); 
	
}

/*function slack_notify($token,$message, $channel)
{
    $ch = curl_init("https://slack.com/api/chat.postMessage");
    $data = http_build_query([
        "token" => $token,
    	"channel" => $channel, //"#mychannel",
    	"text" => $message, //"Hello, Foo-Bar channel message.",
    	"username" => "VoxxyCloud",
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}*/

function slack_notify($webhook,$message, $room = "auto_cloud", $icon = ":longbox:") {
        $room = ($room) ? $room : "engineering";
        $data = "payload=" . json_encode(array(
                "channel"       =>  "#{$room}",
                "text"          =>  $message
                //"icon_emoji"    =>  $icon
            ));
	
	// You can get your webhook endpoint from your Slack settings
        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
	
	// Laravel-specific log writing method
        // Log::info("Sent to Slack: " . $message, array('context' => 'Notifications'));
        return $result;
    }


date_default_timezone_set('asia/bangkok');
$pre = mktime(date('H'), date('i'),  0, date('m'), date('d'), date('Y'));
$pretime = mktime(date('H'), date('i'),  0, 0, 0, 0);
$present = date("Y-m-d");
$date_full = date("D");



    
//	$con = mysql_connect($dbhost,$dbuser,$dbpassword);
//	mysql_select_db($dbname);
	$con_db = new PDO("pgsql:host=$dbhost dbname=$dbname user=$dbuser password=$dbpassword");
	$con_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$miss_call ="SELECT *,start_stamp::timestamptz at time zone 'Asia/Bangkok' AS call_date FROM vm_xml_cdr a inner join notify_2_groups b on a.destination_number  = b.extension inner join notify_groups c on b.gid = c.group_id where (a.missed_call  = true ) and (a.xml_cdr_uuid = '$argv[1]')"; 
	$pre_pare =$con_db->prepare($miss_call);
    	$pre_pare->execute();
 //  $misscall = "SELECT * FROM `misscall_data` a inner join notify_2_groups b on a.to_number = b.extension inner join notify_groups c on b.gid = c.group_id 
//	where c.misscall = 1 and a.flag !=5";
   //  $misscallqry = mysql_query($misscall);
      while ($rowmiss=$pre_pare->fetch(PDO::FETCH_ASSOC)) {
		  
		  $txt = explode(":",$rowmiss['group_end']);

		//array start
		$sxt = explode(":",$rowmiss['group_start']);

		//check start time		
		$te = mktime($txt[0], $txt[1], 0, 0, 0, 0);
		$ts = mktime($sxt[0], $sxt[1], 0, 0, 0, 0);

		if ($pretime>=$ts && $pretime<=$te)  {
			     
				     if (($date_full=="Sat" && $rowmiss['group_sat']=="0") || ($date_full=="Sun" && $rowmiss['group_sun']=="0")) {
						//echo 
//						$ret.="No Jobs holiday\n";
						//continue;
					  } else {

//						$ret.="You Have Job";
                                                 $spldate = explode(" ",$rowmiss["call_date"]);
						 $psdes = 'You have a missed call from '.$rowmiss["destination_number"].' to extension '.$rowmiss["caller_id_number"].' at '.$rowmiss["call_date"];
                                                 $psdes = " Your extension ".$rowmiss["destination_number"]." had a missed call from ".$rowmiss["caller_id_number"]." on ".$spldate[0]." at ".$spldate[1];
						 $message = array( 'message' => ' Your extension '.$rowmiss["destination_number"].' had a missed call from '.$rowmiss["caller_id_number"].' on '.$spldate[0].' at '.$spldate[1], ); 
						 if ($rowmiss["line_token"]!=="") {
							echo $miss_call;
						/*	date_default_timezone_set("Asia/Bangkok");
							$answer_sta = $rowmiss[call_date];
							$date_ans = new DateTime($answer_sta);
							$now_date = new DateTime();
							$dif_time = $now_date->diff($date_ans, true);
    							if($dif_time->y == 0 && $dif_time->m == 0 && $dif_time->d == 0 && $dif_time->h == 0 && $dif_time->i < 1 ){
							 line_notify($rowmiss["line_token"], $message);
							}*/
                                                         line_notify($rowmiss["line_token"], $message);
						//	}
						//	else{
                                                  //      break;
						 }
						}
						}
						}
							


		/*


						 if ($rowmiss["slack_channel"]!=="") {
							 slack_notify("$rowmiss[slack_webhook]",$psdes, "$rowmiss[slack_channel]");
						 }
						  if ($rowmiss["emaila"]!=="") {
							  //echo $rowmiss["emaila"];
							 //slack_notify("$rowmiss[slack_webhook]",$psdes, "$rowmiss[slack_channel]");
							    $mail = new PHPMailer();
								$mail->IsHTML(true);
								$mail->IsSMTP();
								$mail->SMTPAuth = true; // enable SMTP authentication
								$mail->SMTPSecure = "ssl"; // sets the prefix to the servier
								$mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server
								$mail->Port = 465; // set the SMTP port for the GMAIL server
								$mail->Username = "cns.dev@creaturelab.co.th"; // GMAIL username
								$mail->Password = "8qmFhX5J3c"; // GMAIL password
							    //$mail->Password = "kanom2005";
								$mail->From = "cns.dev@creaturelab.co.th"; // "name@yourdomain.com";
								//$mail->AddReplyTo = "support@thaicreate.com"; // Reply
								$mail->FromName = "support cns";  // set from Name
								$mail->Subject = 'You Have Misscall From '.$rowmiss[caller_id_number]; 
								$mail->Body = 'You have a missed call from '.$rowmiss[caller_id_number].' to extension '.$rowmiss[destination_number].' at '.$rowmiss[calldate];

								$mail->AddAddress($rowmiss[emaila]); // to Address

								//$mail->AddAttachment("thaicreate/myfile.zip");
								//$mail->AddAttachment("thaicreate/myfile2.zip");

								//$mail->AddCC("member@thaicreate.com", "Mr.Member ThaiCreate"); //CC
								//$mail->AddBCC("member@thaicreate.com", "Mr.Member ThaiCreate"); //CC

								$mail->set('X-Priority', '1'); //Priority 1 = High, 3 = Normal, 5 = low

								//echo $mail->Send(); if (!$mail->send()) {
								if (!$mail->send()) {
									echo "Mailer Error: " . $mail->ErrorInfo;
								} else {
									//echo "Message sent!";
								}
							}

							//fresh
							if ($rowmiss["freshdesk"]!=="") {

								$sql = "SELECT * FROM  cextension_groups WHERE group_crm_type = 'freshdesk' and group_crm_status = 1 and group_crm_api_key = '$rowmiss[freshdesk]'";
	                              $ret=mysql_query($sql);
								  $crow=mysql_fetch_array($ret);
								  
								  $sqlq = "SELECT * from qstats.queue_stats where (qevent = 1 or qevent = 18) and  uniqueid = '$rowmiss[luniq]'";
								  $retq = mysql_query($sqlq);
								  
								  $numqe = mysql_num_rows($retq);
								  
								  if ($numqe==1) {
								
								/*$api_key = "7hGGNhCw9ji2UTJ74S9";
								$password = "x";
								$yourdomain = "thelivingmobile";
								$api_key = $crow["group_crm_api_key"];
								$password = $crow["group_crm_api_password"];
								$yourdomain = $crow["group_crm_api_domain"];
								$group_id = $crow["group_id"];



								$agent_api_url = "https://$yourdomain.freshdesk.com/api/v2/agents";
								$ticket_api_url = "https://$yourdomain.freshdesk.com/api/v2/tickets";
								$contact_api_url = "https://$yourdomain.freshdesk.com/api/v2/contacts";


								//get customer id

								$cus_phone = $rowmiss[destination_number];
								$agent_phone = $rowmiss[caller_id_number];

								$dstc="?mobile=".$cus_phone;
								$urlc = $contact_api_url.$dstc;

								$cusinfo = request_zen_api($urlc,$api_key,$password,"GET");
								$cusid =  $cusinfo[0]['id'];
								$cname =  $cusinfo[0]['name'];
								$cmail =  $cusinfo[0]['email'];

								//print_r($cusinfo);

								//get agent id

								/*$incalle = "select * from admin_user where owner_extension='$agent_phone'";
								$irete=mysql_query($incalle,$con);

								$irowe=mysql_fetch_array($irete);
								$amail = $irowe['crm_email'];
								$dsta="?email=".$amail;
								$urla = $agent_api_url.$dsta;

								$ainfo = request_zen_api($urla,$api_key,$password,"GET");

								//print_r($ainfo);

								//$agentid =  $ainfo[0]['id'];
								//$agentname =  $ainfo[0]['contact']['name'];
								//$agentmail =  $ainfo[0]['contact']['email'];

								//if ($agentid) {
								
								//43000630067

								  if ($cusid) {
                                       $arraygid = array("3"=>"69000255009","99"=>"69000155876","101"=>"43000637018");
									  $ticket_data = json_encode(array(
									  "description" => "You Have New Miss Call by ".$cname."<br>*** Call Info ***
									                                      <br>Queue: ".$rowmiss[qnumber]."
																		  <br>Date: ".$rowmiss[calldate]."
																		  <br>Caller Number: ".$cus_phone."
																		  <br>Caller Name: ".$cname,
																		  //<br>*** Agent Info ***
																		  //<br>Agent Name: ".$agentname,
									  "type" => "Miss Call",
									  "subject" => "[".$rowmiss[uniq]."] You Have New Miss Call  by ".$cname,
									  "email" => $cmail,
									  "phone" => $cus_phone,
									  "name" => $cname,
									  "priority" => 4,
									  "status" => 2,
"tags" =>  array("misscall"),
"source" => 3,
									  //"responder_id" => $agentid,
                                                                          "group_id"=>(int)$arraygid[$group_id],

									  //"cc_emails" => array("ram@freshdesk.com", "diana@freshdesk.com")
									));
									  $create_ticket = request_zen_api($ticket_api_url,$api_key,$password,"POST",$ticket_data);
									   echo "ti";
									  print_r($create_ticket);


								  } else {
									  $contact_data = json_encode(array(
									  "name" => "New Contacts From Phone Call number ".$cus_phone,
									  "email" => $cus_phone."@newcustomer.com",
									  "mobile" => $cus_phone,
									  "phone" => $cus_phone,
									 ));

									  $create_contact = request_zen_api($contact_api_url,$api_key,$password,"POST",$contact_data);
									  echo "co";
									  print_r($create_contact);

                                       $cus_phone = $rowmiss[from_number];
										$agent_phone = $rowmiss[to_number];
                                        
										 //create ticket
										$dstc="?mobile=".$cus_phone;
										$urlc = $contact_api_url.$dstc;

										$cusinfo = request_zen_api($urlc,$api_key,$password,"GET");
										$cusid =  $cusinfo[0]['id'];
										$cname =  $cusinfo[0]['name'];
										$cmail =  $cusinfo[0]['email'];

										$arraygid = array("3"=>"69000255009","99"=>"69000155876","101"=>"43000637018");
									  $ticket_data = json_encode(array(
									  "description" => " You Have New Miss Call by ".$cname."<br>*** Call Info ***
									                                      <br>Queue: ".$rowmiss[qnumber]."
																		  <br>Date: ".$rowmiss[calldate]."
																		  <br>Caller Number: ".$cus_phone."
																		  <br>Caller Name: ".$cname,
																		  //<br>*** Agent Info ***
																		  //<br>Agent Name: ".$agentname,
									  "type" => "Miss Call",
									  "subject" => "[".$rowmiss[uniq]."] You Have New Miss Call  by ".$cname,
									  "email" => $cmail,
									  "phone" => $cus_phone,
									  "name" => $cname,
									  "priority" => 4,
									  "status" => 2,
"tags" =>  array("misscall"),
"source" => 3,
									  //"responder_id" => $agentid,
                                       "group_id"=>(int)$arraygid[$group_id],

									  //"cc_emails" => array("ram@freshdesk.com", "diana@freshdesk.com")
									));
									  $create_ticket = request_zen_api($ticket_api_url,$api_key,$password,"POST",$ticket_data);
									   echo "ti";
									  print_r($create_ticket);
									  //end create ticket
								  }

								//}
								} else {

									 $sqlq = "SELECT * from qstats.queue_stats where (qevent = 9 or qevent = 10) and  uniqueid = '$rowmiss[luniq]'";
								     $retq = mysql_query($sqlq);
								  
								     $numqe = mysql_num_rows($retq);
								  
								    if ($numqe==1) {
										mysql_query("update misscall_data set flag = 5 where id = ".$rowmiss[id]."");
									} else {
									 mysql_query("update misscall_data set flag = 4 where id = ".$rowmiss[id]."");
									 exit();
									}
								}
						    }
						
						 //
						 mysql_query("update misscall_data set flag = 5 where id = ".$rowmiss[id]."");
					}
			  echo $ret;
			

		  } //sat
	  }
*/
//     mysql_query("delete from misscall_data where flag = ''");
  //   mysql_close($con);
$con_db = null;
$pre_pare = null;
?>
