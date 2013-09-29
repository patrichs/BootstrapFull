<?php

include ("dbClass.php");

$loginUserDB = new dbClass();

$loginUserDB->logout();

header("Location: ../login.html");