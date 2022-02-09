<?php
$slacktoken = "xoxp-284400513266-2692658439952-3021138875041-b74eecc3b44a1ffc2efe1a6ee9813c4e";
        $header = array();
        $header[] = 'Content-Type: multipart/form-data';
        $file = new CurlFile('testw.pdf', 'application/pdf');
        $postitems =  array(
            'token' => $slacktoken,
            'file' =>  $file,
            'text' => "This is my photo",
            'title' => "Why this?",
            'filename' => "testw.pdf",
            'channels' => "server-alert"

        );
            
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/files.upload");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$postitems);
        $data = curl_exec($curl);
        ?>