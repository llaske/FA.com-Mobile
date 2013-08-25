<?php 
	// Database connection
	define("mysql_server", "localhost");
	define("mysql_user", "root");
	define("mysql_password", "");
	define("mysql_database", "football_lionel");
	
	// Image prefix
	define("prefix_global", "http://www.footballamericain.com/");
	define("prefix_img_small", "http://www.footballamericain.com/images/thumb/");
	define("prefix_img_medium", "http://www.footballamericain.com/images/newsslider/");
	define("prefix_img_team", "http://www.footballamericain.com/images/team/100/");
	
	define("URL","http://www.footballamericain.com") ; // to be compliant with FANFL (url without trailing slash)
	define("URL_IMAGES","http://www.footballamericain.com") ;
	
	// Max constants
	define("max_list_size", "12");
	define("max_resume_size", 90);
	define("max_competition_days", 3);
	define("force_season", "2012");
	
	$_SESSION['URL_REWRITING_ENABLED'] = true ;
	
	date_default_timezone_set('Europe/Paris');	
	
	require_once("p3f-rewriting.class.php"); // business functions FA-NFL	
	
	$oP3F_URL = new P3F_Rewriting("") ; //création de l'objet P3F_URL	
	
	require_once("include/fonctions-fanfl.php"); // business functions FA-NFL	
?>
