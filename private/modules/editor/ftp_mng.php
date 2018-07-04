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
 * Description of FTP_mng
 *
 * @author imthemanintheshower
 */

class FTP_mng {
    
    private $ftp_details;
    private $application_configs;
    
    public function __construct($ftp_details = false, $application_configs = false) {
        $this->ftp_details = $ftp_details;
        $this->application_configs = $application_configs;
    }

    public function getFileViaFTP($post, $remote__root_folder, $local__root_folder){
        if(isset($post) && isset($post['subfolder']) && isset($post['file']) && isset($post['token'])){
            $subfolder = $this->_cleanSubfolder($post['subfolder']);
            return $this->_ftp_download($remote__root_folder.$subfolder.'/', $local__root_folder, $post['file']);
       }
    }

    public function setDirViaFTP($post, $remote__root_folder, $new_dir, $website, $db_mng, $id_project){
        if(isset($post) && isset($post['subfolder']) && isset($post['token'])){
            $subfolder = $this->_cleanSubfolder($post['subfolder']);
            $bkup_file = $this->_ftp_mkdir($remote__root_folder.$subfolder.'/', $new_dir);
            
            return $bkup_file;
       }
    }

    public function setFileViaFTP($post, $remote__root_folder, $local__root_folder, $website, $db_mng, $id_project){
        if(isset($post) && isset($post['subfolder']) && isset($post['token'])){
            $subfolder = $this->_cleanSubfolder($post['subfolder']);
            $file_to_be_uploaded = $post['file'];
            file_put_contents($local__root_folder.$file_to_be_uploaded, $post['data']);
            $bkup_file = $this->_ftp_upload($remote__root_folder.$subfolder.'/', $local__root_folder, $file_to_be_uploaded, $website);

            if($bkup_file){
                $this->_set_editorsavelog($subfolder, $file_to_be_uploaded, $post['token'], $id_project, $bkup_file, $db_mng);
            }
            
            return $bkup_file;
       }
    }

    public function deleteFileViaFTP($post, $remote__root_folder, $local__root_folder, $website, $db_mng, $id_project){
        if(isset($post) && isset($post['subfolder']) && isset($post['token'])){
            $subfolder = $this->_cleanSubfolder($post['subfolder']);
            $file_to_be_uploaded = $post['file'];
            $bkup_file = $this->_ftp_delete($post, $remote__root_folder.$subfolder.'/', $local__root_folder, $website);

            if($bkup_file){
                $this->_set_editorsavelog($subfolder, $file_to_be_uploaded, $post['token'], $id_project, $bkup_file, $db_mng);
            }
            
            return $bkup_file;
       }
    }

    private function _ftp_download($destination_folder, $local__root_folder, $file_to_be_downloaded){
        $ftp_connection = $this->__ftp_getConnection();
        if($ftp_connection !== FALSE){
            ftp_chdir($ftp_connection, $destination_folder);
            $filename = $local__root_folder.$file_to_be_downloaded;

            if (ftp_get($ftp_connection, $filename, $destination_folder.$file_to_be_downloaded, FTP_ASCII)){
                if($this->_supported_file($filename)){
                    $handle = fopen($filename, "rb");
                    $file_content = fread($handle, filesize($filename));
                    fclose($handle);
                }else{
                    $file_content = '';
                }
            }
        }
        ftp_close($ftp_connection);
        
        return $file_content;
    }

    private function _ftp_upload($destination_folder, $local__root_folder, $file_to_be_uploaded, $website){
        $ftp_connection = $this->__ftp_getConnection();
        if($ftp_connection !== FALSE){
            ftp_chdir($ftp_connection, $destination_folder);

            if(ftp_size($ftp_connection, $file_to_be_uploaded) !== -1){
                $bkup_file = $this->__ftp_make_backup($destination_folder, $file_to_be_uploaded, $website);
            }
            if (ftp_put($ftp_connection, $file_to_be_uploaded, $local__root_folder.$file_to_be_uploaded, FTP_BINARY)){
                ftp_close($ftp_connection);
                return $local__root_folder.$file_to_be_uploaded;
            }else{
                ftp_close($ftp_connection);
                return false;
            }
        }
    }

    private function _ftp_mkdir($destination_folder, $new_dir){
        $ftp_connection = $this->__ftp_getConnection();
        if($ftp_connection !== FALSE){
            ftp_chdir($ftp_connection, $destination_folder);

            if (ftp_mkdir($ftp_connection, $destination_folder.'/'.$new_dir)){
                ftp_close($ftp_connection);
                return $destination_folder.'/'.$new_dir;
            }else{
                ftp_close($ftp_connection);
                return false;
            }
        }
    }

    private function _ftp_delete($post, $destination_folder, $local__root_folder, $website){
        $ftp_connection = $this->__ftp_getConnection();
        if($ftp_connection !== FALSE){
            ftp_chdir($ftp_connection, $destination_folder);

            if (ftp_delete($ftp_connection, $post['file'])){
                ftp_close($ftp_connection);
                return $local__root_folder.$post['file'];
            }else{
                ftp_close($ftp_connection);
                return false;
            }
        }
    }

    private function __ftp_getConnection(){
        $encryption = new Encryption($this->application_configs['encryption_details']);
        $ftp_host = $this->ftp_details['ftp_host'];
        $ftp_user = $encryption->decrypt($this->ftp_details['ftp_user']);
        $ftp_psw = $encryption->decrypt($this->ftp_details['ftp_psw']);

        $conn = ftp_connect($ftp_host) or die("Couldn't connect to $ftp_host"); 
        if (ftp_login($conn, $ftp_user, $ftp_psw)) { return $conn; }else{ return false; }
    }

    private function _cleanSubfolder($subfolder){
        //#adapt it when necessary
        $subfolder = str_replace('/web/htdocs/', '', $subfolder);
        $subfolder = str_replace('/home', '', $subfolder);
        
        return $subfolder;
    }

    private function _set_editorsavelog($subfolder, $file_name, $token, $id_project, $bkup_file, $db_mng){
        $inputValues[] = array('field' => 'folder', 'typed_value' => $subfolder);
        $inputValues[] = array('field' => 'filename', 'typed_value' => $file_name);
        $inputValues[] = array('field' => 'token', 'typed_value' => $token);
        $inputValues[] = array('field' => 'bkup_file', 'typed_value' => $bkup_file);
        $inputValues[] = array('field' => 'id_project', 'typed_value' => $id_project);

        $db_mng->saveDataOnTable('oap__editorsavelog', $inputValues, 'db', 0);
    }

    public function get_editorsavelog($db_mng, $id_project, $token){

        $getEditorSaveLog__query = 
            'SELECT id_editorsavelog, filename, folder, bkup_file, token, `insert` FROM  `oap__editorsavelog` WHERE id_project = "'.$id_project.'" AND token = "'.$token.'" GROUP BY filename, folder';

        $getEditorSaveLog = $db_mng->getDataByQuery($getEditorSaveLog__query, 'db');

        $ids_max = $getEditorSaveLog['response'];

        foreach ($ids_max as $id_max){
            $max__id_editorsavelog[] = $id_max['max__id_editorsavelog'];
        }
        
        $query = 'SELECT id_editorsavelog, filename, folder, bkup_file, token, `insert` FROM `oap__editorsavelog` WHERE id_project = "'.$id_project.'" AND token = "'.$token.'" ';
        $query .= ' AND (';
        $i = 1;
        foreach ($max__id_editorsavelog as $id){
            if($i < count($max__id_editorsavelog)){
                $query .=  'id_editorsavelog = '.$id.' OR ';
            }else{
                $query .=  'id_editorsavelog = '.$id.' )';
            }
            $i++;
        }

        $getEditorSaveLog = $db_mng->getDataByQuery(
            $query, 'db'
        );
        return $getEditorSaveLog['response'];
    }
    
    private function __ftp_make_backup($destination_folder, $file_to_be_uploaded__local, $website){

        $application_configs = $this->application_configs;
        $website = str_replace('https://', '', $website);
        $destination_folder = str_replace('/', '_', $destination_folder);

        $ext = pathinfo($application_configs['editor__temp-file-to-be-uploaded'].$file_to_be_uploaded__local, PATHINFO_EXTENSION);

        $new_file = $destination_folder.'__'.$file_to_be_uploaded__local.'-bkup-'.date('d-m-Y_H:i:s').'.'.$ext;

        if(file_exists($application_configs['editor__temp-file-to-be-uploaded'].$file_to_be_uploaded__local)){
            if (copy($application_configs['editor__temp-file-to-be-uploaded'].$file_to_be_uploaded__local, $application_configs['editor__backup-on-save'].$website.'/'.$new_file)) {
                return $application_configs['editor__backup-on-save'].$website.'/'.$new_file;
            }else{
                return false;
            }
        }
    }
    
    public function _compress_files($compressed_filename, $files){
        $zip = new ZipArchive;

        if ($zip->open($compressed_filename, ZipArchive::CREATE)!== TRUE) {
            exit("cannot open <$compressed_filename>\n");
        }

        foreach ($files as $file){
            $zip->addFile($file);
        }
        
        $zip->close();
    }
    private function _supported_file($file){
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $finfo_file = finfo_file($finfo, $file);
        switch ($finfo_file) {
            case 'text/x-php':
            case 'text/php':
            case 'plain/text':
            case 'text/html':
                return true;

                break;

            default:
                break;
        }
    }
}
