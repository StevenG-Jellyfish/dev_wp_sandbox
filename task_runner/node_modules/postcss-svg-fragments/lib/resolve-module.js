// tooling
const resolve = require('resolve');

// resolve, then-ified
module.exports = (id, opts = {}) => new Promise(
	(resolvePromise, rejectPromise) => resolve(
		id,
		opts,
		(error, result) => error ? rejectPromise(error) : resolvePromise(result)
	)
);
