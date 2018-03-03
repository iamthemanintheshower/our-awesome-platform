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
 * Description of Project
 *
 * @author imthemanintheshower
 */

class Project {
    
    private $db_mng;
    
    public function __construct($db_mng = false) {
        $this->db_mng = $db_mng;
    }
    
    public function getProjectDataByID($id_project){
        //# project
        $selectedTable = 'oap__projects';
        $selectValues_getProjectDataByID[] = 'id_project';
        $selectValues_getProjectDataByID[] = 'project';
        $selectValues_getProjectDataByID[] = 'website_id';

        $whereValues[] = array('where_field' => 'id_project', 'where_value' => $id_project);

        $getProjectDataByID = $this->db_mng->getDataByWhere($selectedTable, $selectValues_getProjectDataByID, $whereValues);
        
        $website_id = $getProjectDataByID['response_columns'][0]['website_id'];
        
        //# website
        $selectedTable__website = 'oap__websites';
        $selectValues_getWebsiteByID[] = 'id_website';
        $selectValues_getWebsiteByID[] = 'website';
        $selectValues_getWebsiteByID[] = 'wp_admin';
        $selectValues_getWebsiteByID[] = 'ftp_id_details';
        $selectValues_getWebsiteByID[] = 'db_id_details';
        $selectValues_getWebsiteByID[] = 'ws_id_details';
        
        $whereValues__website[] = array('where_field' => 'id_website', 'where_value' => $website_id);

        $getWebsiteByID = $this->db_mng->getDataByWhere($selectedTable__website, $selectValues_getWebsiteByID, $whereValues__website);

        return array_merge($getProjectDataByID['response_columns'][0], $getWebsiteByID['response_columns'][0]);
    }
    
    public function getTabsByProjectID($id_project){
        //# project/tabs
        $selectedTable = 'oap__projects_tabs';
        $selectValues_getTabsByProjectID[] = 'tab_id';

        $whereValues[] = array('where_field' => 'project_id', 'where_value' => $id_project);

        $getTabsByProjectID = $this->db_mng->getDataByWhere($selectedTable, $selectValues_getTabsByProjectID, $whereValues);

        foreach ($getTabsByProjectID['response_columns'] as $v){   
            //# tabs
            $selectedTable_getTabByID = 'oap__tabs';
            $selectValues_getTabByID[] = 'id_tab';
            $selectValues_getTabByID[] = 'tab';
            $selectValues_getTabByID[] = 'html_id';
            $selectValues_getTabByID[] = 'data-action';
            $whereValues_getTabByID[] = array('where_field' => 'id_tab', 'where_value' => $v['tab_id']);

            $getTabByID = $this->db_mng->getDataByWhere($selectedTable_getTabByID, $selectValues_getTabByID, $whereValues_getTabByID);

            $button = new Button();
            $tabs[] = array_merge(
                $getTabByID['response_columns'][0], 
                array(
                    'button' =>
                    $button->getResponse(
                        $getTabByID['response_columns'][0]['tab'], 
                        $getTabByID['response_columns'][0]['html_id'], 
                        'action-button', 
                        'data-action="'.$getTabByID['response_columns'][0]['html_id'].'" data-id_tab="'.$getTabByID['response_columns'][0]['id_tab'].'"' //active-action-button
                    )
                )
            );
            $selectValues_getTabByID = $whereValues_getTabByID = null;
            
        }
        return $tabs;
    }

    public function getProjectFTPDetails($ftp_id_details){
        //# ftp
        $selectedTable__ftp_details = 'oap__ftp_details';
        $selectValues_getFTPdetailsByID[] = 'id_ftp_details';
        $selectValues_getFTPdetailsByID[] = 'ftp_server';
        $selectValues_getFTPdetailsByID[] = 'ftp_user';
        $selectValues_getFTPdetailsByID[] = 'ftp_psw';
        
        $whereValues__ftp_details[] = array('where_field' => 'id_ftp_details', 'where_value' => $ftp_id_details);

        $getFTPdetailsByID = $this->db_mng->getDataByWhere($selectedTable__ftp_details, $selectValues_getFTPdetailsByID, $whereValues__ftp_details);

        return array_merge($getFTPdetailsByID['response_columns'][0]);
    }

    public function getProjectTimetracker($project_id){
        //# ftp
        $selectedTable__ftp_details = 'oap__timetracker';
        $selectValues_getProjectTimetracker[] = 'id_timetracker';
        $selectValues_getProjectTimetracker[] = 'project_id';
        $selectValues_getProjectTimetracker[] = 'tab_id';
        $selectValues_getProjectTimetracker[] = 'insert';
        $selectValues_getProjectTimetracker[] = 'update';

        $whereValues__ftp_details[] = array('where_field' => 'project_id', 'where_value' => $project_id);

        $getProjectTimetracker = $this->db_mng->getDataByWhere(
            $selectedTable__ftp_details, $selectValues_getProjectTimetracker, $whereValues__ftp_details
        );

        return array_merge($getProjectTimetracker['response_columns']);
    }

    public function getProjectDBDetails($db_id_details){
        //# db
        $selectedTable__db_details = 'oap__db_details';
        $selectValues_getDBdetailsByID[] = 'id_db_details';
        $selectValues_getDBdetailsByID[] = 'db_server';
        $selectValues_getDBdetailsByID[] = 'db_name';
        $selectValues_getDBdetailsByID[] = 'db_user';
        $selectValues_getDBdetailsByID[] = 'db_psw';
        
        $whereValues__db_details[] = array('where_field' => 'id_db_details', 'where_value' => $db_id_details);

        $getDBdetailsByID = $this->db_mng->getDataByWhere($selectedTable__db_details, $selectValues_getDBdetailsByID, $whereValues__db_details);

        return array_merge($getDBdetailsByID['response_columns'][0]);
    }

    public function getProjectWSDetails($ws_id_details){
        //# ws
        $selectedTable__ws_details = 'oap__ws_details';
        $selectValues_getWSdetailsByID[] = 'id_ws_details';
        $selectValues_getWSdetailsByID[] = 'ws_user';
        $selectValues_getWSdetailsByID[] = 'ws_psw';
        $selectValues_getWSdetailsByID[] = 'ws_find_string_in_file_url';
        $selectValues_getWSdetailsByID[] = 'ws_database_url';
        $selectValues_getWSdetailsByID[] = 'ws_file_list_url';
        
        $whereValues__ws_details[] = array('where_field' => 'id_ws_details', 'where_value' => $ws_id_details);

        $getWSdetailsByID = $this->db_mng->getDataByWhere($selectedTable__ws_details, $selectValues_getWSdetailsByID, $whereValues__ws_details);

        if(isset($getWSdetailsByID['response_columns'][0])){
            return array_merge($getWSdetailsByID['response_columns'][0]);
        }else{
            return false;
        }
    }
}
