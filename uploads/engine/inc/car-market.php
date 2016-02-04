<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

define('LIC_DOMAIN', /*lic*/'.'/*/lic*/);
define('DLE_CLASSES' , ENGINE_DIR . (($config['version_id'] > 6.3)?'/classes/':'/inc/'));
define('AJAX' , (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')?TRUE:FALSE);

require(ROOT_DIR . '/language/'.$config['langs'].'/car-market.lng');
require(ENGINE_DIR . '/car-market/functions.php');
//require_once (ENGINE_DIR.'/inc/functions.inc.php');
require(ENGINE_DIR . "/car-market/other_fields_array.php");
require (ENGINE_DIR.'/car-market/tables.php');

class Licencing
{
    static private $dom = '';

    public $domain;

    static public $tpl;

    public function __construct($domain)
    {
        
    }

    public static function check()
    {
        
    }
}
$licence = new Licencing(LIC_DOMAIN);

require(ENGINE_DIR . "/Core_modules/Cache.php");
Cache::$array_cache_path = ENGINE_DIR . "/car-market/cache/array/";
Cache::$HTML_cache_path = ENGINE_DIR . "/car-market/cache/";

require(ENGINE_DIR . "/Core_modules/functions.php");
require(ENGINE_DIR . "/Core_modules/ExceptionCore.php");
$exc = new ExceptionErrorHandler('All');
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

try
{
    require(ENGINE_DIR . "/Core_modules/TemplateAdmin.php");
    $tpl = new TemplateAdmin();
    $tpl->setBasePath($config['http_home_url']);
    Licencing::$tpl =& $tpl;
    Func::$tpl =& $tpl;

    require(ENGINE_DIR . "/Core_modules/Timer.php");
    $timer = new Timer($_TIME);

    if (file_exists(ENGINE_DIR . '/data/car-market_conf.php'))
    {
        require_once (ENGINE_DIR . '/data/car-market_conf.php');
    }
    else
    {
        $tpl->msg('Error', $lang_car['module_not_installed']);
    }

    if ($db->mysql_extend == 'MySQLi')
    {
        require(ENGINE_DIR . "/Core_modules/MySQLi_DLE.php");
        $base = new MySQLi_DLE($db, $timer, $tables, PREFIX . "_");
    }
    else
    {
        require(ENGINE_DIR . "/Core_modules/MySQL_DLE.php");
        $base = new MySQL_DLE($db, $timer, $tables, PREFIX . "_");
    }

    require(ENGINE_DIR . "/car-market/classes/CarMarketAdmin.php");
    $auto = new CarMarketAdmin($base, $car_conf, $lang_car, ($config['version_id'] < 7.5)?$member_db:$member_id, $other_fields_array, $checkboxes_array);

    Func::$obj =& $auto;
    
    if (get_magic_quotes_gpc() && function_exists('array_map_recursive'))
    {
        array_map_recursive('stripslashes',$_GET);
        array_map_recursive('stripslashes',$_POST);
        array_map_recursive('stripslashes',$_COOKIE);
        array_map_recursive('stripslashes',$_REQUEST);
    }

    $action = (empty($_REQUEST['action']))?'':$_REQUEST['action'];
    $subaction = (empty($_REQUEST['subaction']))?'':$_REQUEST['subaction'];
    $id = (intval($_REQUEST['id']))?intval($_REQUEST['id']):0;
    $auto->currency_array = array( 'USD' => $auto->lang['USD'],
								   'RUR' => $auto->lang['RUR'],
							       'EUR' => $auto->lang['EUR'],
    );

    $auto->sort_array = array( 'cost' => $auto->lang['sort_cost'],
							   'race' => $auto->lang['sort_race'],
							   'date' => $auto->lang['sort_date'],
						       'year' => $auto->lang['sort_year'],
    );
    if ($auto->config['general_debug'])
    {
        $base->debug = true;
        TemplateAdmin::$Debug_info =& $base->query_list;
    }

    $auto->subsort_array = array( 'ASC' => $auto->lang['subsort_ASC'], 'DESC' => $auto->lang['subsort_DESC']);

    $hidden_array = array("mod" => 'car-market');
    if ($action)
    {
        $hidden_array["action"] = $action;
    }

    $PHP_SELF .= "?mod=car-market&action=";

    if ($config['version_id'] < 9)
    {
        $tpl->SetStyleScript(array('/engine/car-market/css/style_admin.css'), array('/engine/car-market/javascript/jquery.js',
																				'/engine/car-market/javascript/car-market.js',));
    }
    else 
    {
        $tpl->SetStyleScript(array('/engine/car-market/css/style_admin.css'), array('/engine/car-market/javascript/car-market.js'));
    }

    $tpl->menu(array(
    $auto->lang['auto_add'] => array('auto', 'auto.png'),
    $auto->lang['auto_edit'] => array('edit', 'edit.png'),
    $auto->lang['set_city'] => array('cities', 'city.png'),
    $auto->lang['set_model'] => array('models', 'model.png'),
    $auto->lang['other_field']=> array('fields', 'fields.png'),
    $auto->lang['xfields']=> array('xfields', 'xfields.png'),
//    $auto->lang['payment_systems']=> array('payment', 'payment.png'),
    $auto->lang['settings']=> array('settings', 'settings.png')
    ),
    $PHP_SELF, "/engine/car-market/images/admin/menu");
    	
    $ajax_domain = reset(explode($config['admin_path'], $_SERVER['PHP_SELF']));
    	
    $tpl->subhead = <<<SCRIPT
<script type="text/javascript">
var dle_root = '$ajax_domain';
var admin = true;
var ajax_url = dle_root + 'engine/car-market/ajax.php';
var use_country = {$auto->use_country};
var use_region = {$auto->use_region};
</script>
SCRIPT;
    	
    $tpl->footer = FALSE;

    switch ($action)
    {
        case "auto":
            require(ENGINE_DIR . "/car-market/admin/auto.php");
            break;
            
        case "payment":
            require(ENGINE_DIR . "/car-market/admin/payment_systems.php");
            break;
            
        case 'xfields':
            require ENGINE_DIR . '/car-market/admin/xfields.php';
            break;
            	
        case "edit":
            require(ENGINE_DIR . "/car-market/admin/edit_auto.php");
            break;
            	
        case "cities":
            require(ENGINE_DIR . "/car-market/admin/cities.php");
            break;
            	
        case "models":
            require(ENGINE_DIR . "/car-market/admin/models.php");
            break;
            	
        case "fields":
            require(ENGINE_DIR . "/car-market/admin/fields.php");
            break;
            	
        case "field":
            require(ENGINE_DIR . "/car-market/admin/field.php");
            break;
            	
        case "settings":
            require(ENGINE_DIR . "/car-market/admin/settings.php");
            break;
            	
        case "clearcache":
            Cache::ClearAllCache();
            $tpl->msg($auto->lang['clearcache'], $auto->lang['clearcache_ok'], true);
            break;
            	
        case "rebuildcounter":
            $base->Select('auto_models', array('id'));
            while ($row = $base->FetchArray())
            {
                $models[] = $row['id'];
            }
            foreach ($models as $id)
            {
                $count = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'), array('model_id' => $id, "allow_site" => 1));
                $base->Update('auto_models', array('auto_num' => $count['count']), array('id' => $id));
            }
            $base->Select('auto_marks', array("id"));
            while ($row = $base->FetchArray())
            {
                $marks[] = $row['id'];
            }
            foreach ($marks as $id)
            {
                $count = $base->SelectOne('auto_models', array('count' => 'SUM(auto_num)'), array("mark_id" => $id));
                $count_other = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'), array('mark_id' => $id, 'model_id' => 0, "allow_site" => 1));
                $base->Update('auto_marks', array('auto_num' => $count['count'] + $count_other['count']), array('id' => $id));
            }
            Cache::ClearArrayCache();
            $tpl->msg($auto->lang['rebuildcounter'], $auto->lang['rebuildcounter_ok'], true);
            break;
            	
        case "rebuildphotocount":
            $base->Select('auto_autos', array('id'));
            while ($row = $base->FetchArray())
            {
                $autos[] = $row['id'];
            }
            foreach ($autos as $id)
            {
                $count = $base->SelectOne('auto_images', array("count" => "COUNT(*)"), array('auto_id' => $id));
                $base->Update('auto_autos', array('photo_count' => $count['count']), array('id' => $id));
            }
            $tpl->msg($auto->lang['rebuildcounter'], $auto->lang['rebuildcounter_ok'], true);
            break;
            	
        default:
            $all_auto = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'));
            $auto_on_site = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'), array('allow_site' => 1));
            $base->SetWhere('add_date', $timer->cur_time - 24*60*60, ">");
            $today = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'));
            $auto_reg = $base->SelectOne('auto_autos', array('max' => 'MAX(id)'));
            $base->SetBeginBlockWhere();
            $base->SetWhere("exp_date", $timer->cur_time, '>');
            $base->SetWhere("exp_date", '', "=", 'auto_autos', 'OR');
            $base->SetEndBlockWhere();
            $auto_no_moder = $base->SelectOne("auto_autos", array("count" => "COUNT(*)"), array('allow_site' => 0));
            	
            $tpl->header($auto->lang['stats'], true);
            $tpl->OpenTable();
            $tpl->OpenSubtable($auto->lang['stats']);
            $tpl->stats(array(
            $auto->lang['all_auto']     => $all_auto['count'],
            $auto->lang['auto_on_site'] => $auto_on_site['count'] ,
            $auto->lang['auto_today']   => $today['count'],
            $auto->lang['all_auto_reg'] => $auto_reg['max']?$auto_reg['max']:0,
            $auto->lang['no_moder_auto']=> $auto_no_moder['count'],
            $tpl->line 			        => $tpl->line,
							"Версия используемого модуля" => $auto->config['version_id'], 
							"Модуль зарегистрирован на" => "<b><a href=\"http://www." . LIC_DOMAIN . "\" >". LIC_DOMAIN . "<a/></b>", 
							"Страничка поддержки модуля" => "<a href=\"http://www.kaliostro.net/\" ><b><font color=\"green\" >www.kaliostro.net</font><b></a>")
            );
            $tpl->OTable();
            $tpl->OpenForm('', $hidden_array + array('action' => 'clearcache'));
            $tpl->echo = FALSE;
            echo $tpl->row(array($tpl->InputSubmit($auto->lang['clear_cache']) . $tpl->CloseForm(),
            $tpl->OpenForm('', $hidden_array + array('action' => 'rebuildcounter')) . $tpl->InputSubmit($auto->lang['rebuildcounter']) . $tpl->CloseForm(),
            $tpl->OpenForm('', $hidden_array + array('action' => 'rebuildphotocount')) . $tpl->InputSubmit($auto->lang['rebuildphotocount']) . $tpl->CloseForm()), false);
            $tpl->echo = TRUE;
            $tpl->CTable();
            $tpl->CloseSubtable();
            $tpl->CloseTable();
    }
    $tpl->footer(true, 2007);
}
catch (Exception $e)
{
    echoheader();
    echo $e->getMessage();
    echofooter();
}

?>
