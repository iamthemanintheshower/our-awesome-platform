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
        $_getProjectDataByID = $_getWebsiteByID = array();
        
        //# project
        $selectedTable = 'oap__projects';
        $selectValues_getProjectDataByID[] = 'id_project';
        $selectValues_getProjectDataByID[] = 'project';
        $selectValues_getProjectDataByID[] = 'website_id';
        $selectValues_getProjectDataByID[] = 'radioProjectType';

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

        if(isset($getProjectDataByID['response_columns'][0])){
            $_getProjectDataByID = $getProjectDataByID['response_columns'][0];
        }
        if(isset($getWebsiteByID['response_columns'][0])){
            $_getWebsiteByID = $getWebsiteByID['response_columns'][0];
        }
        return array_merge($_getProjectDataByID, $_getWebsiteByID);
    }
    
    public function getTabsByProjectID($id_project){
        //# project/tabs
        $selectedTable = 'oap__projects_tabs';
        $selectValues_getTabsByProjectID[] = 'tab_id';

        $userbean = unserialize($_SESSION['userbean-Q4rp']);

        $_usertype_id = $this->getIdUsertypeInProjectByUserID($id_project, $userbean->getId());

        $whereValues[] = array('where_field' => 'project_id', 'where_value' => $id_project);
        $whereValues[] = array('where_field' => 'usertype_id', 'where_value' => $_usertype_id);

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
        $selectValues_getFTPdetailsByID[] = 'ftp_host';
        $selectValues_getFTPdetailsByID[] = 'ftp_root';
        $selectValues_getFTPdetailsByID[] = 'ftp_user';
        $selectValues_getFTPdetailsByID[] = 'ftp_psw';
        
        $whereValues__ftp_details[] = array('where_field' => 'id_ftp_details', 'where_value' => $ftp_id_details);

        $getFTPdetailsByID = $this->db_mng->getDataByWhere($selectedTable__ftp_details, $selectValues_getFTPdetailsByID, $whereValues__ftp_details);

        return array_merge($getFTPdetailsByID['response_columns'][0]);
    }

    public function getProjectTimetracker($project_id){
        $sql_query = 'SELECT timetracker.id_timetracker, projects.project, tabs.tab, timetracker.insert
        FROM `oap__timetracker` timetracker
        LEFT JOIN oap__projects projects ON projects.id_project = timetracker.project_id
        LEFT JOIN oap__tabs tabs ON tabs.id_tab = timetracker.tab_id
        WHERE timetracker.project_id="'.$project_id.'"';
        return $this->db_mng->getDataByQuery($sql_query, 'db');
    }

    public function getIdUsertypeInProjectByUserID($project_id, $user_id){
        $sql_query = 'SELECT user_project_usertype.usertype_id
        FROM `oap__user_project_usertype` user_project_usertype
        WHERE user_project_usertype.user_id="'.$user_id.'"
        AND user_project_usertype.project_id="'.$project_id.'"';
        return $this->db_mng->getDataByQuery($sql_query, 'db')['response'][0]['usertype_id'];
    }

    public function getProjectDBDetails($db_id_details){
        //# db
        $selectedTable__db_details = 'oap__db_details';
        $selectValues_getDBdetailsByID[] = 'id_db_details';
        $selectValues_getDBdetailsByID[] = 'db_host';
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
