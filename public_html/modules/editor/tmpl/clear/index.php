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
        <input type="hidden" id="upload_dir" name="upload_dir" />
        <input type="hidden" id="dir" name="dir" />
        <input type="hidden" id="file" name="file" />

        <div class="container-fluid">
            <div id="div_header_bar" class="row">
                <div class="col-sm-12 no-margin-no-padding">
                    <span id="edit_on_domain" style="line-height: 12px;font-weight: bold" class="pull-left"><?php echo $project_domain;?>&nbsp;|&nbsp;</span>
                    <input class="no-margin-no-padding" style="float:left;font-size: 7px" id="sortpicture" type="file" name="sortpic" />
                    <button class="no-margin-no-padding" id="upload">UPLOAD</button>
                    &nbsp;|&nbsp;
                    <input type="text" id="searchstringinfile" name="serchstringinfile" /><button class="no-margin-no-padding" id="btnSearchStringInFile">CERCA</button>
                    &nbsp;|&nbsp;&nbsp;<button class="no-margin-no-padding" id="btnCollectEditedFiles">SAVED-FILES</button>

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
                <div class="col-sm-10">
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

    </body>
</html>