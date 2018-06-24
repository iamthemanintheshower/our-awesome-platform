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
        $project = $page_data['project'];
        $website = $project['website'];
        $project_domain = str_replace('https://', '', $website);
        ?>
        <script>
            var current_project = "<?php echo $project['id_project'];?>"
        </script>
    </head>

    <body>

        <div class="container-fluid">
            <div id="div_body" class="row">
                <div class="col-md-2">
                    <div id="left-sidebar"></div>
                </div>
                <div class="col-sm-10">
                    <div id="buttons_dbadmin">
                        <button id="db_query__action" data-action="db_query" class="db-query-action-button active" type="button">query</button>
                        <button id="db_structure__action" data-action="db_structure" class="db-query-action-button" type="button">structure</button>
                        <button id="db_insert__action" data-action="db_insert" class="db-query-action-button" type="button">insert</button>
                        <button id="db_history__action" data-action="db_history" class="db-query-action-button float-right" type="button">query history</button>
                    </div>

                    <div id="dbadmin">
                        <div id="db_query">
                            <textarea type="text" id="query_text" name="query_text"></textarea>
                            <button id="execute_raw_query">Execute</button>
                            <div id="tblRow__container">
                                <div id="tblRow"></div>
                            </div>
                        </div>
                        <div id="db_structure">
                            <div id="tblRow__structure"></div>
                        </div>
                        <div id="db_insert">
                            <div id="tblRow__insert"></div>
                        </div>
                        <div id="db_history">
                            <div id="tblRow__history"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>