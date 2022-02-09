<?php
function secToHR($seconds) {
  $hours = floor($seconds / 3600);
  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  //return "$hours:$minutes:$seconds";
  if($hours == 0 && $minutes == 0){
  return $seconds." s";
  }
  if($hours == 0 && $minutes !== 0){
    return $minutes.$seconds." min";
    }
    if($hours !== 0){
      return $hours.$minutes.$seconds." hr";
      }
}


  //Relative Date Function
  function relative_date($TStamp) {
    date_default_timezone_set('Asia/Bangkok');
    $timeStamp = strtotime($TStamp);
    $today = strtotime(date('Y-M-d'));
     
    $reldays = ($timeStamp - $today)/86400;
     
   // if ($today == $timeStamp) {
    if ($reldays >= 0 && $reldays < 1) { 
    return 'Today';
     
    } else if ($reldays >= 1 && $reldays < 2) {
     
    return 'Tomorrow';
     
    } else if ($reldays >= -1 && $reldays < 0) {
     
    return 'Yesterday';
    }
    else {

      $retime = date_create($TStamp);
      $send = date_format($retime,"M d D");
      return $send;
    }
    
  }


  function convert_filesize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function slack($message, $channel)
{
    $ch = curl_init("https://slack.com/api/chat.postMessage");
    $data = http_build_query([
        "token" => "YOUR_API_TOKEN",
    	"channel" => $channel, //"#mychannel",
    	"text" => $message, //"Hello, Foo-Bar channel message.",
    	"username" => "MySlackBot",
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}


function RemoveEmptySubFolders($path)
{
  $empty=true;
  foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
  {
     $empty &= is_dir($file) && RemoveEmptySubFolders($file);
  }
  return $empty && rmdir($path);
}


?>