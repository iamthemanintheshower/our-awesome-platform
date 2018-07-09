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
                        <a class="navbar-brand dropdown-toggle" href="#" id="dropdown_groups" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['groups'];?></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown_groups">
                            <div id="groups"></div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="navbar-brand dropdown-toggle" href="#" id="dropdown_projects" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['projects'];?></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown_projects">
                            <div id="projects_by_group_id"></div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <span id="current_project"></span>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['search'];?>" aria-label="Search">
                </form>
                <ul class="navbar-nav px-3">
                    <li class="nav-item">
                        <span class="nav-link disabled"><?php echo $userbean->getEmailAndUser();?></span>
                    </li>
                    <li class="nav-item text-nowrap">
                        <a class="nav-link" href="<?php echo $application_configs['APPLICATION_URL'];?>login/login/index"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['logout'];?></a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
            <div id="div_body_bar" class="row">
                <div class="col-md-12">
                    <div id="tabs"></div>
                    <span id="blur_action" data-action="blur_action" data-id_tab="7"></span>
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

        <!-- Modal -->
        <div class="modal fade" id="new_project_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['new-project'];?></h4>
                    </div>
                    <div class="modal-body" id="new_project">

                        <form id="saveNewProject"> <!-- https://getbootstrap.com/docs/4.0/components/modal/ -->
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="project" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['project'];?></label>
                                    <input type="text" class="form-control" id="project" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['project'];?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-9">
                                    <label for="website" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['website'];?></label>
                                    <input type="text" class="form-control" id="website" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['website'];?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="wp_admin" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['wp-admin'];?></label>
                                    <input type="text" class="form-control" id="wp_admin" value="wp-admin">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="ftp_host" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-host'];?></label>
                                    <input type="text" class="form-control" id="ftp_host" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-host'];?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ftp_root" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-root'];?></label>
                                    <input type="text" class="form-control" id="ftp_root" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-root'];?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="ftp_user" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-user'];?></label>
                                    <input type="text" class="form-control" id="ftp_user" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-user'];?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ftp_psw" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-psw'];?></label>
                                    <input type="text" class="form-control" id="ftp_psw" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftp-psw'];?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="db_host" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-host'];?></label>
                                    <input type="text" class="form-control" id="db_host" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-host'];?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="db_name" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-name'];?></label>
                                    <input type="text" class="form-control" id="db_name" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-name'];?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="db_user" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-user'];?></label>
                                    <input type="text" class="form-control" id="db_user" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-user'];?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="db_psw" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-psw'];?></label>
                                    <input type="text" class="form-control" id="db_psw" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['db-psw'];?>">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="save-new-project">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['close'];?></button>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="modal fade" id="new_group_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['new-group'];?></h4>
                    </div>
                    <div class="modal-body" id="new_group">
                        <form> <!-- https://getbootstrap.com/docs/4.0/components/modal/ -->
                            <div class="form-group">
                                <label for="project_group" class="col-form-label">Group</label>
                                <input type="text" class="form-control" id="project_group" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['project-group'];?>">
                            </div>
                            <div class="form-group">
                                <label for="group_color" class="col-form-label">Group color</label>
                                <input type="text" class="form-control" id="group_color" placeholder="<?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['group-color'];?>">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="save-new-group">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['close'];?></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="message_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="message_h4" class="modal-title"></h4>
                    </div>
                    <div class="modal-body" id="message_body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['close'];?></button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>