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

** This code is probably inspired to some code I found on internet but I can't remember where (please let me know if you recognize this code) **

*/

if(isset($_GET['searchstring']) && $_GET['searchstring'] !== ''){
    $searchString = $_GET['searchstring'];
    $path = dirname( dirname(__FILE__) );
    $files = fileList($path);

    foreach ($files as $filename) {
        $content = file_get_contents($path . '/' . $filename);
        if(strpos(strtolower($content), strtolower($searchString))) {
            $filesFound[] = $path . '/' . $filename;
        } 
    }

    if(is_array($filesFound)){
        foreach ($filesFound as $f){
            echo '<a href="javascript:;" class="file" data-dir="'. str_replace(basename($f), '', $f).'" data-file="'.basename($f).'">'.$f.'</a><br>';
        }
    }
}

function fileList($dir, $prefix = '') {
    $_dir = rtrim($dir, '/');
    $result = array();

    foreach (glob("$_dir/*", GLOB_MARK) as &$f) {
        if (substr($f, -1) === '/') {
            $result = array_merge($result, fileList($f, $prefix . basename($f) . '/'));
        } else {
            $result[] = $prefix . basename($f);
        }
    }

    return $result;
}
