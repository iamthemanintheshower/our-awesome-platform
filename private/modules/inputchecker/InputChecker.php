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
        $getParametersWhitelist = $this->getParametersWhitelist($position);
        $post_keys = array_keys($post);

        if($getParametersWhitelist){
            if($this->checkIfThePostKeysAreEqual($post_keys, $getParametersWhitelist)){
                return true;
            }else{
                $localization = $this->getLocalization($application_configs['language'], $module, $controller, 'default');
                die('<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
            }
        }else{
            $localization = $this->getLocalization($application_configs['language'], $module, $controller, 'default');
            die($position.'|<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
        }
    }

    private function getParametersWhitelist($position){
        $parameters_whitelist = array(
            'errors_mng/errors_mng/log' => 'no-parameters',
            'application/home/index' => 'no-parameters',
            'application/home/getProject' => 'no-parameters',
            'application/home/getProjectsByGroupID' => 'no-parameters',
            'application/home/getProjectGroups' => 'no-parameters',
            'editor/editor/index' => 'no-parameters',
            'editor/editor/refreshFilelistCacheByProject' => 'no-parameters',
            'editor/editor/getFile' => 'no-parameters',
            'editor/editor/getFileHistory' => 'no-parameters', //#TODO
            'editor/editor/setFile' => 'no-parameters',
            'editor/editor/searchStringInFile' => array(
                'id_project', 'searchstring', 'token'
            ),
            'editor/editor/collectEditedFiles' => array(
                'id_project', 'token'
            ),
            'editor/editor/sendToDropbox' => array(
                'id_project', 'send_to_dropbox__files', 'token'
            ),
            'editor/editor/collectEditedFilesgetFileZIP' => 'no-parameters',
            'editor/editor/uploadFile' => 'no-parameters', //#TODO
            'editor/editor/deleteFile' => 'no-parameters', //#TODO
            'editor/editor/setDirectory' => 'no-parameters', //#TODO
            
            'dbadmin/dbadmin/index' => 'no-parameters',
            'dbadmin/dbadmin/getDBTables' => array(
                'id_project', 'token'
            ),
            'dbadmin/dbadmin/getTableDescription' => array(
                'id_project', 'tablename', 'token'
            ),
            'dbadmin/dbadmin/downloaddatabase' => 'no-parameters', //#TODO
            
            'dbadmin/dbadmin/getQueryResult' => array(
                'id_project', 'raw_query', 'token'
            ),
            'dbadmin/dbadmin/executeInsertQuery' => array(
                'id_project', 'tablename', 'inputFields', 'inputValues', 'prefix', 'token'
            ),
            'dbadmin/dbadmin/executeUpdateQuery' => 'no-parameters', //#TODO
            'dbadmin/dbadmin/getTableQueryHistory' => array(
                'id_project', 'token'
            ),

            'timetracker/timetracker/trackProjectAndAction' => array(
                'token', 'current_project', 'current_action'
            ),
            'timetracker/timetracker/index' => 'no-parameters',

            'login/login/index' => 'no-parameters',
            'login/login/checklogin' => array(
                'token', 'username', 'password'
            )
        );
        if(isset($parameters_whitelist[$position])){
            return $parameters_whitelist[$position];
        }else{
            return false;
        }
    }
    
    private function checkIfThePostKeysAreEqual($post_keys, $whitelist_keys){
        if($whitelist_keys === 'no-parameters'){return true;}
        if($post_keys === $whitelist_keys){return true;}else{return false;}
    }


    private function getLocalization($language, $module, $controller, $action){
        $localization = new localization();
        return $localization->getLocalization($language, $module, $controller, $action);
    }

}
