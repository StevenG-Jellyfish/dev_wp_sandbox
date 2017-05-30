// clone a node
module.exports = (node) => {
	const clone = {};

	for (let key in node) {
		if (node[key] instanceof Array) {
			clone[key] = node[key].map(module.exports);
		} else if (typeof node[key] === 'object') {
			clone[key] = module.exports(node[key]);
		} else {
			clone[key] = node[key];
		}
	}

	return clone;
};
