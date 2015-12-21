var gulp = require("gulp")
    less = require("gulp-less");

gulp.task('less2css', function() {
    gulp.src(['less/*.less'])
        .pipe(less())
        .pipe(gulp.dest('css'));
});

gulp.task('watch', function() {
    gulp.watch('less/*.less', ['less2css']); 
});


