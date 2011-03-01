<?php

function DoEvents($that) {
	global $_CONF , $_TSM;

	$_TSM["MENU"] = "";

	//checking if user is logged in
	if (!$_SESSION["minibase"]["user"]) {

		if ($_SERVER["REQUEST_METHOD"] == "POST") {

			//autentificate
			$user = $that->db->QFetchArray("select * from {$that->tables[users]} where `user_login` = '{$_POST[user]}' AND `user_password` = '{$_POST[pass]}'");

			if (is_array($user)) {
				$_SESSION["minibase"]["user"] = 1;
				$_SESSION["minibase"]["raw"] = $user;

				//redirecing to viuw sites
				header("Location: $_CONF[default_location]");
				exit;
			} else
				return $that->templates["login"]->blocks["Login"]->output;

		} else
			return $that->templates["login"]->blocks["Login"]->output;
	}
	if ($_SESSION["minibase"]["raw"]["user_level"] == 0) {
		$_TSM["MENU"] = $that->templates["login"]->blocks["MenuAdmin"]->output;
	} else {
		$_TSM["MENU"] = $that->templates["login"]->blocks["MenuUser"]->output;
	}

	if (!$_POST["task_user"])
		$_POST["task_user"] = $_SESSION["minibase"]["user"];

	if($_SESSION["minibase"]["raw"]["user_level"] == 1) {
		$_CONF["forms"]["adminpath"] = $_CONF["forms"]["userpath"];
	}

	switch ($_GET["sub"]) {
		case "logout":
			unset($_SESSION["minibase"]["user"]);
			header("Location: index.php");

			return $that->templates["login"]->EmptyVars();
		break;

		
		case "types":
		case "staff":
		case "help":
		case "notes":
		case "users":
		case "workers":

			if ($_GET["sub"] == "workers") {
				
				if ((!$_GET["action"])&&($_SESSION["minibase"]["raw"]["user_level"] != 0 )) {
					$_GET["action"] = "details";				
				}

				if ($_SESSION["minibase"]["raw"]["user_level"] == 1) {
					$_GET["user_id"] = $_SESSION["minibase"]["raw"]["user_id"]; 
					$_POST["user_id"] = $_SESSION["minibase"]["raw"]["user_id"];
				}
			}

			if (is_subaction("help" , "details") && $_GET["section"]) {
				$data = new CSQLAdmin("notes", $_CONF["forms"]["admintemplate"],$that->db,$that->tables);					
				$extra["details"]["after"] = $data->DoEvents();
			}



			$data = new CSQLAdmin($_GET["sub"], $_CONF["forms"]["admintemplate"],$that->db,$that->tables,$extra);

			if (is_subaction("help" , "details")  && $_GET["section"]) {
				foreach ($data->forms["forms"]["details"]["fields"] as $key => $val) {
					if ($found == true)
						unset($data->forms["forms"]["details"]["fields"][$key]);

					if (!$found && ($key == "section"))
						$found = "true";
				}				
			}

			if (is_subaction("help" , "")) {
					$file = $_CONF["forms"]["adminpath"] . "help/search.xml" ;
					$search = new CForm($_CONF["forms"]["admintemplate"],$that->db,$that->tables);
					$extra2["after"] = $data->DoEvents();
					return $search->Show($file , array("values"=>$_GET) , $extra2);
			}
			return $data->DoEvents();

			return $data->DoEvents();
		break;
	}
}

?>