<?php
// Inside the controller or file handling route("ajax")
include "application/config/connection.php";

// header('Content-Type: application/json');

$type      = $_POST['type'] ?? '';
$searchKey = $_POST['searchKey'] ?? '';
$siteId    = $_POST['siteId'] ?? '';
$skip      = (int)($_POST['skip'] ?? 0);
$take      = (int)($_POST['take'] ?? 30);

// --- 1. HANDLE SUPPLIER SEARCH ---
if ($type === 'supplier') {
    $payload = [
        "VendorNameKey" => $searchKey,
        "Skip"      => $skip,
        "Take"      => $take
    ];
    
    $url = API_SERVER . "Vendors?". http_build_query($payload);
    $response = ApiCall("GET", $url, null);
    
    // Decode the response to filter out duplicates based on vendorNumber
    $data = json_decode($response, true);
    if (is_array($data)) {
        $uniqueData = [];
        $seenIds = [];
        
        foreach ($data as $item) {
            $id = $item['vendorId'] ?? null;
            // Only add to the final array if the ID exists and hasn't been seen yet
            if ($id !== null && !isset($seenIds[$id])) {
                $seenIds[$id] = true;
                $uniqueData[] = $item;
            }
        }
        echo json_encode($uniqueData);
    } else {
        // Fallback in case the API returns an error message instead of an array
        echo $response; 
    }
    exit;
}

// --- 2. HANDLE TRANSPORTER SEARCH ---
elseif ($type === 'transporter') {
    
    // Build the query string using the parameters from your API documentation
    $queryParams = http_build_query([
        'TransporterNameKey' => $searchKey,
        'IsActive'           => 'true',
        'Skip'               => $skip,
        'Take'               => $take
    ]);
    
    // Append the query parameters to the URL
    $url = API_SERVER . "Transporters?" . $queryParams;
    
    // Execute the GET request
    $response = ApiCall("GET", $url, "");
    
    // Decode the response to filter out duplicates based on transporterId
    $data = json_decode($response, true);
    if (is_array($data)) {
        $uniqueData = [];
        $seenIds = [];
        
        foreach ($data as $item) {
            $id = $item['transporterId'] ?? null;
            // Only add to the final array if the ID exists and hasn't been seen yet
            if ($id !== null && !isset($seenIds[$id])) {
                $seenIds[$id] = true;
                $uniqueData[] = $item;
            }
        }
        echo json_encode($uniqueData);
    } else {
        // Fallback in case the API returns an error message instead of an array
        echo $response;
    }
    exit;
}
else if ($type =='customer' ){
     $payload = [
        "CustomerNameKey" => $searchKey,
        'IsActive'           => 'true',
        "Skip"      => $skip,
        "Take"      => $take
    ];
    
    $url = API_SERVER . "Customers?". http_build_query($payload);
    $response = ApiCall("GET", $url, null);
    // Debuger::dump($response,1);
    // Decode the response to filter out duplicates based on vendorNumber
    $data = json_decode($response, true);
    if (is_array($data)) {
        $uniqueData = [];
        $seenIds = [];
        
        foreach ($data as $item) {
            $id = $item['customerId'] ?? null;
            // Only add to the final array if the ID exists and hasn't been seen yet
            if ($id !== null && !isset($seenIds[$id])) {
                $seenIds[$id] = true;
                $uniqueData[] = $item;
            }
        }
        echo json_encode($uniqueData);
    } else {
        // Fallback in case the API returns an error message instead of an array
        echo $response; 
    }
    exit;
}
// Return empty array if type doesn't match
echo json_encode([]);
exit;
?>