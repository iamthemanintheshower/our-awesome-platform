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
 * Description of User
 *
 * @author imthemanintheshower
 */

class User {
    
    private $db_mng;
    
    public function __construct($db_mng = false) {
        $this->db_mng = $db_mng;
    }

    public function login($username, $password, $_loginType){
        switch ($_loginType) {
            case 'db':
                $db = $this->db_mng->getDB();

                $stmt = $db->prepare("SELECT * FROM `oap__users` WHERE email_and_user = :email_and_user AND psw = :password");
                $stmt->execute(array(':email_and_user' => $username, ':password' => $password));

                $row_count = $stmt->rowCount();
                if ($row_count > 0){
                    while ($row = $stmt->fetchObject()) {
                        $userbean = new UserBean();
                        $userbean->setId($row->id_user);
                        $userbean->setEmailAndUser($row->email_and_user);
                        $userbean->setIdUserType($row->usertype_id);

                        $response = $userbean;
                    }
                }else{
                    $response = 'login-error';
                }
                $db = null;
                return $response;

            case 'file':
                
                break;
            default:
                break;
        }
    }
    
    public function getLoggedUserOrFalse($session){
        if(isset($session['userbean-Q4rp']) && null !== $session['userbean-Q4rp'] && null !== unserialize($session['userbean-Q4rp'])->getEmailAndUser() && $session['userbean-Q4rp'] !== ''){
            return $session['userbean-Q4rp'];
        }else{
            return false;
        }
    }
    
    public function ifNotLoggedThenLogin($session, $controller, $application_configs){
        if($controller !== 'login' && !$this->getLoggedUserOrFalse($session)){
            $localization = $this->getLocalization($application_configs['language'], 'default', 'default', 'default');
            die('<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['not-logged'].'</a>');
        }
    }
    
    public function ifNotAutorizedThenLogin($session, $module, $controller, $action, $application_configs){
        //# TO DO
    }

    private function getLocalization($language, $module, $controller, $action){
        $localization = new localization();
        return $localization->getLocalization($language, $module, $controller, $action);
    }

}
