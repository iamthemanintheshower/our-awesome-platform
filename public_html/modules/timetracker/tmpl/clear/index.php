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
        $getTimeTracker = $page_data['getTimeTracker'];
        $project_domain = str_replace('https://', '', $website);
        ?>
        <script>
            var current_project = "<?php echo $project['id_project'];?>"
        </script>
    </head>

    <body>

        <div class="container-fluid">

            <div id="div_body" class="row">
                <div class="col-md-12">
                    <?php
                    foreach ($getTimeTracker as $tracker){
                        echo $tracker['insert'].'<br>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>