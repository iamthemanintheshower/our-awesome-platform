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
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/project.php',
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/project_group.php',
                $application_configs['PRIVATE_FOLDER_CLASSES'].'button.php'
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $css = 
            array(
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/css/bootstrap.min.css',
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
                    'jquery/jquery-3.3.1.min',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/js/bootstrap.min.js',
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
                'button' => new Button(),
                'userbean' => $_SESSION['userbean-Q4rp']
            )
        );
    }

    public function _action_getProjectGroups($application_configs, $module, $action, $post, $optional_parameters){
        $button = new Button();
        $getProjectGroups = $this->getProjectGroups($application_configs['db_mng']);
        $groups = 'no-groups';

        if(is_array($getProjectGroups)){
            $groups = array();
            foreach ($getProjectGroups as $group){
                $groups[] = $this->getProjectByID($application_configs['db_mng'], $group['id_group']);
            }
            foreach ($getProjectGroups as $group){
                $group_buttons[] = $button->getResponse($group['project_group'], 'id_'.$group['id_group'], 'group-button', 'data-id_group="'.$group['id_group'].'"');
            }
        }

        $group_buttons[] = $button->getResponse('New group', 'id_group', 'group-button', 'data-id_group="new_group"');
        
        return array(
            'type' => 'ws', 
            'response' => array(
                'groups' => $groups,
                'group_buttons' => $group_buttons
            )
        );
    }

    public function _action_getProjectsByGroupID($application_configs, $module, $action, $post, $optional_parameters){
        $button = new Button();
        $group_id = $post['group_id'];
        $projects_id_by_group_id = $this->getProjectsIDByGroupID($application_configs['db_mng'], $group_id);
        $projects = 'no-projects';

        if(is_array($projects_id_by_group_id)){
            $projects = array();
            foreach ($projects_id_by_group_id as $project_id){
                $projects[] = $this->getProjectByID($application_configs['db_mng'], $project_id['project_id']);
            }
            foreach ($projects as $project){
                $project_buttons[] = $button->getResponse($project['project'], 'id_'.$project['id_project'], 'project-button', 'data-id_project="'.$project['id_project'].'"');
            }
        }else{
            
        }

        $project_buttons[] = $button->getResponse('New project', 'id_newproject', 'project-button', 'data-id_project="new_project"');
        
        return array(
            'type' => 'ws', 
            'response' => array(
                'projects' => $projects,
                'project_buttons' => $project_buttons
            )
        );
    }

    public function _action_getProject($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $tabs = $this->getTabsByProjectID($application_configs['db_mng'], $project['id_project']);
        
        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'tabs' => $tabs,
            )
        );
    }
    
    private function getProjectGroupID($optional_parameters){
        if($optional_parameters){
            $parameter_key = $optional_parameters[0];
            if($parameter_key === 'id_group'){
                return $optional_parameters[1];
            }else{
                return false;
            }
        }
    }

    private function getProjectsIDByGroupID($db_mng, $group_id){
        $project_group = new ProjectGroup($db_mng);
        $_getProjectsIDByGroupID = $project_group->getProjectsIDByGroupID($group_id);
        if($_getProjectsIDByGroupID){
            return $_getProjectsIDByGroupID;
        }else{
            return 'no-project-in-group';
        }
    }

    private function getProjectGroups($db_mng){
        $project_group = new ProjectGroup($db_mng);
        return $project_group->getProjectGroups();
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
    
    private function getTabsByProjectID($db_mng, $project_id){
        $project = new Project($db_mng);
        return $project->getTabsByProjectID($project_id);
    }
    
    public function getInitScript($application_configs, $token){
        $this->_getInitScript($application_configs, $token);
    }
}