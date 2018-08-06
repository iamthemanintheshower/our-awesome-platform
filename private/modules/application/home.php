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
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'application/htmltowp.php',
                $application_configs['PRIVATE_FOLDER_CLASSES'].'button.php',
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/ftp_mng.php',
                $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'ws_consumer/ws_consumer.php'
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
                'userbean' => $_SESSION['userbean-Q4rp'],
                'strings' => new Strings()
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
                if(isset($project) && isset($project['project'])){
                    $project_buttons[] = $button->getResponse($project['project'], 'id_'.$project['id_project'], 'project-button', 'data-id_project="'.$project['id_project'].'"');
                }
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
        $project['projectslug'] = $this->_getSlugByProjectName($project['project']);
        return array(
            'type' => 'ws', 
            'response' => array(
                'project' => $project,
                'tabs' => $tabs,
            )
        );
    }

    public function _action_saveNewGroup($application_configs, $module, $action, $post, $optional_parameters){
        
        $inputValues[] = array('field' => 'project_group', 'typed_value' => $post['project_group']);
        $inputValues[] = array('field' => 'group_color', 'typed_value' => $post['group_color']);

        $_saveNewGroup = $application_configs['db_mng']->saveDataOnTable('oap__groups', $inputValues, 'db', 0);
        
        return array(
            'type' => 'ws', 
            'response' => array(
                'saveNewGroup' => $_saveNewGroup
            )
        );
    }

    public function _action_saveNewProject($application_configs, $module, $action, $post, $optional_parameters){
        $projectslug = $this->_getSlugByProjectName($post['project']);
        if(file_exists($application_configs['wp_install']['temp'].$projectslug) && is_dir($application_configs['wp_install']['temp'].$projectslug)){
            $_message = array('field' => '', 'valid' => false, 'message' => 'Project already exists');
        }else{
            ini_set('max_execution_time', 300);

            $_group_id = $post['current_group'];

            $encryption = new Encryption($application_configs['encryption_details']);

            //#- oap__ftp_details
            $ivFTPDetails[] = array('field' => 'ftp_host', 'typed_value' => $post['ftp_host']);
            $ivFTPDetails[] = array('field' => 'ftp_root', 'typed_value' => $post['ftp_root']);
            $ivFTPDetails[] = array('field' => 'ftp_user', 'typed_value' => $encryption->encrypt($post['ftp_user']));
            $ivFTPDetails[] = array('field' => 'ftp_psw', 'typed_value' => $encryption->encrypt($post['ftp_psw']));

            $_id_ftp_details = $application_configs['db_mng']->saveDataOnTable('oap__ftp_details', $ivFTPDetails, 'db', 0);

            //#- oap__db_details
            $ivDBDetails[] = array('field' => 'db_host', 'typed_value' => $post['db_host']);
            $ivDBDetails[] = array('field' => 'db_name', 'typed_value' => $encryption->encrypt($post['db_name']));
            $ivDBDetails[] = array('field' => 'db_user', 'typed_value' => $encryption->encrypt($post['db_user']));
            $ivDBDetails[] = array('field' => 'db_psw', 'typed_value' => $encryption->encrypt($post['db_psw']));

            $_id_db_details = $application_configs['db_mng']->saveDataOnTable('oap__db_details', $ivDBDetails, 'db', 0);

            //#- oap__ws_details
            $strings = new Strings();
            $_ws_oap_folder = 'ws-oap-'.$strings->getRandomString();
            $_ws_find_string_in_file_url = $_ws_oap_folder.'/WS-find-string-in-file-'.$strings->getRandomString().'.php';
            $_ws_database_url = $_ws_oap_folder.'/WS-database-url-'.$strings->getRandomString().'.php';
            $_ws_file_list_url = $_ws_oap_folder.'/WS-file-list-url-'.$strings->getRandomString().'.php';
            $ivWSDetails[] = array('field' => 'ws_user', 'typed_value' => $strings->getRandomString());
            $ivWSDetails[] = array('field' => 'ws_psw', 'typed_value' => $strings->getRandomString());
            $ivWSDetails[] = array('field' => 'ws_find_string_in_file_url', 'typed_value' => $_ws_find_string_in_file_url);
            $ivWSDetails[] = array('field' => 'ws_database_url', 'typed_value' => $_ws_database_url);
            $ivWSDetails[] = array('field' => 'ws_file_list_url', 'typed_value' => $_ws_file_list_url);

            $_id_ws_details = $application_configs['db_mng']->saveDataOnTable('oap__ws_details', $ivWSDetails, 'db', 0);

            //#- oap__websites
            $ivWebsite[] = array('field' => 'website', 'typed_value' => $post['website']);
            $ivWebsite[] = array('field' => 'wp_admin', 'typed_value' => 'wp-admin'); //#TODO: take a decision about this field
            $ivWebsite[] = array('field' => 'ftp_id_details', 'typed_value' => $_id_ftp_details);
            $ivWebsite[] = array('field' => 'db_id_details', 'typed_value' => $_id_db_details);
            $ivWebsite[] = array('field' => 'ws_id_details', 'typed_value' => $_id_ws_details);

            $_website_id = $application_configs['db_mng']->saveDataOnTable('oap__websites', $ivWebsite, 'db', 0);

            //#- oap__projects
            if(isset($post['radioProjectType']) && $post['radioProjectType'] === 'WP'){$_radioProjectType = 'WP';}
            if($post['radioProjectType'] === 'BlankProject'){$_radioProjectType = 'BP';} //#TODO: improve these lines
            if($post['radioProjectType'] === 'None'){$_radioProjectType = 'NN';}
            $ivProject[] = array('field' => 'project', 'typed_value' => $post['project']);
            $ivProject[] = array('field' => 'website_id', 'typed_value' => $_website_id);
            $ivProject[] = array('field' => 'radioProjectType', 'typed_value' => $_radioProjectType);

            $_project_id = $application_configs['db_mng']->saveDataOnTable('oap__projects', $ivProject, 'db', 0);
            $project = $this->getProjectByID($application_configs['db_mng'], $_project_id);

            //#- oap__projects_tabs
            $ivProjectTabs[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivProjectTabs[] = array('field' => 'tab_id', 'typed_value' => 1);
            $ivProjectTabs[] = array('field' => 'usertype_id', 'typed_value' => 1); //#TODO default available for usertype_id = 1
            $application_configs['db_mng']->saveDataOnTable('oap__projects_tabs', $ivProjectTabs, 'db', 0);
            $ivProjectTabs = null;
            $ivProjectTabs[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivProjectTabs[] = array('field' => 'tab_id', 'typed_value' => 2);
            $ivProjectTabs[] = array('field' => 'usertype_id', 'typed_value' => 1); //#TODO default available for usertype_id = 1
            $application_configs['db_mng']->saveDataOnTable('oap__projects_tabs', $ivProjectTabs, 'db', 0);
            $ivProjectTabs = null;
            if(isset($post['radioProjectType']) && $post['radioProjectType'] === 'WP'){
                $ivProjectTabs[] = array('field' => 'project_id', 'typed_value' => $_project_id);
                $ivProjectTabs[] = array('field' => 'tab_id', 'typed_value' => 3);
                $ivProjectTabs[] = array('field' => 'usertype_id', 'typed_value' => 1); //#TODO default available for usertype_id = 1
                $application_configs['db_mng']->saveDataOnTable('oap__projects_tabs', $ivProjectTabs, 'db', 0);
                $ivProjectTabs = null;
            }
            $ivProjectTabs[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivProjectTabs[] = array('field' => 'tab_id', 'typed_value' => 5);
            $ivProjectTabs[] = array('field' => 'usertype_id', 'typed_value' => 1); //#TODO default available for usertype_id = 1
            $application_configs['db_mng']->saveDataOnTable('oap__projects_tabs', $ivProjectTabs, 'db', 0);
            $ivProjectTabs = null;
            $ivProjectTabs[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivProjectTabs[] = array('field' => 'tab_id', 'typed_value' => 6);
            $ivProjectTabs[] = array('field' => 'usertype_id', 'typed_value' => 1); //#TODO default available for usertype_id = 1
            $application_configs['db_mng']->saveDataOnTable('oap__projects_tabs', $ivProjectTabs, 'db', 0);
            $ivProjectTabs = null;
            $ivProjectTabs[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivProjectTabs[] = array('field' => 'tab_id', 'typed_value' => 7);
            $ivProjectTabs[] = array('field' => 'usertype_id', 'typed_value' => 1); //#TODO default available for usertype_id = 1
            $application_configs['db_mng']->saveDataOnTable('oap__projects_tabs', $ivProjectTabs, 'db', 0);
            $ivProjectTabs = null;

            //#- oap__projects_groups
            $ivProjectsGroups[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivProjectsGroups[] = array('field' => 'group_id', 'typed_value' => $_group_id);

            $application_configs['db_mng']->saveDataOnTable('oap__projects_groups', $ivProjectsGroups, 'db', 0);

            //#- oap__user_project_usertype
            $userbean = unserialize($_SESSION['userbean-Q4rp']);
            $ivUserProjectUsertype[] = array('field' => 'user_id', 'typed_value' => $userbean->getId());
            $ivUserProjectUsertype[] = array('field' => 'project_id', 'typed_value' => $_project_id);
            $ivUserProjectUsertype[] = array('field' => 'usertype_id', 'typed_value' => 1);

            $application_configs['db_mng']->saveDataOnTable('oap__user_project_usertype', $ivUserProjectUsertype, 'db', 0);


            //# Upload the WS folders
            $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $_id_ftp_details);
            $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
            $ftp->uploadFileViaFTP($post['ftp_root'], $application_configs['ws_oap_install']['ws_oap_tmpl'], $post['website']);
            $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);

            if(isset($post['radioProjectType']) && $post['radioProjectType'] === 'WP'){
                if($getProjectWSDetails){
                    $wordpress_latest_zip = file_get_contents('http://wordpress.org/latest.zip');
                    file_put_contents($application_configs['wp_install']['wp_tmpl'].'/wordpress_latest_zip.zip', $wordpress_latest_zip);
                    $ftp->uploadFileViaFTP($post['ftp_root'], $application_configs['wp_install']['wp_tmpl'].'/WS-uncompress-jeuastod.php', $post['website']);
                    $zip = new ZipArchive;
                    if ($zip->open($application_configs['wp_install']['wp_tmpl'].'/wordpress_latest_zip.zip', ZipArchive::CREATE) === TRUE) {
                        $zip->extractTo($application_configs['wp_install']['wp_tmpl']);
                        system('rm -rf ' . escapeshellarg($application_configs['wp_install']['wp_tmpl'].'/wordpress/wp-content/plugins/akismet'), $retval);
                        system('rm ' . escapeshellarg($application_configs['wp_install']['wp_tmpl'].'/wordpress/wp-content/plugins/hello.php'), $retval);
                        system('rm -rf ' . escapeshellarg($application_configs['wp_install']['wp_tmpl'].'/wordpress/wp-content/themes/twentyfifteen'), $retval);
                        system('rm -rf ' . escapeshellarg($application_configs['wp_install']['wp_tmpl'].'/wordpress/wp-content/themes/twentysixteen'), $retval);
                    }
                    
                    //#https://github.com/iamthemanintheshower/itmits__html-to-wp/archive/master.zip
                    $zip = new ZipArchive;
                    file_put_contents($application_configs['wp_install']['wp_tmpl'].'/itmits__html-to-wp_latest_zip.zip', 'http://github.com/iamthemanintheshower/itmits__html-to-wp/archive/master.zip');

                    if ($zip->open($application_configs['wp_install']['wp_tmpl'].'/itmits__html-to-wp_latest_zip.zip', ZipArchive::CREATE) === TRUE) {
                        $zip->extractTo($application_configs['wp_install']['wp_tmpl'].'/wordpress/wp-content/plugins/');
                    }

                    #https://github.com/iamthemanintheshower/itmits__editor/archive/master.zip
                    $zip = new ZipArchive;
                    file_put_contents($application_configs['wp_install']['wp_tmpl'].'/itmits__editor_latest_zip.zip', 'http://github.com/iamthemanintheshower/itmits__editor/archive/master.zip');

                    if ($zip->open($application_configs['wp_install']['wp_tmpl'].'/itmits__editor_latest_zip.zip', ZipArchive::CREATE) === TRUE) {
                        $zip->extractTo($application_configs['wp_install']['wp_tmpl'].'/wordpress/wp-content/plugins/');
                    }

                    $ws_details = array(
                        'ws_url' => $project['website'].'/WS-uncompress-jeuastod.php',
                        'user' => $getProjectWSDetails['ws_user'],
                        'psw' => $getProjectWSDetails['ws_psw'],
                    );
                    $password = crypt($getProjectWSDetails['ws_psw'], base64_encode($getProjectWSDetails['ws_psw']));
                    $fields = array(
                        'compressed_filename',
                        'application_slug',
                        'ws_oap_folder',
                        'ws_database_url', 'ws_file_list_url', 'ws_find_string_in_file_url',
                        'ws_user', 'ws_psw',
                        'db_host', 'db_name', 'db_user', 'db_psw'
                    );
                    $post_ = 'compressed_filename=ws-oap.zip'.
                        '&application_slug='.$projectslug.
                        '&ws_oap_folder='.$_ws_oap_folder.
                        '&ws_database_url='.$_ws_database_url.
                        '&ws_file_list_url='.$_ws_file_list_url.
                        '&ws_find_string_in_file_url='.$_ws_find_string_in_file_url.
                        '&ws_user='.$getProjectWSDetails['ws_user'].
                        '&ws_psw='.$password.
                        '&db_host='.$post['db_host'].'&db_name='.$post['db_name'].'&db_user='.$post['db_user'].'&db_psw='.$post['db_psw']
                        ;
                    $_uncompressfile_ws = $this->_uncompressfile_ws(new WSConsumer, $ws_details, $fields, $post_);
                }

                //# Create and Upload WP instance (Inspired by https://github.com/iamthemanintheshower/custom-wp-installer)
                mkdir($application_configs['wp_install']['temp'].$projectslug);

                $wp_config_tmpl_content = file_get_contents($application_configs['wp_install']['wp_tmpl'].$application_configs['wp_install']['wp_config_tmpl_filename']);
                $htaccess_tmpl_content = file_get_contents($application_configs['wp_install']['wp_tmpl'].$application_configs['wp_install']['htaccess_tmpl_filename']);
                $WP_db_content = file_get_contents($application_configs['wp_install']['wp_tmpl'].$application_configs['wp_install']['wp_db_template']);

                //wp-config.php
                $wp_config = str_replace('#DB-NAME#', $post['db_name'], $wp_config_tmpl_content);
                $wp_config = str_replace('#DB-USER#', $post['db_user'], $wp_config);
                $wp_config = str_replace('#DB-PSW#', $post['db_psw'], $wp_config);
                $wp_config = str_replace('#DB-HOST#', $post['db_host'], $wp_config);
                file_put_contents($application_configs['wp_install']['temp'].$projectslug.'/wp-config.php', $wp_config);

                //use an already customized .htaccess
                $htaccess_tmpl_content = str_replace('#APPLICATION-SLUG#', $projectslug, $htaccess_tmpl_content);
                file_put_contents($application_configs['wp_install']['temp'].$projectslug.'/.htaccess', $htaccess_tmpl_content);

                //use the WP instance from template
                $this->recurse_copy($application_configs['wp_install']['wp_tmpl'].'/wordpress/', $application_configs['wp_install']['temp'].$projectslug.'/');

                //customize the DB from a template
                $WP_db_content = str_replace('#SITE-URL#', $post['website'].'/'.$projectslug, $WP_db_content);
                $WP_db_content = str_replace('#SITE-NAME#', $post['project'], $WP_db_content);
                $WP_db_content = str_replace('#WP-USR#', $post['_user'], $WP_db_content);
                $WP_db_content = str_replace('#WP-PSW#', md5($post['_psw']), $WP_db_content);
                $WP_db_content = str_replace('#ADMIN-EMAIL#', $post['_email'], $WP_db_content);

                //create the customized DB
                file_put_contents($application_configs['wp_install']['temp'].$projectslug.'/'.$application_configs['wp_install']['wp_db_template'], $WP_db_content);

                //#Upload WP customized instance
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($application_configs['wp_install']['temp'].$projectslug),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $file){
                    if(!$file->isDir()){
                        $_files[] = $file;
                    }
                }
                $_website_compressed_filename = $application_configs['wp_install']['temp'].'project-oaisdakwhe.zip';
                $ftp->_compress_files($_website_compressed_filename, $_files, $application_configs['wp_install']['temp']); //.$projectslug.'/'
                $ftp->uploadFileViaFTP($post['ftp_root'], $application_configs['wp_install']['temp'].'project-oaisdakwhe.zip', $projectslug);

                //# Uncompress files via WS
                if($getProjectWSDetails){
                    $ws_details = array(
                        'ws_url' => $project['website'].'/WS-uncompress-jeuastod.php',
                        'user' => $getProjectWSDetails['ws_user'],
                        'psw' => $getProjectWSDetails['ws_psw'],
                    );
                    $fields = array('compressed_filename');
                    $post_ = 'compressed_filename=project-oaisdakwhe.zip';
                    $_uncompressfile_ws = $this->_uncompressfile_ws(new WSConsumer, $ws_details, $fields, $post_);
                }

                $ws_details = array(
                    'ws_url' => $project['website'].'/WS-database-import-asoiwnoienoiaero.php',
                    'user' => $getProjectWSDetails['ws_user'],
                    'psw' => $getProjectWSDetails['ws_psw'],
                );
                $fields = array('db_host', 'db_name', 'db_user', 'db_psw');
                $post_ = 'db_host='.$post['db_host'].'&db_name='.$post['db_name'].'&db_user='.$post['db_user'].'&db_psw='.$post['db_psw'];
                $_import_ws = $this->_import_ws(new WSConsumer, $ws_details, $fields, $post_);

                unlink($_website_compressed_filename);
                system('rm -rf ' . escapeshellarg($application_configs['wp_install']['temp'].$projectslug), $retval);
                
                $_message = array('field' => '', 'valid' => true, 'message' => 'ok');
            }

            if(isset($post['radioProjectType']) && $post['radioProjectType'] === 'BlankProject'){
                if($getProjectWSDetails){
                    $ftp->uploadFileViaFTP($post['ftp_root'], $application_configs['bp_install']['bp_tmpl'].'/WS-uncompress-jeuastod.php', $post['website']);

                    $ws_details = array(
                        'ws_url' => $project['website'].'/WS-uncompress-jeuastod.php',
                        'user' => $getProjectWSDetails['ws_user'],
                        'psw' => $getProjectWSDetails['ws_psw'],
                    );
                    $password = crypt($getProjectWSDetails['ws_psw'], base64_encode($getProjectWSDetails['ws_psw']));
                    $fields = array(
                        'compressed_filename',
                        'application_slug',
                        'ws_oap_folder',
                        'ws_database_url', 'ws_file_list_url', 'ws_find_string_in_file_url',
                        'ws_user', 'ws_psw',
                        'db_host', 'db_name', 'db_user', 'db_psw'
                    );
                    $post_ = 'compressed_filename=ws-oap.zip'.
                        '&application_slug='.$projectslug.
                        '&ws_oap_folder='.$_ws_oap_folder.
                        '&ws_database_url='.$_ws_database_url.
                        '&ws_file_list_url='.$_ws_file_list_url.
                        '&ws_find_string_in_file_url='.$_ws_find_string_in_file_url.
                        '&ws_user='.$getProjectWSDetails['ws_user'].
                        '&ws_psw='.$password.
                        '&db_host='.$post['db_host'].'&db_name='.$post['db_name'].'&db_user='.$post['db_user'].'&db_psw='.$post['db_psw']
                    ;
                    $_uncompressfile_ws = $this->_uncompressfile_ws(new WSConsumer, $ws_details, $fields, $post_);
                }

                mkdir($application_configs['bp_install']['temp'].$projectslug);

                $bp_config_tmpl_content = file_get_contents($application_configs['bp_install']['bp_tmpl'].$application_configs['bp_install']['bp_config_tmpl_filename']);
                $htaccess_tmpl_content = file_get_contents($application_configs['bp_install']['bp_tmpl'].$application_configs['bp_install']['htaccess_tmpl_filename']);
                $BP_db_content = file_get_contents($application_configs['bp_install']['bp_tmpl'].$application_configs['bp_install']['bp_db_template']);

                //bp-config
                $bp_config_tmpl_content = str_replace('#SITE-URL#', $this->_getDomainWithoutProtocol($post['website']), $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#ROOT_PATH#', $post['ftp_root'], $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#APPLICATION-NAME#', $post['project'], $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#APPLICATION-SLUG#', $projectslug, $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#DB-NAME#', $post['db_name'], $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#DB-USER#', $post['db_user'], $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#DB-PSW#', $post['db_psw'], $bp_config_tmpl_content);
                $bp_config_tmpl_content = str_replace('#DB-HOST#', $post['db_host'], $bp_config_tmpl_content);

                //use the BP instance from template
                $this->recurse_copy($application_configs['bp_install']['bp_tmpl'], $application_configs['bp_install']['temp'].$projectslug.'/');
                $this->recurse_copy($application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PUBLIC_FOLDER'].$application_configs['LIB'], $application_configs['bp_install']['temp'].$projectslug.'/'.$application_configs['PUBLIC_FOLDER'].$application_configs['LIB']);
                $this->recurse_copy($application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PUBLIC_FOLDER'].'fonts/', $application_configs['bp_install']['temp'].$projectslug.'/'.$application_configs['PUBLIC_FOLDER'].'fonts/');

                file_put_contents($application_configs['bp_install']['temp'].$projectslug.'/-application-config.php', $bp_config_tmpl_content);

                //use an already customized .htaccess
                $htaccess = str_replace('#APPLICATION-SLUG#', $projectslug, $htaccess_tmpl_content);
                file_put_contents($application_configs['bp_install']['temp'].$projectslug.'/.htaccess', $htaccess);

                //customize the DB from a template
                $BP_db_content = str_replace('#BP-USR#', $post['_user'], $BP_db_content);
                $BP_db_content = str_replace('#BP-PSW#', md5($post['_psw']), $BP_db_content);
                $BP_db_content = str_replace('#APPLICATION-SLUG#', $projectslug, $BP_db_content);

                //create the customized DB
                file_put_contents($application_configs['bp_install']['temp'].$projectslug.'/'.$application_configs['bp_install']['bp_db_template'], $BP_db_content);

                //#Upload BP customized instance
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($application_configs['bp_install']['temp'].$projectslug),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $file){
                    if(!$file->isDir()){
                        $_files[] = $file;
                    }
                }
                $_website_compressed_filename = $application_configs['bp_install']['temp'].'project-oaisdakwhe.zip';
                $ftp->_compress_files($_website_compressed_filename, $_files, $application_configs['bp_install']['temp']);
                $ftp->uploadFileViaFTP($post['ftp_root'], $application_configs['bp_install']['temp'].'project-oaisdakwhe.zip', $post['website']);

                //# Uncompress files via WS
                if($getProjectWSDetails){
                    $ws_details = array(
                        'ws_url' => $project['website'].'/WS-uncompress-jeuastod.php',
                        'user' => $getProjectWSDetails['ws_user'],
                        'psw' => $getProjectWSDetails['ws_psw'],
                    );
                    $fields = array('compressed_filename');
                    $post_ = 'compressed_filename=project-oaisdakwhe.zip';
                    $_uncompressfile_ws = $this->_uncompressfile_ws(new WSConsumer, $ws_details, $fields, $post_);
                }

                $ws_details = array(
                    'ws_url' => $project['website'].'/WS-database-import-asoiwnoienoiaero.php',
                    'user' => $getProjectWSDetails['ws_user'],
                    'psw' => $getProjectWSDetails['ws_psw'],
                );
                $fields = array('db_host', 'db_name', 'db_user', 'db_psw');
                $post_ = 'db_host='.$post['db_host'].'&db_name='.$post['db_name'].'&db_user='.$post['db_user'].'&db_psw='.$post['db_psw'];
                $_import_ws = $this->_import_ws(new WSConsumer, $ws_details, $fields, $post_);

                unlink($_website_compressed_filename);
                system('rm -rf ' . escapeshellarg($application_configs['bp_install']['temp'].$projectslug), $retval);
                
                $_message = array('field' => '', 'valid' => true, 'message' => 'ok');
            }
        }

        return array(
            'type' => 'ws', 
            'response' => array(
                'project_id' => $_project_id,
                'id_db_details' => $_id_db_details,
                '_import' => $_import_ws,
                '_uncompressfile_ws' => $_uncompressfile_ws,
                'message' => $_message
            )
        );
    }

    public function _action_maintenanceMode($application_configs, $module, $action, $post, $optional_parameters){
        var_dump($post);

        $_id_project = $post['current_project'];

        switch ($post['maintenanceMode']) {
            case 'btnMaintenanceModeMessage':
                //#TODO
                //Put in maintenance
                //# 1) rename index.php to index-[salt].php
                //# 2) rename maintenance.php to index.php
                //Back from maintenance
                //# 1) rename index.php to maintenance.php
                //# 2) rename index-[salt].php to index.php
                break;

            case 'btnMaintenanceModeAuthpopup':
                //#TODO
                //Put in maintenance
                //# 1) change .htaccess in order to consider the .htpasswd file
                //Back from maintenance
                //# 1) change .htaccess in order to ignore the .htpasswd file
                break;

            default:
                break;
        }
        return array(
            'type' => 'ws', 
            'response' => array() //#TODO setup a response
        );
    }

    public function _action_adminPasswordReset($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $post['current_project'];
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $sql_query = "UPDATE `wp_users` SET `user_pass` = md5('".$post['adminpasswordreset']."') WHERE `ID` = '1'";

        $adminPasswordReset_via_ws = $this->getProjectDBMng($application_configs, $project)->getDataByQuery($sql_query, 'ws');

        return array(
            'type' => 'ws', 
            'response' => array(
                'adminPasswordReset_via_ws' => $adminPasswordReset_via_ws
            )
        );
    }

    public function _action_downloadcurrentproject($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $post['current_project'];
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        //#TODO
        //Compress the project folder
        //Download the archive

        return array(
            'type' => 'ws', 
            'response' => array(

            )
        );
    }
    
    public function _action_golive($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $post['id_project'];
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $selectedTable = 'oap__projects';
        $selectValues_oap__projects[] = 'id_project';
        $selectValues_oap__projects[] = 'project';
        $selectValues_oap__projects[] = 'website_id';
        $whereValues_oap__projects[] = array('where_field' => 'id_project', 'where_value' => $id_project);
        $oap__projects = $application_configs['db_mng']->getDataByWhere($selectedTable, $selectValues_oap__projects, $whereValues_oap__projects)['response'];

        $selectedTable = 'oap__websites';
        $selectValues_oap__websites[] = 'id_website';
        $selectValues_oap__websites[] = 'website';
        $selectValues_oap__websites[] = 'wp_admin';
        $selectValues_oap__websites[] = 'ftp_id_details';
        $selectValues_oap__websites[] = 'db_id_details';
        $selectValues_oap__websites[] = 'ws_id_details';
        $whereValues_oap__websites[] = array('where_field' => 'id_website', 'where_value' => $oap__projects[0]['website_id']);
        $oap__websites = $application_configs['db_mng']->getDataByWhere($selectedTable, $selectValues_oap__websites, $whereValues_oap__websites)['response'];
        
        $selectedTable = 'oap__ftp_details';
        $selectValues_oap__ftp_details[] = 'id_ftp_details';
        $selectValues_oap__ftp_details[] = 'ftp_host';
        $selectValues_oap__ftp_details[] = 'ftp_root';
        $selectValues_oap__ftp_details[] = 'ftp_user';
        $selectValues_oap__ftp_details[] = 'ftp_psw';
        $whereValues_oap__ftp_details[] = array('where_field' => 'id_ftp_details', 'where_value' => $oap__websites[0]['ftp_id_details']);
        $oap__ftp_details = $application_configs['db_mng']->getDataByWhere($selectedTable, $selectValues_oap__ftp_details, $whereValues_oap__ftp_details)['response'];
        
        //# change this out
        $dev_website_protocol = 'https://'; //# TODO fix this part
        $dev_website_url = $oap__websites[0]['website'].'/'.$this->_getSlugByProjectName($oap__projects[0]['project']);
        $prod_website_url = '/';

        $encryption = new Encryption($application_configs['encryption_details']);

        $ftp__host = $oap__ftp_details[0]['ftp_host'];
        $ftp__user = $encryption->decrypt($oap__ftp_details[0]['ftp_user']);
        $ftp__password = $encryption->decrypt($oap__ftp_details[0]['ftp_psw']);
        $ftp__destination_folder = str_replace('/web/htdocs', '', $oap__ftp_details[0]['ftp_root']); //# TODO fix this part
        $ftp__destination_folder = str_replace('/home', '', $ftp__destination_folder); //# TODO fix this part

        $_date = new DateTime();

        $file_ary_edit[] = $file_ary_all[] = NULL;

        if($dev_website_url !== ''){
//            $ary_missing_files = array( //#TODO fix this part
//                '_utils/wp-emoji-release.min.js' => '/wp-includes/js/wp-emoji-release.min.js',
//                '_utils/wp-embed.min.js' => '/wp-includes/js/wp-embed.min.js'
//            );
            $dev_website_url_js = $this->_get_website_url_js($dev_website_url); //#Customized URL for the javascript part
            $dev_website_cache_dir = $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_DATA'].'cache/'.$this->_getSlugByProjectName($oap__projects[0]['project']).'-'.$this->_getSlugByProjectName($_date->format('Y-m-d_H:i:s'));

            $this->download_website_to_cache($dev_website_url, $dev_website_cache_dir); //# TODO: manage the return and proceed only if it's ok
            $files = $this->filesystem_navigation($dev_website_cache_dir);

            $file_ary_edit = $files['file_ary_edit'];
            $file_ary_all = $files['file_ary_all'];

            //replace dev_website_url
            foreach ($file_ary_edit as $f){
                $this->_find_replace_in_file($f, $dev_website_url, $prod_website_url);
            }

            //replace dev_website_url_js
            foreach ($file_ary_edit as $f){
                $this->_find_replace_in_file($f, $dev_website_url_js, $this->_get_website_url_js($prod_website_url));
            }

            //replace /wp-content
            foreach ($file_ary_edit as $f){
                $this->_find_replace_in_file($f, 'src="/wp-content/', 'src="'.$prod_website_url.'/wp-content/');
            }

            //rename file with ?
            foreach ($file_ary_all as $f){
                $this->_rename_file($f);
            }

            //copy some files that wget can't catch
            //# Inspired by https://github.com/iamthemanintheshower/WP-from-DEV-to-HTML-LIVE
            //# TODO: fix this part
            /*
            foreach ($ary_missing_files as $k => $v){
                copy(
                    __DIR__. '/'.$k, 
                    $dev_website_cache_dir.'/'.str_replace($dev_website_protocol, '', $dev_website_url).$v
                );
            }
            */
            $ftp_connection = ftp_connect($ftp__host) or die("Couldn't connect to ".$ftp__host); 
            if (ftp_login($ftp_connection, $ftp__user, $ftp__password)) {
                ftp_chdir($ftp_connection, $ftp__destination_folder);
                foreach ($file_ary_all as $filename){
                    if(file_exists($filename)){
                        $this->ftp_put_dir($ftp_connection, $dev_website_cache_dir.'/'.str_replace($dev_website_protocol, '', $dev_website_url), $ftp__destination_folder);
                    }
                }
                $_result = 'Check the website: <a target="_blank" href="'.$prod_website_url.'">'.$prod_website_url.'</a>';
            }else{
                $_result = 'Not connected. Check FTP details.';
            }
        }

        return array(
            'type' => 'ws', 
            'response' => array(
                '_result' => $_result,
                'prod_website_url' => $prod_website_url
            )
        );
    }

    public function _action_disableallplugins($application_configs, $module, $action, $post, $optional_parameters){
        $id_project = $post['id_project'];
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        $sql_query = "SELECT `option_id`, `option_value` FROM `wp_options` WHERE `option_name` = 'active_plugins'";

        $getActivePlugins_via_ws = $this->getProjectDBMng($application_configs, $project)->getDataByQuery($sql_query, 'ws');

        foreach ($getActivePlugins_via_ws['response'] as $element){
            $_option_id = $element['option_id'];
            $_option_value = $element['option_value'];
        }

        $sql_query = "UPDATE `wp_options` SET option_value = '' WHERE `option_id` = '$_option_id'";
        $setActivePlugins_via_ws = $this->getProjectDBMng($application_configs, $project)->getDataByQuery($sql_query, 'ws');
        
        return array(
            'type' => 'ws', 
            'response' => array(
                'option_id' => $_option_id,
                'option_value' => $_option_value,
                'setActivePlugins_via_ws' => $setActivePlugins_via_ws
            )
        );
    }

    public function _action_htmltowp($application_configs, $module, $action, $post, $optional_parameters){
        //#Inspired by https://github.com/iamthemanintheshower/itmits__html-to-wp
        $id_project = $post['id_project'];
        $project = $this->getProjectByID($application_configs['db_mng'], $id_project);

        return array(
            'type' => 'ws', 
            'response' => array(

            )
        );
    }
    public function _action_getTemplateFiles($application_configs, $module, $action, $post, $optional_parameters){
        //#Inspired by https://github.com/iamthemanintheshower/itmits__html-to-wp
        $_project_id = $post['id_project'];
        $project = $this->getProjectByID($application_configs['db_mng'], $_project_id);
        $getProjectWSDetails = $this->getProjectWSDetails($application_configs['db_mng'], $project);
        $ws_details = array(
            'ws_url' => $project['website'].'/WS-getTemplateFile-asdoiawndaiwo.php',
            'user' => $getProjectWSDetails['ws_user'],
            'psw' => $getProjectWSDetails['ws_psw'],
        );
        $password = crypt($getProjectWSDetails['ws_psw'], base64_encode($getProjectWSDetails['ws_psw']));
        $fields = array();
        $post_ = '';
        $_WSConsumer = new WSConsumer();
        $_getTemplateFiles = json_decode($_WSConsumer->get_ws($ws_details, $fields, $post_));

        return array(
            'type' => 'ws', 
            'response' => array(
                'getTemplateFiles' => $_getTemplateFiles
            )
        );
    }
    public function _action_parseHTML($application_configs, $module, $action, $post, $optional_parameters){
        //#Inspired by https://github.com/iamthemanintheshower/itmits__html-to-wp
        $getProjectFTPDetails = $this->getProjectFTPDetails($application_configs['db_mng'], $_id_ftp_details);
        $ftp = new FTP_mng($getProjectFTPDetails, $application_configs);
        $file_content = $ftp->getFileViaFTP($post, $remote__root_folder, $local__root_folder); //#TODO: get the file content
        $_htmltowp = new HTMLtoWP();
        $_get_head = $_htmltowp->get_custom_tag('head', $file_content);
        $_get_body = $_htmltowp->get_custom_tag('body', $file_content);
        $_get_footer = $_htmltowp->get_custom_tag('footer', $file_content);

        return array(
            'type' => 'ws', 
            'response' => array(
                'get_head' => $_get_head,
                'get_body' => $_get_body,
                'get_footer' => $_get_footer
            )
        );
    }

    private function _import_ws($WSConsumer, $ws_details, $fields, $post_){
        return $WSConsumer->import_ws($ws_details, $fields, $post_);
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

    private function recurse_copy($src,$dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }

    private function getProjectFTPDetails($db_mng, $_id_db_details){
        $project = new Project($db_mng);
        return $project->getProjectFTPDetails($_id_db_details);
    }

    private function _getSlugByProjectName($ProjectName){ //#TODO improve the slug creator
        $_projectname = str_replace(' ', '-', $ProjectName);
        $_projectname = str_replace('é', 'e', $_projectname);
        $_projectname = str_replace('è', 'e', $_projectname);
        $_projectname = str_replace(':', '-', $_projectname);
        $_projectname = str_replace('https://', '', $_projectname);
        $_projectname = strtolower($_projectname);
        return $_projectname;
    }

    private function _getDomainWithoutProtocol($_projectname){
        $_projectname = str_replace('https://', '', $_projectname);
        $_projectname = strtolower($_projectname);
        return $_projectname;
    }

    public function getInitScript($application_configs, $token){
        $this->_getInitScript($application_configs, $token);
    }
    
    
    
    
    
    
    //# Inspired by https://github.com/iamthemanintheshower/WP-from-DEV-to-HTML-LIVE
    //# TODO: fix this part
    private function _find_replace_in_file($path_to_file, $dev_website_url, $prod_website_url){
        if(!empty($path_to_file) && is_file($path_to_file)){
            $file_contents = file_get_contents($path_to_file);
            $file_contents = str_replace($dev_website_url, $prod_website_url, $file_contents);
            file_put_contents($path_to_file,$file_contents);
        }
    }
    private function _rename_file($path_to_file){
        if(strpos($path_to_file, '?') !== false || strpos($path_to_file, '?') !== false){
            $path_to_file__ary = explode('?', $path_to_file);
            if(isset($path_to_file__ary[0])){
                rename($path_to_file, $path_to_file__ary[0]);
            }
        }
    }

    private function download_website_to_cache($dev_website_url, $dev_website_cache_dir){
        $exec_output = $exec_status = '';

        //# Create dir for the cache
        mkdir($dev_website_cache_dir);

        //# Change directory to the cache
        chdir($dev_website_cache_dir);

        //# wget the website cache into the directory cache
        exec('wget  -r -p -U Mozilla --no-parent '.$dev_website_url, $exec_output, $exec_status); //wget -E -H -k -p 

        return array('exec_output' => $exec_output, 'exec_status' => $exec_status);
    }

    private function _get_website_url_js($dev_website_url){
        return str_replace('/', '\/', $dev_website_url);
    }

    private function ftp_put_dir($ftp_connection, $local_folder, $ftp__destination_folder) {
        $d = dir($local_folder);
        while($file = $d->read()) {
            if ($file != "." && $file != "..") {
                if (is_dir($local_folder."/".$file)) {
                    if (!@ftp_chdir($ftp_connection, $ftp__destination_folder."/".$file)) {
                        ftp_mkdir($ftp_connection, $ftp__destination_folder."/".$file);
                    }
                    $this->ftp_put_dir($ftp_connection, $local_folder."/".$file, $ftp__destination_folder."/".$file);
                } else {
                    ftp_put($ftp_connection, $ftp__destination_folder."/".$file, $local_folder."/".$file, FTP_BINARY);
                }
            }
        }
        $d->close();
    }

    private function filesystem_navigation($directory, $extensions = array()) {
        global $file_ary_edit, $file_ary_all;
        if( substr($directory, -1) == "/" ) { 
            $directory = substr($directory, 0, strlen($directory) - 1); 
        }

        $this->_filesystem_navigation_folder($directory, $extensions);

        return array('file_ary_edit' => $file_ary_edit, 'file_ary_all' => $file_ary_all);
    }

    private function _filesystem_navigation_folder($directory, $extensions = array(), $first_call = true) {
        global $file_ary_edit, $file_ary_all;

        $file = scandir($directory); 
        natcasesort($file);
        $files = $dirs = array();

        foreach($file as $this_file) {
            if( is_dir("$directory/$this_file" ) ){ 
                $dirs[] = $this_file;
            }else{ 
                $files[] = $this_file;
            }
        }
        $file = array_merge($dirs, $files);

        if( !empty($extensions) ) {
            foreach( array_keys($file) as $key ) {
                if( !is_dir("$directory/$file[$key]") ) {
                    $ext = substr($file[$key], strrpos($file[$key], ".") + 1); 
                    if( !in_array($ext, $extensions) ){ unset($file[$key]); }
                }
            }
        }

        if( count($file) > 2 ) {
            if( $first_call ) { $first_call = false; }

            foreach( $file as $this_file ) {
                if( $this_file != "." && $this_file != ".." ) {
                    if( is_dir("$directory/$this_file") ) {
                        $this->_filesystem_navigation_folder("$directory/$this_file", $extensions, false);
                    } else {
                        $ext = '';
                        $path_parts = pathinfo($this_file);
                        if(isset($path_parts['extension'])){
                            $ext = $path_parts['extension'];
                        }

                        switch ($ext) {
                            case 'php':
                            case 'html':
                            case 'js':
                            case 'txt':
                            case 'mo':
                            case '':
                                $file_ary_edit[] = $directory.'/'.$this_file;

                                break;

                            default:
                                break;
                        }
                        if(!isset($path_parts['extension'])){
                            $file_ary_edit[] = $directory.'/'.$this_file;
                        }

                        $file_ary_all[] = $directory.'/'.$this_file;
                    }
                }
            }
        }
    }
}