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

var opened_file_tabs = new Array();

$( document ).ready(function() {
    set_mirror_height();

    $(document).on('click','.file',function(){
        var this_ = $(this);
        disable_element(this_);

        var dir = $( this ).data('dir');
        var file = $( this ).data('file');

        $( '#dir').val(dir);
        $( '#file').val(file);

        var values = {
            id_project: current_project,
            subfolder: dir,
            file: file,
            token: token
        };

        $.post( APPLICATION_URL + "editor/editor/getFile", values)
        .done(function( data ) {
            console.log(data);
            if(opened_file_tabs.indexOf(dir+'/'+file) > -1){
                console.log('already');
            }else{
                console.log('added');
                opened_file_tabs.push(dir+'/'+file);
                $('.editor_history').hide();
                $('#buttons_editor').html( $('#buttons_editor').html() + 
                    '<span><a href="javascript:;" class="file" data-dir="' + dir + '" data-file="' + file + '" data-bookmarked_file="' + '">' + file + '</a></span>' +
                    '<span class="pull-right editor_history"><a id="_history_' + dir + '__' + file + '" href="javascript:editor_history()">_history</a></span>'
                );
            }

            if(data.supported_by_the_editor){
                editor.setValue(data.file_content);
            }else{
                var url = dir + '//' + file;
                url = 'https://' + url.replace($('#root_dir').val(), $('#edit_on_domain').html());
                window.open(encodeURI(url), '_blank');
            }
            enable_element(this_);
        })
        .fail(function( data ) {
            console.log( data );
            sendError('file_onclick', '', 'script.js', 'file_onclick-fail', '0', data);
        });
    });

    $( ".file").contextmenu(function() {
        var dir = $( this ).data('dir');
        var file = $( this ).data('file');
        var url = dir + '//' + file;

        url = 'https://' + url.replace($('#root_dir').val(), $('#edit_on_domain').html());
        window.open(encodeURI(url), '_blank');
    });

    $(document).on('click','.dir',function(){
        var dir = $( this ).data('dir');
        console.log(dir);
        $( '#dir').val(dir);
        $( '#current_dir' ).html(dir);
    });

    $('body').on('click', '#btnSaveHTML', function () {
        var this_ = $(this);
        disable_element(this_);
        var data = editor.getValue();
        setFile(this_,data);
    });
    $('body').on('click', '#btnDeleteFile', function () {
        var this_ = $(this);
        disable_element(this_);
        var data = editor.getValue();
        deleteFile(this_,data);
    });

    $('body').on('click', '#btnSearchStringInFile', function () {
        var values = {
            id_project: current_project,
            searchstring: $('#searchstringinfile').val(),
            token: token
        };

        $.post( APPLICATION_URL + "editor/editor/searchStringInFile", values)
        .done(function( data ) {
            $('#searchinfile_result').html(data.stream_searchStringInFile);
            $('#searchinfile_result_modal').modal();
        })
        .fail(function( data ) {
            console.log( data );
            alert('************** failed ************************');
            sendError('#btnSearchStringInFile', '', 'script.js', 'btnSearchStringInFile-fail', '0', data);
        });
    });


    var compressed_filename = '';

    $('body').on('click', '#btnCollectEditedFiles', function () {
        var values = {
            id_project: current_project,
            token: token
        };

        $.post( APPLICATION_URL + "editor/editor/collectEditedFiles", values)
        .done(function( data ) {
            console.log(data);
            compressed_filename = data.compressed_filename;
            var tblRow__edited_files = '<table>';
            $.each(data.get_editorsavelog, function( i, column ) {
                tblRow__edited_files = tblRow__edited_files + '<tr>';
                $.each(column, function( n, field ) {
                    if(field !== null && field.filename.length > 8){
                        tblRow__edited_files = tblRow__edited_files + '<td class="show_on_click" data-field="' + field.bkup_file + '">' + field.bkup_file.substring(0,20) + '</td>';
                    }else{
                        if(field === null){
                            tblRow__edited_files = tblRow__edited_files + '<td></td>';
                        }else{
                            tblRow__edited_files = tblRow__edited_files + '<td>' + field.bkup_file + '</td>';
                        }
                    }
                });

                tblRow__edited_files = tblRow__edited_files + '</tr>';
            });

            $('#collected_edited_files').html(
                tblRow__edited_files + '</table>' + 
                '<a target="_blank" href="' + APPLICATION_URL + 'editor/editor/collectEditedFilesgetFileZIP/compressed_filename/' + compressed_filename + '">Download</a>'
            );

            $('#collected_edited_files_modal').modal();
        })
        .fail(function( data ) {
            console.log( data );
            alert('************** failed ************************');
            sendError('#btnCollectEditedFiles', '', 'script.js', 'btnCollectEditedFiles-fail', '0', data);
        });
    });

    $('body').on('click', '#refreshFilelistCacheByProject', function () {
        var values = {};
        $('#spinner', window.parent.document).html('FTP manager is refreshing the file tree, wait until it\'s done');
        $('#spinner', window.parent.document).show();
        $.post( APPLICATION_URL + "editor/editor/refreshFilelistCacheByProject/id_project/" + current_project, values)
        .done(function( data ) {
            console.log( data );
            $('#left-sidebar').html(data.filelist_ws);
            $('.filesystem-nav').find('ul').hide();

            $('.folder-nav a').click( function() {
                $(this).parent().find('ul:first').slideToggle('fast');
                if($(this).parent().attr('className') === 'folder-nav') return false;
            });
            $('#spinner', window.parent.document).hide();
        })
        .fail(function( data ) {
            console.log( data );
        });
    });
    
    $('body').on('click', '#btnNewFile', function () {
        if($('#dir').val() === ''){
            $( '#dir').val($('#root_dir').val());
        }
        $('#new_file_dir').html($( '#dir').val());
        $('#new_file_modal').modal();
    });

    $('body').on('click', '#btnSaveNewFile', function () {
        var this_ = $(this);
        disable_element(this_);
        $( '#file').val($('#new_file').val());
        setFile(this_, '');
    });

    $('body').on('click', '#btnNewDir', function () {
        var newFolder = getFolderName();
        if(newFolder === null){
            return false;
        }
        var destination_folder = '';
        if($('#dir').val() === ''){
            destination_folder = $('#root_dir').val();
        }else{
            destination_folder = $('#dir').val();
        }

        var values = {
            subfolder: destination_folder,
            newFolder: newFolder,
            token: 'token1'
        };

        $.post( APPLICATION_URL + "editor/editor/setDirectory/id_project/" + current_project, values)
        .done(function( data ) {
            console.log( data );
        })
        .fail(function( data ) {
            console.log( "FAIL: " + data );
        });
        $('#new_file_modal').modal();
    });

    $('#upload').on('click', function() {
        if($( '#dir').val() !== ''){
            $( '#upload_dir').val($( '#dir').val());
        }else{
            $( '#upload_dir').val($( '#root_dir').val());
        }
        
        var file = $( '#file').val();

        if($( '#upload_dir').val() !== ''){
            var confirm_upload = confirm("Sei sicuro di voler caricare " + $( '#upload_dir').val());
            console.log(confirm_upload);
            if(!confirm_upload){
                return false;
            }
        }else{
            alert('Seleziona una cartella');
            return false;
        }
        var file_data = $('#sortpicture').prop('files')[0];   
        var form_data = new FormData();                  
        form_data.append('file', file_data);
        form_data.append('action', 3);

        form_data.append('subfolder', $( '#upload_dir').val());
        form_data.append('file', file);
        form_data.append('token', 'token');

        $.ajax({
            url: APPLICATION_URL + "editor/editor/uploadFile/id_project/" + current_project + "/subfolder/" + $( '#upload_dir').val() + "/file/" + file + "/data/" + file_data,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(php_script_response){
                console.log(php_script_response);
                alert('Upload completo');
                location.reload();
            }
         });
    });
    $('body').on('click', '.view_file_in_history_content', function () {
        console.log($(this).data('file'));
        var file = $( this ).data('file');
        var values = {
            id_project: current_project,
            get_backup: '1',
            file: file,
            token: 'token1'
        };

        $.post( APPLICATION_URL + "editor/editor/getFile", values)
        .done(function( data ) {
            console.log(data);
            $('#file_change_history_content').text(data.file_content);
        })
        .fail(function( data ) {
            console.log( data );
        });
    });

    $('body').on('click', '#golive', function () {
        $('#spinner', window.parent.document).html('The platform is trying to generate the HTML cache of the website. Please, wait until it\'s done');
        $('#spinner', window.parent.document).show();
        var values = {
            id_project: current_project
        };

        $.post( APPLICATION_URL + "application/home/golive", values)
        .done(function( data ) {
            console.log(data);
            $('#spinner', window.parent.document).hide();
        })
        .fail(function( data ) {
            console.log( data );
        });
    });

    $('body').on('click', '#disableallplugins', function () {
        $('#spinner', window.parent.document).html('The platform is trying to disable all plugins of the website. Please, wait until it\'s done');
        $('#spinner', window.parent.document).show();
        var values = {
            id_project: current_project
        };

        $.post( APPLICATION_URL + "application/home/disableallplugins", values)
        .done(function( data ) {
            console.log(data);
            $('#spinner', window.parent.document).hide();
        })
        .fail(function( data ) {
            console.log( data );
        });
    });
    
});


function set_mirror_height(){
    var code_mirror_height = $( window ).height() - 20;
    $( '#editor' ).css( "height", code_mirror_height );
    $( '#left-sidebar' ).css( "height", code_mirror_height );
}
function getFolderName(){
    var newFolder = prompt("Enter folder name: ", "[directory]");
    return newFolder;
}
function setFile(this_, data){
    var dir = $( '#dir').val();
    var file = $( '#file').val();

    var values = {
        id_project: current_project,
        subfolder: dir,
        file: file,
        data: data,
        token: token
    };

    $.post( APPLICATION_URL + "editor/editor/setFile", values)
    .done(function( data ) {
        manage_result(values, data, window.parent.document);
        enable_element(this_);
    })
    .fail(function( data ) {
        console.log( data );
        alert('************** failed ************************');
        sendError('#btnSaveHTML', '', 'script.js', 'btnSaveHTML-fail', '0', data);
    });
}

function deleteFile(this_, data){
    var dir = $( '#dir').val();
    var file = $( '#file').val();

    var values = {
        id_project: current_project,
        subfolder: dir,
        file: file,
        data: data,
        token: token
    };

    $.post( APPLICATION_URL + "editor/editor/deleteFile/id_project/" + current_project + "/subfolder/" + $( '#upload_dir').val() + "/file/" + file, values)
    .done(function( data ) {
        manage_result(values, data, window.parent.document);
        enable_element(this_);
    })
    .fail(function( data ) {
        console.log( data );
        alert('************** failed ************************');
        sendError('#btnSaveHTML', '', 'script.js', 'btnSaveHTML-fail', '0', data);
    });
}
function editor_history() {
    var file = $( '#file').val();
    var values = {file: file, id_project: current_project, token: 'token1'};

    $.post( APPLICATION_URL + "editor/editor/getFileHistory", values)
    .done(function( data ) {
        console.log(data);
        $('#file_change_history_result').html(data.getFileHistory);
        $('#file_change_history_result_modal').modal();
    })
    .fail(function( data ) {
        console.log( data );
    });
}

//# Code Mirror https://codemirror.net
var editor;
window.onload = function() {
  editor = CodeMirror(document.getElementById("editor"), {
    lineNumbers: true,
    matchBrackets: true,
    value: "", 
    mode: "application/x-httpd-php",
    indentUnit: 4,
    indentWithTabs: true,
    styleActiveLine: true,
    autoCloseTags: true,
    viewportMargin: Infinity,
    matchTags: {bothTags: true},
    extraKeys: {
    "'<'": completeAfter,
    "'/'": completeIfAfterLt,
    "' '": completeIfInTag,
    "'='": completeIfInTag,
    "Ctrl-Space": "autocomplete"
    },
    hintOptions: {schemaInfo: tags},
    highlightSelectionMatches: {showToken: /\w/, annotateScrollbar: true},
    lineWrapping: true
  });
};      

var dummy = {
  attrs: {
    color: ["red", "green", "blue", "purple", "white", "black", "yellow"],
    size: ["large", "medium", "small"],
    description: null
  },
  children: []
};

var tags = {
  "!top": ["top"],
  "!attrs": {
    id: null,
    class: ["A", "B", "C"]
  },
  top: {
    attrs: {
      lang: ["en", "de", "fr", "nl"],
      freeform: null
    },
    children: ["animal", "plant"]
  },
  animal: {
    attrs: {
      name: null,
      isduck: ["yes", "no"]
    },
    children: ["wings", "feet", "body", "head", "tail"]
  },
  plant: {
    attrs: {name: null},
    children: ["leaves", "stem", "flowers"]
  },
  wings: dummy, feet: dummy, body: dummy, head: dummy, tail: dummy,
  leaves: dummy, stem: dummy, flowers: dummy
};

function completeAfter(cm, pred) {
  var cur = cm.getCursor();
  if (!pred || pred()) setTimeout(function() {
    if (!cm.state.completionActive)
      cm.showHint({completeSingle: false});
  }, 100);
  return CodeMirror.Pass;
}

function completeIfAfterLt(cm) {
  return completeAfter(cm, function() {
    var cur = cm.getCursor();
    return cm.getRange(CodeMirror.Pos(cur.line, cur.ch - 1), cur) == "<";
  });
}

function completeIfInTag(cm) {
  return completeAfter(cm, function() {
    var tok = cm.getTokenAt(cm.getCursor());
    if (tok.type == "string" && (!/['"]/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length == 1)) return false;
    var inner = CodeMirror.innerMode(cm.getMode(), tok.state).state;
    return inner.tagName;
  });
}

var disabled_element = '';
function disable_element(element){
    disabled_element = element.html();
    element.html('...loading...');
    element.attr('disabled', true);
}
function enable_element(element){
    element.html(disabled_element);
    element.attr('disabled', false);
}
