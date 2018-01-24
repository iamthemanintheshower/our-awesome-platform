<?php
/*
MIT License

Copyright (c) 2017 https://github.com/iamthemanintheshower

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
/**
 * Description of DbMng
 *
 * @author imthemanintheshower
 */

class errors_mng extends page{

    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(
                
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $css = array();
        return $this->_getCss($css);
    }
    
    public function getJs($application_configs){
        $js = array();
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('editor');
    }
    
    
    public function _action_log($application_configs, $module, $action, $post, $optional_parameters){
        //TODO
        return array(
            'type' => 'ws', 
            'response' => array()
        );
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }
}

set_error_handler('myErrorHandler');

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {return false;}

    $date = new DateTime();
    switch ($errno) {
        case E_USER_ERROR:
            error_log('!PHP-ERROR|'.$date->getTimestamp().'|'.$errno.'|'.$errstr.'|'.$errline.'|'.$errfile, 3, __DIR__."/logs/php-exception.log");
            exit(1);
            break;

        case E_USER_WARNING:
            error_log('!PHP-WARNING|'.$date->getTimestamp().'|'.$errno.'|'.$errstr.'|'.$errline.'|'.$errfile, 3, __DIR__."/logs/php-exception.log");
            break;

        case E_USER_NOTICE:
            error_log('!PHP-NOTICE|'.$date->getTimestamp().'|'.$errno.'|'.$errstr.'|'.$errline.'|'.$errfile, 3, __DIR__."/logs/php-exception.log");
            break;

        default:
            $str_error = '!PHP-DEFAULT|'.$date->getTimestamp().'|'.$errno.'|'.$errstr.'|'.$errline.'|'.$errfile;
            error_log($str_error, 3, __DIR__."/logs/php-exception.log");
            die($str_error);
            break;
    }
    return true;
}