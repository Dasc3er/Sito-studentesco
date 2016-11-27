// Librerie NPM richieste per l'esecuzione
var gulp = require('gulp');
var mainBowerFiles = require('main-bower-files');
var bowerMain = require('bower-main');
var del = require('del');
var uglify = require('gulp-uglify');
var uglifyCSS = require('gulp-uglifycss');
var flatten = require('gulp-flatten');
var rename = require('gulp-rename');
var jsonMinify = require('gulp-json-minify');
var concat = require('gulp-concat');
var concatCss = require('gulp-concat-css');

/**
 * Detect .bowerrc file and read bower directory's path.
 *
 * @returns {string} bower directory path
 */
function getBowerDirectory() {
    var cwd = require('cwd');
    var path = require('path');
    var fs = require('fs');

    var bowerrc = path.join(cwd(), '.bowerrc');

    if (fs.existsSync(bowerrc)) {
        try {
            bower_config = JSON.parse(fs.readFileSync(bowerrc));
            directory = bower_config.directory;
        } catch (err) {

        }
    }

    if (typeof directory === "undefined") {
        directory = './bower_components';
    }

    return directory;
}

// Cartelle di destinazione
var directoryAssets = 'public/assets';
var directoryJS = directoryAssets + '/js';
var directoryCSS = directoryAssets + '/css';
var directoryImages = directoryAssets + '/img';
var directoryFonts = directoryAssets + '/fonts';

// Cartelle di origine
var bowerDirectory = getBowerDirectory();
var directorySrc = 'resources/assets';
var directorySrcJS = directorySrc + '/js';
var directorySrcCSS = directorySrc + '/css';
var directorySrcImages = directorySrc + '/img';
var directorySrcFonts = directorySrc + '/fonts';

// Elaborazione e minificazione di JS
gulp.task('JS', ['clean'], function () {
    var JS = bowerMain('js', 'min.js');
    gulp.src(JS.minified)
        .pipe(gulp.dest(directoryJS));

    gulp.src(JS.minifiedNotFound)
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(directoryJS));

    gulp.start('srcJS');
});

// Elaborazione e minificazione di JS personalizzati
gulp.task('srcJS', function () {
    gulp.src([directorySrcJS + '/*.js'])
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(directoryJS));
});

// Elaborazione e minificazione di CSS
gulp.task('CSS', ['clean'], function () {
    var CSS = bowerMain('css', 'min.css');
    gulp.src(CSS.minified)
        .pipe(gulp.dest(directoryCSS));

    gulp.src(CSS.minifiedNotFound)
        .pipe(uglifyCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(directoryCSS));

    gulp.start('srcCSS');
});

// Elaborazione e minificazione di CSS personalizzati
gulp.task('srcCSS', function () {
    gulp.src([directorySrcCSS + '/*.css'])
        .pipe(uglifyCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(directoryCSS));
});

// Elaborazione delle immagini
gulp.task('images', ['clean'], function () {
    gulp.src(mainBowerFiles('**/*.{jpg,png,jpeg,gif}'))
        .pipe(flatten())
        .pipe(gulp.dest(directoryImages));

    gulp.start('srcImages');
});

// Elaborazione delle immagini personalizzate
gulp.task('srcImages', function () {
    gulp.src([directorySrcImages + '/**/*.{jpg,png,jpeg,gif}'])
        .pipe(flatten())
        .pipe(gulp.dest(directoryImages));
});

// Elaborazione dei fonts
gulp.task('fonts', ['clean'], function () {
    gulp.src(mainBowerFiles('**/*.{otf,eot,svg,ttf,woff,woff2}'))
        .pipe(flatten())
        .pipe(gulp.dest(directoryFonts));

    gulp.start('srcFonts');
});

// Elaborazione dei fonts personalizzati
gulp.task('srcFonts', function () {
    gulp.src([directorySrcFonts + '/**/*.{otf,eot,svg,ttf,woff,woff2}'])
        .pipe(flatten())
        .pipe(gulp.dest(directoryFonts));
});

gulp.task('tinymce', ['clean'], function () {
    gulp.src([bowerDirectory + '/tinymce/plugins/**/*'])
        .pipe(gulp.dest(directoryJS + '/plugins'));

    gulp.src([bowerDirectory + '/tinymce/themes/**/*'])
        .pipe(gulp.dest(directoryJS + '/themes'));

    gulp.src([bowerDirectory + '/tinymce/skins/**/*'])
        .pipe(gulp.dest(directoryJS + '/skins'));

    gulp.src([bowerDirectory + '/tinymce/tinymce.min.js'])
        .pipe(gulp.dest(directoryJS));
});

gulp.task('php-debugbar', ['clean'], function () {
    gulp.src([bowerDirectory + '/php-debugbar/src/DebugBar/Resources/**/*'])
        .pipe(gulp.dest(directoryAssets + '/php-debugbar'));
});

// Elaborazione e minificazione delle informazioni sull'internazionalizzazione
gulp.task('i18n', ['clean'], function () {
    gulp.src([bowerDirectory + '/**/i18n/*.js', bowerDirectory + '/**/lang/*.js', directorySrcJS + '/i18n/**/*.js', '!' + bowerDirectory + '/**/src/**', '!' + bowerDirectory + '/tinymce/**', '!src/js/i18n/datatables/**'])
        .pipe(rename(function (path) {
            if (path.basename.indexOf('.min') == -1) path.basename += '.min';
        }))
        .pipe(uglify())
        .pipe(flatten({
            includeParents: 1
        }))
        .pipe(gulp.dest(directoryJS + '/i18n'));
});

// Pulizia
gulp.task('clean', function () {
    return del([directoryAssets]);
});

gulp.task('bower', ['clean'], function () {
    gulp.start('JS');
    gulp.start('CSS');
    gulp.start('images');
    gulp.start('fonts');
    gulp.start('other');
});

gulp.task('other', ['clean'], function () {
    gulp.start('tinymce');
    gulp.start('php-debugbar');
    gulp.start('i18n');
});

gulp.task('src', function () {
    gulp.start('srcJS');
    gulp.start('srcCSS');
    gulp.start('srcFonts');
    gulp.start('srcImages');
});

gulp.task('watch', function () {
    gulp.watch('src/**/*', ['src']);
});

gulp.task('src&watch', ['src', 'watch']);

gulp.task('default', ['clean', 'bower']);
