<?php
include_once "../shop/database.php";
include_once "payQuicker.php";
include "constantsPayQuicker.php";

$quicker = new Quicker();

$quicker->setValues($connection, $payQuickerTokenUrl, $payBaseUrl, $userName, $password, $fundingAccountId);
$action = $_REQUEST['action'];
switch($action){
    case "access-token":
        $quicker->getAccessToken();
        break;
    case "send-payment":
        $quicker->sendPayment();
        break;
    case "send-invitation":
        $quicker->sendInvitations();
        break;
    default:
        $helper->finalResponse(false,404,"No Results");
        break;
}
?>