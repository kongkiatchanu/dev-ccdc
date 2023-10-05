<?php

	function sendBulk($msisdn,$msg,$msgtype,$sender) {
		global $user_id;

		$host = "corpsms.dtac.co.th";
		$port = "80";
		$post_url = "http://corpsms.dtac.co.th/servlet/com.iess.socket.SmsCorplink";

		$refno = date("YmdHis");
		$timestamp = date("ymdHis");

		unset($data);
		$data = "RefNo=".$refno;
		$data .= "&TimeStamp=".$timestamp;
		$data .= "&Sender=".$sender;
		$data .= "&Msn=".$msisdn;
		$data .= "&Msg=".$msg;
		$data .= "&Encoding=0";
		$data .= "&MsgType=".$msgtype;
//		$data .= "&User=ictgater";
//		$data .= "&Password=ictin193";

        $data .= "&User=advans";
        $data .= "&Password=fteretex";

		$header = "POST ".$post_url." HTTP/1.0\n";
		$header .= "Host: $host\n";
		$header .= "Content-type: application/x-www-form-urlencoded\n";
		$header .= "Content-length: " . strlen($data) . "\n";
		$header .= "\n";

		$fp = fsockopen($host, $port, $err_num, $err_msg, 30);
		if(!$fp){

		}

		fputs($fp, $header . $data);
		while (!feof($fp)) {
				$error_msg .= fgets ($fp,1024);
		}
		fclose($fp);

		$socket_report = "";
		$array_error_msg = explode("\r\n", $error_msg);
		foreach ($array_error_msg as $result) {
				if (ereg("Status",$result)) {
						if (strlen($socket_report) == 0) {
								$socket_report = $result;
						} else {
								$socket_report = $socket_report.",".$result;
						}
				}
		}

        $filename = "./log/ccdc_".date("Ym").".log";
        $handle = fopen($filename, "a+");
        fwrite($handle, date("Y-m-d H:i:s")."|".$user_id."|".$refno."|".$timestamp."|".$sender."|".$msisdn."|".$msg."\n");
        fclose($handle);

		$returnVal = $socket_report;
		return $returnVal;
	}

	$socket_report = sendBulk("66899446998",$sms_text,"E","ccdc");
	$socket_report = ereg_replace("\r\n","",$socket_report);
	
	$socket_report = sendBulk("66947092193",$sms_text,"E","ccdc");
	$socket_report = ereg_replace("\r\n","",$socket_report);	
?>
