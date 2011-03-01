<?php
if (!isset($_SERVER["REQUEST_URI"])) {
	$_SERVER["REQUEST_URI"] = 
			(strtoupper($_SERVER["HTTPS"]) == "on" ? "https://" :  "http://") . 
			$_SERVER["SERVER_NAME"] . 
			($_SERVER["SERVER_PORT"] != 80 ? ':' . $_SERVER["SERVER_PORT"] : '') .
			$_SERVER["SCRIPT_NAME"] .
			( $_SERVER["QUERY_STRING"] ? '?' . $_SERVER["QUERY_STRING"] : '' );
}

// do a fast check for the modules path
if (!defined("_MODPATH")) {
	define("_MODPATH" , "modules/");
}

//devel stuff only



$_preg_IDENTITY_CODE = "<font style='font-size:0px'>1.1</font>";


session_start();

//error_reporting(0);

require_once _LIBPATH . "common.php";
require_once _LIBPATH . "xml.php";
require_once _LIBPATH . "template.php";
require_once _LIBPATH . "config.php";
require_once _LIBPATH . "html.php";
require_once _LIBPATH . "database.php";
require_once _LIBPATH . "vars.php";
require_once _LIBPATH . "menu.php";
require_once _LIBPATH . "library.php";
require_once _LIBPATH . "sqladmin.php";
require_once _LIBPATH . "forms.php";
require_once _LIBPATH . "mail.php";
require_once _LIBPATH . "sendmail.php";
//require_once _LIBPATH . "rss.php";
//require_once _LIBPATH . "pay.php";

class CBase {
	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $html;
	
}
class CSite {

	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $admin;
	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $html;
	

	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function CSite($xml , $admin = false , $preload_modules = true) {
		global $_CONF , $base , $_VARS;

		$this->admin = $admin;
		$this->preload = $preload_modules;

		//loading the config
		$tmp_config = new CConfig($xml);

		$_CONF = $tmp_config->vars["config"];

		//fix the paths
		if (($this->admin) && is_array($_CONF["paths"])){
			$_CONF["upload"] = $_CONF["paths"]["admin"]["upload"];
			$_CONF["path"] = $_CONF["paths"]["admin"]["path"];
			$_CONF["url"] = $_CONF["paths"]["admin"]["url"];
		} else {
			$_CONF["upload"] = $_CONF["paths"]["site"]["upload"];
			$_CONF["path"] = $_CONF["paths"]["site"]["path"];
			$_CONF["url"] = $_CONF["paths"]["site"]["url"];
		}
			

		//loading the templates
		if ($this->admin) {
			if (is_array($_CONF["templates"]["admin"])) {
				foreach ($_CONF["templates"]["admin"] as $key => $val) {
					if ($key != "path")
						$this->templates[$key] = new CTemplate($_CONF["templates"]["admin"]["path"] . $_CONF["templates"]["admin"][$key]);
				}			
			}			
		} else {

			//allow both methods of having the data in the <site> or directly in the root
			if (is_array($_CONF["templates"]["site"]))
				$_CONF["templates"] = $_CONF["templates"]["site"];

			if (is_array($_CONF["templates"])) {
				foreach ($_CONF["templates"] as $key => $val) {
					if (($key != "path" ) && ($key != "admin"))
						$this->templates[$key] = new CTemplate($_CONF["templates"]["path"] . $_CONF["templates"][$key]);
				}				
			}
		}
		

		$base = new CBase();
		$base->html = new CHtml();
		$this->html = &$base->html;

		//make a connection to db
		if (is_array($_CONF["database"])) {
			$this->db = new CDatabase($_CONF["database"]);

			//vars only if needed
			if ($_CONF["tables"]["vars"]) {
				$this->vars = new CVars($this->db , $_CONF["tables"]["vars"]);
				$base->vars = &$this->vars;
				$_VARS = $this->vars->data;
			}

			$this->tables = &$_CONF["tables"];
		}				
		
	}

	function TableFiller($item) {
		if (file_exists("pb_tf.php")) {
			include("pb_tf.php");
		}
	}

	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function Run() {
		global $_TSM , $_preg_IDENTITY_CODE, $_CONF , $_USER , $_VARS , $_PAGE , $base;
		$_USER = $_SESSION["minibase"]["raw"];

		if ($this->admin) {
			$_CONF["modules"] = $_CONF["modules"]["admin"];
			unset($_CONF["modules"]["admin"]);
		} else {
			$_CONF["modules"] = $_CONF["modules"]["site"];
			//unset($_CONF["modules"]["admin"]);
		}

		if (!$this->preload) {
			foreach ($_CONF["modules"] as $key => $val) {
				if ($_GET["mod"] != $val) {
					unset($_CONF["modules"][$key]);
				}				
			}			
		}
		

		//replace some global vars in the template, i'm doing it here, becouse in modules i may want to change them
		if (is_array($_CONF["vars"])) {
			foreach ($_CONF["vars"] as $key => $var) {
				$_TSM["MINIBASE." . strtoupper($key)] = $var;
			}			
		}
		
		//do a module detection now
		if ($this->admin) {
			//add the menus for the navigation
			$_TSM["MINIBASE.POSTMENU"] = file_exists("templates/menu.post.htm") ? GetFileContents("templates/menu.post.htm") : "";
			$_TSM["MINIBASE.PREMENU"] = file_exists("templates/menu.pre.htm") ? GetFileContents("templates/menu.pre.htm") : "";
			
			//okay, first be a bitch and do the autentification thingy
			if (!$_SESSION["minibase"]["user"]) {
				//force to the auth module
				$_GET["mod"] = "auth";
				//no action = login screen
				$_GET["sub"] = ($_GET["sub"] == "recover") || ($_GET["sub"] == "recover.thanks") ? $_GET["sub"] : "";
				$_GET["action"] = "";
			} else {

				//in case there is specified and index.php?redirect=/.///
				if ($_GET["redirect"]) {
					header("Location: " . urldecode($_GET["redirect"]));
					exit;
				}				
			}
		}
		if (is_array($_CONF["modules"])) {
			//okay initialize the new module now;
			foreach ($_CONF["modules"] as $_KMOD => $_MOD) {

					$file = _MODPATH . $_MOD . "/" . ($this->admin ? "admin.php" : "site.php");

					//detect if the file exists
					if (file_exists($file)) {
							require_once $file;
							eval("\$this->modules[\"". $_MOD. "\"] = new c{$_MOD}();");				
							//send the used params
							$this->modules[$_MOD]->templates = $this->templates;
							$this->modules[$_MOD]->tables = $this->tables;
							$this->modules[$_MOD]->vars = $this->vars;
							$this->modules[$_MOD]->db = $this->db;

							$_CONF["forms"]["adminpath"] = _MODPATH . $_MOD . "/forms/";

							//read the module config if any exists
							if (file_exists( _MODPATH . $_MOD . "/" . "module.xml")) {
								$this->modules[$_MOD]->config = new CConfig(_MODPATH . $_MOD . "/" . "module.xml");
								$this->modules[$_MOD]->_CONF = $this->modules[$_MOD]->config->vars["module"];

								if ($this->admin)
									$this->modules[$_MOD]->config->vars["module"]["templates"] = is_array($this->modules[$_MOD]->config->vars["module"]["admin"]["templates"]) ? $this->modules[$_MOD]->config->vars["module"]["admin"]["templates"] : $this->modules[$_MOD]->config->vars["module"]["templates"];
								else
									$this->modules[$_MOD]->config->vars["module"]["templates"] = is_array($this->modules[$_MOD]->config->vars["module"]["site"]["templates"]) ? $this->modules[$_MOD]->config->vars["module"]["site"]["templates"] : $this->modules[$_MOD]->config->vars["module"]["templates"];
								

								//load the specific files
								if (is_array($this->modules[$_MOD]->config->vars["module"]["templates"])) {
									foreach ($this->modules[$_MOD]->config->vars["module"]["templates"] as $key => $val) {
										if ($key != "path") {
											$template = isset($this->modules[$_MOD]->config->vars["module"]["templates"]["path"]) ? $this->modules[$_MOD]->config->vars["module"]["templates"]["path"] . $val : _MODPATH . $_MOD . "/templates/" . $val ;
											$this->modules[$_MOD]->private->templates[$key] = new CTemplate( $template);
										}
										
										//$this->modules[$_MOD]->private->templates[$key] = new CTemplate(_MODPATH . $_MOD . "/templates/" . $val );
									}								
								}

								if ($this->admin)
									$this->modules[$_MOD]->private->tables = is_array($this->modules[$_MOD]->config->vars["module"]["admin"]["tables"]) ? $this->modules[$_MOD]->config->vars["module"]["admin"]["tables"] : $this->modules[$_MOD]->config->vars["module"]["tables"];
								else
									$this->modules[$_MOD]->private->tables = is_array($this->modules[$_MOD]->config->vars["module"]["site"]["tables"]) ? $this->modules[$_MOD]->config->vars["module"]["site"]["tables"] : $this->modules[$_MOD]->config->vars["module"]["tables"];

								//load the tables
								if (is_array($this->modules[$_MOD]->config->vars["module"]["tables"])) {
									$this->modules[$_MOD]->private->tables = is_array($this->modules[$_MOD]->config->vars["module"]["admin"]["tables"]) ? $this->modules[$_MOD]->config->vars["module"]["admin"]["tables"] : $this->modules[$_MOD]->config->vars["module"]["tables"];

									//do a check for the private vars table if available
									foreach ($this->modules[$_MOD]->private->tables as $key => $val) {
										if ($key == "vars") {
											$this->modules[$_MOD]->private->vars = new CVars($this->db , $val);
										}									
									}															
								}							
							}
							
							if ($_GET["mod"] == $_MOD) {
								//if is the module then return in the layout the results
								$_TSM[strtoupper($_MOD)] = $_TSM["PB_EVENTS"] = $this->modules[$_MOD]->DoEvents();

								//control variable to see if there was found a module
								$executed_module = true;
							} else {
								//elese simply execute for global routines fo the module
								$_TSM[strtoupper($_MOD)] = $this->modules[$_MOD]->DoEvents();
							}								
				}

				if (file_exists(_MODPATH . $_MOD . "/" . "menu.xml") && $this->admin) {
					$menu = new CConfig (_MODPATH . $_MOD . "/" . "menu.xml");
					if (is_array($menu->vars["menu"]["level_" . $_SESSION["minibase"]["raw"]["user_level"] ] ))
						$menu = $menu->vars["menu"]["level_" . $_SESSION["minibase"]["raw"]["user_level"] ];
					else
						$menu = $menu->vars["menu"];

					if (is_array($menu)) {
						$tmp_menu = "";
						foreach ($menu as $key => $val) {
							$_links = "";

							if (is_array($val["links"])) {
								foreach ($val["links"] as $k => $v) {
									$_links[] = array(
													"title"	=> ucwords($k),
													"link" => $v
												);
								}								
							}

							$val["title"] = $val["title"] ? $val["title"] : ucwords ($key);
							$val["id"] = str_replace(" ", "_" , $key);

							$tmp_menu .= $this->templates["menus"]->blocks["MenuGroup"]->Replace(array(
												"title_data" => $this->templates["menus"]->blocks[$val["link"] ? "TitleLink" : "Title"]->Replace($val) ,
												"data" => is_array($_links) ? $base->html->Table($this->templates["menus"],"Links",$_links) : "",
												"id" => $val["id"],
												"collapse" =>is_array($_links) ? $this->templates["menus"]->blocks["Collapse"]->Replace($val) : ""
											));
						}						


						$menus .= $tmp_menu;
					}										
 				} else
					//do a search for menus
					if (file_exists(_MODPATH . $_MOD . "/" . "menu.htm") && $this->admin) {
						//read the menus
						$tmp_menu = new CTemplate(_MODPATH . $_MOD . "/" . "menu.htm");

						//check if there is made any difference between users levels
						if (is_object($tmp_menu->blocks["MenuLevel" . (int)$_SESSION["minibase"]["raw"]["user_level"]]))
							$menus .= $tmp_menu->blocks["MenuLevel" . (int)$_SESSION["minibase"]["raw"]["user_level"]]->output;
						else
							//load a menu block depending the user level
							$menus .= !count($tmp_menu->blocks) ? $tmp_menu->output : "";
						
					} else {
						//here will be in future the xml menu
					}																
			}			
		}

			
		if (is_object($this->templates["menus"]) && $this->admin) {
			$menus = new CTemplate($menus,"string");
			$_TSM["MINIBASE.MENU"] = $_SESSION["minibase"]["user"] ? $this->templates["menus"]->blocks["Menu"]->Replace(array("MENUS.CONTENT"=>$menus->Replace($_TSM))) : "";
		} else {
			$_TSM["MINIBASE.MENU"] = "";
		}
		//build the menus now
		
		

		if (file_exists("pb_events.php") && !$executed_module) {
			include("pb_events.php");
			
			$_TSM["PB_EVENTS"] = @DoEvents($this);
		}

		if (!$_TSM["PB_EVENTS"]) {
			$_TSM["PB_EVENTS"] = "";
		}
		

		if ($_GET["devel"] == "phpinfo") {
				ob_start(); 
				phpinfo(); 
				$phpinfo .= ob_get_contents(); 
				ob_end_clean(); 
		//		$phpinfo = str_replace("td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}" , "", $phpinfo );

				$search = array ("'<style[^>]*?>.*?</style>'si"

								);                    

				$replace = array (""
								);		

				$phpinfo = preg_replace ($search, $replace, $phpinfo); 
				$phpinfo = str_replace(
								array(
									'class="e"',
									'class="v"',
									'class="h"'
					
								),
								array(
									'style=" border: 1px solid #000000; font-size: 75%; vertical-align: baseline;background-color: #ccccff; font-weight: bold; color: #000000;"',
									'style=" border: 1px solid #000000; font-size: 75%; vertical-align: baseline;background-color: #cccccc; color: #000000;"',
									'style=" border: 1px solid #000000; font-size: 75%; vertical-align: baseline;background-color: #9999cc; font-weight: bold; color: #000000;"'
								),
								$phpinfo
							);

				$_TSM["PB_EVENTS"] = "<script>draw_box ( '550' , 1 , 'Php Info' );</script> <div style='width:589;height:600;overflow:auto'>$phpinfo</div><script>draw_box ( '' , 2 , 'Php Info' );</script>";

		}				

		if (isset($_PAGE)) {
//			echo $_CONF["path"] . "templates/$_PAGE/layout.xml";
			$this->layout = new CLayout($_CONF["path"] . "templates/$_PAGE/layout.xml");		
			$this->layout->Build();
			$this->layout->Show();
			die;
		}
				

		if (is_object($this->templates["layout"])) {
			echo $this->templates["layout"]->Replace($_TSM) . $_preg_IDENTITY_CODE ;
		}		
	}
}


?>