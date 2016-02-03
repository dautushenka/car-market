<?php

if(!defined('DATALIFEENGINE'))
{
    die("Hacking attempt!");
}

function auto_check_email($email)
{
    return (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'. '@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email))?false:true;
}

function auto_check_reg ($name, $email, $password1, $password2)
{
    global $auto, $db;
    $Errors = array();

    if ($password1 != $password2) $Errors[] = $auto->lang['reg_err_1'];
    if (strlen($password1) < 6) $Errors[] = $auto->lang['reg_err_2'];
    if (strlen($name) > 20) $Errors[] = $auto->lang['reg_err_3'];
    if (preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $name)) $Errors[] = $auto->lang['reg_err_4'];
    if ($name == "") $Errors[] = $auto->lang['reg_err_7'];

    if (!$Errors)
    {

        @setlocale(LC_CTYPE, array("ru_RU.CP1251", "ru_SU.CP1251", "ru_RU.KOI8-r", "ru_RU", "russian", "ru_SU", "ru"));
        $replace_word = array ('e' => '[eеё]', 'r' => '[rг]', 't' => '[tт]', 'y' => '[yу]','u' => '[uи]','i' => '[i1l!]','o' => '[oо0]','p' => '[pр]','a' => '[aа]','s' => '[s5]','w' => 'w','q' => 'q','d' => 'd','f' => 'f','g' => '[gд]','h' => '[hн]','j' => 'j','k' => '[kк]','l' => '[l1i!]','z' => 'z','x' => '[xх%]','c' => '[cс]','v' => '[vuи]','b' => '[bвь]','n' => '[nпл]','m' => '[mм]','й' => '[йиu]','ц' => 'ц','у' => '[уy]','е' => '[еeё]','н' => '[нh]','г' => '[гr]','ш' => '[шwщ]','щ' => '[щwш]','з' => '[з3э]','х' => '[хx%]','ъ' => '[ъь]','ф' => 'ф','ы' => '(ы|ь[i1l!]?)','в' => '[вb]','а' => '[аa]','п' => '[пn]','р' => '[рp]','о' => '[оo0]','л' => '[лn]','д' => 'д','ж' => 'ж','э' => '[э3з]','я' => '[я]','ч' => '[ч4]','с' => '[сc]','м' => '[мm]','и' => '[иuй]','т' => '[тt]','ь' => '[ьb]','б' => '[б6]','ю' => '(ю|[!1il][oо0])','ё' => '[ёеe]','1' => '[1il!]','2' => '2','3' => '[3зэ]','4' => '[4ч]','5' => '[5s]','6' => '[6б]','7' => '7','8' => '8','9' => '9','0' => '[0оo]','_' => '_','#' => '#','%' => '[%x]','^' => '[^~]','(' => '[(]',')' => '[)]','=' => '=','.' => '[.]','-' => '-');
        $name=strtolower($name);
        $name=strtr($name, $replace_word);

        $row = $db->super_query ("SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE email = '$email' OR LOWER(name) REGEXP '[[:<:]]{$name}[[:>:]]'");

        if ($row['count']) $Errors[] = $auto->lang['reg_err_8'];
    }

    if ($Errors)
    $auto->Errors = array_merge($auto->Errors, $Errors);
}

function auto_num_format($number)
{
    return number_format($number, 0, ".", " ");
}

if (!function_exists('array_intersect_key'))
{
    function array_intersect_key(array $array1, array $array2)
    {
        foreach ($array1 as $key=>$value)
        {
            if (!array_key_exists($key, $array2))
            unset($array1[$key]);
        }

        return $array1;
    }
}

if (!function_exists('array_map_recursive'))
{
    function array_map_recursive($function, &$data)
    {
        foreach ($data as $i=>$item)
        $data[$i]=is_array($item) ? array_map_recursive($function, $item) : $function($item);
        return $data ;
    }
}

if (!function_exists('array_diff_key'))
{
    function array_diff_key(array $array1, array $array2)
    {
        foreach ($array1 as $key=>$value)
        {
            if (array_key_exists($key, $array2))
            unset($array1[$key]);
        }

        return $array1;
    }
}

if (!function_exists('set_cookie'))
{
    function set_cookie ($name, $value, $expires)
    {
        if ( $expires )
        $expires = time() + ( $expires * 86400 );
        else
        $expires = FALSE;

        if ( PHP_VERSION < 5.2 )
        setcookie ($name, $value, $expires, "/", DOMAIN."; HttpOnly");
        else
        setcookie ($name, $value, $expires, "/", DOMAIN, NULL, TRUE);
    }
}


function UrlParse($url)
{
    if (!$url)
    return array();

    preg_match_all('#(\w+)=([^&]*)#i', $url, $matches);

    if (empty($matches[1]))
    return false;

    foreach ($matches[1] as $key=>$value)
    {
        $array[$value] = $matches[2][$key];
    }

    return $array;
}

function ShowPhoto($id, &$auto)
{
    $photo_one =  "<img src=\"{THEME}/car-market/images/no_photo.jpg\" alt=\"{$auto->tpl->autos[$id]['mark_name']} {$auto->autos[$id]['model_name']}\" >";

    if (!empty($auto->autos[$id]['photos'][0]['id']))
    {
        if (file_exists(UPLOAD_DIR . $auto->autos[$id]['photos'][0]['model_id'] . "/" . $auto->autos[$id]['photos'][0]['image_name']))
        {
            $auto->tpl->SetBlock('exist_photo'); $i = 1;
            $photo_one = "<img src=\"" . UPLOAD_URL . $auto->autos[$id]['photos'][0]['model_id'] . "/thumbs/{$auto->autos[$id]['photos'][0]['image_name']}\" alt=\"{$auto->autos[$id]['mark_name']} {$auto->autos[$id]['model_name']} photo\" >";
            	
            $i++;
        }
    }

    $set_array["{photo}"] = "<a href=\"" . $auto->tpl->GetUrl(array('action' => 'auto', 'id' => $id)) . "\" >" . $photo_one . "</a>";

    return $set_array;
}

function ConvertToRSSURL(&$url)
{
    $url = str_replace("&amp;", "~", $url);
    $url = str_replace("&", "~", $url);
}

function auto_include_lng($file, $varname)
{
    global $config, $$varname;

    if (empty($config['langs']))
    {
        $config['langs'] = 'Russian';
    }
    
    if (isset($config["lang_" . $config['skin']]) and $config["lang_" . $config['skin']] != '')
    {
        require ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . "/$file.lng";
    }
    else
    {
        require(ROOT_DIR . "/language/".$config['langs']."/$file.lng");
    }
}

function RunCron(CarMarketUser &$auto)
{
    if (!$auto->config['general_main_page'])
    {
        $url = $auto->tpl->main_url . "&action=cron";
    }
    else 
    {
        $url = $auto->tpl->main_url . "?action=cron";
    }
    
    if (function_exists('curl_init'))
    {
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 1000);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        curl_exec($curl);
        
        curl_close($curl);
    }
    else
    {
        $stream = fsockopen($url, 80);
        stream_set_timeout(1);
        fclose($stream);
    }
}
?>
