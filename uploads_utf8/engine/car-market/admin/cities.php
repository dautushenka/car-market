<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!in_array($auto->member['group'], $auto->config['admin_city']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$PHP_SELF .= "cities";
$type = (empty($_REQUEST['type']))?'':$_REQUEST['type'];
$name = (empty($_REQUEST['name']))?'':$_REQUEST['name'];
$hidden_array['subaction'] = 'add';

switch ($subaction)
{
    case "add":
        if ($name)
        {
            switch ($type)
            {
                case "country":
                    $base->Insert('auto_countries', array('name' => $name));
                    Cache::ClearArrayCache('countries');
                    Cache::ClearArrayCache('countries_search');
                    break;
                    	
                case "region":
                    if ($auto->use_country && empty($_REQUEST['country_id']))
                    $tpl->msg($auto->lang['error'], $auto->lang['cities_no_country'], $PHP_SELF);

                    $base->Insert('auto_regions', array('name' => $name, 'country_id' => $_REQUEST['country_id']));
                    Cache::ClearArrayCache('regions_' . $_REQUEST['country_id']);
                    Cache::ClearArrayCache('regions_search_' . $_REQUEST['country_id']);
                    break;
                    	
                case "city":
                    if ($auto->use_country && empty($_REQUEST['country_id']))
                    $tpl->msg($auto->lang['error'], $auto->lang['cities_no_country'], $PHP_SELF);
                    if ($auto->use_region && empty($_REQUEST['region_id']))
                    $tpl->msg($auto->lang['error'], $auto->lang['cities_no_region'], $PHP_SELF);

                    $base->Insert('auto_cities', array('name' => $name, "country_id" => $_REQUEST['country_id'], 'region_id' => $_REQUEST['region_id']));
                    break;
                    	
                default:
                    break;
            }
            $tpl->msg($auto->lang['add'], $auto->lang['add_desc'], $PHP_SELF);
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_name'], $PHP_SELF);
        break;

    case "edit":
        if ($id)
        {
            switch ($type)
            {
                case "country":
                    $edit = $base->SelectOne('auto_countries', array("*"), array("id" => $id));
                    break;
                    	
                case "region":
                    $edit = $base->SelectOne('auto_regions', array("*"), array("id" => $id));
                    break;
                    	
                case "city":
                    $edit = $base->SelectOne('auto_cities', array("*"), array("id" => $id));
                    break;
                    	
                default:
                    break;
            }
            $hidden_array['subaction'] = 'save';
            $hidden_array['id'] = $id;
            $auto->lang['btn_add'] = $auto->lang['btn_save'];
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_select'], $PHP_SELF);
        break;

    case "save":
        if ($name && $id)
        {
            switch ($type)
            {
                case "country":
                    $base->Update('auto_countries', array('name' => $name), array("id" => $id));
                    Cache::ClearArrayCache('countries');
                    Cache::ClearArrayCache('countries_search');
                    break;
                    	
                case "region":
                    if ($auto->use_country && empty($_REQUEST['country_id']))
                    $tpl->msg($auto->lang['error'], $auto->lang['cities_no_country'], $PHP_SELF);

                    $base->Update('auto_regions', array('name' => $name, 'country_id' => $_REQUEST['country_id']), array("id" => $id));
                    Cache::ClearArrayCache('regions_' . $_REQUEST['country_id']);
                    Cache::ClearArrayCache('regions_search_' . $_REQUEST['country_id']);
                    break;
                    	
                case "city":
                    if ($auto->use_country && empty($_REQUEST['country_id']))
                    $tpl->msg($auto->lang['error'], $auto->lang['cities_no_country'], $PHP_SELF);
                    if ($auto->use_region && empty($_REQUEST['region_id']))
                    $tpl->msg($auto->lang['error'], $auto->lang['cities_no_region'], $PHP_SELF);

                    $base->Update('auto_cities', array('name' => $name, "country_id" => $_REQUEST['country_id'], 'region_id' => $_REQUEST['region_id']), array("id" => $id));
                    break;
                    	
                default:
                    break;
            }
            $tpl->msg($auto->lang['save'], $auto->lang['save_desc'], $PHP_SELF);
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_name'], $PHP_SELF);
        break;

    case "del":
        if ($id)
        {
            switch ($type)
            {
                case "country":
                    $count = $base->SelectOne('auto_autos', array('count' => "COUNT(*)"), array("country_id" => $id));
                    break;
                    	
                case "region":
                    $count = $base->SelectOne('auto_autos', array('count' => "COUNT(*)"), array("region_id" => $id));
                    break;
                    	
                case "city":
                    $count = $base->SelectOne('auto_autos', array('count' => "COUNT(*)"), array("city_id" => $id));
                    break;
                    	
                default:
                    break;
            }
            $hidden_array['type'] = $type;
            $hidden_array['subaction'] = 'dodel';
            $hidden_array['id'] = $id;
            $tpl->msg_yes_no($auto->lang['del'],  str_replace('{count}', $count['count'], $auto->lang['del_desc']), $hidden_array, $PHP_SELF);
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_select'], $PHP_SELF);
        break;

    case "dodel":
        if ($id)
        {
            switch ($type)
            {
                case "country":
                    $base->Delete('auto_countries', array("id" => $id));
                    if ($auto->use_region)
                    $base->Delete('auto_regions', array("country_id" => $id));
                    $base->Delete('auto_cities', array("country_id" => $id));
                    $base->Select('auto_autos', array("id"), array("country_id" => $id));
                    Cache::ClearArrayCache('countries');
                    Cache::ClearArrayCache('countries_search');
                    break;
                    	
                case "region":
                    $base->Delete('auto_regions', array("id" => $id));
                    $base->Delete('auto_cities', array("region_id" => $id));
                    $base->Select('auto_autos', array("id"), array("region_id" => $id));
                    Cache::ClearArrayCache();
                    break;
                    	
                case "city":
                    $base->Delete('auto_cities', array("id" => $id));
                    $base->Select('auto_autos', array("id"), array("city_id" => $id));
                    break;
                    	
                default:
                    break;
            }
            $auto_id = array();
            while ($row = $base->FetchArray())
            {
                $auto_id[] = $row['id'];
            }
            $auto->DelAuto($auto_id);
            $tpl->msg($auto->lang['del'],  $auto->lang['del_desc_ok'], $PHP_SELF);
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_select'], $PHP_SELF);
        break;

    default:
        break;
}

$JScript = <<<JS
<script type="text/javascript">
$(document).ready(function()
{
	$("#country tr:nth-child(even), #region tr:nth-child(even), #city tr:nth-child(even)").addClass("odd");
	$("#country tbody tr, #region tbody tr, #city tbody tr").hover(function()
	{
		$(this).addClass("over");
	}, function()
	{
		$(this).removeClass("over");
	});
});
</script>
JS;

if ($auto->use_country)
$add_script[] = '/engine/car-market/javascript/edit_city_use_country.js';
elseif ($auto->use_region)
$add_script[] = '/engine/car-market/javascript/edit_city_use_region.js';

$tpl->header($auto->lang['set_country'], true, $JScript, array(), $add_script);

if ($auto->use_country && (($hidden_array['subaction'] == "save" && $type == "country") || $hidden_array['subaction'] == "add"))
{
    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['set_country']);
    $tpl->OTable();
    $tpl->OpenForm('', $hidden_array + array("type" => 'country'), 'id="edit_country"');
    $tpl->echo = FALSE;

    echo $tpl->row(array('align="right"' => $tpl->inputText('name', $edit['name'], 'size="55"') . " " . $tpl->InputSubmit($auto->lang['btn_add'])), false);

    $tpl->echo = TRUE;
    $tpl->CTable();
    $tpl->CloseSubtable();
    $tpl->CloseForm();
    $tpl->CloseTable();
}

if ($auto->use_region && (($hidden_array['subaction'] == "save" && $type == "region") || $hidden_array['subaction'] == "add"))
{
    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['set_region']);
    $tpl->OTable();
    $tpl->OpenForm('', $hidden_array + array("type" => 'region'), 'id="edit_region" name="edit_region"');
    $tpl->echo = FALSE;

    if ($auto->use_country)
    {
        $auto->GetCountries();
        $selec_country = $tpl->selection($auto->countries, 'country_id', $edit['country_id']) . " ";
    }
    else
    $selec_country = '';

    echo $tpl->row(array('align="right"' => $selec_country . $tpl->inputText('name', $edit['name'], 'size="55"') . " " . $tpl->InputSubmit($auto->lang['btn_add'])), false);

    $tpl->echo = TRUE;
    $tpl->CTable();
    $tpl->CloseSubtable();
    $tpl->CloseForm();
    $tpl->CloseTable();
}

if (($hidden_array['subaction'] == "save" && $type == "city") || $hidden_array['subaction'] == "add")
{

    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['set_city']);
    $tpl->OTable();
    $tpl->OpenForm('', $hidden_array + array("type" => 'city'), 'id="edit_city"');
    $tpl->echo = FALSE;

    if ($auto->use_country)
    {
        if (!$auto->countries)
        $auto->GetCountries();
        $select_country = $tpl->selection($auto->countries, 'country_id', $edit['country_id'], ($auto->use_region)?'id="country_id"':'') . " ";
    }
    else
    $select_country = '';

    if ($auto->use_region)
    {
        $auto->GetRegions($edit['country_id']);
        $select_region = $tpl->selection($auto->regions, 'region_id', $edit['region_id'], 'id="region_id"') . " ";
    }
    else
    $select_region = '';
    	
    echo $tpl->row(array('align="right"' => $select_country . $select_region . $tpl->inputText('name', $edit['name'], 'size="55" validate="required:true"') . " " . $tpl->InputSubmit($auto->lang['btn_add'])), false);

    $tpl->echo = TRUE;
    $tpl->CTable();
    $tpl->CloseSubtable();
    $tpl->CloseForm();
    $tpl->CloseTable();
}


$type = 'city';
if ($auto->use_country)
{
    $base->Select('auto_countries', array("*"), array(), array("name"=>'ASC'));
    $type = 'country';
}
elseif ($auto->use_region)
{
    $base->Select('auto_regions', array("*"), array(), array("name"=>'ASC'));
    $type = 'region';
}
else
$base->Select('auto_cities', array("*"), array(), array("name"=>'ASC'));

while ($row = $base->FetchArray())
{
    $values[$row['id']] = $row['name'];
}

$tpl->OpenTable();
$tpl->OpenSubtable('');
$tpl->OTable(array( "",
					"ID",
$auto->lang['name'],
					'width="20px;"' => $auto->lang['action']), "id=\"$type\"");
	
foreach ($values as $id=>$name)
{
    if ($type == "country" || $type == "region")
    $img = "<div class=\"plus\" > </div>";
    else
    $img = '';
    $tpl->row(array('style="width:30px;" align="center"' => $img, $id, $name, "[<a href=\"{$PHP_SELF}&subaction=edit&type={$type}&id=$id\">{$auto->lang['edit']}</a>][<a href=\"{$PHP_SELF}&subaction=del&type={$type}&id=$id\">{$auto->lang['del']}</a>]"), false, false, "id=\"$id\"");
}

$tpl->CTable();
$tpl->CloseSubtable();
$tpl->CloseTable();

?>