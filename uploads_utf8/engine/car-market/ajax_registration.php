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

        $replace_word = array ('e' => '[eеё]', 'r' => '[rг]', 't' => '[tт]', 'y' => '[yу]','u' => '[uи]','i' => '[i1l!]','o' => '[oо0]','p' => '[pр]','a' => '[aа]','s' => '[s5]','w' => 'w','q' => 'q','d' => 'd','f' => 'f','g' => '[gд]','h' => '[hн]','j' => 'j','k' => '[kк]','l' => '[l1i!]','z' => 'z','x' => '[xх%]','c' => '[cс]','v' => '[vuи]','b' => '[bвь]','n' => '[nпл]','m' => '[mм]','й' => '[йиu]','ц' => 'ц','у' => '[уy]','е' => '[еeё]','н' => '[нh]','г' => '[гr]','ш' => '[шwщ]','щ' => '[щwш]','з' => '[з3э]','х' => '[хx%]','ъ' => '[ъь]','ф' => 'ф','ы' => '(ы|ь[i1l!]?)','в' => '[вb]','а' => '[аa]','п' => '[пn]','р' => '[рp]','о' => '[оo0]','л' => '[лn]','д' => 'д','ж' => 'ж','э' => '[э3з]','я' => '[я]','ч' => '[ч4]','с' => '[сc]','м' => '[мm]','и' => '[иuй]','т' => '[тt]','ь' => '[ьb]','б' => '[б6]','ю' => '(ю|[!1il][oо0])','ё' => '[ёеe]','1' => '[1il!]','2' => '2','3' => '[3зэ]','4' => '[4ч]','5' => '[5s]','6' => '[6б]','7' => '7','8' => '8','9' => '9','0' => '[0оo]','_' => '_','#' => '#','%' => '[%x]','^' => '[^~]','(' => '[(]',')' => '[)]','=' => '=','.' => '[.]','-' => '-','[' => '[\[]');
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