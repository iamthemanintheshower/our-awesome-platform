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
            $file_to_be_downloaded = $post['file'];
            return $this->_ftp_download($remote__root_folder.$subfolder.'/', $local__root_folder, $file_to_be_downloaded);    
       }
    }

    public function setFileViaFTP($post, $remote__root_folder, $local__root_folder, $website){
        if(isset($post) && isset($post['subfolder']) && isset($post['token'])){
            $subfolder = $this->_cleanSubfolder($post['subfolder']);
            $data = $post['data'];
            $file_to_be_uploaded = $post['file'];

            file_put_contents($local__root_folder.$file_to_be_uploaded, $data);

            return $this->_ftp_upload($remote__root_folder.$subfolder.'/', $local__root_folder, $file_to_be_uploaded, $website);

            //TODO: log query
       }
    }

    private function _ftp_download($destination_folder, $local__root_folder, $file_to_be_downloaded){
        $ftp_connection = $this->__ftp_getConnection();
        if($ftp_connection !== FALSE){
            ftp_chdir($ftp_connection, $destination_folder);

            if (ftp_get($ftp_connection, $local__root_folder.$file_to_be_downloaded, $destination_folder.$file_to_be_downloaded, FTP_ASCII)){
                $file_content = file_get_contents($local__root_folder.$file_to_be_downloaded);
            }
        }
        ftp_close($ftp_connection);
        
        return $file_content;
    }
    private function _ftp_upload($destination_folder, $local__root_folder, $file_to_be_uploaded, $website){
        $ftp_connection = $this->__ftp_getConnection();
        if($ftp_connection !== FALSE){
            ftp_chdir($ftp_connection, $destination_folder);
//TODO:            if(ftp_size($ftp_connection, $file_to_be_uploaded) !== -1){
//TODO:                $this->__ftp_make_backup($ftp_connection, $file_to_be_uploaded, $website);
//TODO:            }

            if (ftp_put($ftp_connection, $file_to_be_uploaded, $local__root_folder.$file_to_be_uploaded, FTP_ASCII)){
                ftp_close($ftp_connection);
                return "Successfully uploaded.";
            }else{
                ftp_close($ftp_connection);
                return "Error uploading.";
            }
        }
    }
    private function __ftp_getConnection(){
        $encryption = new Encryption($this->application_configs['encryption_details']);
        $ftp_server = $this->ftp_details['ftp_server'];
        $ftp_user = $encryption->decrypt($this->ftp_details['ftp_user']);
        $ftp_psw = $encryption->decrypt($this->ftp_details['ftp_psw']);

        $conn = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
        if (ftp_login($conn, $ftp_user, $ftp_psw)) { return $conn; }else{ return false; }
    }
    private function _cleanSubfolder($subfolder){
        $subfolder = str_replace('/web/htdocs/', '', $subfolder);
        $subfolder = str_replace('/home', '', $subfolder);
        
        return $subfolder;
    }
}