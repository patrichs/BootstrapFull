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
 * If the Json posted is command "infopls"
 */

if (isset($getJson["infopls"]))
{
    if ($getJson["infopls"] == 1)
    {
        $check = $createCon->checkAuthentication();
        if (!$check)
        {
            $array["typeOf"] = "authInfo";
            $array["isSuccess"] = 0;
            $array["replyMessage"] = $createCon->errors;
        }
        else
        {
            $array["typeOf"] = "authInfo";
            $array["isSuccess"] = 1;
            $array["replyMessage"] = $check;
        }
    }
}

echo json_encode($array);