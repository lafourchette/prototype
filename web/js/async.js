function waitForMsg(url){
    var url = url;
    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        success: function(data) {
            if(data.msg != '' && data.status != 1)
            {
                $('#log').html(data.msg);
                setTimeout('waitForMsg("'+url+'")',5000);                
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            setTimeout('waitForMsg("'+url+'")',5000);
        }
    });
}