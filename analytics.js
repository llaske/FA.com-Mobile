// Trace in Google Analytics

Stats = {}


// Init Google Analytics
Stats.init = function() {
	// Create a random GUID and store it in the local storage
	var cid = LocalStorage.getValue("cid");
	if (cid == null) {
		cid = createUUID();
		LocalStorage.setValue("cid", cid);
	}
	this.cid = cid;
}


// Send a page view to Google Analytics
Stats.trace = function(page) {
	// Init request parameter
	var url = "http://www.google-analytics.com/collect";
	var handleAs = "text";
	
	// Set trace info
	var postBody = "v=1&tid=UA-18664661-1&cid="+this.cid+"&t=pageview&dp="+encodeURIComponent(page);
	$.post(url, postBody, function(r,s) { console.log(s); }, handleAs);
}


// Create a random GUID compliant with http://www.ietf.org/rfc/rfc4122.txt
// Based on the work of Kevin Hakanson 
function createUUID() {
    var s = [];
    var hexDigits = "0123456789abcdef";
    for (var i = 0; i < 36; i++) {
        s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[14] = "4"; 
    s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);
    s[8] = s[13] = s[18] = s[23] = "-";

    var uuid = s.join("");
    return uuid;
}