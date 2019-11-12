<?php

$ip = $_SERVER['REMOTE_ADDR'];
$identifier = $_GET['macAddr'];
$rating = $_GET['rating'];
$data = json_encode($_GET);
$now = date("Y-m-d H:i:s");

$query = "INSERT INTO data (`from`, `identifier`, `rating`, `data`, `date`)" .
	"SELECT ?, dev.id, ?, ?, now() " .
	"FROM device dev " .
	"WHERE dev.identifier = ?";

if($stmt = $db->prepare($query)) {
	$stmt->bind_param("ssss", $ip, $rating, $data, $identifier);
	$stmt->execute();
} else {
	echo $db->error;
}

echo $db->error;