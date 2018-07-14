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
 * Description of InputChecker
 *
 * @author imthemanintheshower
 */

class InputChecker {

    public function checkParameters($application_configs, $module, $controller, $action, $post){
        $position = $module.'/'.$controller.'/'.$action;
        $getParametersWhitelist = $this->getParametersWhitelist($position, $application_configs['parameters_whitelist']);
        $post_keys = array_keys($post);

        if($getParametersWhitelist){
            if($this->checkIfThePostKeysAreEqual($post_keys, $getParametersWhitelist)){
                $_checkIfPostDataIsValid = $this->checkIfPostDataIsValid($position, $post, $application_configs['parameters_whitelist']);
                if($_checkIfPostDataIsValid['valid']){
                    $_checkIfTheUsertypeIsAllowedToThePosition = $this->checkIfTheUsertypeIsAllowedToThePosition($application_configs, $position);
                    if($_checkIfTheUsertypeIsAllowedToThePosition['valid']){
                        $_checkAuthorizationOnData = $this->checkAuthorizationOnData($application_configs, $post, $position);
                        return $_checkAuthorizationOnData;
//                        return $_checkIfTheUsertypeIsAllowedToThePosition;
                    }else{
                        return $_checkIfTheUsertypeIsAllowedToThePosition;
                    }
                }else{
                    return $_checkIfPostDataIsValid;
                }
            }else{
                $localization = $this->getLocalization($application_configs['language'], $module, $controller, 'default');
                die('1<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
            }
        }else{
            $localization = $this->getLocalization($application_configs['language'], $module, $controller, 'default');
            die($position.'|<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
        }
    }

    private function getParametersWhitelist($position, $parameters_whitelist){
        if(isset($parameters_whitelist[$position])){
            return $parameters_whitelist[$position];
        }else{
            return false;
        }
    }

    private function checkIfThePostKeysAreEqual($post_keys, $whitelist_keys){
        if($whitelist_keys === 'no-parameters'){return true;}

        if($post_keys === array_keys($whitelist_keys)){return true;}else{return false;}
    }

    private function checkIfPostDataIsValid($form, $post, $parameters_whitelist){
        $_valid = false;
        if(isset($parameters_whitelist[$form])){
            $form_fields = $parameters_whitelist[$form];
            if(is_array($form_fields)){
                foreach ($form_fields as $key => $value){
                    if(is_array($value) && $value['require']['required']){
                        if($post[$key] !== ''){
                            $_valid = true;
                        }else{
                            $_valid = false;
                        }
                        $value_message = $value['require']['message'];
                    }else{
                        $_valid = true;
                        $value_message = '';
                    }
                    if(!$_valid){
                        return array('field' => $key, 'valid' => $_valid, 'message' => $value_message);
                    }

                    if(is_array($value) && $value['valid']['required']){
                        if (preg_match($value['valid']['regular_expression'], $post[$key])){
                            $_valid = true;
                        }else{
                            $_valid = false;
                        }
                        $value_message = $value['valid']['message'];
                    }else{
                        $value_message = '';
                    }
                    if(!$_valid){
                        return array('field' => $key, 'valid' => $_valid, 'message' => $value_message);
                    }
                }
            }
        }
        return array('field' => '', 'valid' => true, 'message' => 'ok');
    }

    private function checkIfTheUsertypeIsAllowedToThePosition($application_configs, $position){
        //#usertypes/positions
        $_valid = false;

        if($position !== 'login/login/index' && $position !== 'login/login/checklogin'){ //#TODO improve this part developing a method that checks the whitelist 
            if(isset($_SESSION) && isset($_SESSION['userbean-Q4rp'])){
                $userbean = unserialize($_SESSION['userbean-Q4rp']);

                $sql_query = 
                    "SELECT * FROM `oap__usertypes_positions` utp LEFT JOIN `oap__positions` p ON utp.position_id = p.id_position ".
                    "WHERE utp.usertype_id='".$userbean->getIdUserType()."' AND p.position='$position'";
                $checkIfTheUsertypeIsAllowedToThePosition = $application_configs['db_mng']->getDataByQuery($sql_query, 'db');

                if(isset($checkIfTheUsertypeIsAllowedToThePosition['response']) && is_array($checkIfTheUsertypeIsAllowedToThePosition['response'])){
                    foreach ($checkIfTheUsertypeIsAllowedToThePosition['response'] as $element){
                        $_valid = true;
                    }
                }
            }

            if($_valid === false){
                return array('field' => $position, 'valid' => $_valid, 'message' => 'Not authorized.'); //#TODO improve this part with the Localization
            }else{
                return array('field' => $position, 'valid' => true, 'message' => 'ok');
            }
        }else{
            return array('field' => $position, 'valid' => true, 'message' => 'ok');
        }
    }

    private function checkAuthorizationOnData($application_configs, $post, $position){
        //#
        $_valid = true;

        switch ($position) {
            case 'application/home/getProject':
                //# TODO Check if the logged user can getProject by checking the id_project parameter
                $project_id = $this->getProjectID($post);

                //# project/tabs
                $selectedTable = 'oap__projects_tabs';
                $selectValues_getTabsByProjectID[] = 'tab_id';

                $userbean = unserialize($_SESSION['userbean-Q4rp']);

                $whereValues[] = array('where_field' => 'project_id', 'where_value' => $project_id);
                $whereValues[] = array('where_field' => 'usertype_id', 'where_value' => $userbean->getIdUserType());

                $getTabsByProjectID = $application_configs['db_mng']->getDataByWhere($selectedTable, $selectValues_getTabsByProjectID, $whereValues);

                if(sizeof($getTabsByProjectID['response']) > 0){
                    return array('field' => $position, 'valid' => true, 'message' => 'ok');
                }else{
                    return array('field' => $position, 'valid' => $_valid, 'message' => 'Not authorized.'); //#TODO improve this part with the Localization
                }
                break;

            case 'application/home/saveNewProject':
                //# projects
                $selectedTable = 'oap__projects';
                $selectValues_getProjectByName[] = 'id_project';

                $whereValues[] = array('where_field' => 'project', 'where_value' => $post['project']);

                $getProjectByName = $application_configs['db_mng']->getDataByWhere($selectedTable, $selectValues_getProjectByName, $whereValues);

                if(sizeof($getProjectByName['response']) === 0){
                    return array('field' => $position, 'valid' => true, 'message' => 'ok');
                }else{
                    return array('field' => $position, 'valid' => $_valid, 'message' => 'Project with the same name already exists'); //#TODO improve this part with the Localization
                }
                break;

            default: //# no check needed, return "ok"
                return array('field' => $position, 'valid' => true, 'message' => 'ok');
        }
        return array('field' => $position, 'valid' => true, 'message' => 'ok');
    }

    private function getLocalization($language, $module, $controller, $action){
        $localization = new localization();
        return $localization->getLocalization($language, $module, $controller, $action);
    }
    private function getProjectID($optional_parameters){
        if($optional_parameters){
            return $optional_parameters['id_project'];
        }else{
            return false;
        }
    }
}
