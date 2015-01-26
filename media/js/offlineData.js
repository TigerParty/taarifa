// Send data to the remote server.
function sendDataToServer(serialized_form, success_callback) {
	var form_object = JSON.parse(serialized_form);
    console.log('Sending to server... url:' + form_object.url + ' form:' + form_object.form);
	
    // Send form using a ajax request.
	$.post(form_object.url, form_object.form, function(data) {
	  success_callback();
	  console.log("Success: " + serialized_form);
	});
}

function submitForm(form) {
	// If online, proceed with normal form submission.
	if (navigator.onLine) return true;
	// If offline, save form in localstorage.
	saveDataLocally(form);
	// Prevent form form beeing submitted when offline.
	return false;
}

//called on submit if device is offline from processData()
function saveDataLocally(unserialized_form) {

	var timeStamp = new Date().getTime();
	var serialized_form = JSON.stringify({'url': location.href, 'time': timeStamp, 'form': unserialized_form.serialize()});
	
	try {
		localStorage.setItem(timeStamp, serialized_form);
		console.log('Saved locally: ' + timeStamp + ': ' + serialized_form);
		alert('Thank you for your submission. The data is cached locally and will be send to taarifa as soon as you visit this page with internet connectivity.');
	} catch (e) {
		if (e == QUOTA_EXCEEDED_ERR) {
			console.log('Quota exceeded!');
		}
	}
	
	// About count info.
	updateCount();
}

// Sends locally stored data to the external host.
function sendLocalDataToServer() {
	// Helper function 
	// http://stackoverflow.com/questions/750486/javascript-closure-inside-loops-simple-practical-example
	function createDeleteCallback(keyDelete) {
	    return function(){ // Callback on success.
			// Remove Item
			console.log('Delete ' + keyDelete);
			window.localStorage.removeItem(keyDelete);
			// About count info.
			updateCount();
		};
	}
	
    // Iterate all items.
	for (var key in localStorage){
		var value = window.localStorage.getItem(key); 
		console.log('Loading: ' + key + ': ' + value);
		sendDataToServer(value, createDeleteCallback(key));
    }
	
}

//called when device goes online
function goneOnline(){
    // Notify user about online status.
    notifyUserIsOnline();
    // Start sending cached forms.
    sendLocalDataToServer();
}
//called when device goes offline
function goneOffline(){
	// Notify user about offline status.
	notifyUserIsOffline();
}

// Notifies users about beeing online.
function notifyUserIsOnline() {
	var status = document.querySelector('#status');
	var offlineData = document.querySelector('#offlineData');
	offlineData.className = 'online';
	status.innerHTML = 'Connected';
}
// Notifies users about beeing offline.
function notifyUserIsOffline() {
	var status = document.querySelector('#status');
	var offlineData = document.querySelector('#offlineData');
	offlineData.className = 'offline';
	status.innerHTML = 'Offline';
}
// Notifies users about how many items are stored offline ready to be send.
function updateCount() {
	var length = window.localStorage.length;
	if (length > 0)
	  document.querySelector('#local-count').innerHTML = '(' + length + ' item(s) cached)';
	else 
		document.querySelector('#local-count').innerHTML = '';
}

//called when DOM has fully loaded
function loaded() {
	if(typeof(window.localStorage) == 'undefined') return false;
	
	// Create DOM nodes.
	$('.background').prepend('<div id="offlineData"><span id="status"></span> <span id="local-count"></span></div>');

	// Update count.
	updateCount();
	
	//if online
	if (navigator.onLine) goneOnline();

	//listen for connection changes
	window.addEventListener('online', goneOnline, false);
	window.addEventListener('offline', goneOffline, false);
	
	// Assign our custom submit handler to the form we deal with.
	$('#reportForm').submit(function(e) {return submitForm($(this));});
}

window.addEventListener('load', loaded, true);