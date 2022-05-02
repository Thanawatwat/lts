<?php
$dbhost = '127.0.0.1';

$dbuser = 'fusionpbx';

$dbpassword = 'F3McXYB82GIby8xew9mBMdb4GFw';

$dbname = 'fusionpbx';


$dbhostcdr = '127.0.0.1';
$dbusercdr = 'fusionpbx';
$dbpasswordcdr = 'F3McXYB82GIby8xew9mBMdb4GFw';
$dbnamecdr = 'fusionpbx';



$Year = date("Y")+543;

$thaiweekFull=array("วันอาทิตย์ ที่","วันจันทร์ ที่","วันอังคาร ที่","วันพุธ ที่","วันพฤหัสบดี ที่","วันศุกร์ ที่","วันเสาร์ ที่");

$thaimonthFull=array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม", "พฤศจิกายน","ธันวาคม");

$thaimonth=array("ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.", "พ.ย.","ธ.ค.");

$ThaiDateFull = $thaiweekFull[date("w")]. date(" j "). $thaimonthFull[date("m")-1]. " ". $Year ; 


//api authen

/*$paramapi = array(
   "key" => "cpanel1234",
   "username" => "api_login",
   "password" => "CNS12_Xorcom34"
);

$api_url = "http://localhost/api/";*/

$PROVISION_AUTH_USER = 'provision';
$PROVISION_AUTH_PASS = 'AIzaSyC2ZpuUWO0QjkJXYpIXmxROuIdWPhY9Ub0';

$CONTACT_AUTH_USER = 'phonebook';
$CONTACT_AUTH_PASS = 'AIzaSyC2ZpuUWO0QjkJXYpIXmxROuIdWPhY9Ub0';
?>