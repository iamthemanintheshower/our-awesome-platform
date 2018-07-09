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

class Strings {

    private $strings_details;
    
    public function __construct($strings_details = false) {
        $this->strings_details = $strings_details;
    }

    public function getRandomString() {
        $_random_string = '';
        if(isset($this->strings_details) && isset($this->strings_details['getRandomString']['chars'])){
            $chars = $this->strings_details['getRandomString']['chars'];
        }else{
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        }
        if(isset($this->strings_details) && isset($this->strings_details['getRandomString']['lenght'])){
            $lenght = $this->strings_details['getRandomString']['lenght'];
        }else{
            $lenght = 16;
        }
        for ($i = 0; $i < $lenght; $i++) {
            $_random_string .= $chars[rand(0, strlen($chars) -1)];
        }

        return str_shuffle($_random_string);
    }
}