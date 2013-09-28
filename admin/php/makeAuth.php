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
 * If the Json posted is command "login"
 */

if (isset($getJson["login"]))
{
    if ($getJson["login"] == 1)
    {
        /*
         * Spara vals i sina egna vars
         */

        $username = $getJson["username"];
        $password = $getJson["password"];

        $login = $createCon->login($username, $password);
        if ($login)
        {
            $array["typeOf"] = "login";
            $array["isSuccess"] = 1;
            $array["replyMessage"] = $createCon->messages;
        }

        else
        {
            $array["typeOf"] = "login";
            $array["isSuccess"] = 0;
            $array["replyMessage"] = "FEL " . $createCon->errors;
        }

    }
}

echo json_encode($array);