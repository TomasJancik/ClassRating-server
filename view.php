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

echo <<< xxx
<doctype !HTML>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.2/dist/Chart.min.js"></script>
</head>
<body>
xxx;


echo "<table border='1'><thead><tr><td>Time</td><td>😊</td><td>😐</td><td>😟</td></tr></thead>\n";
$rowNum = 0;
foreach ($data as $time => $values) {
	$rowNum++;
	echo "<tr>\n";
	echo "<td>" . $time . "</td>\n";
	echo "<td>" . $values[1] . "</td>\n";
	echo "<td>" . $values[2] . "</td>\n";
	echo "<td>" . $values[3] . "</td>\n";
	echo "<td><canvas id='r" . $rowNum . "'></canvas>\n";
	echo '<script type="text/javascript">' . "\n";
	echo 'var ctx = document.getElementById("r' . $rowNum . '").getContext("2d")' . "\n";
	echo 'var chart = new Chart(ctx, {' . "\n";
	echo "\t" . 'type: "pie",' . "\n";
	echo "\t" . 'data: {labels: ["😊", "😐", "😟"],' . "\n";
	echo "\t\t" . 'datasets: [{' . "\n";
	echo "\t\t\t" . 'data: [' . $values[1] . ', ' . $values[2] . ', ' . $values[3] . '],' . "\n";
	echo "\t\t\t" . 'backgroundColor: ["rgba(80, 220, 100, 1)", "rgba(248, 222, 126, 1)", "rgba(255, 8, 0, 1)"],' . "\n";
	echo "\t}]"; // end of dataset

	echo "}})\n";
	echo "</script>\n";

	echo "</td>\n";
	echo "</tr>\n";
}

echo <<< xxx
</body>
</html>
xxx;


