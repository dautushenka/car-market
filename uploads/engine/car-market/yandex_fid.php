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
    $config['http_home_url'] = explode("engine/car-market/yandex_fid.php", $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
    $config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
}

require_once DLE_CLASSES . 'mysql.php';
include_once ENGINE_DIR . '/data/dbconfig.php';
include_once ENGINE_DIR . '/modules/functions.php';
include_once ROOT_DIR . '/language/'.$config['langs'] . '/website.lng';
include_once DLE_CLASSES . 'templates.class.php';

define('LIC_DOMAIN', '.');

check_xss ();

$PHP_SELF = $config['http_home_url']."index.php";
$_TIME = time()+($config['date_adjust']*60);

$tpl = new dle_template ( );
$tpl->dir = ROOT_DIR . '/templates/' . $config['skin'];
define ( 'TEMPLATE_DIR', $tpl->dir );

require_once(ENGINE_DIR . "/car-market/includes.php");

if ($config['site_offline'] == "yes") die ("The site in offline mode");

$base->Connect(DBHOST, '', DBUSER, DBPASS, DBNAME, false, COLLATE);



$domain = str_replace("http://", '', $config['http_home_url']);
$domain = str_replace("/", '', $domain);


$auto->search_array['search_count_day'] = 60;

if (!$auto->config['count_yandex_export']) 
{
    $auto->config['count_yandex_export'] = 10;
}

$auto->Search(array("count" => $auto->config['count_yandex_export'], "get_count" => 0));

$gen_date = date("Y-m-d H:i:s \G\M\TO", $_TIME);

header('Content-type: application/xml');
echo <<<XML
<?xml version="1.0" encoding="windows-1251"?>
<auto-catalog>
<creation-date>$gen_date</creation-date>
<host>$domain</host>
XML;


if ($auto->autos)
{
    echo "<offers>";

    foreach ($auto->autos as $id=>$auto_one)
    {
        $array = $auto->ShowAuto($id, array("show_photo" => 0, "show_edit" => 0));
        $array['{add_date}'] = date("Y-m-d H:i:s \G\M\TO", $auto_one['add_date']);
        $array['{exp_date}'] = date("d.m.Y", $auto_one['exp_date']);
        $array['{description}'] = htmlspecialchars($array['{description}']);

        $XML = <<<XML
<url>{$array['{auto_url}']}</url>
<date>{$array['{add_date}']}</date>
<mark>{$array['{mark}']}</mark>
<seller-phone>{$array['{phone}']}</seller-phone>
<model>{$array['{model}']}</model>
<year>{$array['{year}']}</year>
<seller-city>{$array['{city}']}</seller-city>
<run-metric>тыс. км.</run-metric>
<run>{$array['{race}']}</run>
<additional-info>
        {$array['{description}']}
</additional-info>
<state>{$array['{state}']}</state>
<body-type>{$array['{basket}']}</body-type>
<engine-type>{$array['{type_motor}']}</engine-type>
<displacement>{$array['{capacity_motor}']}</displacement>
<transmission>{$array['{transmission}']}</transmission>
<seller>{$array['{contact_person}']}</seller>
XML;
// <steering-wheel>{$array['{count_door}']}</steering-wheel>
        foreach ($auto->checkbox_fields as $name=>$value)
        {
            if ($auto_one[$name])
            {
                $XML .= "<equipment>{$value}</equipment>";
            }
        }
        //print_r($auto_one);exit;
        foreach ($auto_one['photos'] as $photo)
        {
            if ($photo['image_name'] && file_exists(ROOT_DIR . "/uploads/auto_foto/" . $photo['model_id'] . "/" . $photo['image_name']))
            {
                $XML .= "<image>{$config['http_home_url']}uploads/auto_foto/{$photo['model_id']}/thumbs/{$photo['image_name']}</image>";
            }
        }

        if ($auto_one['cost'])
        {
            $XML .= <<<XML
<price>{$auto_one['cost']}</price>
<currency-type>{$auto->lang[$auto_one['currency']]}</currency-type>
XML;
        }
        else
        $XML .= <<<XML
<price>100</price>
<currency-type>руб</currency-type>
XML;

        if ($auto_one['auction'])
        $XML .= "<haggle>Возможен</haggle>";

        $XML = str_replace("&", "&amp;", $XML);
        $XML = str_replace('"', "&quot;", $XML);
        //$XML = str_replace(">", "&gt;", $XML);
        //$XML = str_replace(">", "&lt;", $XML);
        $XML = str_replace("'", "&apos;", $XML);

        echo '<offer type="private">';
        echo $XML;
        echo "</offer>";
    }
}

echo'</offers></auto-catalog>';

?>