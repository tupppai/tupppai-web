var gulp    = require('gulp'),
	shell   = require('gulp-shell'),
    jshint  = require('gulp-jshint'),
    uglify  = require('gulp-uglify'),
    clean   = require('gulp-clean'),
    rename  = require('gulp-rename'),
    rev     = require('gulp-rev'),
    revCollector = require('gulp-rev-collector'),
	less    = require("gulp-less");

gulp.task('clean', function() {
    return gulp.src('scripts-build', {read: false}).pip(clean());
});

gulp.task('css', function() {
    return gulp.src(['less/*.less'])
        .pipe(less()).pipe(rename(function(path) { 
            path.basename += '.min';
            path.extname   = '.css';
        })
        .pipe(gulp.dest('../css'));
        //.pipe(rev())
        //.pipe(rev.manifest())
        //.pipe(gulp.desc('../css/rev'));
});

gulp.task('js', function() {
    return gulp.src(['app/**/*.js'])
    /*
        .pipe(rename(function(path) {
            path.basename += '.min';
            path.extname   = '.css';
        })
    */
        .pipe(gulp.dest('../build'));//todo: scripts
});

gulp.task('html', function() {
    return gulp.src(['app/**/*.html']).pipe(gulp.dest('../build'));//todo: scripts
});

gulp.task('less', function() {
    return gulp.src(['main/less/*.less'])
        .pipe(less())
        .pipe(gulp.dest('../css'));
});

gulp.task('rev', function() {
    return gulp.src(['../css/rev/**/*.json', '../*.html']);
});

gulp.task('rjs', shell.task([
	'node r.js -o scripts/build.js'
]));

gulp.task('watch', function() {
    gulp.watch('app/**', ['less', 'js', 'html']); 
});

gulp.task('release', ['less', 'rjs']);

gulp.task('default', function() {
	//TODO
});
