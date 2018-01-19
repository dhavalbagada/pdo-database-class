<?php
// all setting are here...

$db_host		=	"localhost";
$db_user		=	"confession";
$db_password	=	"confession";
$db_name		=	"confession"; 
$page = basename($_SERVER['PHP_SELF']);
$dir  = dirname($_SERVER['PHP_SELF']);

//on local.
$site_url =	"http://$_SERVER[HTTP_HOST]$dir";

if(!defined('CONFESSION_DB_HOST'))
	define( 'CONFESSION_DB_HOST', $db_host );

if(!defined('CONFESSION_DB_USER'))
	define( 'CONFESSION_DB_USER', $db_user );

if(!defined('CONFESSION_DB_PASSWORD'))
	define( 'CONFESSION_DB_PASSWORD',$db_password );

if(!defined('CONFESSION_DB_NAME'))
	define( 'CONFESSION_DB_NAME', $db_name  );

if (!defined('CONFESSION_SITEURL'))
    define( 'CONFESSION_SITEURL', $site_url );

if(!defined('CONFESSION_EMAIL_TEMPLATE_PATH'))
	define( 'CONFESSION_EMAIL_TEMPLATE_PATH', '../email-templates/' );

if(!defined('CONFESSION_EMAIL_FOR_EMAILHEADER'))
	define( 'CONFESSION_EMAIL_FOR_EMAILHEADER', 'contact@test.com' );



if(!isset($_SESSION)){
	session_start();
}

$clearUrlValue=basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$file = basename($clearUrlValue, ".php");

require_once("classes/database-class.php");
$DB    = new DB;


//$superAdminAccess = array();  



/* if(!isset($_SESSION['CONFESSION_USERTYPE'])){
		
		if( isset($_COOKIE['CONFESSION_UTYPE'])){
			
			$DB->remember_me();	
			
		}else{
			
			if($file == 'login' || $file == 'sign-up' || $file == 'signup-login-manage' || $file == 'confirm-user' || $file == 'confirm-user-success' || $file == 'forgot-password' || $file == 'reset-password' || $file == 'confirm-subscriber' ){
				
			}else{
				//header("Location:login.php?page=$file"); 

				
				header("Location:login.php"); 
				exit();
			}		
		}
	}else{

		if($file == 'login'){
			header("Location:index.php"); 
		}
		
	} */

/* if(isset($_SESSION['CONFESSION_USERTYPE'])){
	if($_SESSION['CONFESSION_USERTYPE'] == 1){
		if(!in_array($file,$superAdminAccess)){
			header("Location:index.php"); 
			exit();
		}
	}
} */

require_once("classes/post-class.php");
