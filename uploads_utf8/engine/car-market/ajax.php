<?php

@session_start();

@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

$member_id = FALSE;
$is_logged = FALSE;
$allow_sql_skin = false;

include ENGINE_DIR.'/data/config.php';

define('DLE_CLASSES' , ENGINE_DIR . (($config['version_id'] > 6.3)?'/classes/':'/inc/'));
define('AJAX', true);

if ($config['http_home_url'] == "")
{

    $config['http_home_url'] = explode("engine/car-market/ajax.php", $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
    $config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

if (isset ( $_COOKIE['dle_skin'] ) and $_COOKIE['dle_skin'] != '' && @is_dir ( ROOT_DIR . '/templates/' . $_COOKIE['dle_skin'] ))
$config['skin'] = $_COOKIE['dle_skin'];

$PHP_SELF = $config['http_home_url']."index.php";
$_TIME = time()+($config['date_adjust']*60);

require_once DLE_CLASSES.'mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require(ENGINE_DIR . '/modules/functions.php');
require_once ENGINE_DIR.'/modules/sitelogin.php';

if (isset($config["lang_" . $config['skin']]) and $config["lang_" . $config['skin']] != '')
require ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . "/website.lng";
else
require(ROOT_DIR . "/language/".$config['langs']."/website.lng");

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

check_xss();

require_once DLE_CLASSES.'templates.class.php';
$tpl = new dle_template;
$tpl->dir = ROOT_DIR.'/templates/'.$config['skin'];

require_once(ENGINE_DIR . "/car-market/includes.php");

define("MODER", (in_array($member_id['user_group'], $auto->config['general_moderator']))?TRUE:FALSE);

$action = (empty($_REQUEST['action']))?'':$_REQUEST['action'];
$id = (intval($_REQUEST['id']))?intval($_REQUEST['id']):0;
$search = (empty($_REQUEST['search']))?false:true;
$currecy_array = array( 'USD' => $auto->lang['USD'],
						'RUR' => $auto->lang['RUR'],
						'EUR' => $auto->lang['EUR'],
);

switch ($action)
{
    case 'GetModel':
        $auto->GetModels($id, $search); $result = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n<list>\n";
        foreach ($auto->models as $id=>$name)
        {
            $result .= "<item id=\"$id\"><![CDATA[$name]]></item>\n";
        }
        $result .= "</list>";
        header('Content-Type: text/xml; charset="' . $config['charset'] . '"');
        echo $result;
        exit();
        break;

    case 'GetRegion':
        $auto->GetRegions($id, $search); $result = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n<list>\n";
        foreach ($auto->regions as $id=>$name)
        {
            $result .= "<item id=\"$id\">$name</item>\n";
        }
        $result .= "</list>";
        header('Content-Type: text/xml; charset="' . $config['charset'] . '"');
        echo $result;
        exit();
        break;

    case 'GetCity':
        if (!$auto->use_region)
        $auto->GetCities($id, 0, $search);
        else
        $auto->GetCities(0, $id, $search);
        $result = "<?xml version=\"1.0\" encoding=\"windows-1251\" ?>\n<list>\n";
        foreach ($auto->cities as $id=>$name)
        {
            $result .= "<item id=\"$id\">$name</item>\n";
        }
        $result .= "</list>";
        header('Content-Type: text/xml; charset="' . $config['charset'] . '"');
        echo $result;
        exit();
        break;

    case "GetRegionEdit":
        require(ENGINE_DIR . "/Core_modules/TemplateAdmin.php");
        $tpl = new TemplateAdmin();
        $tpl->echo = FALSE;
        $PHP_SELF = $config['http_home_url']."admin.php?mod=car-market&action=";
        $result = $tpl->OTable(array(), 'style="margin-left:30px;width:95%" id="region_' . $id . '"');
        $auto->GetRegions($id);
        unset($auto->regions[0]);
        foreach ($auto->regions as $id=>$name)
        {
            $result .= $tpl->row(array('width="30px" align="center"' => "<div class=\"plus\"> </div>", $id, $name, "[<a href=\"{$PHP_SELF}cities&subaction=edit&type=region&id=$id\">{$auto->lang['edit']}</a>][<a href=\"{$PHP_SELF}cities&subaction=del&type=region&id=$id\">{$auto->lang['del']}</a>]"), false, false, "id=\"$id\"");
        }
        $result .= $tpl->CTable();
        header('Content-Type: text/html; charset="' . $config['charset'] . '"');
        echo $result;
        break;

    case "GetCityEdit":
        require(ENGINE_DIR . "/Core_modules/TemplateAdmin.php");
        $tpl = new TemplateAdmin();
        $tpl->echo = FALSE;
        $PHP_SELF = $config['http_home_url']."admin.php?mod=car-market&action=";
        $result = $tpl->OTable(array(), 'style="margin-left:30px;width:95%" id="city_' . $id . '"');
        if (!$auto->use_region)
        $auto->GetCities($id, 0);
        else
        $auto->GetCities(0, $id);
        unset($auto->cities[0]);
        foreach ($auto->cities as $id=>$name)
        {
            $result .= $tpl->row(array('width="30px" align="center"' => "", $id, $name, "[<a href=\"{$PHP_SELF}cities&subaction=edit&type=city&id=$id\">{$auto->lang['edit']}</a>][<a href=\"{$PHP_SELF}cities&subaction=del&type=city&id=$id\">{$auto->lang['del']}</a>]"), false, false, "id=\"$id\"");
        }
        $result .= $tpl->CTable();
        header('Content-Type: text/html; charset="' . $config['charset'] . '"');
        echo $result;
        break;

    case "GetModelEdit":
        require(ENGINE_DIR . "/Core_modules/TemplateAdmin.php");
        $tpl = new TemplateAdmin();
        $tpl->echo = FALSE;
        $PHP_SELF = $config['http_home_url']."admin.php?mod=car-market&action=models&subaction=";
        $result = $tpl->OTable(array(), 'style="margin-left:30px;width:95%" id="model_' . $id . '"');
        $auto->GetModels($id);
        foreach ($auto->models as $id=>$name)
        {
            $result .= $tpl->row(array('width="30px" align="center"' => "", $id, $name, "[<a href=\"{$PHP_SELF}edit&type=model&id=$id\">{$auto->lang['edit']}</a>][<a href=\"{$PHP_SELF}del&type=model&id=$id\">{$auto->lang['del']}</a>]"), false, false, "id=\"$id\"");
        }
        $result .= $tpl->CTable();
        header('Content-Type: text/html; charset="' . $config['charset'] . '"');
        echo $result;
        break;
        
    case "author":
        if (empty($_REQUEST['q']))
        {
            return '';
        }
        
        $name = addcslashes($base->EscapeString($_REQUEST['q']), "_%'");
        $base->SetWhere('author', $name, "LIKE", 'auto_autos');
        $base->Group(array('author'), 'auto_autos');
        $resourse = $base->Select("auto_autos", array("author"), array(), array("author"));
        
        while ($row = $base->FetchArray($resourse))
        {
            echo $row['author'] . "\n";
        }
        exit;
        break;

    case "photo_upload":
        if ($auto->config['photo_upload_type'] == 2)
        {
            $auto->member['id'] = empty($_REQUEST['user_id'])?0:$_REQUEST['user_id'];
            $auto->guest_session = empty($_REQUEST['guest_session'])?'':$_REQUEST['guest_session'];
        }
        
        require_once ENGINE_DIR . '/car-market/classes/ajax_upload.php';
        require_once ENGINE_DIR . '/car-market/classes/thumb.class.php';
        if ($image = $auto->UploadAJAXPhoto($_GET['photo_num'], $id, $_GET['model_id']))
        {
            echo "{status:'success', success:true, image_url:'{$image['image_url']}', image_th_url:'{$image['image_th_url']}', image_id:{$image['image_id']}}";
        }
        else 
        {
            $errors = '';
            foreach ($auto->Errors as $error)
            {
                if ($errors)
                {
                    $errors .= "; ";
                }
                
                $errors .= $error;
            }
            $errors .= "";
            
            echo "{error:'" . ($errors) . "', status:'error'}";
        }
        break;
        
    case "DelImage":
        $auto_id = (empty($_REQUEST['auto_id']))?0:$_REQUEST['auto_id'];
        header('Content-Type: text/html; charset="' . $config['charset'] . '"');
        echo $auto->DelPhoto($auto_id, $id);
        break;

    case "CheckLogin":
        require(ENGINE_DIR . "/car-market/ajax_registration.php");
        break;

    case "send_mail":
        $error = false; $data = array_map_recursive('urldecode', UrlParse($_REQUEST['data']));

        include_once DLE_CLASSES.'mail.class.php';
        $mail = new dle_mail ($config);

        if (!$is_logged)
        {
            if (!auto_check_email($data['from_email']))
            $error = true;

            if (!$data['from_name'])
            $error = true;

            $mail->from = $data['from_email'];
        }
        else
        $mail->from = $member_id['email'];

        if (!auto_check_email($data['user_email']) || strlen($data['text']) < 10 || strlen($data['subj']) < 5)
        $error = true;
        	
        if ($error)
        die("Error");
        	
        $mail->send ($data['user_email'], $data['subj'], $data['text']);
        if ($mail->send_error) die("Error");
        echo "ok";
        break;

    case "email_auto":
        $error = false; $data = array_map_recursive('urldecode', UrlParse($_REQUEST['data']));

        if (!$id)
        $error = true;
        else
        $email = $base->SelectOne('auto_autos', array("email"), array("id" => $id));
        	
        if (empty($email['email']))
        $error = true;

        include_once DLE_CLASSES.'mail.class.php';
        $mail = new dle_mail ($config);

        if (!$is_logged)
        {
            if (!auto_check_email($data['from_email']))
            $error = true;

            if (!$data['from_name'])
            $error = true;

            $mail->from = $data['from_email'];
        }
        else
        $mail->from = $member_id['email'];

        if (strlen($data['text']) < 10 || strlen($data['subj']) < 5)
        $error = true;
        	
        if ($error)
        die("Error");
        	
        $mail->send ($email['email'], $data['subj'], $data['text']);
        if ($mail->send_error) die("Error");
        echo "ok";
        break;

    case "allow_site":
        $allow = (intval($_REQUEST['allow']))?1:0;
        if (MODER && $id)
        {
            if ($auto_one = $base->SelectOne('auto_autos', array("mark_id", "model_id"), array("id" => $id)))
            {
                if ($allow)
                {
                    $base->Update('auto_marks', array("auto_num" => "auto_num+1"), array('id' => $auto_one['mark_id']), true);
                    $base->Update('auto_models', array("auto_num" => "auto_num+1"), array('id' => $auto_one['model_id']), true);
                }
                else
                {
                    $base->Update('auto_marks', array("auto_num" => "IF(auto_num=0, 0, auto_num-1)"), array('id' => $auto_one['mark_id']), true);
                    $base->Update('auto_models', array("auto_num" => "IF(auto_num=0, 0, auto_num-1)"), array('id' => $auto_one['model_id']), true);
                }
                $base->Update('auto_autos', array("allow_site" => $allow), array('id' => $id));
                Cache::ClearHTMLCache();
            }
        }
        break;

    default:
        echo 'Unknown Action';
        break;
}
exit;
?>