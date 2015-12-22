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
    return gulp.src('../res', {read: false, force: true}).pipe(clean());
});

gulp.task('css', function() {
    return gulp.src(['less/*.less'])
        .pipe(less()).pipe(rename(function(path) { 
            //path.basename += '.min';
            path.extname   = '.css';
        }))
        .pipe(gulp.dest('../css'))
        .pipe(rev())
        .pipe(rev.manifest())
        .pipe(gulp.dest('../css/rev'));
});

gulp.task('app', function() {
    return gulp.src(['./**'])
        .pipe(gulp.dest('../res'));
});

gulp.task('less', function() {
    return gulp.src(['less/*.less'])
        .pipe(less())
        .pipe(gulp.dest('../css'));
});

gulp.task('watch', function() {
    //gulp.watch(['./app/**'], ['app']);
    //gulp.watch(['./less/**'], ['css']);
    gulp.watch(['./**/*.js','./**/*.html', '!node_modules'] , function(event, type) {
        console.log('File ' + event.path + ' was ' + event.type);
        var src = event.path;
        var arr = src.replace('/src/', '/res/').split('/');
        arr.pop();
        var dest = gulp.dest(arr.join('/'))
        console.log(dest);

        return gulp.src(event.path).pipe(dest);
    });
});

gulp.task('rev', function() {
    return gulp.src(['../css/rev/**/*.json', '../index.html']);
});

gulp.task('rjs', shell.task([
	'node r.js -o build.js'
]));

gulp.task('cp', function() {
    gulp.src(['../css/**']).pipe(gulp.dest('./dist/css'));
    gulp.src(['../res/**']).pipe(gulp.dest('./dist/res'));
});

gulp.task('release', ['rjs']);

gulp.task('default', function() {
	//TODO
});
