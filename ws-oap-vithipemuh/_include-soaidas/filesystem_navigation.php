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
 * Description of FileSystemNavigation
 *
 * @author imthemanintheshower
 */

class FileSystemNavigation{

    public function filesystem_navigation($directory) {
        $code = '';
        if( substr($directory, -1) == "/" ) { 
            $directory = substr($directory, 0, strlen($directory) - 1); 
        }
        $code .= $this->filesystem_navigation_folder($directory);
        return $code;
    }

    public function filesystem_navigation_folder($directory, $first_call = true) {
        $filesystem_navigation = '';
        $file_list__temp = scandir($directory); 
        sort($file_list__temp, SORT_NATURAL | SORT_FLAG_CASE);
        $files = $dirs = array();
        foreach($file_list__temp as $current_file) {
            if( is_dir("$directory/$current_file" ) ){ 
                $dirs[] = $current_file;
            }else{ 
                $files[] = $current_file;
            }
        }
        $file_list = array_merge($dirs, $files);

        if( count($file_list) > 2 ) {
            $filesystem_navigation = '<ul';
            if( $first_call ) { $filesystem_navigation .= ' class="filesystem-nav"'; $first_call = false; }
            $filesystem_navigation .= ">";
            foreach( $file_list as $current_file ) {
                if( $current_file !== '.' && $current_file !== '..' ) {
                    if( is_dir("$directory/$current_file") ) {
                        $filesystem_navigation .= '<li class="folder-nav" data-dir="'."$directory/$current_file".'">';
                        $filesystem_navigation .= '<a href="javascript:;">';
                        $filesystem_navigation .= '<span class="glyphicon glyphicon-folder-close"></span>';
                        $filesystem_navigation .= htmlspecialchars($current_file) . '</a>';
                        $filesystem_navigation .= $this->filesystem_navigation_folder("$directory/$current_file", false);
                        $filesystem_navigation .= '</li>';
                    } else {
                        $file_name = urlencode($current_file);
                        $filesystem_navigation .= '<li>';
                        $filesystem_navigation .= '<span class="glyphicon glyphicon-file"></span>';
                        $filesystem_navigation .= '<a href="javascript:;" class="file" data-dir="'.$directory.'" data-file="'.$file_name.'">';
                        $filesystem_navigation .= htmlspecialchars($current_file) . '</a>';
                        $filesystem_navigation .= '</li>';
                    }
                }
            }
            $filesystem_navigation .= "</ul>";
        }
        return $filesystem_navigation;
    }
}