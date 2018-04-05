<?php
require_once("../config.php");

if(isset($_POST['type'])){
	$type = $_POST['type'];
	if(isset($_POST['data'])){
		$data = $_POST['data'];
	}elseif($type == 'user_register'){
		$data = $_POST;
	}
}

$userObj = new User();

if(isset($type) && !empty($type))
{
	switch ($type) {
		case 'user_register':
			user_register($userObj,$data);
			break;

		case 'login_user':
			login_user($userObj,$data);
			break;	
		
		default:
			
			break;
	}
}

function user_register($userObj,$data){



	$error_message = "";
	$all_img2 = "";
	$passimgpath = "../upload";

	if(isset($_FILES['image']['name'])){

		if(isset($_FILES['image']['name'][0])){

			$path_parts = pathinfo($_FILES['image']['name']);
			// print_r($path_parts);die();
			$image_path = $path_parts['filename'].'_'.time().'.'.$path_parts['extension'];
			$type = $_FILES['image']['type'];
			$all_img2.=$image_path;

			$image_types = array('image/jpeg','image/gif','image/png');

			if(in_array($type, $image_types)){
				move_uploaded_file($file_tmp=$_FILES['image']['tmp_name'],$passimgpath."/".$image_path);
				$data['image'] = $all_img2;
			}else{
				print json_encode(array("status"=>0,"message"=>"only allow jpg , png , gif file<br/>"));
					exit;
			}	

		}else{
			$data['image']='';
		}
	}else{
		$data['image']='';
	}


	if($error_message != ""){
		print  json_encode(array('status'=>0,'message'=>$error_message));
	}else{
		
	}
	$res = $userObj->user_registers($data);
	print json_encode($res);
}

function login_user($userObj,$data){

	$error_message="";

	if(empty($data['username'])){
		$error_message.="Please Enter username";
	}

	if(empty($data['password'])){
		$error_message.="Please Enter password";
	}

	if($error_message != ""){
		print json_encode(array('status'=>0,'message'=>$error_message));
	}else{

		$uname = $data['username'];
		$pass = $data['password'];

		$res = $userObj->login_users($uname,$pass);
		print json_encode($res);
	}
}
?>