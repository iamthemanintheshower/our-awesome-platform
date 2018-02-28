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

var inputFields = new Array();
var inputValues = new Array();
var selectedTable = '';

$( document ).ready(function() {

    getDBTables();

    $('body').on('click', '.db-query-action-button', function () {
        
        var action = $(this).data('action');
        
        $('.db-query-action-button').removeClass('active');

        $(this).addClass('active');

        switch (action) {
            case 'db_query':
                $('#db_query').show();

                $('#db_structure').hide();
                $('#db_insert').hide();
                $('#db_history').hide();
                break;

            case 'db_structure':
                $('#db_structure').show();

                $('#db_query').hide();
                $('#db_insert').hide();
                $('#db_history').hide();
                break;

            case 'db_insert':
                $('#db_insert').show();

                $('#db_query').hide();
                $('#db_structure').hide();
                $('#db_history').hide();
                break;

            case 'db_history':
                $('#db_history').show();

                $('#db_query').hide();
                $('#db_insert').hide();
                $('#db_structure').hide();
                break;

            default:

            break;
        }

    });

    $('body').on('click', '#execute_raw_query', function () {
        get_query_result();
    });
    
    $('body').on('click', '.db_table', function () {
        var table = $(this).data('table');
        $('#query_text').val('SELECT * FROM  `' + table + '`');

        get_table_description(table);
        $('.db_table').removeClass('db_table_active');
        $(this).addClass('db_table_active');
        
        get_query_result();
    });
    
    $('body').on('click', '.show_on_click', function () {
        alert($(this).data('field'));
    });

    $('body').on('click', '#execute_insert_query', function () {
        inputValues = new Array();
        $.each(inputFields, function( o, field ) {
            inputValues.push({field:field, typed_value:$('#' + field).val()});
        });
        execute_insert_query();
    });
});

function getDBTables(){
    var values = {
        id_project: current_project,
        token: token
    };

    $.post( APPLICATION_URL + "dbadmin/dbadmin/getDBTables", values)
    .done(function( data ) {
        console.log(data);
        data = data.getDBTables;
        $.each(data, function( i, table ) {
            $.each(table, function( i, t ) {
                if(typeof t != 'undefined' && typeof t.Tables_in_Sql1129750_3 != 'undefined'){
                    var table_in_db = t.Tables_in_Sql1129750_3;
                    console.log(table_in_db);
                    $('#left-sidebar').html($('#left-sidebar').html() + '<div class="db_table" data-table="' + table_in_db + '">' + table_in_db + '</div>')
                }
            });
        });
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}

function get_table_query_history(tablename){
    var values = {
        id_project: current_project,
        token: token
    };
  
    $.post( APPLICATION_URL + "dbadmin/dbadmin/getTableQueryHistory", values)
    .done(function( data ) {
        var tblRow__history = '<table>';    
        $.each(data.response, function( i, column ) {
            tblRow__history = tblRow__history + '<tr>';
            if(column.executed_query !== null && column.executed_query.length > 20){
                tblRow__history = tblRow__history + '<td>' + column.id_executed_query + '</td>' + '<td class="show_on_click" data-field="' + column.executed_query + '">' + column.executed_query.substring(0,20) + '</td>' + '<td>' + column.insert_time + '</td>';
            }else{
                tblRow__history = tblRow__history + '<td>' + column.id_executed_query + '</td>' + '<td class="show_on_click" data-field="' + column.executed_query + '">' + column.executed_query + '</td>' + '<td>' + column.insert_time + '</td>';
            }
            tblRow__history = tblRow__history + '</tr>';
        });
        $('#tblRow__history').html(tblRow__history + '</table>');
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}

function get_query_result(){
    var query_text_width = $('#query_text').width();
    var result_height = $(window).height() - ( $('#query_text').height() + $('#buttons_dbadmin').height() + 30);
    $('#tblRow__container').width(query_text_width);
    $('#tblRow__container').height(result_height);

    var values = {
        id_project: current_project,
        raw_query: $('#query_text').val(),
        token: token
    };

    
    $.post( APPLICATION_URL + "dbadmin/dbadmin/getQueryResult", values)
    .done(function( data ) {
        console.log(data);
        data = data.getQueryResult;
        console.log(data);
        var tblRow = '<table>';
        tblRow = tblRow + '<tr>';
        $.each(data.response_columns, function( field ) {
            if(field !== null && field.length > 20){
                tblRow = tblRow + '<th class="show_on_click" data-field="' + field + '">' + field.substring(0,8) + '</th>';
            }else{
                tblRow = tblRow + '<th data-field="' + field + '">' + field + '</th>';
            }
        });
        tblRow = tblRow + '</tr>';

        data = data.response;
        console.log(data);
        if(data !== 'no-rows'){
            $.each(data, function( i, row ) {
                tblRow = tblRow + '<tr>';
                $.each(row, function( i, field ) {
                    if(field !== null && field.length > 20){
                        tblRow = tblRow + '<td class="show_on_click" data-field="' + field + '">' + field.substring(0,20) + '</td>';
                    }else{
                        if(field === null){
                            tblRow = tblRow + '<td></td>';
                        }else{
                            tblRow = tblRow + '<td>' + field + '</td>';
                        }
                    }
                });
                tblRow = tblRow + '</tr>';
            });
            $('#tblRow').html(tblRow + '</table>');
        }else{
            $('#tblRow').html('no records found');
        }
    })
    .fail(function( data ) {
        console.log( "FAIL: " + data );
    });
    return false;
}

function get_table_description(tablename){
    var values = {
        id_project: current_project,
        tablename: tablename,
        token: token
    };
    selectedTable = tablename;
    
    $.post( APPLICATION_URL + "dbadmin/dbadmin/getTableDescription", values)
    .done(function( data ) {
        var data_structure = data.getTableDescription;
        $('#li__db_structure').show();
        $('#li__db_insert').show();

        //#table structure
        var tblRow__structure = '<table>';
        var response_columns = data_structure.response_columns;

        tblRow__structure = tblRow__structure + '<tr>';
        $.each(response_columns, function( field ) {
            tblRow__structure = tblRow__structure + '<th>' + field + '</th>';
        });
        tblRow__structure = tblRow__structure + '</tr>';

        data_structure = data_structure.response;

        $.each(data_structure, function( i, column ) {
            tblRow__structure = tblRow__structure + '<tr>';
            $.each(column, function( n, field ) {
                if(field !== null && field.length > 8){
                    tblRow__structure = tblRow__structure + '<td>' + field + '</td>';
                }else{
                    if(field === null){
                        tblRow__structure = tblRow__structure + '<td></td>';
                    }else{
                        tblRow__structure = tblRow__structure + '<td>' + field + '</td>';
                    }
                }
            });
            tblRow__structure = tblRow__structure + '</tr>';
        });
        $('#tblRow__structure').html(tblRow__structure + '</table>');

        //#table insert
        var tblRow__insert = '<table>';
        $.each(data_structure, function( i, column ) {
            tblRow__insert = tblRow__insert + '<tr>';
            tblRow__insert = tblRow__insert + '<td>' + column.Field + '</td>' + '<td>' + _getInputField(column) + '</td>';
            tblRow__insert = tblRow__insert + '</tr>';
        });
        tblRow__insert = tblRow__insert + '<tr>';
        tblRow__insert = tblRow__insert + '<td colspan="2"><button id="execute_insert_query">Save</button></td>';
        tblRow__insert = tblRow__insert + '</tr>';
        $('#tblRow__insert').html(tblRow__insert + '</table>');

    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}

function _getInputField(column){
    var field_type = '';
    if(typeof column.Type != 'undefined' && typeof column.Type != 'undefined'){
        var column_type = column.Type;
        var column_type_ary = column_type.split('(');
        column_type = column_type_ary[0];        
    }

    switch (column_type) {   
        case 'varchar':
        case 'longtext':
        case 'int':
        case 'bigint':
        case 'datetime':
            field_type = '<input id="__' + column.Field + '" type="text" value=""/>';
            break;

        case 'text':
            field_type = '<textarea id="__' + column.Field + '" value=""></textarea>';
            break;

        default:

            break;
    }
    if(column.Extra === 'auto_increment'){
        return 'auto_increment';
    }else{
        inputFields.push('__' + column.Field);
        return field_type;
    }
}

function execute_insert_query(){
    var values = {
        id_project: current_project,
        tablename: selectedTable,
        inputFields: inputFields,
        inputValues: inputValues,
        token: token
    };
console.log(values);    
    $.post( APPLICATION_URL + "dbadmin/dbadmin/executeInsertQuery", values)
    .done(function( data ) {
console.log(data);
        if(data > 0){
            alert('Inserito');
            raw_query();
        }else{
            alert('error');
        }
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}