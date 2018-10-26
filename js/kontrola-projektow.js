function ShowForumMonth(search_month){
    check_content = $("#wpisy-month-"+search_month).html();
    if(check_content == ""){
        get_html({'params': {month : search_month},
                'type'  : 'POST',
                'action': 'include/classes/ajax/get-post-by-month.php',
                'return_type' : 'html',
                'return_object_id' : "#wpisy-month-"+search_month
        });
    }else{
        $("#wpisy-month-"+search_month).html("");
    }
}