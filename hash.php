<?php
$password = 'password3';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Mot de passe haché : " . $hashed_password . "\n";
?>