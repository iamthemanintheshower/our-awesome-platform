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
            if(opened_file_tabs.indexOf(dir+'/'+file) > -1){
                console.log('already');
            }else{
                console.log('added');
                opened_file_tabs.push(dir+'/'+file);
            }
            console.log(data);
            editor.setValue(data.file_content);
        })
        .fail(function( data ) {
            console.log( data );
            sendError('file_onclick', '', 'script.js', 'file_onclick-fail', '0', data);
        });
    });

    $('body').on('click', '#btnSaveHTML', function () {
        var dir = $( '#dir').val();
        var file = $( '#file').val();
        var data = editor.getValue();

        var values = {
            id_project: current_project,
            subfolder: dir,
            file: file,
            data: data,
            token: token
        };

        $.post( APPLICATION_URL + "editor/editor/setFile", values)
        .done(function( data ) {
            console.log( "Data Loaded: " );
            console.log( data );
            alert('Saved');
        })
        .fail(function( data ) {
            console.log( "FAIL: " + data );
            alert('************** failed ************************');
            sendError('#btnSaveHTML', '', 'script.js', 'btnSaveHTML-fail', '0', data);
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