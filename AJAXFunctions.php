<?php 

// this is the file for all the AJAX Requests from the contact us page.

//these are for the PHP Helper files
// include ('headers/databaseConn.php');
include('helpers.php');

// for mandrill mail sending API.
require_once 'mandrill/Mandrill.php'; 

if(isset($_GET["no"]) && $_GET["no"] == "1") {  // for sending the contact-me email 
	SendContactMeQuery($_GET["name"], $_GET["email"], $_GET["tel"], $_GET["message"]);
}

// for sending the contact-me email Query
function SendContactMeQuery($name, $email, $tel, $message) {
	$resp = "-1";
	$adminResp = "-1";
	$queryResp = "-1";
	try {
		// for the config file
		$configs = include('config/config.php');
		if(!isset($configs)) {   // if configs are not populated
			$resp = "-1";
			echo $resp;
			return;
		}

		// first send the mail to query@sagaranand.com
		$adminMessage = "Dear Admin, <br /><br /> Please find the query below, from the contact-me page of SagarAnand.com: <br /><br />";
		$adminMessage .= "<b>Name: " . $name . "</b><br />";
		$adminMessage .= "<b>Email Address: " . $email . "</b><br />";
		$adminMessage .= "<b>Contact: " . $tel . "</b><br />";
		$adminMessage .= "<b>Message/Query: " . $message . "</b><br /><br />";
		$adminMessage .= "Please take appropriate action. Thank you.";

		$adminRes = SendMessage($configs['adminEmail'], $configs['adminName'], $email, $name, $configs['adminSubject'], $adminMessage);
		$adminStatus = $adminRes[0]['status'];
		if($adminStatus == 'sent') {
			$resp = "1";
			$adminResp = "1";
		} else if($adminStatus == 'queued' || $adminStatus == 'scheduled') {
			$resp = "1";
			$adminResp = "2";
		} else if($adminStatus == 'rejected' || $adminStatus == 'invalid') {
			$resp = "-1";
			$adminResp = "-1";
		}

		// now, send the acknowlwdgement mail to the user
		$queryMessage = "Hi " . $name . "<br /><br />";
		$queryMessage .= "I have received your query and will reply to you in the next 48 hours. :) <br /><br />";
		$queryMessage .= "Thank you. <br />Sagar Anand, <br />query@sagaranand.com";
		$queryRes = SendMessage($email, $name, $configs['adminEmail'], $configs['adminName'], $configs['autoQuerySubject'], $queryMessage);
		$queryStatus = $queryRes[0]['status'];
		if($queryStatus == 'sent') {
			$resp = "1";
			$queryResp = "1";
		} else if($queryStatus == 'queued' || $queryStatus == 'scheduled') {
			$resp = "1";
			$queryResp = "2";
		} else if($queryStatus == 'rejected' || $queryStatus == 'invalid') {
			$resp = "-1";
			$queryResp = "-1";
		}

		echo $resp . " ~~ " . $adminResp . " ~~ " . $queryResp;
	} catch(Exception $e) {
		$resp = "-1";
		echo $resp;
	}
}


// $result = SendMessage($emails[$j], $emails[$j], $from, $from, $subject, $message);
// $status = $result[0]['status'];
// if($status == 'sent') {
// 	$sent++;
// } else if($status == 'queued' || $status == 'scheduled') {
// 	$queued++;
// } else if($status == 'rejected' || $status == 'invalid') {
// 	$error++;
// }


?>