$(document).ready(function(){

});

function startAjax(){$('#is_sended_ajax').val('1');loading(true);}
function stopAjax(){$('#is_sended_ajax').val('0');loading(false);}
function processAjax(){return $('#is_sended_ajax').val() == '1';}