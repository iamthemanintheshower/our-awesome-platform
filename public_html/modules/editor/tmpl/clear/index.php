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
        $_wp_admin = $page_data['wp_admin'];

        $website = $project['website'];
        $project_domain = str_replace('https://', '', $website);
        ?>
        <script>
            var current_project = "<?php echo $project['id_project'];?>"
        </script>
    </head>

    <body>
        <div id="spinner"><i class="fa fa-spinner fa-spin"></i></div>
        <input type="hidden" id="upload_dir" name="upload_dir" />
        <input type="hidden" id="dir" name="dir" value=""/>
        <input type="hidden" id="file" name="file" />
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <span id="edit_on_domain" class="pull-left"><?php echo $project_domain;?></span>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="navbar-brand dropdown-toggle" href="#" id="dropdown_wptools" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['wptools'];?></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown_wptools">
                            <?php if($_wp_admin !== ''){?>
                            <div class="row"><div class="col-md-12"><button class="btn pull-right" id="golive">golive</button></div></div>
                                <button class="btn pull-right" id="disableallplugins">disableallplugins</button>
                                <button class="btn pull-right" id="htmltowp">HTMLtoWP</button>
                            <?php }?>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="navbar-brand dropdown-toggle" href="#" id="dropdown_ftptools" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['ftptools'];?></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown_ftptools">
                            <button class="btn" id="btnNewFile"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;NEW FILE</button>
                            <button class="btn" id="btnNewDir"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;NEW DIR</button>
                            
                            <input class="btn pull-left" id="sortpicture" type="file" name="sortpic" />
                            <button class="btn pull-right" id="upload"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;UPLOAD</button>

                            <input type="text" id="searchstringinfile" name="serchstringinfile" placeholder="Type text to search in files"/>
                            <button class="btn" id="btnSearchStringInFile"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;FIND IN FILES</button>

                            <button class="btn" id="btnDeleteFile"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp;DELETE FILE</button>

                            <button class="btn" id="btnCollectEditedFiles">SAVED FILES</button>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav px-3 pull-left mr-auto">
                    <li class="nav-item">
                        <button class="btn btn-default" id="refreshFilelistCacheByProject"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                    </li>
                </ul>
                <ul class="navbar-nav px-3 pull-left">
                    <li class="nav-item">
                        <span id="current_dir" class="pull-left"></span>
                    </li>
                </ul>
                <ul class="navbar-nav px-3">
                    <li class="nav-item text-nowrap">
                        <button class=" btn btn-default" id="btnSaveHTML"><i class="fa fa-save" aria-hidden="true"></i>&nbsp;SAVE</button>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
            

            <div id="div_body" class="row">
                <div class="col-md-2">
                    <div id="left-sidebar"><?php
                        if($project_domain === $application_configs['APPLICATION_DOMAIN']){
                            $filesystem_navigation = new FileSystemNavigation();
                            echo $filesystem_navigation->filesystem_navigation($application_configs['ROOT_PATH']);
                        }else{
                            if($page_data['filelist_ws']){
                                echo $page_data['filelist_ws'];
                            }else{
                                //# TODO: create label in localize to say 'Configure FTP details'
                            }
                        }
                    ?>
                    </div>
                </div>
                <div class="col-sm-10 no-margin-no-padding">
                    <div id="buttons_editor"></div>
                    <div id="editor"></div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="searchinfile_result_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Search result</h4>
                    </div>
                    <div class="modal-body" id="searchinfile_result">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="collected_edited_files_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Files edited in the current session</h4>
                    </div>
                    <div class="modal-body">
                        <div id="collected_edited_files_sessions"></div>
                        <div id="collected_edited_files"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="new_file_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create new file in the selected folder</h4>
                    </div>
                    <div class="modal-body">
                        <div id="new_file_dir"></div>
                        <input type="text" id="new_file" name="new_file" placeholder="Type the file name with extension"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btnSaveNewFile">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="file_change_history_result_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">History</h4>
                    </div>
                    <div class="modal-body">
                        <div id="file_change_history_result"></div>
                        <textarea id="file_change_history_content"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmupload_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Confirm Upload</h4>
                    </div>
                    <div class="modal-body" id="confirmupload_modal">
                        <button type="button" class="btn btn-primary btnUpload" id="btnUpload">Upload</button>
                        <button type="button" class="btn btn-primary btnUpload" id="btnUpload_Uncompress">Upload & uncompress</button>
                        <button type="button" class="btn btn-primary btnUpload" id="btnUpload_Uncompress_Delete">Upload, uncompress & delete the original file</button>
                        <button type="button" class="btn btn-primary btnUpload" id="btnUpload_WP_Theme">Upload a WP theme</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmltowp_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['html-to-wp'];?></h4>
                    </div>
                    <div class="modal-body" id="htmltowp">
                        <form> <!-- https://getbootstrap.com/docs/4.0/components/modal/ -->
                            <div class="form-group">
                                This function is not ready yet in this platform, but it's just a porting of the WP plugin https://github.com/iamthemanintheshower/WP-from-DEV-to-HTML-LIVE that you can just install in your WP instance.
                                <br><br>
                                <span id="file_buttons"></span>
                            </div>
                            <div class="form-group">
                                <label for="file_to_import" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['file-to-import'];?></label>
                                <input type="text" class="form-control" id="file_to_import">
                            </div>
                            <div class="form-group">
                                <label for="is_index" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['is-index'];?></label>
                                <input id="is_index" name="is_index" class="retrieved_checkbox" class="form-control" type="checkbox">
                            </div>
                            <div class="form-group">
                                <label for="copy_all_folders" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['copy-all-folders'];?></label>
                                <input id="copy_all_folders" name="copy_all_folders" class="retrieved_checkbox" class="form-control" type="checkbox">
                            </div>
                            <div class="form-group">
                                <label for="page_template" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['page-template'];?></label>
                                <input type="text" class="form-control" id="page_template">
                            </div>
                            <div class="form-group">
                                <label for="retrieved_header" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['retrieved-header'];?></label>
                                <textarea id="retrieved_header" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="retrieved_body" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['retrieved-body'];?></label>
                                <textarea id="retrieved_body" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="retrieved_footer" class="col-form-label"><?php echo $page->getLocalization($application_configs['language'], 'application', 'home', 'index')['retrieved-footer'];?></label>
                                <textarea id="retrieved_footer" class="form-control"></textarea>
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

    </body>
</html>