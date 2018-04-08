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
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'ws_consumer/ws_consumer.php'
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
                    'jquery/jquery-3.3.1.min',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-3.3.7-dist/js/bootstrap.min.js',
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

        $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);
        if($getProjectWSDetails){
            $ws_details = array(
                'ws_url' => $project['website'].'/'.$getProjectWSDetails['ws_file_list_url'],
                'user' => $getProjectWSDetails['ws_user'],
                'psw' => $getProjectWSDetails['ws_psw'],
            );
            $_filelist_ws = $this->_filelist_ws(new WSConsumer, $ws_details);
        }else{
            $_filelist_ws = false;
        }

        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php', 
            'data' => array(
                'userbean' => $_SESSION['userbean-Q4rp'],
                'project' => $project,
                'filelist_ws' => $_filelist_ws
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
        $upload_response = $ftp->setFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website'], $application_configs['db_mng'], $id_project);


        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'upload_response' => $upload_response
            )
        );
    }
    

    public function _action_searchStringInFile($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);
        
        $searchstring = $post['searchstring'];

        $user = $getProjectWSDetails['ws_user'];
        $password = $getProjectWSDetails['ws_psw'];
        $ws_find_string_in_file_url = $getProjectWSDetails['ws_find_string_in_file_url'];
        $url = $project['website'].$ws_find_string_in_file_url.'?searchstring='.$searchstring;
        
        $url = str_replace('https://', '', $url);
        $stream = fopen("https://$user:$password@$url", "r");
        $stream_searchStringInFile = stream_get_contents($stream, -1, 0);
        fclose($stream);

        return array(
            'type' => 'ws', 
            'response' => array(
                'stream_searchStringInFile' => $stream_searchStringInFile,
            )
        );
    }

    public function _action_collectEditedFiles($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $website = str_replace('https://', '', $project['website']);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

        $token = $post['token'];

        $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);      
        $get_editorsavelog = $ftp->get_editorsavelog($application_configs['db_mng'], $id_project, $token);

        foreach ($get_editorsavelog as $file){
            $bkup_file[] = $file['bkup_file'];
        }

        $compressed_filename = 'bkup-'.date('d-m-Y_H:i:s').'-'.$website.'-'.$token.'.zip';
        $ftp->_compress_files($application_configs['editor__temp-download-collected-files'].$compressed_filename, $bkup_file);
        
        
        return array(
            'type' => 'ws', 
            'response' => array(
                'get_editorsavelog' => $get_editorsavelog,
                'compressed_filename' => $compressed_filename
            )
        );
    }

    public function _action_collectEditedFilesgetFileZIP($application_configs, $module, $action, $post, $optional_parameters){
        if($optional_parameters){
            $parameter_key = $optional_parameters[0];
            if($parameter_key === 'compressed_filename'){
                $compressed_filename = $optional_parameters[1];
            }else{
                $compressed_filename = 0;;
            }
        }

        if (file_exists($application_configs['editor__temp-download-collected-files'].$compressed_filename)) {
            $contents = file_get_contents($application_configs['editor__temp-download-collected-files'].$compressed_filename);
            header('Content-Length: ' . filesize($application_configs['editor__temp-download-collected-files'].$compressed_filename));
            header('Content-type: application/zip');
            header('Content-Disposition: download; filename="' . $compressed_filename . '"');

            echo $contents;

            $this->_sendToDropbox($application_configs, $compressed_filename);
        }
    }

    private function _sendToDropbox($application_configs, $compressed_filename){
        if (file_exists($application_configs['editor__temp-download-collected-files'].$compressed_filename)) {
            $path = '';
            $headers = array("Content-Type: application/json");
            $endpoint = "https://api.dropboxapi.com/2/files/create_folder_v2";
            $postdata = json_encode(array( "path" => $path, "autorename" => FALSE ));
            $this->_dropbox_postRequest($endpoint, $headers, $postdata, $application_configs);

            $filename = $application_configs['editor__temp-download-collected-files'].$compressed_filename;
            $headers = array('Dropbox-API-Arg: {"path":"/'.$compressed_filename.'", "mode":"add"}','Content-Type: application/octet-stream',);
            $endpoint = 'https://content.dropboxapi.com/2/files/upload';

            $postdata = file_get_contents($filename); //
            $this->_dropbox_postRequest($endpoint, $headers, $postdata, $application_configs);
        }
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

    private function getProjectWSDetails($db_mng, $getProjectData){
        $project = new Project($db_mng);
        $ws_id_details = $getProjectData['ws_id_details'];
        return $project->getProjectWSDetails($ws_id_details);
    }

    public function getInitScript($application_configs, $token){
        //# put here page related scripts
        $this->_getInitScript($application_configs, $token);
    }
    
    private function _dropbox_postRequest($endpoint, $headers, $data, $application_configs){
        $ch = curl_init($endpoint);
        array_push($headers, $application_configs['editor__dropbox_key']);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $r = curl_exec($ch);
        curl_close($ch);

        return json_decode($r, true);
    }

    private function _filelist_ws($WSConsumer, $ws_details){
        return $WSConsumer->filelist_ws($ws_details);
    }
}