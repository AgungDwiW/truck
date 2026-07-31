<?php

class ApiModel{
    public static $url     = '';
    public static $DICT    = '';
    public static function logError($responseData, $paramData) {
        // Format dates: mm-yy and dd-mm-yy
        $monthYear = date('m-y');
        $dayMonthYear = date('d-m-y');
        
        // Define directory and file paths using __DIR__ (current file's directory)
        $logDir = __DIR__ . "/log/{$monthYear}";
        $logFile = $logDir . "/{$dayMonthYear}.log";

        // Create the directory recursively if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Prepare the log entry with a timestamp and the data
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] ERROR in :" . PHP_EOL;
        $logMessage .= "Params sent: " . json_encode($paramData) . PHP_EOL;
        $logMessage .= "Error response: " . json_encode($responseData) . PHP_EOL;
        $logMessage .= str_repeat("-", 60) . PHP_EOL;

        // Append to the log file (creates the file if it doesn't exist)
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    public static function get($params, $url = null){
        
        if (is_null($url))
            $url = static::$url;
        
        $q              = $url . "?" . http_build_query($params);
        
        $data           = ApiCall("GET", $q, null, null, false);
		$status         = $data[2];
        $data           = json_decode($data[1],1);
        Debuger::dump(["param" => $params, "url" => $q, "response" => $data, "status"=> $status]);
        // Debuger::dump([$params, $q, $data],1);
        if (is_null($data))
            $data = [];
        return $data;
    }
    public static function post($params, $url = null){
        if (is_null($url))
            $url = static::$url;
        if (is_array($params))
            $params = json_encode($params);
        
        $data           = ApiCall("POST", $url, $params, null, false);
        Debuger::dump(["param" => json_decode($params,1), "url" => $url, "response" => json_decode($data[1],1), "status"=> $data[2], "json payload" => $params]);
        $status         = $data[2];
        $data           = json_decode($data[1],1);
        if ($status != 200)
            static::logError($data, $params);
        return $data;
		

    }


    


}