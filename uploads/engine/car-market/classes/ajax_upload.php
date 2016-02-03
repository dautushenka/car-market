<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    
    protected $_errors = array();
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        return false;
        if ($realSize != $this->getSize()){            
            //return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return urldecode($_REQUEST['filename']);
//        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
    
    public function getErrors()
    {
        return $this->_errors;
    }
}

class UploafFile
{
    protected $_errors = array();

    protected $_varname;
    
    function __construct($varname)
    {
        if (!$varname || empty($_FILES[$varname]))
        {
            throw new ExceptionAllError('Varname is empty');
        }
        
        $this->_varname = $varname;
    }
    
    public function getName()
    {
        return $_FILES[$this->_varname]['name'];
    }
    
    public function getSize()
    {
        return $_FILES[$this->_varname]['size'];
    }
    
    public function save($path)
    {
        if (move_uploaded_file($_FILES[$this->_varname]['tmp_name'], $path))
        {
            return true;
        }
        
        $this->_errors[] = 'Ошибка при перемещении файла';
        
        return false;
    }
    
    public function getErrors()
    {
        return $this->_errors;
    }
}
