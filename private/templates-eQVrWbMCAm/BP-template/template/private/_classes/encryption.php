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
 * Description of encryption
 * inspired to https://gist.github.com/joashp/a1ae9cb30fa533f4ad94
 * @author imthemanintheshower
 */

class Encryption {

    private $encryption_details;
    
    public function __construct($encryption_details = false) {
        $this->encryption_details = $encryption_details;
    }

    public function encrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $this->encryption_details['secret_key']);
        $iv = substr(hash('sha256', $this->encryption_details['secret_iv']), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }

    public function decrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $this->encryption_details['secret_key']);
        $iv = substr(hash('sha256', $this->encryption_details['secret_iv']), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}