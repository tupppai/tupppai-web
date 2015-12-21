var gulp = require('gulp')
	shell = require('gulp-shell')
	less = require("gulp-less");

gulp.task('less', function() {
    gulp.src(['main/less/*.less'])
        .pipe(less())
        .pipe(gulp.dest('main/css'));
});

gulp.task('rjs', shell.task([
	'node r.js -o scripts/build.js'
]));

gulp.task('watch', function() {
    gulp.watch('main/less/*.less', ['less']); 
});

gulp.task('release', ['less', 'rjs']);

gulp.task('default', function() {
	//TODO
});
