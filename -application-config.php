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

const ENABLE_HTTPS = true;

$application_configs = array();
//
$application_configs['APPLICATION_PROTOCOL'] = 'https://';
$application_configs['APPLICATION_DOMAIN'] = 'YOUR_DOMAIN';
$application_configs['APPLICATION_DOMAIN_PROTOCOL'] = $application_configs['APPLICATION_PROTOCOL'].$application_configs['APPLICATION_DOMAIN'].'/';
$application_configs['ROOT_PATH'] = '/web/htdocs/'.$application_configs['APPLICATION_DOMAIN'].'/home/';

$application_configs['APPLICATION_NAME'] = 'Our Awesome Platform';
$application_configs['APPLICATION_SLUG'] = 'oap';
$application_configs['PUBLIC_FOLDER'] = 'public_html/';

$application_configs['PRIVATE_FOLDER'] = 'private/';
$application_configs['PRIVATE_FOLDER_MODULES'] = 'private/modules/';
$application_configs['PRIVATE_FOLDER_CLASSES'] = 'private/_classes/';
$application_configs['PRIVATE_FOLDER_DATA'] = 'private/data/';
$application_configs['PRIVATE_FOLDER_LOGS'] = 'logs/';

$application_configs['APPLICATION_ROOT'] = $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/';
$application_configs['APPLICATION_URL'] = $application_configs['APPLICATION_DOMAIN_PROTOCOL'].$application_configs['APPLICATION_SLUG'].'/';
$application_configs['PUBLIC_FOLDER_MEDIA_MODULES'] = $application_configs['APPLICATION_DOMAIN_PROTOCOL'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PUBLIC_FOLDER'].'media/modules/';
$application_configs['APPLICATION_URL_WS'] = $application_configs['APPLICATION_DOMAIN_PROTOCOL'].$application_configs['APPLICATION_SLUG'].'/WS-dsfpoe/';
$application_configs['APPLICATION_URL_MODULES'] = $application_configs['APPLICATION_DOMAIN_PROTOCOL'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PUBLIC_FOLDER'].'/modules/';
$application_configs['APPLICATION_URL_LOGIN'] = $application_configs['APPLICATION_URL'].'login/login/index';


$application_configs['LIB'] = 'lib/';
$application_configs['PUBLIC_FOLDER_MODULES'] = 'modules/';

$application_configs['tmpl'] = 'clear/';

$application_configs['language'] = 'IT';

$application_configs['encryption_details'] = array(
    'secret_key' => 'z6aUz4f7iRz6aUz4f7iR',
    'secret_iv' => 'kT5I42gyBCkT5I42gyBC'
);

$application_configs['db_details'] = array(
    'Nrqtx0HHsX' => 'DB_SERVER',
    'VxMO8N5kX4' => 'DB_NAME',
    'qsPV6EwtzA' => 'DB_USER',
    'AQowahicz5' => 'DB_PSW'
);

$application_configs['editor__backup-on-save'] = $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/_backup-on-save/';
$application_configs['editor__temp-file-to-be-uploaded'] = $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/_temp-file-to-be-uploaded/';
$application_configs['editor__temp-download-collected-files'] = $application_configs['APPLICATION_ROOT'].$application_configs['PRIVATE_FOLDER_MODULES'].'editor/_temp-download-collected-files/';
$application_configs['editor__dropbox_key'] = 'YOUR_DROPBOX_KEY';

//# parameters_whitelist
$_parameter_token = array(
    'maxlength' => 200,
    'require' => array(
        'required' => false,
        'message' => 'Mandatory'
    ),
    'valid' => array(
        'required' => false,
        'regular_expression' => '/^[A-Za-z0-9]+$/',
        'message' => 'Not valid'
    )
);
$_parameter_mandatory_not_formalcheck = array(
    'maxlength' => 50,
    'require' => array(
        'required' => true,
        'message' => 'Mandatory'
    ),
    'valid' => array(
        'required' => false,
        'regular_expression' => '',
        'message' => 'Not valid'
    )
);
$_parameter_mandatory_number_2 = array(
    'maxlength' => 2,
    'require' => array(
        'required' => true,
        'message' => 'Mandatory'
    ),
    'valid' => array(
        'required' => true,
        'regular_expression' => '/^[0-9]+$/',
        'message' => 'Not valid'
    )
);
$application_configs['parameters_whitelist'] = array(
    'errors_mng/errors_mng/log' => 'no-parameters', //#TODO

    'application/home/index' => 'no-parameters',
    'application/home/getProject' => 'no-parameters', //#TODO
    'application/home/saveNewGroup' => array(
        'token' => $_parameter_token,
        'project_group' => $_parameter_mandatory_number_2,
        'group_color' => $_parameter_mandatory_not_formalcheck,
    ),
    'application/home/saveNewProject' => array(
        'token' => $_parameter_token,
        'current_group' => $_parameter_mandatory_number_2,
        'ftp_host' => $_parameter_mandatory_not_formalcheck,
        'ftp_user' => $_parameter_mandatory_not_formalcheck,
        'ftp_psw' => $_parameter_mandatory_not_formalcheck,

        'db_host' => $_parameter_mandatory_not_formalcheck, //#TODO
        'db_name' => $_parameter_mandatory_not_formalcheck, //#TODO
        'db_user' => $_parameter_mandatory_not_formalcheck,
        'db_psw' => $_parameter_mandatory_not_formalcheck,

        'ws_user' => $_parameter_mandatory_not_formalcheck,
        'ws_psw' => $_parameter_mandatory_not_formalcheck,
        'ws_find_string_in_file_url' => $_parameter_mandatory_not_formalcheck, //#TODO
        'ws_database_url' => $_parameter_mandatory_not_formalcheck, //#TODO
        'ws_file_list_url' => $_parameter_mandatory_not_formalcheck, //#TODO

        'website' => $_parameter_mandatory_not_formalcheck, //#TODO
        'wp_admin' => $_parameter_mandatory_not_formalcheck,

        'project' => $_parameter_mandatory_not_formalcheck //#TODO
    ),
    'application/home/getProjectsByGroupID' => 'no-parameters',
    'application/home/getProjectGroups' => 'no-parameters',

    'editor/editor/index' => 'no-parameters',
    'editor/editor/refreshFilelistCacheByProject' => 'no-parameters', //#TODO
    'editor/editor/getFile' => 'no-parameters', //#TODO
    'editor/editor/getFileHistory' => 'no-parameters', //#TODO
    'editor/editor/setFile' => 'no-parameters', //#TODO
    'editor/editor/searchStringInFile' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'searchstring' => $_parameter_mandatory_not_formalcheck, //#TODO
        'token' => $_parameter_token
    ),
    'editor/editor/collectEditedFiles' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'token' => $_parameter_token
    ),
    'editor/editor/collectEditedFilesgetFileZIP' => 'no-parameters',
    'editor/editor/uploadFile' => 'no-parameters', //#TODO
    'editor/editor/deleteFile' => 'no-parameters', //#TODO
    'editor/editor/setDirectory' => 'no-parameters', //#TODO

    'dbadmin/dbadmin/index' => 'no-parameters',
    'dbadmin/dbadmin/getDBTables' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'token' => $_parameter_token
    ),
    'dbadmin/dbadmin/getTableDescription' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'tablename' => $_parameter_mandatory_not_formalcheck,
        'token' => $_parameter_token
    ),
    'dbadmin/dbadmin/downloaddatabase' => 'no-parameters', //#TODO

    'dbadmin/dbadmin/getQueryResult' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'raw_query' => array(
            'maxlength' => 500,
            'require' => array(
                'required' => true,
                'message' => 'Mandatory'
            ),
            'valid' => array(
                'required' => false,
                'regular_expression' => '',
                'message' => 'Not valid'
            )
        ),
        'token' => $_parameter_token
    ),
    'dbadmin/dbadmin/executeInsertQuery' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'tablename' => $_parameter_mandatory_not_formalcheck,
        'inputFields' => $_parameter_mandatory_not_formalcheck,
        'inputValues' => $_parameter_mandatory_not_formalcheck,
        'prefix' => $_parameter_mandatory_not_formalcheck,
        'token' => $_parameter_token
    ),
    'dbadmin/dbadmin/executeUpdateQuery' => 'no-parameters', //#TODO
    'dbadmin/dbadmin/getTableQueryHistory' => array(
        'id_project' => $_parameter_mandatory_number_2,
        'token' => $_parameter_token
    ),

    'timetracker/timetracker/trackProjectAndAction' => array(
        'token' => $_parameter_token,
        'current_project' =>  $_parameter_mandatory_number_2,
        'current_action' => $_parameter_mandatory_number_2,
    ),
    'timetracker/timetracker/index' => 'no-parameters', //#TODO

    'login/login/index' => 'no-parameters',
    'login/login/checklogin' => array(
        'token' => $_parameter_token,
        'username' => $_parameter_mandatory_not_formalcheck,
        'password' => $_parameter_mandatory_not_formalcheck,
    )
);
error_reporting(E_ALL|E_STRICT);
