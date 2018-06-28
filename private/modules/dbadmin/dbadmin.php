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
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/project.php',
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'ws_consumer/ws_consumer.php'
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $css = 
            array(
                //#bootstrap
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/css/bootstrap.min.css',

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
                    'jquery/jquery-3.3.1.min',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/js/bootstrap.min.js',

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
        $sql_query = 'show tables';


        $getDBTables_via_ws = $this->getProjectDBMng($application_configs, $project)->getDataByQuery($sql_query, 'ws');
        
        $_show_tables = array();

        foreach ($getDBTables_via_ws['response'] as $element){
            $_show_tables[] = $element['Tables_in_'.$encryption->decrypt($getProjectDBDetails['db_name'])];
        }

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'db_name' => $encryption->decrypt($getProjectDBDetails['db_name']),
                'getDBTables' => $_show_tables
            )
        );
    }
    
    public function _action_getTableDescription($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $tablename = $post['tablename'];
        $sql_query = 'DESCRIBE `'.$tablename.'`';

        $getDBTables_via_ws = $this->getProjectDBMng($application_configs, $project)->getDataByQuery($sql_query, 'ws');
        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'getTableDescription' => $getDBTables_via_ws
            )
        );
    }

    public function _action_getQueryResult($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $raw_query = $post['raw_query'];

        $getQueryResult = $this->getProjectDBMng($application_configs, $project)->getDataByQuery($raw_query, 'ws');
        $this->_log_executed_query($application_configs['db_mng'], $raw_query, $id_project, null, 'db');

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

        $saveDataOnTable = $this->_executeQuery($application_configs, $post, $project, $id_project);

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'saveDataOnTable' => $saveDataOnTable
            )
        );
    }

    public function _action_executeUpdateQuery($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $saveDataOnTable = $this->_executeQuery($application_configs, $post, $project, $id_project);

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'saveDataOnTable' => $saveDataOnTable
            )
        );
    }

    public function _action_getTableQueryHistory($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);

        //# oap__dbadminexecutedqueries
        $selectedTable = 'oap__dbadminexecutedqueries';
        $selectValues_getExecutedQueriesByProjectID[] = 'id_executed_query';
        $selectValues_getExecutedQueriesByProjectID[] = 'executed_query';
        $selectValues_getExecutedQueriesByProjectID[] = 'query_values';
        $selectValues_getExecutedQueriesByProjectID[] = 'insert_time';

        $whereValues[] = array('where_field' => 'project_id', 'where_value' => $id_project);
        $orderBy = 'id_executed_query DESC';

        $getExecutedQueriesByProjectID = $application_configs['db_mng']->getDataByWhere($selectedTable, $selectValues_getExecutedQueriesByProjectID, $whereValues, $orderBy);

        return array(
            'type' => 'ws', 
            'response' => array(
                'getExecutedQueriesByProjectID' => $getExecutedQueriesByProjectID
            )
        );
    }

    public function _action_downloaddatabase($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $getDBDump = $this->getProjectDBMng($application_configs, $project)->getDBDump('ws');

        return array(
            'type' => 'ws', 
            'response' => array(
                'getDBDump' => $getDBDump
            )
        );
    }

    private function _executeQuery($application_configs, $post, $project, $id_project){
        $tablename = $post['tablename'];
        $inputFields = $post['inputFields'];
        $inputValues = $post['inputValues'];
        $prefix = $post['prefix'];

        $i = 0;
        foreach ($inputValues as $v){
            $_inputValues[] = array('field' => str_replace($prefix.'__', '', $inputFields[$i]), 'typed_value' => str_replace($prefix.'__', '', $v['typed_value']));
            $i++;
        }

        if(isset($post['field_id__update']) && $post['field_id__update'] !== ''){
            $_insert_update = 1;
            $_inputValues[] = array('where_field' => $post['field_id__update'], 'where_value' => $post['row_id']);
        }else{
            $_insert_update = 0;
        }

        $this->_log_executed_query($application_configs['db_mng'], $tablename, $id_project, $_inputValues, 'db');

        return $this->getProjectDBMng($application_configs, $project)->saveDataOnTable($tablename, $_inputValues, 'ws', $_insert_update);

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
    private function getProjectDBMng($application_configs, $project){
        //# can't connect to the database server? use the ws...
        $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);

        return new DbMng(
            $application_configs['db_details'],
            false,
            array(
                'ws_url' => $project['website'].'/'.$getProjectWSDetails['ws_database_url'],
                'user' => $getProjectWSDetails['ws_user'],
                'psw' => $getProjectWSDetails['ws_psw'],
                'WSConsumer' => new WSConsumer()
            )
        );
    }
    private function getProjectDBDetails($db_mng, $db_id_details){
        $project = new Project($db_mng);
        return $project->getProjectDBDetails($db_id_details);
    }
    private function _log_executed_query($db_mng, $executed_query, $project_id, $query_values, $_dbType){
        $inputValues[] = array('field' => 'executed_query', 'typed_value' => $executed_query);
        $inputValues[] = array('field' => 'project_id', 'typed_value' => $project_id);
        $inputValues[] = array('field' => 'query_values', 'typed_value' => print_r($query_values, true));

        return $db_mng->saveDataOnTable('oap__dbadminexecutedqueries', $inputValues, 'db', 0);
    }
    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }

}