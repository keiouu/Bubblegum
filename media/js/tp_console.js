function debug_hideTabs() {
	document.getElementById('console-tab').style.display='none';
	document.getElementById('profile-tab').style.display='none';
	document.getElementById('db-tab').style.display='none';
}

function debug_toggleTab(name) {
	var elem = document.getElementById(name + '-tab');
	var show = !(elem.style.display == 'block');
	debug_hideTabs();
	if (show)
		elem.style.display='block';
}