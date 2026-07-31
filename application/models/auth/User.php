<?php
include_once "ApiClient.inc";

class User{
	public static $username 		= null;
	public static $plantid   		= null;
	public static $plant_name   		= null;
    public static $plant    		= null;
	public static $region    		= null;
	public static $line    			= null;
    public static $nama     		= null;
	public static $name     		= null;
    public static $admin    		= null;
	public static $nik    			= null;
    public static $email    		= null;
	public static $role_desc     	= null;
	public static $role_id   		= null;
	public static $access   		= null;
	public static $access_old 		= null;
	public static $akses_sl 		= null;
	public static $slAccess 		= null;
	public static $role_code 	 	= null;
	public static $claims 	 	= null;

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
	public static function loginDummy($username, $password){
		
		global $conSL;
		//-----------------------------------------------------------------------------------
		User::$username 				= 'tester';
		User::$name 					= 'tester';
		User::$plant 					= '9012';
		User::$region 					= 'tester';
		User::$line 					= 'tester';
		User::$nik 						= 'tester';
		User::$role_desc 				= 'tester';
		User::$role_id 					= 'tester';
		User::$slAccess 				= '1,2,3';

		//-----------------------------------------------------------------------------------
		# SESSION initialization
		$_SESSION["username"] 	= 'tester';
		$_SESSION["email"] 		= 'tester';
		$_SESSION["nama"] 		= 'tester';
		$_SESSION["plantid"] 	= '9012';
		$_SESSION["plant"] 		= 'tester';
		$_SESSION["region"] 	= 'tester';
		$_SESSION["line"] 		= 'tester';
		$_SESSION["nik"] 		= 'tester';
		$_SESSION["role_id"] 	= '261';
		$_SESSION["role_desc"] 	= 'Logistic Checker 9007';
		$_SESSION["akses_sl"] 	= '1,2,3';
		$_SESSION['admin'] 		= true;
		//-----------------------------------------------------------------------------------
		
		//-----------------------------------------------------------------------------------
		//									NEW ROLE
		//-----------------------------------------------------------------------------------
			$role_desc = preg_replace('/9[0-9A-Za-z]{3}$/', '', $_SESSION["role_desc"]);
			$sql = "SELECT * FROM tbm_akses WHERE (ISNULL(role_id) AND role_desc = '{$role_desc}') OR role_id = '{$_SESSION["role_id"]}'";
			printpre($sql);
			$result= mysqli_query($conSL, $sql);
			$access = [];
			while ($r=mysqli_fetch_assoc($result)) {
				printpre($r);
				$access[$r['module']][$r['action']]=1;
			}			
			$_SESSION["access"] 	= $access;
		User::checkLogin();
		return true;
	}

	
    public static function login($username, $password){
		// $username = preg_replace('/[^a-zA-Z0-9.]/', '', $username);
		$response = GetAccessToken($username, $password, 420);
		printpre($response);
		if (isset($response['success']))
			$response['Success'] = $response['success'];
		if ($response['Success'] && $response['Success'] == true) {
			return User::setUserProfile();		
		}
    }

	public static function setUserProfile(){
		global $conSL;
		
		$json = ApiCall('GET', AUTH_SERVER.'manage/userinfo', null, null);
		$data = json_decode($json, true);
		
	
		if ($json && isset($data['userName']) && strlen($data['userName']) > 0) {
				
			# SESSION initialization
			$_SESSION["username"] 		= $data['userName'];
			$_SESSION["email"] 			= $data['email'];
			$_SESSION["nama"] 			= $data['fullName'];
			$_SESSION["name"] 			= $data['fullName'];
			$_SESSION["plantid"] 		= $data['siteId'];
			$_SESSION["plant_name"] 	= isset($data['site'], $data['site']['siteName']) ?$data['site']['siteName']: "PLANT UNDEFINED";
			$_SESSION["region"] 		= isset($data['site'], $data['site']['siteRegion']) ?$data['site']['siteRegion']: "REGION UNDEFINED";
			$_SESSION["nik"] 			= $data['employeeId'];
			$_SESSION["role_desc"] 		= $data["roles"][0];

			$claims = [];
			foreach($data['claims'] as $item){
				if (!isset($claims[$item['type']]))
					$claims[$item['type']] = [$item['value']];
				else
					$claims[$item['type']][] = $item['value'];
			}

			$_SESSION["claims"] 	    = $claims;
			$_SESSION["claims_data"] 	    = $data['claims'];
			User::checkLogin();
			return true;
		} else {
			return false;
		}
    }

	public static function checkAccess($claims, $value=null){
		if (User::$username=='test9012')
			return true;
		if (is_null($value))
			return in_array($claims, array_keys(User::$claims) );
		else{
			return (isset(User::$claims[$claims]) and in_array($value,User::$claims));
		}
	}

	
	public static function haveRole($role){
		return in_array($role, User::$role);
	}

	public static function unsetSession(){
		unset($_SESSION["username"]);
		unset($_SESSION["email"]);
		unset($_SESSION["nama"]);
		unset($_SESSION["name"]);
		unset($_SESSION["plantid"]);
		unset($_SESSION["plant_name"]);
		unset($_SESSION["region"]);
		unset($_SESSION["nik"]);
		unset($_SESSION["role_desc"]);
		unset($_SESSION["cRefTkn"]);
		unset($_SESSION["cTkn"]);
		unset($_SESSION["cExpTkn"]);
	}
	

	public static function loginWithToken($token){
		// session_destroy();
		User::unsetSession();
		$_SESSION["cRefTkn"] = $token;
		
    	$ret = RefreshToken();
		if ($ret['Message'] =='success'){ 
			User::setUserProfile();
			// echo json_encode(['status' => "SUCCESS"]);
		}
		else{
			echo "<script>alert('{$ret['Message']}')</script>";
			// echo json_encode(['status' => "FAILED", "message" =>$ret['Message']]);
		}
	}

	
}

?>
