<?php
# Create: DamarTeduh©2021,  HOME 20210310 09:21 AM
// ini_set('memory_limit', '1024M');
// define('AUTH_API_BASEURL', 'https://adop.co.id/api');
define('AUTH_API_BASEURL', 'https://devex.adop.co.id/sandbox_api/');

define('AUTH_SERVER', 'https://devex.adop.co.id/sandbox_api/');
define('RADAR_API_BASEURL', 'https://radar.adop.co.id/api');
define('API_ERR_LOG', $_SERVER['DOCUMENT_ROOT'] . "/zz-ApiErr" . date('Y-m-d') . ".log");

// date_default_timezone_set("Asia/Jakarta");

function GetAccessToken($username, $password)
{
    $url = AUTH_API_BASEURL . '/Login';
    try {
        $loginDto = array(
            "userName" => $username,
            "password" => $password,
            "twoFactorCode" => null,
            "twoFactorRecoveryCode" => null
        );

        $call = new ApiClient();
        $response = $call->ExecuteApi('POST', $url, json_encode($loginDto), null, false, false);
        $responseDto = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response[1]), true);

        $statusCode = get_http_response_code($response[0]);
        if ($statusCode == 200) {
            if (!isset($_SESSION))
                session_start();
            $_SESSION["cTkn"] = $responseDto['accessToken'];
            $_SESSION["cExpTkn"] = date('Y-m-d H:i:s', strtotime(($responseDto['expiresIn']) . ' minute'));
            $_SESSION["cRefTkn"] = $responseDto['refreshToken'];
            return array("Success" => true, "Message" => null);
        }
        return array("Success" => false, "Message" => $responseDto['title'] . (isset($responseDto['detail']) ? (' ' . $responseDto['detail']) : ''));
    } catch (Throwable $th) {
        file_put_contents(API_ERR_LOG, date('H:i:s') . " Get Token: " . $username . "; " . $th->getMessage() . "; " . $url . "; \r\n", FILE_APPEND | LOCK_EX);
        return array("Success" => false, "Message" => $th->getMessage());
    }
}

function RefreshToken()
{
    try {
        $url = AUTH_API_BASEURL . '/Refresh';
        $refreshDto = array("refreshToken" => $_SESSION["cRefTkn"]);
        unset($_SESSION['cTkn']);
        $call = new ApiClient();
        $response = $call->ExecuteApi('POST', $url, json_encode($refreshDto), null, true, false);
        $responseDto = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response[1]), true);
        $statusCode = get_http_response_code($response[0]);
        // print_r($statusCode);
        // print_r($responseDto);
        if ($statusCode == 200) {
            if (!isset($_SESSION))
                session_start();
            // print_r($responseDto);
            $_SESSION["cTkn"] = $responseDto['accessToken'];
            $_SESSION["cExpTkn"] = date('Y-m-d H:i:s', strtotime(($responseDto['expiresIn']) . ' minute'));
            $_SESSION["cRefTkn"] = $responseDto['refreshToken'];
            
            return array("Success" => 1, "Message" => "success");
        }
        else{
            
            return array("Success" => 0, "Message" => $responseDto['title']);
        }
        
    } catch (Throwable $th) {
        return array("Success" => false, "Message" => $th->getMessage());
    }
}

function ApiCall($method, $url, $data, $token = null, $bodyOnly = true)
{
    $call = new ApiClient();
    return $call->ExecuteApi($method, $url, $data, $token, false, $bodyOnly);
}

function get_http_response_code($header)
{
    // $headers = get_headers($response);
    return substr($header, 9, 3);
}

function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

class ApiClient
{
    function ExecuteApi($method, $url, $data, $token, $refreshTkn = false, $bodyOnly = true)
    {
        $tokenExpired = false;

        try {
            if (isset($_SESSION)) {
                if (isset($_SESSION["cExpTkn"])) {
                    if (date('Y-m-d H:i:s') > $_SESSION["cExpTkn"])
                        $tokenExpired = true;
                }
            }

            if ($tokenExpired && !$refreshTkn)
                RefreshToken();
            if (!isset($_SESSION))
                session_start();
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

            // extract body
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            curl_close($curl);

            // print_r($header);
            // print_r($body);

            if ($bodyOnly)
                return $body;
            return [$header, $body];
        } catch (Throwable $th) {
            file_put_contents(API_ERR_LOG, date('H:i:s') . " Api Call: " . $th->getMessage() . "; " . $url . "; " . $data . "\r\n", FILE_APPEND | LOCK_EX);
            return array("Success" => false, "Message" => $th->getMessage());
        }
    }
}
