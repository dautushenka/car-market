<?php

@session_start();

@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');
define("RSS", false);

$member_id = FALSE;
$is_logged = FALSE;
$allow_sql_skin = false;

include ENGINE_DIR.'/data/config.php';
define('DLE_CLASSES' , ENGINE_DIR . (($config['version_id'] > 6.3)?'/classes/':'/inc/'));

if ($config['http_home_url'] == "")
{

    $config['http_home_url'] = explode("engine/car-market/compare.php", $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
    $config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

$PHP_SELF = $config['http_home_url']."index.php";
$_TIME = time()+($config['date_adjust']*60);

require_once DLE_CLASSES.'mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require(ENGINE_DIR . '/modules/functions.php');
require_once ENGINE_DIR.'/modules/sitelogin.php';
if ($config["lang_".$config['skin']]) {

    include_once ROOT_DIR.'/language/'.$config["lang_".$config['skin']].'/website.lng';

} else {

    include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';

}
$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

check_xss();

require_once DLE_CLASSES.'templates.class.php';
$tpl = new dle_template;
$tpl->dir = ROOT_DIR.'/templates/'.$config['skin'];

require_once(ENGINE_DIR . "/car-market/includes.php");

$base->Connect(DBHOST, $port = '', DBUSER, DBPASS, DBNAME, $usepconnect = false, COLLATE);

$template->_blank = true;
$template->SetStyleScript(array('{THEME}/car-market/css/style_user.css',
								'/engine/car-market/css/jquery.lightbox-0.5.css'), array('/engine/car-market/javascript/jquery.js', 
																				'/engine/car-market/javascript/jquery.cookie.js',
																				'/engine/car-market/javascript/jquery.blockUI.js',
																				'/engine/car-market/javascript/car-market.js',
																				'/engine/car-market/javascript/jquery.lightbox-0.5.js'
																				));

																				$template->subhead = <<<SCRIPT
<script type="text/javascript">
var dle_root       = '{$config['http_home_url']}';
var dle_skin       = '{$config['skin']}';
var ajax_url = dle_root + 'engine/car-market/ajax.php';
var use_country = {$auto->use_country};
var use_region  = {$auto->use_region};

$(document).ready(function()
{
	$("a.go_big_photo").lightBox(
	{
		imageLoading: '/engine/car-market/images/admin/lightbox-ico-loading.gif',
		imageBtnPrev: '/engine/car-market/images/admin/lightbox-btn-prev.gif',
		imageBtnNext: '/engine/car-market/images/admin/lightbox-btn-next.gif',
		imageBtnClose: '/engine/car-market/images/admin/lightbox-btn-close.gif',
		imageBlank: '/engine/car-market/images/admin/lightbox-blank.gif',
		txtImage : '{$auto->lang['txtImage']}',
		txtOf : '{$auto->lang['txtOf']}'
	});
	
	$("img.close_compare_auto").click(function()
	{
		$(this).parents("tr").fadeOut();
	});
	
	$("#equal").click(function()
	{
		if ($(".equal").is(":hidden"))
			$(".equal").fadeIn("normal");
		else
			$(".equal").fadeOut("normal");
			
		return false;
	});
});
</script>
SCRIPT;

																				function CompareField(array $array)
																				{
																				    global $equal_array, $first_auto;

																				    if (!$first_auto)
																				    {
																				        $first_auto = $array;
																				        return ;
																				    }

																				    foreach ($array as $key=>$value)
																				    {
																				        if ((!isset($equal_array[$key]) || $equal_array[$key] !== 0) && $first_auto[$key] == $value)
																				        $equal_array[$key] = 1;
																				        else
																				        $equal_array[$key] = 0;
																				    }
																				}


																				$autos = explode(",", $_GET['id']);

																				$template->load('compare');
																				if (count($autos) >= 2)
																				{
																				    $auto->Search(array("get_count" => 0), $autos);
																				    $template->OpenRow('row_auto');
																				    $equal_array = array(); $first_auto = array();
																				    foreach ($auto->autos as $id=>$auto_one)
																				    {
																				        $array = $auto->ShowAuto($id);
																				        CompareField($array);
																				        $template->SetRow($array, 'row_auto');
																				    }
																				    $template->CloseRow('row_auto');
																				    foreach ($equal_array as $tag=>$equal)
																				    {
																				        if ($equal)
																				        $template->Set('equal', "__" . str_replace(array("{", "}"), array("", ""),$tag) . "__");
																				    }
																				}
																				else
																				$template->SetBlock('row_auto', $auto->lang['compare_error']);


																				$template->Compile('content');

																				echo $tpl->result['content'];

																				?>