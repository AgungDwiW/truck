<?php 
$base_path = dirname(__FILE__);
$cache 	= new Cache();

//====================================================================================================================================
//                                               UTILITY FOR MASTER DATA
//====================================================================================================================================
    $__materialName = [];
    function getMaterialName($material_id){
        global $__materialName;
        if(isset($__materialName[$material_id]))
            return $__materialName[$material_id];
        else{
            $table = new Table("tbm_material");
            $name = $table->get("diskripsi_material")->where("kode_material='{$material_id}'")->fetchOne();
            $__materialName[$material_id] = $name['diskripsi_material'];
            printpre([$name,$__materialName[$material_id]]);
            return $__materialName[$material_id];
        }
    }
    function conv_uom_buom($qty, $uom, $material_id ){
        //function to convert uom to buom 
        // return array ['qty_buom' => qty, 'buom' => buom]
        if ($qty == '' )
            $qty = 0;
        
        $table 	= new Table('tbm_material_conversion');
        $result = $table->get(
                            "{$qty} * (conversion_numerator_buom/conversion_denominator_buom) AS qty_buom", 
                            'base_unit_of_measure'
                            )
                        ->where("
                            
                            material_code = '{$material_id}' AND 
                            alt_uom_sku = '{$uom}'
                            ")
                        ->fetchOne();

        #$result = mysqli_query($con, $str);
        // $row=mysqli_fetch_assoc($result);
        return $result;

    }
    function conv_buom_uom($qty, $uom, $material_id ){
        //function to convert buom to uom 
        // return array ['qty_buom' => qty, 'buom' => buom]
        if ($qty == '' or $qty == 0)
            return 0;
        
        $table 	= new Table('tbm_material_conversion');
        $result = $table->get(
                            "{$qty} * (conversion_denominator_buom/conversion_numerator_buom) AS qty_uom", 
                            'alt_uom_sku'
                            )
                        ->where("
                            
                            material_code = '{$material_id}' AND 
                            alt_uom_sku = '{$uom}'
                            ")
                        ->fetchOne();
        if(count($result)==0){
            $result = ['qty_uom' => $qty, 'alt_uom_sku' => $uom];
        }
        #$result = mysqli_query($con, $str);
        // $row=mysqli_fetch_assoc($result);
        return $result;

    }



    // function getMasterData($table, $idRow, $valueRow, $condition='TRUE'){
    //     $table = new Table($table);
    //     // global $debug;
    //     // $debug = 1;
    //     $result = $table->get($idRow, $valueRow)->where($condition)->fetchAll();
    //     $data = [];
    //     foreach ($result as $row){
    //         printpre($row);
    //         $data[$row[$idRow]] = "{$row[$idRow]} - {$row[$valueRow]}";
    //         printpre_warn($data);
    //     }
    //     return $data;
    // }

    function checkTableExist($db, $table, $cone){
        $str = "SELECT * 
                FROM information_schema.tables
                WHERE table_schema = '{$db}' 
                    AND table_name = '{$table}'
                LIMIT 1;";
        $result = run_query($cone, $str);	
        return (mysqli_num_rows($result) == 1);
    }

//====================================================================================================================================
//                                               UTILITY FOR EASE SQL
//====================================================================================================================================
    function easyReplace($data, $con){
        $column = array_keys($data);
        $field_arr 	= array_keys($data[0]);
        $field 		= implode(",", array_keys($data[0]));
        $data_arr 	= [];
        foreach($data as $row){
            $d 		= [];
            foreach($field_arr as $col){ //ensure in same order
                // $row[$col] =str_replace("'", "\'", $row[$col]);
                $d[] = $row[$col];
            }
            // $d 	=  implode("','", array_values($d));;

            $d 		= encaseValue($d);
            if (!is_null($d))
                $d = "'{$d}'";
            $d 	= "({$d})";
            $data_arr[] = $d;
        }
        $content = implode(", ", $data_arr);
    }



?>

