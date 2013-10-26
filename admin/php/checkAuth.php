<?php
require_once("dbClass.php");

$checkAuth = new dbClass();

$checkVar = $checkAuth->checkAuthentication();

if (!$checkVar)
{
    header("Location: login.html");
    die();
}
else
{
    $checkAuth->messages = "User is authenticated.";
}