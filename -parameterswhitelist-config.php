<?php

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
        'project_group' => $_parameter_mandatory_not_formalcheck,
        'group_color' => $_parameter_mandatory_not_formalcheck,
    ),
    'application/home/saveNewProject' => array(
        'token' => $_parameter_token,
        'current_group' => $_parameter_mandatory_number_2,
        'ftp_host' => $_parameter_mandatory_not_formalcheck,
        'ftp_root' => $_parameter_mandatory_not_formalcheck,
        'ftp_user' => $_parameter_mandatory_not_formalcheck,
        'ftp_psw' => $_parameter_mandatory_not_formalcheck,

        'db_host' => $_parameter_mandatory_not_formalcheck, //#TODO
        'db_name' => $_parameter_mandatory_not_formalcheck, //#TODO
        'db_user' => $_parameter_mandatory_not_formalcheck,
        'db_psw' => $_parameter_mandatory_not_formalcheck,
        'radioProjectType' => $_parameter_mandatory_not_formalcheck,

        '_user' => $_parameter_mandatory_not_formalcheck,
        '_psw' => $_parameter_mandatory_not_formalcheck,
        '_email' => $_parameter_mandatory_not_formalcheck,

        'website' => $_parameter_mandatory_not_formalcheck, //#TODO

        'project' => $_parameter_mandatory_not_formalcheck //#TODO
    ),

    'application/home/golive' => 'no-parameters', //#TODO
    'application/home/disableallplugins' => 'no-parameters', //#TODO

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

    'logviewer/logviewer/index' => 'no-parameters', //#TODO
    
    'login/login/index' => 'no-parameters',
    'login/login/checklogin' => array(
        'token' => $_parameter_token,
        'username' => $_parameter_mandatory_not_formalcheck,
        'password' => $_parameter_mandatory_not_formalcheck,
    )
);