// HTML5 local storage handling

LocalStorage = {};

// Test if HTML5 storage is available
LocalStorage.test = function() {
	return (typeof(Storage)!=="undefined" && typeof(window.localStorage)!=="undefined");
};
	
// Set a value in the storage
LocalStorage.setValue = function(key, value) {
	if (this.test()) {	
		window.localStorage.setItem(key, JSON.stringify(value));
	}
};
	
// Get a value in the storage
LocalStorage.getValue = function(key) {
	if (this.test()) {
		return JSON.parse(window.localStorage.getItem(key));
	}
	return null;
};