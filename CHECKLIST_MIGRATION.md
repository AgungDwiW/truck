# Checklist Database Migration

## Overview
The checklist database structure has been refactored to normalize the data and support many-to-many relationships between checklists and checklist parameters.

## Changes Made

### 1. New Tables Created

#### `tbl_checklist_param`
Merged data from `tb_ceklist_utama`, `tb_ceklist_tambahan`, and `tb_ceklist_qa` into a single parameter table.

Columns:
- `id` (PK, auto_increment)
- `param_name` (VARCHAR) - The parameter name
- `param_type` (ENUM: 'utama', 'tambahan', 'qa') - Parameter type
- `name` (VARCHAR) - Short name
- `green_label` (VARCHAR) - Label for "good" condition
- `red_label` (VARCHAR) - Label for "bad" condition  
- `status` (INT) - Active status (default: 1)
- `status_temp` (INT) - Temporary status (default: 0)
- `created_at` (TIMESTAMP)

#### `tbl_checklist`
Simplified version of `tb_ceklist` without parameter columns (`utama1-20`, `tambahan1-20`, `qa1-10`).

Contains only:
- Metadata columns (idref, plant_id, nopol, petugas_pemeriksa, etc.)
- Result columns (hasil_pemeriksaan, warna, komentar_kerusakan, etc.)
- QA result columns (cek_qa, hasil_qa, warna_qa, etc.)

#### `tbl_checklist_detail`
Junction table for many-to-many relationship between checklists and parameters.

Columns:
- `id` (PK, auto_increment)
- `checklist_id` (FK to `tbl_checklist.no`)
- `param_id` (FK to `tbl_checklist_param.id`)
- `value` (INT) - Parameter value
- `created_at` (TIMESTAMP)

Unique constraint: (`checklist_id`, `param_id`) to prevent duplicates.

### 2. Data Migration
- Parameters from `tb_ceklist_utama` migrated to `tbl_checklist_param` with `param_type='utama'`
- Parameters from `tb_ceklist_tambahan` migrated to `tbl_checklist_param` with `param_type='tambahan'`
- Empty `tb_ceklist_qa` table - no parameters migrated
- Checklist data from `tb_ceklist` migrated to `tbl_checklist` (excluding parameter columns)
- Parameter values from `tb_ceklist.utama1-20` migrated to `tbl_checklist_detail` with appropriate param_id mapping

### 3. New Models Created

#### `Checklist` (`application/models/Checklist.php`)
Extends `Table` class for `tbl_checklist` operations.

Key methods:
- `getWithParams($checklist_id)` - Get checklist with parameter values
- `getChecklistParams($checklist_id)` - Get parameters with values for a checklist
- `createWithParams($data, $params)` - Create checklist with parameter values
- `updateWithParams($checklist_id, $data, $params)` - Update checklist and parameters
- `getAllPaginated($page, $per_page)` - Get paginated checklists
- `getByDateRange($start_date, $end_date)` - Get checklists by date range
- `getStats()` - Get inspection statistics

#### `ChecklistParam` (`application/models/ChecklistParam.php`)
Extends `Table` class for `tbl_checklist_param` operations.

Key methods:
- `getByType($type)` - Get parameters by type ('utama', 'tambahan', 'qa')
- `getAllGroupedByType()` - Get all parameters grouped by type
- `getWithUsageCount()` - Get parameters with usage counts
- `getActive($type)` - Get active parameters

## Usage Examples

### 1. Include the Models
```php
// Include the models (autoload already includes Table.php)
include_once APP_DIR . 'models/Checklist.php';
include_once APP_DIR . 'models/ChecklistParam.php';
```

### 2. Create a New Checklist with Parameters
```php
$checklist = new Checklist();

// Checklist metadata
$checklistData = [
    'idref' => 'TRUCK-001',
    'plant_id' => 'PLANT01',
    'nopol' => 'B1234XYZ',
    'petugas_pemeriksa' => 'John Doe',
    'tujuan_kirim' => 'Warehouse A',
    // ... other fields
];

// Parameter values: [param_id => value]
$paramValues = [
    1 => 1,  // utama1 = good
    2 => 0,  // utama2 = bad
    3 => 1,  // utama3 = good
    // ... other parameters
];

// Create checklist with parameters
$checklistId = $checklist->createWithParams($checklistData, $paramValues);
```

### 3. Get Checklist with Parameters
```php
$checklist = new Checklist();
$checklistData = $checklist->getWithParams(123); // 123 = checklist_id

// Structure:
// $checklistData contains:
// - All checklist metadata fields
// - 'parameters' array with parameter details and values
```

### 4. Update Checklist and Parameters
```php
$checklist = new Checklist();

// Update checklist metadata
$updateData = [
    'hasil_pemeriksaan' => 'Layak',
    'warna' => 'Hijau'
];

// Update parameter values
$paramUpdates = [
    1 => 1,  // Set utama1 to 1
    2 => null, // Remove value for utama2
    32 => 0   // Set tambahan1 to 0
];

$checklist->updateWithParams(123, $updateData, $paramUpdates);
```

### 5. Get Parameters by Type
```php
$paramModel = new ChecklistParam();
$utamaParams = $paramModel->getByType('utama');
$tambahanParams = $paramModel->getByType('tambahan');
```

### 6. Get Statistics
```php
$checklist = new Checklist();
$stats = $checklist->getStats('2024-01-01', '2024-12-31');
// Returns count of checklists by hasil_pemeriksaan
```

## Migration Notes

### Parameter ID Mapping
- `utama1` → `param_id` = 1
- `utama2` → `param_id` = 2
- ...
- `utama20` → `param_id` = 20
- `tambahan1` → `param_id` = 32
- `tambahan2` → `param_id` = 33
- ...
- `tambahan10` → `param_id` = 41

### Backward Compatibility
To maintain backward compatibility during migration:

1. Keep old tables (`tb_ceklist`, `tb_ceklist_utama`, `tb_ceklist_tambahan`, `tb_ceklist_qa`) for reference
2. Update application code gradually to use new models
3. Create views or triggers if needed for legacy code

### Next Steps for Full Migration
1. Update all PHP files that reference old tables
2. Replace direct SQL queries with model method calls
3. Remove old tables after confirming new structure works correctly
4. Update reports and analytics to use new structure

## Model Dependencies
- Both models extend `Table` class from `application/library/model/Table.php`
- Require database connection to be established (global `$con`)
- Use the same query builder pattern as existing `Table` class

## Benefits of New Structure
1. **Normalized data** - No repetitive columns (utama1-20, etc.)
2. **Flexible parameters** - Easy to add/remove parameters without schema changes
3. **Better querying** - Can query parameter values more efficiently
4. **Scalable** - Supports unlimited parameters per checklist
5. **Maintainable** - Clean separation between checklist metadata and parameter values