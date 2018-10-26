function CancelNewWpis(project){
    if(window.opener){
        window.opener.CancelNewWpis(project);
    }
    $("#answer").val("0");
    $("#NewWpis").val("");
    $("#id_wpis").val("");
    $("#forum-div").css('display', 'none');
    $("#new-wpis-div").css('display', 'none');
    get_html({'params': {text : "", project : project},
                'type'  : 'POST',
                'action': 'include/classes/ajax/save-session.php',
                'return_type' : 'html',
                'do_after' : "CheckIsSzkic("+project+", 0)"
        });
}

function SaveWpis(project){
    text = $("#NewWpis").val();
    if(window.opener){
        window.opener.CopyContent(text);
    }
    get_html({'params': {text : text, project : project},
                'type'  : 'POST',
                'action': 'include/classes/ajax/save-session.php',
                'return_type' : 'html'
        });
}

function SaveSzkic(project){
    text = $("#NewWpis").val();
    //text = CKEDITOR.instances['NewWpis'].getData();
    get_html({'params': {text : text, project : project},
                'type'  : 'POST',
                'action': 'include/classes/ajax/save-szkic.php',
                'return_type' : 'html',
                'return_object_id' : "#szkic-cont",
                'do_after' : "CopySzkicTdInit()"
        });

}

function GetSzkic(project){
    var res = $.ajax({
        url: "include/classes/ajax/get-szkic.php",
        data: "project="+project,
        async: false
    }).responseText;

    if($("#NewWpis").val(res)){
        SaveWpis(project);
        NewComment(0,project,0,true);
    }
}

function CopyContent(text){
    $("#NewWpis").val(text);
}

$().ready(function(){
    if(window.opener){
       if(window.opener.NewWpisFormIsOpen()){
            $("#forum-div").css('display', '');
            $("#new-wpis-div").css('display', 'block');
       }
       set_html = window.opener.GetSzkicContButton();
       CopySzkicTd(set_html);
    }
})