//----------------- Pre JQuery Mobile init

// Set loading message
$(document).bind("mobileinit", function(){
  $.mobile.loadingMessage =  "chargement...";
});

// Init stats
Stats.init();


// HACK: Specific features for Firefox OS
var prefixPage = "";
var clickAction = 'vclick';
var isFFOS = ("mozApps" in navigator && navigator.userAgent.search("Mobile") != -1);
if (!isFFOS)
	$.getJSONWithMoz = $.getJSON;
else {
	prefixPage = "/";
	clickAction = 'click';
	$.getJSONWithMoz = function getJSONMoz(url, data) {
		var xhr = new XMLHttpRequest({ mozSystem: true });
		xhr.dataCallback = data;
		xhr.errorCallback = function(p) {};
		xhr.onreadystatechange = function(e) {
			if (xhr.status == 200) {
				if (xhr.readyState == 4) {
					xhr.dataCallback(JSON.parse(xhr.responseText));
				}
			} else if (xhr.status != 0) {
				xhr.errorCallback(xhr);
			}
		};	
		xhr.error = function(errorCallback) {
			xhr.errorCallback = errorCallback;
			xhr.open("GET", url, true);
			xhr.send(null);
		};
		return xhr;
	};
}