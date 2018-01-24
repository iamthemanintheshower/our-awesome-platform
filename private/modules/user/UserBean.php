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
 * Description of UserBean
 *
 * @author 
 */

class UserBean {
    private $email_and_user = null;
    private $firstName = null;
    private $lastName = null;
    private $id_usertype = null;
    private $id = 0;

    public function getEmailAndUser(){
        return $this->email_and_user;
    }
    public function getFirstName(){
       return $this->firstName;
    }
    public function getLastName(){
       return $this->lastName;
    }
    public function getIdUserType(){
       return $this->id_usertype;
    }
    public function getId(){
       return $this->id;
    }
    
    public function setEmailAndUser($email_and_user){
        $this->email_and_user = $email_and_user;
    }
    public function setFirstName($firstName){
       $this->firstName = $firstName;
    }
    public function setLastName($lastName){
       $this->lastName = $lastName;
    }
    public function setIdUserType($id_usertype){
       $this->id_usertype = $id_usertype;
    }
    public function setId($id){
       $this->id = $id;
    }
}
