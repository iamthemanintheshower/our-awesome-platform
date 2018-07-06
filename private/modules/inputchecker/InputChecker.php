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
 * Description of InputChecker
 *
 * @author imthemanintheshower
 */

class InputChecker {

    public function checkParameters($application_configs, $module, $controller, $action, $post){
        $position = $module.'/'.$controller.'/'.$action;
        $getParametersWhitelist = $this->getParametersWhitelist($position, $application_configs['parameters_whitelist']);
        $post_keys = array_keys($post);

        if($getParametersWhitelist){
            if($this->checkIfThePostKeysAreEqual($post_keys, $getParametersWhitelist)){

                $_checkIfPostDataIsValid = $this->checkIfPostDataIsValid($position, $post, $application_configs['parameters_whitelist']);
                return $_checkIfPostDataIsValid;
            }else{
                $localization = $this->getLocalization($application_configs['language'], $module, $controller, 'default');
                die('1<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
            }
        }else{
            $localization = $this->getLocalization($application_configs['language'], $module, $controller, 'default');
            die($position.'|<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
        }
    }

    private function getParametersWhitelist($position, $parameters_whitelist){
        if(isset($parameters_whitelist[$position])){
            return $parameters_whitelist[$position];
        }else{
            return false;
        }
    }

    private function checkIfThePostKeysAreEqual($post_keys, $whitelist_keys){
        if($whitelist_keys === 'no-parameters'){return true;}

        if($post_keys === array_keys($whitelist_keys)){return true;}else{return false;}
    }

    private function checkIfPostDataIsValid($form, $post, $parameters_whitelist){
        $_valid = false;
        if(isset($parameters_whitelist[$form])){
            $form_fields = $parameters_whitelist[$form];
            if(is_array($form_fields)){
                foreach ($form_fields as $key => $value){
                    if(is_array($value) && $value['require']['required']){
                        if($post[$key] !== ''){
                            $_valid = true;
                        }else{
                            $_valid = false;
                        }
                        $value_message = $value['require']['message'];
                    }else{
                        $_valid = true;
                        $value_message = '';
                    }
                    if(!$_valid){
                        return array('field' => $key, 'valid' => $_valid, 'message' => $value_message);
                    }

                    if(is_array($value) && $value['valid']['required']){
                        if (preg_match($value['valid']['regular_expression'], $post[$key])){
                            $_valid = true;
                        }else{
                            $_valid = false;
                        }
                        $value_message = $value['valid']['message'];
                    }else{
                        $value_message = '';
                    }
                    if(!$_valid){
                        return array('field' => $key, 'valid' => $_valid, 'message' => $value_message);
                    }
                }
            }
        }
        return array('field' => '', 'valid' => true, 'message' => 'ok');
    }


    private function getLocalization($language, $module, $controller, $action){
        $localization = new localization();
        return $localization->getLocalization($language, $module, $controller, $action);
    }

}
