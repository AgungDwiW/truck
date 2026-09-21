<?php
class ApiModel{
    public static $url     = '';
    public static $DICT    = '';
    
    
    public static function RefreshToken()
    {
        try {
            $url = AUTH_SERVER . 'Refresh';
            $refreshDto = array("refreshToken" => $_SESSION["cRefTkn"]);
            unset($_SESSION['cTkn']);
            $data           = self::ApiCall("POST", $url, json_encode($refreshDto),  false, false);
            
            $responseDto = json_decode($data[1],1);
            $statusCode = $data[2];
            // exit();
            if ($statusCode == 200) {
                if (!isset($_SESSION))
                    session_start();
                $_SESSION["cTkn"] = $responseDto['accessToken'];
                $_SESSION["cExpTkn"] = date('Y-m-d H:i:s', strtotime(($responseDto['expiresIn']) . ' minute'));
                $_SESSION["cRefTkn"] = $responseDto['refreshToken'];
                
                return array("Success" => 1, "Message" => "success");
            }
            else{
                User::unsetSession();
                echo"<script type='text/javascript'>alert('Session sudah habis, perubahan data pada sistem yang dilakukan sebelumnnya belum tersimpan. Mohon log in kembali dan lakukan perubahan kembali.');window.location.href='login.php'</script>";
                exit;
            }
            
        } catch (Throwable $th) {
            return array("Success" => false, "Message" => $th->getMessage());
        }
    }
    public static function  ApiCall($method, $url, $data, $bodyOnly = true, $checkToken=true)
    {
        $tokenExpired = false;

        try {
            if (isset($_SESSION)) {
                if (isset($_SESSION["cExpTkn"])) {
                    if (date('Y-m-d H:i:s') > $_SESSION["cExpTkn"])
                        $tokenExpired = true;
                }
            }
            if ($tokenExpired && $checkToken)
                self::RefreshToken();
           
            if (empty($token) && isset($_SESSION["cTkn"]))
                $token = $_SESSION["cTkn"];

            $curl = curl_init();
            switch ($method) {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);
                    if ($data)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    if ($data)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    break;
                default:
                    if ($data && is_array($data))
                        $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            // OPTIONS:
            if (!empty($token))
                $header = array('Content-Type: application/json', 'Authorization: Bearer ' . $token);
            else
                $header = array('Content-Type: application/json');
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            // EXECUTE:
            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'cURL Error: ' . curl_error($curl);
            }

            if (!$response) {
                die("Api Connection Failure");
            }

             // EXTRACT HEADER & BODY
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $headerResp = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            // print_r($headerResp);
            // print_r($body);

            if ($bodyOnly)
                return $body;

            return [$headerResp, $body, $statusCode];
        } catch (Throwable $th) {
            // file_put_contents(API_ERR_LOG, date('H:i:s') . " Api Call: " . $th->getMessage() . "; " . $url . "; " . $data . "\r\n", FILE_APPEND | LOCK_EX);
            // Debuger::dump($th->getMessage(),1);
            // exit();
            return array("Success" => false, "Message" => $th->getMessage());
        }
    }



    public static function logPostRequest($url, $params, $response, $statusCode) {
        $month = date('Y-m');
        $date = date('Y-m-d');
        $timestamp = date('Y-m-d H:i:s');
        
        // Ensure we handle cases where User::$username might not be set yet
        $username = isset(User::$username) ? User::$username : 'Unknown';

        // Prepare the structured log message
        $logMessage = "[{$timestamp}] Initiated by User: {$username}" . PHP_EOL;
        $logMessage .= "URL: {$url}" . PHP_EOL;
        $logMessage .= "Params: " . (is_string($params) ? $params : json_encode($params)) . PHP_EOL;
        $logMessage .= "Response: " . (is_string($response) ? $response : json_encode($response)) . PHP_EOL;
        $logMessage .= "Status Code: {$statusCode}" . PHP_EOL;
        $logMessage .= str_repeat("-", 80) . PHP_EOL;

        // --- 1. Log to the primary API directory (Every request) ---
        $apiLogDir = APP_DIR . "logs/API/{$month}";
        $apiLogFile = $apiLogDir . "/{$date}.log";

        if (!is_dir($apiLogDir)) {
            mkdir($apiLogDir, 0755, true);
        }
        file_put_contents($apiLogFile, $logMessage, FILE_APPEND);

        // --- 2. Log to API_FAIL directory (If request fails) ---
        // Assuming a non-200 status code constitutes a failure
        if ($statusCode != 200) {
            $failLogDir = APP_DIR . "logs/API_FAIL/{$month}";
            $failLogFile = $failLogDir . "/{$date}.log";

            if (!is_dir($failLogDir)) {
                mkdir($failLogDir, 0755, true);
            }
            file_put_contents($failLogFile, $logMessage, FILE_APPEND);
        }
    }
    public static function get($params, $url = null, $bodyOnly = true){
        
        if (is_null($url))
            $url = static::$url;
        
        $q              = $url . "?" . http_build_query($params);
        
        $data           = self::ApiCall("GET", $q, null,  false);
		$status         = $data[2];
        $data           = json_decode($data[1],1);
        Debuger::dump(["param" => $params, "url" => $q, "response" => $data, "status"=> $status]);
        // Debuger::dump([$params, $q, $data],1);
        if (is_null($data))
            $data = [];
        if ($bodyOnly)
            return $data;
        else 
            return ["data"=>$data, "status"=>$status];
    }
    public static function post($params, $url = null, $bodyOnly = true){
        if (is_null($url))
            $url = static::$url;
        if (is_array($params))
            $params = json_encode($params);
        
        $data           = self::ApiCall("POST", $url, $params,  false);
        Debuger::dump(["param" => json_decode($params,1), "url" => $url, "response" => json_decode($data[1],1), "status"=> $data[2], "json payload" => $params]);
        
        $status         = $data[2];
        $responseDecoded = json_decode($data[1],1);
        
        // Log every POST request (handles both success and failure internally)
        static::logPostRequest($url, $params, $responseDecoded, $status);

        // Reassign back to $data to maintain your existing application flow
        $data = $responseDecoded;

        if ($status == 429){
            $data = ['title'=>"Too many request, please wait several second before retrying", 'body'=>''];
        }
        if ($status == 401){
            $data = ['title'=>"unauthorized", 'body'=>''];
            User::unsetSession();
            echo"<script type='text/javascript'>alert('Session sudah habis, perubahan data pada sistem yang dilakukan sebelumnnya belum tersimpan. Mohon log in kembali dan lakukan perubahan kembali.');window.location.href='login.php'</script>";
            exit;
        }
        
        if ($bodyOnly)
            return $data;
        else 
            return ["data"=>$data, "status"=>$status];
    }


    


}