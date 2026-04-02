<?php
# Create: DamarTeduh©2021,  HOME 20210310 09:21 AM
include_once "application/config/config.php";
ini_set('memory_limit', '1024M');
define('API_ERR_LOG', $_SERVER['DOCUMENT_ROOT'] . "/zz-ApiErr" . date('Y-m-d') . ".log");

date_default_timezone_set("Asia/Jakarta");

function GetAccessToken($username, $password, $timeZoneOffsetMinutes = 420, $tenantId = null)
{
	$url = AUTH_SERVER.'login';
	try {
		$clientDate = ComposeClientDateTime($timeZoneOffsetMinutes);
		$hostAddress = get_client_ip();

		$loginData = array(
			"Username" => $username,
			"Password" => $password,
			"LocalDateTime" => $clientDate,
			"TenantId" => $tenantId,
			"HostName" => $hostAddress,
			"ModuleName" => "ADOP Main Web",
			"ModuleVersion" => "2023-12-15",
			"AppDirectory" => null
		);

		$call = new ApiClient();
	
		$response = $call->ExecuteApi('POST', $url, json_encode($loginData), null, false, true);
		printpre(['url' => $url, "payload" => $loginData, "response" => $response]);
		$row = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true);
		if (isset($row['accessToken'])) {
			
			if (!isset($_SESSION))
				session_start();
			$_SESSION["cTkn"] = $row['accessToken'];
			$_SESSION["cExpTkn"] = date('Y-m-d H:i:s', strtotime(($row['expiresIn']) . ' minute'));
			$_SESSION["cRefTkn"] = $row['refreshToken'];

			// file_put_contents(API_ERR_LOG, date('H:i:s') .  " : " . $_SESSION["cExpTkn"] .  "; " . "\r\n" , FILE_APPEND | LOCK_EX);
			return array("Success" => true, "Message" => null);
		} else {
			return $row;
		}
		
	} catch (Throwable $th) {
	
		file_put_contents(API_ERR_LOG, date('H:i:s') . " Get Token: " . $username . "; " . $th->getMessage() . "; " . $url . "; \r\n", FILE_APPEND | LOCK_EX);
		return array("Success" => false, "Message" => $th->getMessage());
	}
}

function RefreshToken()
{
	try {
		$url = AUTH_SERVER.'auth/RefreshToken';
		if (!isset($_SESSION))
			session_start();
		$tokenData = array("AccessToken" => $_SESSION["cTkn"], "RefreshToken" => $_SESSION["cRefTkn"]);
		$call = new ApiClient();
		$json = $call->ExecuteApi('POST', $url, json_encode($tokenData), $tokenData['AccessToken'], true);
		$row = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json), true);
		if (isset($row['success'])) {
			if ($row['success']) {
				$_SESSION["cTkn"] = $row['Message']['AccessToken'];
				$_SESSION["cExpTkn"] = date('Y-m-d H:i:s', strtotime(($row['message']['expiredIn']) . ' minute'));
				$_SESSION["cRefTkn"] = $row['Message']['RefreshToken'];
			} else {
				return $row;
			}
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

function ComposeClientDateTime($timeZoneOffsetMinutes)
{
	$sign = $timeZoneOffsetMinutes > 0 ? "+" : "-";
	$hours = str_pad(floor($timeZoneOffsetMinutes / 60), 2, '0', STR_PAD_LEFT); // Get the number of whole hours
	$minutes = str_pad(($timeZoneOffsetMinutes - floor($timeZoneOffsetMinutes / 60) * 60), 2, '0', STR_PAD_LEFT); // Get the remainder of the hours

	$dateTime = new DateTime('now', new DateTimeZone('UTC'));
	$dateTime->add(new DateInterval('PT' . $timeZoneOffsetMinutes . 'M'));
	return $dateTime->format('Y-m-d\TH:i:s') . $sign . $hours . ':' . $minutes;
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
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			// EXECUTE:
			$response = curl_exec($curl);
			// printpre(['url' => $url, "payload" => $data, "response" => $response, "header" => $header],1);
			
			if (!$response) {
				print_r( $url);
				die("Connection Failure");
			}
		
			// extract body
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $headerSize);
			$body = substr($response, $headerSize);

			curl_close($curl);
			if ($bodyOnly)
				return $body;
			return [$header, $body];

		} catch (Throwable $th) {
			file_put_contents(API_ERR_LOG, date('H:i:s') . " Api Call: " . $th->getMessage() . "; " . $url . "; " . $data . "\r\n", FILE_APPEND | LOCK_EX);
			return array("Success" => false, "Message" => $th->getMessage());
		}
	}
}
?>