<?php
/**
 * ChecklistParam Model
 * Extends Table for tbl_checklist_param operations
 */
class ChecklistParam extends Table {
    
    /**
     * Constructor
     * @param string $dbname Optional database name
     * @param mixed $conn Optional connection
     */
    public function __construct($dbname = '', $conn = '') {
        parent::__construct('tbl_checklist_param', $dbname, $conn);
    }
    
    /**
     * Get parameters by type
     * @param string $type utama|tambahan|qa
     * @return array
     */
    public function getByType($type) {
        return $this->get()
            ->where(['param_type' => $type])
            ->order('id ASC')
            ->fetchAll();
    }
    
    /**
     * Get all parameters grouped by type
     * @return array
     */
    public function getAllGroupedByType() {
        $params = $this->get()
            ->order('param_type, id')
            ->fetchAll();
            
        $grouped = [
            'utama' => [],
            'tambahan' => [],
            'qa' => []
        ];
        
        foreach ($params as $param) {
            $type = $param['param_type'];
            if (isset($grouped[$type])) {
                $grouped[$type][] = $param;
            }
        }
        
        return $grouped;
    }
    
    /**
     * Get parameter with usage count
     * @return array
     */
    public function getWithUsageCount() {
        $detailTable = new Table('tbl_checklist_detail');
        
        // We'll do a left join to get count of usage
        $params = $this->get()
            ->joinSQL(
                "SELECT param_id, COUNT(*) as usage_count FROM tbl_checklist_detail GROUP BY param_id",
                "usage",
                [['id', 'param_id']]
            )
            ->fetchAll();
            
        // Ensure usage_count is set
        foreach ($params as &$param) {
            if (!isset($param['usage_count'])) {
                $param['usage_count'] = 0;
            }
        }
        
        return $params;
    }
    
    /**
     * Create multiple parameters
     * @param array $parameters Array of parameter data
     * @return bool
     */
    public function createMultiple($parameters) {
        if (empty($parameters)) {
            return true;
        }
        
        return $this->multiInsert($parameters)->execute();
    }
    
    /**
     * Update parameter status
     * @param int $param_id
     * @param int $status
     * @return bool
     */
    public function updateStatus($param_id, $status) {
        return $this->update(['status' => $status])
            ->where(['id' => $param_id])
            ->execute();
    }
    
    /**
     * Get active parameters
     * @param string $type Optional filter by type
     * @return array
     */
    public function getActive($type = null) {
        $where = ['status' => 1];
        if ($type) {
            $where['param_type'] = $type;
        }
        
        return $this->get()
            ->where($where)
            ->order('param_type, id')
            ->fetchAll();
    }
}
?>