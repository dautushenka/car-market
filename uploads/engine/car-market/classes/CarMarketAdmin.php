<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

require(ENGINE_DIR . "/car-market/classes/CarMarket.php");

final class CarMarketAdmin extends CarMarket
{

    public function __construct(&$base, &$car_conf, &$lang_car, $member, $other_fields_array, $checkboxes_array)
    {
        parent::__construct($base, $car_conf, $lang_car, $other_fields_array, $checkboxes_array);

        if ($GLOBALS['config']['version_id'] < 7.5)
        {
            $this->member['id']    = $member[10];
            $this->member['name']  = $member[2];
            $this->member['group'] = $member[1];
            $this->member['ip']    = $member[15];
        }
        else
        {
            $this->member['id']    = $member['user_id'];
            $this->member['name']  = $member['name'];
            $this->member['group'] = $member['user_group'];
            $this->member['ip']    = $this->base->EscapeString($_SERVER['REMOTE_ADDR']);;
        }
    }

    public function PreparationSearchArray()
    {
        $this->search_array = array_merge($this->search_array, array_intersect_key($_REQUEST['where'], $this->search_array));

        if ($this->use_country && !$this->search_array['country_id'])
        {
            $this->search_array['region_id'] = 0;
            $this->search_array['city_id'] = 0;
        }
        elseif ($this->use_region && !$this->search_array['region_id'])
        $this->search_array['city_id'] = 0;
        	
        if (!$this->search_array['mark_id'])
        $this->search_array['model_id'] = 0;
    }

    public function Add($value_array)
    {
        $this->values = $value_array;

        $this->CheckError();
        
        require_once ENGINE_DIR . '/car-market/classes/Fields.php';
        
        $xfields = new Fields($this->base, $this);
        
        $this->values['xfields'] = $xfields->EncodeFields($this->values);
        
        $this->Errors = $this->Errors + $xfields->getErrors();

        if ($this->Errors)
        {
            return false;
        }

        $this->PreparationValues();
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
                $this->base->Update('auto_images', array('auto_id' => $id), array('auto_id' => 0));
                
                if (!(int)$this->values['main_photo'])
                {
                    $this->values['main_photo'] = reset($this->values['images']);
                }
                
                $this->base->Update('auto_autos', array('photo' => $this->values['main_photo'], 'photo_count' => count($this->values['images'])), array('id' => $id));
            }
        }
        
        
        if (!empty($_FILES['photo']['name'][0]) && $this->UploadPhoto($id))
        {
            $this->base->Update('auto_autos', array('photo' => $this->values['photo'], 'photo_count' => $this->values['photo_count']), array('id' => $id));
        }
        	
        if (!empty($this->values['allow_site']))
        {
            $this->IncrementCounter($this->values['mark_id'], $this->values['model_id']);
            Cache::ClearAllCache();
        }
        	
        return $id;
    }

    public function Search(array $search_param = array(), array $count = array())
    {
        $this->base->BuildQuery('auto_autos', array("auto_cities" => array('id' => 'city_id'),
													"auto_marks"  => array('id' => 'mark_id'),
													"auto_models" => array('id' => 'model_id'),
        ));
        	
        $this->base->SetSelection(array(
										'auto_autos' => array('id', "cost", "contact_person", "add_date", "exp_date", "block_date", "currency", "allow_site", 'author'),
										'auto_models' => array('model_name' => 'name'),
										'auto_marks' => array('mark_name' => 'name'),
										'auto_cities' => array('city_name' => 'name'),
        ));

        $this->PreparationSearch();
        
        if (!empty($search_param['author'])) 
        {
            $this->base->SetWhere('author', $search_param['author'], "LIKE");
        }
        
        if (!empty($search_param['contact_person'])) 
        {
            $this->base->SetWhere('contact_person', $search_param['contact_person'], 'LIKE');
        }

        if ($search_param['add_date'] && strtotime($search_param['add_date']) !== -1)
        $this->base->SetWhere('add_date', strtotime($search_param['add_date']), ">");
        	
        $this->base->ExecuteBuildQuery(array('allow_site'=>'DESC', 'add_date'=> 'DESC'), $count);

        while ($row = $this->base->FetchArray())
        {
            $this->autos[$row['id']] = $row;
        }

        $this->autos_count = $this->base->CountForBuldQuery();
    }

    public function DelAuto($autos_id)
    {
        if (!is_array($autos_id))
        $autos_id = array($autos_id);
        	
        foreach ($autos_id as $id)
        {
            $this->old_values = $this->base->SelectOne('auto_autos', array('allow_site', "model_id", "mark_id", "photo"), array("id"=>$id));

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
        return true;
    }
    
    public function GetSearchJS()
    {
        return parent::_getSearchJS();
    }
}


?>