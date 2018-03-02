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
 * Description of dbadmin
 *
 * @author imthemanintheshower
 */

class dbadmin extends page{

    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/project.php'
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $css = 
            array(
                //#bootstrap
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-3.3.7-dist/css/bootstrap.min.css',

                //#dbadmin
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'dbadmin/tmpl/clear/css/dbadmin.css',
                //#fonts
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].'fonts/SourceSansPro.css',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].'fonts/Inconsolata.css',
                
            )
        ;
        return $this->_getCss($css);
    }
    
    public function getJs($application_configs){
        $js =
            array(
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'jquery/jquery-1.12.4.min.js',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-3.3.7-dist/js/bootstrap.min.js',

                //# dbadmin
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'dbadmin/script.js'
            )
        ;
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('dbadmin');
    }
    
    
    public function _action_index($application_configs, $module, $action, $post, $optional_parameters){
        if($optional_parameters){
            $parameter_key = $optional_parameters[0];
            if($parameter_key === 'id_project'){
                $id_project = $optional_parameters[1];
            }else{
                $id_project = 0;;
            }
        }
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php', 
            'data' => array(
                'userbean' => $_SESSION['userbean-Q4rp'],
                'project' => $project
            )
        );
    }
    
    public function _action_getDBTables($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $encryption = new Encryption($application_configs['encryption_details']);

        //#TODO: following is an example on how to use the stored db credentials (to connect the database) or an external ws

        //# project's db details: direct connection to the database, use the stored db credentials
        $db_id_details = $project['db_id_details'];
        $getProjectDBDetails = $this->getProjectDBDetails($application_configs['db_mng'], $db_id_details);
        $_project_db_mng = new DbMng(
            array(
                'Nrqtx0HHsX' => $getProjectDBDetails['db_server'],
                'VxMO8N5kX4' => $encryption->decrypt($getProjectDBDetails['db_name']),
                'qsPV6EwtzA' => $encryption->decrypt($getProjectDBDetails['db_user']),
                'AQowahicz5' => $encryption->decrypt($getProjectDBDetails['db_psw'])
            )
        );
        $getDBTables = $_project_db_mng->getDataByQuery('show tables', 'db');  //# not using $getDBTables in this example

        //# can't connect to the database server? use the ws...
        $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);
        $__project_db_mng = new DbMng(
            false,
            false,
            array(
                'ws_url' => $project['website'].'/'.$getProjectWSDetails['ws_database_url'],
                'user' => $getProjectWSDetails['ws_user'],
                'psw' => $getProjectWSDetails['ws_psw'],
            )
        );

        $getDBTables_via_ws = $__project_db_mng->getDataByQuery('show tables', 'ws');

        $_show_tables = array();

        foreach ($getDBTables_via_ws['response'] as $element){
            $_show_tables[] = $element['Tables_in_'.$encryption->decrypt($getProjectDBDetails['db_name'])];
        }

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'getDBTables' => $_show_tables
            )
        );
    }
    
    public function _action_getTableDescription($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $tablename = $post['tablename'];
        
        $getTableDescription = $application_configs['db_mng']->getDataByQuery('DESCRIBE `'.$tablename.'`', 'db');

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'getTableDescription' => $getTableDescription
            )
        );
    }

    public function _action_getQueryResult($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $raw_query = $post['raw_query'];

        $getQueryResult = $application_configs['db_mng']->getDataByQuery($raw_query, 'db');

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'getQueryResult' => $getQueryResult
            )
        );
    }

    public function _action_executeInsertQuery($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $tablename = $post['tablename'];
        $inputFields = $post['inputFields'];
        $inputValues = $post['inputValues'];
        $i = 0;
        foreach ($inputValues as $v){
            $_inputValues[] = array('field' => str_replace('__', '', $inputFields[$i]), 'typed_value' => str_replace('__', '', $v['typed_value']));
            $i++;
        }

        $saveDataOnTable = $application_configs['db_mng']->saveDataOnTable($tablename, $_inputValues, 'db', 0);
        

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'saveDataOnTable' => $saveDataOnTable
            )
        );
    }



    private function getProjectID($optional_parameters){
        if($optional_parameters){
            return $optional_parameters['id_project'];
        }else{
            return false;
        }
    }

    private function getProjectByID($db_mng, $project_id){
        $project = new Project($db_mng);
        return $project->getProjectDataByID($project_id);
    }

    private function getProjectWSDetails($db_mng, $getProjectData){
        $project = new Project($db_mng);
        $ws_id_details = $getProjectData['ws_id_details'];
        return $project->getProjectWSDetails($ws_id_details);
    }

    private function getProjectDBDetails($db_mng, $db_id_details){
        $project = new Project($db_mng);
        return $project->getProjectDBDetails($db_id_details);
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }

}
