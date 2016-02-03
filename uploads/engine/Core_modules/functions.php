<?php


class Func
{
    /**
     * Object of currecnt module
     * @var object
     */
    static public $obj = null;
    
    /**
     * Resource for file
     * @var resourse
     */
    static private $handler = null;
    
    /**
     * Object of Template class
     * @var object
     */
    static public $tpl = null;
    
    /**
     * Function for Saving config module
     * 
     * @access public
     * @param string $file file for config
     * @param string $config_var config varname 
     * @param array $save_con config for save
     * @return void
     */
    static public function SaveConfig($file, $config_var, array $save_con)
    {
        if (!is_writable($file)) 
        {
            throw new ExceptionAllError("File $file is not for writable");
        }
        
        self::$handler = fopen($file, "w");
        fwrite(self::$handler, "<?php \n\$$config_var = array (\n");

        self::_save_conf($save_con);
        fwrite(self::$handler, ");\n?>");
        fclose(self::$handler);
    }
    
    /**
     * Function for recursiv saving array
     * 
     * @access private
     * @param array $save_con
     * @param boolean $is_array
     * @return void
     */
    static private function _save_conf(array $save_con, $is_array = false)
    {
        foreach($save_con as $name => $value)
        {
            if (is_array($value))
            {
                fwrite(self::$handler, "'{$name}' => array (\n"); self::_save_conf($value, true);
            }
            else
            {
                $value = trim($value);
                $value = addcslashes($value, '"');
                fwrite(self::$handler, "'{$name}' => \"$value\",\n");
            }
        }
        if ($is_array)
        {
            fwrite(self::$handler, "),\n");
        }
    }
    
    /**
     * Select yes or no in admin center
     * @param string $value config var
     * @return string
     */
    static public function YesNo($value)
    {
        return self::$tpl->selection(array(0 => self::$obj->lang['no'], 1 => self::$obj->lang['yes']), "save_con[$value]", intval(self::$obj->config[$value]));
    }
    
    /**
     * Wraping text on max length
     * 
     * @param string &$text link on text
     * @param integer $length
     * @return void
     */
    static public function StringLength(&$text, $length = 150)
    {
        $strings = explode("\n", $text);
    
        $new_text = array();
    
        foreach ($strings as $string)
        {
            if (strlen($string) > $length)
            {
                while (strlen($string) > $length)
                {
                    $i = $length;
                    while (!empty($string{$i}) && $string{$i} != " ")
                    {
                        $i++;
                    }
    
                    if (!empty($string{$i}) && $string{$i} == " ")
                    {
                        $new_text[] = substr($string, 0, $i);
                        $string = substr($string, $i - strlen($string));
                    }
                    else
                    {
                        $new_text[] = $string;
                        break;
                    }
                }
                
                if (strlen($string) < $i)
                {
                    $new_text[] = $string;
                }
            }
            else
            {
                $new_text[] = $string;
            }
        }
    
        return implode("\n", $new_text);
    } 
}

?>