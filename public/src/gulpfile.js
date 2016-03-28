var gulp    = require('gulp'),
	shell   = require('gulp-shell'),
    jshint  = require('gulp-jshint'),
    uglify  = require('gulp-uglify'),
    clean   = require('gulp-clean'),
    rename  = require('gulp-rename'),
    rev     = require('gulp-rev'),
    revCollector = require('gulp-rev-collector'),
	less    = require("gulp-less");

// 监听日志文件
gulp.task('listen', function() {
    gulp.watch(['/data/storage/logs/lumen.log'], function(event) {
        var exec = require('child_process').exec; 
        var cmdStr = 'osascript -e \'tell app "System Events" to display dialog "Bugs!!!"\'';
        exec(cmdStr);

        console.log(event);
    });
});

// 监听会变动的文件
gulp.task('watch', function() {
    gulp.watch(['./lib/**/*','./app/**/*', 'main.js', './less/**/*.less'], function(event, type) {
        var src = event.path;
        var arr = src.replace('/src/', '/res/').split('/');
        arr.pop();
        var dest = arr.join('/');

	    if(src.indexOf('less', src.length - 'less'.length) !== -1) {
            console.log(event.path + '(less) -> ' + '../css');
            return gulp.src(event.path)
                .pipe(less())
                .pipe(gulp.dest('../css'));
        }
        console.log(event.path + '(res) -> ' + dest);
        return gulp.src(event.path).pipe(gulp.dest(dest));

    });
});

gulp.task('rev', function() {
    return gulp.src(['../css/rev/**/*.json', '../index.html']);
});

gulp.task('css', function() {
    return gulp.src(['less/*.less'])
        .pipe(less()).pipe(rename(function(path) { 
            //path.basename += '.min';
            path.extname   = '.css';
        }))
        .pipe(gulp.dest('../css'));
        //.pipe(rev())
        //.pipe(rev.manifest())
        //.pipe(gulp.dest('../css/rev'));
});

gulp.task('app', function() {
    return gulp.src(['./app/**', './lib/**', 'main.js'])
        .pipe(gulp.dest('../res'));
});

gulp.task('less', function() {
    return gulp.src(['less/*.less'])
        .pipe(less())
        .pipe(gulp.dest('../css'));
});

gulp.task('rjs', shell.task([
	'node r.js -o build.js',
    'node r.js -o cssIn=../css/import.css out=../css/main.css optimizeCss=standard'
]));

gulp.task('cp', function() {
    gulp.src(['../css/**']).pipe(gulp.dest('./dist/css'));
    gulp.src(['../res/**']).pipe(gulp.dest('./dist/res'));
});

