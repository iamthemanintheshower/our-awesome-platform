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

$get = $_GET;

include('-application-config.php');

include($application_configs['PRIVATE_FOLDER_CLASSES'].'db_mng.php');
include($application_configs['PRIVATE_FOLDER_CLASSES'].'page.php');
include($application_configs['PRIVATE_FOLDER_CLASSES'].'encryption.php');

include($application_configs['PRIVATE_FOLDER_MODULES'].'errors_mng/errors_mng.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'log_everything/log_everything.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'user/User.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'user/UserBean.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'user/login.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'application/home.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'token/token.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'https_redirect/https_redirect.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'localization/localization.php');
include($application_configs['PRIVATE_FOLDER_MODULES'].'inputchecker/InputChecker.php');

session_start();

if(isset($get['q'])){

    $application_configs['db_mng'] = new DbMng($application_configs['db_details']);

    $post = $_POST;
    $session = $_SESSION;

    $parameters = explode('/', $get['q']);

    $module = _is_not_set_return_index($parameters[0]);
    $controller = _is_not_set_return_index($parameters[1]);
    $action = _is_not_set_return_index($parameters[2]);

    $log_everything = new log_everything();
    $log_everything->log($application_configs, $session, $module, $controller, $action, $parameters, $post);

    $user = new User();
    $user->ifNotLoggedThenLogin($session, $controller, $application_configs);
    $user->ifNotAutorizedThenLogin($session, $module, $controller, $action, $application_configs);

    $optional_parameters = _get_optional_parameters($parameters);

    $inputchecker = new InputChecker();
    $checkParameters = $inputchecker->checkParameters($application_configs, $module, $controller, $action, $post);

    if(!$checkParameters){die();}

    $class_name = &$controller;

    $page = new $class_name();

    //# PHP include
    $page->getFilesToInclude($application_configs);

    $page_response = $page->getResponse($application_configs, $module, $action, $post, $optional_parameters);
    
    switch ($page_response['type']) {
        case 'view':
            $page_data = $page_response['data'];

            include($page_response['response']);

            break;

        case 'ws':
            response($page_response['response']);

            break;

        default:
            break;
    }
}

function _is_not_set_return_index($parameter){
    if(!isset($parameter)){
        return 'index';
    }else{
        return $parameter;
    }
}

function _get_optional_parameters($parameters){
    $optional_parameters = array_slice($parameters,3);
    if(is_array($optional_parameters)){
        return $optional_parameters;
    }else{
        return false;
    }
}

function response($response){
    header("Content-Type: application/json");
    if($response !== ''){
        echo json_encode($response);
    }
    die();
}