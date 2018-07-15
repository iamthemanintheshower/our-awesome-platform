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
$_WS_database_import = '<?php
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
*/$application_configs = array();$post = $_POST;include(__DIR__.\'/WP-OAP-FOLDER/_include-soaidas/db_mng.php\');$application_configs[\'db_details\'] = array(\'Nrqtx0HHsX\' => $post[\'db_host\'],\'VxMO8N5kX4\' => $post[\'db_name\'],\'qsPV6EwtzA\' => $post[\'db_user\'],\'AQowahicz5\' => $post[\'db_psw\']);$application_configs[\'db_mng\'] = new DbMng($application_configs[\'db_details\']);$query_string = file_get_contents(__DIR__.\'/#APPLICATION-SLUG#/WP_db_template.sql\');$_ = $application_configs[\'db_mng\']->getDataByQuery($query_string, \'db\');response($_);function response($response){header("Content-Type: application/json");if($response !== \'\'){echo json_encode($response);}}';
$application_configs = array();
$post = $_POST;

if(isset($post) && isset($post['compressed_filename']) && 
    ($post['compressed_filename'] === 'ws-oap.zip' ||  $post['compressed_filename'] === 'project-oaisdakwhe.zip' || $post['compressed_filename'] === 'theme_.zip')){
    $zip = new ZipArchive;

    $compressed_filename = __DIR__.'/'.$post['compressed_filename'];
    if ($zip->open($compressed_filename, ZipArchive::CREATE)!== TRUE) {
        exit("cannot open $compressed_filename");
    }
    if($post['compressed_filename'] === 'theme_.zip'){
        $zip->extractTo(__DIR__.'/-themes-oisdhhwd');
    }else{
        $zip->extractTo(__DIR__);
    }

    $zip->close();

    if($post['compressed_filename'] === 'ws-oap.zip' && isset($post['ws_oap_folder'])){
        rename('ws-oap', $post['ws_oap_folder']);

        $_WS_database_url__content = file_get_contents($post['ws_oap_folder'].'/WS-database-url-jeuastodruvezb.php');
        $_WS_database_url__content = str_replace('#DB-DETAILS#', '$application_configs[\'db_details\'] = array(\'Nrqtx0HHsX\' => \''.$post['db_host'].'\',\'VxMO8N5kX4\' => \''.$post['db_name'].'\',\'qsPV6EwtzA\' => \''.$post['db_user'].'\',\'AQowahicz5\' => \''.$post['db_psw'].'\');', $_WS_database_url__content);

        file_put_contents($post['ws_oap_folder'].'/WS-database-url-jeuastodruvezb.php', $_WS_database_url__content);
        rename($post['ws_oap_folder'].'/WS-database-url-jeuastodruvezb.php', $post['ws_database_url']);
        rename($post['ws_oap_folder'].'/WS-file-list-url-wrestispimvdbt.php', $post['ws_file_list_url']);
        rename($post['ws_oap_folder'].'/WS-find-string-in-file-shiawdjaiowdnw.php', $post['ws_find_string_in_file_url']);
        file_put_contents($post['ws_oap_folder'].'/.htpasswd', $post['ws_user'].':'.$post['ws_psw']);
        $_WS_import = 'WS-database-import-asoiwnoienoiaero.php';

        $_WS_import_php = str_replace('WP-OAP-FOLDER', $post['ws_oap_folder'], $_WS_database_import);
        $_WS_import_php = str_replace('#APPLICATION-SLUG#', $post['application_slug'], $_WS_import_php);

        file_put_contents($_WS_import, $_WS_import_php);

        $_htaccess__content = file_get_contents($post['ws_oap_folder'].'/.htaccess');
        $_htaccess__content = str_replace('#WS-OAP-FOLDER#', __DIR__.'/'.$post['ws_oap_folder'], $_htaccess__content);
        file_put_contents($post['ws_oap_folder'].'/.htaccess', $_htaccess__content);
    }
    response($_);
}



function response($response){
    header("Content-Type: application/json");
    if($response !== ''){
        echo json_encode($response);
    }
}