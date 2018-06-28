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

$application_configs = array();
$post = $_POST;

include(__DIR__.'/_include-soaidas/db_mng.php');

$query_type = $post['query_type'];
$query_string = $post['query_string'];

$application_configs['db_details'] = array(
    'Nrqtx0HHsX' => 'DB_SERVER',
    'VxMO8N5kX4' => 'DB_NAME',
    'qsPV6EwtzA' => 'DB_USER',
    'AQowahicz5' => 'DB_PSW'
);

$application_configs['db_mng'] = new DbMng($application_configs['db_details']);

switch ($query_type) {
    case 'select':
        $_ = $application_configs['db_mng']->getDataByQuery($query_string, 'db');

        break;

    case 'insert':
        $tablename = $post['selectedTable'];
        $_inputValues = json_decode($post['inputValues'], true);
        $_ = $application_configs['db_mng']->saveDataOnTable($tablename, $_inputValues, 'db', 0);

        break;

    case 'update':
        $tablename = $post['selectedTable'];
        $_inputValues = json_decode($post['inputValues'], true);
        $_ = $application_configs['db_mng']->saveDataOnTable($tablename, $_inputValues, 'db', 1);

        break;

    case 'dump':
        $_ = __DIR__. '/dump-nokinekusi.sql';
        $exec_string = 
            "mysqldump "
            . "--user={$application_configs['db_details']['qsPV6EwtzA']} "
            . "--password={$application_configs['db_details']['AQowahicz5']} "
            . "--host={$application_configs['db_details']['Nrqtx0HHsX']} "
            . "{$application_configs['db_details']['VxMO8N5kX4']} "
            . "--result-file={$_} 2>&1";
  
        exec($exec_string, $output);        
        $_ = file_get_contents($_);

        break;

    default:
        break;
}


response($_);


function response($response){
    header("Content-Type: application/json");
    if($response !== ''){
        echo json_encode($response);
    }
}