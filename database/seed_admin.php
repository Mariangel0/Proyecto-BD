<?php
require __DIR__ . 'config/config.php';

$username = 'admin';
$password = 'admin123';
$nombre   = 'Administrador del sistema';
$email    = 'admin@ejemplo.com';

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $con->prepare("
  INSERT INTO usuarios (username, password_hash, nombre, email)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    password_hash = VALUES(password_hash),
    nombre        = VALUES(nombre),
    email         = VALUES(email)
");
$stmt->bind_param('ssss', $username, $hash, $nombre, $email);
$stmt->execute();

echo $stmt->affected_rows >= 1 ? 'Usuario admin creado/actualizado' : 'Sin cambios';
