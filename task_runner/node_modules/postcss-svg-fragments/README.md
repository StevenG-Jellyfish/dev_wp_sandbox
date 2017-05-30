# SVG Fragments [<img src="https://postcss.github.io/postcss/logo.svg" alt="PostCSS Logo" width="90" height="90" align="right">][SVG Fragments]

[![NPM Version][npm-img]][npm-url]
[![Build Status][cli-img]][cli-url]
[![Licensing][lic-image]][lic-url]
[![Changelog][log-image]][log-url]
[![Gitter Chat][git-image]][git-url]

[SVG Fragments] lets you use SVG Fragments in CSS.

```css
/* before */

.icon {
    background-image: url(sprite.svg#pencil);
    fill: red;
    stroke: black;
}

/* after */

.icon {
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg fill='red' stroke='black' viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E %3Cpath d='M432 0c44.182 0 80 35.817 80 80 0 18.01-5.955 34.629-16 48l-32 32-112-112 32-32c13.371-10.045 29.989-16 48-16zm-400 368l-32 144 144-32 296-296-112-112-296 296zm325.789-186.211l-224 224-27.578-27.578 224-224 27.578 27.578z'/%3E %3C/svg%3E");
    fill: red;
    stroke: black;
}
```

When **sprite.svg** is:

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    <symbol id="camera" viewBox="0 0 512 512">
        <path d="M152 304c0 57.438 46.562 104 104 104s104-46.562 104-104-46.562-104-104-104-104 46.562-104 104zm328-176h-112c-8-32-16-64-48-64h-128c-32 0-40 32-48 64h-112c-17.6 0-32 14.4-32 32v288c0 17.6 14.4 32 32 32h448c17.6 0 32-14.4 32-32v-288c0-17.6-14.4-32-32-32zm-224 318c-78.425 0-142-63.574-142-142 0-78.425 63.575-142 142-142 78.426 0 142 63.575 142 142 0 78.426-63.573 142-142 142zm224-222h-64v-32h64v32z"/>
    </symbol>
    <symbol id="pencil">
        <path d="M432 0c44.182 0 80 35.817 80 80 0 18.01-5.955 34.629-16 48l-32 32-112-112 32-32c13.371-10.045 29.989-16 48-16zm-400 368l-32 144 144-32 296-296-112-112-296 296zm325.789-186.211l-224 224-27.578-27.578 224-224 27.578 27.578z"/>
    </symbol>
</svg>
```

## Options

#### `utf8`

Type: `Boolean`  
Default: `true`

Allows you to define whether UTF-8 or base64 encoding will be used.

```css
/* before { utf8: false } */

.icon {
	background-image: url(sprite.svg#pencil);
	fill: red;
	stroke: black;
}

/* after */

.icon {
	background-image: url("data:image/svg+xml;base64,PHN2ZyBmaWxsPSJyZWQiIHN0cm9rZT0iYmxhY2siIHZpZXdCb3g9IjAgMCA1MTIgNTEyIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxwYXRoIGQ9Ik00MzIgMGM0NC4xODIgMCA4MCAzNS44MTcgODAgODAgMCAxOC4wMS01Ljk1NSAzNC42MjktMTYgNDhsLTMyIDMyLTExMi0xMTIgMzItMzJjMTMuMzcxLTEwLjA0NSAyOS45ODktMTYgNDgtMTZ6bS00MDAgMzY4bC0zMiAxNDQgMTQ0LTMyIDI5Ni0yOTYtMTEyLTExMi0yOTYgMjk2em0zMjUuNzg5LTE4Ni4yMTFsLTIyNCAyMjQtMjcuNTc4LTI3LjU3OCAyMjQtMjI0IDI3LjU3OCAyNy41Nzh6Ii8+Cjwvc3ZnPg==");
	fill: red;
	stroke: black;
}
```

## Usage

Add [SVG Fragments] to your build tool:

```bash
npm install postcss-svg-fragments --save-dev
```

#### Node

Use [SVG Fragments] as a processor:

```js
require('postcss-svg-fragments').process(YOUR_CSS, { /* options */ });
```

#### PostCSS

Add [PostCSS] to your build tool:

```bash
npm install postcss --save-dev
```

Use [SVG Fragments] as a plugin:

```js
postcss([
	require('postcss-svg-fragments')({ /* options */ })
]).process(YOUR_CSS, /* options */);
```

#### Gulp

Add [Gulp PostCSS] to your build tool:

```bash
npm install gulp-postcss --save-dev
```

Use [SVG Fragments] in your Gulpfile:

```js
var postcss = require('gulp-postcss');

gulp.task('css', function () {
	return gulp.src('./src/*.css').pipe(
		postcss([
			require('postcss-svg-fragments')({ /* options */ })
		])
	).pipe(
		gulp.dest('.')
	);
});
```

#### Grunt

Add [Grunt PostCSS] to your build tool:

```bash
npm install grunt-postcss --save-dev
```

Use [SVG Fragments] in your Gruntfile:

```js
grunt.loadNpmTasks('grunt-postcss');

grunt.initConfig({
	postcss: {
		options: {
			use: [
				require('postcss-svg-fragments')({ /* options */ })
			]
		},
		dist: {
			src: '*.css'
		}
	}
});
```

## Additional Usage

You may also use SVG Fragments found in [modules]. Just specify the SVG using the `media` field in `package.json`, and then use `url("my-module#my-fragment")` to reference the SVG Fragment in CSS. You may also use files from modules, like `url("my-module/my-sprite.svg#my-fragment")`.

[npm-url]: https://www.npmjs.com/package/postcss-svg-fragments
[npm-img]: https://img.shields.io/npm/v/postcss-svg-fragments.svg
[cli-url]: https://travis-ci.org/jonathantneal/postcss-svg-fragments
[cli-img]: https://img.shields.io/travis/jonathantneal/postcss-svg-fragments.svg
[lic-url]: LICENSE.md
[lic-image]: https://img.shields.io/npm/l/postcss-svg-fragments.svg
[log-url]: CHANGELOG.md
[log-image]: https://img.shields.io/badge/changelog-md-blue.svg
[git-url]: https://gitter.im/postcss/postcss
[git-image]: https://img.shields.io/badge/chat-gitter-blue.svg

[SVG Fragments]: https://github.com/jonathantneal/postcss-svg-fragments
[PostCSS]: https://github.com/postcss/postcss
[Gulp PostCSS]: https://github.com/postcss/gulp-postcss
[Grunt PostCSS]: https://github.com/nDmitry/grunt-postcss
[modules]: https://docs.npmjs.com/files/package.json
