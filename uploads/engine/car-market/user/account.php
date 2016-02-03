<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

$auto->search_array = array();

if ($auto->member['id'])
{
    $auto->search_array = array('author_id' => $auto->member['id']);
}
elseif ($auto->guest_session)
{
    $auto->search_array = array('guest_session' => $auto->guest_session);
}
else
{
    $template->msg($auto->lang['error'], $auto->lang['access_denied'], true);
    return ;
}

$template->load('account')
         ->Set($template->InputCheckbox('master', 1, 0, 'id="master"'), '{master_checkbox}');

// Мои авто
$auto->Search(array("count" => $auto->config['user_int_pre_page']['table'], 'show_status' => 1));
if ($auto->autos)
{
    $template->PageNavigation(array('action' => "account"), // Url array
    $auto->autos_count, // Count all auto in this query
    $auto->config['user_int_pre_page']['table'], // Count auto per page
    $_REQUEST['page'], // Current page
    count($auto->autos) // Now auto
    );

    $template->OpenRow('row_auto');
    foreach ($auto->autos as $id=>$auto_one)
    {
        $array = $auto->ShowAuto($id);
        $template->SetRow($array, "row_auto");
    }
    $template->CloseRow('row_auto');

    $template->SetResult('PageNavigation', "{pages}");
}
else
$template->SetBlock('row_auto', $auto->lang['no_my_auto']);

if (in_array($auto->member['group'], $auto->config['user_int_allow_del']))
$template->SetBlock('allow_del');

if ((in_array($auto->member['group'], $auto->config['user_int_allow_del']) &&
(
($auto->member['id'] && $auto->member['id'] == $auto->autos[$id]['author_id']) ||
($auto->guest_session && $auto->guest_session == $auto->autos[$id]['guest_session'])
)) || MODER
)
$template->SetForm(array_merge($hidden_array, array('subaction' => "del")), $template->main_url, 'POST', 'id="auto_form"');


// Избранное
$favorites = array();
if (!empty($_COOKIE['auto_favorites']) && $favorites = explode(",", $_COOKIE['auto_favorites']))
{
    $auto->Search(array("count" => 100), $favorites);

    if ($auto->autos)
    {
        $template->Set("<img src=\"{THEME}/car-market/images/checked.gif\" id=\"compare_master\" title=\"{$auto->lang['compare_master_title']}\" />", '{compare_master}')
        ->OpenRow('row_favorites');
        foreach ($auto->autos as $id=>$auto_one)
        {
            $favorites_id[] = $id;
            $template->SetRow($auto->ShowAuto($id), 'row_favorites');
        }
        $template->CloseRow('row_favorites');
        setcookie("auto_favorites", implode(",", $favorites_id), time() + 365 * 24 * 3600);
    }
    else
    {
        $template->SetBlock('row_favorites', $auto->lang['no_favorites_auto']);
        setcookie('auto_favorites', '', time() - 365);
    }
}
else
{
    $template->SetBlock('row_favorites', $auto->lang['no_favorites_auto']);
}



// Настройки

$auto->PreparationSearchArray();

if ($auto->use_country)
$auto->GetCountries(true);
if ($auto->use_region)
$auto->GetRegions(empty($auto->search_array['country_id'])?0:$auto->search_array['country_id'], true);

$auto->GetCities($auto->search_array['country_id'], $auto->search_array['region_id'], true);

$auto->GetMarks(true);
$auto->GetModels($auto->search_array['mark_id'], true);

$auto->ShowSearch();
$template->Set($template->Selection(array(0 => $auto->lang['account_not_currency']) + $auto->currency_array, 'currency_defalut', $auto->search_array['currency_defalut']), "{currency_defalut}");
$template->Set($template->Selection($auto->sort_array, 'sort', $auto->search_array['sort']) . " " . $template->Selection($auto->subsort_array, 'subsort', $auto->search_array['subsort']), "{sort}");
//$template->Set();
//$template->Set();

$JScript = <<<JS
<script type="text/javascript" >
$(document).ready(function()
{
	$("#form_settings").submit(function()
	{
		var str = $(this).serialize();
		$.cookie('auto_settings', str, {expires: 365, path:"/"});
		$.blockUI({message:'{$auto->lang['settings_save']}'});
		window.setTimeout($.unblockUI, 2000);
		return false;
	});
});
function favorites(img, id)
{
	if (!favorites && $.cookie('auto_favorites'))
		var favorites = $.cookie('auto_favorites').split(",");
	else
		var favorites = new Array();
		
	var index = FindIndex(favorites, id);

	if (favorites.length > 0 && index != -1)
	{
		favorites.splice(index, 1);
		$(img).parent("td").parent("tr").fadeOut();
	}
		
	$.cookie('auto_favorites', favorites.toString(), {expires: 365, path:"/"});
}
</script>
JS;
$template->Compile('content', $JScript);
$template->TitleSpeedBar($auto->lang['account']);

?>