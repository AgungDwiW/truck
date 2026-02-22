<?php 
    require_once "utility_debugging.php";
    function in_string($str, $substr){
        // printpre([$str, $substr,strpos($str, $substr)!== false?"true":"false"]);
        if (strpos($str, $substr) !== false)
            return true;
        else
            return false;
        
    }
        
    function redirect_back($additional_get=[]){
        global $debug;
        $location = $_SERVER['HTTP_REFERER'];
        $replaced = [];
        if (! in_string($location, "?")){
            $location.="?";
        }
        else{
            $location2  = explode("?", $location);
            printpre($location2, $debug);
            $location_b = $location2[0];
            $location_s = $location2[1];
            printpre(["b"=>$location_b, 's'=>$location_s],$debug);
            $location_s = explode("&", $location_s); 
            printpre(["b"=>$location_b, 's'=>$location_s],$debug);
            $gets 		= [];
            foreach($location_s as $get){
                $keyval = explode("=", $get);
                foreach($additional_get as $key => $value){
                    if ($key == $keyval[0]){
                        $keyval[1] = $value;
                        $replaced[]=$key;
                    }
                }
                $keyval  	= implode("=", $keyval);
                if ($keyval)
                    $gets[] 	= $keyval;
            }
            $location_s = implode("&", $gets);
            $location = $location_b."?".$location_s;

        }
        printpre($location,$debug);

        foreach($additional_get as $key => $value){
            if (in_array($key, $replaced))
                continue;
            $location.="&{$key}={$value}";
        }
        if ($debug == 0){
            header('location:' . $location);
            echo "<script> window.location = '".$location."'; </script>";
        }

    }
    function redirect_page($page){
        global $debug;
        if (!$debug){
            header('location:'.$page);
            echo "<script> window.location = '".$page."'; </script>";
        }
    }
    



?>