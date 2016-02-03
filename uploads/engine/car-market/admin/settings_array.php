<?php
$block3_action = array(
    $auto->lang['block_dimanic_no_action'], 
    $auto->lang['block_dimanic_search'], 
    $auto->lang['block_dimanic_main_country'], 
    $auto->lang['block_dimanic_marks'], 
    $auto->lang['block_dimanic_country']
);

$block3_main_module = $block3_add_default = $block3_action;
unset($block3_main_module[1], $block3_main_module[2], $block3_add_default[1]);

$access_types = array(
                      'admin_main'     => $auto->lang['access_admin_main'],
                      'admin_add'      => $auto->lang['access_admin_add'],
                      'admin_edit'     => $auto->lang['access_admin_edit'],
                      'admin_city'     => $auto->lang['access_admin_city'],
                      'admin_model'    => $auto->lang['access_admin_model'],
                      'admin_fields'   => $auto->lang['access_admin_fields'],
                      'admin_settings' => $auto->lang['access_admin_settings'],
                      );
                      
$access = "<table width=100%><tr><td></td>";
foreach ($access_types as $type)
{
    $access .= "<td style='padding:2px;border-bottom:1px solid; border-color:#cccccc'>" . $type . "</td>\n";
}
$access .= "</tr>\n";

$count_photo = '<table width=100%>';
$groups = $db->query("SELECT id, group_name FROM " . USERPREFIX . "_usergroups");
while ($row = $db->get_row())
{
    $group[$row['id']] = $row['group_name'];
    
    $count_photo .= "<tr><td align=\"right\" style=\"padding:3px;\">" . $row['group_name'] . " </td><td> " . $tpl->inputText("save_con[count_photo][{$row['id']}]", $auto->config['count_photo'][$row['id']], 'size="8"') . "</td></tr>";

    if ($row['id'] == 5)
    {
        continue;
    }
    
    $access .= "<tr><td style='padding:2px;border-bottom:1px solid; border-color:#cccccc'>" . $row['group_name'] . "</td>";
    foreach ($access_types as $var => $type)
    {
        $access .= "<td style='padding:2px;border-bottom:1px solid; border-color:#cccccc'>" . $tpl->InputCheckbox("save_con[$var][]", $row['id'], @in_array($row['id'], $auto->config[$var])) . "</td>\n";
    }
    $access .= "</tr>\n";
}
$count_photo .= "</table>";
$access .= "</table>";

$main_country_array = array(
    '0' => $auto->lang['general_no_main_region']
);
if (!defined('INSTALL')) 
{
    if ($auto->use_country)
    {
        $auto->GetCountries();
        $main_country_array += $auto->countries;
    }
    elseif ($auto->use_region)
    {
        $auto->GetRegions();
        $main_country_array += $auto->regions;
    }
}


$show_moder = array(
    $auto->lang['general_show_moder_now_new'], 
    $auto->lang['general_show_moder_all'], 
    $auto->lang['general_show_moder_new'], 
    $auto->lang['general_show_moder_new_old'], 
    $auto->lang['general_show_moder_old']
);

$general_need_field = "<div align=\"left\" style=\"margin-left:150px;\">";
$general_need_field .= $tpl->InputCheckbox('save_con[need_field][race]', 1, $auto->config['need_field']['race']) . " " . $auto->lang['auto_race'] . "<br/>";
$general_need_field .= $tpl->InputCheckbox('save_con[need_field][power]', 1, $auto->config['need_field']['power']) . " " . $auto->lang['auto_power'] . "<br/>";
$general_need_field .= $tpl->InputCheckbox('save_con[need_field][contact_person]', 1, $auto->config['need_field']['contact_person']) . " " . $auto->lang['auto_contact_person'] . "<br/>";
$general_need_field .= $tpl->InputCheckbox('save_con[need_field][year]', 1, $auto->config['need_field']['year']) . " " . $auto->lang['auto_year'] . "<br/>";
$general_need_field .= $tpl->InputCheckbox('save_con[need_field][email]', 1, $auto->config['need_field']['email']) . " " . $auto->lang['auto_email'] . "<br/>";
$general_need_field .= $tpl->InputCheckbox('save_con[need_field][phone]', 1, $auto->config['need_field']['phone']) . " " . $auto->lang['auto_phone'] . "</div>";
$general_currency = "<div align=\"left\" style=\"margin-left:100px;\">";
$general_currency .= "RUR " . $tpl->InputText('save_con[currency][RUR]', $auto->config['currency']['RUR'], 'size="8"');
$general_currency .= "&nbsp; EUR " . $tpl->InputText('save_con[currency][EUR]', $auto->config['currency']['EUR'], 'size="8"');

$settings_array = array(
    "block1" => array(
        array(
            "title" => $auto->lang['block_last_allow'], 
            "descr" => $auto->lang['block_last_allow_desc'], 
            "setting" => Func::YesNo('block_last_allow'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_last_count_auto'], 
            "descr" => $auto->lang['block_last_count_auto_desc'], 
            "setting" => $tpl->InputText('save_con[block_last_count_auto]', $auto->config['block_last_count_auto'], 'size="8"'), 
            "regexp" => '#^[1-9]+$#', 
            "name" => 'block_last_count_auto'
        ), 
        array(
            "title" => $auto->lang['block_last_auto_photo'], 
            "descr" => $auto->lang['block_last_auto_photo_desc'], 
            "setting" => Func::YesNo('block_last_auto_photo'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_last_auto_user'], 
            "descr" => $auto->lang['block_last_auto_user_desc'], 
            "setting" => Func::YesNo('block_last_auto_user'), 
            "regexp" => false
        )
    ), 
    "block2" => array(
        array(
            "title" => $auto->lang['block_hot_allow'], 
            "descr" => $auto->lang['block_hot_allow_desc'], 
            "setting" => Func::YesNo('block_hot_allow'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_hot_count_auto'], 
            "descr" => $auto->lang['block_hot_count_auto_desc'], 
            "setting" => $tpl->InputText('save_con[block_hot_count_auto]', $auto->config['block_hot_count_auto'], 'size="8"'), 
            "regexp" => '#^[1-9]+$#', 
            "name" => 'block_hot_count_auto'
        ), 
        array(
            "title" => $auto->lang['block_hot_auto_photo'], 
            "descr" => $auto->lang['block_hot_auto_photo_desc'], 
            "setting" => Func::YesNo('block_hot_auto_photo'), 
            "regexp" => false
        ),
        array(
            "title" => $auto->lang['block_hot_auto_time'], 
            "descr" => $auto->lang['block_hot_auto_time_desc'], 
            "setting" => $tpl->InputText('save_con[block_hot_auto_time]', $auto->config['block_hot_auto_time'], 'size="8"'), 
            "regexp" => false
        )
    ), 
    "block3" => array(
        array(
            "title" => $auto->lang['block_dimanic_allow'], 
            "descr" => $auto->lang['block_dimanic_allow_desc'], 
            "setting" => Func::YesNo('block_dimanic_allow'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_dimanic_on_main_site'], 
            "descr" => $auto->lang['block_dimanic_on_main_site_desc'], 
            "setting" => $tpl->selection($block3_action, "save_con[block_dimanic_on_main_site]", $auto->config['block_dimanic_on_main_site']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_dimanic_on_main_module'], 
            "descr" => $auto->lang['block_dimanic_on_main_module_desc'], 
            "setting" => $tpl->selection($block3_main_module, "save_con[block_dimanic_on_main_module]", $auto->config['block_dimanic_on_main_module']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_dimanic_on_add'], 
            "descr" => $auto->lang['block_dimanic_on_add_desc'], 
            "setting" => $tpl->selection($block3_add_default, "save_con[block_dimanic_on_add]", $auto->config['block_dimanic_on_add']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_dimanic_on_search'], 
            "descr" => $auto->lang['block_dimanic_on_search_desc'], 
            "setting" => $tpl->selection($block3_add_default, "save_con[block_dimanic_on_search]", $auto->config['block_dimanic_on_search']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['block_dimanic_on_default'], 
            "descr" => $auto->lang['block_dimanic_on_default_desc'], 
            "setting" => $tpl->selection($block3_add_default, "save_con[block_dimanic_on_default]", $auto->config['block_dimanic_on_default']), 
            "regexp" => false
        )
    ), 
    "user" => array(
        array(
            "title" => $auto->lang['user_int_allow_add'], 
            "descr" => $auto->lang['user_int_allow_add_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_add][]', $auto->config['user_int_allow_add']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_allow_no_code'], 
            "descr" => $auto->lang['user_int_allow_no_code_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_no_code][]', $auto->config['user_int_allow_no_code']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_allow_no_moder'], 
            "descr" => $auto->lang['user_int_allow_no_moder'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_no_moder][]', $auto->config['user_int_allow_no_moder']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_allow_edit'], 
            "descr" => $auto->lang['user_int_allow_edit_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_edit][]', $auto->config['user_int_allow_edit']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_allow_del'], 
            "descr" => $auto->lang['user_int_allow_del_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_del][]', $auto->config['user_int_allow_del']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_allow_extend'], 
            "descr" => $auto->lang['user_int_allow_extend_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_extend][]', $auto->config['user_int_allow_extend']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_allow_change_exp'], 
            "descr" => $auto->lang['user_int_allow_change_exp_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[user_int_allow_change_exp][]', $auto->config['user_int_allow_change_exp']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_default_day_count'], 
            "descr" => $auto->lang['user_int_default_day_count_desc'], 
            "setting" => $tpl->selection($auto->count_day_array, 'save_con[user_int_default_day_count]', $auto->config['user_int_default_day_count']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_count_photo'], 
            "descr" => $auto->lang['user_int_count_photo_desc'], 
            "setting" => $count_photo, 
            "regexp" => '#^[0-9]+$#', 
            "name" => 'count_photo'
        ), 
        array(
            "title" => $auto->lang['user_int_default_sort'], 
            "descr" => $auto->lang['user_int_default_sort_desc'], 
            "setting" => $tpl->selection($auto->sort_array, 'save_con[user_int_default_sort]', $auto->config['user_int_default_sort']) . " " . $tpl->selection($auto->subsort_array, "save_con[user_int_default_subsort]", $auto->config['user_int_default_subsort']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_show_counter'], 
            "descr" => $auto->lang['user_int_show_counter_desc'], 
            "setting" => Func::YesNo('user_int_show_counter'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_show_marks_no_auto'], 
            "descr" => $auto->lang['user_int_show_marks_no_auto_desc'], 
            "setting" => Func::YesNo('user_int_show_marks_no_auto'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_send_mail'], 
            "descr" => $auto->lang['user_send_mail_desc'], 
            "setting" => Func::YesNo('user_send_mail'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_pre_page_table'], 
            "descr" => $auto->lang['user_int_pre_page_table_desc'], 
            "setting" => $tpl->InputText('save_con[user_int_pre_page][table]', $auto->config['user_int_pre_page']['table'], 'size="8"'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['user_int_pre_page_modern'], 
            "descr" => $auto->lang['user_int_pre_page_modern_desc'], 
            "setting" => $tpl->InputText('save_con[user_int_pre_page][modern]', $auto->config['user_int_pre_page']['modern'], 'size="8"'), 
            "regexp" => false
        ),
        array(
            "title" => $auto->lang['access_admin'], 
            "descr" => $auto->lang['access_admin_desc'], 
            "setting" => $access, 
            "regexp" => false
        )
    ), 
    "photo" => array(
        array(
            "title" => $auto->lang['photo_upload_type'], 
            "descr" => $auto->lang['photo_upload_type_desc'], 
            "setting" => $tpl->selection(array(
                                                1 => $auto->lang['photo_upload_type_1'],
                                                2 => $auto->lang['photo_upload_type_2'],
                                                3 => $auto->lang['photo_upload_type_3']
                                                ),
                                         'save_con[photo_upload_type]',
                                         $auto->config['photo_upload_type']), 
            "regexp" => false 
        ), 
        array(
            "title" => $auto->lang['photo_size_byte'], 
            "descr" => $auto->lang['photo_size_byte_desc'], 
            "setting" => $tpl->InputText("save_con[photo_size_byte]", $auto->config['photo_size_byte']), 
            "regexp" => '#^[0-9]+$#', 
            "name" => 'photo_size_byte'
        ), 
        array(
            "title" => $auto->lang['photo_size_width'], 
            "descr" => $auto->lang['photo_size_width_desc'], 
            "setting" => $tpl->InputText("save_con[photo_size_width]", $auto->config['photo_size_width']), 
            "regexp" => '#^[0-9]+$#', 
            "name" => 'photo_size_width'
        ), 
        array(
            "title" => $auto->lang['photo_size_width_th'], 
            "descr" => $auto->lang['photo_size_width_th_desc'], 
            "setting" => $tpl->InputText("save_con[photo_size_width_th]", $auto->config['photo_size_width_th']), 
            "regexp" => '#^[0-9]+$#', 
            "name" => 'photo_size_width_th'
        ), 
        array(
            "title" => $auto->lang['photo_quality'], 
            "descr" => $auto->lang['photo_quality_desc'], 
            "setting" => $tpl->InputText("save_con[photo_quality]", $auto->config['photo_quality']), 
            "regexp" => '#^[0-9]{2,3}$#', 
            "name" => 'photo_quality'
        ), 
        array(
            "title" => $auto->lang['photo_logo'], 
            "descr" => $auto->lang['photo_logo_desc'], 
            "setting" => Func::YesNo('photo_logo'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['photo_size_for_logo'], 
            "descr" => $auto->lang['photo_size_for_logo_desc'], 
            "setting" => $tpl->InputText("save_con[photo_size_for_logo]", $auto->config['photo_size_for_logo']), 
            "regexp" => '#^[0-9]+$#', 
            "name" => 'photo_size_for_logo'
        )
    ), 
    "general" => array(
        array(
            "title" => $auto->lang['general_allow_module'], 
            "descr" => $auto->lang['general_allow_module_desc'], 
            "setting" => Func::YesNo('general_allow_module'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_name_module'], 
            "descr" => $auto->lang['general_name_module_desc'], 
            "setting" => $tpl->InputText("save_con[general_name_module]", $auto->config['general_name_module']), 
            "regexp" => '#[a-z_\-]+#', 
            "name" => 'general_name_module'
        ), 
        array(
            "title" => $auto->lang['general_allow_add'], 
            "descr" => $auto->lang['general_allow_add_desc'], 
            "setting" => Func::YesNo('general_allow_add'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_mod_rewrite'], 
            "descr" => $auto->lang['general_mod_rewrite_desc'], 
            "setting" => Func::YesNo('general_mod_rewrite'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_AJAX'], 
            "descr" => $auto->lang['general_AJAX_desc'], 
            "setting" => Func::YesNo('general_AJAX'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_cache'], 
            "descr" => $auto->lang['general_cache_desc'], 
            "setting" => Func::YesNo('general_cache'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_main_page'], 
            "descr" => $auto->lang['general_main_page_desc'], 
            "setting" => Func::YesNo('general_main_page'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_RSS'], 
            "descr" => $auto->lang['general_RSS_desc'], 
            "setting" => Func::YesNo('general_RSS'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_inform'], 
            "descr" => $auto->lang['general_inform_desc'], 
            "setting" => Func::YesNo('general_inform'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_email'], 
            "descr" => $auto->lang['general_email_desc'], 
            "setting" => $tpl->InputText('save_con[general_email]', $auto->config['general_email']), 
            "regexp" => '#(^[\w-]+(\.[\w-]+)*@([\w-]+)\.+[a-zA-Z]{2,3}$|^$)#', 
            "name" => 'general_email'
        ), 
        array(
            "title" => $auto->lang['general_view_mode'], 
            "descr" => $auto->lang['general_view_mode_desc'], 
            "setting" => $tpl->selection(array(
                "table" => $auto->lang['general_view_mode_table'], 
                "modern" => $auto->lang['general_view_mode_modern']
            ), 'save_con[general_view_mode]', $auto->config['general_view_mode']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_main_country'], 
            "descr" => $auto->lang['general_main_country_desc'], 
            "setting" => $tpl->selection($main_country_array, 'save_con[general_main_country]', $auto->config['general_main_country']), 
            "regexp" => false,
            "noinstall" => true
        ), 
        array(
            "title" => $auto->lang['general_need_field'], 
            "descr" => $auto->lang['general_need_field_desc'], 
            "setting" => $general_need_field, 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_currency'], 
            "descr" => $auto->lang['general_currency_desc'], 
            "setting" => $general_currency, 
            "regexp" => '#^[0-9\.]+$#', 
            "name" => 'currency'
        ), 
        array(
            "title" => $auto->lang['general_moderator'], 
            "descr" => $auto->lang['general_moderator_desc'], 
            "setting" => $tpl->SelectionMulti($group, 'save_con[general_moderator][]', $auto->config['general_moderator']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_show_moder'], 
            "descr" => $auto->lang['general_show_moder_desc'], 
            "setting" => $tpl->selection($show_moder, 'save_con[general_show_moder]', $auto->config['general_show_moder']), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_allow_reg'], 
            "descr" => $auto->lang['general_allow_reg_desc'], 
            "setting" => Func::YesNo('general_allow_reg'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_count_main_auto'], 
            "descr" => $auto->lang['general_count_main_auto_desc'], 
            "setting" => $tpl->InputText('save_con[general_count_main_auto]', $auto->config['general_count_main_auto'], 'size="8"'), 
            "regexp" => '#[0-9]+#', 
            "name" => 'general_count_main_auto'
        ), 
        array(
            "title" => $auto->lang['general_auto_photos'], 
            "descr" => $auto->lang['general_auto_photos_desc'], 
            "setting" => Func::YesNo('general_auto_photos'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_allow_statistic'], 
            "descr" => $auto->lang['general_allow_statistic_desc'], 
            "setting" => Func::YesNo('general_allow_statistic'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['count_yandex_export'], 
            "descr" => $auto->lang['count_yandex_export_desc'], 
            "setting" => $tpl->InputText('save_con[count_yandex_export]', $auto->config['count_yandex_export'], "size='8'"), 
            "regexp" => '#^[0-9]+$#', 
            "name" => 'count_yandex_export'
        ),
        array(
            "title" => $auto->lang['general_allow_block_statistic'], 
            "descr" => $auto->lang['general_allow_block_statistic_desc'], 
            "setting" => Func::YesNo('general_allow_block_statistic'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_allow_block_search'], 
            "descr" => $auto->lang['general_allow_block_search_desc'], 
            "setting" => Func::YesNo('general_allow_block_search'), 
            "regexp" => false
        ), 
        array(
            "title" => $auto->lang['general_debug'], 
            "descr" => $auto->lang['general_debug_desc'], 
            "setting" => Func::YesNo('general_debug'), 
            "regexp" => false
        )
    )
);
?>