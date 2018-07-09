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
                'application' =>
                    array(
                        'home' =>
                            array(
                                'index' => 
                                    array(
                                        'groups' => 'Groups',
                                        'projects' => 'Projects',
                                        'search' => 'Search',
                                        'new-project' => 'New project',

                                        //# New project modal
                                        'project' => 'Project',
                                        'website' => 'Website',
                                        'wp-admin' => 'WP admin',

                                        'ws-user' => 'WS user',
                                        'ws-psw' => 'WS psw',
                                        'ws-find-string-in-file' => 'WS find-string-in-file',
                                        'ws-database-file' => 'WS database-file',
                                        'ws-file-list' => 'WS file-list',
                                        
                                        'ftp-host' => 'FTP host',
                                        'ftp-root' => 'FTP root folder',
                                        'ftp-user' => 'FTP user',
                                        'ftp-psw' => 'FTP psw',
                                        
                                        'db-host' => 'DB host',
                                        'db-name' => 'DB name',
                                        'db-user' => 'DB user',
                                        'db-psw' => 'DB psw',
                                        
                                        'project-type-WP' => 'WordPress',
                                        'project-type-BlankProject' => 'Blank Project',
                                        'project-type-None' => 'None',

                                        //# New group modal
                                        'new-group' => 'New group',
                                        'project-group' => 'Group',
                                        'group-color' => 'Color (in HEX)',

                                        'logout' =>'logout',
                                        'close' => 'Close'
                                    ),
                            )
                    ),
                
                'user' =>
                    array(
                        'login' =>
                            array(
                                'getInitScript' => 
                                    array(
                                        'empty' => 'Devi indicare nome utente e password',
                                        'email_or_password_error' => 'Credenziali errate'
                                    ),
                            )
                    ),
                'default' =>
                    array(
                        'default' =>
                            array(
                                'default' => 
                                    array(
                                        'error-log-done' => 'Qualcosa è successo e ho avvertito un amministratore. Riceverai una notiica quando il problema verrà analizzato.',
                                        'error-log-fail' => 'Qualcosa è successo, ma NON sono riuscito ad avvertire alcun amministratore per via di altri problemi. Se non mandi tu una mail a problems@ourawesomeplatform.com, nessun amministratore lo saprà mai e non potrà gestire il problema...',
                                        'not-logged' => 'clicca qui per accedere'
                                    )
                            )
                    ),
            ),

            'EN' => array(
                'application' =>
                    array(
                        'home' =>
                            array(
                                'index' => 
                                    array(
                                        'groups' => 'Groups',
                                        'projects' => 'Projects',
                                        'search' => 'Search',
                                        'new-project' => 'New project',
                                        'new-group' => 'New group',
                                        'logout' =>'logout',
                                        'close' => 'Close'
                                    ),
                            )
                    ),
                
                'user' =>
                    array(
                        'login' =>
                            array(
                                'getInitScript' => 
                                    array(
                                        'empty' => 'Something is wrong',
                                        'email_or_password_error' => 'Something is wrong'
                                    ),
                            )
                    ),
                'default' =>
                    array(
                        'default' =>
                            array(
                                'default' => 
                                    array(
                                        'error-log-done' => 'Something is wrong',
                                        'error-log-fail' => 'Something is wrong',
                                        'not-logged' => 'Click here to login'
                                    )
                            )
                    ),
            )
        );

        if($action === 'default'){
            $module = $controller = 'default';
        }
        return $_language[$language][$module][$controller][$action];
    }
}