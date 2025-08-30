<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /proyecto/login');
  exit;
}

require __DIR__ . '/../../config/config.php'; // conexión $con

$u = trim($_POST['username'] ?? '');
$p = trim($_POST['password'] ?? '');

if ($u === '' || $p === '') {
  header('Location: /proyecto/login?error=1');
  exit;
}

$stmt = $con->prepare("SELECT id_usuario, username, password_hash, nombre FROM usuarios WHERE username = ?");
$stmt->bind_param('s', $u);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();

if ($user && password_verify($p, $user['password_hash'])) {
  session_regenerate_id(true);
  $_SESSION['user']  = $user['username'];
  $_SESSION['name']  = $user['nombre'];
  $_SESSION['uid']   = (int)$user['id_usuario'];
  header('Location: /proyecto/home'); // public/index.php
  exit;
}

header('Location: /proyecto/login?error=1');
exit;
