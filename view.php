<?php

$dateYesterday = date("Y-m-d", time() - 60*60*24);
$dateToday = date("Y-m-d");

$query = "SELECT distinct date(date) as day, SEC_TO_TIME(FLOOR((TIME_TO_SEC(date)+300)/600)*600) as `time`, rating, count(SEC_TO_TIME(FLOOR((TIME_TO_SEC(date)+300)/600)*600)) as `count`" .
		"from data " .
		"where date >= ? " .
		"group by SEC_TO_TIME(FLOOR((TIME_TO_SEC(date)+300)/600)*600), rating";

if($stmt = $db->prepare($query)) {
	$stmt->bind_param("s", $dateYesterday);
	$stmt->execute();
	$res = $stmt->get_result();
} else {
	echo $db->error;
}

$data = array();

while($row = $res->fetch_assoc()) {
	if(!isset($data[$row['day']])) {
		$data[$row['day']] = array();
	}

	if(!isset($data[$row['day']][$row['time']])) {
		$data[$row['day']][$row['time']] = array(1 => 0, 2 => 0, 3 => 0);
	}

	$data[$row['day']][$row['time']][$row['rating']] = $row['count'];
}

$data = isset($data[$dateToday]) ? $data[$dateToday] : $data[$dateYesterday];

echo "<table border='1'><thead><tr><td>Time</td><td>😊</td><td>😐</td><td>😟</td></tr></thead>";
foreach ($data as $time => $values) {
	echo "<tr>";
	echo "<td>" . $time . "</td>";
	echo "<td>" . $values[1] . "</td>";
	echo "<td>" . $values[2] . "</td>";
	echo "<td>" . $values[3] . "</td>";
	echo "</tr>";
}

