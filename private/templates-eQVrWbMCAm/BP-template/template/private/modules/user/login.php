<?php
/*
MIT License

Copyright (c) 2018 https://github.com/iamthemanintheshower

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
 * Description of login
 *
 * @author imthemanintheshower
 */

class login extends page{

    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(
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
                    'login/tmpl/clear/css/login.css',
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
                    'login/script.js'
            )
        ;
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('login');
    }
    
    
    public function _action_index($application_configs, $module, $action){
        if(isset($_SESSION) && isset($_SESSION['userbean-Q4rp'])){session_destroy();header("Refresh:0");}
        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php',
            'data' => array()
        );
    }

    public function _action_checklogin($application_configs, $module, $action, $post){
        if(isset($post) && is_array($post) && isset($post['token'])){
            try {
                if(isset($post['username']) && isset($post['password'])){
                    $username = $post['username'];
                    $password = md5($post['password']);

                    $user = new User($application_configs['db_mng']);
                    $login_response = $user->login($username, $password, 'db', $application_configs);
                    if($login_response !== 'login-error'){
                        if($login_response->getEmailAndUser() === $username){
                            $_SESSION['userbean-obj'] = $login_response;
                            $_SESSION['userbean-Q4rp'] = serialize($login_response);
                            $response = unserialize($_SESSION['userbean-Q4rp'])->getEmailAndUser();
                        }else{
                            $_SESSION['userbean-Q4rp'] = '';
                            $response = 'login-error';
                        }
                    }else{
                        $_SESSION['userbean-Q4rp'] = '';
                        $response = 'login-error';
                    }
                }else{
                    $response = 'empty';
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
                $response = 'getMessage';
            }
        }else{
            $response = 'no-token';
        }
        return array('type' => 'ws', 'response' => $response);
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $localization = $this->getLocalization($application_configs['language'], '', '', 'login');
        $page_related_scripts = 'var empty = "'.$localization['empty'].'";';
        $page_related_scripts .= 'var email_or_password_error = "'.$localization['email_or_password_error'].'";';
        $this->_getInitScript($application_configs, $token, $page_related_scripts);
    }
}
