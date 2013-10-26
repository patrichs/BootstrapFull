<?php
require("checkAuth.php");

$oldPassword = $_POST["oldPassword"];
$newPassword = $_POST["newPassword"];

$changePassword = new dbClass();

$changePassword->changeUserPassword($oldPassword, $newPassword);