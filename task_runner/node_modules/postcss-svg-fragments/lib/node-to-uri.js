// tooling
const encodeUTF8 = require('./encode-utf-8');

// node to uri
module.exports = (fragment, document, utf8) => {
	// rebuild fragment as <svg>
	fragment.name = 'svg';

	delete fragment.attr.id;

	fragment.attr.viewBox = fragment.attr.viewBox || document.attr.viewBox;

	fragment.attr.xmlns = 'http://www.w3.org/2000/svg';

	const xml = fragment.toString({
		compressed: true
	});

	// return data URI
	return `data:image/svg+xml;${ utf8 ? `charset=utf-8,${ encodeUTF8(xml) }` : `base64,${ new Buffer(xml).toString('base64') }` }`;
};
