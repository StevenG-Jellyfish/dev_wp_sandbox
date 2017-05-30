// encode as utf-8
module.exports = (string) => encodeURIComponent(
	string.replace(
		// collapse whitespace
		/[\n\r\s\t]+/g, ' '
	).replace(
		// remove comments
		/<\!--([\W\w]*(?=-->))-->/g, ''
	).replace(
		// pre-encode ampersands
		/&/g, '%26'
	)
).replace(
	// escape commas
	/'/g, '\\\''
).replace(
	// un-encode compatible characters
	/%20/g, ' '
).replace(
	/%22/g, '\''
).replace(
	/%2F/g, '/'
).replace(
	/%3A/g, ':'
).replace(
	/%3D/g, '='
).replace(
	// encode additional incompatible characters
	/\(/g, '%28'
).replace(
	/\)/g, '%29'
);
