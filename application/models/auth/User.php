<?php
include_once "ApiClient.inc";

class User{
	public static $username 		= null;
    public static $plant    		= null;
	public static $plantid     		= null;
	public static $plant_name 		= null;
	public static $region    		= null;
	public static $line    			= null;
    public static $name     		= null;
	public static $nama     		= null;
    public static $admin    		= null;
    public static $email    		= null;
	public static $role_desc     	= null;
	public static $claims 			= null;
	public static $nik				= null;


    private $table   = null;
	public function __construct()
	{
	}

	public static function checkLogin(){
		if(isset($_SESSION["username"])){
			foreach($_SESSION as $key => $value){
				try {
					User::$$key = $value;
				} catch (\Throwable $th) {
					//throw $th;
				}
					
			}
			return true;
		}
		else{
			return false;
		}
	}


    public function login($username, $password){
		global $con140;
		global $con73;
		global $con;
		global $conSL;
		//-----------------------------------------------------------------------------------
		$username = preg_replace('/[^a-zA-Z0-9.]/', '', $username);
		$response = GetAccessToken($username, $password, 420);
		printpre($response);
		
		if (isset($response['Success']) ) {
			$json = ApiCall('GET', AUTH_SERVER.'manage/userinfo', null, null);
			$data = json_decode($json, true);
			printpre(["data"=>$data, $data['userName']] );
			if ($json && isset($data['userName']) && strlen($data['userName']) > 0) {
				
				# SESSION initialization
				$_SESSION["username"] 	= $data['userName'];
				$_SESSION["email"] 		= $data['email'];
				$_SESSION["nama"] 		= $data['fullName'];
				$_SESSION["name"] 		= $data['fullName'];
				$_SESSION["plantid"] 		= $data['siteId'];
				$_SESSION["plant_name"] 	= $data['site']['siteName'];
				$_SESSION["region"] 		= $data['site']['siteRegion'];
				$_SESSION["nik"] 			= $data['employeeId'];
				$_SESSION["role_desc"] 			= $data["roles"][0];

				$claims = [];
				foreach($data['claims'] as $item){
					$claims[] = $item['type'];
				}

				$_SESSION["claims"] 	    = $claims;
				// print_r($data);
				# class initialization
				foreach($_SESSION as $key => $value){
					try {
						User::$$key = $value;
					} catch (\Throwable $th) {
						//throw $th;
					}
				}

				//-------------------
				printpre($_SESSION,1);
				//-----------------------------------------------------------------------------------
				return true;
			} else {
				return false;
			}
			
		}
    }

	public function isHO(){
		return $this->lokasi =='9000';
	}
	public function isOtif(){
		return $this->lokasi =='8001';
	}

	public static function checkAccess($claims){
		return in_array($claims, User::$claims);

	}

	
}

?>
