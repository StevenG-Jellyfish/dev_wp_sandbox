// ========================================
// Gulpfile
// ========================================

// load plugins
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    notify = require('gulp-notify'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    svgfragments = require('postcss-svg-fragments'),
    cssnano = require('cssnano'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    runSequence = require('run-sequence'),
    del = require('del'),
    theme_path  = '../public_html/wp-content/themes/jupiter-child/'

var paths = {
    styles:  ['scss/**/*.scss'],
    scripts: [ 'js/*.js']
}

/**
 * Error notification settings for plumber
 */
var plumberErrorHandler = {
    errorHandler: notify.onError({
        message: "Error: <%= error.message %>"
    })
};


/**
 * Run the main SASS task, this will create a temp CSS file and Source maps,
 * as well as running vendor prefixes.
 */
gulp.task('sass', function() {

    var plugins = [
        autoprefixer({
            browsers: ['last 2 versions', 'ie 9', 'ie 10'],
            cascade: false
        })
    ];

    return gulp.src(paths.styles)
        .pipe(sourcemaps.init())
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(postcss( plugins ))
        .pipe(concat('style.css'))
        //.pipe(sourcemaps.write(theme_path + '/css'))
        //.pipe(gulp.dest('.tmp'))
        .pipe(gulp.dest(theme_path))

});

/**
 * Compile SVG Spritesheet css
 */
gulp.task('svgcompile', function(){

    var plugins = [
        svgfragments({})
    ];

    return gulp.src('./.tmp/app.css')
        .pipe(postcss(plugins))
        .pipe(gulp.dest('.tmp'))

})

/**
 * Run the Minify task which has a dependency of the SASS task
 * (SASS will be run first). Minify the temp CSS file and save to the
 * dist directory.
 */
gulp.task('minifycss', function() {

    var plugins = [
        cssnano({})
    ];

    gulp.src('.tmp/app.css')
        .pipe(rename({suffix: '.min'}))
        .pipe(postcss(plugins))
        .pipe(gulp.dest(theme_path + '/css'));
});


/**
 * Concatenate scripts into a single file and minify the file.
 */
gulp.task('minify-scripts', function() {

    gulp.src(paths.scripts)
        .pipe(plumber({errorHandler: notify.onError("Error: <%= error %> <%= error.message %>")}))
        .pipe(sourcemaps.init())
        .pipe(concat('custom.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(theme_path));
});


/**
 * Clean up the CSS files
 */
gulp.task('clean-css', function(cb) {
    del([
        './.tmp/*.css',
        theme_path + 'css/*.css',
        theme_path + 'css/*.map'
    ], cb)
});

/**
 * Clean up the JS files
 */
gulp.task('clean-js', function(cb) {
    del([
        theme_path + 'js/*.js',
        theme_path + 'js/*.map'
    ], cb)
});


/**
 * Set the task run order for style tasks
 */
gulp.task('styles', function(callback) {
    runSequence(
        'clean-css',
        'sass',
        'svgcompile',
        'minifycss'
    ,callback);
});

/**
 * Set the task run order for script tasks
 */
gulp.task('scripts', function(callback) {
    runSequence(
        'clean-js',
        'minify-scripts',
        callback
    );
});


/**
 * Create the watch listener
 */
gulp.task('watch', function() {
    gulp.watch(paths.styles, ['styles']);
    gulp.watch(paths.scripts, ['scripts']);
});



/**
 * Set the default task to furst run styles, then scripts and then watch for
 * changes to the JS or SASS files.
 */
gulp.task('default', function (callback) {
    runSequence(
        'styles',
        'scripts',
        'watch',
        callback
    );
});