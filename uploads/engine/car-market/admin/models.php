<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!in_array($auto->member['group'], $auto->config['admin_model']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$PHP_SELF .= "models";
$type = (empty($_REQUEST['type']))?'':$_REQUEST['type'];
$name = (empty($_REQUEST['name']))?'':$_REQUEST['name'];
$hidden_array['subaction'] = 'add';


switch ($subaction)
{
    case "add":
        if ($name)
        {
            if ($type == "mark")
            {
                $base->Insert('auto_marks', array("name" => $name));
                Cache::ClearArrayCache('marks');
                Cache::ClearArrayCache('marks_search');
            }
            elseif (intval($_REQUEST['mark_id']))
            $base->Insert('auto_models', array("name" => $name, "mark_id" => intval($_REQUEST['mark_id'])));
            else
            $tpl->msg($auto->lang['error'], $auto->lang['no_marks'], $PHP_SELF);

            $tpl->msg($auto->lang['add'], $auto->lang['add_desc'], $PHP_SELF);
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_name'], $PHP_SELF);
        break;

    case "edit":
        if ($id)
        {
            if ($type == "mark")
            $edit = $base->SelectOne('auto_marks', array("*"), array("id" => $id));
            else
            $edit = $base->SelectOne('auto_models', array("*"), array("id" => $id));

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
            if ($type == "mark")
            {
                $base->Update('auto_marks', array("name" => $name), array('id' => $id));
                Cache::ClearArrayCache('marks');
                Cache::ClearArrayCache('marks_search');
            }
            else
            {
                if (empty($_REQUEST['mark_id']))
                $tpl->msg($auto->lang['error'], $auto->lang['models_no_mark'], $PHP_SELF);
                $base->Update('auto_models', array("name" => $name, "mark_id" => $_REQUEST['mark_id']), array("id" => $id));
            }
            $tpl->msg($auto->lang['save'], $auto->lang['save_desc'], $PHP_SELF);
        }
        else
        $tpl->msg($auto->lang['error'], $auto->lang['no_name'], $PHP_SELF);
        break;

    case "del":
        if ($id)
        {
            if ($type == "mark")
            $count = $base->SelectOne('auto_autos', array('count' => "COUNT(*)"), array("mark_id" => $id));
            else
            $count = $base->SelectOne('auto_autos', array('count' => "COUNT(*)"), array("model_id" => $id));

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
            if ($type == "mark")
            {
                $base->Delete('auto_marks', array("id" => $id));
                $base->Delete('auto_models', array("mark_id" => $id));
                $base->Select('auto_autos', array("id"), array("mark_id" => $id));
                Cache::ClearArrayCache('marks');
                Cache::ClearArrayCache('marks_search');
            }
            else
            {
                $base->Delete('auto_models', array("id" => $id));
                $base->Select('auto_autos', array("id"), array("model_id" => $id));
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
	$("#marks tr:nth-child(even)").addClass("odd");
	$("#marks tbody tr").hover(function()
	{
		$(this).addClass("over");
	}, function()
	{
		$(this).removeClass("over");
	});
	$("#marks").find("td:not(:has(a))").click(function()
	{
		if ($(this).parent("tr").next().children("td").is("[colspan='4']"))
		{
			$(this).parent("tr").find("div").toggleClass("minus");
			$(this).parent("tr").next().toggle();
		}
		else
		{
			var id = $(this).parent("tr").attr("id");
			$(this).parent("tr").find("div").toggleClass("loader");
			$(this).parent("tr").after("<tr><td></td></tr>").next().hide();
			$(this).parent("tr").next().find("td").attr("colSpan", "4").load(dle_root + "engine/car-market/ajax.php", {'id':id, 'action':"GetModelEdit"}, function()
			{
				$(this).parent("tr").show();
				$("div.loader").toggleClass("loader");
				$("#model_" + id + " tr:nth-child(even)").addClass("odd");
				$("#model_" + id + " tr").hover(function()
				{
					$(this).addClass("over");
				}, function()
				{
					$(this).removeClass("over");
				});
			});
			$(this).parent("tr").find("div").toggleClass("minus");
		}
	});
	$("#marks tbody").find("td:not(:has(a))").css("cursor", "pointer");
});
</script>
JS;

$tpl->header($auto->lang['set_model'], true, $JScript);

if (($hidden_array['subaction'] == "save" && $type == "mark") || $hidden_array['subaction'] == "add")
{
    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['set_marka']);
    $tpl->OpenForm('', $hidden_array + array('type'=> 'mark'));
    $tpl->OTable();
    $tpl->echo = FALSE;

    echo $tpl->row(array('align="right"' => $tpl->inputText('name', $edit['name'], 'size="55"') . " " . $tpl->InputSubmit($auto->lang['btn_add'])), false);

    $tpl->echo = TRUE;
    $tpl->CTable();
    $tpl->CloseForm();
    $tpl->CloseSubtable();
    $tpl->CloseTable();
}

if (($hidden_array['subaction'] == "save" && $type == "model") || $hidden_array['subaction'] == "add")
{
    $auto->GetMarks();
    unset($auto->marks[0]);

    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['set_model']);
    $tpl->OpenForm('', $hidden_array + array('type'=> 'model'));
    $tpl->OTable();
    $tpl->echo = FALSE;

    echo $tpl->row(array('align="right"' => $tpl->selection($auto->marks, "mark_id", $edit['mark_id']) . " " . $tpl->InputText('name', $edit['name'], 'size="55"') . " " . $tpl->InputSubmit($auto->lang['btn_add'])), false);

    $tpl->echo = TRUE;
    $tpl->CTable();
    $tpl->CloseForm();
    $tpl->CloseSubtable();
    $tpl->CloseTable();
}

if (!$auto->marks)
{
    $auto->GetMarks();
    unset($auto->marks[0]);
};

$tpl->OpenTable();
$tpl->OpenSubtable('');
$tpl->OTable(array( "",
					"ID",
$auto->lang['name'],
					'width="20px;"' => $auto->lang['action']), "id=\"marks\"");
$tpl->echo = FALSE;

foreach ($auto->marks as $id=>$name)
{
    echo $tpl->row(array('style="width:30px;" align="center"' => "<div class=\"plus\" > </div>", $id, $name, "[<a href=\"{$PHP_SELF}&subaction=edit&type=mark&id=$id\">{$auto->lang['edit']}</a>][<a href=\"{$PHP_SELF}&subaction=del&type=mark&id=$id\">{$auto->lang['del']}</a>]"), false, false, "id=\"$id\"");
}

$tpl->echo = TRUE;
$tpl->CTable();
$tpl->CloseSubtable();
$tpl->CloseTable();
?>