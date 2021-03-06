<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}


define('LIC_DOMAIN', '.');
define('DLE_CLASSES' , ENGINE_DIR . (($config['version_id'] > 6.3)?'/classes/':'/inc/'));
define('AJAX' , (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')?TRUE:FALSE);

require(ENGINE_DIR . '/car-market/functions.php');
auto_include_lng('car-market', 'lang_car');
require(ENGINE_DIR . "/car-market/other_fields_array.php");
require (ENGINE_DIR.'/car-market/tables.php');
require (ENGINE_DIR.'/Core_modules/functions.php');

if (file_exists(ENGINE_DIR . '/data/car-market_conf.php'))
require_once (ENGINE_DIR . '/data/car-market_conf.php');
else
{
    msgbox('Error', $lang_car['module_not_installed']);
    return 0;
}

if (!class_exists('Licencing'))
{
    class Licencing
    {
        static private $dom;
    
        public $domain;
    
        public function __construct($domain)
        {
            self::$dom = $domain;
            $this->domain = $domain;
        }
    
        public static function check()
        {
    
        }
    }
}
$licence = new Licencing(LIC_DOMAIN);
require_once(ENGINE_DIR . "/Core_modules/Cache.php");
Cache::$array_cache_path = ENGINE_DIR . "/car-market/cache/array/";
Cache::$HTML_cache_path = ENGINE_DIR . "/car-market/cache/";

require_once(ENGINE_DIR . "/Core_modules/ExceptionCore.php");
$exc = new ExceptionErrorHandler(array(E_ERROR, E_WARNING, E_USER_WARNING));
if ($car_conf['general_debug'])
{
    ExceptionErrorHandler::$log_type = 'show';
    ExceptionDataBase::$log_type = 'show';
    ExceptionAllError::$log_type = 'show';
}
else
{
    ExceptionErrorHandler::$log_type = 'file';
    ExceptionErrorHandler::$log_file = ENGINE_DIR . "/car-market/logs/HandlerErrors.log";
    ExceptionDataBase::$log_type = 'file';
    ExceptionDataBase::$log_file = ENGINE_DIR . "/car-market/logs/database.log";
    ExceptionAllError::$log_type = 'file';
    ExceptionAllError::$log_file = ENGINE_DIR . "/car-market/logs/errors.log";
}
require_once(ENGINE_DIR . "/Core_modules/TemplateUser.php");
$template = new TemplateUser($tpl, 'car-market/');
$template->setBasePath($config['http_home_url']);

require_once(ENGINE_DIR . "/Core_modules/Timer.php");
$timer = new Timer($_TIME);

if ($db->mysql_extend == 'MySQLi')
{
    require_once(ENGINE_DIR . "/Core_modules/MySQLi_DLE.php");
    $base = new MySQLi_DLE($db, $timer, $tables, PREFIX . "_");
}
else
{
    require_once(ENGINE_DIR . "/Core_modules/MySQL_DLE.php");
    $base = new MySQL_DLE($db, $timer, $tables, PREFIX . "_");
}
if (!$base->isConnect())
{
    $base->Connect(DBHOST, $port = '', DBUSER, DBPASS, DBNAME, $usepconnect = false, COLLATE);
}

require(ENGINE_DIR . "/car-market/classes/CarMarketUser.php");
$auto = new CarMarketUser($base, $car_conf, $lang_car, $member_id, $other_fields_array, $checkboxes_array);
$auto->tpl =& $template;
//$auto->tpl->lang['go_to_back'] = $auto->lang['go_to_back'];

include_once ENGINE_DIR.'/car-market/classes/Fields.php';
$auto->xfields = new Fields($base, $auto);

$template->use_alt_url = $auto->config['general_mod_rewrite'];

if ($auto->config['general_main_page'])
{
    $template->main_alt_url = substr($config['http_home_url'], 0, strlen($config['http_home_url']) - 1);
    $template->main_url = $PHP_SELF;
}
else
{
    $template->main_alt_url = $config['http_home_url'] . $auto->config['general_name_module'];
    $template->main_url = $PHP_SELF . "?do=" . $auto->config['general_name_module'];
}
$template->alt_url_array = array(
								"action" => '/',
								"auto" => 'auto',
								"country_id" => '/country-',
								"region_id" => '/region-',
								"city_id" => '/city-',
								"page" => '/page',
);

if (get_magic_quotes_gpc() && function_exists('array_map_recursive'))
{
    array_map_recursive('stripslashes', $_GET);
    array_map_recursive('stripslashes', $_POST);
    array_map_recursive('stripslashes', $_COOKIE);
    array_map_recursive('stripslashes', $_REQUEST);
}

$auto->currency_array = array( 'USD' => $auto->lang['USD'],
							   'RUR' => $auto->lang['RUR'],
						       'EUR' => $auto->lang['EUR'],
);

$auto->sort_array = array( 'cost' => $auto->lang['sort_cost'],
						   'race' => $auto->lang['sort_race'],
						   'date' => $auto->lang['sort_date'],
					       'year' => $auto->lang['sort_year'],
					       'author' => $auto->lang['sort_author'],
					       'exp_date' => $auto->lang['sort_exp_date'],
);

$auto->subsort_array = array( 'ASC' => $auto->lang['subsort_ASC'], 'DESC' => $auto->lang['subsort_DESC']);

?>