// include gulp
var gulp = require('gulp'); 

// include plugins
var jshint = require('gulp-jshint'),
    minify = require('gulp-minify-css'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    autoprefixer = require('gulp-autoprefixer');

// default task 
gulp.task('default', ['sass', 'lint', 'scripts']);

// compile sass
gulp.task('sass', function() {
  return gulp.src('assets/styles/development/scss/*.scss')
    .pipe(sass())
    .pipe(autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(gulp.dest('assets/styles/development/css'))
    .pipe(minify())
    .pipe(rename(function (path) {
      path.extname = '.min.css'
    }))
    .pipe(gulp.dest('assets/styles/production'))
});

// lint js
gulp.task('lint', function() {
  return gulp.src('assets/scripts/development/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('default'))
});

// concatenate & minify js
gulp.task('scripts', function() {
  return gulp.src('assets/scripts/development/*.js')
    .pipe(rename(function (path) {
        path.extname = '.min.js'
    }))
    .pipe(uglify())
    .pipe(gulp.dest('assets/scripts/production'))
});

// watch for changes
gulp.task('watch', ['default'], function() {
  var watchFiles = [
    'assets/styles/development/scss/*.scss',
    'assets/styles/development/scss/_partials/*.scss',
    'assets/scripts/development/*.js'
  ];

  gulp.watch(watchFiles, ['default']);
});