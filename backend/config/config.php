<?php
$host = "localhost";
$user = "root";
$pass = "Vainilla";
$db   = "auditoria_db";

$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) {
  http_response_code(500);
  die("Connection failed: " . $con->connect_error);
}
$con->set_charset('utf8mb4');