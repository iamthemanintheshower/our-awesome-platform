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
$application_configs['APPLICATION_DOMAIN'] = '#SITE-URL#';
$application_configs['APPLICATION_DOMAIN_PROTOCOL'] = $application_configs['APPLICATION_PROTOCOL'].$application_configs['APPLICATION_DOMAIN'].'/';
$application_configs['ROOT_PATH'] = '#ROOT_PATH#';

$application_configs['APPLICATION_NAME'] = '#APPLICATION-NAME#';
$application_configs['APPLICATION_SLUG'] = '#APPLICATION-SLUG#';
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
    'Nrqtx0HHsX' => '#DB-HOST#',
    'VxMO8N5kX4' => '#DB-NAME#',
    'qsPV6EwtzA' => '#DB-USER#',
    'AQowahicz5' => '#DB-PSW#'
);

error_reporting(E_ALL|E_STRICT);

