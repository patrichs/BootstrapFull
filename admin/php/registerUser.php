<?php

include ("dbClass.php");

$loginUserDB = new dbClass();

$loginUserDB->register("admin", "0159341b", "admin@patrich.info");

echo $loginUserDB->errors . "<br>" . $loginUserDB->messages;