function ValueChange(idPola,newValue){
	document.getElementById(idPola).value = newValue;
	document.formularz.submit();
}

function ValueChangeNoSubmit(idPola,newValue){
	document.getElementById(idPola).value = newValue;
}

function NewWindow(mypage,myname,w,h,scroll){
	LeftPosition = (screen.width - w)/2;
	TopPosition = (screen.height - h)/2;
	settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable';
	window.open(mypage,myname,settings);
}

function Close(){
	window.close();
}

function ChangeAction(Form, Pole, Value){
	$("#"+Pole).val(Value);
	$("#"+Form).submit();
}

function Popup(page){
	NewWindow(page, "Popup", 800, 600, "yes");
}

function ShowPopup(){
	$('#offtop').css("visibility", "visible");
	$('#popup_bg').css("visibility", "visible");
	$('#popup').css("visibility", "visible");
}

function ClosePopup(){
	$('#offtop').css("visibility", "hidden");
	$('#popup_bg').css("visibility", "hidden");
	$('#popup').css("visibility", "hidden");
}

function AutomaticClose(ReturnObj){
	$(ReturnObj).css('background-image', '');
	setTimeout(KomunikatyClose, 5000);
}

function KomunikatyClose(){
	$('#Komunikaty').css('display','none');
	$('#Komunikaty2').css('display','none');
}

var url_base = '';
var url_fullPath = '';

function get_html(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });
    ajax_action(setting, params)
    
}

function save_form(setting){
    var params = '';
    params = setting['params'];
    ajax_action(setting, params)
}

function ajax_action(setting, params){
   Loading(setting['return_object_id']);
   $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).html(html);
                }
                if(setting['do_after']){
                    eval(setting['do_after']);
                }
             }
          })
}

function add_row(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).after(html);
                }
                if(setting['after_get_content']){
                    eval(setting['after_get_content']);
                }
             }
          })
}

function remove_row(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(html == "true"){
                    if(setting['return_object_id']){
                        $(setting['return_object_id']).remove();
                    }
                    if(setting['after_get_content']){
                        eval(setting['after_get_content']);
                    }
                }
             }
          })
}

function save_form_add_row(setting){
    var params = '';
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).after(html);
                }
                if(setting['after_get_content']){
                    eval(setting['after_get_content']);
                }
             }
          })
}

function NewUser(pole, prefix){
    $("#a-add-"+pole).css('display', 'none');
    $("#a-cancel-"+pole).css('display', 'block');
    $("#div-"+pole).css('display', 'block');
    name = $("#nazwa-projektu").val();
    $("#login-"+pole).val(name+(prefix != "" ? "_"+prefix : ""));
}

function CancelNewUser(pole){
    $("#a-add-"+pole).css('display', 'block');
    $("#a-cancel-"+pole).css('display', 'none');
    $("#div-"+pole).css('display', 'none');
    $("#login-"+pole).val("");
    $("#haslo-"+pole).val("");
    $("#email-"+pole).val("");
    $("#name-"+pole).val("");
}

function ChangeSubject(){
    $("#forum-div").css('display', '');
    $("#change-subject-div").css('display', 'block');
}

function NewComment(answer,project,thread,reload){
    if($("#NewWpis").val() == "" || CKEDITOR.instances['NewWpis'].getData() == ""){
        $("#id_wpis").val("");
        var stopka = $("#stopka-wpisu").html();
        $("#NewWpis").val(stopka);
        CKEDITOR.instances['NewWpis'].setData(stopka, function(){if(this.checkDirty()) SaveWpis(project);});
    }
    $("#answer").val(answer);
    $("#add_project").val(project);
    $("#add_thread").val(thread);
    $("#forum-div").css('display', '');
    $("#new-wpis-div").css('display', 'block');
    var name = $("#name_"+project+"_"+thread).html();
    if(name != ""){
        $("#tytul-odpowiedzi").html(name);
        $("#tytul-odpowiedzi").show();
    }
    if(window.opener){
        window.opener.NewComment(answer,project,thread,false);
    }
    CheckIsSzkic(project, 1);
    if(reload){
        window.location.href = "#newform";
    }
}

function Loading(ID){
    $(ID).html("<img src='/images/ajax-loader.gif' />");
}

function EditInfo(id){
    get_html({'params': {id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=edit-info',
                'return_type' : 'html',
                'return_object_id' : "#info_"+id
        });
}

function EditInfoSave(id){
    var params = $('#EditInfo_'+id).serialize();
    params += "&id="+id
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=save-info',
                'return_type' : 'html',
                'return_object_id' : "#info_"+id
        });
}

function EditClientSave(id){
    var params = $('#EditClient_'+id).serialize();
    params += "&id="+id
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=save-client',
                'return_type' : 'html',
                'return_object_id' : "#client_info_"+id
        });
}

function EditClient(id){
    get_html({'params': {id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=edit-client',
                'return_type' : 'html',
                'return_object_id' : "#client_info_"+id
        });
}

function AddUmowa(id){
    get_html({'params': {id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=add-umowa',
                'return_type' : 'html',
                'return_object_id' : "#client_umowa_"+id
        });
}

function AddUmowaSave(id){
    $("#AddUmowa_"+id).submit();
}

function AddUmowaCancel(id){
    get_html({'params': {id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=get-umowa',
                'return_type' : 'html',
                'return_object_id' : "#client_umowa_"+id
        });
}

function EditPayment(user, row, id){
    get_html({'params': {user: user, row : row, id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=edit-payment',
                'return_type' : 'html',
                'return_object_id' : "#platnosci_"+user+"_"+row
        });
}

function SavePayment(user, row, id){
    var nextrow = row + 1;
    var params = $('#PaymentEdit_'+user+'_'+row).serialize();
    params += "&user="+user+"&row="+row+"&id="+id
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=save-payment',
                'return_type' : 'html',
                'return_object_id' : "#platnosci_"+user+"_"+row,
                'do_after' : "AddNextAv("+user+","+nextrow+")"
        });
}

function NewTermin(user, row, id){
    get_html({'params': {user: user, row : row, id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=new-termin',
                'return_type' : 'html',
                'return_object_id' : "#platnosci_"+user+"_"+row
        });
}

function SaveNewTermin(user, row, id){
    var params = $('#PaymentEdit_'+user+'_'+row).serialize();
    params += "&user="+user+"&row="+row+"&id="+id
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=save-new-termin',
                'return_type' : 'html',
                'return_object_id' : "#platnosci_"+user+"_"+row
        });
}

function CancelNewTermin(user, row, id){
    get_html({'params': {user: user, row : row, id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=cancel-new-termin',
                'return_type' : 'html',
                'return_object_id' : "#platnosci_"+user+"_"+row
        });
}

function AddNextAv(user,row){
    get_html({'params': {user : user, row : row},
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=addrow',
                'return_type' : 'html',
                'return_object_id' : "#platnosci_"+user+"_"+row,
                'do_after' : "CalculateSum("+user+")"
        });
}

function CalculateSum(user){
    get_html({'params': {user : user},
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=calculate-suma',
                'return_type' : 'html',
                'return_object_id' : "#info_"+user
        });
}

function OplacPayment(user,row,id){
    var status = ($('#oplac_'+user+'_'+row).attr('checked') === true ? 1 : 0);
    get_html({'params': {user : user, row : row, id : id, payment_oplacona : status},
                'type'  : 'POST',
                'action': 'include/classes/ajax/payment.php?act=check-paid',
                'return_type' : 'html',
                'do_after' : "CheckOplaconaTD("+user+","+row+","+status+")"
        });
}

function CheckOplaconaTD(user,row,status){
    if(status == 1){
        $("#platnosci_"+user+"_"+row).attr('class','platnosci-oplacona');
    }else{
        $("#platnosci_"+user+"_"+row).attr('class','');
    }
    CalculateSum(user);
}

function RemoveUmowa(id){
    if(window.confirm("Czy na pewno chcesz usunąć umowę?")){
        get_html({'params': {id : id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/edit-client.php?act=remove-umowa',
                'return_type' : 'html',
                'return_object_id' : "#client_umowa_"+id
        });
    }
}

function logIn(){
    $(".logIn").css('display', 'none');
    $("#logInForm").css('display', 'block');
}

function Rozwin(id){
    $("#wpis_"+id).attr('class', 'forum-rozwin');
    $("#wpis_rozwin_"+id).html('<a href="javascript:Zwin('+id+')">[zwiń]</a>');
}

function Zwin(id){
    $("#wpis_"+id).attr('class', 'forum-zwin');
    $("#wpis_rozwin_"+id).html('<a href="javascript:Rozwin('+id+')">[rozwiń całość]</a>');
}

function OpenBigForum(project, thread){
    wth = (screen.width * 3)/4;
    hght = (screen.height * 3)/4;
    NewWindow("show-forum.php?project="+project+"&watek="+thread,"forum",wth,hght,"yes");
}

function ReloadForumContainer(project, thread, clean){
    get_html({'params': {},
                'type'  : 'POST',
                'action': 'include/classes/ajax/show-forum.php?project='+project+'&watek='+thread+'&clean='+clean,
                'return_type' : 'html',
                'return_object_id' : "#forum-container"
        });
}

function CopySzkicTd(set_html){
   $("#szkic-cont").html(set_html);
}

function GetSzkicContButton(){
    return $("#szkic-cont").html();
}

function CopySzkicTdInit(){
    if(window.opener){
        var set_html = GetSzkicContButton();
        window.opener.CopySzkicTd(set_html);
    }
}

function NewWpisFormIsOpen(){
    if($("#new-wpis-div").css('display') == "block"){
        return true;
    }
    return false;
}

function CheckIsSzkic(project, open){
    get_html({'params': {project : project, open : open},
                'type'  : 'POST',
                'action': 'include/classes/ajax/check-szkic.php',
                'return_type' : 'html',
                'return_object_id' : "#szkic-cont",
                'do_after' : "CopySzkicTdInit()"
        });
}

function Timer(data,i)
{
    var czas = data;
    //eval('var inter'+i+' = setInterval(Odmierz,1000,--czas,i)');
   eval('var inter'+i+' = setInterval(function(){ Odmierz(--czas,i); if(czas<=0){clearInterval(inter'+i+');  $("#opcje_wpis").css("display", "none"); } },1000)');
  
        
    

}

function Odmierz(data,i)
{
    //var data_now = Math.floor(Date.now() / 1000);
    //var czas = (data - data_now);
    var czas = data;
    
    var sekundy = czas%60;
    var minuty =  Math.floor((czas%3600)/60);
    if(sekundy<10)
    {
        sekundy = '0'+sekundy;
    }
    eval("$('#publikuj"+i+"').text('publikuj '+minuty+':'+sekundy)");
    

//    if(czas <= 0)
//    {
//        eval('clearInterval(inter'+i+')');
//    }
    
}
    
    function EditWpis(i,id)
    {
        var tekst = i;
        console.log(i);
       // var tekst = $('#content'+i).html();
        CKEDITOR.instances['NewWpis'].setData(tekst);
         $("#forum-div").css('display', '');
         $("#new-wpis-div").css('display', 'block');
         $("#id_wpis").val(id); 
    
        
    }
    
