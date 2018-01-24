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
 * Description of ProjectGroup
 *
 * @author imthemanintheshower
 */

class ProjectGroup {
    
    private $db_mng;
    
    public function __construct($db_mng = false) {
        $this->db_mng = $db_mng;
    }


    public function getProjectsIDByGroupID($group_id){
        $selectedTable = 'oap__projects_groups';
        $selectValues_getProjectsIDByGroupID[] = 'project_id';

        $whereValues[] = array('where_field' => 'group_id', 'where_value' => $group_id);
        
        $getProjectsIDByGroupID = $this->db_mng->getDataByWhere($selectedTable, $selectValues_getProjectsIDByGroupID, $whereValues);

        return $getProjectsIDByGroupID['response_columns'];

    }
}
