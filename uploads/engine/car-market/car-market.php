<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}
if (!$is_logged)
{
    $member_id['user_group'] = 5;
    $member_id['user_id'] = 0;
}

try
{
    if (!require_once(ENGINE_DIR . "/car-market/includes.php"))
    {
        return ;
    }
    
    define("MODER", (in_array($member_id['user_group'], $auto->config['general_moderator']))?TRUE:FALSE);
}
catch (Exception $e)
{
    if ($member_id['user_group'] == 1)
    {
        msgbox("Error", $e->getMessage());
    }
    else
    {
        msgbox("Error", $lang_car['global_error']);
    }
}

if (!$auto->config['general_allow_module'] && $auto->member['group'] != 1)
{
    msgbox('Error', $auto->lang['module_disabled']);
    return ;
}

preg_match_all('#(\w+)=([^~&]*)#i', $_SERVER['REQUEST_URI'], $matches);
if ($matches)
{
    foreach ($matches[1] as $var=>$value)
    {
        $_GET[$value] = $_REQUEST[$value] = $matches[2][$var];
    }
}

define("RSS", false);
$action = (empty($_REQUEST['action']))?'':$_REQUEST['action'];
$subaction = (empty($_REQUEST['subaction']))?'':$_REQUEST['subaction'];
$id = (!empty($_REQUEST['id']) && intval($_REQUEST['id']))?intval($_REQUEST['id']):0;

$hidden_array['action'] = $action;

if (!$auto->config['general_main_page'])
{
    if ($action == 'main' || !$action)
    {
        $template->TitleSpeedBar($auto->lang['name_module']);
    }
    else
    {
        $template->TitleSpeedBar($auto->lang['name_module'], $template->GetUrl());
    }
}
if (!$auto->config['general_main_page'])
{
    $hidden_array["do"] = $auto->config['general_name_module'];
}


if ($subaction == "del" && $_POST['selected_auto'])
{
    if ($auto->DelAuto($_POST['selected_auto']))
    {
        $template->msg($auto->lang['del_auto'], $auto->lang['del_auto_desc']);
        Cache::ClearAllCache();
    }
    else
    {
        $template->msg($auto->lang['error'], $auto->lang['del_auto_error']);
    }
}


$template->SetStyleScript(array('{THEME}/car-market/css/style_user.css'), array('/engine/car-market/javascript/preload.js', /*
																				'/engine/car-market/javascript/jquery.cookie.js',
																				'/engine/car-market/javascript/jquery.blockUI.js',
																				//'/engine/car-market/javascript/car-market.js?v=2.3.0',*/
));
if ($auto->config['general_AJAX'])
$AJAX = <<<JS
$(document).ready(function()
{
	$("a.ajax_link").click(function()
	{
		BlockContent($("#auto-content"));
		url = $(this).attr("href");
		$("#auto-content").load(url, function()
		{
			$("a.ajax_link").unbind("click");
			$.getScript("{$config['http_home_url']}engine/car-market/javascript/response_ajax.js");
			return false;
		});
		
		return false;
	});
});
JS;
else
$AJAX = '';

$template->subhead .= <<<SCRIPT
<script type="text/javascript">
var ajax_url = dle_root + 'engine/car-market/ajax.php';
var use_country = {$auto->use_country};
var use_region  = {$auto->use_region};
var auto_edit   = '{$auto->lang['auto_edit']}';
var auto_del    = '{$auto->lang['auto_del']}';
var auto_allow_yes = '{$auto->lang['auto_allow_yes']}';
var auto_allow_no  = '{$auto->lang['auto_allow_no']}';
var compare_error  = '{$auto->lang['compare_error']}';
var send  = '{$auto->lang['send']}';
var send_ok  = '{$auto->lang['send_ok']}';
var send_error  = '{$auto->lang['send_error']}';
var email_error  = '{$auto->lang['email_error']}';
var from_name_error  = '{$auto->lang['from_name_error']}';
var subj_error  = '{$auto->lang['subj_error']}';
var text_error  = '{$auto->lang['text_error']}';
var more_favorites  = '{$auto->lang['more_favorites']}';
$AJAX
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.cookie.js');
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.blockUI.js');
</script>
SCRIPT;

if (!AJAX)
{
    $template->load('send_mail')
            ->SetForm(array(), '', 'POST', 'id="send_form"', true)
            ->WrapContent('<div style="display:none; cursor: default;" id="send_mail" >', '</div>')
            ->Set($config['http_home_url'], '{site_link}');

    if (!$is_logged)
    {
        $template->SetBlock('not_logged');
    }
    else
    {
        $template->Set($auto->member['name'], '{user_from}');
    }
    
    $template->Compile('content');
}


if (isset($_COOKIE['auto_session']) && strlen($_COOKIE['auto_session']) == 32 && $auto->member['id'])
{
    $base->Update('auto_autos', array('author_id' => $auto->member['id'], 'guest_session' => '', "author_ip" => $auto->member['ip'], "author" => $auto->member['name']), array("guest_session" => $_COOKIE['auto_session'], "author_id" => 0));
    set_cookie("auto_session", '', -1);
}

$metatags['description'] = $auto->lang['meta_descr_default'];
$metatags['keywords'] = $auto->lang['meta_keys_default'];

// check cron
$cron_time = Cache::GetHTMLCache('cron_time');
if ($action != 'cron' && $cron_time < (time() - 3600 * 2))
{
    RunCron($auto);
}

try {
    
    switch ($action)
    {
        case "auto":
            require(ENGINE_DIR . "/car-market/user/auto.php");
            break;
            	
        case "doadd":
        case "save":
        case "add":
        case "edit":
            require(ENGINE_DIR . "/car-market/user/add_auto.php");
            break;
            	
        case "search":
            require(ENGINE_DIR . "/car-market/user/search.php");
            break;
            	
        case "send":
            require(ENGINE_DIR . "/car-market/user/send_auto.php");
            break;
            	
        case "account":
            require(ENGINE_DIR . "/car-market/user/account.php");
            break;
            	
        case "main":
            require(ENGINE_DIR . "/car-market/user/main.php");
            break;
            
        case "cron":
            require(ENGINE_DIR . "/car-market/cron.php");
            break;
            	
        default:
            require(ENGINE_DIR . "/car-market/user/default.php");
            break;
    }
}
catch (Exception $e)
{
    if ($member_id['user_group'] == 1)
    msgbox("Error", $e->getMessage());
    else
    msgbox("Error", $lang_car['global_error']);
}

if (AJAX)
{
    @header("Content-type: text/css; charset=".$config['charset']);
    echo $tpl->result['content'];
    exit;
}

if ($auto->config['general_debug'] && $base->query_list)
{
    $quer_list = wordwrap(print_r($base->query_list, true), 100);
    $tpl->result['content'] .= "<pre>" . $quer_list . "</pre>";
}

$TitleSpeedBar = $template->TitleSpeedBar();

include_once(ENGINE_DIR . "/car-market/blocks.php");

?>