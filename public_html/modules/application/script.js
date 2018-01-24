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

var current_project = 1;
var current_group = 1;
var current_action = 'ftp_action';
var opened_iframes = new Array();

$( document ).ready(function() {
    'use strict';

    getProjectsByGroupID(APPLICATION_URL, token, current_group);


    $('body').on('click', '.project-button', function() {
        current_project = $(this).data('id_project');
        current_action = 'ftp_action';

        console.log(current_project);
        console.log(current_action);

        getProjectActionData(APPLICATION_URL, token, current_project, current_action);

    });
    
    $('body').on('click', '.action-button', function() {
        current_action = $(this).data('action');

        console.log(current_project);
        console.log(current_action);

        getProjectActionData(APPLICATION_URL, token, current_project, current_action);
    });

    $( "iframe" ).height( $( window ).height() - 150 );

    window.addEventListener('resize', function () {
        $( "iframe" ).height( $( window ).height() - 150 );
    });

});

function getProjectActionData(APPLICATION_URL, token, current_project, current_action){
    var position = 'getProjectActionData';

    var ftp_src = APPLICATION_URL + 'editor/editor/index/id_project/' + current_project;

    $.post( APPLICATION_URL + "/application/home/getProject", { token: token, id_project: current_project, current_action: current_action})
    .done(function(data) {

        var project = data.project;
        var tabs = data.tabs;

        show_tabs(tabs);

        switch (current_action) {
            case 'ftp_action':
                $('#website_iframe').hide();
                $('#wp_admin_iframe').hide();

                $('#ftp_iframe').show();
                $('#ftp_iframe').attr('src', ftp_src);
                break;
            case 'website_action':
                $('#ftp_iframe').hide();
                $('#wp_admin_iframe').hide();

                $('#website_iframe').show();
                $('#website_iframe').attr('src', project.website);
                break;
            case 'wp_admin_action':
                if(project.wp_admin !== ''){
                    $('#ftp_iframe').hide();
                    $('#website_iframe').hide();

                    $('#wp_admin_iframe').show();
                    $('#wp_admin_iframe').attr('src', project.wp_admin);
                }else{
                    alert('no WP');
                }
                break;

            default:

                break;
        }
        
        reset_project();

        $('#id_' + current_project).addClass('active-action-button');
        $('#' + current_action).addClass('active-action-button');
    })
    .fail(function(data) {
        console.log( "error" );
        console.log(data.responseText);
        sendError(position, '', 'script.js', 'getProjectActionData-fail', '0', data);
    });
}


function getProjectsByGroupID(APPLICATION_URL, token, current_group){
    var position = 'getProjectsByGroupID';

    $.post( APPLICATION_URL + "/application/home/getProjectsByGroupID", { token: token, id_group: current_group, current_action: current_action})
    .done(function(data) {
        var projects = data.projects;
        var project_buttons = data.project_buttons;
        $('#projects_by_group_id').html(project_buttons);
        $('#id_' + projects[0].id_project).addClass('active-action-button');
        current_project = projects[0].id_project;

        getProjectActionData(APPLICATION_URL, token, current_project, current_action);
    })
    .fail(function(data) {
        console.log( "error" );
        console.log(data.responseText);
        sendError(position, '', 'script.js', 'getProjectsByGroupID-fail', '0', data.responseText);
    });
}

function show_tabs(tabs){
    $( tabs ).each(function( index, value ) {
        $('#' + value.html_id).removeClass('active-action-button');
        $('#' + value.html_id).show();
    });
}

function reset_project(){
    var projects = $('#projects_by_group_id').html();
    $( projects ).each(function( index, value ) {
        $('#' + value.id).removeClass('active-action-button');
    });
}