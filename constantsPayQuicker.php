<?php
if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'bemodonew.wpengine.com' || $_SERVER['HTTP_HOST'] == 'bemodostg.wpengine.com'){
    //Sandbox credentials  
    //Pay Quicker URL

    $payQuickerTokenUrl = "https://identity.mypayquicker.build/core/connect/token";

    //Pay Base Url 
    $payBaseUrl = "https://platform.mypayquicker.build/api/v1/";
    // Username and Password
    $userName = '955ece6d89c4426fbaf69a2109ea058174a902be69ad4420a12cd7d6d63349c4';
    $password = '6a350f92d43145f8a03f02d860e5538df1fb31a094834e0cb64c18f00b36ace3';

    $fundingAccountId = "edf681a65df74cb9be64d9ebbd2c6f0d";
} else {
    //Live credentials  
    //Pay Quicker URL

    $payQuickerTokenUrl = "https://identity.mypayquicker.com/core/connect/token";

    //Pay Base Url 
    $payBaseUrl = "https://platform.mypayquicker.com/api/v1/";
    // Username and Password
    $userName = '94bfbdd245f4439b8b18043cc8c7e228f4d1dc9d1d694fc7aece1e317d28abe2';
    $password = '054a3b88595f4896a0cac873d8416d2f4b1905e0ddbb4171a25eaf6d29d5d8d1';

    $fundingAccountId = "a03fddde31434dfa8cd53dadddd73080";
}


?>