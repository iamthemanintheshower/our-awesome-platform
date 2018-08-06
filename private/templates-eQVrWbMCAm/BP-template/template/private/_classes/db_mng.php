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

class DbMng {

    private $db_details;
    private $file_details;
    private $ws_details;
    private $WSConsumer;

    public function __construct($db_details = false, $file_details = false, $ws_details = false, $WSConsumer = false) {
        $this->db_details = $db_details;
        $this->file_details = $file_details;
        $this->ws_details = $ws_details;
    }

    public function saveDataOnTable($selectedTable, $inputValues, $_dbType, $_insert_update = 0){
        try {

            if($_insert_update === 0){
                $_insert_into__ary = $this->_insert_into($selectedTable, $inputValues);
                $query = $_insert_into__ary['query'];
                $execute_ary = $_insert_into__ary['execute_ary'];
            }else{
                $_update__ary = $this->_update($selectedTable, $inputValues);
                $query = $_update__ary['query'];
                $execute_ary = $_update__ary['execute_ary'];
            }

        } catch(PDOException $pdoE) {
            echo 'errorequery';
            var_dump($pdoE);
        } catch (Exception $e) {
            echo 'Exception';
            var_dump($e);
        }

        switch ($_dbType) {
            case 'db':
                if(!$this->db_details){return false;}
                try {
                    if(!is_array($inputValues)){return 0;}

                    $db = $this->getDB();
                    $stmt = $db->prepare($query);
                    $stmt->execute($execute_ary);

                    if($_insert_update === 0){
                        $newId = $db->lastInsertId();
                    }else{
                        $newId = $stmt->rowCount();
                    }
                    $db = null;
                } catch(PDOException $pdoE) {
                    echo 'errorequery';
                    var_dump($pdoE);
                } catch (Exception $e) {
                    echo 'Exception';
                    var_dump($e);
                }
                return $newId;

            case 'file':
                
                break;

            case 'ws':
                $_select_insert_update = 'insert';
                $_db_ws = $this->_db_ws($query, $_select_insert_update, $selectedTable, $inputValues);
                return json_decode($_db_ws, true);

            default:
                break;
        }
    }

    public function getDataByWhere($selectedTable, $selectValues, $whereValues, $orderBy = ''){
        $selectValuesLenght = sizeof($selectValues);
        $execute_ary = array();
        $i = 1;

        if(!is_array($selectValues)){return 0;}
        if(!$this->db_details){return false;}

        $response_columns = null;

        $db = $this->getDB();

        $select = "SELECT ";

        foreach ($selectValues as $v){
            if($v !== NULL){
                $field = $v;
                if($i < $selectValuesLenght){
                    $select .= '`'.$field.'`, ';
                }else{
                    $select .= '`'.$field.'`';
                }
            }
            $i++;
        }
        $select .= ' FROM `'.$selectedTable.'` ';

        if(count($whereValues) === 1){
            foreach ($whereValues as $v){
                if($v['where_value'] !== NULL){
                    $where_field = $v['where_field'];
                    $select .= ' WHERE `'.$where_field.'` = :'.$where_field;
                }
                if($v['where_value'] !== NULL){
                    $execute_ary[':'.$v['where_field']] = $v['where_value'];
                }
            }
        }

        if(count($whereValues) > 1){
            if($whereValues[0]['where_value'] !== NULL){
                $select .= ' WHERE 1= 1 ';
            }
            foreach ($whereValues as $v){
                if($v['where_value'] !== NULL){
                    $where_field = $v['where_field'];
                    $select .= ' AND `'.$where_field.'` = :'.$where_field;
                }
                if($v['where_value'] !== NULL){
                    $execute_ary[':'.$v['where_field']] = $v['where_value'];
                }
            }
        }

        if($orderBy !== ''){
            $query = $select.' ORDER BY '.$orderBy;
        }else{
            $query = $select;
        }
        $stmt = $db->prepare($query);
        $stmt->execute($execute_ary);

        $row_count = $stmt->rowCount();
        if ($row_count > 0){
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($data as $row){
                $response[] = $row;
            }

            foreach($data as $row){
                $response_columns[] = $row;
            }
        }else{
            $response = 'no-rows';
        }

        $db = null;

        return array('response_columns' => $response_columns, 'response'=>$response);
    }

    public function getDataByQuery($query, $_dbType){
        switch ($_dbType) {
            case 'db':
                if(!$this->db_details){return false;}

                try {
                    $db = $this->getDB();
                    $db->exec("SET NAMES 'utf8';");
                    $stmt = $db->prepare($query);

                    $stmt->execute();
            
                    $row_count = $stmt->rowCount();
                    if ($row_count > 0){
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach($data as $row){
                            $response[] = $row;
                        }

                        foreach($data as $row){
                            $response_columns = $row;
                        }
                    }else{
                        $response = 'no-rows';
                    }
                    $db = null;

                } catch(PDOException $pdoE) {
                    error_log(print_r($pdoE, true),0);
                }
                if(!isset($response_columns)){
                    $response_columns = array();
                }
                if(!isset($response)){
                    $response = array();
                }
                return array('response_columns' => $response_columns, 'response'=>$response);

            case 'file':
                
                break;

            case 'ws':
                $_select_insert_update = 'select';
                $_db_ws = $this->_db_ws($query, $_select_insert_update);
                return json_decode($_db_ws, true);

            default:
                break;
        }
    }
    
    private function _insert_into($selectedTable, $inputValues){
        $insert_into = "INSERT INTO `".$selectedTable."` ";
        $insert_into .= '(';
        $inputValuesLenght = sizeof($inputValues);
        $execute_ary = array();
        $i = 1;
        foreach ($inputValues as $v){
            if($v['typed_value'] === NULL){
                $inputValuesLenght--;
            }
        }
        foreach ($inputValues as $v){
            $field = $v['field'];
            if($v['typed_value'] !== NULL){
                if($i < $inputValuesLenght){
                    $insert_into .= '`'.$field.'`, ';
                }else{
                    $insert_into .= '`'.$field.'`';
                }
            }
            $i++;
        }
        $insert_into .= ') VALUES';

        $insert_into .= '(';
        $i = 1;
        foreach ($inputValues as $v){
            $field = $v['field'];
            if($v['typed_value'] !== NULL){
                $execute_ary[$field] = $v['typed_value'];
                if($i < $inputValuesLenght){
                    $insert_into .= ':'.$field.', ';
                }else{
                    $insert_into .= ':'.$field;
                }
            }
            $i++;
        }
        $insert_into .= ') ';

        return array('query' => $insert_into, 'execute_ary' => $execute_ary);
    }

    private function _update($selectedTable, $inputValues){
        $update = "UPDATE `".$selectedTable."` ";
        $update .= 'SET ';
        $inputValuesLenght = sizeof($inputValues);
        $execute_ary = array();
        $i = 1;
        foreach ($inputValues as $v){
            if($v['typed_value'] === NULL){
                $inputValuesLenght--;
            }
        }
        foreach ($inputValues as $v){
            $field = $v['field'];
            if($v['typed_value'] !== NULL){
                if($i < $inputValuesLenght){
                    $update .= '`'.$field.'` = :'.$field.', ';
                }else{
                    $update .= '`'.$field.'` = :'.$field;
                }
            }
            if($v['where_value'] !== NULL){
                $where_field = $v['where_field'];
                $update .= ' WHERE `'.$where_field.'` = :'.$where_field;
            }
            $i++;
        }

        $i = 1;
        foreach ($inputValues as $v){
            $field = $v['field'];
            if($v['typed_value'] !== NULL){
                $execute_ary[$field] = $v['typed_value'];
            }
            if($v['where_value'] !== NULL){
                $execute_ary[$v['where_field']] = $v['where_value'];
            }
            $i++;
        }

        return array('query' => $update, 'execute_ary' => $execute_ary);
    }

    private function _db_ws($query, $_select_insert_update, $selectedTable = null, $inputValues = null){
        $WSConsumer = $this->ws_details['WSConsumer'];

        return $WSConsumer->db_ws($this->ws_details, $query, $_select_insert_update, $selectedTable, $inputValues);
    }

    public function getDB(){
        $db_host = $this->db_details['Nrqtx0HHsX'];
        $db_name = $this->db_details['VxMO8N5kX4'];
        $db_user = $this->db_details['qsPV6EwtzA'];
        $db_psw = $this->db_details['AQowahicz5'];
        $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_psw);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}