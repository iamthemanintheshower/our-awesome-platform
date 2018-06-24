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
 * Description of DbMng
 *
 * @author imthemanintheshower
 */

class WSConsumer {

    public function db_ws($ws_details, $query, $_select_insert_update, $selectedTable, $inputValues){

        $fields = array('query_type' => $_select_insert_update, 'query_string' => $query, 'selectedTable' => $selectedTable, 'inputValues' => $inputValues);
        $post_ = 'query_type='.$_select_insert_update.'&query_string='.$query.'&selectedTable='.$selectedTable.'&inputValues='. json_encode($inputValues);

        return $this->_ws($ws_details, $fields, $post_);

    }

    public function filelist_ws($ws_details){

        $fields = array();
        $post_ = '';

        return $this->_ws($ws_details, $fields, $post_);

    }


    private function _ws($ws_details, $fields, $post_){
        $ws_url = $ws_details['ws_url'];
        $ws_user = $ws_details['user'];
        $ws_psw = $ws_details['psw'];

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$ws_url);
        curl_setopt($ch, CURLOPT_USERPWD, $ws_user . ":" . $ws_psw);
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

        $_ws = curl_exec($ch);
//        var_dump(curl_error($ch)); //# debug
//        var_dump(curl_getinfo($ch)); //# debug
        curl_close($ch);
//        var_dump($_ws);
        return $_ws;
    }
}