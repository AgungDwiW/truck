<?php
// Inside the controller or file handling route("ajax")
include "application/config/connection.php";

header('Content-Type: application/json');

$type      = $_POST['type'] ?? '';
$searchKey = $_POST['searchKey'] ?? '';
$siteId    = $_POST['siteId'] ?? '';
$skip      = (int)($_POST['skip'] ?? 0);
$take      = (int)($_POST['take'] ?? 30);

// --- 1. HANDLE SUPPLIER SEARCH ---
if ($type === 'supplier') {
    $payload = json_encode([
        "searchKey" => $searchKey,
        "siteId"    => $siteId,
        "skip"      => $skip,
        "take"      => $take
    ]);
    
    $url = API_SERVER . "Vendor/QuickSearch";
    $response = ApiCall("POST", $url, $payload);
    
    echo $response;
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
    
    // Since the API handles the filtering and pagination, 
    // we can echo the JSON response directly back to Select2
    echo $response;
    exit;
}

// Return empty array if type doesn't match
echo json_encode([]);
exit;
?>