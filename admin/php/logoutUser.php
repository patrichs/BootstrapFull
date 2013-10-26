<?php

require("checkAuth.php");

$loginUserDB = new dbClass();

$logout = $loginUserDB->logout();

if ($logout)
header("Location: ../login.html");