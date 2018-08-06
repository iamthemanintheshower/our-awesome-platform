<?php

$application_configs['ws_oap_install'] = array(
    'ws_oap_tmpl' => $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_TEMPLATES'].'ws-oap/ws-oap.zip'
);

$application_configs['wp_install'] = array(
    'wp_tmpl' => $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_TEMPLATES'].'WP-template/template/',
    'temp' => $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_TEMPLATES'].'WP-template/_temp/',
    'wp_config_tmpl_filename' => 'wp-config-template.php',
    'wp_db_template' => 'WP_db_template.sql',
    'htaccess_tmpl_filename' => '.htaccess-template'
);

$application_configs['bp_install'] = array(
    'bp_tmpl' => $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_TEMPLATES'].'BP-template/template/',
    'temp' => $application_configs['ROOT_PATH'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PRIVATE_FOLDER_TEMPLATES'].'BP-template/_temp/',
    'bp_config_tmpl_filename' => '-application-config.php',
    'bp_db_template' => 'BP_db_template.sql',
    'htaccess_tmpl_filename' => '.htaccess'
);