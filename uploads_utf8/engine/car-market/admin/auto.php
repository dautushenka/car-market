<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (($subaction == 'add' && !in_array($auto->member['group'], $auto->config['admin_add'])) ||
    ($subaction == 'edit' && !in_array($auto->member['group'], $auto->config['admin_edit']))
    )
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

if ($config['version_id'] >= 8.2)
{
    include(ENGINE_DIR . '/inc/include/inserttag.php');
}
else
{
    include(ENGINE_DIR . '/inc/inserttag.php');
}


include_once ENGINE_DIR.'/car-market/classes/thumb.class.php';
include_once ENGINE_DIR.'/car-market/classes/Fields.php';
include_once(DLE_CLASSES . 'parse.class.php');

$hidden_array['subaction'] = 'add';
$cur_fotos = '';
$xfields = new Fields($base, $auto);
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
			'xfields'        => '',
			'exchange'       => 0,
			'description'    => '',
			'contact_person' => '',
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
	
foreach ($other_fields_array as $field)
{
    $edit[str_replace("_array", '', $field['file'])] = 0;
}

foreach ($checkboxes_array as $column => $name)
{
    $edit[$column] = 0;
}



switch ($subaction)
{
    case "add":
        if (!empty($_POST))
        {
            if ($auto->Add($_POST))
            {
                $tpl->msg($auto->lang['auto_add'], $auto->lang['auto_add_desc'], $PHP_SELF . "auto");
            }

            $edit = $_POST;
            if (empty($edit['photo']))
            {
                $edit['photo'] = 0;
            }
        }
        break;

    case "edit":
        if (!intval($id))
        $tpl->msg($auto->lang['error'], $auto->lang['no_auto'], $PHP_SELF . "edit");

        $edit = $base->SelectOne('auto_autos', array('*'), array('id' => $id));

        foreach ($edit as $key=>&$value)
        {
            if (!$value)
            $value = '';
        }
        
        $parser = new ParseFilter();
        $edit['description'] = $parser->decodeBBCodes($edit['description'], false);

        if ($edit['exp_date'])
        $edit['exp_date'] = date("Y-m-d H:i", $edit['exp_date']);

        if ($edit['block_date'])
        $edit['block_date'] = date("Y-m-d H:i", $edit['block_date']);

        $hidden_array['subaction'] = 'save';
        $hidden_array['referal'] = $_SERVER['HTTP_REFERER'];
        $auto->lang['btn_add'] = $auto->lang['btn_save'];
        $hidden_array['id'] = $id;

        if (empty($edit['photo']))
        {
            $edit['photo'] = 0;
        }
        break;

    case "save":
        if ($auto->Save($_POST))
        {
            $tpl->msg($auto->lang['auto_edit'], $auto->lang['auto_edit_desc'], (empty($_POST['referal']))?$PHP_SELF. "edit":$_POST['referal']);
        }
        	
        $edit = $_POST;
        
        if (empty($edit['photo']))
        {
            $edit['photo'] = 0;
        }

        $hidden_array['subaction'] = 'save';
        $hidden_array['referal'] = $_POST['referal'];
        $hidden_array['id'] = $id;
        break;

    default:
        break;
}

if ($auto->use_country)
{
    $auto->GetCountries();
}
if ($auto->use_region)
{
    $auto->GetRegions($edit['country_id']);
}

$auto->GetCities($edit['country_id'], $edit['region_id']);

$auto->GetMarks();
$auto->GetModels($edit['mark_id']);

$cur_photos_jsa = '';
if ($edit['photo'])
{
    $photos = $base->Select('auto_images', array('*'), array('auto_id' => $id));
    
    $hidden_array['images'] = array();
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
        //$hidden_array['images'][$image[id]] = $image[id];
    }
    
    $cur_photos_jsa = "[" . $cur_photos_jsa . "]";
}
else 
{
    $cur_photos_jsa = " new Array()";
}

$max_photo = $auto->config['count_photo'][$auto->member['group']];
if (!$max_photo)
{
    $max_photo = 0;
}

$tpl->echo = FALSE;

$data = array(
array($auto->lang['auto_marka'], $tpl->selection($auto->marks, 'mark_id', $edit['mark_id'], "id='mark_id'")),
array($auto->lang['auto_model'], $tpl->selection($auto->models, 'model_id', $edit['model_id'], "id='model_id'")),
array($auto->lang['auto_other'], $tpl->inputText('model_other', $edit['model_other'])),
);

if ($auto->use_country)
{
    $data[] = array($auto->lang['auto_country'], $tpl->selection($auto->countries, 'country_id', $edit['country_id'], "id='country_id'"));
}
if ($auto->use_region)
{
    $data[] = array($auto->lang['auto_region'], $tpl->selection($auto->regions, 'region_id', $edit['region_id'], "id='region_id'"));
}

$data[] = array($auto->lang['auto_city'], $tpl->selection($auto->cities, 'city_id', $edit['city_id'], "id='city_id'"));
$data[] = array($auto->lang['auto_other_city'], $tpl->InputText('city_other', $edit['city_other']));
foreach ($auto->sel_fields as $name=>$values)
{
    if ($values['values'])
    {
        $data[] = array($values['name'], $tpl->selection(array("0" => $auto->lang['no_show']) + $values['values'], $name, $edit[$name]));
    }
}
$i = 0; $check_right = $check_left = '';
foreach ($auto->checkbox_fields as $box_name=>$name)
{
    if ($i%2 == 0)
    $check_right .= $tpl->InputCheckbox($box_name, 1, $edit[$box_name]) . "&nbsp;" . $name . "<br />";
    else
    $check_left .= $tpl->InputCheckbox($box_name, 1, $edit[$box_name]) . "&nbsp;" . $name . "<br />";
    $i++;
}
$data[] = array($check_left, $check_right);

foreach ($xfields->DecodeFields($edit['xfields']) as $fid => $field)
{
    if (!empty($auto->lang[$field['title']]))
    {
        $field['title'] = $auto->lang[$field['title']];
    }
    
    $data[] = array($field['title'], $field['html'], $field['description']);
}

$style_array = array('/engine/car-market/css/jquery.lightbox-0.5.css',
               '/engine/car-market/css/colorpicker.css',
               '/engine/car-market/css/ajaxupload.3.5.css');

$jscript_array = array('/engine/car-market/javascript/jquery.validate.js', 
                 '/engine/car-market/javascript/messages_ru_ansi.js', 
                 '/engine/car-market/javascript/jquery.cookie.js', 
                 '/engine/car-market/javascript/colorpicker.js', 
                 '/engine/car-market/javascript/jquery.lightbox-0.5.js', 
                 '/engine/car-market/javascript/jquery.metadata.js');

$data[] = array($auto->lang['auto_year'], $tpl->selection(array("" => $auto->lang['no_show']) + $auto->year_array, "year", $edit['year'],  ((empty($auto->config['need_field']['year']))?'':' validate="required:true"')));
$data[] = array($auto->lang['auto_color'], $tpl->InputHidden('color', $edit['color'], 'id="color"') . "&nbsp;<div id='color_div'><div></div></div>");
$data[] = array($auto->lang['auto_race'], $tpl->inputText('race', $edit['race'], 'size="10"' . ((empty($auto->config['need_field']['race']))?'':' validate="required:true"')). "&nbsp;" . $auto->lang['race_unit']);
$data[] = array($auto->lang['auto_power'], $tpl->inputText('power', $edit['power'], 'size="10"' . ((empty($auto->config['need_field']['power']))?'':' validate="required:true"')). "&nbsp;" . $auto->lang['power_unit']);
$data[] = array($auto->lang['auto_capacity_motor'], $tpl->inputText('capacity_motor', $edit['capacity_motor'], 'size="10"' . ((empty($auto->config['need_field']['capacity_motor']))?'':' validate="required:true"')). "&nbsp;" . $auto->lang['capacity_unit']);
$data[] = array($auto->lang['auto_cost'], 
                $tpl->inputText('cost', $edit['cost'], 'size="10"'). "&nbsp;" . 
                    $tpl->selection($auto->currency_array, 'currency', $edit['currency']) . "&nbsp;" . 
                    $tpl->InputCheckbox('auction', 1, $edit['auction']) . "&nbsp;" .$auto->lang['auto_auction'] . "&nbsp;" . 
                    $tpl->InputCheckbox('exchange', 1, $edit['exchange']) . "&nbsp;" .$auto->lang['auto_exchange']);
$data[] = array($auto->lang['auto_desc'], $bb_code . "<textarea rows=\"13\" style=\"width:98%; padding:0px;\" onclick=\"setFieldName(this.name)\" name=\"description\" id=\"description\">{$edit['description']}</textarea>" , "");
$data[] = array($auto->lang['auto_contact_person'], $tpl->inputText('contact_person', $edit['contact_person'], 'size="55"' . ((empty($auto->config['need_field']['contact_person']))?'':' validate="required:true"')));
$data[] = array($auto->lang['auto_email'], $tpl->inputText('email', $edit['email'], 'size="55"' . ((empty($auto->config['need_field']['email']))?'':' validate="required:true, email:true"')) );
$data[] = array($auto->lang['auto_phone'], $tpl->inputText('phone', $edit['phone'], 'size="55"' . ((empty($auto->config['need_field']['phone']))?'':' validate="required:true"')));
$data['style="display:none;"'] = array($auto->lang['auto_cur_foto'], '<table id="images"></table>');
//$data[] = array($auto->lang['auto_new_foto'], "<div>" . $tpl->inputFile('photo[]', '', 'size="41" id="photo"') , $auto->lang['auto_new_foto_desc']);
//$data[] = array($auto->lang['auto_new_foto'], "<div id='uploads'></div>");

switch ($auto->config['photo_upload_type']) 
{
    case 1:
    default:
        $jscript_array[] = '/engine/car-market/javascript/jquery.MultiFile.js';
        $upload_js = <<<JS
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
        $data[] = array($auto->lang['auto_new_foto'], $tpl->InputFile('photo[]', 'id="photo"'));
        break;
    
    case 2:
        $sizeLimit = 1024 * $auto->config['photo_size_byte'];
        $jscript_array[] = '/engine/car-market/javascript/swfobject.js';
        $jscript_array[] = '/engine/car-market/javascript/jquery.uploadify.v2.1.0.min.js';
        $style_array[] = '/engine/car-market/css/uploadify.css';
        $upload_js = <<<JS
$().ready(function()
{
    $("#uploads").uploadify({
        'uploader'       : dle_root + 'engine/car-market/javascript/uploadify.swf',
        'script'         : ajax_url,
        'cancelImg'      : '{THEME}/car-market/images/cancel.png',
        'folder'         : 'uploads',
        'queueID'        : 'fileQueue',
        'simUploadLimit' : 1,
        'auto'           : true,
        'height'         : 30,
        'width'          : 150,
        'buttonText'     : '',
        'fileExt'        : '*.jpeg;*.png;*.gif;*.jpg;*.jpe',
        'fileDesc'       : '*.jpeg;*.png;*.gif;*.jpg;*.jpe',
        'sizeLimit'      : $sizeLimit,
        'buttonImg'      : dle_root + "engine/car-market/images/upload.gif",
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
        $data[] = array($auto->lang['auto_new_foto'], "<div id='uploads'></div><div id=\"fileQueue\"></div>");
        break;
        
    case 3:
        $jscript_array[] = '/engine/car-market/javascript/ajaxupload.3.5.js';
        $style_array[] = '/engine/car-market/css/ajaxupload.3.5.css';
        $upload_js = <<<JS
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
                $('#upload').show();
                if (data.status == 'error')
                {
                    status.text(data.error);
                    return;
                }
                
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
        $data[] = array($auto->lang['auto_new_foto'], "<div id='uploads'><div id='upload'>{$auto->lang['photo_up_button']}</div><div id=\"status\"></div></div>");
        break;
}

$data[] = array($auto->lang['auto_exp_date'], $tpl->inputText('exp_date', $edit['exp_date'], 'size="20" id="exp_date"') . $tpl->calendar('exp_date'), $auto->lang['exp_date_help']);
$data[] = array($auto->lang['auto_allow_block'], $tpl->InputCheckbox('allow_block', 1, $edit['allow_block']) . "&nbsp;" . $tpl->InputText('block_date', $edit['block_date'], 'size="20" id="allow_block"') . $tpl->calendar('allow_block'), $auto->lang['block_date_help']);
$data[] = array($auto->lang['allow_site'], $tpl->InputCheckbox('allow_site', 1, $edit['allow_site']));

$JScript = <<<JS
<script type="text/javascript">
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
			}
		});
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
	}
	catch(e) { }
	
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
        $("#images").parents('tr:hidden').show();
    }
    
    if (allow_max_photo <= photos)
    {
        $('#uploads').hide().after('<span id="max_photo">{$auto->lang['max_foto']}</span>');
    }
    
	$("#images a.image").lightBox(lightBox_opt);
	
	try
	{
	
	   $upload_js

    }
    catch(e)
    {
        alert(e.message);
    }
	
});
</script>
JS;


$tpl->echo = TRUE;
$tpl->header($auto->lang['auto_add'], true, $JScript, $style_array, $jscript_array);

if ($auto->Errors)
{
    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['error']);
    echo "  <font color=\"red\" >" . $auto->lang['isset_error'] . "</font><ol>";
    foreach ($auto->Errors as $error)
    {
        echo "<li>" . $error . "</li>";
    }
    echo "</ol>";
    $tpl->CloseSubtable();
    $tpl->CloseTable();
}

$tpl->OpenTable();
$tpl->OpenSubtable($auto->lang['auto_add']);
$tpl->OpenForm('', $hidden_array, 'enctype="multipart/form-data" id="form_add"');
$tpl->OTable();

foreach ($data as $row_style => $row)
{
    if (!empty($row[2]))
    {
        $hit = "&nbsp;<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('{$row[2]}', this, event, '320px')\">[?]</a>";
    }
    else 
    {
        $hit = "";
    }

    echo <<<HTML
   <tr $row_style>
       <td height="29" style="padding-left:5px;">{$row[0]}</td>
       <td>{$row[1]}$hit</td>
   </tr>
HTML;
}

$tpl->CTable();
$tpl->CloseSubtable($auto->lang['btn_add']);
$tpl->CloseForm();
$tpl->CloseTable();

?>