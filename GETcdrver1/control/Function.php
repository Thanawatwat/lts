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

?>