var save_html = "";
function AddValuation(user){
    save_html = $("#add_valuation_"+user).html();
    Loading("#add_valuation_"+user);
     add_row({'params': {u : user},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=add-valuation',
                'return_type' : 'html',
                'return_object_id' : ".prowizje_client_"+user+":last",
                'after_get_content' : 'IncreaseRowspan('+user+')'
        });
}

function IncreaseRowspan(user){
    var rows = $("#client_"+user).attr("rowspan");
    rows++;
    $("#client_"+user).attr("rowspan", rows);
    $("#add_valuation_"+user).html(save_html);
}

function DecreaseRowspan(user){
    var rows = $("#client_"+user).attr("rowspan");
    rows--;
    $("#client_"+user).attr("rowspan", rows);
}

function EditValuation(val_id){
     get_html({'params': {id : val_id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=edit-valuation',
                'return_type' : 'html',
                'return_object_id' : "#wycena_"+val_id
        });
}

function CancelCompanyEdit(val_id){
     get_html({'params': {id : val_id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=cancel-edit-valuation',
                'return_type' : 'html',
                'return_object_id' : "#wycena_"+val_id
        });
}

function SaveCompanyEdit(val_id){
    var params = $('#ValuationEdit_'+val_id).serialize();
    params += "&id="+val_id;
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=save-valuation',
                'return_type' : 'html',
                'return_object_id' : "#wycena_"+val_id
        });
}

function DeleteValuation(user, val_id){
    if(window.confirm("Czy na pewno chcesz skasować Wycenę?")){
        remove_row({'params': {id : val_id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=delete-valuation',
                'return_type' : 'html',
                'return_object_id' : "#row_"+val_id,
                'after_get_content' : 'DecreaseRowspan('+user+')'
        });
    }
}

function CheckValuation(check, val_id){
    var is_checked = (check.checked == true ? 1 : 0);
    get_html({'params': {id : val_id, check : is_checked},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=check-valuation',
                'return_type' : 'html',
                'return_object_id' : "#prowizja_"+val_id
        });
}

var save_html_value = "";
function AddValuationValue(val_id){
    save_html_value = $("#pro-add-"+val_id).html();
    get_html({'params': {id : val_id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=add-valuation-value',
                'return_type' : 'html',
                'return_object_id' : "#pro-add-"+val_id
        });
}

function CancelValuationValue(val_id){
    $("#pro-add-"+val_id).html(save_html_value);
}

function SaveAddValuationValue(val_id){
    var params = $('#ValuationAddValue_'+val_id).serialize();
    params += "&id="+val_id;
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=save-valuation-value',
                'return_type' : 'html',
                'return_object_id' : "#prowizja_"+val_id
        });
}

function DeleteValuationValue(kw_id){
    if(window.confirm("Czy na pewno chcesz skasować kwotę?")){
        remove_row({'params': {kid : kw_id},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=delete-valuation-value',
                'return_type' : 'html',
                'return_object_id' : "#row_pro_"+kw_id
        });
    }
}

function CheckValuationValue(check, val_id, kw_id){
   var is_checked = (check.checked == true ? 1 : 0);
    get_html({'params': {id : val_id, kid : kw_id, check : is_checked},
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=check-valuation-value',
                'return_type' : 'html',
                'return_object_id' : "#wyplacono_"+kw_id
        });
}
var save_html_wplacono = "";
function EditValuationValuePaid(val_id, kw_id){
      save_html_wplacono = $("#wyplacono_"+kw_id).html();
      get_html({'params': {id : val_id, kid : kw_id},
                    'type'  : 'POST',
                    'action': 'include/classes/ajax/valuation.php?act=edit-valuation-value-paid',
                    'return_type' : 'html',
                    'return_object_id' : "#wyplacono_"+kw_id
            });
}

function CancelValuationValuePaid(kw_id){
    $("#wyplacono_"+kw_id).html(save_html_wplacono);
}

function SaveValuationValuePaid(val_id, kw_id){
    var params = $('#ValuationValuePaid_'+kw_id).serialize();
    params += "&id="+val_id+"&kid="+kw_id;
    save_form({'params': params,
                'type'  : 'POST',
                'action': 'include/classes/ajax/valuation.php?act=save-valuation-value-paid',
                'return_type' : 'html',
                'return_object_id' : "#wyplacono_"+kw_id
        });
}