<?php
include ("dbClass.php");

$array = array();
$getJson = json_decode($_POST["obj"], true);

$createCon = new dbClass();
if ($createCon)
{}
else
{
    $array["typeOf"] = "createObj";
    $array["isSuccess"] = 0;
    $array["replyMessage"] = $createCon->errors;

    echo json_encode($array);
    die();
}

/*
 * If posted command is "reigster"
 */

if (isset($getJson["register"]))
{
    if ($getJson["register"] == 1)
    {
        /*
         * Spara vals i sina egna vars
         */

        $username = $getJson["username"];
        $password = $getJson["password"];
        $email = $getJson["email"];

        $register = $createCon->register($username, $password, $email);
        if ($register)
        {
            $array["typeOf"] = "register";
            $array["isSuccess"] = 1;
            $array["replyMessage"] = $createCon->messages;
        }
        else
        {
            $array["typeOf"] = "register";
            $array["isSuccess"] = 0;
            $array["replyMessage"] = $createCon->errors;
        }
    }
}

echo json_encode($array);