var gulp    = require('gulp'),
	shell   = require('gulp-shell'),
    jshint  = require('gulp-jshint'),
    uglify  = require('gulp-uglify'),
    clean   = require('gulp-clean'),
    rename  = require('gulp-rename'),
    rev     = require('gulp-rev'),
    revCollector = require('gulp-rev-collector'),
    concat  = require("gulp-concat"),
    less    = require("gulp-less"),
    _       = require("underscore");

gulp.task('css', function() {
   gulp.src(['less/*.less', 'app/views/**/*.less'])
        .pipe(less())
        .pipe(concat('main.css'))
        .pipe(gulp.dest('../../css'));
});

gulp.task('page-dev', function() {
    var fs  = require("fs");
    var html= fs.readFileSync('./index.tpl');
    var tpl = _.template(html.toString());

    fs.writeFile('../index.html', tpl({env:'dev'}));
});

gulp.task('page', function() {
    var fs  = require("fs");
    var html= fs.readFileSync('./index.tpl');
    var tpl = _.template(html.toString());

    fs.writeFile('../index.html', tpl({env:'production'}));
});


gulp.task('rjs', shell.task([
	'node r.js -o build.js',
    'node r.js -o cssIn=../../css/main.css out=../../css/main.min.css optimizeCss=standard'
]));

// 监听会变动的文件
gulp.task('listen', function() {
    gulp.watch(['./less/*.less', './app/**/*.less'], ['css']);
});

gulp.task('build', ['css', 'page-dev']);
gulp.task('release', ['css', 'rjs', 'page']);
gulp.task('watch', ['build', 'listen']);
