History = {};

History.stack = [];

History.count = 0;

// Push a new screen into history
History.push = function(s, p) {
    this.stack[this.count] = { screen: s, param: p };
	this.count = this.count+1;
}

// Pop the last screen of the history
History.pop = function() {
	this.count = this.count-1;
	return this.stack[this.count];
}

// Get last screen name in the history
History.getScreen = function() {
	return this.stack[this.count-1].screen;
}

// Get last screen param in the history
History.getParam = function() {
	return this.stack[this.count-1].param;
}

// Set back button event
$('#btnBackHistory').live('vclick', function(event, ui) {
	var pop = History.pop();
	$.mobile.changePage(pop.screen);
});