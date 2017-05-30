// tooling
const fs = require('fs');

// read file contents
module.exports = (file) => new Promise(
	(resolvePromise, rejectPromise) => fs.readFile(
		file,
		'utf8',
		(error, data) => error ? rejectPromise(error) : resolvePromise(data)
	)
);
