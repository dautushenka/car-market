<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!in_array($auto->member['group'], $auto->config['admin_fields']))
{
    $tpl->msg($auto->lang['access_denied'], $auto->lang['access_denied_desc'], true);
}

$select_type = empty($_REQUEST['select_type'])?'':$_REQUEST['select_type'];
$PHP_SELF .= "xfields";

require ENGINE_DIR . '/car-market/classes/FieldsAdmin.php';
$fields = new FieldsAdmin($base, $auto);
$field_types = array( 'text' => $auto->lang['xfields_text'], 
                      'select' => $auto->lang['xfields_select'], 
                      'checkbox' => $auto->lang['xfields_checkbox'], 
                      'textarea' => $auto->lang['xfields_textarea']
                      );

$edit = array(  'id'         => 0,
                'title'      => '',
                'description'=> '',
                'data'       => '',
                'regex'      => '',
                'required'   => 0,
                'default'    => '',
                'active'     => 1
                );

switch ($subaction)
{
    case 'add':
        if ($fields->AddField($select_type, $_POST['xfield']))
        {
            $tpl->msg($auto->lang['xfields'], $auto->lang['xfields_add_ok'], $PHP_SELF);$tpl->CloseTable();
        }
        else 
        {
            $edit = $_POST['xfield'];
            $hidden_array['subaction'] = 'add';
        }
        break;
        
    case 'edit':
        $auto->lang['btn_add'] = $auto->lang['btn_save'];
        $edit = $fields->GetField($id);
        $hidden_array['subaction'] = 'save';
        $hidden_array['id'] = $id;
        $select_type = $edit['type'];
        break;
        
    case 'save':
        if ($fields->UpdateField($id, $_POST['xfield']))
        {
            $tpl->msg($auto->lang['xfields'], $auto->lang['xfields_add_ok'], $PHP_SELF);$tpl->CloseTable();            
        }
        else 
        {
            $edit = $_POST['xfield'];
            $auto->lang['btn_add'] = $auto->lang['btn_save'];
            $hidden_array['subaction'] = 'save';
            $hidden_array['id'] = $id;
        }
        break;
        
    case 'del':
        $fields->DeleteField($id);
        Cache::ClearArrayCache();
        $tpl->msg($auto->lang['xfields'], $auto->lang['xfields_del_ok'], $PHP_SELF);
        break;
        
    default:
        if ($select_type)
        {
            $hidden_array['subaction'] = 'add';
        }
        break;
}

$tpl->header($auto->lang['admin_xfields'], true);

if ($fields->getErrors())
{
    $tpl->OpenTable();
    $tpl->OpenSubtable($auto->lang['error']);
    echo "  <font color=\"red\" >" . $auto->lang['isset_error'] . "</font><ol>";
    foreach ($fields->getErrors() as $error)
    {
        echo "<li>" . $error . "</li>";
    }
    echo "</ol>";
    $tpl->CloseSubtable();
    $tpl->CloseTable();
}

$tpl->OpenTable();
$tpl->OpenSubtable($auto->lang['xfields_add']);

if (!$select_type)
{
    $tpl->OpenForm('', $hidden_array);
    echo "<center>";
    
    $tpl->selection($field_types, 
                    'select_type');
    echo "&nbsp;&nbsp;";
    $tpl->InputSubmit($auto->lang['btn_next']);

    echo "</center>";
    
    $tpl->CloseForm();
    $tpl->CloseSubtable();
    $tpl->CloseTable();
    
    $fields_all = $fields->GetFields();
    
    if ($fields_all)
    {
        $tpl->OpenTable();
        $tpl->OpenSubtable($auto->lang['xfields_fields']);
        $tpl->OTable(array("ID",
                           $auto->lang['xfields_title'],
                           $auto->lang['xfields_type'],
                           $auto->lang['xfields_default'],
                           $auto->lang['xfields_required'],
                           $auto->lang['action']));
        
        foreach ($fields_all as $id => $field)
        {
            $link = $PHP_SELF . "&id={$field['id']}&subaction=";
            $tpl->row(array($field['active']?$field['id']:"<font color='red'>{$field['id']}</font>",
                            $field['title'],
                            $field_types[$field['type']],
                            $field['default'],
                            $field['required']?$auto->lang['yes']:$auto->lang['no'],
                            "[<a href='{$link}edit']'>{$auto->lang['edit']}</a>][<a href='{$link}del'>{$auto->lang['del']}</a>]"
                            ));
        }
                           
        $tpl->CTable();
        $tpl->CloseSubtable();
        $tpl->CloseTable();
    }
}
else 
{
    $hidden_array['select_type'] = $select_type;
    $tpl->OpenForm('', $hidden_array);
    $tpl->OTable()->setTableStyle(array('height="29" style="padding-left:5px;"', 'style="padding-left:5px;"'));
    $tpl->echo = FALSE;
    
    switch ($select_type)
    {
        case 'select':
            unset($edit['regex']);
            $auto->lang['xfields_data_desc'] = $auto->lang['xfields_data_select_desc'];
            $default = $tpl->selection(array(
                                             1 => $auto->lang['xfields_deault_select_1'],
                                             2 => $auto->lang['xfields_deault_select_2']
                                             ), 
                                       'xfield[default]', $edit['default']);
            break;
        
        case 'checkbox':
            unset($edit['data'], $edit['regex']);
            $default = $tpl->InputCheckbox('xfield[default]', 1, $edit['default']);
            break;
            
        case 'textarea':
            unset($edit['data']);
            $default = $tpl->TextArea('xfield[default]', $edit['default']);
            break;
            
        case 'text':
            $default = $tpl->InputText('xfield[default]', $edit['default']);
            unset($edit['data']);
            break;
    	
        default:
            $default = $tpl->InputText('xfield[default]', $edit['default']);
            break;
    }
    
    echo $tpl->row(array($auto->lang['xfields_title'], $tpl->InputText('xfield[title]', $edit['title'])), false);
    echo $tpl->row(array($auto->lang['xfields_description'], $tpl->TextArea('xfield[description]', $edit['description'], 'style="width:250px;"')), false);
    
    if (isset($edit['data']))
    {
        echo $tpl->row(array($auto->lang['xfields_data'] . $auto->lang['xfields_data_desc'], $tpl->TextArea('xfield[data]', $edit['data'], 'style="width:250px;height:100px"')), false);
    }
    
    echo $tpl->row(array($auto->lang['xfields_default'], $default), false);
    
    if (isset($edit['regex']))
    {
        echo $tpl->row(array($auto->lang['xfields_regex'], $tpl->InputText('xfield[regex]', $edit['regex'])), false);
    }
    echo $tpl->row(array($auto->lang['xfields_required'], $tpl->InputCheckbox('xfield[required]', 1, $edit['required'])), false);
    echo $tpl->row(array($auto->lang['xfields_active'], $tpl->InputCheckbox('xfield[active]', 1, $edit['active'])), false);
    
    $tpl->echo = TRUE;
    $tpl->CTable();
    $tpl->CloseSubtable($auto->lang['btn_add']);
    $tpl->CloseForm();
    $tpl->CloseTable();
}


?>