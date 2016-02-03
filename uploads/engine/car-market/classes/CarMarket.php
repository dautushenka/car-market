<?php

if (! defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}


abstract class CarMarket
{

    public $use_country = false;

    public $use_region = false;

    public $countries = array();

    public $regions = array();

    public $cities = array();

    public $marks = array();

    public $models = array();

    public $autos = array();

    public $autos_count = 0;

    public $sel_fields = array();

    public $checkbox_fields = array();

    public $year_array = array();

    public $lang = array();

    public $config = array();

    public $Errors = array();

    public $member = array(
        "id" => 0, 
        "name" => '', 
        "group" => '', 
        "ip" => ''
    );

    public $currency_array = array(
        'USD' => 'USD', 
        'RUR' => 'USD', 
        'EUR' => 'USD'
    );

    public $count_day_array = array(
        1 => 1, 
        3 => 3, 
        5 => 5, 
        10 => 10, 
        15 => 15, 
        31 => 31, 
        62 => 62, 
        0 => 0
    );

    public $sort_array = array(
        'cost' => 'cost', 
        'race' => 'race', 
        'year' => 'year', 
        'date' => 'date', 
        'author' => 'author'
    );

    public $subsort_array = array(
        'ASC' => 'ASC', 
        'DESC' => 'DESC'
    );

    public $search_array = array(
        'country_id' => 0, 
        'region_id' => 0, 
        'city_ids' => 0, 
        'city_id' => 0, 
        'city_other' => '', 
        'mark_id' => 0, 
        'model_id' => 0, 
        'model_ids' => array(), 
        'model_other' => '', 
        'capacity_motor_min' => '', 
        'capacity_motor_max' => '', 
        'power_min' => '', 
        'power_max' => '', 
        'race' => '', 
        'cost_min' => '', 
        'cost_max' => '', 
        'year_min' => '', 
        'year_max' => '', 
        'currency' => 'USD', 
        'search_count_day' => 0, 
        'isset_photo' => 0, 
        'sort' => 'date', 
        'subsort' => 'DESC',
        'author_id' => 0,
        'author' => ''
    );

    /**
     * DataBase delegate
     *
     * @var DataBaseCore
     */
    protected $base = false;

    protected $save = array();

    protected $values = array();

    protected $old_values = array();

    protected $allowed_mime_types = array(
        'jpeg', 
        'png', 
        'gif', 
        'jpg', 
        'jpe'
    );

    public function __construct(DataBaseCore &$base, &$car_conf, &$lang_car, &$other_fields_array, &$checkboxes_array)
    {
        if (empty($car_conf['version_id']))
            throw new ExceptionAllError('Модуль не установлен');
        Licencing::check();
        $this->base = & $base;
        $this->config = & $car_conf;
        $this->config['currency']['USD'] = 1;
        $this->lang = & $lang_car;
        
        if ($this->config['use_country'])
            $this->use_country = 1;
        else
            $this->use_country = 0;
        
        if ($this->config['use_region'])
            $this->use_region = 1;
        else
            $this->use_region = 0;
        
        $this->count_day_array = array(
            1 => $lang_car['count_day_1'], 
            3 => $lang_car['count_day_3'], 
            5 => $lang_car['count_day_5'], 
            10 => $lang_car['count_day_10'], 
            15 => $lang_car['count_day_15'], 
            31 => $lang_car['count_day_month'], 
            62 => $lang_car['count_day_month2'], 
            0 => $lang_car['count_day_always']
        );
        
        $this->GetFields($other_fields_array, $checkboxes_array);
        $this->PreparationSearchArray();
    }

    public function GetCountries($search = false)
    {
        $this->countries = array();
        
        if ($search && $this->config['general_cache'])
            $cache = Cache::GetArrayCache('countries_search');
        elseif ($this->config['general_cache'])
            $cache = Cache::GetArrayCache('countries');
        
        if (! empty($cache))
            return $this->countries = $cache;
        
        if (! $search)
            $this->countries[] = $this->lang['sel_country'];
        else
            $this->countries[] = $this->lang['any'];
        
        $this->base->Select("auto_countries", array(
            "*"
        ), array(), array(
            "name" => 'ASC'
        ));
        while ($row = $this->base->FetchArray())
        {
            $this->countries[$row['id']] = $row['name'];
        }
        
        if ($search && $this->config['general_cache'])
            Cache::SetArrayCache('countries_search', $this->countries);
        elseif ($this->config['general_cache'])
            Cache::SetArrayCache('countries', $this->countries);
        
        return $this->countries;
    }

    public function GetRegions($country = 0, $search = false)
    {
        $this->regions = array();
        
        if ($search && $this->config['general_cache'])
            $cache = Cache::GetArrayCache('regions_search_' . $country);
        elseif ($this->config['general_cache'])
            $cache = Cache::GetArrayCache('regions_' . $country);
        
        if (! empty($cache))
            return $this->regions = $cache;
        
        if (! $country && $this->use_country)
        {
            $this->regions[] = $this->lang['sel_country'];
            return;
        }
        
        if (! $search)
            $this->regions[] = $this->lang['sel_region'];
        else
            $this->regions[] = $this->lang['any'];
        
        $where = array();
        
        if (intval($country))
            $where['country_id'] = intval($country);
        
        $this->base->Select("auto_regions", "*", $where, array(
            "name" => 'ASC'
        ));
        while ($row = $this->base->FetchArray())
        {
            $this->regions[$row['id']] = $row['name'];
        }
        
        if ($search && $this->config['general_cache'])
            Cache::SetArrayCache('regions_search_' . $country, $this->regions);
        elseif ($this->config['general_cache'])
            Cache::SetArrayCache('regions_' . $country, $this->regions);
        
        return $this->regions;
    }

    public function GetCities($country = 0, $region = 0, $search = false)
    {
        $this->cities = array();
        
        if (!$country && $this->use_country && !AJAX)
        {
            if ($this->use_region)
            {
                $this->cities[] = $this->lang['sel_region'];
            }
            else
            {
                $this->cities[] = $this->lang['sel_country'];
            }
            
            return;
        }
        elseif (! $region && $this->use_region)
        {
            $this->cities[] = $this->lang['sel_region'];
            return;
        }
        else if (!$search)
        {
            $this->cities[''] = $this->lang['sel_city'];
        }
        
        if ($search)
        {
            $this->cities[] = $this->lang['any'];
        }
        
        $where = array();
        
        if (intval($country))
            $where['country_id'] = intval($country);
        
        if (intval($region))
            $where['region_id'] = intval($region);
        
        $this->base->Select("auto_cities", "*", $where, array(
            "name" => 'ASC'
        ));
        while ($row = $this->base->FetchArray())
        {
            $this->cities[$row['id']] = $row['name'];
        }
        if ($search)
            $this->cities[- 1] = $this->lang['other'];
        
        return $this->cities;
    }

    public function GetMarks($search = false)
    {
        $this->marks = array();
        
        if ($search && $this->config['general_cache'])
            $cache = Cache::GetArrayCache('marks_search');
        elseif ($this->config['general_cache'])
            $cache = Cache::GetArrayCache('marks');
        
        if (! empty($cache))
            return $this->marks = $cache;
        
        if (! $search)
            $this->marks[] = $this->lang['sel_mark'];
        else
            $this->marks[] = $this->lang['any'];
        
        if (! $this->config['user_int_show_marks_no_auto'] && $search)
            $this->base->SetWhere('auto_num', 0, ">", "auto_marks");
        
        $this->base->Select("auto_marks", "*", array(), array(
            'name' => 'ASC'
        ));
        while ($row = $this->base->FetchArray())
        {
            if ($this->config['user_int_show_counter'] && $search)
                $this->marks[$row['id']] = $row['name'] . " (" . $row['auto_num'] . ")";
            else
                $this->marks[$row['id']] = $row['name'];
        }
        
        if ($search && $this->config['general_cache'])
            Cache::SetArrayCache('marks_search', $this->marks);
        elseif ($this->config['general_cache'])
            Cache::SetArrayCache('marks', $this->marks);
        
        return $this->marks;
    }

    public function GetModels($mark_id, $search = false)
    {
        $this->models = $where = array();
        
        if (! $mark_id)
        {
            $this->models[] = $this->lang['sel_mark'];
            return;
        }
        
        if ($search)
            $this->models[] = $this->lang['any'];
        
        if (intval($mark_id))
            $where['mark_id'] = intval($mark_id);
        Licencing::check();
        if (! $this->config['user_int_show_marks_no_auto'] && $search)
            $this->base->SetWhere('auto_num', 0, ">", "auto_models");
        
        $this->base->Select('auto_models', '*', $where, array(
            'name' => 'ASC'
        ));
        while ($row = $this->base->FetchArray())
        {
            if ($this->config['user_int_show_counter'] && $search)
                $this->models[$row['id']] = $row['name'] . " (" . $row['auto_num'] . ")";
            else
                $this->models[$row['id']] = $row['name'];
        }
        
        if ($search)
            $this->models[- 1] = $this->lang['other'];
        
        return $this->models;
    }

    public function Save($values_array, $old = array())
    {
        if (empty($values_array['id']))
        {
            return false;
        }
        
        $this->values = $values_array;
        
        $this->CheckError();
        
        require_once ENGINE_DIR . '/car-market/classes/Fields.php';
        
        $xfields = new Fields($this->base, $this);
        
        $this->values['xfields'] = $xfields->EncodeFields($this->values);
        
        $this->Errors = $this->Errors + $xfields->getErrors();
        
        if ($this->Errors)
            return false;
        
        if ($old)
            $this->old_values = $old;
        elseif (! ($this->old_values = $this->base->SelectOne('auto_autos', array(
            '*'
        ), array(
            'id' => $this->values['id']
        ))))
            throw new ExceptionAllError("Автомобиль не обнаружен");
        
        $this->PreparationValues();
        $this->values['update_date'] = $this->base->timer->cur_time;
        
        if (!empty($this->values['images']))
        {
            $this->values['images'] = array_slice($this->values['images'], 0, $this->config['count_photo'][$this->member['group']]);
            
            if ($this->values['images'])
            {
                if (!(int)$this->values['main_photo'])
                {
                    $this->values['main_photo'] = reset($this->values['images']);
                }
                
                $this->base->Update('auto_autos', array('photo' => $this->values['main_photo'], 'photo_count' => count($this->values['images'])), array('id' => $this->values['id']));
            }
        }
        
        
        if (! empty($_FILES['photo']['name'][0]))
            $this->UploadPhoto($this->values['id'], $this->old_values['photo_count']);
        
        $this->base->Update('auto_autos', $this->values, array(
            'id' => $this->values['id']
        ));
        
        if ($this->values['allow_site'])
        {
            if (! $this->old_values['allow_site'])
                $this->IncrementCounter($this->values['mark_id'], $this->values['model_id']);
            elseif ($this->values['model_id'] != $this->old_values['model_id'])
            {
                $this->DecrementCounter($this->old_values['mark_id'], $this->old_values['model_id']);
                $this->IncrementCounter($this->values['mark_id'], $this->values['model_id']);
            }
            Cache::ClearHTMLCache();
        }
        elseif ($this->old_values['allow_site'])
            $this->DecrementCounter($this->old_values['mark_id'], $this->old_values['model_id']);
        
        return true;
    }

    protected function CheckError()
    {
        if ($this->use_country && ! intval($this->values['country_id']))
            $this->Errors[] = $this->lang['auto_error_country'];
        
        if ($this->use_region && ! intval($this->values['region_id']))
            $this->Errors[] = $this->lang['auto_error_region'];
        
        if (! intval($this->values['city_id']) && ! $this->values['city_other'])
            $this->Errors[] = $this->lang['auto_error_city'];
        
        if (! intval($this->values['mark_id']))
            $this->Errors[] = $this->lang['auto_error_marka'];
        
        if (! intval($this->values['model_id']) && ! $this->values['model_other'])
            $this->Errors[] = $this->lang['auto_error_model'];
        
        if (! $this->values['contact_person'] && ! empty($this->config['need_field']['contact_person']))
            $this->Errors[] = $this->lang['auto_error_contact_person'];
        Licencing::check();
        
        if (! $this->values['year'] && ! empty($this->config['need_field']['year']))
            $this->Errors[] = $this->lang['auto_error_year'];
        
        if ((! $this->values['email'] || ! auto_check_email($this->values['email'])) && ! empty($this->config['need_field']['email']))
            $this->Errors[] = $this->lang['auto_error_email'];
        
        if ((! $this->values['phone'] || ! preg_match('#^[\d- +\(\);:]+$#i', $this->values['phone'])) && ! empty($this->config['need_field']['phone']))
            $this->Errors[] = $this->lang['auto_error_phone'];
        
        if ($this->values['exp_date'] && strtotime($this->values['exp_date']) === - 1)
            $this->Errors[] = $this->lang['auto_error_exp_date'];
        
        if ($this->values['allow_block'] && $this->values['block_date'] && strtotime($this->values['block_date']) === - 1)
            $this->Errors[] = $this->lang['auto_error_block_date'];
        
        if ($this->values['race'] === '' && ! empty($this->config['need_field']['race']))
            $this->Errors[] = $this->lang['auto_error_race'];
        
        if (! $this->values['power'] && ! empty($this->config['need_field']['power']))
            $this->Errors[] = $this->lang['auto_error_power'];
        
        if ($this->values['model_other'])
        {
            $this->values['model_id'] = 0;
        }
            
        if (! empty($_FILES['photo']['name'][0]))
            $this->CheckUploadError();
    }

    protected function checkUploadAJAXError(&$file)
    {
        $size = $file->getSize();
        
        $pathinfo = pathinfo($file->getName());
        
        $name = $pathinfo['filename'];
        
        if ($size <= 0)
            $this->Errors[] = $name . $this->lang['auto_error_photo_empty'];
        
        if ($this->config['photo_size_byte'] && ($size >= $this->config['photo_size_byte'] * 1024))
            $this->Errors[] = $this->lang['auto_error_big_image'] . formatsize($this->config['photo_size_byte']) . " " . $name;
        
        if (! in_array(strtolower($pathinfo['extension']), $this->allowed_mime_types))
            $this->Errors[] = $this->lang['auto_error_type_image'] . $name;
            
        $this->CheckUploadDir();
    }
    
    protected function CheckUploadDir()
    {
        if (! is_dir(UPLOAD_DIR . $this->values['model_id']))
        {
            if (! @mkdir(UPLOAD_DIR . $this->values['model_id'] . "/", 0777))
                throw new ExceptionAllError('Неаозможно создать каталог для модели №' . $this->values['model_id'] . " Скорее всего запрещена запись в каталог /upoads/auto_foto/ или его не существует");
            
            @chmod(UPLOAD_DIR . $this->values['model_id'], 0777);
            @mkdir(UPLOAD_DIR . $this->values['model_id'] . "/thumbs", 0777);
            @chmod(UPLOAD_DIR . $this->values['model_id'] . "/thumbs", 0777);
        }
    }
    
    protected function CheckUploadError()
    {
        $photos = $_FILES['photo'];
        $count = count($photos['name']);
        
        for ($current = 0; $current < $count; $current ++)
        {
            if ($photos['name'][$current])
            {
                switch ($photos['error'][$current])
                {
                    case 0:
                        break;
                    case 1:
                        $this->Errors[] = $photos['name'][$current] . $this->lang['auto_error_max_php_ini'];
                        break;
                    case 2:
                        $this->Errors[] = $photos['name'][$current] . $this->lang['auto_error_max_php_ini'];
                        break;
                    case 3:
                        throw new ExceptionAllError($photos['name'][$current] . ' was only partially uploaded');
                        break;
                    case 4:
                        throw new ExceptionAllError('No file was uploaded');
                        break;
                    case 6:
                        throw new ExceptionAllError('Missing a temporary folder');
                        break;
                    case 7:
                        throw new ExceptionAllError('Failed to write ' . $photos['name'][$current] . ' to disk');
                        break;
                    case 8:
                        $this->Errors[] = $this->lang['auto_error_type_image'] . $photos['name'][$current];
                        break;
                    default:
                        throw new ExceptionAllError('Unidentified Error, caused by ' . $photos['name'][$current]);
                        break;
                }
                
                if ($photos['size'][$current] <= 0)
                    $this->Errors[] = $photos['name'][$current] . $this->lang['auto_error_photo_empty'];
                
                if ($this->config['photo_size_byte'] && ($photos['size'][$current] >= $this->config['photo_size_byte'] * 1024))
                    $this->Errors[] = $this->lang['auto_error_big_image'] . formatsize($this->config['photo_size_byte']) . " " . $photos['name'][$current];
                
                if (! in_array(strtolower(end(explode(".", $photos['name'][$current]))), $this->allowed_mime_types))
                    $this->Errors[] = $this->lang['auto_error_type_image'] . $photos['name'][$current];
            }
        }
        
        $this->CheckUploadDir();
    }

    protected function PreparationValues()
    {
        if (! class_exists('ParseFilter'))
            throw new ExceptionAllError('Не найден класс ParseFilter');
        
        $parse = new ParseFilter(Array(), Array(), 1, 1);
        
        foreach ($this->checkbox_fields as $box_name => $name)
        {
            if (! empty($this->values[$box_name]))
                $this->values[$box_name] = 1;
            else
                $this->values[$box_name] = 0;
        }
        
        if (empty($this->values['cost']))
            $this->values['cost'] = 0;
        else
            $this->values['cost'] = str_replace(",", ".", str_replace(" ", "", $this->values['cost']));
            
        if (empty($this->values['currency'])) 
        {
            $this->values['currency'] = "USD";
        }
        
        $this->values['cost_search'] = $this->values['cost'] / $this->config['currency'][$this->values['currency']];
        
        if (! empty($this->values['auction']) && $this->values['cost'])
            $this->values['auction'] = 1;
        else
            $this->values['auction'] = 0;
        
        if (! empty($this->values['allow_site']))
            $this->values['allow_site'] = 1;
        else
            $this->values['allow_site'] = 0;
        
        if (! empty($this->values['allow_block']))
        {
            $this->values['allow_block'] = 1;
            if (! empty($this->values['block_date']))
                $this->values['block_date'] = strtotime($this->values['block_date']);
            else
                $this->values['block_date'] = 0;
        }
        else
        {
            $this->values['block_date'] = 0;
            $this->values['allow_block'] = 0;
        }
        
        if (! empty($this->values['exp_date']))
            $this->values['exp_date'] = strtotime($this->values['exp_date']);
        else
            $this->values['exp_date'] = 0;
        
        $this->values['city_other'] = $parse->process(trim($this->values['city_other']));
        $this->values['model_other'] = $parse->process(trim($this->values['model_other']));
        $this->values['phone'] = $parse->process(trim($this->values['phone']));
        $this->values['contact_person'] = $parse->process(trim($this->values['contact_person']));
        $this->values['description'] = $parse->BB_Parse($this->values['description'], false);
        
        if ($this->values['model_other'])
            $this->values['model_id'] = 0;
        
        if ($this->values['city_other'])
            $this->values['city_id'] = 0;
    }

    protected function PreparationSearch()
    {
        if ($this->use_country && ! empty($this->search_array['country_id']))
            $this->base->SetWhere('country_id', $this->search_array['country_id'], '=');
        
        if ($this->use_region && ! empty($this->search_array['region_id']))
            $this->base->SetWhere('region_id', $this->search_array['region_id'], "=");
        
        if (!empty($this->search_array['city_ids']) && is_array($this->search_array['city_ids']))
        {
            $this->base->SetWhere('city_id', $this->search_array['city_ids'], 'IN');
        }
        else if (! empty($this->search_array['city_id']))
        {
            if ($this->search_array['city_id'] != - 1)
                $this->base->SetWhere('city_id', $this->search_array['city_id'], "=");
            else
                $this->base->SetWhere('city_other', "", "!=");
        }
        
        if (! empty($this->search_array['mark_id']))
            $this->base->SetWhere('mark_id', $this->search_array['mark_id'], "=");
        
        if (!empty($this->search_array['model_ids']) && is_array($this->search_array['model_ids']))
        {
            $this->base->SetWhere('model_id', $this->search_array['model_ids'], 'IN');
        }
        else if (! empty($this->search_array['model_id']))
        {
            if ($this->search_array['model_id'] != - 1)
                $this->base->SetWhere('model_id', $this->search_array['model_id'], "=");
            else
                $this->base->SetWhere('model_other', "", "!=");
        }
        
        if (! empty($this->search_array['race']))
            $this->base->SetWhere('race', $this->search_array['race'], '<=');
        
        if (! empty($this->search_array['power_min']) && ! empty($this->search_array['power_max']))
            $this->base->SetWhere('power', array(
                $this->search_array['power_min'], 
                $this->search_array['power_max']
            ), "BETWEEN");
        elseif (! empty($this->search_array['power_max']))
            $this->base->SetWhere('power', $this->search_array['power_max'], "<=");
        elseif (! empty($this->search_array['power_min']))
            $this->base->SetWhere('power', $this->search_array['power_min'], ">=");
        
        if (! empty($this->search_array['year_min']) && ! empty($this->search_array['year_max']))
            $this->base->SetWhere('year', array(
                $this->search_array['year_min'], 
                $this->search_array['year_max']
            ), "BETWEEN");
        elseif (! empty($this->search_array['year_max']))
            $this->base->SetWhere('year', $this->search_array['year_max'], "<=");
        elseif (! empty($this->search_array['year_min']))
            $this->base->SetWhere('year', $this->search_array['year_min'], ">=");
        
        if (! empty($this->search_array['capacity_motor_min']) && ! empty($this->search_array['capacity_motor_max']))
            $this->base->SetWhere('capacity_motor', array(
                $this->search_array['capacity_motor_min'], 
                $this->search_array['capacity_motor_max']
            ), "BETWEEN");
        elseif (! empty($this->search_array['capacity_motor_max']))
            $this->base->SetWhere('capacity_motor', $this->search_array['capacity_motor_max'], "<=");
        elseif (! empty($this->search_array['capacity_motor_min']))
            $this->base->SetWhere('capacity_motor', $this->search_array['capacity_motor_min'], ">=");
        
        if (empty($this->search_array['currency']) || empty($this->config['currency'][$this->search_array['currency']]))
            $this->search_array['currency'] = 'USD';
        
        if (! empty($this->search_array['cost_min']) && ! empty($this->search_array['cost_max']))
            $this->base->SetWhere('cost_search', array(
                $this->search_array['cost_min'] / $this->config['currency'][$this->search_array['currency']], 
                $this->search_array['cost_max'] / $this->config['currency'][$this->search_array['currency']]
            ), "BETWEEN");
        elseif (! empty($this->search_array['cost_max']))
            $this->base->SetWhere('cost_search', $this->search_array['cost_max'] / $this->config['currency'][$this->search_array['currency']], "<=");
        elseif (! empty($this->search_array['cost_min']))
        {
            $this->base->SetBeginBlockWhere();
            $this->base->SetWhere('cost_search', $this->search_array['cost_min'] / $this->config['currency'][$this->search_array['currency']], ">=");
            $this->base->SetWhere('cost_search', 0, "=", '', 'OR');
            $this->base->SetEndBlockWhere();
            Licencing::check();
        }
        
        if (!empty($this->search_array['author']))
        {
            $this->base->SetWhere('author', urldecode($this->search_array['author']), '=');
        }
        
        if (! empty($this->search_array['contact_person']))
            $this->base->SetWhere('contact_person', $this->search_array['contact_person'], "LIKE");
        
        if (! empty($this->search_array['isset_photo']))
            $this->base->SetWhere('photo', "0", "!=");
        
        foreach ($this->sel_fields as $name => $value)
        {
            if (! empty($this->search_array[$name]) && ! empty($value['values'][$this->search_array[$name]]))
                $this->base->SetWhere($name, $this->search_array[$name], "=");
        }
        
        foreach ($this->checkbox_fields as $name => $value)
        {
            if (! empty($this->search_array[$name]))
                $this->base->SetWhere($name, 1, "=");
        }
        
        if (! empty($this->search_array['author_id']))
        {
            $this->base->SetWhere('author_id', $this->search_array['author_id'], "=");
        }
        elseif (! empty($this->search_array['guest_session']))
        {
            $this->base->SetWhere('guest_session', $this->search_array['guest_session'], "=");
        }
    }
    
    protected function handlingImage($name, $current, $id = 0)
    {
        $thumb = new auto_thumbnail(UPLOAD_DIR . $this->values['model_id'] . "/" . $name);
        $thumb->size_auto($this->config['photo_size_width']);
        $thumb->jpeg_quality($this->config['photo_quality']);
        
        if ($this->config['photo_logo'])
            $thumb->insert_watermark($this->config['photo_size_for_logo']);
        
        $thumb->save(UPLOAD_DIR . $this->values['model_id'] . "/" . $name);
        $thumb->auto_thumbnail(UPLOAD_DIR . $this->values['model_id'] . "/" . $name);
        $thumb->size_auto($this->config['photo_size_width_th']);
        $thumb->jpeg_quality($this->config['photo_quality']);
        $thumb->save(UPLOAD_DIR . $this->values['model_id'] . "/thumbs/" . $name);
        $cur_id = $this->base->Insert('auto_images', array(
            'model_id' => $this->values['model_id'], 
            'auto_id' => $id, 
            'add_date' => $this->base->timer->cur_time, 
            'image_name' => $name,
            'user_id' => $this->member['id'],
            'guest_session' => empty($this->guest_session)?'':$this->guest_session
        ));
        
        return $cur_id;
    }

    protected function UploadPhoto($id, $photo_now = 0)
    {
        $photos = $_FILES['photo'];
        $count = (count($photos['name']) > $this->config['count_photo'][$this->member['group']] - $photo_now) ? $this->config['count_photo'][$this->member['group']] - $photo_now : count($photos['name']);
        
        if (! class_exists('auto_thumbnail'))
            throw new ExceptionAllError('Не найден класс auto_thumbnail');
        
        $i = 0;
        $photo_main = 0;
        for ($current = 0; $current < $count; $current ++)
        {
            if ($photos['name'][$current])
            {
                $type = strtolower(end(explode(".", $photos['name'][$current])));
                $name = $id . "_" . $current . "_" . $this->base->timer->cur_time . "." . $type;
                var_dump(UPLOAD_DIR . $this->values['model_id'] . "/" . $name);
                if (copy($photos['tmp_name'][$current], UPLOAD_DIR . $this->values['model_id'] . "/" . $name))
                { //die($temp_name);
                    
                    $cur_id = $this->handlingImage($name, $current, $id);
                    
                    if (!$i)
                        $photo_main = $cur_id;
                }
                else
                    throw new ExceptionAllError('Ошибка при перемещении файла');
                
                if (($this->config['count_photo'][$this->member['group']] - $photo_now) <= ++ $i)
                    break;
            }
        }
        
        if (! $photo_now && $photo_main)
            $this->values['photo'] = $photo_main;
        if ($i)
            $this->values['photo_count'] = $i + $photo_now;
        
        return $i;
    }
    
    /**
     * Uploading photos this AJAX over php stream
     * @param integer $current current image number
     * @param integer $id auto id
     * @param integer $model_id model auto id
     * @return array
     */
    public function UploadAJAXPhoto($current, $id = 0, $model_id = 0)
    {
        if (!(int)$model_id)
        {
            $model_id = 0;
        }
        
        if (!(int)$id)
        {
            $id = 0;
        }
        $this->values['model_id'] = $model_id;
        
        // factory
        if (!empty($_GET['qqfile']))
        {
            $file = new qqUploadedFileXhr();
        }
        elseif (!empty($_FILES['Filedata']))
        {
            $file = new UploafFile('Filedata');
        }
        elseif (!empty($_FILES['uploadfile']))
        {
            $file = new UploafFile('uploadfile');
        }
        
        $this->checkUploadAJAXError($file);
        
        if (!$this->Errors)
        {
            $type = strtolower(end(explode(".", urldecode($file->getName()))));
            $name = $current . "_" . $this->base->timer->cur_time . "." . $type;
            
            if ($file->save(UPLOAD_DIR . $model_id . "/" . $name))
            {
                $image_id = $this->handlingImage($name, $current, $id);
                
                return array(
                            'image_url' => UPLOAD_URL . $model_id . '/' . $name,
                            'image_th_url' => UPLOAD_URL . $model_id . '/thumbs/' . $name,
                            'image_id' => $image_id
                             );
            }
            else 
            {
                $this->Errors = array_merge($this->Errors, $file->getErrors());
                $this->Errors[] = 'File save error';
                return array();
            }
        }
    }

    protected function DelPhotos($auto_id)
    {
        $resource = $this->base->Select('auto_images', array(
            "*"
        ), array(
            "auto_id" => $auto_id
        ));
        
        while ($row = $this->base->FetchArray($resource))
        {
            @unlink(ROOT_DIR . "/uploads/auto_foto/" . $row['model_id'] . "/" . $row['image_name']);
            @unlink(ROOT_DIR . "/uploads/auto_foto/" . $row['model_id'] . "/thumbs/" . $row['image_name']);
            $this->base->Delete('auto_images', array(
                'id' => $row['id']
            ));
        }
    }

    public function DelPhoto($auto_id, $photo_id)
    {
        if (!$photo_id)
        {
            return;
        }
        
        if (!(int)$auto_id)
        {
            $auto_id = 0;
        }
        
        $allow_del = false;
        
        if ($auto_id)
        {
            $this->old_values = $this->base->SelectOne('auto_autos', array(
                "photo", 
                "photo_count", 
                "author_id"
            ), array(
                'id' => $auto_id
            ));
            
            if (! $this->old_values)
            {
                return 'denied';
            }
            
            if (in_array($this->member['group'], $this->config['general_moderator']) || ((($this->old_values['author_id'] && $this->member['id'] == $this->old_values['author_id']) || ($this->old_values['guest_session'] && $this->old_values['guest_session'] == $this->guest_session)) && in_array($this->member['group'], $this->config['user_int_allow_edit'])))
            {
                $allow_del = true;
            }
        }
        else 
        {
            $allow_del = true;
        }
        
        if ($allow_del)
        {
            
            $photo = $this->base->SelectOne('auto_images', array(
                "*"
            ), array(
                "id" => $photo_id, 
                "auto_id" => $auto_id
            ));
            
            @unlink(ROOT_DIR . "/uploads/auto_foto/" . $photo['model_id'] . "/" . $photo['image_name']);
            @unlink(ROOT_DIR . "/uploads/auto_foto/" . $photo['model_id'] . "/thumbs/" . $photo['image_name']);
            $this->base->Delete('auto_images', array(
                'id' => $photo['id']
            ));
            
            if (-- $this->old_values['photo_count'] <= 0 && $auto_id)
                $this->base->Update('auto_autos', array(
                    "photo" => 0, 
                    "photo_count" => 0
                ), array(
                    "id" => $auto_id
                ));
            else if ($auto_id)
            {
                if ($photo_id == $this->old_values['photo'])
                {
                    $id = $this->base->SelectOne('auto_images', array(
                        "id"
                    ), array(
                        'auto_id' => $auto_id
                    ), array(
                        "add_date" => "DESC"
                    ));
                    
                    if (! $id['id'])
                        $id['id'] = 0;
                    
                    $this->base->Update('auto_autos', array(
                        "photo" => $id['id'], 
                        "photo_count" => $this->old_values['photo_count']
                    ), array(
                        "id" => $auto_id
                    ));
                }
                else
                    $this->base->Update('auto_autos', array(
                        "photo_count" => $this->old_values['photo_count']
                    ), array(
                        "id" => $auto_id
                    ));
            }
            return "ok";
        }
        else
            return 'denied';
    }

    protected function IncrementCounter($mark_id, $model_id)
    {
        $this->base->Update('auto_marks', array(
            "auto_num" => "auto_num+1"
        ), array(
            'id' => $mark_id
        ), true);
        if ($model_id)
            $this->base->Update('auto_models', array(
                "auto_num" => "auto_num+1"
            ), array(
                'id' => $model_id
            ), true);
    }

    protected function DecrementCounter($mark_id, $model_id)
    {
        $this->base->Update('auto_marks', array(
            "auto_num" => "IF(auto_num=0, 0, auto_num-1)"
        ), array(
            'id' => $mark_id
        ), true);
        if ($model_id)
            $this->base->Update('auto_models', array(
                "auto_num" => "IF(auto_num=0, 0, auto_num-1)"
            ), array(
                'id' => $model_id
            ), true);
    }

    protected function GetFields(&$other_fields_array, &$checkboxes_array)
    {
        if (! $other_fields_array || ! $checkboxes_array)
            throw new ExceptionAllError("не найдены данные о дополнительных полях");
        
        foreach ($other_fields_array as $id => $field)
        {
            if (file_exists(ENGINE_DIR . '/car-market/array/' . $field['file'] . '.php'))
            {
                include_once (ENGINE_DIR . '/car-market/array/' . $field['file'] . '.php');
                $name = str_replace("_array", "", $field['file']);
                $this->sel_fields[$name]['name'] = $field['name'];
                $this->sel_fields[$name]['id'] = $id;
                $this->sel_fields[$name]['values'] = $$field['file'];
                
                $this->search_array[$name] = 0;
            }
            else
                trigger_error("Не найден файл дополнительного поля " . $field['file'], E_WARNING);
        }
        Licencing::check();
        
        $this->checkbox_fields = $checkboxes_array;
        
        foreach ($this->checkbox_fields as $name => $value)
            $this->search_array[$name] = 0;
        
        for ($i = (int) date("Y", $this->base->timer->cur_time); $i >= 1900; $i --)
        {
            $this->year_array[$i] = $i;
        }
    }
    
    protected function _getSearchJS()
    {
        $JS = '';
        
        if (!empty($this->search_array['model_ids']) && is_array($this->search_array['model_ids']))
        {
            $json_model = '';
            foreach ($this->models as $model_id => $model_name)
            {
                if (in_array($model_id, $this->search_array['model_ids']))
                {
                    if ($json_model)
                    {
                        $json_model .= ", ";
                    }
                    $model_name = addcslashes($model_name, "'");
                    $json_model .= "{id:$model_id, name:'$model_name'}";
                }
            }
            $JS .= "\nSetSearchModels(new Array($json_model));";
        }
        
        if (!empty($this->search_array['city_ids']) && is_array($this->search_array['city_ids']))
        {
            $json_city = '';
            foreach ($this->cities as $city_id => $city_name)
            {
                if (in_array($city_id, $this->search_array['city_ids']))
                {
                    if ($json_city)
                    {
                        $json_city .= ", ";
                    }
                    $city_name = addcslashes($city_name, "'");
                    $json_city .= "{id:$city_id, name:'$city_name'}";
                }
            }
            $JS .= "\nSetSearchCities(new Array($json_city));";
        }
        
        return $JS;
    }
}
?>