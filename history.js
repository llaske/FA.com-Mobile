History = {};

History.stack = [];

History.count = 0;

// Push a new screen into history
History.push = function(s, p) {
    this.stack[this.count] = { screen: s, param: p };
	this.count = this.count+1;
	this.save();
}

// Pop the last screen of the history
History.pop = function() {
	this.load();
	this.count = this.count-1;
	this.save();
	return this.stack[this.count];
}

// Clear the history
History.clear = function() {
	this.stack = [];
	this.count = 0;
	this.save();
}

// Get last screen name in the history
History.getScreen = function() {
	return this.stack[this.count-1].screen;
}

// Get last screen param in the history
History.getParam = function() {
	return this.stack[this.count-1].param;
}

// Save stack
History.save = function() {
	LocalStorage.setValue("fa_stackcount", this.count);
	LocalStorage.setValue("fa_stack", this.stack);	
}

History.load = function() {
	var count = LocalStorage.getValue("fa_stackcount");
	if (count == null)
		return;
	this.count = count;
	this.stack = LocalStorage.getValue("fa_stack");
}

// Move to a new page
History.changePage = function(toPage) {
	$.mobile.changePage(prefixPage+toPage);
}

// Set back button event
$('#btnBackHistory').live(clickAction, function(event, ui) {
	var pop = History.pop();
	History.changePage(pop.screen);
});

// Set top page button event
$('#btnTop').live(clickAction, function(event, ui) {
	$('html, body').animate({ scrollTop: 0 }, 0);
});