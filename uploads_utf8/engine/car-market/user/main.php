<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if ($auto->use_country)
$auto->GetCountries(true);
if ($auto->use_region)
$auto->GetRegions($auto->search_array['country_id'], true);

$auto->GetCities($auto->search_array['country_id'], $auto->search_array['region_id'], true);

$auto->GetMarks(true);
$auto->GetModels($auto->search_array['mark_id'], true);

if (!$auto->config['general_allow_block_search'])
{
    $template->load('head');
    $auto->ShowSearch();

    if (!$auto->config['general_main_page'])
    $hidden_array['do'] = $auto->config['general_name_module'];
     
    unset($hidden_array['action']);

    $template->SetForm($hidden_array, '/index.php', "GET", 'id="filter"');
    $template->Compile('head');
}

if ($auto->config['general_allow_statistic'])
{
    if (!$auto->config['general_cache'] || !($cache = Cache::GetHTMLCache('stats')))
    {
        $auto_on_site = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'), array('allow_site' => 1));
        $auto_reg = $base->SelectOne('auto_autos', array('max' => 'MAX(id)'));

        $base->SetWhere('add_date', $base->timer->cur_time - 24*60*60, ">");
        $today = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'));

        $template->load('stats')
        ->Set($auto_on_site['count'], "{auto_now}")
        ->Set($today['count'], "{auto_today}")
        ->Set($auto_reg['max'], "{auto_max}")
        ->Compile('stats');
        	
        if ($auto->config['general_cache'])
        Cache::SetHTMLCache('stats', $template->stats);
    }
    else
    $template->stats = $cache;
}

$template->load('main')
->SetResult('head', "{head}")
->SetResult('stats', '{stats}');

if ($auto->config['general_main_country'])
{
    if (!$auto->config['general_cache'] || !$cache = Cache::GetHTMLCache('main_country'))
    {
        if ($auto->use_country)
        {
            $name = $auto->countries[$auto->config['general_main_country']];
            if ($auto->use_region)
            $items = $auto->GetRegions($auto->config['general_main_country']);
            else
            $items = $auto->GetCities($auto->config['general_main_country'], 0);
        }
        elseif ($auto->use_region)
        {
            $name = $auto->regions[$auto->config['general_main_country']];
            $items = $auto->GetCities(0, $auto->config['general_main_country']);
        }
        $template->Set($name, "{name}");
        unset($items[0]);

        $template->OpenRow('row_items');
        foreach ($items as $id=>$item)
        {
            if ($auto->use_country)
            {
                if ($auto->use_region)
                $url = $template->GetUrl(array('country_id' => $auto->config['general_main_country'], 'region_id' => $id));
                else
                $url = $template->GetUrl(array('country_id' => $auto->config['general_main_country'], 'city_id' => $id));
            }
            elseif ($auto->use_region)
            $url = $template->GetUrl(array('region_id' => $auto->config['general_main_country'], 'city_id' => $id));
            	
            $item = "<a href=\"" . $url . "\" >$item</a>";
            $template->SetRow(array("{item}" => $item), 'row_items');
        }
        $template->CloseRow('row_items');
        
        if ($auto->config['general_cache'])
        Cache::SetHTMLCache('main_country', $template->GetBlockContent('main_country_region'));
        	
        $template->SetBlock('main_country_region');
    }
    else
    $template->SetBlockContent('main_country_region', $cache);
}
	
	
If ($auto->config['general_auto_photos'])
$auto->search_array['isset_photo'] = 1;

if (!$auto->config['general_cache'] || !$cache = Cache::GetArrayCache('main_autos'))
{
    $auto->Search(array('count' => $auto->config['general_count_main_auto']));

    if ($auto->config['general_cache'])
    Cache::SetArrayCache('main_autos', $auto->autos);
}
else
$auto->autos = $cache;


if ($auto->autos)
{
    $template->OpenRow('row_auto');
    foreach ($auto->autos as $id=>$auto_one)
    {
        $template->SetRow($auto->ShowAuto($id), "row_auto");
    }
    $template->CloseRow('row_auto');
}

$jscript = <<<JS
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
});
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.lightbox-0.5.js');
</script>
JS;

$template->SetStyleScript(array('/engine/car-market/css/jquery.lightbox-0.5.css'));

$template->Compile('content', $jscript);

$metatags['description'] = $auto->lang['meta_descr_main'];
$metatags['keywords'] = $auto->lang['meta_keys_main'];
?>