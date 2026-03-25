<?php

$var = "passer";
$ss = password_hash($var, PASSWORD_DEFAULT);
echo $ss;
?>