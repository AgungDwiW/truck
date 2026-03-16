<?php

class Cache {
    public $cacheFolder = '';
    public $cached = [];

    function __construct() {
        $this->cacheFolder = dirname(__FILE__, 2) . "/cache/";
        
        // Ensure the cache directory exists
        if (!is_dir($this->cacheFolder)) {
            mkdir($this->cacheFolder, 0777, true);
        }
    }

    // HELPER: Generates a safe filename to prevent directory traversal hacks
    private function getFilename($name) {
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '', $name);
        return $this->cacheFolder . $safeName . ".json";
    }

    function check($name) {  
        $filename = $this->getFilename($name);

        if (!file_exists($filename)) {
            return false;
        }

        // Read the file once
        $fileContent = file_get_contents($filename);
        if (!$fileContent) {
            return false;
        }

        $payload = json_decode($fileContent, true);

        // Verify the file has our expected cache structure
        if (!isset($payload['timestamp']) || !isset($payload['lifetime'])) {
            return false;
        }

        $now = time();

        // Check expiration (lifetime is in minutes)
        if (($now - $payload['timestamp']) / 60 > $payload['lifetime']) {
            unlink($filename); // Automatically delete the file if it's expired
            return false;
        }

        // Store the valid data in memory so get() doesn't have to read the file again!
        $this->cached[$name] = $payload['data'];
        
        return true;
    }

    function set($name, $data, $lifetime = '30') {
        if ($name == 'master') {
            print_r("cant assign cache with name master");
            return false;
        }

        $filename = $this->getFilename($name);
        
        // Wrap the metadata and actual data into one payload
        $payload = [
            "timestamp" => time(),
            "lifetime"  => (int)$lifetime,
            "data"      => $data
        ];

        // Cache it in memory for the current script run
        $this->cached[$name] = $data;

        // Write to file atomically (prevents file corruption if 2 users hit it at once)
        file_put_contents($filename, json_encode($payload), LOCK_EX);
    }

    function get($name) {
        // Because check() now saves valid data to $this->cached, 
        // we don't need to read the file a second time here!
        if ($this->check($name)) {
            return $this->cached[$name];            
        }
        
        return null;
    }

    function resetCache() {
        // Since there is no master file, resetting means deleting all .json files in the folder
        $files = glob($this->cacheFolder . '*.json');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->cached = [];
    }
}
?>