<?php
include ("dbClass.php");

$checkAuth = new dbClass();

$checkAuth->checkAuthentication();

if (!$checkAuth)
{
    header("Location: login.html");
    die();
}
else
{
    $checkAuth->messages = "Login success";
}