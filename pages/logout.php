<?php
include_once '../config/auth.php';

session_start();
$_SESSION = array();
session_destroy();

header("Location: ../index.php?nocache=".time());
exit;

header("Location: ../index.php");
exit;
?>