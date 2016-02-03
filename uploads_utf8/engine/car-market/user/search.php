<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if ($auto->use_country)
{
    $auto->GetCountries(true);
}
if ($auto->use_region)
{
    $auto->GetRegions(0, true);
}

$auto->GetCities(0, 0, true);

$auto->GetMarks(true);
$auto->GetModels(0, true);

$template->load('search');

$auto->ShowSearch();

if (!$auto->config['general_main_page'])
{
    $hidden_array['do'] = $auto->config['general_name_module'];
}

unset($hidden_array['action']);
$template->SetForm($hidden_array, $template->main_url, "GET");
$template->Compile('content');
$template->TitleSpeedBar($auto->lang['search']);

$metatags['description'] = $auto->lang['meta_descr_search'];
$metatags['keywords'] = $auto->lang['meta_keys_search'];
?>