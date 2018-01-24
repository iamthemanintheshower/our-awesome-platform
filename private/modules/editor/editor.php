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
 * Description of editor
 *
 * @author imthemanintheshower
 */

class editor extends page{

    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/project.php',
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/filesystem_navigation/filesystem_navigation.php',
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/ftp_mng.php',
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
                    'bootstrap-3.3.7-dist/css/bootstrap.min.css',
                //filesystem_navigation
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'filesystem_navigation/styles/default/default.css',

                //#codemirror
                $code_mirror_base_url.'codemirror.css',
                $code_mirror_base_url.'addon/hint/show-hint.css',

                //#editor
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'editor/tmpl/clear/css/editor.css',
                //#fonts
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].'fonts/SourceSansPro.css'
            )
        ;
        return $this->_getCss($css);
    }
    
    public function getJs($application_configs){
        $code_mirror_base_url = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/';
        
        $js =
            array(
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'jquery/jquery-1.12.4.min.js',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'filesystem_navigation/filesystem_navigation_jquery.js',
                //# code mirror
                $code_mirror_base_url.'codemirror.js',
                $code_mirror_base_url.'addon/hint/show-hint.js',
                $code_mirror_base_url.'addon/hint/xml-hint.js',

                $code_mirror_base_url.'addon/scroll/annotatescrollbar.js',
                $code_mirror_base_url.'addon/search/matchesonscrollbar.js',
                $code_mirror_base_url.'addon/search/match-highlighter.js',

                $code_mirror_base_url.'addon/selection/active-line.js',
                $code_mirror_base_url.'addon/edit/closetag.js',    
                $code_mirror_base_url.'addon/fold/xml-fold.js',
                $code_mirror_base_url.'addon/edit/matchbrackets.js',
                $code_mirror_base_url.'mode/htmlmixed/htmlmixed.js',
                $code_mirror_base_url.'addon/edit/matchtags.js',
                $code_mirror_base_url.'mode/xml/xml.js',
                $code_mirror_base_url.'mode/javascript/javascript.js',
                $code_mirror_base_url.'mode/css/css.js',
                $code_mirror_base_url.'mode/clike/clike.js',
                $code_mirror_base_url.'mode/php/php.js',
                $code_mirror_base_url.'addon/search/search.js',
                $code_mirror_base_url.'addon/search/searchcursor.js',
                $code_mirror_base_url.'addon/search/jump-to-line.js',
                $code_mirror_base_url.'addon/dialog/dialog.js',

                //# editor
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].
                    'editor/script.js'
            )
        ;
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('editor');
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
    
    public function _action_getFile($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

        $remote__root_folder = '/';
        $local__root_folder = $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/_temp-file-to-be-uploaded/';

        $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
        $file_content = $ftp->getFileViaFTP($post, $remote__root_folder, $local__root_folder);


        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'file_content' => $file_content
            )
        );
    }

    
    public function _action_setFile($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

        $remote__root_folder = '/';
        $local__root_folder = $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/_temp-file-to-be-uploaded/';

        $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
        $upload_response = $ftp->setFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website']);


        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'upload_response' => $upload_response
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

    private function getProjectFTPDetails($db_mng, $getProjectData){
        $project = new Project($db_mng);
        $ftp_id_details = $getProjectData['ftp_id_details'];
        return $project->getProjectFTPDetails($ftp_id_details);
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }    
}