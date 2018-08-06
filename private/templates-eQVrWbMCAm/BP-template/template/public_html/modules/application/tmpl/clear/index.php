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
        ?>
    </head>

    <body>

        <div class="container-fluid">
            <div id="div_header_bar" class="row">
                <div class="col-md-10">
                    <?php
                    $userbean = unserialize($page_data['userbean']);
                    ?>
                    <div id="projects_by_group_id"></div>
                </div>
                <div class="col-md-2 text-right">
                    <span><?php echo $userbean->getEmailAndUser();?></span>&nbsp;<span><a href="<?php echo $application_configs['APPLICATION_URL'];?>login/login/index">logout</a></span>
                </div>
            </div>

            <div id="div_body_bar" class="row">
                <div class="col-md-12">
                    <?php

                    ?>
                </div>
            </div>

            <div id="div_body" class="row">
                <div class="col-md-12">
                    
                </div>
            </div>

            <div class="row footer">
                <div>

                </div>
            </div>
        </div>
    </body>
</html>