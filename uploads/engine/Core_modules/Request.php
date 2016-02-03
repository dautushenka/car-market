<?php



class Request
{
    /**
     * Array of variables from $_POST that have already been cleaned
     *
     * @var array
     */
    static public $POST = array();
    
    /**
     * Array of variables from $_GET that have already been cleaned
     *
     * @var array
     */
    static public $GET = array();
    
    /**
     * Array of variables from $_COOKIE that have already been cleaned
     *
     * @var array
     */
    static public $COOKIE = array();
    
    /**
     * Array of variables from $_REQUEST that have already been cleaned
     *
     * @var array
     */
    static public $REQUEST = array();
    
    /**
     * Array of variables from $_SERVER
     *
     * @var array
     */
    static public $SERVER = array();
    
    /**
     * Array of variables from $_SESSION that have already been cleaned
     *
     * @var array
     */
    static public $SESSION = array();
    
    /**
     * Array of variables from $_FILES that have already been cleaned
     *
     * @var array
     */
    static public $FILES = array();
    
    /**
     * Remote ip adress
     *
     * @var string
     */
    static public $IP = '';
    
    /**
     * Check ajax request
     *
     * @var boolean
     */
    static public $AJAX = false;
    
    private function __construct() {}
    
    /**
     * Initialization variables and constant
     *
     */
    static public function init()
    {
        if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc())
		{
			self::stripslashes_deep($_REQUEST); // needed for some reason (at least on php5 - not tested on php4)
			self::stripslashes_deep($_GET);
			self::stripslashes_deep($_POST);
			self::stripslashes_deep($_COOKIE);

			if (is_array($_FILES))
			{
				foreach ($_FILES AS $key => $val)
				{
					$_FILES["$key"]['tmp_name'] = str_replace('\\', '\\\\', $val['tmp_name']);
				}
				self::stripslashes_deep($_FILES);
			}
		}
		set_magic_quotes_runtime(0);
		@ini_set('magic_quotes_sybase', 0);
        
		if ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ||
		     !empty($_REQUEST['rndval']))
		{
		    self::$AJAX = true;
		    
		    // объ€вим константу дл€ старый версий модулей
		    define('AJAX', true);
		}
		else
		{
		    // объ€вим константу дл€ старый версий модулей
		    define('AJAX', false);
		}
		
		foreach ($_COOKIE as $key=>$val)
		{
		    unset($_REQUEST[$key]);
		}
		
		self::$IP = $_SERVER['REMOTE_ADDR'];
    }
    
    static public function &Clean($varname, $vartype = TYPE_NOCLEAN, $r_type = REQUEST)
    {
        if (isset($GLOBALS[$r_type][$varname]))
        {
            $var = $GLOBALS[$r_type][$varname];
            self::CleanVar($var, $vartype, true);
        }
        else
        {
            $var = false;
            self::CleanVar($var, $vartype, false);
        }
        
        self::_add_to_cleaned($varname, $var, $r_type);
        
        return $var;
    }
    
    static private function _add_to_cleaned($varname, $value, $r_type)
    {
        switch ($r_type)
        {
            case POST:
                self::$POST[$varname] = $value;
                break;
                
            case GET:
                self::$GET[$varname] = $value;
                break;
                
            case REQUEST:
                self::$REQUEST[$varname] = $value;
                break;
                
            case COOKIE:
                self::$COOKIE[$varname] = $value;
                break;
                
            case SERVER:
                self::$SERVER[$varname] = $value;
                break;
                
            case SESSION:
                self::$SESSION[$varname] = $value;
                break;
                
            case FILE:
                self::$FILES[$varname] = $value;
                break;
            
            default:
                break;
        }
    }
    
    /**
     * Clean variable in request query
     *
     * @param mixed $var
     * @param integer $vartype
     * @param boolean $isset
     * @return mixed
     */
    static public function &CleanVar(&$var, $vartype = TYPE_NOCLEAN, $isset = true)
    {
        if ($isset)
        {
            if ($vartype < 100)
            {
                self::_do_clean($var, $vartype);
            }
            else if (is_array($var))
            {
                $vartype -= 100;
                
                foreach ($var as &$value)
                {
                    self::_do_clean($value, $vartype);
                }
            }
            else
            {
                $var = array();
            }
            
        }
        else
        {
            if ($vartype > 100)
            {
                $var = array();
            }
            else
            {
                switch ($vartype)
                {
                    case TYPE_BOOL:
                        $var = 0;
                        break;
                        
                    case TYPE_INT:
                    case TYPE_UINT:
                    case TYPE_NUM:
                    case TYPE_UNUM:
                    case TYPE_UNIXTIME:
                        $var = 0;
                        break;
                        
                    case TYPE_STR:
                    case TYPE_NOTRIM:
                    case TYPE_NOHTML:
                        $var = '';
                        break;
                        
                    case TYPE_ARRAY:
                    case TYPE_FILE:
                        $var = array();
                        break;
                        
                    case TYPE_NOCLEAN:
                    default:
                        $var = null;
                        break;
                }
            }
        }
        
        return $var;
    }
    
    static public function &CleanVarsArray(array $array_vars, $r_type = REQUEST)
    {
        $return = array();
        
        foreach ($array_vars as $varname=>$vartype)
        {
            if (isset($GLOBALS[$r_type][$varname]))
            {
                $var = $GLOBALS[$r_type][$varname];
                $return[$varname] = self::CleanVar($var, $vartype, true);
            }
            else
            {
                $var = false;
                $return[$varname] = self::CleanVar($var, $vartype, false);
            }
            self::_add_to_cleaned($varname, $var, $r_type);
        }
        
        return $return;
    }
    
    static public function &CleanArrayVar($varname, array $array_vars, $r_type = REQUEST)
    {
        $return = array();
        
        foreach ($array_vars as $subvarname=>$vartype)
        {
            if (isset($GLOBALS[$r_type][$varname][$subvarname]))
            {
                $var = $GLOBALS[$r_type][$varname][$subvarname];
                $return[$subvarname] = self::CleanVar($var, $vartype, true);
            }
            else
            {
                $var = false;
                $return[$subvarname] = self::CleanVar($var, $vartype, false);
            }
        }
        
        self::_add_to_cleaned($varname, $return, $r_type);
        
        return $return;
    }
    
    static public function &CleanArray(array &$array, array $array_vars)
    {
        $return = array();
        
        foreach ($array_vars as $varname=>$vartype)
        {
            if (isset($array[$varname]))
            {
                $return[$varname] = self::CleanVar($array[$varname], $vartype, true);
            }
            else
            {
                $array[$varname] = null;
                $return[$varname] = self::CleanVar($array[$varname], $vartype, false);
            }
        }
        
        return $return;
    }
    
    static private function _do_clean(&$var, $vartype)
    {
        static $booltypes = array('1', 'yes', 'y', 'true');
        
        switch ($vartype)
        {
            case TYPE_INT:    $var = intval($var);                                          break;
            case TYPE_UINT:   $var = ($var = intval($var)) < 0 ? 0 : $var;                  break;
            case TYPE_NUM:    $var = strval($var) + 0;                                      break;
            case TYPE_UNUM:   $var = strval($var) + 0;
            $var = ($var < 0) ? 0 : $var;                                 break;
            case TYPE_STR:    $var = trim(strval($var));                                    break;
            case TYPE_NOTRIM: $var = strval($var);                                          break;
            case TYPE_NOHTML: $var = htmlspecialchars(trim(strval($var)));              break;
            case TYPE_BOOL:   $var = in_array(strtolower($var), $booltypes) ? 1 : 0;        break;
            case TYPE_ARRAY:  $var = (is_array($var)) ? $var : array();                     break;

            case TYPE_FILE:
                {
                    // perhaps redundant :p
                    if (is_array($var))
                    {
                        if (is_array($var['name']))
                        {
                            $files = count($var['name']);
                            for ($index = 0; $index < $files; $index++)
                            {
                                $var['name']["$index"] = trim(strval($var['name']["$index"]));
                                $var['type']["$index"] = trim(strval($var['type']["$index"]));
                                $var['tmp_name']["$index"] = trim(strval($var['tmp_name']["$index"]));
                                $var['error']["$index"] = intval($var['error']["$index"]);
                                $var['size']["$index"] = intval($var['size']["$index"]);
                            }
                        }
                        else
                        {
                            $var['name'] = trim(strval($var['name']));
                            $var['type'] = trim(strval($var['type']));
                            $var['tmp_name'] = trim(strval($var['tmp_name']));
                            $var['error'] = intval($var['error']);
                            $var['size'] = intval($var['size']);
                        }
                    }
                    else
                    {
                        $var = array(
                        'name'     => '',
                        'type'     => '',
                        'tmp_name' => '',
                        'error'    => 0,
                        'size'     => 4, // UPLOAD_ERR_NO_FILE
                        );
                    }
                    break;
                }
                
            case TYPE_UNIXTIME:
                {
                    if (is_array($var))
                    {
                        $var = self::Clean($var, TYPE_ARRAY_UINT);
                        if ($var['month'] AND $var['day'] AND $var['year'])
                        {
                            $var = mktime($var['hour'], $var['minute'], $var['second'], $var['month'], $var['day'], $var['year']);
                        }
                        else
                        {
                            $var = 0;
                        }
                    }
                    else
                    {
                        $var = ($var = intval($var)) < 0 ? 0 : $var;
                    }
                    break;
                }
                // null actions should be deifned here so we can still catch typos below
            case TYPE_NOCLEAN:
                {
                    break;
                }
                
            case TYPE_STR:
			case TYPE_NOTRIM:
			case TYPE_NOHTML:
				$var = str_replace(chr(0), '', $var);
				break;

            default:
//                
                break;
        }
    }
    
    static private function stripslashes_deep(&$value, $depth = 0)
	{
		if (is_array($value))
		{
		    foreach ($value AS $key => $val)
		    {
		        if (is_string($val))
		        {
		            $value["$key"] = stripslashes($val);
		        }
		        else if (is_array($val) AND $depth < 10)
		        {
		            self::stripslashes_deep($value["$key"], $depth + 1);
		        }
		    }
		}
	}
}

define('POST',    '_POST');
define('GET',     '_GET');
define('REQUEST', '_REQUEST');
define('COOKIE',  '_COOKIE');
define('SERVER',  '_SERVER');
define('SESSION', '_SERVER');
define('FILE',    '_FILES');

define('TYPE_NOCLEAN',  0); // no change
define('TYPE_BOOL',     1); // force boolean
define('TYPE_INT',      2); // force integer
define('TYPE_UINT',     3); // force unsigned integer
define('TYPE_NUM',      4); // force number
define('TYPE_UNUM',     5); // force unsigned number
define('TYPE_UNIXTIME', 6); // force unix datestamp (unsigned integer)
define('TYPE_STR',      7); // force trimmed string
define('TYPE_NOTRIM',   8); // force string - no trim
define('TYPE_NOHTML',   9); // force trimmed string with HTML made safe
define('TYPE_ARRAY',   10); // force array
define('TYPE_FILE',    11); // force file

define('TYPE_ARRAY_BOOL',     101);
define('TYPE_ARRAY_INT',      102);
define('TYPE_ARRAY_UINT',     103);
define('TYPE_ARRAY_NUM',      104);
define('TYPE_ARRAY_UNUM',     105);
define('TYPE_ARRAY_UNIXTIME', 106);
define('TYPE_ARRAY_STR',      107);
define('TYPE_ARRAY_NOTRIM',   108);
define('TYPE_ARRAY_NOHTML',   109);
define('TYPE_ARRAY_ARRAY',    110);

Request::init();

?>