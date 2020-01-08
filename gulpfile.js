const path   = require('path');
const gulp   = require('gulp');
const concat = require('gulp-concat');

const appResourceDir = path.join(__dirname, 'src/resources/public');
const nodeModulesDir = path.join(__dirname, 'node_modules');
const vendorDir = path.join(__dirname, 'vendor');
const appWebDir = path.join(__dirname, 'public');

const appTask = function() {

    // Control the order
    gulp.src([
        appResourceDir + '/css/style.css',
        appResourceDir + '/css/refsched.css',
    ], { allowEmpty: true }
    )
        .pipe(concat("app.css"))
        .pipe(gulp.dest('public/css'));

    //Java scripts
    gulp.src(
        appResourceDir + '/js/app.js',
        { allowEmpty: true }
    )
        .pipe(concat("ext.js"))
        .pipe(gulp.dest(appWebDir + '/js'));

    // images
    return gulp.src([
        appResourceDir + '/images/*.png',
        appResourceDir + '/images/*.ico'
        ], { allowEmpty: true }
    )
        .pipe(gulp.dest(appWebDir + '/images'));

};

const nodeModulesTask = function() {

    gulp.src([
        path.join(nodeModulesDir, 'normalize.css/normalize.css'),
        path.join(nodeModulesDir, 'bootstrap/dist/css/bootstrap.min.css'),
        path.join(nodeModulesDir, 'purecss/build/base-min.css'),
        path.join(nodeModulesDir, 'purecss/build/grids-responsive-min.css'),
        path.join(nodeModulesDir, 'purecss/build/buttons-min.css'),
        path.join(nodeModulesDir, 'purecss/build/pure-nr-min.css'),
        path.join(nodeModulesDir, 'jquery-datetimepicker/build/jquery.datetimepicker.min.css')
        ],
        { allowEmpty: true }
    )
        .pipe(gulp.dest(appWebDir + '/css'));
    //
    gulp.src([
        path.join(nodeModulesDir, 'jquery/dist/jquery.min.js'),
        path.join(nodeModulesDir, 'bootstrap/dist/js/bootstrap.min.js'),
        path.join(nodeModulesDir, 'jquery-datetimepicker/build/jquery.datetimepicker.full.js')
        ],
        { allowEmpty: true }
    )
        .pipe(gulp.dest(appWebDir + '/js'));

    return gulp.src([
        path.join(vendorDir, 'components/bootstrap-default/fonts/glyphicons-halflings-regular.ttf'),
        path.join(vendorDir, 'components/bootstrap-default/fonts/glyphicons-halflings-regular.woff'),
        path.join(vendorDir, 'components/bootstrap-default/fonts/glyphicons-halflings-regular.woff2')
        ],
        { allowEmpty: true }
    )
        .pipe(gulp.dest(appWebDir + '/fonts'));


};

const buildTask = function() {
    appTask();
    return nodeModulesTask();

};

const watchTask = function() {
    buildTask();

    // Why the warnings, seems to work fine
    return gulp.watch([
        appResourceDir + '/css/*.css',
        appResourceDir + '/js/*.js',
        appResourceDir + '/images/*.png',
        appResourceDir + '/images/*.ico'
    ], gulp.series['app']);

};

gulp.task('watch', watchTask);
gulp.task('build', buildTask);

// The default task (called when you run `gulp` from cli)
gulp.task('default', gulp.series('build'));