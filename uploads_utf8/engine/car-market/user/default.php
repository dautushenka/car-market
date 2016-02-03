<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!empty($_REQUEST['view_mode']) && in_array($_REQUEST['view_mode'], array('table', 'modern')))
{
    set_cookie ("auto_view_mode", $_REQUEST['view_mode'], 365);
    $auto->config['general_view_mode'] = $_REQUEST['view_mode'];
}
elseif (!empty($_COOKIE['auto_view_mode']) &&  in_array($_COOKIE['auto_view_mode'], array('table', 'modern')))
{
    $auto->config['general_view_mode'] = $_COOKIE['auto_view_mode'];
}


$template->load('sort');
foreach ($auto->sort_array as $key=>$value)
{
    if ($auto->search_array['sort'] == $key && strtoupper($auto->search_array['subsort']) == 'ASC')
    {
        $subsort = "DESC";
    }
    else
    {
        $subsort ="ASC";
    }
    	
    $template->Set("<a class=\"ajax_link\" href=\"" . $template->GetUrl($auto->search_array, array(), array('sort' => $key, "subsort" => $subsort), array(), array("use_alt_url"=>false)) . "\" >", "[$key]");
    $template->Set("</a>", "[/$key]");
}
$template->Compile('sort');

if (!AJAX && !RSS && !$auto->config['general_allow_block_search'])
{
    if ($auto->use_country)
    {
        $auto->GetCountries(true);
    }
    if ($auto->use_region)
    {
        $auto->GetRegions($auto->search_array['country_id'], true);
    }

    $auto->GetCities($auto->search_array['country_id'], $auto->search_array['region_id'], true);

    $auto->GetMarks(true);
    $auto->GetModels($auto->search_array['mark_id'], true);

    $template->load('head')->SetBlock('sort')->SetBlock('view');

    if ($auto->config['general_RSS'])
    {
        $template->temp_main_url = $config['http_home_url'] . "engine/car-market/rss.php";
        $template->Set('<a href="' . $template->GetUrl($auto->search_array, array(), array(), array(), array("use_alt_url" => false)) . '" >', '[rss]');
        $template->set("</a>", "[/rss]");
    }

    $JS = '';
    $auto->ShowSearch($JS);
    
    if ($JS)
    {
        $JS = "<script type='text/javascript'>$().ready(function(){{$JS}});</script>";
    }

    $template->SetResult('sort', "{sort}");

    $template->Set("<a class=\"ajax_link\" href=\"" . $template->GetUrl($auto->search_array, array(), array('view_mode' => 'modern'), array(), array("use_alt_url"=>false)) . "\" >", "[modern]");
    $template->Set("</a>", "[/modern]");
    $template->Set("<a class=\"ajax_link\" href=\"" . $template->GetUrl($auto->search_array, array(), array('view_mode' => 'table'), array(), array("use_alt_url"=>false)) . "\" >", "[table]");
    $template->Set("</a>", "[/table]");

    if (!$auto->config['general_main_page'])
    $hidden_array['do'] = $auto->config['general_name_module'];

    unset($hidden_array['action']);

    $template->SetForm($hidden_array, $template->main_url, "GET", 'id="filter"');
    $template->Compile('content', $JS);
}

$auto->Search(array("count" => $auto->config['user_int_pre_page'][$auto->config['general_view_mode']]));

if ($auto->autos)
{
    if (!RSS)
    $template->PageNavigation($auto->search_array, // Url array
    $auto->autos_count, // Count all auto in this query
    $auto->config['user_int_pre_page'][$auto->config['general_view_mode']], // Count auto per page
    $_REQUEST['page'], // Current page
    count($auto->autos), // Now auto
    array("use_alt_url" => false, "link_script" => 'class="ajax_link"') // Options links
    );

    $JScript = <<<JS
<script type="text/javascript" >
$(document).ready(function()
{
	$("a.go_big_photo").lightBox(
	{
		imageLoading: '/engine/car-market/images/admin/lightbox-ico-loading.gif',
		imageBtnPrev: '/engine/car-market/images/admin/lightbox-btn-prev.gif',
		imageBtnNext: '/engine/car-market/images/admin/lightbox-btn-next.gif',
		imageBtnClose: '/engine/car-market/images/admin/lightbox-btn-close.gif',
		imageBlank: '/engine/car-market/images/admin/lightbox-blank.gif',
		txtImage : '{$auto->lang['txtImage']}',
		txtOf : '{$auto->lang['txtOf']}'
	});
	$AJAX
});
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.lightbox-0.5.js');
</script>
JS;

	$template->AJAX_script = <<<JS
<script type="text/javascript" >
$.LoadScript(dle_root + 'engine/car-market/javascript/jquery.lightbox-0.5.js');
$("a.go_big_photo").lightBox(
{
	imageLoading: '/engine/car-market/images/admin/lightbox-ico-loading.gif',
	imageBtnPrev: '/engine/car-market/images/admin/lightbox-btn-prev.gif',
	imageBtnNext: '/engine/car-market/images/admin/lightbox-btn-next.gif',
	imageBtnClose: '/engine/car-market/images/admin/lightbox-btn-close.gif',
	imageBlank: '/engine/car-market/images/admin/lightbox-blank.gif',
	txtImage : '{$auto->lang['txtImage']}',
	txtOf : '{$auto->lang['txtOf']}'
});
</script>
JS;

	$template->SetStyleScript(array('/engine/car-market/css/jquery.lightbox-0.5.css'));

	if (RSS)
	$template->load('rss');
	else
	{
	    if ($auto->config['general_view_mode'] == 'table')
	    $template->load('autos');
	    else
	    $template->load('autos_modern');
	}

	if (MODER)
	$template->SetBlock('moder');

	$template->Set($template->InputCheckbox('master', 1, 0, 'id="master"'), '{master_checkbox}')
	->Set("<img src=\"{THEME}/car-market/images/compare_checked.gif\" id=\"compare_master\" title=\"{$auto->lang['compare_master_title']}\" />", '{compare_master}')
	->OpenRow('row_auto');

	foreach ($auto->autos as $id=>$auto_one)
	{
	    if (RSS)
	    {
	        $array = $auto->ShowAuto($id, array("show_photo" => 0, "show_edit" => 0)) + ShowPhoto($id, $auto);
	        	
	        if (!$auto->config['general_mod_rewrite'])
	        ConvertToRSSURL($array['{auto_url}']);
	        	
	        $template->Set($array['{auto_url}'], '{rsslink}');
	    }
	    else
	    $array = $auto->ShowAuto($id);

	    $template->SetRow($array, "row_auto");
	}

	$template->CloseRow('row_auto');

	if (RSS)
	$template->Compile('rss');
	else
	{
	    if (MODER)
	    $template->SetForm(array('subaction' => 'del') + $hidden_array, '', 'POST', 'id="auto_form"');
	    if (!AJAX)
	    $template->WrapContent("<div id='auto-content'>", "</div>");
	    	
	    $template->SetResult('sort', "{sort}");
	    	
	    $template->SetResult('PageNavigation', '{pages}')->Compile('content', $JScript);
	}
}
else
{
    $template->AddToContent($auto->lang['not_found']);
}

?>