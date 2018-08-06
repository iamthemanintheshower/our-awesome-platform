<?php
/*
MIT License

Copyright (c) 2017 https://github.com/iamthemanintheshower

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
 * Description of Localization
 *
 * @author imthemanintheshower
 */

class localization{
    public function getLocalization($language, $module, $controller, $action){
        $_language = array(
            'IT' => array(
                'login' => 
                    array(
                        'empty' => 'Devi indicare nome utente e password',
                        'email_or_password_error' => 'Credenziali errate'
                    ),
                'default' => 
                    array(
                        'error-log-done' => 'Qualcosa è successo e ho avvertito un amministratore. Riceverai una notiica quando il problema verrà analizzato.',
                        'error-log-fail' => 'Qualcosa è successo, ma NON sono riuscito ad avvertire alcun amministratore per via di altri problemi. Se non mandi tu una mail a problems@ourawesomeplatform.com, nessun amministratore lo saprà mai e non potrà gestire il problema...',
                        'not-logged' => 'clicca qui per accedere'
                    )
            ),
            'EN' => array(
                'login' => 
                    array(
                        'empty' => 'Something is wrong',
                        'email_or_password_error' => 'Something is wrong',
                        'not-logged' => 'Click here to login'
                    ),
                'default' => 
                    array(
                        'error-log-done' => 'Something is wrong',
                        'error-log-fail' => 'Something is wrong'
                    )
            )
        );
        return $_language[$language][$action];
    }
}