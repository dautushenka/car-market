<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (($action == "add" || $action == "doadd") && !$auto->config['general_allow_add'])
{
    $template->msg($auto->lang['info'], $auto->lang['no_allow_add'], true);
    return ;
}
if (($action == "add" || $action == "doadd") && !in_array($member_id['user_group'], $auto->config['user_int_allow_add']))
{
    $template->msg($auto->lang['info'], $auto->lang['user_no_allow_add'], true);
    return ;
}
if (($action == "edit" || $action == "save") && !MODER && !in_array($member_id['user_group'], $auto->config['user_int_allow_edit']))
{
    $template->msg($auto->lang['info'], $auto->lang['user_no_allow_edit'], true);
    return ;
}
if (($action == "edit" || $action == "save") && !$id)
{
    $template->msg($auto->lang['error'], $auto->lang['auto_not_found'], true);
    return ;
}

include_once(DLE_CLASSES . "parse.class.php");
include_once ENGINE_DIR.'/car-market/classes/thumb.class.php';

$cur_fotos = '';
$edit = array(
			'country_id'     => 0,
			'region_id'      => 0,
			'city_id'        => 0,
			'city_other'     => '',
			'mark_id'        => 0,
			'model_id'       => 0,
			'model_other'    => '',
			'capacity_motor' => '',
			'power'          => '',
			'race'           => '',
			'cost'           => '',
			'currency'       => 'USD',
			'auction'        => 0,
			'exchange'       => 0,
			'description'    => '',
			'contact_person' => '',
            'xfields'        => '',
			'email'          => '',
			'phone'          => '',
			'photo'          => 0,
			'photo_count'    => '',
			'exp_date'       => '',
			'add_date'       => '',
			'block_date'     => '',
			'allow_site'     => '',
			'author'         => '',
			'guest_session'  => '',
);

switch ($action)
{
    case 'doadd':
        if ($_SESSION['form_salt'] != $_POST['form_salt'])
        {
            die("Hacking attempt!");
        }
        if ($id = $auto->Add($_POST))
        {
            //include ENGINE_DIR . '/car-market/payment.php';
            $template->msg($auto->lang['auto_add'], $auto->lang['auto_add_desc']);
            unset($_SESSION['form_salt']);
            return ;
        }
        $hidden_array['form_salt'] = $_SESSION['form_salt'];
        $edit = $_POST;
        if (empty($edit['photo']))
        {
            $edit['photo'] = 0;
        }
        break;

    case 'edit':
        if (!($edit = $base->SelectOne('auto_autos', array('*'), array('id' => $id))))
        {
            $template->msg($auto->lang['error'], $auto->lang['auto_not_found'], true);
            return ;
        }

        if (!MODER &&
        (
        ($edit['author_id'] && $edit['author_id'] != $auto->member['id']) ||
        ($edit['guest_session'] && $edit['guest_session'] != $auto->guest_session) ||
        !in_array($auto->member['group'], $auto->config['user_int_allow_edit'])
        )
        )
        {
            $template->msg($auto->lang['error'], $auto->lang['no_allow_edit_auto'], true);
            return ;
        }
        foreach ($edit as $key=>&$value)
        {
            if (!$value)
            {
                $value = '';
            }
        }
        if (empty($edit['photo']))
        {
            $edit['photo'] = 0;
        }
        $hidden_array['action'] = 'save';
        $hidden_array['id'] = $id;
        break;

    case "save":
        if ($auto->SaveUser($_POST, $id))
        {
            $template->msg($auto->lang['auto_edit'], $auto->lang['auto_edit_desc']);
            return ;
        }
        	
        $edit = $_POST;
        if (empty($edit['photo']))
        {
            $edit['photo'] = 0;
        }
        $hidden_array['subaction'] = 'save';
        $hidden_array['id'] = $id;
        break;

    default:
        $hidden_array['action'] = "doadd";
        if ($is_logged)
        $edit['email'] = $member_id['email'];
        $hidden_array['form_salt'] = $_SESSION['form_salt'] = uniqid(time());
        break;
}

if ($auto->Errors)
{
    $errors =  "  <font color=\"red\" >" . $auto->lang['isset_error'] . "</font><ol>";
    foreach ($auto->Errors as $error)
    {
        $errors .= "<li>" . $error . "</li>";
    }
    $errors .= "</ol>";
    $template->msg($auto->lang['error'], $errors);
}

if ($auto->use_country)
$auto->GetCountries();
if ($auto->use_region)
$auto->GetRegions($edit['country_id']);

$auto->GetCities($edit['country_id'], $edit['region_id']);

$auto->GetMarks();
$auto->GetModels($edit['mark_id']);

if (!$is_logged && $auto->config['general_allow_reg'])
$auto->config['need_field']['email'] = true;

$cur_photos_jsa = '';
if ($edit['photo'])
{
    $photos = $base->Select('auto_images', array('*'), array('auto_id' => $id));

    while ($image = $base->FetchArray())
    {
        if ($cur_photos_jsa)
        {
            $cur_photos_jsa .= ", ";
        }
        
        $cur_photos_jsa .= "{".
                            "image_id:" . $image[id] . ",".
                            "image_url:'" . $config['http_home_url'] . "uploads/auto_foto/" . $image['model_id'] . "/" . $image['image_name'] . "',".
                            "image_th_url:'" . $config['http_home_url'] . "uploads/auto_foto/" . $image['model_id'] . "/thumbs/" . $image['image_name'] . 
                            "'}";
    }
    $cur_photos_jsa = "[" . $cur_photos_jsa . "]";
}
else 
{
    $cur_photos_jsa = "[]";
}

$set_array = array(
					"{sel_marka}"      => $template->Selection($auto->marks, 'mark_id', $edit['mark_id'], 'id="mark_id"'),
					"{sel_model}"      => $template->Selection($auto->models, 'model_id', $edit['model_id'], 'id="model_id"'),
					"{model_other}"    => $template->InputText('model_other', $edit['model_other'], 'id="model_other"'),
					"{sel_city}"       => $template->Selection($auto->cities, 'city_id', $edit['city_id'], 'id="city_id"'),
					"{city_other}"     => $template->InputText('city_other', $edit['city_other'], 'id="city_other"'),
					"{year}"           => $template->Selection(array("" => $auto->lang['no_show']) + $auto->year_array, "year", $edit['year'],  ((empty($auto->config['need_field']['year']))?'':' validate="required:true"')),
					"{color}"          => $template->InputHidden('color', $edit['color'], 'id="color"') . "&nbsp;<div id='color_div'><div></div></div>",
					"{race}"           => $template->InputText('race', $edit['race'], 'class="race"' . ((empty($auto->config['need_field']['race']))?'':' validate="required:true"')),
					"{power}"          => $template->InputText('power', $edit['power'], 'class="power"' . ((empty($auto->config['need_field']['power']))?'':' validate="required:true"')),
					"{capacity_motor}" => $template->InputText('capacity_motor', $edit['capacity_motor'], 'class="capacity_motor"' . ((empty($auto->config['need_field']['capacity_motor']))?'':' validate="required:true"')),
					"{cost}"           => $template->InputText('cost', $edit['cost'], 'class="cost"'),
					"{currency}"       => $template->Selection($auto->currency_array, 'currency', $edit['currency']),
					"{check_auction}"  => $template->InputCheckbox('auction', 1, $edit['auction']),
					"{check_exchange}" => $template->InputCheckbox('exchange', 1, $edit['exchange']),
					"{description}"    => $edit['description'],
					"{contact_person}" => $template->InputText('contact_person', $edit['contact_person'], 'class="contact_person"' . ((empty($auto->config['need_field']['contact_person']))?'':' validate="required:true"')),
					"{email}"          => $template->InputText('email', $edit['email'], 'class="email"' . ((empty($auto->config['need_field']['email']))?'':' validate="required:true, email:true"')),
					"{phone}"          => $template->InputText('phone', $edit['phone'], 'class="phone"' . ((empty($auto->config['need_field']['phone']))?'':' validate="required:true"')),
					"{count_day}"      => $template->Selection($auto->count_day_array, 'count_day', $auto->config['user_int_default_day_count']),
					"{cur_photo}"      => '<table id="images"></table>',
);

$set_array["{xfields}"] = '';
foreach ($auto->xfields->DecodeFields($edit['xfields']) as $fid => $field)
{
    if (!empty($auto->lang[$field['title']]))
    {
        $field['title'] = $auto->lang[$field['title']];
    }
    
    $set_array["{xfield_{$fid}_title}"] = $field['title'];
    $set_array["{xfield_{$fid}_descr}"] = $field['description'];
    $set_array["{xfield_{$fid}_html}"] = $set_array["{xfield_$fid}"] = $field['html'];
    
    $set_array["{xfields}"] .= $field['title'] . ": " . $field['html'] . "<br />";
}

$max_photo = $auto->config['count_photo'][$auto->member['group']];
if (!$max_photo)
{
    $max_photo = 0;
}

$sizeLimit = 1024 * $auto->config['photo_size_byte'];
$JScript = <<<JS

var photos = 0;
var current_photos = $cur_photos_jsa;
var del_txt = "{$auto->lang['del']}";
var main_txt = "{$auto->lang['main_photo']}";
var allow_max_photo = $max_photo;

$(document).ready(function()
{
	try
	{
		$('#form_add').validate(
		{
			errorElement: "label",
			success: function(label) 
			{
				label.text("ok!").addClass("success");
			},
			rules:
			{
				model_other:"model",
				city_other:"city",
				city_id:"sel",
				model_id:"sel"
			},
			messages:
			{
				name:
				{
					required:"{$auto->lang['reg_err_7']}",
					remote: $.format('"{0}" {$auto->lang['reg_err_9']}')
				}
			},
            submitHandler:function(form)
			{
				$(form).find('input[type="submit"]').replaceWith('Loading....');
				
				form.submit();
			}
		});
	}
	catch(e) {}
	$('#color_div').ColorPicker({
		color: '{$edit['color']}',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$("#color_div div").css('backgroundColor', '#' + hex);
			$("#color").val('#' + hex);
		}
	});
	$("#color_div div").css('backgroundColor', '{$edit['color']}');
	
	lightBox_opt = {
        imageLoading: '/engine/car-market/images/admin/lightbox-ico-loading.gif',
        imageBtnPrev: '/engine/car-market/images/admin/lightbox-btn-prev.gif',
        imageBtnNext: '/engine/car-market/images/admin/lightbox-btn-next.gif',
        imageBtnClose: '/engine/car-market/images/admin/lightbox-btn-close.gif',
        imageBlank: '/engine/car-market/images/admin/lightbox-blank.gif'
    };
	
	if (current_photos.length > 0)
    {
        for (index in current_photos)
        {
            AddPhoto(current_photos[index], '$id', {$edit['photo']} == current_photos[index].image_id);
            photos++;
        }
    }
    
    if (allow_max_photo <= photos)
    {
        $('#uploads').hide().after('<span id="max_photo">{$auto->lang['max_foto']}</span>');
    }
    
    $("#images a.image").lightBox(lightBox_opt);
	
	$.metadata.setType("attr", "validate");
    $.validator.addMethod("model", function(value, elemen)
    {
        return value != '' || (value == '' && $("#model_id").val() != 0)
    }, "{$auto->lang['auto_error_model']}");
    $.validator.addMethod("city", function(value, elemen)
    {
        return value != '' || (value == '' && $("#city_id").val() != 0)
    }, "{$auto->lang['auto_error_city']}");
    $.validator.addMethod("sel", function(value, element)
    {
        return value != 0;
    });
});
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.validate.js');
NeedLoad.push(dle_root + 'engine/car-market/javascript/messages_ru.js');
NeedLoad.push(dle_root + 'engine/car-market/javascript/colorpicker.js');
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.lightbox-0.5.js');
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.metadata.js');
JS;

switch ($auto->config['photo_upload_type']) 
{
    case 1:
    default:
        $JScript .= <<<JS
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.MultiFile.js');
$().ready(function()
{
    $('#photo').MultiFile(
    {
        max:$max_photo,
        accept:"jpeg|png|gif|jpg|jpe",
        STRING:
        {
            remove:"{$auto->lang['photo_del_upload']}",
            duplicate:"{$auto->lang['photo_selected']}",
            denied:"{$auto->lang['auto_error_type_image_upload']}"
        }
    });
});
JS;
        $set_array['{photo}'] = $template->InputFile('photo[]', 'id="photo"');
        break;
    
    case 2:
        $template->SetStyleScript(array('{THEME}/car-market/css/uploadify.css'));
        $JScript .= <<<JS
NeedLoad.push(dle_root + 'engine/car-market/javascript/swfobject.js');
NeedLoad.push(dle_root + 'engine/car-market/javascript/jquery.uploadify.v2.1.0.min.js');
$().ready(function()
{
    $("#uploads").uploadify({
        'uploader'       : dle_root + 'engine/car-market/javascript/uploadify.swf',
        'script'         : ajax_url,
        'cancelImg'      : '{THEME}/car-market/images/cancel.png',
        'folder'         : 'uploads',
        'queueID'        : 'fileQueue',
        'simUploadLimit' : 1,
        'height'         : 30,
        'width'          : 150,
        'auto'           : true,
        'buttonText'     : '',
        'fileExt'        : '*.jpeg;*.png;*.gif;*.jpg;*.jpe',
        'fileDesc'       : '*.jpeg;*.png;*.gif;*.jpg;*.jpe',
        'sizeLimit'      : $sizeLimit,
        'buttonImg'      : '{THEME}/car-market/images/upload.gif',
        'multi'          : true,
        scriptData       : {action:'photo_upload', id:'$id', model_id:$('#model_id').val(), user_id:{$auto->member['id']}, guest_session:$.cookie('guest_session')},
        onError          : function (event, queueID, fileObj, errorObj)
                           {
//                                document.getElementById('uploadsUploader').cancelFileUpload(queueID, false, false)
                                window.setTimeout("jQuery('#uploads').uploadifyCancel('" + queueID + "')", 2000);
                           },
        onComplete           : function(event, queueID, fileObj, response)
                           {
                                eval('var data = ' + response);
                                
                                if (data.status == 'error')
                                {
                                    alert(data.error);
                                    return;
                                }
                                
                                AddPhoto(data, '$id', !photos);
                                $("#images a.image").lightBox(lightBox_opt);
                                if (!photos)
                                 {
                                    $("#images").parents('tr:hidden').show();
                                 }
                                 photos++;
                                 
                                 if (allow_max_photo <= photos)
                                 {
                                     $('#uploadsUploader').hide();
                                     $('#uploads').hide().after('<span id="max_photo">{$auto->lang['max_foto']}</span>');
                                 }
                           }
    });
});
JS;
        $set_array['{photo}'] = "<div id='uploads'></div><div id=\"fileQueue\"></div>";
        break;
        
    case 3:
        $template->SetStyleScript(array('{THEME}/car-market/css/ajaxupload.3.5.css'));
        $JScript .= <<<JS
NeedLoad.push(dle_root + 'engine/car-market/javascript/ajaxupload.3.5.js');
$(function(){
        var btnUpload=$('#upload');
        var status=$('#status');
        new AjaxUpload(btnUpload, {
            action: ajax_url,
            name: 'uploadfile',
            responseType: 'json',
            data:{action:'photo_upload', id:'$id', model_id:$('#model_id').val()},
            onSubmit: function(file, ext){
                 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
                    status.text('{$auto->lang['photo_up_wrong_ext']}');
                    return false;
                }
                $('#upload').hide();
                status.text("{$auto->lang['Uploading...']}");
            },
            onComplete: function(file, data){
                if (data.status == 'error')
                {
                    status.text(data.error);
                    $('#upload').show();
                    return;
                }
                $('#upload').show();
                status.text('');
                
                AddPhoto(data, '$id', !photos);
                $("#images a.image").lightBox(lightBox_opt);
                if (!photos)
                 {
                    $("#images").parents('tr:hidden').show();
                 }
                 photos++;
                 
                 if (allow_max_photo <= photos)
                 {
                     $('#uploads').hide().after('<span id="max_photo">{$auto->lang['max_foto']}</span>');
                 }
            }
        });
        
    });
JS;
        $set_array['{photo}'] = "<div id='uploads'><div id='upload'>{$auto->lang['photo_up_button']}</div><div id=\"status\"></div></div>";
        break;
}
	
$template->load('add_auto');

if ($auto->use_country)
{
    $template->SetBlock('country');
    $set_array['{sel_country}'] = $template->Selection($auto->countries, 'country_id', $edit['country_id'], 'id="country_id"');
}
if ($auto->use_region)
{
    $template->SetBlock('region');
    $set_array['{sel_region}'] = $template->Selection($auto->regions, 'region_id', $edit['region_id'], 'id="region_id"');
}

if ($hidden_array['action'] == "doadd")
{
    if (in_array($auto->member['group'], $auto->config['user_int_allow_change_exp']))
    $template->SetBlock('count_day');

    if ($auto->config['general_allow_reg'] && !$is_logged)
    {
        $template->SetBlock('register');
        $set_array['{user_name}'] = $template->InputText('name', $edit['name'], 'id="name" validate="required:true, remote:\'' . $config['http_home_url'] . "engine/car-market/ajax.php?action=CheckLogin" . '\'"');
        $set_array['{password}'] = $template->InputPassword('password1', $edit['password1'], 'id="password1"');
        $set_array['{password_confirm}'] = $template->InputPassword('password2', $edit['password2'], 'id="password2"');
    }

    if (!in_array($member_id['user_group'], $auto->config['user_int_allow_no_code']))
    $template->SetBlock('code');

    if (!in_array($member_id['user_group'], $auto->config['user_int_allow_no_code']))
    {
        $path = parse_url($config['http_home_url']);
        $set_array['{code}'] = "<span id=\"dle-captcha\"><img src=\"".$path['path']."engine/modules/antibot.php\" alt=\"{$lang['sec_image']}\" border=\"0\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>";
        $JScript .= <<<JS
function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('dle-captcha').innerHTML = '<img src="{$path['path']}engine/modules/antibot.php?rndval=' + rndval + '" border="0" width="120" height="50"><br /><a onclick="reload(); return false;" href="#">{$lang['reload_code']}</a>';

};
JS;
    }
}

if ($hidden_array['action'] == "save")
{
    if ($edit['photo'])
    $template->SetBlock('edit');
    if (in_array($member_id['user_group'], $auto->config['user_int_allow_extend']))
    {
        $template->SetBlock('extend');
        $set_array['{count_extend}'] = $template->Selection(array('-1' => $auto->lang['no_extend']) + $auto->count_day_array, 'count_extend', '-1');
    }
}

foreach ($auto->sel_fields as $name=>$field)
{
    if ($field['values'])
    $set_array['{' . $name . '}'] = $template->Selection(array("0" => $auto->lang['no_show']) + $field['values'], $name, $edit[$name]);
}
foreach ($auto->checkbox_fields as $box_name=>$name)
{
    $set_array['{' . $box_name . '}'] = $template->InputCheckbox($box_name, 1, $edit[$box_name]);
}
$template->SetStyleScript(  array('/engine/car-market/css/jquery.lightbox-0.5.css',
								  '/engine/car-market/css/colorpicker.css'));

$template->SetForm($hidden_array, $template->main_url, 'POST', 'enctype="multipart/form-data" id="form_add"');
$template->Set($set_array);
$template->Compile('content', "<script type=\"text/javascript\">\n" . $JScript . "\n</script>");
if ($action == "add")
{
    $template->TitleSpeedBar($auto->lang['add_auto']);
}
else
{
    $template->TitleSpeedBar($auto->lang['edit_auto']);
}

$metatags['description'] = $auto->lang['meta_descr_add'];
$metatags['keywords'] = $auto->lang['meta_keys_add'];

?>