// get element by id
module.exports = (node, id) => {
	if (node.attr && node.attr.id === id) {
		return node;
	} else if (node.children) {
		let index = -1;
		let child;

		while (child = node.children[++index]) {
			child = module.exports(child, id);

			if (child) {
				return child;
			}
		}
	}

	return undefined;
};
