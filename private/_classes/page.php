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
/**
 * Manage files to include
 *
 * @author imthemanintheshower
 */

class page {

    public function _getFilesToInclude($files_to_include){
        foreach ($files_to_include as $include){
            include($include);
        }
    }

    public function _getCss($csss){
        $css_string = '';
        foreach ($csss as $css){
            $css_string .= '<link href="'.$css.'" rel="stylesheet">';
        }
        return $css_string;
    }

    public function _getJs($jss){
        $js_string = '';
        foreach ($jss as $js){
            $js_string .= '<script src="'.$js.'"></script>';
        }
        return $js_string;
    }

    public function _getTitle($module){
        return $module;
    }
    
    public function getToken(){
        $token = new token();
        return $token->getToken();
    }

    public function getLocalization($language, $module, $controller, $action){
        $localization = new localization();
        return $localization->getLocalization($language, $module, $controller, $action);
    }
    
    public function getResponse($application_configs, $module, $action, $post, $optional_parameters){
        $method = '_action_'.$action;
        return $this->$method($application_configs, $module, $action, $post, $optional_parameters);
    }

    public function _getInitScript($application_configs, $token, $page_related_scripts = ''){
        $localization = $this->getLocalization($application_configs['language'], '', '', 'default');
        ?>
        <script>
            var token = "<?php if(isset($token) && $token !== ''){echo $token;}else{echo '';}?>";
            var APPLICATION_URL = "<?php echo $application_configs['APPLICATION_URL']; ?>";
            var error_log_done = '<?php if(isset($localization)){echo $localization['error-log-done'];}?>';
            var error_log_fail = '<?php if(isset($localization)){echo $localization['error-log-fail'];}?>';

            window.onerror = function (message, source, lineno, columnno, error) {
                console.log("Error: " + message + " at line: " + lineno + " source: " + source + " columnNo: " + columnno + " error: " + error);
                sendError('onerror', message, source, lineno, columnno, error);
            }

            function sendError(position, message, source, lineno, columnno, error){
                $.post( APPLICATION_URL + "/errors_mng/errors_mng/log", { position: position, token: token, message: message, source: source, lineno: lineno, columnno: columnno, error: error})
                .done(function(data) {
                    console.log(data);
                    alert(error_log_done);
                })
                .fail(function(data) {
                    console.log(data.responseText);
                    alert(error_log_fail);
                });
                return true;
            }

            <?php echo $page_related_scripts;?>
        </script>
    <?php
    }

}