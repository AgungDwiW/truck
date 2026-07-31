<?php
include_once "application/config/connection.php";
class DistributorDepo extends ApiModel{
    public static $listParam       = [];
    public static $mDist           = [];
    public static $mDistDnTipe     = [];
    public static $mDistShort      = [];
    public static $mDepo           = [];
    public static $mDistDepo     = [];
    public static $mDepoDist      = [];
    public static $dataDist      = [];
    public static $url     = API_SERVER . 'Customers';
  
    
    public static function initMaster(){

        $params = ['IsActive' => "true", "Skip" => 0 , "Take" =>"1000", ];
        // printpre($params,1);
        $result = ApiCall("GET", API_SERVER . "Customers?". http_build_query($params), null );
        $result = json_decode($result,1);
        $allDataDist = [];
        $mDist = [];
        $mDistShort = [];
        $mDistDnTipe = [];
        $mDepoDist  = [];
        $mDistDepo  = [];
        foreach($result as $row){
            $tipe = "DIST";
            if ($row['partnerFunctionId'] == "SH"){
                $tipe = "DEPO";
                DistributorDepo::$mDepo[$row['partnerNumber']] = "{$row['partnerNumber']} - {$row['firstName']}";
                DistributorDepo::$mDistDepo[$row['billToParty']][] = ['depo_id' => $row['partnerNumber'], "depo_name" =>$row['firstName']];
                DistributorDepo::$mDepoDist[$row['partnerNumber']] = $row['billToParty'];
            
            }
            DistributorDepo::$mDist[$row['partnerNumber']] = "{$row['partnerNumber']} - {$row['firstName']}";
            DistributorDepo::$mDistShort[$row['partnerNumber']] = "{$row['shortName']}";
            DistributorDepo::$mDistDnTipe[$row['partnerNumber']] = $tipe;
        }
    }

    public static function initMasterReg(){
        $REG = str_replace("REG-","", User::$region);
        while (strlen($REG)<4)
            $REG = "0{$REG}";
        $params = ['IsActive' => "true", "Skip" => 0 , "Take" =>"1000", "sourcingRegion" => User::$region];
        // printpre($params,1);
        $result = ApiCall("GET", API_SERVER . "Customers?". http_build_query($params), null );
        $result = json_decode($result,1);
        $allDataDist = [];
        $mDist = [];
        $mDistShort = [];
        $mDistDnTipe = [];
        $mDepoDist  = [];
        $mDistDepo  = [];
        if (is_null($result))
            return;
        foreach($result as $row){
            $tipe = "DIST";
            if ($row['partnerFunctionId'] == "SH"){
                $tipe = "DEPO";
                DistributorDepo::$mDepo[$row['partnerNumber']] = "{$row['partnerNumber']} - {$row['firstName']}";
                DistributorDepo::$mDistDepo[$row['billToParty']][] = ['depo_id' => $row['partnerNumber'], "depo_name" =>$row['firstName']];
                DistributorDepo::$mDepoDist[$row['partnerNumber']] = $row['billToParty'];
            
            }
            DistributorDepo::$mDist[$row['partnerNumber']] = "{$row['partnerNumber']} - {$row['firstName']}";
            DistributorDepo::$mDistShort[$row['partnerNumber']] = "{$row['shortName']}";
            DistributorDepo::$mDistDnTipe[$row['partnerNumber']] = $tipe;
        }
    }

    public static function getShortNameFromDist($distributor_id){
        if (isset(DistributorDepo::$mDistShort[$distributor_id]))
            return DistributorDepo::$mDistShort[$distributor_id];
        DistributorDepo::getOne($distributor_id);
        return DistributorDepo::$mDistShort[$distributor_id];

        
    }

    public static function getOne($distributor_id){
        if (isset(DistributorDepo::$dataDist[$distributor_id]))
            return DistributorDepo::$dataDist[$distributor_id];
        $params = ["Skip" => 0 , "Take" =>"1000", "partnerNumbers" => $distributor_id];
        $result = ApiCall("GET", API_SERVER . "Customers?". http_build_query($params), null );
        $result = json_decode($result,1);
        Debuger::dump([$result, $params]);
        if (is_null($result) || (count($result) == 0 || isset($result['title']))){
            DistributorDepo::$mDistShort[$distributor_id] = "UNKNO";
            return DistributorDepo::$mDistShort[$distributor_id];
        }
            
        $row = $result[0];
        $tipe = "DIST";
        if ($row['partnerFunctionId'] == "SH"){
            $tipe = "DEPO";
            DistributorDepo::$mDepo[$row['partnerNumber']] = "{$row['partnerNumber']} - {$row['firstName']}";
            DistributorDepo::$mDistDepo[$row['billToParty']][] = ['depo_id' => $row['partnerNumber'], "depo_name" =>$row['firstName']];
            DistributorDepo::$mDepoDist[$row['partnerNumber']] = $row['billToParty'];
        
        }
        DistributorDepo::$mDist[$row['partnerNumber']] = "{$row['partnerNumber']} - {$row['firstName']}";
        DistributorDepo::$mDistShort[$row['partnerNumber']] = "{$row['shortName']}";
        DistributorDepo::$mDistDnTipe[$row['partnerNumber']] = $tipe;
        DistributorDepo::$dataDist[$row['partnerNumber']] = $row;
        // Debuger::dump($row,1);
        return $row;

    }

    public static function getAll(){
        $params = [ ];
        // printpre($params,1);
        $result = ApiCall("GET", API_SERVER . "Customers?". http_build_query($params), null );
        $result = json_decode($result,1);
        return $result;
    }

    public static function edit($id, $key, $value){
        
        $data = DistributorDepo::getOne($id);
        Debuger::dump($data);
        $param = [
            'partnerNumber' => $data['partnerNumber'],
            'partnerNumber' => $data['partnerNumber'],
            'partnerFunctionId' => $data['partnerFunctionId'],
            'firstName' => $data['firstName'],
            'shortName' => $data['shortName'],
            'salesRegionId' => $data['salesRegionId'],
            'billToParty' => $data['billToParty'],
            'soldToParty' => $data['soldToParty'],
            'isActive' => $data['isActive'],
        ];
        $param[$key]= $value;
        $param = [$param];
        $url = API_SERVER."Customers";
        return self::post($param, $url);
    }
    
}

?>
