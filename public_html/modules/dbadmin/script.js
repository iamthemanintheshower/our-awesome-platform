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

var inputFields = new Array(new Array(), new Array());
var inputValues = new Array(new Array(), new Array());
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
        var button = $(this).html();
        $(this).html('...loading...');
        $(this).attr('disabled', true);
        get_query_result($(this), button);
    });
    
    $('body').on('click', '.db_table', function () {
        var button = $(this).html();
        $(this).html('...loading...');
        $(this).attr('disabled', true);

        var table = $(this).data('table');
        $('#query_text').val('SELECT * FROM  `' + table + '`');

        get_table_description(table);
        $('.db_table').removeClass('db_table_active');
        $(this).addClass('db_table_active');
        
        get_query_result($(this), button);
    });
    
    $('body').on('click', '.show_on_click', function () {
        alert($(this).data('field'));
    });

    $('body').on('click', '#execute_insert_query', function () {
        inputValues = new Array(new Array(), new Array());
        $.each(inputFields[0], function( o, field ) {
            inputValues[0].push({field:field, typed_value:$('#' + field).val()});
        });
        execute_insert_query();
    });

    $('body').on('click', '#db_history__action', function () {
        get_table_query_history();
    });

    $('body').on('click', '#execute_update_query', function () {
        inputValues = new Array(new Array(), new Array());
        $.each(inputFields[1], function( o, field ) {
            inputValues[1].push({field:field, typed_value:$('#' + field).val()});
        });
        execute_update_query(this);
    });

    $('body').on('click', '.edit_row', function () {
        var tr = $(this).parent();
        var td_s = tr.find('td');

        $.each(td_s, function( i, field ) {
            console.log('#__' + $(field).data('column'));

            $('#update__' + $(field).data('column')).val($(field).data('field'));
        });
        $('#update_row_modal').modal();
    });
    $('body').on('click', '#download_database', function () {
        $('#spinner').show();
        var values = {
            id_project: current_project,
            token: token
        };
        $.post( APPLICATION_URL + "dbadmin/dbadmin/downloaddatabase", values)
        .done(function( data ) {
            $('#text_dump').val(data.getDBDump);

            $('#db_dump_modal').modal();

            $('#spinner').hide();
        })
        .fail(function( data ) {
            console.log( data );
        });
        return false;
    });
});

function getDBTables(){
    $('#spinner').show();
    var values = {
        id_project: current_project,
        token: token
    };

    $.post( APPLICATION_URL + "dbadmin/dbadmin/getDBTables", values)
    .done(function( data ) {
        console.log(data);
        var db_name = data.db_name;
        data = data.getDBTables;
        $.each(data, function( i, table_in_db ) {
            if(typeof table_in_db != 'undefined'){
                $('#left-sidebar').html(
                    $('#left-sidebar').html() + '<div class="db_table" data-table="' + table_in_db + '">' + table_in_db + '</div>'
                );
            }
        });

        $('#left-sidebar').html(
            '<div class="row no-margin">' +
                '<div class="col-md-9 no-margin no-padding">' +
                    '<div class="db_name db_table">' + db_name + '</div>' + $('#left-sidebar').html() +
                '</div>' +
                '<div class="col-md-3 text-center no-margin no-padding">' +
                    '<div><button class="btn db-button" id="download_database">Dw</button></div>' +
                '</div>' +
            '</div>'
        );

        $('.db_name').html(db_name);
        $('#spinner').hide();
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}

function get_table_query_history(){
    $('#spinner').show();
    var values = {
        id_project: current_project,
        token: token
    };
  
    $.post( APPLICATION_URL + "dbadmin/dbadmin/getTableQueryHistory", values)
    .done(function( data ) {
        console.log(data);
        var tblRow__history = '<table>';    
        $.each(data.getExecutedQueriesByProjectID.response, function( i, column ) {
            tblRow__history = tblRow__history + '<tr>';
            if(column.executed_query !== null && column.executed_query.length > 20){
                tblRow__history = tblRow__history + '<td>' + column.id_executed_query + '</td>' + '<td class="show_on_click" data-field="' + column.executed_query + '">' + column.executed_query.substring(0,60) + '</td>' + '<td class="show_on_click" data-field="' + column.query_values + '">' + column.query_values.substring(0,60) + '</td>' + '<td>' + column.insert_time + '</td>';
            }else{
                tblRow__history = tblRow__history + '<td>' + column.id_executed_query + '</td>' + '<td class="show_on_click" data-field="' + column.executed_query + '">' + column.executed_query + '</td>' + '<td>' + column.query_values + '</td>' + '<td>' + column.insert_time + '</td>';
            }
            tblRow__history = tblRow__history + '</tr>';
        });
        $('#tblRow__history').html(tblRow__history + '</table>');
        $('#spinner').hide();
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}

function get_query_result(element, button){
    var query_text_width = $('#query_text').width();
    var result_height = $(window).height() - ( $('#query_text').height() + $('#buttons_dbadmin').height() + 30);
    $('#tblRow__container').width(query_text_width);
    $('#tblRow__container').height(result_height);
    $('#tblRow').html('...loading...');

    var values = {
        id_project: current_project,
        raw_query: $('#query_text').val(),
        token: token
    };
    
    $.post( APPLICATION_URL + "dbadmin/dbadmin/getQueryResult", values)
    .done(function( data ) {
        var tblRow = '<table>';
        tblRow = tblRow + '<tr>';
        if(typeof data.getQueryResult != 'undefined' && data.getQueryResult !== 'no-rows'){
            $.each(data.getQueryResult.response_columns, function( field ) {
                if(field !== null && field.length > 20){
                    tblRow = tblRow + '<th class="show_on_click" data-field="' + field + '">' + field.substring(0,8) + '</th>';
                }else{
                    tblRow = tblRow + '<th data-field="' + field + '">' + field + '</th>';
                }
            });
        }
        tblRow = tblRow + '</tr>';

        if(typeof data.getQueryResult.response != 'undefined' && data.getQueryResult.response !== 'no-rows'){
            $.each(data.getQueryResult.response, function( i, row ) {

                tblRow = tblRow + '<tr>';
                $.each(row, function( column, field ) {
                    if(field !== null && field.length > 20){
                        tblRow = tblRow + '<td class="show_on_click" data-column="' + column +'" data-id_row="' + $('#field_id__update').val() + '" data-field="' + field + '">' + field.substring(0,20) + '</td>';
                    }else{
                        if(field === null){
                            tblRow = tblRow + '<td></td>';
                        }else{
                            tblRow = tblRow + '<td class="edit_row" data-column="' + column +'" data-id_row="' + $('#field_id__update').val() + '" data-field="' + field + '">' + field + '</td>';
                        }
                    }
                });
                tblRow = tblRow + '</tr>';
            });
            $('#tblRow').html(tblRow + '</table>');
        }else{
            $('#tblRow').html('no records found');
        }
        element.html(button);
        element.attr('disabled', false);
    })
    .fail(function( data ) {
        console.log( "FAIL: " + data );
    });
    return false;
}

function get_table_description(tablename){
    $('#spinner').show();
    inputFields = new Array(new Array(), new Array());
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
        if(typeof data_structure!= 'undefined' && typeof data_structure != 'undefined'){
            var response_columns = data_structure.response_columns;
        }

        if(typeof data_structure.response_columns != 'undefined' && typeof data_structure.response_columns != 'undefined'){
            $.each(response_columns, function( field ) {
                tblRow__structure = tblRow__structure + '<th>' + field + '</th>';
            });    
        }

        tblRow__structure = tblRow__structure + '<tr>';
        
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
            tblRow__insert = tblRow__insert + '<td>' + column.Field + '</td>' + '<td>' + _getInputField(column, 'insert') + '</td>';
            tblRow__insert = tblRow__insert + '</tr>';
        });
        tblRow__insert = tblRow__insert + '<tr>';
        tblRow__insert = tblRow__insert + '<td colspan="2"><button id="execute_insert_query">Save</button></td>';
        tblRow__insert = tblRow__insert + '</tr>';
        $('#tblRow__insert').html(tblRow__insert + '</table>');
        $('#spinner').hide();

        //#table update
        var tblRow__update = '<table>';
        $.each(data_structure, function( i, column ) {
            tblRow__update = tblRow__update + '<tr>';
            var getInputField = _getInputField(column, 'update');
            console.log(column);
            tblRow__update = tblRow__update + '<td>' + column.Field + '</td>' + '<td>' + getInputField + '</td>';
            tblRow__update = tblRow__update + '</tr>';
        });
        tblRow__update = tblRow__update + '<tr>';
        tblRow__update = tblRow__update + '<td colspan="2"><button id="execute_update_query">Save</button></td>';
        tblRow__update = tblRow__update + '</tr>';
        tblRow__update = tblRow__update + '<input type="hidden" id="field_id__update" name="field_id__update" value=""/>'
                    + '<input type="hidden" id="field_row_id" name="field_row_id" value=""/>';
        $('#tblRow__update').html(tblRow__update + '</table>');
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}
var field_id__update = '-';
function _getInputField(column, prefix){
    var field_type = '';
    var prefix_ = 0;
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
            field_type = '<input id="' + prefix + '__' + column.Field + '" type="text" value=""/>';
            break;

        case 'text':
            field_type = '<textarea id="' + prefix + '__' + column.Field + '" value=""></textarea>';
            break;

        default:

            break;
    }
    
    if(column.Extra === 'auto_increment' && prefix === 'update'){
        $('#field_id__update').val(column.Field);
        field_id__update = column.Field;
    }
    if(column.Extra === 'auto_increment' && prefix === 'insert'){
        return 'auto_increment';
    }else{
        if(prefix === 'insert'){prefix_ = 0;}
        if(prefix === 'update'){prefix_ = 1;console.log(column.Field);}
        inputFields[prefix_].push(prefix + '__' + column.Field);
        return field_type;
    }
}

function execute_insert_query(){
    $('#spinner').show();
    var values = {
        id_project: current_project,
        tablename: selectedTable,
        inputFields: inputFields[0],
        inputValues: inputValues[0],
        prefix: 'insert',
        token: token
    };

    $.post( APPLICATION_URL + "dbadmin/dbadmin/executeInsertQuery", values)
    .done(function( data ) {
        console.log(data);
        if(data.saveDataOnTable > 0){
            location.reload();
        }else{
            alert('error');
        }
        $('#spinner').hide();
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}

function execute_update_query(this_obj){
    $('#spinner').show();
    var values = {
        id_project: current_project,
        tablename: selectedTable,
        inputFields: inputFields[1],
        inputValues: inputValues[1],
        prefix: 'update',
        token: token,

        field_id__update: field_id__update,
        row_id: $('#update__' + field_id__update).val() //$('#field_row_id').val()
    };
    console.log(values);    
    $.post( APPLICATION_URL + "dbadmin/dbadmin/executeUpdateQuery", values)
    .done(function( data ) {
        console.log(data);
        if(data.saveDataOnTable > 0){
            location.reload();
        }else{
            alert('error');
        }
        $('#spinner').hide();
    })
    .fail(function( data ) {
        console.log( data );
    });
    return false;
}