<?php

class DB{

	public $db_host=CONFESSION_DB_HOST;
	public $db_name=CONFESSION_DB_NAME;
	public $db_user=CONFESSION_DB_USER;
	private $db_password=CONFESSION_DB_PASSWORD;
    protected $db;
	
	function __construct($db_host=CONFESSION_DB_HOST,$db_user=CONFESSION_DB_USER,$db_password=CONFESSION_DB_PASSWORD,$db_name=CONFESSION_DB_NAME){
		try{
			$dsn = 'mysql:dbname='.$db_name.';host='.$db_host;
			$pdo = new PDO($dsn, $db_user, $db_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
			$this->db = $pdo;
			
		}catch( PDOExecption $e ) {
			$errorMsg = "Error!: " . $e->getMessage() . "</br>";
			//self::EZBEER_error_log($errorMsg);
		}
	}
	
	
	/* insertQuery()
	* Function for insert data in database.
	* Query
	* Return insert_id.
	*/
	public function insertQuery($query,$param){
		
		if(!$query || !$param){
			return false;
		}
		try{
			$pdo = $this->db;
			$sql = $pdo->prepare($query);	
			try{		
				$sql->execute($param);				
				$pdo->beginTransaction();
				$pdo->commit();
				//get last inserted id;
				$stmt = $pdo->query("SELECT LAST_INSERT_ID()");
				$lastId = $stmt->fetch(PDO::FETCH_NUM);
				return $lastId = $lastId[0];
				
			}catch(PDOExecption $e){
				
				$pdo->rollback();
				$error = "Insert Query Error: ".$e->getMessage()."\n";
				
				return $error;
			}
		}catch( PDOExecption $e ) {
	
			$pdo->rollback();
			$error = "Insert Query Error: ".$e->getMessage()."\n";
			
			return $error;
		}
	}
	
	
	/* selectQuery()
	* function for select data from database. 
	* Query,parameter.
	* Return row data.
	*/
	public function selectQuery($query,$param = null){
		
		if(!$query){
			return false;
		}
		try{
			$pdo = $this->db;
			$sql = $pdo->prepare($query);
			try{
				
				$pdo->beginTransaction();
				if($param){
					$sql->execute($param);
				}else{
					$sql->execute();
				}
				$pdo->commit();
				return $sql->fetchAll();
				
			}catch(PDOExecption $e){
				
				$pdo->rollback();
				$error = "Select Query Error: ".$e->getMessage()."\n";
				return $error;
			}
		}catch( PDOExecption $e ) {
			
			$error = "Select Query Error: ".$e->getMessage()."\n";
			return $error;
		}
	} 
	
	/* updateQuery()
	* function for update data in database.
	* Query
	* Return true.
	*/
	public function updateQuery($query,$param){
		
		if(!$query || !$param){
			return false;
		}
		try{
			$pdo = $this->db;
			$sql = $pdo->prepare($query);
			try{	
				$sql->execute($param);				
				$status  = $sql->rowCount();
				return $status;
			}catch(PDOExecption $e){
				$pdo->rollback();
				$error = "Update Query Error: ".$e->getMessage()."\n";
			//	self::EZBEER_error_log($error,'error');
				return $error;	
			}
		}catch( PDOExecption $e ) {
			
			$error = "Update Query Error: ".$e->getMessage()."\n";
		//	self::EZBEER_error_log($error,'error');
			return $error;
		}
	} 
	
	
	/* deleteQuery()
	* function for delete record in database.
	* Query
	* Return true.
	*/
	public function deleteQuery($query,$param){
		if(!$query || !$param){
			return false;
		}else{
			$status = self::updateQuery($query,$param);
			return $status;
		}
	}
	
	/* get_option()
	* function for get options.
	* option_name 
	* Return option value.
	*/
	public function get_user_meta($userID,$metaName){
		if(!$metaName || !$userID  || empty($metaName) || empty($userID)){
			return false;
		}else{
			$query = "SELECT * FROM user_meta WHERE user_id=:user_id AND user_meta_key=:user_meta_key LIMIT 1";
			$param =array('user_id'=>$userID,'user_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
			return $res;
		}
	}
	
	/* update_user_meta()
	* update option in databse and if not available record then add new record.
	* option_name , option_value
	* Return true.
	*/
	public function update_user_meta($userID,$metaName,$metaValue){
		if(!$metaName || !$userID || $metaValue=="" || empty($metaName) || empty($userID) ){
			return false;
		}else{
			$query = "SELECT * FROM user_meta WHERE user_id=:user_id AND user_meta_key=:user_meta_key LIMIT 1";
			$param =array('user_id'=>$userID,'user_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
		
			if(!empty($res)){
				$query = "UPDATE user_meta SET user_meta_value =:user_meta_value WHERE user_id=:user_id AND user_meta_key=:user_meta_key";
				$param =array('user_meta_value'=>$metaValue,'user_id'=>$userID,'user_meta_key'=>$metaName);
				$res = self::updateQuery($query,$param); 
				return $res;
			}else{
				$query = "INSERT INTO user_meta (user_id,user_meta_key,user_meta_value) VALUES (:user_id,:user_meta_key,:user_meta_value)";
				$param = array('user_id'=>$userID,'user_meta_key'=>$metaName,'user_meta_value'=>$metaValue);
				$res = self::insertQuery($query,$param);
				return $res;
			}
		}
	} 
	
	public function get_account_meta($userID,$metaName){
		if(!$metaName || !$userID  || empty($metaName) || empty($userID)){
			return false;
		}else{
			$query = "SELECT * FROM account_meta WHERE acc_id=:acc_id AND account_meta_key=:account_meta_key LIMIT 1";
			$param =array('acc_id'=>$userID,'account_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
			
			return $res;
		}
	}
	
	/* update_Account_meta()
	* update option in databse and if not available record then add new record.
	* option_name , option_value
	* Return true.
	*/
	public function update_account_meta($userID,$metaName,$metaValue){
		if(!$metaName || !$userID || $metaValue=="" || empty($metaName) || empty($userID) ){
			return false;
		}else{
			$query = "SELECT * FROM account_meta WHERE acc_id=:acc_id AND account_meta_key=:account_meta_key LIMIT 1";
			$param =array('acc_id'=>$userID,'account_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
		
			if(!empty($res)){
				$query = "UPDATE account_meta SET account_meta_value =:account_meta_value WHERE acc_id=:acc_id AND account_meta_key=:account_meta_key";
				$param =array('account_meta_value'=>$metaValue,'acc_id'=>$userID,'account_meta_key'=>$metaName);
				$res = self::updateQuery($query,$param); 
				return $res;
			}else{
				$query = "INSERT INTO account_meta (acc_id,account_meta_key,account_meta_value) VALUES (:acc_id,:account_meta_key,:account_meta_value)";
				$param = array('acc_id'=>$userID,'account_meta_key'=>$metaName,'account_meta_value'=>$metaValue);
				$res = self::insertQuery($query,$param);
				return $res;
			}
		}
	}
	
	
	/* remove_user_meta()
	* function for remove options.
	* option_name 
	* Return true.
	*/
	public function remove_user_meta($userID,$metaName){
		if(!$userID || !$metaName || empty($metaName) || empty($userID)){
			return false;
		}else{
			$query = "DELETE FROM user_meta WHERE user_id=:user_id AND user_meta_key=:user_meta_key";
			$param =array('user_id'=>$userID,'user_meta_key'=>$metaName);
		    $res = self::deleteQuery($query,$param);
			return $res;
		}
	}
	
	/* remove_account_meta()
	* function for remove options.
	* option_name 
	* Return true.
	*/
	public function remove_account_meta($userID,$metaName){
		if(!$userID || !$metaName || empty($metaName) || empty($userID)){
			return false;
		}else{
			$query = "DELETE FROM account_meta WHERE acc_id=:acc_id AND account_meta_key=:account_meta_key";
			$param =array('acc_id'=>$userID,'account_meta_key'=>$metaName);
		    $res = self::deleteQuery($query,$param);
			return $res;
		}
	}
	
	/* get_option()
	* function for get options.
	* option_name 
	* Return option value.
	*/
	public function get_option($optionName){
		if(!$optionName  ||empty($optionName)){
			return false;
		}else{
			$query = "SELECT * FROM options WHERE option_name=:option_name LIMIT 1";
			$param =array('option_name'=>$optionName);
			$res = self::selectQuery($query,$param);
			return $res;
		}
	}

	/* update_option()
	* update option in databse and if not available record then add new record.
	* option_name , option_value
	* Return true.
	*/
	public function update_option($optionName,$optionValue){
		if(!$optionName || !$optionName ||empty($optionName)){
			return false;
		}else{
			$query = "SELECT * FROM options WHERE option_name=:option_name LIMIT 1";
			$param =array('option_name'=>$optionName);
			$res = self::selectQuery($query,$param);
		
			if(!empty($res)){
				$query = "UPDATE options SET option_value =:option_value where option_name =:option_name";
				$param =array('option_value'=>$optionValue,'option_name'=>$optionName);
				$res = self::updateQuery($query,$param); 
				return $res;
			}else{
				$query = "INSERT INTO options (option_name,option_value) VALUES (:option_name,:option_value)";
				$param =array('option_name'=>$optionName,'option_value'=>$optionValue);
				$res = self::insertQuery($query,$param);
				return $res;
			}
		}
	} 
	
	
	/* remove_option()
	* function for remove options.
	* option_name 
	* Return true.
	*/
	public function remove_option($optionName){
		if(!$optionName || !$optionName ||empty($optionName)){
			return false;
		}else{
			$query = "DELETE FROM options WHERE option_name = :option_name";
			$param =array('option_name'=>$optionName);
		    $res = self::deleteQuery($query,$param);
			return $res;
		}
	}
	
	
	/* send_email()
	* Mailing function for sending mail
	* templatename , sender email address , receiver email address , data to send
	* Return true.
	*/
	public function send_email($template_name, $to, $subject, $template_data)
	{
		// Get template data from provided template
		$template_file_full_path = CONFESSION_EMAIL_TEMPLATE_PATH . $template_name;

		if (file_exists($template_file_full_path)) {
			 $html_content = file_get_contents($template_file_full_path);
		}else{
			return false; // Template Not available
		}
		
		foreach($template_data as $key => $data){
			$html_content = str_replace("{#$". $key ."$#}", $data, $html_content);
		}
		
		// To send HTML mail, the Content-type header must be set
		$headers  = "From: ".CONFESSION_EMAIL_FOR_EMAILHEADER . " CONFESSION\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		$messages = $html_content;
		
		// Ready to send email
		if(mail($to,$subject,$messages,$headers)){
			$error = "Email SuccessFully send to : ".$to."\r\n";
			//self::REALESTATE_error_log($error,'error');
			return true;
		}else{
			$error = "Email Send Problem to : ".$to."\r\n";
			//self::REALESTATE_error_log($error,'error');
			return false;
		}
	}
	 

	
	/* generateStrongPassword()
	* Genrate password
	* length , add dash or not available_set
	* Return string.
	*/	
	public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'abcd')
	{
		$sets = array();
		if(strpos($available_sets, 'a') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'b') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'c') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '!@#$%&*?';
		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];
		$password = str_shuffle($password);
		if(!$add_dashes)
			return $password;
		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}
	 
	
	
	/* SSCRM_error_log()
	* function for create error log.
	* error-text ,other data (optional)
	* Not return any data.
	*/
	public function REALESTATE_error_log($errorMsg){
		
		$path='../error-logs/'.date('m/d/Y').'-error';
		$log="\n [".date("m/d/Y H:i:s").'] '.$errorMsg ;
		error_log($log, 3, $path);
	}
	
	 function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

	
	//function for country list
	public function countryList(){
		$countries = array(
			'1'=>'USA',
			'2'=>'Aaland Islands',
			'3'=>'Afghanistan',
			'4'=>'Albania',
			'5'=>'Algeria',
			'6'=>'American Samoa',
			'7'=>'Andorra',
			'8'=>'Angola',
			'9'=>'Anguilla',
			'10'=>'Antigua And Barbuda',
			'11'=>'Argentina',
			'12'=>'Armenia',
			'13'=>'Aruba',
			'14'=>'Australia',
			'15'=>'Austria',
			'16'=>'Azerbaijan',
			'17'=>'Bahamas',
			'18'=>'Bahrain',
			'19'=>'Bangladesh',
			'20'=>'Barbados',
			'21'=>'Belarus',
			'22'=>'Belgium',
			'23'=>'Belize',
			'24'=>'Benin',
			'25'=>'Bermuda',
			'26'=>'Bhutan',
			'27'=>'Bolivia',
			'28'=>'Bosnia and Herzegovina',
			'29'=>'Botswana',
			'30'=>'Bouvet Island',
			'31'=>'Brazil',
			'32'=>'Brunei Darussalam',
			'33'=>'Bulgaria',
			'34'=>'Burkina Faso',
			'35'=>'Burundi',
			'36'=>'Cambodia',
			'37'=>'Cameroon',
			'38'=>'Canada',
			'39'=>'Cape Verde',
			'40'=>'Cayman Islands',
			'41'=>'Central African Republic',
			'42'=>'Chad',
			'43'=>'Chile',
			'44'=>'China',
			'45'=>'Christmas Island',
			'46'=>'Colombia',
			'47'=>'Comoros',
			'48'=>'Congo',
			'49'=>'Cook Islands',
			'50'=>'Cote D\'Ivoire',
			'51'=>'Croatia',
			'52'=>'Cuba',
			'53'=>'Curacao',
			'54'=>'Cyprus',
			'55'=>'Czech Republic',
			'56'=>'Democratic Republic of the Congo',
			'57'=>'Denmark',
			'58'=>'Djibouti',
			'59'=>'Dominica',
			'60'=>'Dominican Republic',
			'61'=>'East Timor',
			'62'=>'Ecuador',
			'63'=>'Egypt',
			'64'=>'El Salvador',
			'65'=>'Equatorial Guinea',
			'66'=>'Eritrea',
			'67'=>'Estonia',
			'68'=>'Ethiopia',
			'69'=>'Falkland Islands',
			'70'=>'Faroe Islands',
			'71'=>'Fiji',
			'72'=>'Finland',
			'73'=>'France',
			'74'=>'French Guiana',
			'75'=>'French Polynesia',
			'76'=>'Gabon',
			'77'=>'Gambia',
			'78'=>'Georgia',
			'79'=>'Germany',
			'80'=>'Ghana',
			'81'=>'Gibraltar',
			'82'=>'Greece',
			'83'=>'Greenland',
			'84'=>'Grenada',
			'85'=>'Guadeloupe',
			'86'=>'Guam',
			'87'=>'Guatemala',
			'88'=>'Guernsey',
			'89'=>'Guinea',
			'90'=>'Guyana',
			'91'=>'Haiti',
			'92'=>'Honduras',
			'93'=>'Hong Kong',
			'94'=>'Hungary',
			'95'=>'Iceland',
			'96'=>'India',
			'97'=>'Indonesia',
			'98'=>'Iran',
			'99'=>'Iraq',
			'100'=>'Ireland',
			'101'=>'Isle of Man',
			'102'=>'Israel',
			'103'=>'Italy',
			'104'=>'Jamaica',
			'105'=>'Japan',
			'106'=>'Jersey  (Channel Islands)',
			'107'=>'Jordan',
			'108'=>'Kazakhstan',
			'109'=>'Kenya',
			'110'=>'Kiribati',
			'111'=>'Kuwait',
			'112'=>'Kyrgyzstan',
			'113'=>'Lao People\'s Democratic Republic',
			'114'=>'Latvia',
			'115'=>'Lebanon',
			'116'=>'Lesotho',
			'117'=>'Liberia',
			'118'=>'Libya',
			'119'=>'Liechtenstein',
			'120'=>'Lithuania',
			'121'=>'Luxembourg',
			'122'=>'Macau',
			'123'=>'Macedonia',
			'124'=>'Madagascar',
			'125'=>'Malawi',
			'126'=>'Malaysia',
			'127'=>'Maldives',
			'128'=>'Mali',
			'129'=>'Malta',
			'130'=>'Marshall Islands',
			'131'=>'Martinique',
			'132'=>'Mauritania',
			'133'=>'Mauritius',
			'134'=>'Mayotte',
			'135'=>'Mexico',
			'135'=>'Moldova, Republic of',
			'136'=>'Monaco',
			'137'=>'Mongolia',
			'138'=>'Montenegro',
			'139'=>'Montserrat',
			'140'=>'Morocco',
			'141'=>'Mozambique',
			'142'=>'Myanmar',
			'143'=>'Namibia',
			'144'=>'Nepal',
			'145'=>'Netherlands',
			'146'=>'Netherlands Antilles',
			'147'=>'New Caledonia',
			'148'=>'New Zealand',
			'149'=>'Nicaragua',
			'150'=>'Niger',
			'151'=>'Nigeria',
			'152'=>'Niue',
			'153'=>'Norfolk Island',
			'154'=>'North Korea',
			'155'=>'Norway',
			'156'=>'Oman',
			'157'=>'Pakistan',
			'158'=>'Palau',
			'159'=>'Palestine',
			'160'=>'Panama',
			'161'=>'Papua New Guinea',
			'162'=>'Paraguay',
			'163'=>'Peru',
			'164'=>'Philippines',
			'165'=>'Pitcairn',
			'166'=>'Poland',
			'167'=>'Portugal',
			'168'=>'Qatar',
			'169'=>'Republic of Kosovo',
			'170'=>'Reunion',
			'171'=>'Romania',
			'172'=>'Russia',
			'173'=>'Rwanda',
			'174'=>'Saint Kitts and Nevis',
			'175'=>'Saint Lucia',
			'176'=>'Saint Vincent and the Grenadines',
			'177'=>'Samoa (Independent)',
			'178'=>'San Marino',
			'179'=>'Saudi Arabia',
			'180'=>'Senegal',
			'181'=>'Serbia',
			'182'=>'Seychelles',
			'183'=>'Sierra Leone',
			'184'=>'Singapore',
			'185'=>'Sint Maarten',
			'186'=>'Slovakia',
			'187'=>'Slovenia',
			'188'=>'Solomon Islands',
			'189'=>'Somalia',
			'190'=>'South Africa',
			'191'=>'South Georgia and the South Sandwich Islands',
			'192'=>'South Korea',
			'193'=>'South Sudan',
			'194'=>'Spain',
			'195'=>'Sri Lanka',
			'196'=>'Sudan',
			'197'=>'Suriname',
			'198'=>'Svalbard and Jan Mayen Islands',
			'199'=>'Swaziland',
			'200'=>'Sweden',
			'201'=>'Switzerland',
			'202'=>'Syria',
			'203'=>'Taiwan',
			'204'=>'Tajikistan',
			'205'=>'Tanzania',
			'206'=>'Thailand',
			'207'=>'Togo',
			'208'=>'Tonga',
			'209'=>'Trinidad and Tobago',
			'210'=>'Tunisia',
			'211'=>'Turkey',
			'212'=>'Turks &amp; Caicos Islands',
			'213'=>'Uganda',
			'214'=>'Ukraine',
			'215'=>'United Arab Emirates',
			'216'=>'United Kingdom',
			'217'=>'Uruguay',
			'218'=>'Uzbekistan',
			'219'=>'Vanuatu',
			'220'=>'Vatican City State (Holy See)',
			'221'=>'Venezuela',
			'222'=>'Vietnam',
			'223'=>'Virgin Islands (British)',
			'224'=>'Virgin Islands (U.S.)',
			'225'=>'Western Sahara',
			'226'=>'Yemen',
			'227'=>'Zambia',
			'228'=>'Zimbabwe'
		);
		return $countries;
	}
	
	
	
	function __destruct(){
		$this->db = null;
	} 
}

?>