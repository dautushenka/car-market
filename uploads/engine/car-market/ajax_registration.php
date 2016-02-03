<?php

require_once ENGINE_DIR.'/modules/functions.php';
require_once ENGINE_DIR.'/classes/parse.class.php';

$parse = new ParseFilter();

if (!function_exists('convert_unicode'))
{
    function convert_unicode($t, $to = 'windows-1251')
    {
        $to = strtolower( $to );

        if( $to == 'utf-8' ) {
            	
            return urldecode( $t );

        } else {
            	
            if( function_exists( 'iconv' ) ) $t = iconv( "UTF-8", $to . "//IGNORE", $t );
            else $t = "The library iconv is not supported by your server";

        }

        return urldecode( $t );
    }

}

function check_name($name)
{
    global $lang, $db;

    $stop = '';

    if (strlen($name) > 20)
    {
        	
        $stop .= $lang['reg_err_3'];
    }
    if (preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/",$name))
    {
        	
        $stop .= $lang['reg_err_4'];
    }
    if (empty($name))
    {
        	
        $stop .= $lang['reg_err_7'];
    }
    if (!$stop)
    {

        $replace_word = array ('e' => '[e�]', 'r' => '[r�]', 't' => '[t�]', 'y' => '[y�]','u' => '[u�]','i' => '[i1l!]','o' => '[o�0]','p' => '[p�]','a' => '[a�]','s' => '[s5]','w' => 'w','q' => 'q','d' => 'd','f' => 'f','g' => '[g�]','h' => '[h�]','j' => 'j','k' => '[k�]','l' => '[l1i!]','z' => 'z','x' => '[x�%]','c' => '[c�]','v' => '[vu�]','b' => '[b��]','n' => '[n��]','m' => '[m�]','�' => '[��u]','�' => '�','�' => '[�y]','�' => '[�e�]','�' => '[�h]','�' => '[�r]','�' => '[�w�]','�' => '[�w�]','�' => '[�3�]','�' => '[�x%]','�' => '[��]','�' => '�','�' => '(�|�[i1l!]?)','�' => '[�b]','�' => '[�a]','�' => '[�n]','�' => '[�p]','�' => '[�o0]','�' => '[�n]','�' => '�','�' => '�','�' => '[�3�]','�' => '[�]','�' => '[�4]','�' => '[�c]','�' => '[�m]','�' => '[�u�]','�' => '[�t]','�' => '[�b]','�' => '[�6]','�' => '(�|[!1il][o�0])','�' => '[��e]','1' => '[1il!]','2' => '2','3' => '[3��]','4' => '[4�]','5' => '[5s]','6' => '[6�]','7' => '7','8' => '8','9' => '9','0' => '[0�o]','_' => '_','#' => '#','%' => '[%x]','^' => '[^~]','(' => '[(]',')' => '[)]','=' => '=','.' => '[.]','-' => '-','[' => '[\[]');
        $name=strtolower($name);
        $search_name=strtr($name, $replace_word);

        $db->query ("SELECT name FROM " . USERPREFIX . "_users WHERE LOWER(name) REGEXP '[[:<:]]{$search_name}[[:>:]]' OR name = '$name'");

        if ($db->num_rows() > 0)
        {
            $stop .= $lang['reg_err_20'];
        }
    }

    if (!$stop) return false; else return $stop;
}



$name  = $db->safesql(trim(htmlspecialchars($parse->process(convert_unicode($_GET['name'], $config['charset'])))));
$allow = check_name($name);

if (!$allow)
echo 'true';
else
echo 'false';

exit;
?>