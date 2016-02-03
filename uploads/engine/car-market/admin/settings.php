<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}/*
$regions = array();
foreach ($auto->GetCountries() as $id => $name)
{
    if (!$id)
    {
        continue;
    }
    $regions[$id] = array();
    foreach ($auto->GetRegions($id) as $rid => $name)
    {
        if (!$rid)
        {
            continue;
        }
        
        foreach ($auto->GetCities($id, $rid) as $cid => $name)
        {
            if (!$cid)
            {
                continue;
            }
            
            $regions[$id][$rid][$cid] = array();
            $regions[$id][$rid][$cid]['id'] = $cid;
            $regions[$id][$rid][$cid]['region_id'] = $rid;
            $regions[$id][$rid][$cid]['country_id'] = $id;
            $regions[$id][$rid][$cid]['name'] = $name;
        }
    }
}
Func::SaveConfig(ROOT_DIR . "/regions.php", 'regions', $regions);die('ok');*/
if ($auto->member['group'] != 1 && !in_array($auto->member['group'], $auto->config['admin_settings']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$PHP_SELF .= "&action=settings";
$save_con = (empty($_REQUEST['save_con']))?array():$_REQUEST['save_con'];

if ($subaction == "save")
$auto->config = array_merge($auto->config, $save_con);


$tpl->echo = FALSE;

require ENGINE_DIR . "/car-market/admin/settings_array.php";

$tpl->echo = TRUE;

if ($subaction == "save")
{
    $errors = array();
    foreach ($settings_array as $settings)
    {
        foreach ($settings as $setting)
        {
            if ($setting['regexp'])
            {
                if (is_array($save_con[$setting['name']]))
                {
                    foreach ($save_con[$setting['name']] as $value)
                    {
                        if (!preg_match($setting['regexp'], $value))
                        $errors[] = '"' . $setting['title'] . "\" -- " . $auto->lang['setting_error'];
                    }
                }
                elseif (!preg_match($setting['regexp'], $save_con[$setting['name']]))
                $errors[] = '"' . $setting['title'] . "\" -- " . $auto->lang['setting_error'];
            }
        }
    }

    if (!$errors)
    {
        if ($auto->member['group'] != 1)
        {
            $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
        }
        
        $save_con['version_id'] = $auto->config['version_id'];
        $save_con['currency']['USD'] = 1;
        $save_con['use_country'] = $auto->config['use_country'];
        $save_con['use_region'] = $auto->config['use_region'];

        Func::SaveConfig(ENGINE_DIR.'/data/car-market_conf.php', 'car_conf', $save_con);
         
        Cache::ClearAllCache();
        $tpl->msg("info", $lang['opt_sysok'], $PHP_SELF);
    }
}


$JScript = <<<JS
<script type="text/javascript">
$(document).ready(function()
{
	$("#setting").find("#general").show();
	$("#submenu").find("#general").css("border", "1px solid");
	$("#setting input:text").css("text-align", "center");
	$("#submenu").find("a").click(function()
	{
		$("#submenu").find("a").css("border", "");
		$(this).css("border", "1px solid");
		$("#setting tr[id]").hide();
		id = $(this).attr("id");
		$("#setting").find("#"+id).show();
		return false;
	});
	$("table").find("#subtable tr").hover(function()
	{
		$(this).addClass('over');
	}, 
	function()
	{
		$(this).removeClass('over');
	});
});
</script>
JS;


$tpl->header($auto->lang['settings'], true, $JScript);

$tpl->submenu(array(
$auto->lang['block1_title'] => array('settings', 'block1.png', 'id="block1"'),
$auto->lang['block2_title'] => array('settings', 'block2.png', 'id="block2"'),
$auto->lang['block3_title'] => array('settings', 'block3.png', 'id="block3"'),
$auto->lang['user_title']   => array('settings', 'user.png', 'id="user"'),
$auto->lang['photo_title']  => array('settings', 'photo.png', 'id="photo"'),
$auto->lang['general_title']=> array('settings', 'setting.png', 'id="general"')
),
$PHP_SELF, "/engine/car-market/images/admin/submenu");

if ($errors)
{
    $tpl->OpenTable();
    echo "  <font color=\"red\" >" . $auto->lang['isset_error'] . "</font><ol>";
    foreach ($errors as $error)
    {
        echo "<li>" . $error . "</li>";
    }
    echo "</ol>";
    $tpl->CloseTable();
}

$tpl->OpenTable();
$tpl->OpenForm('', $hidden_array + array('subaction' => 'save'));
echo "<table width=100% id='setting'>";
foreach ($settings_array as $table=>$settings)
{
    echo "<tr id=\"$table\" style='display:none'><td>";
    $tpl->OpenSubtable($auto->lang[$table . '_title']);
    $tpl->OTable(array(), 'id="subtable"');
    foreach ($settings as $setting)
    {
        $tpl->SettingRow($setting['title'], $setting['descr'], $setting['setting']);
    }
    $tpl->CTable();
    $tpl->CloseSubtable();
    echo "</td></tr>";
}
echo "</table>";
$tpl->CloseTable($auto->lang['btn_save']);
$tpl->CloseForm();

?>