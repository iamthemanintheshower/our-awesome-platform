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
                            echo filesystem_navigation($application_configs['ROOT_PATH']);
                        }else{
                            echo file_get_contents($website.'/WS-filelist-dsohfidskjf.php');
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
    </body>
</html>