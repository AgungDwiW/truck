<?php
include_once "application/config/connection.php";
class tbm_hptiga_distributor extends Table{
    public $listParam       = [];
    public $table_name      = '';
    public $signaturePath   = '';
    public $mDist           = [];
    public $mDistDnTipe     = [];
    public $mDistShort      = [];
    
    function __construct( ){
        global $con;
        global $controller;
        $this->db_name    = "dbhptiga_vit";
        $this->con 		  = $con;
        $this->table_name = 'tbm_hptiga_distributor';
        
	}
    
    function getMaster(){
        $data = parent::get()->fetchAll();
        foreach($data as $r){
            $this->mDist[$r['distributor_id']]="{$r['distributor_name']} - {$r['distributor_id']}";
            $this->mDistDnTipe[$r['distributor_id']]=$r['tipe'];
            $this->mDistShort[$r['distributor_id']]=$r['short_name'];
        }
        return $this;
    }

    function getMasterReg(){
        $reg = User::$region;
        $data = parent::get()->where("reg like '%{$reg}%' and aktif='Y'")->fetchAll();
        foreach($data as $r){
            $this->mDist[$r['distributor_id']]="{$r['distributor_name']} - {$r['distributor_id']}";
            $this->mDistDnTipe[$r['distributor_id']]=$r['tipe'];
            $this->mDistShort[$r['distributor_id']]=$r['short_name'];
        }
        return $this;
    }
    
}

?>
