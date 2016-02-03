<?php

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR',dirname (__FILE__) . "/../");

error_reporting(7);
ini_set('display_errors', true);
ini_set('html_errors', false);

include ENGINE_DIR.'/data/config.php';
define('DLE_CLASSES' , ENGINE_DIR . (($config['version_id'] > 6.3)?'/classes/':'/inc/'));

if ($config['http_home_url'] == "")
{
    $config['http_home_url'] = explode("engine/car-market/avtopoisk_fid.php", $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
    $config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
}

require_once DLE_CLASSES . 'mysql.php';
include_once ENGINE_DIR . '/data/dbconfig.php';
include_once ENGINE_DIR . '/modules/functions.php';
include_once ROOT_DIR . '/language/'.$config['langs'] . '/website.lng';

define('LIC_DOMAIN', '.');

check_xss ();

$PHP_SELF = $config['http_home_url']."index.php";
$_TIME = time()+($config['date_adjust']*60);

require_once(ENGINE_DIR . "/car-market/includes.php");

if ($config['site_offline'] == "yes") die ("The site in offline mode");

$base->Connect(DBHOST, '', DBUSER, DBPASS, DBNAME, false, COLLATE);

$domain = str_replace("http://", '', $config['http_home_url']);
$domain = str_replace("/", '', $domain);


if (!empty($_REQUEST['today']))
{
    $auto->search_array['search_count_day'] = 1;
}
else
{
    $auto->search_array['search_count_day'] = 60;
}

if (!$auto->config['count_yandex_export']) 
{
    $auto->config['count_yandex_export'] = 150;
}

$auto->Search(array("count" => $auto->config['count_yandex_export'], "get_count" => 0));


if ($auto->config['general_mod_rewrite']) 
{
    $urlprefix = $template->main_alt_url . "/auto";
}
else
{
    if (strpos($template->main_url, "?") !== false)
    {
        $urlprefix = $template->main_url . "&action=auto&id=";
    }
    else
    {
        $urlprefix = $template->main_url . "?action=auto&id=";
    }
}
$imageprefix = $config['http_home_url'] . "uploads/auto_foto/";
$urlprefix = str_replace("&", "&amp;", $urlprefix);
$currecncy_array = array(
                        'USD' => 1,
                        'EUR' => 2,
                        'UAH' => 3,
                        'RUR' => 3,
);

function format_phone($phone){
    $phone = eregi_replace("([^0-9]+)", "", $phone);
    $phone = eregi_replace("^38044", "", $phone);
    $phone = eregi_replace("^8044", "", $phone);
    $phone = eregi_replace("^044", "", $phone);
    $phone = eregi_replace("^380", "", $phone);
    $phone = eregi_replace("^80", "", $phone);
    $phone = eregi_replace("^0", "", $phone);
    return $phone;
}


header('Content-type: application/xml');
echo <<<XML
<?xml version="1.0" encoding="windows-1251"?>
<chanel>
<urlprefix>$urlprefix</urlprefix>
<imgprefix>$imageprefix</imgprefix>
<host>$domain</host>
XML;


if ($auto->autos)
{
    echo "<cars>\n";

    foreach ($auto->autos as $id=>$auto_one)
    {
        $array = $auto->ShowAuto($id, array("show_photo" => 0, "show_edit" => 0));
        $region = ($auto->use_region)?$array['{region}']:$array['{city}'];
        $array['{race}'] = $array['{race}'] * 1000;
        $array['{phone}'] = md5(format_phone($array['{phone}']));
        $array['{email}'] = md5($auto_one['email']);
        
        if (empty($auto_one['photos'][0]['image_name']))
        {
            $img = '';
        }
        else
        {
            $img = "\n<n>" . $auto_one['photos'][0]['model_id'] . "/thumbs/" . $auto_one['photos'][0]['image_name'] . "</n>";
        }
        
        if ($auto->config['general_mod_rewrite']) 
        {
            $auto_url = $id . ".html";
        }
        else
        {
            $auto_url = $id;
        }
        
        $XML = <<<XML
<i>$auto_url</i>
<id>$id</id>
<k>{$auto_one['add_date']}</k>
<a>{$array['{mark}']}</a>
<m>$region</m>
<t>1</t>
<p1>{$array['{phone}']}</p1>
<b>{$array['{model}']}</b>
<d>{$array['{year}']}</d>
<w>{$array['{email}']}</w>
<g>{$array['{race}']}</g>$img
<y>{$array['{basket}']}</y>
<f>{$array['{type_motor}']}</f>
<f1>{$array['{capacity_motor}']}</f1>
<f2>{$array['{fuel}']}</f2>
<z>{$array['{transmission}']}</z>
<q>{$array['{contact_person}']}</q>
XML;
// <steering-wheel>{$array['{count_door}']}</steering-wheel>

/*        if ($auto_one['cost'])
        {
            $XML .= <<<XML
<price>{$auto_one['cost']}</price>
<currency-type>{$auto->lang[$auto_one['currency']]}</currency-type>
XML;
        }
        else*/
        $XML .= <<<XML
<c>{$auto_one['cost']}</c>
<v>{$currecncy_array[$auto_one['currency']]}</v>
XML;

        /*if ($auto_one['auction'])
        $XML .= "<haggle>Возможен</haggle>";*/

        $XML = str_replace("&", "&amp;", $XML);
        $XML = str_replace('"', "&quot;", $XML);
        //$XML = str_replace(">", "&gt;", $XML);
        //$XML = str_replace(">", "&lt;", $XML);
        $XML = str_replace("'", "&apos;", $XML);

        echo "<car>\n";
        echo $XML;
        echo "\n</car>\n";
    }
    
    echo "</cars>\n";
}

echo'</chanel>';

?>