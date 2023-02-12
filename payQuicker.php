<?php
/**
 * This class is sending commission response
 */
class Quicker {
    /*constant values declaration*/
    public $headers_REST;
    public $payQuickerUrl;
    public $payBaseUrl;
    public $userName;
    public $password;
    public $fundingAccountId;

    /*set constant values*/
    public function setValues($connection, $payQuickerTokenUrl, $payBaseUrl, $userName, $password, $fundingAccountId ){
        $this->connection = $connection;
        $this->payQuickerTokenUrl = $payQuickerTokenUrl;
        $this->payBaseUrl = $payBaseUrl;
        $this->userName = $userName;
        $this->password = $password;
        $this->fundingAccountId = $fundingAccountId;
    }
    /*Get access token*/
    public function getAccessToken(){
        $base64Encode = base64_encode($this->userName.':'.$this->password);
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->payQuickerTokenUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=api%20useraccount_balance%20useraccount_debit%20useraccount_payment%20useraccount_invitation',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.$base64Encode.'',
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));
        //One hour valid for token 
        $response = curl_exec($curl);
        curl_close($curl);     

        session_start();
        $_SESSION["token"] = $response;  

        // Initial Token time is stored in a session variable
        $_SESSION["accessTimeStamp"] = time();
        return $response;

    }

    public function sendPayment(){
        session_start();
        if(time()-$_SESSION["accessTimeStamp"] >3600) {
            session_unset();
            session_destroy();
            $token = $this->getAccessToken();
        }else {
            $token = $_SESSION["token"];
        }

        $tokens = json_decode($token);
        // echo $tokens->access_token;
        // exit;
        $this->input = file_get_contents('php://input');
        $postDatas = $this->input;
        $data = json_decode($postDatas, true);
        $amount = "";
        if($data['amount']!=0){
            $amount = $data['amount'];
        }
        $userCompanyAssignedUniqueKey = $data['userCompanyAssignedUniqueKey'];
        $notificationEmail = $data['userNotificationEmailAddress'];
        $accountingId = $data['accountingId'];
        $payments = array(
            "fundingAccountPublicId" => "$this->fundingAccountId",
                "monetary" => array(
                    "amount"=> "$amount"
                ),
            "userCompanyAssignedUniqueKey" => "$userCompanyAssignedUniqueKey",
            "userNotificationEmailAddress"=> "$notificationEmail",
            "accountingId" => "$accountingId",
            "recipientUserLanguageCode" => "en-us",
            "issuePlasticCard"=> false
        );
        $postDatas =  json_encode($payments, true);
        $datas = '{
                    "payments":[
                        '.$postDatas.'
                    ]
                }';
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->payBaseUrl.'companies/accounts/payments',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $datas,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$tokens->access_token,
            'Accept: application/json; charset=utf-8',
            'X-MyPayQuicker-Version: 01-15-2018',
            'Content-Type: application/json'
        ),
        ));

        $output = curl_exec($curl);
        $returnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($returnCode==200){
            $response['status'] = true;
            $response['code'] = $returnCode;
            $response['message'] = "Payment Success";
            $response['data'] =  json_decode($output);
        } else {
            $response['status'] = false;
            $response['code'] = $returnCode;
            $response['message'] = "Payment Failed";
            $response['data'] =  json_decode($output);
        }
        
        echo json_encode($response);
    }

    public function sendInvitations(){
        session_start();
        if(time()-$_SESSION["accessTimeStamp"] >3600) {
            session_unset();
            session_destroy();
            $token = $this->getAccessToken();
        }else {
            $token = $_SESSION["token"];
        }

        $tokens = json_decode($token);
        //echo $tokens->access_token;
        // exit;
        
        $this->input = file_get_contents('php://input');
        $postDatas = $this->input;
        $data = json_decode($postDatas, true);
        $userCompanyAssignedUniqueKey = $data['userCompanyAssignedUniqueKey'];
        $notificationEmail = $data['userNotificationEmailAddress'];
        $curl = curl_init();
        $invitations = array(
            "fundingAccountPublicId" =>  "$this->fundingAccountId",
            "userCompanyAssignedUniqueKey"=> "$userCompanyAssignedUniqueKey",
            "userNotificationEmailAddress"=> "$notificationEmail"
        );
        $invitationsDatas =  json_encode($invitations, true);  
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->payBaseUrl.'companies/users/invitations',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $invitationsDatas,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$tokens->access_token,
            'Accept: application/json; charset=utf-8',
            'X-MyPayQuicker-Version: 01-15-2018',
            'Content-Type: application/json'
        ),
        ));

        $output = curl_exec($curl);
        $returnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if($returnCode==201){
            $response['status'] = true;
            $response['code'] = $returnCode;
            $response['message'] = "Sent Invitation Success";
            $response['data'] =  json_decode($output);
        } else {
            $response['status'] = false;
            $response['code'] = $returnCode;
            $response['message'] = "Sent Invitation Failed";
            $response['data'] =  json_decode($output);
        }
        
        echo json_encode($response);

    }

}
?>