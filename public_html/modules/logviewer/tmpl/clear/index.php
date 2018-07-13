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
?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $application_configs['APPLICATION_NAME'];?> - <?php echo $page->getTitle($module);?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $page->getInitScript($application_configs, $page->getToken());

        //# CSS
        echo $page->getCss($application_configs);

        //# JS
        echo $page->getJs($application_configs);        

        //#custom on page
        $_project = $page_data['project'];

        $getLogsForViewer = $page_data['getLogsForViewer'];

        ?>
        <script>
            var current_project = "<?php echo $_project['id_project'];?>"
        </script>
    </head>

    <body>

        <div class="container-fluid">

            <div id="div_body" class="row">
                <div class="col-md-12">
                    <table>
                        <thead class="bg555">
                            <th>TYPE</th>
                            <th>WHEN</th>
                            <th>TYPE</th>
                            <th>DESCRIPTION</th>
                            <th>LINE</th>
                            <th>FILE</th>
                        </thead>
                        <tbody>
                            <?php
                            if(!isset($_SESSION) || !isset($_SESSION['last_i'])){ $_SESSION['last_i'] = 0; }
                            $i = 0;
                            foreach ($getLogsForViewer as $l){
                                if($l !== ''){
                                    if($_SESSION['last_i'] > $i){ echo '<tr class="bg">'; }
                                    $columns = explode('|', $l);
                                    //TYPE	WHEN	TYPE	DESCRIPTION	LINE	FILE
                                    echo '<td>'.$columns[0].'</td>'; //TYPE
                                    echo '<td>'.date('d/M/Y H:i:s', intval($columns[1])).'</td>'; //WHEN
                                    echo '<td class="font">'.getErrorType($columns[2]).'</td>'; //TYPE
                                    echo '<td class="font">'.$columns[3].'</td>'; //DESCRIPTION
                                    echo '<td class="center">'.$columns[4].'</td>'; //LINE
                                    echo '<td>'.$columns[5].'</td>'; //FILE
                                    echo '</tr>';
                                    $i++;
                                }
                            }
                            $_SESSION['last_i'] = $i;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html><?php
function getErrorType($code){
    $php_error_codes = array(
        '1' => '<span class="r">E_ERROR</span>',
        '2' => '<span class="o">E_WARNING</span>',
        '4' => 'E_PARSE',
        '8' => '<span class="y">E_NOTICE</span>',
        '16' => 'E_CORE_ERROR',
        '32' => 'E_CORE_WARNING',
        '64' => 'E_COMPILE_ERROR',
        '128' => 'E_COMPILE_WARNING',
        '256' => 'E_USER_ERROR',
        '512' => 'E_USER_WARNING',
        '1024' => 'E_USER_NOTICE',
        '2048' => 'E_STRICT',
        '4096' => 'E_RECOVERABLE_ERROR',
        '8192' => 'E_DEPRECATED',
        '16384' => 'E_USER_DEPRECATED',
        '32767' => 'E_ALL'
    );
    if(isset($php_error_codes[$code])){
        return $php_error_codes[$code];
    }else{
        return $code;
    }
}