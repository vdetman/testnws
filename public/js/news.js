$(document).ready(function(){
	$.isFinish = false;
	$.inProgress = false;
	$(window).scroll(function() {
		if($(window).scrollTop() + $(window).height() >= $(document).height() - 1000 && !$.isFinish && !$.inProgress) {
			loadMore();
		}
	});

	$('.autosubmit').bind("blur keyup", function(){
		if(typeof $.timeout !== 'undefined'){clearTimeout($.timeout);}
		$.timeout = window.setTimeout(function(){ setSearch(); }, 200);
	});
});

function setSearch() {
	const s = $('#search').val();
	const urlParams = new URLSearchParams(window.location.search);
	urlParams.delete('search');
	if (s) {
		urlParams.append('search', s);
	}
	const get = urlParams.toString();
	const newUrl = window.location.pathname + (get ? '?' + get : '');
	window.history.pushState("", "", newUrl);

	// refresh
	var obj = {};
	urlParams.forEach(function(value, key) {
		obj[key] = value;
	});
	$('#filter').val(JSON.stringify(obj));
	$('#loaded').val(0);
	$('#total').val(0);
	$('#newsList').html('');
	loadMore();
}

function loadMore() {
	var wrapper = $('#newsList'), loaded = $('#loaded'), total = $('#total'), loader = $('#loader');
	$.inProgress = true;
	loader.show();
	$.post('/news/loadMore', {
		loaded : loaded.val(),
		filter : $('input#filter').val()
	},
	function(response){
		loader.hide();
		wrapper.append(response.elements);
		total.val(response.total);
		loaded.val(response.loaded);
		$.inProgress = false;
		$.isFinish = response.isFinish;
	}, 'json').fail(function( response ) {
		loader.hide();
		notify('Ajax error', false);
		console.log(response);
	});
}

function refreshRss() {
	$.post('/news/refreshRss', {},
	function(response){
		alert(response.cnt);
		if (response.cnt) {
			window.location.reload();
		}
	}, 'json').fail(function( response ) {
		notify('Ajax error', false);
		console.log(response);
	});
}

function clearCache() {
	$.post('/news/clearCache', {},
	function(){
		alert('success');
		window.location.reload();
	}, 'json').fail(function( response ) {
		notify('Ajax error', false);
		console.log(response);
	});
}
