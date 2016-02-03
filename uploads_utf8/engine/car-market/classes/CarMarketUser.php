<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

require(ENGINE_DIR . "/car-market/classes/CarMarket.php");

final class CarMarketUser extends CarMarket
{
    public $guest_session = '';

    protected $module_name_url = '';

    /**
     * Delegate template
     *
     * @var TemplateUser
     */
    public $tpl = null;
    
    /**
     * 
     * @var Fields
     */
    public $xfields;

    public function __construct(&$base, &$car_conf, &$lang_car, $member, $other_fields_array, $checkboxes_array)
    {
        parent::__construct($base, $car_conf, $lang_car, $other_fields_array, $checkboxes_array);

        $this->member['id']    = $member['user_id'];
        $this->member['name']  = $member['name'];
        $this->member['group'] = $member['user_group'];
        $this->member['ip']    = $this->base->EscapeString($_SERVER['REMOTE_ADDR']);

        if (!$this->member['id'])
        {
            $this->guest_session = $this->GetGuestSession();
        }
        	
        if ($this->config['general_main_page'])
        {
            $this->config['general_name_module'] = '';
        }
        else
        {
            if ($this->config['general_mod_rewrite'])
            {
                $this->module_name_url = $this->config['general_name_module'];
            }
            else
            {
                $this->module_name_url = "do=" . $this->config['general_name_module'];
            }
        }
    }

    public function PreparationSearchArray()
    {
        $this->search_array['sort'] = $this->config['user_int_default_sort'];
        $this->search_array['subsort'] = $this->config['user_int_default_subsort'];

        // addition paran in search block
        if ($GLOBALS['action'] == 'auto' && !empty($_SERVER['HTTP_REFERER']))
        {
            $this->search_array = array_merge($this->search_array, UrlParse(empty($_COOKIE['auto_settings'])?'':$_COOKIE['auto_settings']), array_intersect_key(UrlParse($_SERVER['HTTP_REFERER']), $this->search_array));
        }
        else 
        {
            $this->search_array = array_merge($this->search_array, UrlParse(empty($_COOKIE['auto_settings'])?'':$_COOKIE['auto_settings']), array_intersect_key($_GET, $this->search_array));
        }
        
        if ($this->use_country && empty($this->search_array['country_id']))
        {
            $this->search_array['region_id'] = 0;
            $this->search_array['city_id'] = 0;
        }
        elseif ($this->use_region && !$this->search_array['region_id'])
        {
            $this->search_array['city_id'] = 0;
        }
        	
        if (empty($this->search_array['mark_id']))
        {
            $this->search_array['model_id'] = 0;
        }
    }

    public function Add($values_array)
    {
        if (!in_array($this->member['group'], $this->config['user_int_allow_no_code']))
        {
            $sec_code_session = ($_SESSION['sec_code_session'] != '') ? $_SESSION['sec_code_session'] : false;
            $_SESSION['sec_code_session'] = false;
            	
            if ( $_POST['sec_code'] != $sec_code_session OR !$sec_code_session) $this->Errors[] = $this->lang['error_code'];
        }
        if (!$GLOBALS['is_logged'] && $this->config['general_allow_reg'])
        {
            $parse = new ParseFilter(Array(), Array(), 1, 1);
            	
            $password1 = $this->base->EscapeString($parse->process($_POST['password1']));
            $password2 = $this->base->EscapeString($parse->process($_POST['password2']));
            $name = $this->base->EscapeString($parse->process(htmlspecialchars(trim($_POST['name']))));
            $email = $this->base->EscapeString($parse->process(htmlspecialchars(trim($_POST['email']))));

            $member_id = $GLOBALS['db']->super_query("SELECT * FROM " . USERPREFIX . "_users where name='$name' and password='".md5(md5($password1))."'");
            	
            if ($member_id)
            {
                $this->LoginIn($password1);
                $GLOBALS['member_id']  = $member_id;
                $this->member['id']    = $member_id['user_id'];
                $this->member['name']  = $member_id['name'];
                $this->member['group'] = $member_id['user_group'];
                $this->member['ip']    = $member_id['logged_ip'];
                $this->guest_session = '';
            }
            else
            auto_check_reg ($name, $email, $password1, $password2);
        }

        $this->values = $values_array;

        $this->CheckError();

        require_once ENGINE_DIR . '/car-market/classes/Fields.php';
        
        $xfields = new Fields($this->base, $this);
        
        $this->values['xfields'] = $xfields->EncodeFields($this->values);
        
        $this->Errors = $this->Errors + $xfields->getErrors();
        
        if ($this->Errors)
        {
            return false;
        }
        	
        if (!$GLOBALS['is_logged'] && $this->config['general_allow_reg'])
        {
            if (intval($GLOBALS['config']['reg_group']) < 3) $GLOBALS['config']['reg_group'] = 4;
            	
            $regpassword = md5(md5($password1));

            $GLOBALS['db']->query("INSERT INTO " . USERPREFIX . "_users (name, password, email, reg_date, lastdate, user_group, info, signature, favorites, xfields, logged_ip) VALUES ('$name', '$regpassword', '$email', '{$this->base->timer->cur_time}', '{$this->base->timer->cur_time}', '" . $GLOBALS['config']['reg_group'] . "', '', '', '', '', '" . $this->member['ip'] . "')");

            $this->member['id']    = $GLOBALS['db']->insert_id();
            $this->member['name']  = $name;
            $this->member['group'] = $GLOBALS['config']['reg_group'];
            $guest_session = $this->guest_session;
            $this->guest_session = '';

            $this->LoginIn($password1);
        }
        
//        if ($this->values['capacity_motor'] > 1000) 
//        { 
//            $this->values['capacity_motor'] = $this->values['capacity_motor']/1000; 
//        } 

        $this->PreparationValues();
        
        if (in_array($this->member['group'], $this->config['user_int_allow_change_exp']))
        {
            if ($this->values['count_day'])
            $this->values['exp_date'] = $this->base->timer->cur_time + (int)$this->values['count_day'] * 24*60*60;
            else
            $this->values['exp_date'] = 0;
        }
        elseif ($this->config['user_int_default_day_count'])
        {
            $this->values['exp_date'] = $this->base->timer->cur_time + (int)$this->config['user_int_default_day_count'] * 24*60*60;
        }
        else
        {
            $this->values['exp_date'] = 0;
        }

        if (in_array($this->member['group'], $this->config['user_int_allow_no_moder']))
        $this->values['allow_site'] = 1;

        $this->values['guest_session'] = $this->guest_session;
        $this->values['add_date'] = $this->values['update_date'] = $this->base->timer->cur_time;
        $this->values['author'] = $this->member['name'];
        $this->values['author_id'] = $this->member['id'];
        $this->values['author_ip'] = $this->member['ip'];

        $id = $this->base->Insert('auto_autos', $this->values);

        if (!empty($this->values['images']))
        {
            $this->values['images'] = array_slice($this->values['images'], 0, $this->config['count_photo'][$this->member['group']]);
            
            if ($this->values['images'])
            {
                $this->base->SetWhere('id', $this->values['images'], 'IN', 'auto_images');
                if ($this->member['id'])
                {
                    $this->base->Update('auto_images', array('auto_id' => $id), array('auto_id' => 0, 'user_id' => $this->member['id']));
                }
                else if (!empty($guest_session))
                {
                    $this->base->Update('auto_images', array('auto_id' => $id, 'user_id' => $this->member['id'], 'guest_session' => ''), array('auto_id' => 0, 'guest_session' => $guest_session));
                }
                else 
                {
                    $this->base->Update('auto_images', array('auto_id' => $id), array('auto_id' => 0, 'guest_session' => $this->guest_session));
                }
                
                if (!(int)$this->values['main_photo'])
                {
                    $this->values['main_photo'] = reset($this->values['images']);
                }
                
                $this->base->Update('auto_autos', array('photo' => $this->values['main_photo'], 'photo_count' => count($this->values['images'])), array('id' => $id));
            }
        }
        
        if (!empty($_FILES['photo']['name'][0]) && $this->UploadPhoto($id))
        $this->base->Update('auto_autos', array('photo' => $this->values['photo'], 'photo_count' => $this->values['photo_count']), array('id' => $id));
        	
        if (!empty($this->values['allow_site']))
        {
            $this->IncrementCounter($this->values['mark_id'], $this->values['model_id']);
            Cache::ClearAllCache();
        }
        	
        if ($this->config['general_inform'] && $this->config['general_email'])
        {
            if ($data = @file_get_contents(ENGINE_DIR . "/car-market/mail.txt"))
            {
                include_once DLE_CLASSES.'mail.class.php';
                $mail = new dle_mail ($GLOBALS['config']);

                $mail->from = $GLOBALS['config']['admin_mail'];
                $data = str_replace("{%site_url%}", $GLOBALS['config']['http_home_url'], $data);
                $data = str_replace("{%auto_link%}", $this->tpl->GetUrl(array("action" => 'auto',  "id" => $id)), $data);
                	
                $mail->send ($this->config['general_email'], $this->lang['mail_subj'], $data);
            }
        }
        
        return $id;
    }

    public function SaveUser(&$values, $id)
    {
        if (!($edit = $this->base->SelectOne('auto_autos', array('*'), array('id' => $id))))
        {
            $this->Errors[] = $this->lang['auto_not_found'];
            return ;
        }

        if (!MODER &&
        (
        ($edit['author_id'] && $edit['author_id'] != $this->member['id']) ||
        ($edit['guest_session'] && $edit['guest_session'] != $this->guest_session) ||
        !in_array($this->member['group'], $this->config['user_int_allow_edit'])
        )
        )
        {
            $this->Errors[] = $this->lang['no_allow_edit_auto'];
            return ;
        }

        $values['allow_site']  = $edit['allow_site'];
        $values['allow_block'] = $edit['allow_block'];
        if($edit['block_date'])
        {
            $values['block_date']  = date("Y-m-d H:i", $edit['block_date']);
        }

        if (in_array($this->member['group'], $this->config['user_int_allow_extend']) && $values['count_extend'] != -1)
        {
            if (!$values['count_extend'])
            {
                $values['exp_date'] = '';
            }
            elseif ($edit['exp_date'])
            {
                $values['exp_date'] = date("Y-m-d H:i", $edit['exp_date'] + $values['count_extend'] * 24*60*60);
            }
            else
            {
                $values['exp_date'] = date("Y-m-d H:i", $this->base->timer->cur_time + $values['count_extend'] * 24*60*60);
            }
        }
        else if ($edit['exp_date'])
        {
            $values['exp_date'] = date("Y-m-d H:i", $edit['exp_date']);
        }

        if (!empty($this->values['add_date']))
        {
            unset($this->values['add_date']);
        }

        return $this->Save($values, $edit);
    }

    public function DelAuto(array $autos_id)
    {
        	
        foreach ($autos_id as $id)
        {
            $this->old_values = $this->base->SelectOne('auto_autos', array('allow_site', "model_id", "mark_id", "photo", "author_id", "guest_session"), array("id"=>$id));
            	
            if ((in_array($this->member['group'], $this->config['user_int_allow_del']) &&
            (
            ($this->member['id'] && $this->member['id'] == $this->old_values['author_id']) ||
            ($this->guest_session && $this->guest_session == $this->old_values['guest_session'])
            )
            ) || MODER)
            {
                if (!$this->old_values)
                return false;
                	
                if ($this->old_values['allow_site'])
                {
                    $this->DecrementCounter($this->old_values['mark_id'], $this->old_values['model_id']);
                    Cache::ClearAllCache();
                }

                if ($this->old_values['photo'])
                $this->DelPhotos($id);
                	
                $this->base->Delete('auto_autos', array("id" => $id));
            }
        }

        return true;
    }

    public function Search(array $options = array(), array $id = array())
    {
        $this->autos = $count = $order = array();
        $this->autos_count = 0;
        $default_options = array(
								"count"      => 10,
								"page"       => empty($_REQUEST['page'])?1:$_REQUEST['page'],
								"get_count"  => 1,
								"show_status"  => 0,
        );

        $options = array_merge($default_options, $options);

        $join_table = array("auto_marks"  => array('id' => 'mark_id'),
							"auto_models" => array('id' => 'model_id'),
							"auto_cities"   => array('id' => "city_id")
        );

        $selection = array(
							'auto_images' => array('*', 'photo_id' => "id", "photo_model_id" => "model_id"),
							'auto_autos'  => array('*'),
							'auto_models' => array('model_name' => 'name'),
							'auto_marks'  => array('mark_name' => 'name'),
							"auto_cities" => array('city_name' => "name")
        );
        	
        if ($id)
        {
            $join_table["auto_images"] = array('auto_id' => 'id');
        }
        else
        {
            $join_table["auto_images"] = array('id' => 'photo');
        }

        if ($this->use_country)
        {
            $join_table["auto_countries"] = array('id' => 'country_id');
            $selection["auto_countries"] = array('country_name' => 'name');
        }

        if ($this->use_region)
        {
            $join_table["auto_regions"] = array('id' => 'region_id');
            $selection["auto_regions"] = array('region_name' => 'name');
        }

        if (!empty($this->search_array['subsort']) && !in_array(strtoupper($this->search_array['subsort']), array("ASC", "DESC")))
        {
            $this->search_array['subsort'] = "ASC";
        }

        if (!empty($this->search_array['sort']))
        {
            if ($this->search_array['sort'] == "date")
            {
                $order = array("add_date" => $this->search_array['subsort']);
            }
            elseif ($this->search_array['sort'] == "cost")
            {
                $order = array("cost_search" => $this->search_array['subsort']);
            }
            else
            {
                $order = array($this->search_array['sort'] => $this->search_array['subsort']);
            }
        }

        $this->base->BuildQuery('auto_autos', $join_table);

        $this->base->SetSelection($selection);

        if (MODER)
        {
            switch ($this->config['general_show_moder'])
            {
                case 1:
                    break;
                    	
                case 2:
                    $this->base->SetBeginBlockWhere();
                    $this->base->SetWhere('exp_date', $this->base->timer->cur_time, ">");
                    $this->base->SetWhere('exp_date', 0, "=", '', 'OR');
                    $this->base->SetEndBlockWhere();
                    $this->base->SetWhere('allow_site', 0, "=");
                    break;
                    	
                case 3:
                    $this->base->SetWhere('allow_site', 0, "=");
                    break;
                    	
                case 4:
                    $this->base->SetWhere('exp_date', $this->base->timer->cur_time, "<");
                    break;
                    	
                case 0:
                default:
                    $this->base->SetBeginBlockWhere();
                    $this->base->SetWhere('exp_date', $this->base->timer->cur_time, ">");
                    $this->base->SetWhere('exp_date', 0, "=", '', "OR");
                    $this->base->SetEndBlockWhere();
                    break;
            }
        }
        else
        {
            if (!$options['show_status'])
            {
                $this->base->SetWhere('allow_site', 1, "=");
            }
            $this->base->SetBeginBlockWhere();
            $this->base->SetWhere('exp_date', 0, "=");
            $this->base->SetWhere('exp_date', $this->base->timer->cur_time, ">", "auto_autos", "OR");
            $this->base->SetEndBlockWhere();
        }

        if ($id)
        {
            $this->base->SetWhere('id', $id, "IN");
        }
        else
        {
            if (intval($options['count']) <= 0)
            {
                $count['limit'] = 10;
            }
            else
            {
                $count['limit'] = intval($options['count']);
            }
            	
            if ((int)$options['page'] > 0)
            {
                $count['start'] = ((int)$options['page'] - 1) * $count['limit'];
            }
            else
            {
                $count['start'] = 0;
            }

            $this->PreparationSearch();

            if (!empty($this->search_array['search_count_day']) && intval($this->search_array['search_count_day']))
            {
                $this->base->SetWhere('add_date', $this->base->timer->cur_time - $this->search_array['search_count_day']*24*60*60, ">=");
            }
            	
            if (!empty($this->search_array['allow_block']))
            {
                $this->base->SetWhere("allow_block", 1, '=');
                $this->base->SetBeginBlockWhere();
                $this->base->SetWhere("block_date", '0', "=");
                $this->base->SetWhere("block_date", $this->base->timer->cur_time, ">", '', 'OR');
                $this->base->SetEndBlockWhere();
            }
        }

        $this->base->ExecuteBuildQuery($order, $count, $options);

        while ($row = $this->base->FetchArray())
        {
            if (empty($this->autos[$row['id']]))
            {
                $this->autos[$row['id']] = $row;
            }

            $this->autos[$row['id']]['photos'][] = array(
														"id" => $row['photo_id'],
														"model_id" => $row['photo_model_id'],
														"image_name" => $row['image_name'],
            );
        }

        if ($options['get_count'])
        {
            $this->autos_count = $this->base->CountForBuldQuery();
        }

    }

    public function ShowAuto($id, array $options = array())
    {
        $default_options = array(
						"show_photo" => 1,
						"show_edit"  => 1,
        );

        $options = array_merge($default_options, $options);

        if ($this->use_country)
        {
            $set_array["{country}"] = $this->autos[$id]['country_name'];
            $this->tpl->SetBlock('country');
        }

        if ($this->use_region)
        {
            $set_array["{region}"] = $this->autos[$id]['region_name'];
            $this->tpl->SetBlock('region');
        }

        if ($this->autos[$id]['city_other'])
        {
            $set_array["{city}"] = $this->autos[$id]['city_other'];
        }
        else
        {
            $set_array["{city}"] = $this->autos[$id]['city_name'];
        }
        	
        $set_array["{mark}"] = $this->autos[$id]['mark_name'];

        if ($this->autos[$id]['model_other'])
        {
            $set_array["{model}"] = $this->autos[$id]['model_other'];
        }
        else
        {
            $set_array["{model}"] = $this->autos[$id]['model_name'];
        }

        foreach ($this->sel_fields as $name=>$value)
        {
            if ($this->autos[$id][$name])
            {
                $set_array['{' . $name . '}'] = $value['values'][$this->autos[$id][$name]];
                $this->tpl->SetBlock($name);
            }
            else
            $set_array['{' . $name . '}'] = $this->lang['spacer'];
        }

        foreach ($this->checkbox_fields as $name=>$value)
        {
            if ($this->autos[$id][$name])
            {
                $set_array['{' . $name . '}'] = $this->lang['field_isset'];
                $this->tpl->SetBlock($name);
            }
            else
            $set_array['{' . $name . '}'] = $this->lang['field_not_isset'];
        }

        if ($this->autos[$id]['color'])
        {
            $set_array["{color}"] = $this->autos[$id]['color'];
            $this->tpl->SetBlock('color');
        }

        $set_array["{race}"] = $this->autos[$id]['race'];
        if ($this->autos[$id]['race'])
        $this->tpl->SetBlock('race');
        	
        $fields = array("power",
					   "capacity_motor",
					   "contact_person",
					   "phone",
					   "year",
					   "description",
					   "photo_count",
        );
        	
        foreach ($fields as $field)
        {
            if ($this->autos[$id][$field])
            {
                $set_array["{" . $field . "}"] = $this->autos[$id][$field];
                $this->tpl->SetBlock($field);
            }
            else
            $set_array["{" . $field . "}"] = $this->lang['spacer'];
        }

        if (!$this->autos[$id]['cost'])
        {
            $set_array["{cost}"] = $this->lang['cost_dog'];
        }
        else
        {
            $set_array["{cost}"] = auto_num_format($this->autos[$id]['cost']) . " " . $this->lang[$this->autos[$id]['currency']];
            	
            if ($this->search_array['currency_defalut'] && $this->search_array['currency_defalut'] != $this->autos[$id]['currency'])
            $set_array["{cost}"] .= " (" . auto_num_format($this->autos[$id]['cost_search'] * $this->config['currency'][$this->search_array['currency_defalut']]) . " " . $this->lang[$this->search_array['currency_defalut']] . ")";
            
            if ($this->autos[$id]['auction'])
            {
                $this->tpl->SetBlock('auction');
            }
        }
        
        if ($this->autos[$id]['exchange'])
        {
            $this->tpl->SetBlock('exchange');
        }
        	
        $onclick = "OnClick=\"favorites(this, $id);return false;\"";
        if (!empty($_COOKIE['auto_favorites']) && preg_match("#(^|\D)$id(\D|$)#i", $_COOKIE['auto_favorites']))
        {
            $set_array['{favorites}'] = "<img $onclick class=\"favorites\" src=\"{THEME}/car-market/images/minus.gif\" title=\"{$this->lang['favorites_title']}\" />";
        }
        else
        {
            $set_array['{favorites}'] = "<img $onclick class=\"favorites\" src=\"{THEME}/car-market/images/plus.gif\" title=\"{$this->lang['favorites_title']}\" />";
        }
        	
        $set_array["{view_count}"] = $this->autos[$id]['view_count'];
        $set_array["{add_date}"] = $this->DateFormat($this->autos[$id]['add_date']);
        $set_array["{id}"] = $id;
        $set_array["{author}"] = $this->autos[$id]['author'];
        $set_array["{cars_of_author}"] = $this->tpl->GetUrl(array('author' => urlencode($this->autos[$id]['author'])), array(), array(), array(), array("use_alt_url" => false, "clear" => 1));
        $set_array["{auto_url}"] = $this->tpl->GetUrl(array("action" => 'auto',  "id" => $id));
        $set_array['{checkbox}'] = $this->tpl->InputCheckbox('selected_auto[]', $id);
        $set_array['{compare}']  = "<img src=\"{THEME}/car-market/images/compare_unchecked.gif\" class=\"compare\" id=\"$id\" title=\"{$this->lang['compare_title']}\" />";
        $set_array['{send_mail}']  = "<a href=\"{$set_array['{auto_url}']}\" title=\"{$this->lang['mail_title']}\" class=\"mail_friend\" ><img src=\"{THEME}/car-market/images/mail.gif\" /></a>";
        $set_array['[print]'] = "<a target=\"_blank\" class=\"print-link\" href=\"" . $this->GetPrintUrl($id) . "\" title=\"{$this->lang['print_auto']}\" >";
        $set_array['[/print]'] = "</a>";
        
        if ($this->config['general_mod_rewrite'])
        {
            $set_array["{author_url}"] = $GLOBALS['config']['http_home_url'] . "/user/" . urlencode($this->autos[$id]['author']) . "/";
        }
        else
        {
            $set_array["{author_url}"] = $GLOBALS['PHP_SELF'] . "?subaction=userinfo&user=" . urlencode($this->autos[$id]['author']) . "/";
        }
        
        if ($this->autos[$id]['email'])
        {
            $set_array['[email]'] = "<a href=\"{$set_array['{auto_url}']}\" title=\"{$this->lang['auto_email_author']}\" OnClick=\"auto_email_send('$id');return false;\" >";
            $set_array['[/email]'] = "</a>";
        }

        if ($this->autos[$id]['photo'])
        $set_array["{isset_photo}"] = $this->lang['isset_photo'];

        if ($options['show_photo'] && !empty($this->autos[$id]['photos'][0]['id']))
        {
            $this->tpl->SetBlock('exist_photo');
            $this->tpl->OpenRow('row_photo');
            $i = 1;
            foreach ($this->autos[$id]['photos'] as $photo)
            {
                if (file_exists(UPLOAD_DIR . $photo['model_id'] . "/" . $photo['image_name']))
                {
                    $photo_one = "<img src=\"" . UPLOAD_URL . $photo['model_id'] . "/thumbs/{$photo['image_name']}\" alt=\"{$this->autos[$id]['mark_name']} {$this->autos[$id]['model_name']} photo $i\" >";
                    	
                    $set_array["{photo_$i}"] = "<a class=\"go_big_photo\" href=\"" . UPLOAD_URL . $photo['model_id'] . "/{$photo['image_name']}\" title=\"{$this->autos[$id]['mark_name']} {$this->autos[$id]['model_name']}\" >" . $photo_one . "</a>";
                    $this->tpl->SetRow(array("{photo}" => "<a class=\"lightbox\" href=\"" . UPLOAD_URL . $photo['model_id'] . "/{$photo['image_name']}\" >" . $photo_one . "</a>"), 'row_photo');
                    	
                    if ($photo['id'] == $this->autos[$id]['photo'])
                    {
                        $set_array["{big_photo}"] = "<span id=\"big_photo\"><img src=\"" . UPLOAD_URL . $photo['model_id'] . "/{$photo['image_name']}\" alt=\"{$this->autos[$id]['mark_name']} {$this->autos[$id]['model_name']} photo $i\" ></span>";
                        $set_array["{photo}"] = "<a class=\"go_big_photo\" href=\"" . UPLOAD_URL . $photo['model_id'] . "/{$photo['image_name']}\" title=\"{$this->autos[$id]['mark_name']} {$this->autos[$id]['model_name']}\" >" . $photo_one . "</a>";
                    }
                    	
                    $i++;
                }
            }
            $this->tpl->CloseRow('row_photo');
        }
        elseif (empty($this->autos[$id]['photos'][0]['id']))
        $set_array["{photo}"] =  "<img src=\"{THEME}/car-market/images/no_photo.jpg\" alt=\"{$this->autos[$id]['mark_name']} {$this->autos[$id]['model_name']}\" >";

        if (MODER)
        {
            if (!$this->autos[$id]['allow_site'] && (($this->autos[$id]['exp_date'] > $this->base->timer->cur_time) || !$this->autos[$id]['exp_date']))
            {
                $set_array['{moder_class}'] = 'moder_new_auto id' . $id;
            }
            elseif (!$this->autos[$id]['allow_site'] && $this->autos[$id]['exp_date'] < $this->base->timer->cur_time)
            {
                $set_array['{moder_class}'] = 'moder_old_auto id' . $id;
            }
            else
            {
                $set_array['{moder_class}'] = 'id' . $id;
            }
        }
        else
        $set_array['{moder_class}'] = 'id' . $id;

        if ($options['show_edit'])
        {
            if (MODER)
            {
                $this->tpl->SetBlock('moder')->SetBlock('allow_del');
                $set_array["[edit]"] = "<a id=\"link_admin-$id\" onClick=\"dropdownmenu(this, event, ShowMenu('$id', '" . (($this->autos[$id]['allow_site'])?0:1) . "'), '170px');return false;\" target=\"_blank\" href=\"" . $GLOBALS['config']['http_home_url'] . "?do=" . $this->config['general_name_module'] . "&action=edit&id=$id\">";
                $set_array["[/edit]"] = "</a>";

                $info = '';

                if ($this->autos[$id]['exp_date'])
                $info .= $this->lang['exp_date_site_admin'] . " " . date("d.m.Y H:i", $this->autos[$id]['exp_date']) .";";
                	
                if ($this->autos[$id]['allow_block'])
                {
                    if ($this->autos[$id]['block_date'])
                    $info .= " " . $this->lang['block_date_site_admin'] . " " . date("d.m.Y H:i", $this->autos[$id]['block_date']);
                    else
                    $info .= " " . $this->lang['block_date_site_admin'] . " " . $this->lang['block_date_unlim'];
                }

                if ($info)
                $set_array["{info}"] = $info;
            }
            else
            {
                if (in_array($this->member['group'], $this->config['user_int_allow_edit']) &&
                (
                ($this->member['id'] && $this->member['id'] == $this->autos[$id]['author_id']) ||
                ($this->guest_session && $this->guest_session == $this->autos[$id]['guest_session'])
                )
                )
                {
                    $set_array["[edit]"] = "<a target=\"_blank\" id=\"link_admin-$id\" href=\"" . $GLOBALS['config']['http_home_url'] . "?do=" . $this->config['general_name_module'] . "&action=edit&id=$id\">";
                    $set_array["[/edit]"] = "</a>";
                }

                if (in_array($this->member['group'], $this->config['user_int_allow_del']) &&
                (
                ($this->member['id'] && $this->member['id'] == $this->autos[$id]['author_id']) ||
                ($this->guest_session && $this->guest_session == $this->autos[$id]['guest_session'])
                )
                )
                {
                    $this->tpl->SetBlock('allow_del');
                }
            }
        }
        
        $set_array['{xfields}'] = '';
        foreach ($this->xfields->showFields($this->autos[$id]['xfields']) as $fid => $field)
        {
            if ($field['value'])
            {
                $set_array["{xfield_{$fid}_title}"] = $field['title'];
                $set_array["{xfield_{$fid}_descr}"] = $field['description'];
                $set_array["{xfield_{$fid}_value}"] = $field['value'];
                
                $set_array['{xfields}'] .= $field['title'] . ": " . $field['value'] . "<br />";
                
                $this->tpl->SetBlock('xfield_' . $fid);
            }
        }

        if ($this->autos[$id]['allow_site'])
        {
            $set_array['{status}'] = $this->lang['status_on_site'];
        }
        elseif (!$this->autos[$id]['exp_date'] || $this->autos[$id]['exp_date'] > $this->base->timer->cur_time)
        {
            $set_array['{status}'] = $this->lang['status_on_moder'];
        }
        else
        {
            $set_array['{status}'] = $this->lang['status_old'];
        }

        return $set_array;
    }

    public function ShowSearch(&$JS = '')
    {
        $show_count = array_reverse(array(
										"20" => $this->lang['show_count_20'], 
										"10" => $this->lang['show_count_10'], 
										"5"  => $this->lang['show_count_5'], 
										"3"  => $this->lang['show_count_3'], 
										"2"  => $this->lang['show_count_2'], 
										"1"  => $this->lang['show_count_1'], 
										"0"  => $this->lang['show_count_all']
        ), true);

        if ($this->use_country)
        {
            $this->tpl->Set($this->tpl->Selection($this->countries, 'country_id', $this->search_array['country_id'], 'id="country_id_search"'), "{country}");
            $this->tpl->SetBlock('country');
        }
        if ($this->use_region)
        {
            $this->tpl->Set($this->tpl->Selection($this->regions, 'region_id', $this->search_array['region_id'], 'id="region_id_search"'), "{region}");
            $this->tpl->SetBlock('region');
        }
        $this->tpl->Set($this->tpl->Selection($this->cities, 'city_id', $this->search_array['city_id'], 'id="city_id_search"'), "{city}");
        $this->tpl->Set($this->tpl->Selection($this->marks, 'mark_id', $this->search_array['mark_id'], 'id="mark_id_search"'), "{mark}");
        $this->tpl->Set($this->tpl->Selection($this->models, 'model_id', $this->search_array['model_id'], 'id="model_id_search"'), "{model}");

        $this->tpl->Set($this->tpl->Selection($this->currency_array, 'currency', $this->search_array['currency']), "{currency}");
        $this->tpl->Set($this->tpl->Selection(array(0=>$this->lang['year_any']) + $this->year_array, 'year_min', $this->search_array['year_min']), "{year_min}");
        $this->tpl->Set($this->tpl->Selection(array(0=>$this->lang['year_any']) + $this->year_array, 'year_max', $this->search_array['year_max']), "{year_max}");
        $this->tpl->Set($this->tpl->inputCheckbox('isset_photo', 1, $this->search_array['isset_photo']), "{isset_photo}");
        $this->tpl->Set($this->tpl->Selection($show_count, 'search_count_day', $this->search_array['search_count_day']), "{sel_count}");

        $fields = array("race",
						"capacity_motor_min",
						"capacity_motor_max",
						"power_min",
						"power_max",
						"cost_min",
						"cost_max",
        );

        foreach ($fields as $field)
        {
            $this->tpl->Set($this->search_array[$field], "{" . $field . "}");
        }

        foreach ($this->sel_fields as $name=>$value)
        {
            if ($value['values'])
            $this->tpl->Set($this->tpl->Selection(array(0=>$this->lang['any']) + $value['values'], $name, $this->search_array[$name]), "{" . $name . "}");
        }

        foreach ($this->checkbox_fields as $name=>$value)
        {
            $this->tpl->Set($this->tpl->InputCheckbox($name, 1, $this->search_array[$name]), "{" . $name . "}");
        }
        
        $JS .= $this->_getSearchJS();
        
    }

    public function GetPrintUrl($id)
    {
        if ($this->config['general_mod_rewrite'])
        {
            if ($this->config['general_main_page'])
            {
                return $GLOBALS['config']['http_home_url'] . "print" . $id . ".html";
            }
            else
            {
                return $GLOBALS['config']['http_home_url'] . $this->config['general_name_module'] ."/print" . $id . ".html";
            }
        }

        return $GLOBALS['config']['http_home_url'] . "engine/car-market/print.php?id=" . $id;
    }

    public function DateFormat($time)
    {
        switch (date("d.m.Y", $time))
        {
            case date("d.m.Y", $this->base->timer->cur_time):
                $time = date($this->lang['today_in'] . "H:i", $time);
                break;
                 
            case date("d.m.Y", $this->base->timer->cur_time - 86400):
                $time = date($this->lang['yestoday_in'] . "H:i", $time);
                break;

            default:
                $time = date("d.m.Y H:i", $time);
                break;
        }
        return $time;
    }

    private function LoginIn($pass)
    {
        set_cookie ("dle_password",  md5($pass), 365);
        @session_register('dle_password');
        $_SESSION['dle_password']    = md5($pass);

        if ($GLOBALS['config']['version_id'] < 7.2)
        {
            set_cookie ("dle_name", $this->member['name'], 365);
            @session_register('dle_name');
            $_SESSION['dle_name'] = $this->member['name'];
        }
        else
        {
            set_cookie ("dle_user_id", $this->member['id'], 365);
            @session_register('dle_user_id');
            $_SESSION['dle_user_id'] = $this->member['id'];
        }


        $GLOBALS['is_logged'] = TRUE;
    }

    private function GetGuestSession()
    {
        if (!empty($_COOKIE['auto_session']) && strlen($_COOKIE['auto_session']) == 32)
        return $_COOKIE['auto_session'];
        else
        {
            $sessuon = md5(LIC_DOMAIN . microtime(true));
            set_cookie ("auto_session", $sessuon, 365);
            return $sessuon;
        }
    }

    public function AddView($auto_id)
    {
        $this->base->Update('auto_autos', array('view_count' => 'view_count+1'), array('id' => $auto_id), true);
    }
}
?>