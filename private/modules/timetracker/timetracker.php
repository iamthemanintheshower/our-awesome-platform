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
 * Description of timetracker
 *
 * @author imthemanintheshower
 */

class timetracker extends page{

    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/project.php'
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $code_mirror_base_url = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/';
        $css = 
            array(
                //#bootstrap
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/css/bootstrap.min.css',

                //#timetracker
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'timetracker/tmpl/clear/css/timetracker.css',
                //#fonts
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].'fonts/SourceSansPro.css',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].'fonts/Inconsolata.css',
                
            )
        ;
        return $this->_getCss($css);
    }
    
    public function getJs($application_configs){
        $code_mirror_base_url = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/';
        
        $js =
            array(
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'jquery/jquery-3.3.1.min',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/js/bootstrap.min.js',

                //# timetracker
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'timetracker/script.js'
            )
        ;
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('timetracker');
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
        $getTimeTracker = $this->_getTimeTracker($application_configs['db_mng'], $id_project);
        $project = $this->_getProjectByID($application_configs['db_mng'], $id_project);

        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php', 
            'data' => array(
                'userbean' => $_SESSION['userbean-Q4rp'],
                'project' => $project,
                'getTimeTracker' => $getTimeTracker
            )
        );
    }

    public function _action_trackProjectAndAction($application_configs, $module, $action, $post, $optional_parameters){
        $project_id = $this->_getProjectID($post);
        $tab_id = $this->_getTabID($post);

        $_trackProjectAction = $this->_trackProjectAction($project_id, $tab_id, $application_configs['db_mng']);

        return array(
            'type' => 'ws', 
            'response' => array(
                '_trackProjectAction' => $_trackProjectAction
            )
        );
    }


    private function _getProjectID($optional_parameters){
        if($optional_parameters){
            return $optional_parameters['current_project'];
        }else{
            return false;
        }
    }

    private function _getTabID($optional_parameters){
        if($optional_parameters){
            return $optional_parameters['current_action'];
        }else{
            return false;
        }
    }

    private function _getProjectByID($db_mng, $project_id){
        $project = new Project($db_mng);
        return $project->getProjectDataByID($project_id);
    }

    private function _getTimeTracker($db_mng, $project_id){
        $project = new Project($db_mng);
        return $project->getProjectTimetracker($project_id);
    }

    private function _trackProjectAction($project_id, $tab_id, $db_mng){
        $inputValues[] = array('field' => 'project_id', 'typed_value' => $project_id);
        $inputValues[] = array('field' => 'tab_id', 'typed_value' => $tab_id);

        return $db_mng->saveDataOnTable('oap__timetracker', $inputValues, 'db', 0);
    }


    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }

}