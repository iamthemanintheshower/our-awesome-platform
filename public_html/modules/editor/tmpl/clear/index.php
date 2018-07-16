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

        <div class="container-fluid">
            <div id="div_header_bar" class="row">
                <div class="col-sm-2 no-margin-no-padding">
                    <span id="edit_on_domain" class="pull-left"><?php echo $project_domain;?></span>
                    <span id="current_dir" class="pull-left"></span>
                    <?php if($_wp_admin !== ''){?>
                        <button class="no-margin-no-padding pull-right" id="golive">golive</button>
                        <button class="no-margin-no-padding pull-right" id="disableallplugins">disableallplugins</button>
                        <button class="no-margin-no-padding pull-right" id="htmltowp">HTMLtoWP</button>
                    <?php }?>
                    <button class="no-margin-no-padding pull-right" id="refreshFilelistCacheByProject">R</button>
                </div>
                <div class="col-sm-1 no-margin-no-padding">
                    <button class="no-margin-no-padding" id="btnNewFile">NEW FILE</button>
                    <button class="no-margin-no-padding" id="btnNewDir">NEW DIRECTORY</button>
                </div>
                <div class="col-sm-2">
                    <input type="text" id="searchstringinfile" name="serchstringinfile" placeholder="Type text to search in files"/>
                    <button class="no-margin-no-padding" id="btnSearchStringInFile">FIND IN FILES</button>
                </div>
                <div class="col-sm-2 no-margin-no-padding">
                    <input class="no-margin-no-padding pull-left" id="sortpicture" type="file" name="sortpic" />
                    <button class="no-margin-no-padding pull-right" id="upload">UPLOAD</button>
                </div>
                <div class="col-sm-2 no-margin-no-padding text-right">
                    <button class="no-margin-no-padding" id="btnDeleteFile">DELETE FILE</button>
                    <button class="no-margin-no-padding" id="btnCollectEditedFiles">SAVED FILES</button>
                </div>
                <div class="col-sm-2 no-margin-no-padding pull-right">
                    <button class="no-margin-no-padding" id="btnSaveHTML">SAVE</button>
                </div>
            </div>

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
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
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