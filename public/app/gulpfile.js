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
        }))
        .pipe(gulp.dest('../css'))
        .pipe(rev())
        .pipe(rev.manifest())
        .pipe(gulp.dest('../css/rev'));
});

gulp.task('app', function() {
    return gulp.src(['./**'])
        .pipe(gulp.dest('../build'));//todo: scripts
});

gulp.task('less', function() {
    return gulp.src(['less/*.less'])
        .pipe(less())
        .pipe(gulp.dest('../css'));
});

gulp.task('watch', function() {
    gulp.watch('app/**', ['css', 'app']); 
});

gulp.task('rev', function() {
    return gulp.src(['../css/rev/**/*.json', '../*.html']);
});

gulp.task('rjs', shell.task([
	'node r.js -o build.js'
]));

gulp.task('release', ['rjs']);

gulp.task('default', function() {
	//TODO
});
