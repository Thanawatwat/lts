<?php
include("../configDB/db_con.php");
//specify date&time 
$time_A = "17:0:0" ;
$date_A = "2022-02-28";
$time_B = "17:0:0" ;
$date_B ="2022-03-28";
//conect database
$db_con = new DB($Ho,$Da,$Us,$Pa);
$seLect_ans= $db_con->query(" SELECT * FROM call_result_log where ref_key != 'null' and domain_name ='lmwn.cans.cc'  limit 1");
//$seLect_ans= $db_con->query(" SELECT * from call_result_logs crl ,v_xml_cdr vxc where crl.ref_id = vxc.xml_cdr_uuid and vxc.answer_stamp between '$date_A.' '.'$time_A.' +0700' and '$date_B.' '.'$time_B.' +0700' );

while ($rowexaf = $seLect_ans->fetch(PDO::FETCH_ASSOC)){
        
         
        $explode = explode("archive",$rowexaf['record_path']);
         $Path_sou = "/mnt/recording/".$rowexaf['domain_name']."/"."archive/".$explode[1]."/".$rowexaf['ref_id'];
         $Path_des =  "/var/lib/freeswitch/recordings/customdir/lmwn/".$rowexaf['ref_id'];
/*
        //rename ref_key to ref_id on new path

        if(file_exists($Path_des.".mp3")){
                rename($Path_des.".mp3","/var/lib/freeswitch/recordings/customdir/lmwn/".$rowexaf['ref_id'].".mp3");
        }
        elseif(file_exists($Path_des.".wav")){
                rename($Path_des.".wav","/var/lib/freeswitch/recordings/customdir/lmwn/".$rowexaf['ref_id'].".wav");
        }

*/


         //check file on new path exists ? and copy file from oreca to new path
/*

        if(file_exists($Path_sou.".mp3") and !file_exists($Path_des.".mp3")){
                mkdir(dirname($Path_des.".mp3"), 0777, true);
                      copy($Path_sou.".mp3",$Path_des.".mp3");
        }
        elseif(file_exists($Path_sou.".wav") and !file_exists($Path_des.".wav")){
                //mkdir($Path_des.".wav");
                mkdir(dirname($Path_des.".wav"), 0777, true);
                copy($Path_sou.".wav",$Path_des.".wav");
                echo "wav";
        }

*/

//echo $Path_sou;
//print_r($rowexaf['ref_key']);

}






?>
