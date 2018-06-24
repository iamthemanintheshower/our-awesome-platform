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

        $button = $page_data['button'];
        $userbean = unserialize($page_data['userbean']);
        ?>
    </head>

    <body>
        <div id="spinner"><i class="fa fa-spinner fa-spin"></i></div>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="navbar-brand dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Projects</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <div id="projects_by_group_id"></div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <span id="current_project"></span>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                </form>
                <ul class="navbar-nav px-3">
                    <li class="nav-item">
                        <span class="nav-link disabled"><?php echo $userbean->getEmailAndUser();?></span>
                    </li>
                    <li class="nav-item text-nowrap">
                        <a class="nav-link" href="<?php echo $application_configs['APPLICATION_URL'];?>login/login/index">logout</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
            <div id="div_body_bar" class="row">
                <div class="col-md-12">
                    <div id="tabs"></div>
                </div>
            </div>

            <div id="div_body" class="row">
                <div class="col-md-12">
                    <iframe id="ftp_iframe" src=""></iframe>
                    <iframe id="website_iframe" src=""></iframe>
                    <iframe id="wp_admin_iframe" src=""></iframe>
                    <iframe id="db_admin_iframe" src=""></iframe>
                    <iframe id="time_iframe" src=""></iframe>
                </div>
            </div>

            <div class="row footer">
                <div>

                </div>
            </div>
        </div>
    </body>
</html>
