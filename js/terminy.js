function ShowDefaultTerminy(container, archid){
    get_html({'params': {id : archid},
                'type'  : 'POST',
                'action': 'include/classes/ajax/terminy.php?act=get-default',
                'return_type' : 'html',
                'return_object_id' : container,
                'do_after' : "scrollToDiv('"+container+"')"
        });
}

function ShowAllTerminy(container, archid){
    get_html({'params': {id : archid},
                'type'  : 'POST',
                'action': 'include/classes/ajax/terminy.php?act=get-all',
                'return_type' : 'html',
                'return_object_id' : container,
                'do_after' : "scrollToDiv('"+container+"')"
        });
}

function ShowZrealizowane(container, archid){
    get_html({'params': {id : archid},
                'type'  : 'POST',
                'action': 'include/classes/ajax/terminy.php?act=get-zrealizowane',
                'return_type' : 'html',
                'return_object_id' : container,
                'do_after' : "scrollToDiv('"+container+"')"
        });
}

function ShowBrakRealizacji(container, archid){
    get_html({'params': {id : archid},
                'type'  : 'POST',
                'action': 'include/classes/ajax/terminy.php?act=get-brak-realizacji',
                'return_type' : 'html',
                'return_object_id' : container,
                'do_after' : "scrollToDiv('"+container+"')"
        });
}

function scrollToDiv(container){
    var new_position = $(container).offset();
    var new_top = new_position.top - 50;
    window.scrollTo(new_position.left,new_top);
}

function RealizacjaCheck(container, archid, payid){
    get_html({'params': {id : payid, archid : archid},
                'type'  : 'POST',
                'action': 'include/classes/ajax/terminy.php?act=check-zrealizowane',
                'return_type' : 'html',
                'return_object_id' : container,
                'do_after' : "scrollToDiv('"+container+"')"
        });
}

function BrakRealizacjiCheck(container, archid, payid){
    get_html({'params': {id : payid, archid : archid},
                'type'  : 'POST',
                'action': 'include/classes/ajax/terminy.php?act=check-brak-realizacji',
                'return_type' : 'html',
                'return_object_id' : container,
                'do_after' : "scrollToDiv('"+container+"')"
        });
}