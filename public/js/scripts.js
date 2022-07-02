$(document).ready(function(){

});

function startAjax(){$('#is_sended_ajax').val('1');}
function stopAjax(){$('#is_sended_ajax').val('0');}
function processAjax(){return $('#is_sended_ajax').val() == '1';}