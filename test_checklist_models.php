<?php
/**
 * Test script for new Checklist models
 * 
 * This script demonstrates usage of the new Checklist and ChecklistParam models
 * after database migration.
 */

// Include necessary files
require_once 'application/config/connection.php';
require_once 'application/library/model/Table.php';
require_once 'application/models/Checklist.php';
require_once 'application/models/ChecklistParam.php';

// Check if we have database connection
global $con;
if (!$con) {
    die("Database connection not established.");
}

echo "<h1>Checklist Models Test</h1>";

try {
    // ============================================
    // 1. Test ChecklistParam Model
    // ============================================
    echo "<h2>1. Testing ChecklistParam Model</h2>";
    
    $paramModel = new ChecklistParam();
    
    // Get all parameters grouped by type
    $groupedParams = $paramModel->getAllGroupedByType();
    
    echo "<h3>Parameters Grouped by Type:</h3>";
    foreach ($groupedParams as $type => $params) {
        echo "<h4>Type: " . htmlspecialchars($type) . " (" . count($params) . " parameters)</h4>";
        if (!empty($params)) {
            echo "<ul>";
            foreach ($params as $param) {
                echo "<li>ID: " . $param['id'] . " - " . htmlspecialchars($param['param_name']) . 
                     " (Green: " . htmlspecialchars($param['green_label']) . 
                     ", Red: " . htmlspecialchars($param['red_label']) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No parameters</p>";
        }
    }
    
    // Get parameters by type
    $utamaParams = $paramModel->getByType('utama');
    echo "<h3>Utama Parameters (" . count($utamaParams) . "):</h3>";
    echo "<p>First 5: ";
    foreach (array_slice($utamaParams, 0, 5) as $param) {
        echo $param['id'] . ": " . htmlspecialchars($param['param_name']) . "; ";
    }
    echo "</p>";
    
    // ============================================
    // 2. Test Checklist Model
    // ============================================
    echo "<h2>2. Testing Checklist Model</h2>";
    
    $checklistModel = new Checklist();
    
    // Get all checklists (paginated)
    $checklists = $checklistModel->getAllPaginated(1, 10);
    
    echo "<h3>Checklists (Page 1 of " . $checklists['total_pages'] . "):</h3>";
    echo "<p>Total checklists: " . $checklists['total'] . "</p>";
    
    if (!empty($checklists['data'])) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>idref</th><th>Nopol</th><th>Petugas</th><th>Hasil</th><th>Date</th><th>Actions</th></tr>";
        
        foreach ($checklists['data'] as $cl) {
            echo "<tr>";
            echo "<td>" . $cl['no'] . "</td>";
            echo "<td>" . htmlspecialchars($cl['idref']) . "</td>";
            echo "<td>" . htmlspecialchars($cl['nopol']) . "</td>";
            echo "<td>" . htmlspecialchars($cl['petugas_pemeriksa']) . "</td>";
            echo "<td>" . htmlspecialchars($cl['hasil_pemeriksaan']) . "</td>";
            echo "<td>" . $cl['tgbaca'] . "</td>";
            echo "<td><a href='?action=view&id=" . $cl['no'] . "'>View Details</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No checklists found.</p>";
    }
    
    // ============================================
    // 3. Test Get Checklist with Parameters
    // ============================================
    if (!empty($checklists['data'])) {
        $firstChecklistId = $checklists['data'][0]['no'];
        
        echo "<h3>3. Testing Get Checklist with Parameters (ID: $firstChecklistId)</h3>";
        
        $checklistWithParams = $checklistModel->getWithParams($firstChecklistId);
        
        if ($checklistWithParams) {
            echo "<h4>Checklist Metadata:</h4>";
            echo "<p>ID: " . $checklistWithParams['no'] . ", idref: " . htmlspecialchars($checklistWithParams['idref']) . 
                 ", Nopol: " . htmlspecialchars($checklistWithParams['nopol']) . "</p>";
            
            echo "<h4>Parameters with Values:</h4>";
            if (!empty($checklistWithParams['parameters'])) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>Param ID</th><th>Name</th><th>Type</th><th>Green Label</th><th>Red Label</th><th>Value</th></tr>";
                
                foreach ($checklistWithParams['parameters'] as $param) {
                    echo "<tr>";
                    echo "<td>" . $param['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($param['param_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($param['param_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($param['green_label']) . "</td>";
                    echo "<td>" . htmlspecialchars($param['red_label']) . "</td>";
                    echo "<td>" . ($param['value'] !== null ? $param['value'] : 'NULL') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No parameters found for this checklist.</p>";
            }
        } else {
            echo "<p>Checklist not found.</p>";
        }
    }
    
    // ============================================
    // 4. Test Statistics
    // ============================================
    echo "<h2>4. Testing Statistics</h2>";
    
    $stats = $checklistModel->getStats();
    
    if (!empty($stats)) {
        echo "<h3>Inspection Results:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Result</th><th>Count</th></tr>";
        
        foreach ($stats as $stat) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($stat['hasil_pemeriksaan']) . "</td>";
            echo "<td>" . $stat['count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No statistics available.</p>";
    }
    
    // ============================================
    // 5. Test Creating New Checklist (Demonstration)
    // ============================================
    echo "<h2>5. Demonstration: Creating New Checklist</h2>";
    
    echo "<pre>";
    echo "// Example code to create new checklist:\n";
    echo "\$checklist = new Checklist();\n";
    echo "\n";
    echo "// Checklist data\n";
    echo "\$checklistData = [\n";
    echo "    'idref' => 'TEST-" . date('YmdHis') . "',\n";
    echo "    'plant_id' => 'TEST',\n";
    echo "    'nopol' => 'TEST123',\n";
    echo "    'petugas_pemeriksa' => 'Test User',\n";
    echo "    'tujuan_kirim' => 'Test Location',\n";
    echo "    'jenis_kendaraan' => 'Truck',\n";
    echo "    'tgl_pemeriksaan' => date('Y-m-d'),\n";
    echo "    'jam_pemeriksaan' => date('H:i:s'),\n";
    echo "    'lokasi_pemeriksaan' => 'Test Gate'\n";
    echo "];\n";
    echo "\n";
    echo "// Parameter values (example)\n";
    echo "\$paramValues = [\n";
    echo "    1 => 1,   // utama1 = good\n";
    echo "    2 => 1,   // utama2 = good\n";
    echo "    3 => 0,   // utama3 = bad\n";
    echo "    32 => 1,  // tambahan1 = good\n";
    echo "    33 => 0   // tambahan2 = bad\n";
    echo "];\n";
    echo "\n";
    echo "// Create checklist with parameters\n";
    echo "// \$checklistId = \$checklist->createWithParams(\$checklistData, \$paramValues);\n";
    echo "// echo 'Created checklist ID: ' . \$checklistId;\n";
    echo "</pre>";
    
    echo "<p><em>Note: Actual creation commented out to avoid test data.</em></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<h2>Migration Summary</h2>";
echo "<p>New tables created:</p>";
echo "<ul>";
echo "<li><strong>tbl_checklist_param</strong> - Merged parameters from tb_ceklist_utama, tb_ceklist_tambahan, tb_ceklist_qa</li>";
echo "<li><strong>tbl_checklist</strong> - Simplified checklist without parameter columns</li>";
echo "<li><strong>tbl_checklist_detail</strong> - Many-to-many relationship table</li>";
echo "</ul>";
echo "<p>Models created:</p>";
echo "<ul>";
echo "<li><strong>Checklist</strong> (application/models/Checklist.php)</li>";
echo "<li><strong>ChecklistParam</strong> (application/models/ChecklistParam.php)</li>";
echo "</ul>";
echo "<p>See CHECKLIST_MIGRATION.md for detailed documentation.</p>";

?>