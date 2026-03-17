<?php
/**
 * Checklist Model
 * Extends Table for tbl_checklist operations
 */
class Checklist extends Table {
    
    /**
     * Constructor
     * @param string $dbname Optional database name
     * @param mixed $conn Optional connection
     */
    public function __construct($dbname = '', $conn = '') {
        parent::__construct('tbl_checklist', $dbname, $conn);
    }
    
    /**
     * Get checklist with parameters
     * @param int $checklist_id
     * @return array
     */
    public function getWithParams($checklist_id) {
        $checklist = $this->get()->where(['no' => $checklist_id])->fetchOne();
        if (!$checklist) {
            return null;
        }
        
        // Get parameters details
        $params = $this->getChecklistParams($checklist_id);
        $checklist['parameters'] = $params;
        
        return $checklist;
    }
    
    /**
     * Get checklist parameters with values
     * @param int $checklist_id
     * @return array
     */
    public function getChecklistParams($checklist_id) {
        $paramTable = new Table('tbl_checklist_param');
        $detailTable = new Table('tbl_checklist_detail');
        
        // Get all parameters with values for this checklist
        $params = $paramTable->get()
            ->join('tbl_checklist_detail', [
                ['tbl_checklist_param.id', 'tbl_checklist_detail.param_id']
            ], 'LEFT')
            ->where([
                'tbl_checklist_detail.checklist_id' => $checklist_id
            ])
            ->fetchAll();
            
        // If no details found, return empty parameters
        if (empty($params)) {
            $params = $paramTable->get()->fetchAll();
            // Add null values
            foreach ($params as &$param) {
                $param['value'] = null;
            }
        }
        
        return $params;
    }
    
    /**
     * Create new checklist with parameters
     * @param array $data Checklist data
     * @param array $params Parameter values [param_id => value]
     * @return int|false Checklist ID or false on failure
     */
    public function createWithParams($data, $params = []) {
        // Start transaction if supported
        // For now, we'll do sequential inserts
        
        // Insert checklist
        $checklist_id = $this->insert($data)->execute();
        
        if (!$checklist_id) {
            return false;
        }
        
        // Insert parameter values
        $detailTable = new Table('tbl_checklist_detail');
        foreach ($params as $param_id => $value) {
            if ($value !== null) {
                $detailTable->insert([
                    'checklist_id' => $checklist_id,
                    'param_id' => $param_id,
                    'value' => $value
                ])->execute();
            }
        }
        
        return $checklist_id;
    }
    
    /**
     * Update checklist with parameters
     * @param int $checklist_id
     * @param array $data Checklist data
     * @param array $params Parameter values [param_id => value]
     * @return bool
     */
    public function updateWithParams($checklist_id, $data, $params = []) {
        // Update checklist
        if (!empty($data)) {
            $this->update($data)->where(['no' => $checklist_id])->execute();
        }
        
        // Update parameter values
        if (!empty($params)) {
            $detailTable = new Table('tbl_checklist_detail');
            foreach ($params as $param_id => $value) {
                if ($value === null) {
                    // Delete if null
                    $detailTable->delete()->where([
                        'checklist_id' => $checklist_id,
                        'param_id' => $param_id
                    ])->execute();
                } else {
                    // Upsert
                    $detailTable->upsert(
                        [
                            'checklist_id' => $checklist_id,
                            'param_id' => $param_id,
                            'value' => $value
                        ],
                        ['value']  // Update value on duplicate
                    )->execute();
                }
            }
        }
        
        return true;
    }
    
    /**
     * Get all checklists with pagination
     * @param int $page
     * @param int $per_page
     * @return array
     */
    public function getAllPaginated($page = 1, $per_page = 20) {
        $offset = ($page - 1) * $per_page;
        
        $checklists = $this->get()
            ->order('tgbaca DESC')
            ->limit($offset, $per_page)
            ->fetchAll();
            
        // Get count
        $count = $this->get()->fetchAll();
        $total = count($count);
        
        return [
            'data' => $checklists,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ];
    }
    
    /**
     * Get checklists by date range
     * @param string $start_date YYYY-MM-DD
     * @param string $end_date YYYY-MM-DD
     * @return array
     */
    public function getByDateRange($start_date, $end_date) {
        return $this->get()
            ->where("DATE(tgbaca) BETWEEN '{$start_date}' AND '{$end_date}'")
            ->order('tgbaca DESC')
            ->fetchAll();
    }
    
    /**
     * Get statistics
     * @param string $start_date Optional
     * @param string $end_date Optional
     * @return array
     */
    public function getStats($start_date = null, $end_date = null) {
        $where = '1=1';
        if ($start_date && $end_date) {
            $where = "DATE(tgbaca) BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        $stats = $this->get()
            ->where($where)
            ->group('hasil_pemeriksaan')
            ->get('hasil_pemeriksaan', 'COUNT(*) as count')
            ->fetchAll();
            
        return $stats;
    }
}
?>