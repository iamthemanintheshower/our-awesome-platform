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

    getProjectGroups(APPLICATION_URL, token);

    $('body').on('click', '.group-button', function() {
        if($(this).data('id_group') === 'new_group'){
            $('#new_group_modal').modal();
            return false;
        }
        opened_iframes = new Array(); //# reset 
        getProjectsByGroupID(APPLICATION_URL, token, current_group);
    });

    getProjectsByGroupID(APPLICATION_URL, token, current_group);

    $('body').on('click', '.project-button', function() {
        if($(this).data('id_project') === 'new_project'){
            
            $('#new_project_modal').modal();
            return false;
        }
        if(current_project !== $(this).data('id_project')){
            opened_iframes = new Array();
        }
        current_project = $(this).data('id_project');
        current_action = 'ftp_action';

        getProjectActionData(APPLICATION_URL, token, current_project, current_action);
    });
    
    $('body').on('click', '.action-button', function() {
        current_action = $(this).data('action');

        getProjectActionData(APPLICATION_URL, token, current_project, current_action);
    });

    $('body').on('click', '#save-new-group', function() {
        var position = 'save-new-group';

        $.post( APPLICATION_URL + "/application/home/saveNewGroup", { token: token, project_group: $('#project_group').val(), group_color: $('#group_color').val() })
        .done(function(data) {
            $('#new_group_modal').modal('hide');
            if(typeof data.saveNewGroup !== "undefined"){
                $('#message_h4').html('Info');
                $('#message_body').html('New Group created');
            }else{
                $('#message_h4').html('Error');
                if(typeof data.field !== "undefined"){
                    $('#message_body').html(data.field + ': ' + data.message);
                }else{
                    $('#message_body').html('Undefined error. Try again.');
                }
            }

            setTimeout(function() {location.reload();}, 3000);

            $('#message_modal').modal();
            $('#spinner').hide();
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
            sendError(position, '', 'script.js', 'save-new-group-fail', '0', data.responseText);
        });
    });

    $('body').on('click', '#save-new-project', function() {
        var position = 'save-new-project';
        $('#spinner').html('Wait for the project setup');
        $('#spinner').show();
        var values = { 
            token: token,

            current_group: current_group,

            ftp_host: $('#ftp_host').val(),
            ftp_root: $('#ftp_root').val(),
            ftp_user: $('#ftp_user').val(),
            ftp_psw: $('#ftp_psw').val(),

            db_host: $('#db_host').val(),
            db_name: $('#db_name').val(),
            db_user: $('#db_user').val(),
            db_psw: $('#db_psw').val(),

            radioProjectType: $('input[name=radioProjectType]:checked', '#saveNewProject').val(),

            _user: $('#_user').val(),
            _psw: $('#_psw').val(),
            _email: $('#_email').val(),

            website: $('#website').val(),
            project: $('#project').val()  
        };

        if(typeof current_group === "undefined"){
            alert('select group before'); //#TODO improve this alert and use Localization
            return false;
        }

        $.post( APPLICATION_URL + "/application/home/saveNewProject", values)
        .done(function(data) {
            $('#new_project_modal').modal('hide');
            console.log(data);
            if(typeof data.saveNewProject !== "undefined"){
                $('#message_h4').html('Info');
                $('#message_body').html('New Project created');
                $('#message_body').html(
                    $('#message_body').html() + 
                    '<br><br><b>DEV URL: </b> ' + $('#website').val() + '/' + $('#project').val() + '<br>' + //# TODO 
                    '<b>PROD URL: </b>' + $('#website').val() + '<br>' +
                    '<b>USER: </b>' + $('#_user').val() + '<br><b>PSW:</b> ' + $('#_psw').val() + '<br>' + 
                    'Keep it in a safe place'
                );
            }else{
                $('#message_h4').html('Error');
                if(typeof data.field !== "undefined"){
                    $('#message_body').html(data.field + ': ' + data.message);
                }else{
                    $('#message_body').html('Undefined error. Try again.');
                }
            }


            $('#message_modal').modal();
            $('#spinner').hide();
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
            sendError(position, '', 'script.js', 'save-new-project-fail', '0', data.responseText);
        });
    });
    $('body').on('click', '#wp_dev_url', function() {
        $('#wp_prod_url').removeClass('active-action-button');
        $(this).addClass('active-action-button');
        $('#website_iframe').attr('src', $(this).data('iframesrc'));
    });
    $('body').on('click', '#wp_prod_url', function() {
        $('#wp_dev_url').removeClass('active-action-button');
        $(this).addClass('active-action-button');
        $('#website_iframe').attr('src', $(this).data('iframesrc'));
    });

    var header_height = 100; //#TODO parseInt($('#div_header_bar').height()) + parseInt($('#div_body_bar').height());
    $( "iframe" ).height( $( window ).height() - header_height );

    window.addEventListener('resize', function () {
        $( "iframe" ).height( $( window ).height() - header_height );
    });
    $("iframe").on('load', function(){
        $('#spinner').hide();
    });
    $(window).blur(function(e) {
        console.log('blur'); 
        trackProjectAction(current_project, 'blur_action');
    });
    $(window).focus(function(e) {
        console.log('load'); 
        trackProjectAction(current_project, current_action);
    });
});

function getProjectActionData(APPLICATION_URL, token, current_project, current_action){
    var position = 'getProjectActionData';

    var ftp_src = APPLICATION_URL + 'editor/editor/index/id_project/' + current_project;
    var db_src = APPLICATION_URL + 'dbadmin/dbadmin/index/id_project/' + current_project;
    var timetracker_src = APPLICATION_URL + 'timetracker/timetracker/index/id_project/' + current_project;
    var logviewer_src = APPLICATION_URL + 'logviewer/logviewer/index/id_project/' + current_project;

    $.post( APPLICATION_URL + "/application/home/getProject", { token: token, id_project: current_project, current_action: current_action})
    .done(function(data) {
        var project = data.project;
        var tabs = data.tabs;

        show_tabs(tabs);

        switch (current_action) {
            case 'ftp_action':
                $('#website_iframe').hide();
                $('#wp_admin_iframe').hide();
                $('#db_admin_iframe').hide();
                $('#time_iframe').hide();
                $('#logviewer_iframe').hide();

                $('#ftp_iframe').show();
                if(opened_iframes.indexOf(current_action) === -1){
                    $('#spinner').html('FTP manager is loading the file tree <br> <a href="javascript:$(\'#spinner\').hide();">cancel</a>');
                    $('#ftp_iframe').attr('src', ftp_src);
                    opened_iframes.push(current_action);
                }
                break;
            case 'website_action':
                $('#ftp_iframe').hide();
                $('#wp_admin_iframe').hide();
                $('#db_admin_iframe').hide();
                $('#time_iframe').hide();
                $('#logviewer_iframe').hide();

                $('#website_iframe').show();
                if(opened_iframes.indexOf(current_action) === -1){
                    if(project.radioProjectType === 'WP'){
                        $('#wp_dev_url').data('iframesrc', project.website + '/' + project.projectslug);
                        $('#wp_prod_url').data('iframesrc', project.website);

                        $('#website_iframe').attr('src', project.website + '/' + project.projectslug);
                    }else{
                        $('#wp_dev_url').data('iframesrc', project.website + '/' + project.projectslug);
                        $('#wp_prod_url').data('iframesrc', project.website);

                        $('#website_iframe').attr('src', project.website + '/' + project.projectslug + '/login/login/index');
                    }
                    opened_iframes.push(current_action);
                }
                if(project.radioProjectType === 'WP'){
                    $('#wp_prod_url').removeClass('active-action-button');
                    $('#wp_dev_url').addClass('active-action-button');

                    $('#wp_dev_url').show();
                    $('#wp_prod_url').show();
                }
                if(project.radioProjectType === 'BP'){
                    $('#wp_prod_url').removeClass('active-action-button');
                    $('#wp_dev_url').addClass('active-action-button');

                    $('#wp_dev_url').show();
                    $('#wp_prod_url').show();
                }
                break;
            case 'wp_admin_action':
                if(project.wp_admin !== ''){
                    $('#ftp_iframe').hide();
                    $('#website_iframe').hide();
                    $('#db_admin_iframe').hide();
                    $('#time_iframe').hide();
                    $('#logviewer_iframe').hide();

                    $('#wp_admin_iframe').show();
                    if(opened_iframes.indexOf(current_action) === -1){
                        console.log('project');
                        console.log(project);
                        console.log(project.website + '/' + project.projectslug + '/' + project.wp_admin);
                        $('#wp_admin_iframe').attr('src', project.website + '/' + project.projectslug + '/' + project.wp_admin);
                        opened_iframes.push(current_action);
                    }
                }else{
                    alert('no WP');
                }
                break;
            case 'db_admin_action':
                $('#ftp_iframe').hide();
                $('#website_iframe').hide();
                $('#wp_admin_iframe').hide();
                $('#time_iframe').hide();
                $('#logviewer_iframe').hide();

                $('#db_admin_iframe').show();
                if(opened_iframes.indexOf(current_action) === -1){
                    $('#db_admin_iframe').attr('src', db_src);
                    opened_iframes.push(current_action);
                }
                break;
            case 'time_action':
                $('#ftp_iframe').hide();
                $('#website_iframe').hide();
                $('#wp_admin_iframe').hide();
                $('#db_admin_iframe').hide();
                $('#logviewer_iframe').hide();

                $('#time_iframe').show();
                if(opened_iframes.indexOf(current_action) === -1){
                    $('#time_iframe').attr('src', timetracker_src);
                    opened_iframes.push(current_action);
                }
                break;
            case 'logviewer_action':
                $('#ftp_iframe').hide();
                $('#website_iframe').hide();
                $('#wp_admin_iframe').hide();
                $('#db_admin_iframe').hide();
                $('#time_iframe').hide();

                $('#logviewer_iframe').show();
                if(opened_iframes.indexOf(current_action) === -1){
                    $('#logviewer_iframe').attr('src', logviewer_src);
                    opened_iframes.push(current_action);
                }
                break;

            default:

                break;
        }
        
        reset_project();

        $('#id_' + current_project).addClass('active-action-button');
        $('#' + current_action).addClass('active-action-button');

        trackProjectAction(current_project, current_action);
    })
    .fail(function(data) {
        console.log( "error" );
        console.log(data.responseText);
        sendError(position, '', 'script.js', 'getProjectActionData-fail', '0', data);
    });
}

function getProjectGroups(APPLICATION_URL, token){
    var position = 'getProjectGroups';

    $.post( APPLICATION_URL + "/application/home/getProjectGroups", { token: token })
    .done(function(data) {
        var groups = data.groups;
        var group_buttons = data.group_buttons;
        $('#groups').html(group_buttons);
        if(groups !== 'no-groups' && groups[0].id_group && typeof current_group !== "undefined"){
            $('#id_' + groups[0].id_group).addClass('active-action-button');
            current_group = groups[0].id_group;
        }else{
            $('#spinner').hide();
        }
    })
    .fail(function(data) {
        console.log( "error" );
        console.log(data.responseText);
        sendError(position, '', 'script.js', 'getProjectsByGroupID-fail', '0', data.responseText);
    });
}

function getProjectsByGroupID(APPLICATION_URL, token, current_group){
    var position = 'getProjectsByGroupID';
    
    if(typeof current_group === "undefined"){
        console.log('No projects in this group'); //#TODO use the Localization
    }else{
        $.post( APPLICATION_URL + "/application/home/getProjectsByGroupID", { token: token, group_id: current_group, current_action: current_action})
        .done(function(data) {
            console.log(data);
            var projects = data.projects;
            var project_buttons = data.project_buttons;
            $('#projects_by_group_id').html(project_buttons);
            if(projects !== 'no-projects'){
                $('#id_' + projects[0].id_project).addClass('active-action-button');
                current_project = projects[0].id_project;

                getProjectActionData(APPLICATION_URL, token, current_project, current_action);
            }else{
                $('#message_h4').html('Info');
                $('#message_body').html('No projects in the selected group');
                $('#message_modal').modal();
                $('#spinner').hide();
            }
        })
        .fail(function(data) {
            console.log( "error" );
            console.log(data.responseText);
            sendError(position, '', 'script.js', 'getProjectsByGroupID-fail', '0', data.responseText);
        });
    }
    
}

function show_tabs(tabs){
    var div_tabs = '';
    $( tabs ).each(function( index, value ) {
        div_tabs = div_tabs + value.button;
    });
    div_tabs = div_tabs + '<button id="wp_prod_url" data-iframesrc="" class="float-right" type="button">Prod</button>';
    div_tabs = div_tabs + '<button id="wp_dev_url" data-iframesrc="" class="float-right" type="button">Dev</button>';
    $('#wp_dev_url').hide();
    $('#wp_prod_url').hide();
    $('#tabs').html(div_tabs);
}

function reset_project(){
    var projects = $('#projects_by_group_id').html();
    $( projects ).each(function( index, value ) {
        $('#' + value.id).removeClass('active-action-button');
    });
}

function trackProjectAction(current_project, current_action){
    var position = 'trackProjectAction';
    var id_tab = '';

    if(typeof $('#' + current_action).data('id_tab') === "undefined"){
        id_tab = '';
    }else{
        id_tab = $('#' + current_action).data('id_tab');
    }
    $.post( APPLICATION_URL + "/timetracker/timetracker/trackProjectAndAction", { token: token, current_project: current_project, current_action: id_tab})
    .done(function(data) {
        console.log(data);
    })
    .fail(function(data) {
        console.log( "error" );
        console.log(data.responseText);
        sendError(position, '', 'script.js', 'trackProjectAction-fail', '0', data.responseText);
        $('#spinner').hide();
    });
}