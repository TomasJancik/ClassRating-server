<?php
require "config.php";
$db = new mysqli("localhost", $db_user, $db_pass, $db_name);

$ip = "my ip";
$identifier = "xxx";
$data = json_encode($_GET);
$now = date("Y-m-d H:i:s");

if($stmt = $db->prepare("INSERT INTO data (`from`, `identifier`, `data`, `date`) VALUES (?, ?, ?, ?)")) {
	$stmt->bind_param("ssss", $ip,$identifier, $data, $now);
	$stmt->execute();
} else {
	echo $db->error;
}

echo $db->error;