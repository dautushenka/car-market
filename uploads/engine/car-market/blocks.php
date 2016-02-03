<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

if (!require_once(ENGINE_DIR . "/car-market/includes.php"))
return ;

final class Blocks
{
    /**
     * Delegete auto
     *
     * @var CarMarketUser
     */
    private $auto = null;

    private $action = '';

    public function __construct(CarMarket &$auto, $action)
    {
        $this->auto =& $auto;
        $this->action = $action;
    }

    public function BlockDinamic($setting = 0, $content_name = '')
    {
        if (!$setting)
        {
            if ($this->action == "main")
            $setting = $this->auto->config['block_dimanic_on_main_module'];
            elseif (in_array($this->action, array("add", "account", "save", "edit", "doadd")))
            $setting = $this->auto->config['block_dimanic_on_add'];
            elseif ($this->action == 'search')
            $setting = $this->auto->config['block_dimanic_on_search'];
            elseif (!defined('MODER'))
            $setting = $this->auto->config['block_dimanic_on_main_site'];
            else
            $setting = $this->auto->config['block_dimanic_on_default'];
        }
        	
        $this->auto->tpl->load('block_dinamic');
        	
        switch ($setting)
        {
            case 1:
                if ($this->auto->use_country)
                $this->auto->GetCountries(true);
                if ($this->auto->use_region)
                $this->auto->GetRegions($this->auto->search_array['country_id'], true);

                $this->auto->GetCities($this->auto->search_array['country_id'], $this->auto->search_array['region_id'], true);
                	
                $this->auto->GetMarks(true);
                $this->auto->GetModels($this->auto->search_array['mark_id'], true);

                $this->auto->tpl->SetStyleScript(array('{THEME}/car-market/css/style_user.css'), array('/engine/car-market/javascript/preload.js'));
                
                $this->auto->tpl->subhead = <<<JS
<script type="text/javascript">
var ajax_url = dle_root + 'engine/car-market/ajax.php';
var use_country = {$this->auto->use_country};
var use_region  = {$this->auto->use_region};
</script>
JS;

                $hidden_array = array();
                if (!$this->auto->config['general_main_page'])
                $hidden_array['do'] = $this->auto->config['general_name_module'];
                //				print_r($this->auto->search_array);
                $this->auto->tpl->SetForm($hidden_array, $this->auto->tpl->main_url, "GET", 'id="filter"');
                $JS = '';
                $this->auto->ShowSearch($JS);
                
                if ($JS)
                {
                    $JS = "<script type='text/javascript'>$().ready(function(){{$JS}});</script>";
                }
                
                $this->auto->tpl->SetBlock('form_search');
                break;
                	
            case 2:
                if (empty($this->auto->config['general_main_country']))
                {
                    trigger_error('Не выбран(а) главная страна/регион', E_USER_WARNING);
                    break;
                }
                
                $this->auto->tpl->SetBlock('list');
                if ($this->auto->use_country)
                {
                    if (!$this->auto->countries)
                    {
                        $this->auto->GetCountries();
                    }

                    $name = $this->auto->countries[$this->auto->config['general_main_country']];
                    	
                    if ($this->auto->use_region)
                    {
                        $items = $this->auto->GetRegions($this->auto->config['general_main_country']);
                    }
                    else
                    {
                        $items = $this->auto->GetCities($this->auto->config['general_main_country'], 0);
                    }
                }
                elseif ($this->auto->use_region)
                {
                    if (!$this->auto->regions)
                    {
                        $this->auto->GetRegions();
                    }

                    $name = $this->auto->regions[$this->auto->config['general_main_country']];
                    $items = $this->auto->GetCities(0, $this->auto->config['general_main_country']);
                }
                unset($items[0]);

                $this->auto->tpl->OpenRow('row_item');
                foreach ($items as $id=>$item)
                {
                    if ($this->auto->use_country)
                    {
                        if ($this->auto->use_region)
                        $url = $this->auto->tpl->GetUrl(array('country_id' => $this->auto->config['general_main_country'], 'region_id' => $id));
                        else
                        $url = $this->auto->tpl->GetUrl(array('country_id' => $this->auto->config['general_main_country'], 'city_id' => $id));
                    }
                    elseif ($this->auto->use_region)
                    $url = $this->auto->tpl->GetUrl(array('region_id' => $this->auto->config['general_main_country'], 'city_id' => $id));
                    	
                    $item = "<a href=\"" . $url . "\" >$item</a>";
                    $this->auto->tpl->SetRow(array("{item}" => $item), 'row_item');
                }
                $this->auto->tpl->CloseRow('row_item');
                break;

            case 3:
                $this->auto->tpl->SetBlock('list');
                if ($this->auto->search_array['mark_id'])
                {
                    $name = $this->auto->marks[$this->auto->search_array['mark_id']];
                    	
                    if (!$this->auto->models)
                    $items = $this->auto->GetModels($this->auto->search_array['mark_id']);
                    else
                    $items = $this->auto->models;
                }
                else
                {
                    $name = $this->auto->lang['marks'];
                    	
                    if (!$this->auto->marks)
                    $items = $this->auto->GetMarks();
                    else
                    $items = $this->auto->marks;
                }
                unset($items[0]);

                $this->auto->tpl->OpenRow('row_item');
                foreach ($items as $id=>$item)
                {
                    if ($this->auto->search_array['mark_id'])
                    $url = $this->auto->tpl->GetUrl(array('mark_id' => $this->auto->search_array['mark_id'], 'model_id' => $id), array(), array(), array(), array("use_alt_url" => false));
                    else
                    $url = $this->auto->tpl->GetUrl(array('mark_id' => $id), array(), array(), array(), array("use_alt_url" => false));

                    $item = "<a href=\"" . $url . "\" >$item</a>";
                    $this->auto->tpl->SetRow(array("{item}" => $item), 'row_item');
                }
                $this->auto->tpl->CloseRow('row_item');
                break;

            case 4:
                $this->auto->tpl->SetBlock('list');
                $type = 'country';


                if ($this->auto->search_array['city_id'] ||
                ($this->auto->use_region && $this->auto->search_array['region_id']) ||
                ($this->auto->use_country && $this->auto->search_array['country_id'] && !$this->auto->use_region) ||
                (!$this->auto->use_region && !$this->auto->use_region)
                )
                {
                    $type = 'city';
                    if ($this->auto->use_region)
                    $name = $this->auto->regions[$this->auto->search_array['region_id']];
                    elseif ($this->auto->use_country)
                    $name = $this->auto->countries[$this->auto->search_array['country_id']];
                    else
                    $name = $this->auto->lang['cities'];

                    if (!$this->auto->cities)
                    $items = $this->auto->GetCities($this->auto->search_array['country_id'], $this->auto->search_array['region_id']);
                    else
                    $items = $this->auto->cities;
                }
                elseif ($this->auto->use_region && !$this->auto->search_array['region_id'] &&
                (($this->auto->use_country && $this->auto->search_array['country_id']) || !$this->auto->use_country))
                {
                    $type = 'region';
                    if ($this->auto->use_country)
                    $name = $this->auto->countries[$this->auto->search_array['country_id']];
                    else
                    $name = $this->auto->lang['regions'];

                    if (!$this->auto->regions)
                    $items = $this->auto->GetRegions($this->auto->search_array['country_id']);
                    else
                    $items = $this->auto->regions;
                }
                elseif ($this->auto->use_country)
                {
                    $name = $this->auto->lang['countries'];
                    	
                    if (!$this->auto->counties)
                    $items = $this->auto->GetCountries();
                    else
                    $items = $this->auto->counties;
                }
                unset($items[0]);

                $this->auto->tpl->OpenRow('row_item');
                foreach ($items as $id=>$item)
                {
                    switch ($type)
                    {
                        case 'city':
                            $url = $this->auto->tpl->GetUrl($this->auto->search_array, array('city_id' => $id), array(), array(), array("use_alt_url" => false));
                            break;
                            	
                        case 'region':
                            $url = $this->auto->tpl->GetUrl($this->auto->search_array, array('region_id' => $id), array(), array(), array("use_alt_url" => false));
                            break;
                            	
                        case 'country':
                            $url = $this->auto->tpl->GetUrl($this->auto->search_array, array('country_id' => $id), array(), array(), array("use_alt_url" => false));
                            break;
                    }
                    	
                    $item = "<a href=\"" . $url . "\" >$item</a>";
                    $this->auto->tpl->SetRow(array("{item}" => $item), 'row_item');
                }
                $this->auto->tpl->CloseRow('row_item');

                break;
        }

        if (!empty($name))
        $this->auto->tpl->Set($name, "{name}");

        if ($content_name)
        {
            $this->auto->tpl->Compile($content_name, $JS);
        }
        else
        {
            $this->auto->tpl->Compile('block_dinamic', $JS);
        }
    }

    public function BlockLast()
    {
        if (!$this->auto->config['block_last_auto_user'])
        $this->auto->search_array = array();

        if ($this->auto->config['block_last_auto_photo'])
        $this->auto->search_array['isset_photo'] = 1;
        	
        $this->auto->search_array['sort'] = 'date';
        $this->auto->search_array['subsort'] = 'DESC';

        $this->auto->Search(array("get_count" => 0, "count" => $this->auto->config['block_last_count_auto']));

        $this->auto->tpl->load('block_last_auto')
        ->OpenRow('row_auto');
        	
        foreach ($this->auto->autos as $id=>$auto_one)
        {
            $this->auto->tpl->SetRow($this->auto->ShowAuto($id, array("show_photo" => 0, "show_edit" => 0)) + ShowPhoto($id, $this->auto), 'row_auto');
        }

        $this->auto->tpl->CloseRow('row_auto')->Compile('block_last_auto');
    }

    public  function BlockHot()
    {
        $this->auto->search_array = array();

        if ($this->auto->config['block_hot_auto_photo'])
        $this->auto->search_array['isset_photo'] = 1;
        	
        $this->auto->search_array['sort'] = 'date';
        $this->auto->search_array['subsort'] = 'DESC';
        $this->auto->search_array['allow_block'] = 1;

        $this->auto->Search(array("get_count" => 0, 
                                  "count" => $this->auto->config['block_hot_count_auto'],
                                  'use_order_random' => true
                                  ));

        $this->auto->tpl->load('block_hot_auto')
        ->OpenRow('row_auto');
        	
        foreach ($this->auto->autos as $id=>$auto_one)
        {
            $this->auto->tpl->SetRow($this->auto->ShowAuto($id, array("show_photo" => 0, "show_edit" => 0)) + ShowPhoto($id, $this->auto), 'row_auto');
        }

        $this->auto->tpl->CloseRow('row_auto')->Compile('block_hot_auto');
    }

}

$blocks = new Blocks($auto, $action);

if ($auto->config['general_allow_block_search'])
{
    $blocks->BlockDinamic(1, 'block_search');
}

if ($auto->config['block_dimanic_allow'])
$blocks->BlockDinamic();

if ($auto->config['block_last_allow'])
{
    if (!$auto->config['general_cache'] || !$cache = Cache::GetHTMLCache('BlockLast'))
    {
        $blocks->BlockLast();

        if ($auto->config['general_cache'])
        Cache::SetHTMLCache('BlockLast', $template->block_last_auto);
    }
    else
    $template->block_last_auto = $cache;
}

if ($auto->config['block_hot_allow'])
{
    if (!$auto->config['general_cache'] || !$cache = Cache::GetHTMLCache('BlockHot', $auto->config['block_hot_auto_time'] * 60))
    {
        $blocks->BlockHot();

        if ($auto->config['general_cache'])
        Cache::SetHTMLCache('BlockHot', $template->block_hot_auto);
    }
    else
    $template->block_hot_auto = $cache;
}

if ($auto->config['general_allow_block_statistic'])
{
    if (!$auto->config['general_cache'] || !($cache = Cache::GetHTMLCache('block_stats')))
    {
        $auto_on_site = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'), array('allow_site' => 1));
        $auto_reg = $base->SelectOne('auto_autos', array('max' => 'MAX(id)'));

        $base->SetWhere('add_date', $base->timer->cur_time - 24*60*60, ">");
        $today = $base->SelectOne('auto_autos', array('count' => 'COUNT(*)'));

        $template->load('block_stats');
        $template->Set($auto_on_site['count'], "{auto_now}");
        $template->Set($today['count'], "{auto_today}");
        $template->Set($auto_reg['max'], "{auto_max}");
        $template->Compile('block_stats');
        	
        if ($auto->config['general_cache'])
        {
            Cache::SetHTMLCache('block_stats', $template->block_stats);
        }
    }
    else
    {
        $template->block_stats = $cache;
    }
}

unset($auto, $template, $timer, $base, $exc, $blocks, $licence);

?>