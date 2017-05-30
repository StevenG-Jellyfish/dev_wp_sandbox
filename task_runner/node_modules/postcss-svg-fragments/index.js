// tooling
const parser  = require('postcss-value-parser');
const path    = require('path');
const postcss = require('postcss');
const xmldoc  = require('xmldoc');

// local tooling
const cloneNode      = require('./lib/clone-node');
const getElementById = require('./lib/get-element-by-id');
const nodeToURI      = require('./lib/node-to-uri');
const readFile       = require('./lib/read-file');
const resolveModule  = require('./lib/resolve-module');

// value matcher
const valueMatch = /(^|\s)url\(.+#.+\)(\s|$)/;

// property matcher
const propertyMatch = /^(color|fill|height|stroke|stroke-width|width)$/;

// plugin
module.exports = postcss.plugin('postcss-svg-fragments', ({
	utf8 = true
} = {}) => (css, result) => {
	// create a css promises array
	const cssPromises = [];

	// create an svg promises array
	const svgPromises = [];

	// create array with modified declarations
	const modifiedDecls = [];

	// walk each declaration
	css.walkDecls((decl) => {
		// if the declaration has a url with a fragment
		if (valueMatch.test(decl.value)) {
			modifiedDecls.push(decl);

			// cache the declaration’s siblings
			const parent = decl.parent;

			// walk each node of the declaration
			decl.value = parser(decl.value).walk((node) => {
				// if the node is a url containing an svg fragment
				if (
					node.type === 'function' &&
					node.value === 'url' &&
					node.nodes.length !== 0 &&
					(/^(?!data:).*#/).test(node.nodes[0].value)
				) {
					// current file path of the declaration or root
					const cwf = (decl.source || css.source || {
						input: {}
					}).input.file;

					// current file or current working directory
					const dir = cwf ? path.dirname(cwf) : process.cwd();

					// parse the svg url
					const url   = node.nodes[0];
					const parts = url.value.split('#');
					const base  = parts.shift();
					const file  = path.join(dir, base);
					const id    = parts.join('#');

					// get cached svg promise
					const svgPromise = svgPromises[file] = svgPromises[file] || readFile(file).catch(() => resolveModule(base, {
						basedir: path.resolve(dir),
						packageFilter: (pkg) => {
							// if media is found
							if (pkg.media) {
								// re-assign media to main
								pkg.main = pkg.media;
							}

							return pkg;
						}
					}).then(readFile)).then((content) => {
						// return an xml tree of the svg
						const document = new xmldoc.XmlDocument(content);

						document.ids = {};

						return document;
					});

					// push a modified svg promise to the declaration promises array
					cssPromises.push(svgPromise.then((document) => {
						if (id) {
							// cache fragment by id
							document.ids[id] = document.ids[id] || getElementById(document, id);

							// if the fragment id exists
							if (document.ids[id]) {
								// get cloned fragment
								const clonedFragment = cloneNode(document.ids[id]);

								// walk each sibling declaration
								parent.nodes.forEach((sibling) => {
									// if the sibling is a matching declaration
									if (sibling.type === 'decl' && propertyMatch.test(sibling.prop)) {
										// update the corresponding attribute on the clone
										clonedFragment.attr[sibling.prop] = sibling.value;
									}
								});

								// update the url node
								url.value = nodeToURI(clonedFragment, document, utf8);

								// add quote to base64 urls to improve compatibility
								if (utf8) {
									url.quote = '"';
									url.type = 'string';
								}
							}
						} else {
							// get cloned fragment
							const clonedDocument = cloneNode(document);

							// walk each sibling declaration
							parent.nodes.forEach((sibling) => {
								// if the sibling is a matching declaration
								if (sibling.type === 'decl' && propertyMatch.test(sibling.prop)) {
									// update the corresponding attribute on the clone
									clonedDocument.attr[sibling.prop] = sibling.value;
								}
							});

							// update the url node
							url.value = nodeToURI(clonedDocument, document, utf8);

							// add quote to base64 urls to improve compatibility
							if (utf8) {
								url.quote = '"';
								url.type = 'string';
							}
						}
					}).catch((error) => {
						result.warn(error, node);
					}));
				}
			});
		}
	});

	// return chained css promises array
	return Promise.all(cssPromises).then(() => {
		modifiedDecls.forEach((decl) => {
			// update the declaration value
			decl.value = decl.value.toString();
		});
	});
});

// override plugin#process
module.exports.process = function (cssString, pluginOptions, processOptions) {
	return postcss([
		0 in arguments ? module.exports(pluginOptions) : module.exports()
	]).process(cssString, processOptions);
};
