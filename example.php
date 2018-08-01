<?php

	//Written by Dominick Lee
	//Last Modified 2/27/2017

	//Modified on 2nd July 2018 by BevHost
	// removed database class in favour of native PDO
	// used class extender for database parameters

	//Enable the below two lines to show errors:
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');

	session_set_cookie_params ( 0, '/', $_SERVER["HTTP_HOST"] , true, true);

	include("mysql.sessions.php");	//Include PHP MySQL sessions

	class MySession extends Session {
	        var $database = 'websessdb';
        	var $username = 'sessuser';
	        var $password = '$0m3 $3cr3t';
        	var $hostname = '10.98.76.54';
		var $debug = true;
	}

	$session = new MySession();	//Start a new PHP MySQL session
	
	//Store variable as usual
	$_SESSION['visits'] = isset($_SESSION['visits']) ? $_SESSION['visits'] + 1 : 1;
	
	//Show stored user
	//echo $_SESSION['visits'];
	
	//The following functions are used for sign-out:
	
	//Clear session data (only data column)
	//session_unset();
	
	//Destroy the entire session
	//session_destroy();
?>
