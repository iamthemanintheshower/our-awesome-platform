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
 * Description of LogViewer
 *
 * @author imthemanintheshower
 */

//# Inspired by https://github.com/iamthemanintheshower/wp-exceptions-mng and the "log_everything" module
class LogViewer extends page{

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
                    'bootstrap-4.0.0-dist/css/bootstrap.min.css',

                //#LogViewer
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'logviewer/tmpl/clear/css/logviewer.css',
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

                //# LogViewer
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'logviewer/script.js'
            )
        ;
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('LogViewer');
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
        $getLogsForViewer = $this->_getLogsForViewer($application_configs);

        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        
        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php', 
            'data' => array(
                'userbean' => $_SESSION['userbean-Q4rp'],
                'getLogsForViewer' => $getLogsForViewer,
                'project' => $project
            )
        );
    }


    private function _getLogsForViewer($application_configs){
        $_getLogsForViewer = file_get_contents($application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_DATA'].
            $application_configs['PRIVATE_FOLDER_LOGS']."log.log");

        $invalid_characters = array("$", "%", "#", "<", ">");
        $_getLogsForViewer = str_replace($invalid_characters, "", $_getLogsForViewer);
        return explode('!', $_getLogsForViewer);
    }

    private function getProjectByID($db_mng, $project_id){
        $project = new Project($db_mng);
        return $project->getProjectDataByID($project_id);
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }

}