<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!in_array($auto->member['group'], $auto->config['admin_fields']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$name = (empty($_REQUEST['name']))?'':$_REQUEST['name'];
$hidden_array['name'] = $name;
$hidden_array['subaction'] = "add";
$PHP_SELF .= "field&name=" . $name;

if (!$name)
$tpl->msg($auto->lang['error'], $auto->lang['no_select'], $PHP_SELF . "fields");

if (empty($auto->sel_fields[$name]))
$tpl->msg($auto->lang['error'], $auto->lang['no_values_for_fields'], $PHP_SELF . "fields");


function SaveFields()
{
    global $auto, $name;

    $find[] 	= "'\r'";
    $replace[] 	= "";
    $find[] 	= "'\n'";
    $replace[] 	= "";
    $handler = fopen(ENGINE_DIR . "/car-market/array/" . $name . "_array.php", "w");
    fwrite($handler, "<?PHP \n\n//{$auto->sel_fields[$name]['name']} array\n\n\${$name}_array = array(\n\n");
    foreach($auto->sel_fields[$name]['values'] as $id => $value)
    {
        $value = trim($value);
        $value = htmlspecialchars($value, ENT_QUOTES);
        $value = preg_replace($find, $replace, $value);

        fwrite($handler, "'{$id}' => \"{$value}\",\n\n");
    }
    fwrite($handler, ");\n\n?>");
    fclose($handler);
}

switch ($subaction)
{
    case "del":
        if (empty($id))
        $tpl->msg($auto->lang['error'], $auto->lang['field_add_no_desc'], $PHP_SELF);

        unset($auto->sel_fields[$name]['values'][$id]);
        SaveFields();
        $tpl->msg($auto->lang['field_del_ok'], $auto->lang['field_del_ok_desc'], $PHP_SELF);
        break;
        	
    case "edit":
        if (empty($id))
        $tpl->msg($auto->lang['error'], $auto->lang['field_add_no_desc'], $PHP_SELF);

        $hidden_array['subaction'] = "save";
        $hidden_array['id'] = $id;
        $name_f = $auto->sel_fields[$name]['values'][$id];
        $auto->lang['btn_add'] = $auto->lang['btn_save'];
        break;

    case "save":
        if (empty($_REQUEST['name_f']) || empty($id))
        $tpl->msg($auto->lang['error'], $auto->lang['field_add_no_desc'], $PHP_SELF);

        $auto->sel_fields[$name]['values'][$id] = $_REQUEST['name_f'];
        SaveFields();
        $tpl->msg($auto->lang['field_edit_ok'], $auto->lang['field_edit_ok_desc'], $PHP_SELF);
        break;

    case "add":
        if (empty($_REQUEST['name_f']))
        $tpl->msg($auto->lang['error'], $auto->lang['field_add_no_desc'], $PHP_SELF);

        $max=0;
        foreach($auto->sel_fields[$name]['values'] as $key=>$value)
        {
            if ( $key > $max ) $max = $key;
        }
        $max++;

        $auto->sel_fields[$name]['values'][$max] = $_REQUEST['name_f'];
        SaveFields();
        $tpl->msg($auto->lang['field_add_ok'], $auto->lang['field_add_ok_desc'], $PHP_SELF);
        break;

    case "sortdown":
        arsort($auto->sel_fields[$name]['values']);
        SaveFields();
        break;

    case "sortup":
        asort($auto->sel_fields[$name]['values']);
        SaveFields();
        break;
}

$JScript = <<<JS
<script type="text/javascript">
$(document).ready(function()
{
	$("#field tr:nth-child(even)").addClass('odd');
	$("#field tr").not(":first").hover(function()
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

$tpl->header($auto->sel_fields[$name]['name'], true, $JScript);
$tpl->OpenTable();
$tpl->OpenSubtable($auto->sel_fields[$name]['name']);
$tpl->OpenForm('', $hidden_array);
$tpl->OTable();
$tpl->echo = FALSE;

echo $tpl->row(array('align="right"' => $tpl->inputText("name_f", $name_f, 'size="55"') . " " . $tpl->InputSubmit($auto->lang['btn_add'])), false);

$tpl->echo = TRUE;
$tpl->CTable();
$tpl->CloseForm();
$tpl->CloseSubtable();
$tpl->CloseTable();


$tpl->OpenTable();
$tpl->OpenSubtable($auto->lang['values']);
$tpl->OTable(array("ID", $auto->lang['value'] . " (" . $auto->lang['sort'] . " <a href=\"$PHP_SELF&subaction=sortdown\" title=\"{$auto->lang['sortdown']}\" ><img border=0 src=\"/engine/car-market/images/admin/down.png\" /></a> <a href=\"$PHP_SELF&subaction=sortup\" title=\"{$auto->lang['sortdown']}\" ><img border=0 src=\"/engine/car-market/images/admin/up.png\" /></a>)", $auto->lang['action']), 'id="field"');
$tpl->echo = FALSE;

foreach ($auto->sel_fields[$name]['values'] as $id=>$value_f)
{
    echo $tpl->row(array($id, $value_f, "[<a href=\"{$PHP_SELF}&subaction=edit&id=$id\" >{$auto->lang['edit']}</a>][<a href=\"{$PHP_SELF}&subaction=del&id=$id\" >{$auto->lang['del']}</a>]"), false);
}

$tpl->echo = TRUE;
$tpl->CTable();
$tpl->CloseSubtable();
$tpl->CloseTable();

?>