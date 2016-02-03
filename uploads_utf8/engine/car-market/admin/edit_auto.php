<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!in_array($auto->member['group'], $auto->config['admin_edit']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$per_page = (intval(!empty($_REQUEST['per_page'])))?intval($_REQUEST['per_page']):50;;
$start = (intval(!empty($_REQUEST['start'])))?intval($_REQUEST['start']):0;
$where = (!empty($_REQUEST['where']))?$_REQUEST['where']:array(
															'country_id'     => 0,
															'region_id'      => 0,
															'city_id'        => 0,
															'mark_id'        => 0,
															'model_id'       => 0,
															'cost'           => 0,
															'currency'       => 0,
															'contact_person' => '',
															'add_date'       => '',
);
if (!empty($_REQUEST['page']))
{
    $start = ($_REQUEST['page']-1) * $per_page;
}
$subaction_array = array(
						"del" => $auto->lang['del'],
                        "change_allow_site_date" => $auto->lang['change_allow_site_date'],
                        "change_add_date" => $auto->lang['change_add_date'],
                        "set_allow_site_on" => $auto->lang['set_allow_site_on'],
                        "set_allow_site_off" => $auto->lang['set_allow_site_off'],
);
	
	
switch ($subaction)
{
    case 'search':
        $auto->Search($where, array('start'=>$start, 'limit' => $per_page));
        break;

    case "del":
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');
        	
        $text = $auto->lang['pre_del']; $tpl->echo = FALSE;
        foreach ($_POST['selected_auto'] as $id => $check)
        {
            if ($check)
            $text .= $tpl->InputHidden("selected_auto[$id]", 1);
        }
        $tpl->echo = TRUE;
        $hidden_array['referal'] = $_SERVER['HTTP_REFERER'];
        $tpl->msg_yes_no($auto->lang['del_ok'], str_replace('{count}', count($_POST['selected_auto']), $text), $hidden_array + array('subaction' => 'dodel'), $PHP_SELF . 'edit');
        break;

    case 'dodel':
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');
        	
        if ($auto->DelAuto(array_keys($_POST['selected_auto'])))
        $tpl->msg($auto->lang['del_ok'], $auto->lang['del_ok_desc'], (empty($_POST['referal']))?$PHP_SELF. "edit":$_POST['referal']);

        Cache::ClearAllCache();
        break;

    case "change_allow_site_date":
    case "change_add_date":
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');
         
        $hidden_array['subaction'] = 'do' . $subaction;
        $tpl->header($auto->lang[$subaction]);
        $tpl->OpenTable();
        $tpl->OpenSubtable($auto->lang[$subaction]);
        $tpl->OpenForm($PHP_SELF . 'edit', $hidden_array);

        foreach ($_POST['selected_auto'] as $id=>$value)
        {
            $tpl->InputHidden('selected_auto[]', $id);
        }

        echo $auto->lang['change_add_date_desc'] . "<br />";
        $tpl->InputRadio('type', 'current', "checked='checked'"); echo $auto->lang['change_add_date_type_current']; echo "<br/>";
        $tpl->InputRadio('type', 'change'); echo $auto->lang['change_add_date_type_change'];$tpl->InputText('date', '', 'id="date"'); $tpl->calendar('date'); echo "<br/>";
        $tpl->InputRadio('type', 'add'); echo $auto->lang['change_add_date_type_add']; $tpl->InputText('count_day', ''); echo "<br/>";
        $tpl->InputSubmit($auto->lang['btn_change']); echo "&nbsp;&nbsp;";
        $tpl->InputButton($auto->lang['btn_cancel'], "OnClick='window.history.back();return false;'");
        $tpl->CloseForm();
        $tpl->CloseSubtable();
        $tpl->CloseTable();
        return true;
        break;
         
    case "dochange_add_date":
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');

        switch ($_POST['type'])
        {
            case 'current':
                $base->SetWhere('id', $_POST['selected_auto'], "IN", 'auto_autos');
                $base->Update('auto_autos', array("add_date" => $base->timer->cur_time));
                break;
                 
            case "change":
                if (strtotime($_POST['date']) === -1 || strtotime($_POST['date']) > $base->timer->cur_time)
                {
                    $tpl->msg($auto->lan['error'], $auto->lang['wrong_date'], $PHP_SELF . "edit");
                }
                $base->SetWhere('id', $_POST['selected_auto'], "IN", 'auto_autos');
                $base->Update('auto_autos', array("add_date" => strtotime($_POST['date'])));
                break;
                 
            case "add":
                if (!(int)$_POST['count_day'])
                {
                    $tpl->msg($auto->lan['error'], $auto->lang['wrong_date'], $PHP_SELF . "edit");
                }
                $base->SetWhere('id', $_POST['selected_auto'], "IN", 'auto_autos');
                $base->Select('auto_autos', array('add_date', "id"));
                 
                while ($a = $base->FetchArray())
                {
                    if ($a['add_date'] + (int)$_POST['count_day'] * 3600 * 24 < $base->timer->cur_time)
                    {
                        $base->Update('auto_autos', array("add_date" => $a['add_date'] + (int)$_POST['count_day'] * 3600 * 24), array('id' => $a['id']));
                    }
                }
                break;
                 
            default:
                $tpl->msg($auto->lan['error'], $auto->lang['type_no_set'], $PHP_SELF . "edit");
                break;
        }
        Cache::ClearAllCache();
        $tpl->msg($auto->lan['change_add_date'], $auto->lang['change_date_ok'], $PHP_SELF . "edit");
        break;
         
    case "dochange_allow_site_date":
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');

        switch ($_POST['type'])
        {
            case 'current':
                $base->SetWhere('id', $_POST['selected_auto'], "IN", 'auto_autos');
                $base->Update('auto_autos', array("exp_date" => $base->timer->cur_time));
                break;

            case "change":
                if (strtotime($_POST['date']) === -1)
                {
                    $tpl->msg($auto->lan['error'], $auto->lang['wrong_date'], $PHP_SELF . "edit");
                }
                $base->SetWhere('id', $_POST['selected_auto'], "IN", 'auto_autos');
                $base->Update('auto_autos', array("exp_date" => strtotime($_POST['date'])));
                break;

            case "add":
                if (!(int)$_POST['count_day'])
                {
                    $tpl->msg($auto->lan['error'], $auto->lang['wrong_date'], $PHP_SELF . "edit");
                }
                $base->SetWhere('id', $_POST['selected_auto'], "IN", 'auto_autos');
                $base->Select('auto_autos', array('exp_date', "id"));

                while ($a = $base->FetchArray())
                {
                    $base->Update('auto_autos', array("exp_date" => $a['exp_date'] + (int)$_POST['count_day'] * 3600 * 24), array('id' => $a['id']));
                }
                break;

            default:
                $tpl->msg($auto->lan['error'], $auto->lang['type_no_set'], $PHP_SELF . "edit");
                break;
        }
        Cache::ClearAllCache();
        $tpl->msg($auto->lan['change_allow_site_date'], $auto->lang['change_date_ok'], $PHP_SELF . "edit");
        break;

    case "set_allow_site_on":
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');

        $base->SetWhere('id', array_keys($_POST['selected_auto']), "IN", 'auto_autos');
        $base->Update('auto_autos', array("allow_site" => 1));
        Cache::ClearAllCache();
        $tpl->msg($auto->lan['set_allow_site_on'], $auto->lang['set_allow_site_on_ok'], $PHP_SELF . "edit");
        break;
         
    case "set_allow_site_off":
        if (empty($_POST['selected_auto']))
        $tpl->msg($auto->lang['error'], $auto->lang['auto_no_select'], $PHP_SELF . 'edit');

        $base->SetWhere('id', array_keys($_POST['selected_auto']), "IN", 'auto_autos');
        $base->Update('auto_autos', array("allow_site" => 0));
        Cache::ClearAllCache();
        $tpl->msg($auto->lan['set_allow_site_off'], $auto->lang['set_allow_site_off_ok'], $PHP_SELF . "edit");
        break;

    default:
        $auto->Search(array(), array('start'=>$start, 'limit' => $per_page));
        break;
}

if ($auto->use_country)
{
    $auto->GetCountries(true);
}

if ($auto->use_region)
{
    $auto->GetRegions($where['country_id'], true);
}

$auto->GetCities($where['country_id'], $where['region_id'], true);
$auto->GetMarks(true);
$auto->GetModels($where['mark_id'], true);

$search_js = $auto->GetSearchJS();

$JScript = <<<JS
<script type="text/javascript">
$(document).ready(function()
{
    $search_js

	$("#autos tr:nth-child(even)").addClass('odd');
	$("#autos tr:has(input:checkbox)").not(":first").hover(function()
	{
		$(this).addClass('over');
	},
	function()
	{
		$(this).removeClass('over');
	});
	$("#autos tr:has(input:checkbox)").not(":first").click(function()
	{
		if ($(this).find("input:checked").length)
		{
			$(this).find("input:checkbox:checked").removeAttr("checked");
			$(this).toggleClass('checked');
		}
		else
		{
			$(this).find("input:checkbox").attr("checked", "checked");
			$(this).toggleClass('checked');
		}
		
	});
	$("#master").click(function()
	{
		if ($(this).is(":checked"))
		{
			$("input:checkbox").not("checked").attr("checked", "checked");
			$("#autos tr:has(input:checkbox)").not(":first").addClass('checked');
		}
		else
		{
			$("#autos tr:has(input:checkbox)").not(":first").removeClass('checked');
			$("input:checkbox").not("checked").removeAttr("checked");
		}
	});
	
	$('#author').autocomplete(ajax_url + '?action=author',
    {
       cacheLength: 10,
       selectFirst: true,
       selectOnly: true
    });
});
</script>
JS;

$tpl->header($auto->lang['edit_auto'], true, $JScript, array('engine/car-market/css/jquery.autocomplete.css',), array(
                                                                                                                                        'engine/car-market/javascript/jquery.autocomplete.pack.js',
                                                                                                                                        ));
$tpl->OpenTable();
$tpl->OpenSubtable($auto->lang['filter']);
$tpl->OpenForm($PHP_SELF . 'edit', $hidden_array + array('subaction'=>'search'));

$tpl->echo = FALSE;

$table1 = $tpl->OTable(array(), 'id="filter"');
if ($auto->use_country)
$table1 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['country'],
								 'class="filter_value"' => $tpl->selection($auto->countries, 'where[country_id]', $where['country_id'], 'id="country_id_search"')), false);
if ($auto->use_region)
$table1 .= $tpl->row(array( 'class="filter_name"'  => $auto->lang['region'],
					 			'class="filter_value"' => $tpl->selection($auto->regions, 'where[region_id]', $where['region_id'], 'id="region_id_search"')), false);

$table1 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['city'],
							 'class="filter_value"' => $tpl->selection($auto->cities, 'where[city_id]', $where['city_id'], 'id="city_id_search"')), false);

$table1 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['auto_contact_person'],
							 'class="filter_value"' => $tpl->InputText('where[contact_person]', $where['contact_person'])), false);

$table1 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['filter_date_after'],
							 'class="filter_value"' => $tpl->InputText('where[add_date]', $where['add_date'], 'id="add_date"') . $tpl->calendar('add_date')), false);
$table1 .= $tpl->CTable();

$table2  = $tpl->OTable(array(), 'id="filter"');
$table2 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['marka'],
							 'class="filter_value"' => $tpl->selection($auto->marks, 'where[mark_id]', $where['mark_id'], 'id="mark_id_search"')), false);
$table2 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['model'],
							 'class="filter_value"' => $tpl->selection($auto->models, 'where[model_id]', $where['model_id'], 'id="model_id_search"')), false);
$table2 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['cost'],
							 'class="filter_value"' => $tpl->inputText('where[cost_min]', $where['cost_min'], 'size="10"') . " - " . $tpl->InputText('where[cost_max]', $where['cost_max'], 'size="10"') . " " . $tpl->selection($auto->currency_array, 'where[currecy]', $where['currecy'])), false);
$table2 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['author'],
							 'class="filter_value"' => $tpl->inputText('where[author]', $where['author'], "id='author'")), false);
$table2 .= $tpl->row(array(  'class="filter_name"'  => $auto->lang['count_page'],
							 'class="filter_value"' => $tpl->inputText('per_page', $per_page, 'size="8"')), false);

$table2 .= $tpl->CTable();

$tpl->echo = TRUE;

$tpl->OTable();
$tpl->row(array('width="50%"' => $table1, $table2), false);
$tpl->CTable();

$tpl->CloseSubtable($auto->lang['btn_show']);
$tpl->CloseForm();
$tpl->CloseTable();








$tpl->OpenTable();
$tpl->OpenSubtable($auto->lang['edit_auto']);
$tpl->OpenForm('', $hidden_array, 'name="form_auto"');

$tpl->echo = FALSE;

echo $tpl->OTable(array("ID",
$auto->lang['model'],
$auto->lang['city'],
$auto->lang['cost'],
$auto->lang['auto_contact_person'],
$auto->lang['exp_date'],
$auto->lang['block_date'],
$auto->lang['add_date'],
$auto->lang['author'],
$tpl->InputCheckbox("master", 1, 0, 'id="master"')), 'id="autos" class="tablesorter"');
if ($auto->autos)
{
    $color = '';$i = $start;
    foreach ($auto->autos as $auto_one)
    {
        if (!$auto_one['allow_site'])
        $color = ' style="color:red"';
        else
        $color = '';
        	
        echo $tpl->row(array("<a$color href=\"{$PHP_SELF}auto&subaction=edit&id={$auto_one['id']}\"$color title=\"{$auto->lang['auto_edit']}\" >{$auto_one['id']} </a>",
							"<a$color href=\"{$PHP_SELF}auto&subaction=edit&id={$auto_one['id']}\"$color title=\"{$auto->lang['auto_edit']}\" >{$auto_one['mark_name']} {$auto_one['model_name']} </a>",
        $auto_one['city_name'],
        auto_num_format($auto_one['cost']) . " " . $auto->lang[$auto_one['currency']],
        $auto_one['contact_person'],
        ($auto_one['exp_date'])?date("d-m-Y H:i", $auto_one['exp_date']):'&#8212;',
        ($auto_one['block_date'])?date("d-m-Y H:i", $auto_one['block_date']):'&#8212;',
        date("d-m-Y H:i", $auto_one['add_date']),
        $auto_one['author'],
        $tpl->InputCheckbox("selected_auto[{$auto_one[id]}]", 1, 0, 'style="display:none"')
        ), false);
        $i++;
    }

    $where_url = array();
    foreach ($where as $field=>$value)
    {
        $where_url["where[$field]"] = $value;
    }

    $nav = $tpl->navigation($_REQUEST['page'], $per_page, $auto->autos_count, $tpl->url($where_url, $PHP_SELF."edit&"));
    $table  = $tpl->OTable();
    $table .= $tpl->row(array('align="left"' => $nav, 'align="right"' => $tpl->selection($subaction_array, 'subaction') . " " . $tpl->InputSubmit($auto->lang['btn_submit'])), false);
    $table .= $tpl->CTable();

    echo $tpl->row($table, false, true);

}
else
echo $tpl->row($auto->lang['no_auto'], true, true);

$tpl->echo = TRUE;
$tpl->CTable();
$tpl->CloseSubtable();
$tpl->CloseForm();
$tpl->CloseTable();

?>