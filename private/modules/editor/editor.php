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

                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'font-awesome-4.7.0/css/font-awesome.min',
                $application_configs['APPLICATION_URL'].$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'].
                    'bootstrap-4.0.0-dist/css/bootstrap.min.css', //#TODO must be only one bootstrap, fix this
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
                $id_project = 0;
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

            $filelist_cache = $application_configs['editor__cache'].$id_project;
            if(file_exists($filelist_cache)){
                $_filelist_ws = file_get_contents($filelist_cache);
            }else{
                $_filelist_ws = $this->_filelist_ws(new WSConsumer, $ws_details);
                file_put_contents($filelist_cache, $_filelist_ws);
            }
        }else{
            $_filelist_ws = false;
        }

        return array(
            'type' => 'view', 
            'response' => $application_configs['PUBLIC_FOLDER'].$application_configs['PUBLIC_FOLDER_MODULES'].$module.'/tmpl/'.$application_configs['tmpl'].$action.'.php', 
            'data' => array(
                'userbean' => $_SESSION['userbean-Q4rp'],
                'project' => $project,
                'wp_admin' => $project['wp_admin'],
                'filelist_ws' => $_filelist_ws
            )
        );
    }

    public function _action_refreshFilelistCacheByProject($application_configs, $module, $action, $post, $optional_parameters){
        if($optional_parameters){
            $parameter_key = $optional_parameters[0];
            if($parameter_key === 'id_project'){
                $id_project = $optional_parameters[1];
            }else{
                $id_project = 0;
            }
        }

        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);

        $ws_details = array(
            'ws_url' => $project['website'].'/'.$getProjectWSDetails['ws_file_list_url'],
            'user' => $getProjectWSDetails['ws_user'],
            'psw' => $getProjectWSDetails['ws_psw'],
        );

        $filelist_cache = $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_DATA'].'editor/cache_'.$id_project;
        $_filelist_ws = $this->_filelist_ws(new WSConsumer, $ws_details);

        file_put_contents($filelist_cache, $_filelist_ws);

        return array(
            'type' => 'ws', 
            'response' => array(
                'filelist_ws' => $_filelist_ws
            )
        );
    }

    public function _action_getFile($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);
        $_supported_by_the_editor = true;
        $_editor_type = 'none';
        $file_content = 'filetype-not-supported-chaslachap';
        $remote__root_folder = '/';

        if(isset($post['get_backup']) && $post['get_backup'] === '1'){
            $url = str_replace('https://', '', $project['website']);
            $file_content = file_get_contents(__DIR__.'/_backup-on-save/'.$url.'/'.$post['file']);
        }else{
            $local__root_folder = $application_configs['editor__temp-file-to-be-uploaded'];
            $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
            $_supported_file = $ftp->_supported_file($remote__root_folder.$local__root_folder.'/'.$post['file']);
            if(isset($_supported_file['supported_file']) && $_supported_file['supported_file']){
                $_editor_type = $_supported_file['editor_type'];
                switch ($_editor_type) {
                    case 'text':
                        $file_content = $ftp->getFileViaFTP($post, $remote__root_folder, $local__root_folder);

                        break;
                    case 'image':
                        $file_content = ''; //#

                        break;

                    default:
                        break;
                }
            }
        }

        if($file_content === 'filetype-not-supported-chaslachap'){ //#TODO improve
            $_supported_by_the_editor = false;
        }

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'file_content' => $file_content,
                'supported_by_the_editor' => $_supported_by_the_editor,
                'editor_type' => $_editor_type
            )
        );
    }
    
    public function _action_getFileHistory($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $page_in_editor = $post['file'];
        $url = str_replace('https://', '', $project['website']);
        $bkup_folder = __DIR__.'/_backup-on-save/'.$url.'/';
        $bkup_files = scandir($bkup_folder);

        foreach ($bkup_files as $f){
            if($this->_startsWith($f, '_'.$url.'___'.$page_in_editor)){
                $ary_file_history[] = '<div class="view_file_in_history_content" data-file="'.$f.'">'.$f.' - '. date("d/m/Y H:i:s",filemtime($bkup_folder.$f)) . '</div>';
            }
        }
        $getFileHistory = array_reverse($ary_file_history);

        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'getFileHistory' => $getFileHistory
            )
        );
    }
    public function _action_setFile($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $this->getProjectID($post);
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

        $remote__root_folder = '/';
        $local__root_folder = $application_configs['editor__temp-file-to-be-uploaded'];

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
            $bkup_file[] = $file[0]['bkup_file'];
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
    
    public function _action_uploadFile($application_configs, $module, $action, $post, $optional_parameters){
        if ( 0 < $_FILES['file']['error'] ) {
            echo 'Error: ' . $_FILES['file']['error'] . '<br>';
        }else{
            $local__root_folder = $application_configs['editor__temp-file-to-be-uploaded'];
            move_uploaded_file($_FILES['file']['tmp_name'], $local__root_folder . $_FILES['file']['name']);
            if($optional_parameters){
                $parameter_key = $optional_parameters[0];
                if($parameter_key === 'id_project'){
                    $id_project = $optional_parameters[1];
                }else{
                    $id_project = 0;
                }
                $post['subfolder'] = $post['subfolder'];
                $filename = $local__root_folder . $_FILES['file']['name'];
                $handle = fopen($filename, "rb");
                $post['data'] = fread($handle, filesize($filename));
                fclose($handle);
                $post['file'] = $_FILES['file']['name'];

                file_put_contents($filename, $post['data']);
            }
            $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
            $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

            $remote__root_folder = '/';

            if(isset($post['uploadType']) && $post['uploadType'] !== ''){
                $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
                switch ($post['uploadType']) {
                    case 'btnUpload':
                        $upload_response = $ftp->setFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website'], $application_configs['db_mng'], $id_project);
                        break;
                    case 'btnUpload_Uncompress':
                        $upload_response = $ftp->setFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website'], $application_configs['db_mng'], $id_project);
                        //#TODO uncompress
                        break;
                    case 'btnUpload_Uncompress_Delete':
                        $upload_response = $ftp->setFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website'], $application_configs['db_mng'], $id_project);
                        //#TODO uncompress and delete
                        break;
                    case 'btnUpload_WP_Theme':
                        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                        echo $local__root_folder.$post['file'];
                        $finfo_file = finfo_file($finfo, $local__root_folder.$post['file']);
                        $upload_response = $ftp->setFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website'], $application_configs['db_mng'], $id_project);
                        //#TODO uncompress in /html_file and delete the original theme file
                        if($finfo_file === 'application/zip'){
                            $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);
                            $ws_details = array(
                                'ws_url' => $project['website'].'/WS-uncompress-jeuastod.php',
                                'user' => $getProjectWSDetails['ws_user'],
                                'psw' => $getProjectWSDetails['ws_psw'],
                            );
                            $password = crypt($getProjectWSDetails['ws_psw'], base64_encode($getProjectWSDetails['ws_psw']));
                            $fields = array(
                                'compressed_filename'
                            );
                            $post_ = 'compressed_filename=theme_.zip';
                            $_uncompressfile_ws = $this->_uncompressfile_ws(new WSConsumer, $ws_details, $fields, $post_);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return array(
            'type' => 'ws', 
            'response' => array(
                'file' => $_FILES['file']['tmp_name'],
                'upload_response' => $upload_response
            )
        );
    }
    public function _action_setDirectory($application_configs, $module, $action, $post, $optional_parameters){
        if($optional_parameters){
            $parameter_key = $optional_parameters[0];
            if($parameter_key === 'id_project'){
                $id_project = $optional_parameters[1];
            }else{
                $id_project = 0;
            }
        }
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

        $remote__root_folder = '/';

        $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
        $upload_response = $ftp->setDirViaFTP($post, $remote__root_folder, $post['newFolder'], $project['website'], $application_configs['db_mng'], $id_project);

        return array(
            'type' => 'ws', 
            'response' => array(
                'upload_response' => $upload_response
            )
        );
    }
    
    public function _action_deleteFile($application_configs, $module, $action, $post, $optional_parameters){
        $local__root_folder = $application_configs['editor__temp-file-to-be-uploaded'];
        if($optional_parameters){
            $parameter_key = $optional_parameters[0];
            if($parameter_key === 'id_project'){
                $id_project = $optional_parameters[1];
            }else{
                $id_project = 0;
            }
            $post['subfolder'] = $post['subfolder'];
        }
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $project);

        $remote__root_folder = '/';

        $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
        $upload_response = $ftp->deleteFileViaFTP($post, $remote__root_folder, $local__root_folder, $project['website'], $application_configs['db_mng'], $id_project);

        return array(
            'type' => 'ws', 
            'response' => array(
                'file' => $post['file'],
                'upload_response' => $upload_response
            )
        );
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

            $postdata = file_get_contents($filename);
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
    private function _startsWith($haystack, $needle){
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}