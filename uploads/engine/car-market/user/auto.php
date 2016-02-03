<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!$id)
{
    $template->msg($auto->lang['error'], $auto->lang['auto_not_found'], true);
    return ;
}

$auto->AddView($id);

$auto->search_array = array();
$auto->Search(array("get_count" => 0), array($id));

if (empty($auto->autos[$id]))
{
    $template->msg($auto->lang['error'], $auto->lang['auto_not_found'], true);
    return ;
}

$template->load("auto_full");

$JScript = <<<JS
<script type="text/javascript">
$(document).ready(function()
{
	$("a.go_big_photo").click(function()
	{
		var a_now = $(this);
		$("#big_photo").fadeOut("slow", function()
		{
			$(this).find("img").attr("src", dle_root + "engine/car-market/images/loader.white.gif");
			var objImagePreloader = new Image();
			objImagePreloader.onload = function()
			{
				$('#big_photo img').attr('src', objImagePreloader.src);
				$("#big_photo").fadeIn("slow");
				objImagePreloader.onload=function(){};
			};
			objImagePreloader.src = a_now.attr("href");
		});
		return false;
	});
	$("a.lightbox").lightBox(
	{
		imageLoading: '/engine/car-market/images/admin/lightbox-ico-loading.gif',
		imageBtnPrev: '/engine/car-market/images/admin/lightbox-btn-prev.gif',
		imageBtnNext: '/engine/car-market/images/admin/lightbox-btn-next.gif',
		imageBtnClose: '/engine/car-market/images/admin/lightbox-btn-close.gif',
		imageBlank: '/engine/car-market/images/admin/lightbox-blank.gif',
		txtImage : '{$auto->lang['txtImage']}',
		txtOf : '{$auto->lang['txtOf']}'
	});
});
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.lightbox-0.5.js');
</script>
JS;

$template->SetStyleScript(  array('/engine/car-market/css/jquery.lightbox-0.5.css'));

$array = $auto->ShowAuto($id);
$template->Set($array)->Compile('content', $JScript);

$template->load('email_auto')
->SetForm(array(), '', 'POST', 'id="auto_send"', true)
->WrapContent('<div style="display:none; cursor: default;" id="email_auto" >', '</div>')
->Set($config['http_home_url'], '{site_link}')
->Set($template->GetUrl(array("action" => 'auto',  "id" => $id)), "{auto_link}")
->Set($array['{contact_person}'], "{contact_person}");
	
if (!$is_logged)
$template->SetBlock('not_logged');
else
$template->Set($auto->member['name'], '{user_from}');
$template->Compile('content');
$template->TitleSpeedBar("#" . $id . " - " . $array['{mark}'] . " " . $array['{model}']);

$metatags['description'] = $template->StringFormat($auto->lang['meta_descr_auto'], "#" . $id . " - " . $array['{mark}'] . " " . $array['{model}']);
$metatags['keywords'] = $auto->lang['meta_keys_auto'];
?>