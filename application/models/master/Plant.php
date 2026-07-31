<?php
class Plant {
    public static function getPlant($params=['skip' => 0, "take" =>100]){
        
        $url = API_SERVER. "SiteAddresses?" . http_build_query($params);
        $result            = ApiCall("GET", $url, null);
        $result 	= json_decode($result,1);
        if (is_null($result))
            $result = [];
        return $result;
    }
    public static function getMPlant(){
        $result = Plant::getPlant();
        $mPlant[''] = "- Plant -";
        foreach ($result as $row){
            $mPlant[$row['siteId']] = $row['siteId']." - ".$row['siteName'];
        }
        return $mPlant;

    }
    
    public static function getOne($plantid){
        $params=['skip' => 0, "take" =>100, "SiteIds" => $plantid];
        $url = API_SERVER. "SiteAddresses?" . http_build_query($params);
        $result            = ApiCall("GET", $url, null);
        $result 	= json_decode($result,1);
        if (is_null($result))
            $result = [];
        return $result[0];
    }

}

?>