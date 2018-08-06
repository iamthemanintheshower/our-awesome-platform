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
 * Description of Home
 *
 * @author imthemanintheshower
 */

class home extends page{

    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(                
//                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'', //# class
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $css = 
            array(
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-3.3.7-dist/css/bootstrap.min.css',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'application/tmpl/clear/css/application.css',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].'fonts/SourceSansPro.css'
            )
        ;
        return $this->_getCss($css);
    }
    
    public function getJs($application_configs){
        $js =
            array(
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'jquery/jquery-3.3.1.min.js',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'application/script.js'
            )
        ;
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('application');
    }
    
    
    public function _action_index($application_configs, $module, $action, $post, $optional_parameters){

        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php', 
            'data' => array(
                'userbean' => $_SESSION['userbean-Q4rp']
            )
        );
    }

    public function _action_getData($application_configs, $module, $action, $post, $optional_parameters){
        //# Make a class to deal with the DB
        //# and send the result to the Javascript
        $_class = new Object();

        return array(
            'type' => 'ws', 
            'response' => array(
                'class' => $_class
            )
        );
    }


    public function getInitScript($application_configs, $token){
        $this->_getInitScript($application_configs, $token);
    }
}
