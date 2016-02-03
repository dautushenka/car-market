<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!in_array($auto->member['group'], $auto->config['admin_fields']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$JScript = <<<JS
<script type="text/javascript">
$(document).ready(function()
{
	$("#fields tr:nth-child(even)").addClass('odd');
	$("#fields tr").not(":first").hover(function()
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

$tpl->header($auto->lang['other_field'], true, $JScript);

$tpl->OpenTable();
$tpl->OpenSubtable($auto->lang['other_field']);
$tpl->OTable(array(), 'id="fields"');

$tpl->SettingRow("<font style=\"font-size:14px;color:green;\" >" . $auto->lang['field_sel'] . "</font>", "", "<font style=\"font-size:14px;color:green;\" ><b>" . $auto->lang['templ'] . "</b></font>");

foreach ($auto->sel_fields as $name=>$field)
{
    $tpl->SettingRow("<a href=\"{$PHP_SELF}field&name=$name\" title=\"{$auto->lang['edit']}\" >{$field['name']}</a>", $auto->lang['field_desc'], "{" . $name . "}");
}

$tpl->SettingRow("<font style=\"font-size:14px;color:green;\" >".$auto->lang['checkboxes']."</font>", "", "");

foreach ($auto->checkbox_fields as $key=>$check)
{
    $tpl->SettingRow($check, "", "{".$key."}");
}
$tpl->CTable();
$tpl->CloseSubtable();
$tpl->CloseTable();

?>