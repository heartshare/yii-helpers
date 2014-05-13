'use strict';

exports.warnOn = ['!_db.inc','!main.php','!SiteController.php'];
exports.template = function(grunt, init, done) {
init.process({}, [


  // Prompt for these values
  init.prompt('name'),
  //init.prompt('description'),
  //init.prompt('version')
], function(err, props) {


	// All finished, do something with the properties
	// Files to copy (and process).
	var files = init.filesToCopy(props);
	// Actually copy (and process) files.
	init.copyAndProcess(files, props);
	// Finish
	done();
});

};