<?php
require "config.php";
$db = new mysqli("localhost", $db_user, $db_pass, $db_name);

if(isset($_GET['rating'])) {
	require "save_rating.php";

	return;
}

require "view.php";
