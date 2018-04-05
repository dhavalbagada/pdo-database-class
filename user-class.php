<?php
class User extends DB{

	public function login_users($user,$pass){

		$query = "SELECT * FROM users WHERE user_name=:user_name AND user_password=:user_password";
		$param =  array('user_name'=>$user,
						'user_password'=>md5($pass));
		$result = parent::selectQuery($query,$param);
		
		$count = count($result);
		
		if($count == 1){

			if(md5($pass) == $result[0]['user_password']){
				$_SESSION['MSITE_USERID'] = $result[0]['user_id'];
				// $_SESSION['MOBILEPAYMENT_USERTYPE'] = $result[0]['user_type'];
				$_SESSION['MSITE_uname'] = $result[0]['user_name'];
				// $_SESSION['MSITE_EMAIL'] = $result[0]['user_email'];

				return array('status'=>1,'message'=>'login successfuly' ,'redirectURL'=>'dashboard.php');
			}else{
					$msg = "Invalid Email or Password";
					return array('status'=>0,'message'=>$msg);
				}
		}else{
			$msg = "Invalid Email or Password";
			return array('status'=>0,'message'=>$msg);
		}
	}
	
	public function user_registers($data){

		$query = "SELECT * FROM users WHERE user_name=:user_name";
		$param =  array('user_name'=>$data['uname']);
		$result = parent::selectQuery($query,$param);

		if(empty($result)){
	
			$query = "INSERT INTO users (user_name,user_password,user_firstname,user_lastname,user_hobbies,user_image) VALUES (:user_name,:user_password,:user_firstname,:user_lastname,:user_hobbies,:user_image)";
			$param = array(
						'user_name'=>$data['uname'],
						'user_password'=>md5($data['pass']),
						'user_firstname'=>$data['fname'],
						'user_lastname'=>$data['lname'],
						'user_hobbies'=>$data['hobbie'],
						'user_image'=>$data['image']
				);

			$res = parent::insertQuery($query,$param);

			if($res > 0){

				return array('status'=>1,'message'=>'Record inserted successfuly');
			}else{
				return array('status'=>0,'message'=>'Generate Database Error.');
			}
		}else{

			return array('status'=>0,'message'=>'user name already exits.');
		}	
	}
}
?>