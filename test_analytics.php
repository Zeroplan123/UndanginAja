<?php
// Simple test script to check analytics endpoint
echo "Testing Analytics Endpoint...\n";

// Test basic analytics controller instantiation
require_once 'vendor/autoload.php';

try {
    // Test if we can access the analytics route
    $url = 'http://localhost:8000/admin/analytics/data?period=7';
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest'
            ]
        ]
    ]);
    
    echo "Testing URL: $url\n";
    
    $result = file_get_contents($url, false, $context);
    
    if ($result === false) {
        echo "ERROR: Could not fetch data from analytics endpoint\n";
        echo "HTTP Response Headers:\n";
        print_r($http_response_header);
    } else {
        echo "SUCCESS: Analytics endpoint responded\n";
        echo "Response: " . substr($result, 0, 200) . "...\n";
        
        $data = json_decode($result, true);
        if ($data) {
            echo "JSON parsed successfully\n";
            echo "Data keys: " . implode(', ', array_keys($data)) . "\n";
        } else {
            echo "ERROR: Invalid JSON response\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
