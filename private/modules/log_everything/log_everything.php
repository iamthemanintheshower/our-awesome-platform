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

class log_everything {

    public function log($application_configs, $session, $module, $controller, $action, $parameters, $post){
        $date = new DateTime();
        $user_id = '';
        if(isset($session) && isset($session['userbean-Q4rp'])){
            $userbean = unserialize($session['userbean-Q4rp']);
            $user_id = $userbean->getId();
        }
        error_log(
            'time:'.$date->getTimestamp().'|'.
            'user_id:'.$user_id.'|'.
            'session:'.print_r($session, true).'|'.
            'module:'.$module.'|'.
            'controller:'.$controller.'|'.
            'action:'.$action.'|'.
            'optional_parameters:'.print_r(array_slice($parameters, 3), true).'|'.
            'post:'.print_r($post, true).'|'
            , 3, 
            $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_DATA'].
                $application_configs['PRIVATE_FOLDER_LOGS']."log.log"
        );
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }
}