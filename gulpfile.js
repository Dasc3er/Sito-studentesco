// Librerie NPM richieste per l'esecuzione
var gulp = require('gulp');
var del = require('del');
var debug = require('gulp-debug');
var util = require('gulp-util');

var mainBowerFiles = require('main-bower-files');
var gulpIf = require('gulp-if');

// Minificatori
var minifyJS = require('gulp-uglify');
var minifyCSS = require('gulp-clean-css');
var minifyJSON = require('gulp-json-minify');

// Interpretatori CSS
var sass = require('gulp-sass');
var less = require('gulp-less');
var stylus = require('gulp-stylus');
var autoprefixer = require('gulp-autoprefixer');

// Concatenatore
var concat = require('gulp-concat');

// Altro
var flatten = require('gulp-flatten');
var rename = require('gulp-rename');

/**
 * Detect .bowerrc file and read bower directory's path.
 *
 * @returns {string} bower directory path
 */
function getBowerDirectory() {
    var cwd = require('cwd');
    var path = require('path');
    var fs = require('fs');

    try {
        bower_config = JSON.parse(fs.readFileSync(path.join(cwd(), '.bowerrc')));
        directory = bower_config.directory;
    } catch (err) {

    }

    if (typeof directory === "undefined") {
        directory = './bower_components';
    }

    return directory;
}

// Configurazione
var config = {
    production: 'assets', // Cartella di destinazione
    development: 'resources/assets', // Cartella dei file di personalizzazione
    bower: getBowerDirectory(),
    paths: {
        js: 'js',
        css: 'css',
        images: 'img',
        fonts: 'fonts'
    }
};

// Elaborazione e minificazione di JS
gulp.task('JS', ['clean'], function () {
    gulp.src(mainBowerFiles('**/*.js'))
        .pipe(minifyJS())
        .pipe(gulpIf('!*.min.*', rename({
            suffix: '.min'
        })))
        .pipe(gulp.dest(config.production + '/' + config.paths.js));

    gulp.start('srcJS');
});

// Elaborazione e minificazione di JS personalizzati
gulp.task('srcJS', function () {
    gulp.src([
            config.development + '/' + config.paths.js + '/*.js',
        ])
        .pipe(minifyJS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(config.production + '/' + config.paths.js));
});

// Elaborazione e minificazione di CSS
gulp.task('CSS', ['clean'], function () {
    gulp.src(mainBowerFiles('**/*.{css,scss,less,styl}'))
        .pipe(gulpIf('*.scss', sass(), gulpIf('*.less', less(), gulpIf('*.styl', stylus()))))
        .pipe(autoprefixer({
            browsers: 'last 2 version',
        }))
        .pipe(minifyCSS({
            rebase: false,
        }))
        .pipe(gulpIf('!*.min.*', rename({
            suffix: '.min'
        })))
        .pipe(gulp.dest(config.production + '/' + config.paths.css));

    gulp.start('srcCSS');
});

// Elaborazione e minificazione di CSS personalizzati
gulp.task('srcCSS', function () {
    gulp.src([
            config.development + '/' + config.paths.css + '/*.{css,scss,less,styl}',
        ])
        .pipe(gulpIf('*.scss', sass(), gulpIf('*.less', less(), gulpIf('*.styl', stylus()))))
        .pipe(autoprefixer({
            browsers: 'last 2 version',
        }))
        .pipe(minifyCSS({
            rebase: false,
        }))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(config.production + '/' + config.paths.css));
});

// Elaborazione delle immagini
gulp.task('images', ['clean'], function () {
    gulp.src(mainBowerFiles('**/*.{jpg,png,jpeg,gif}'))
        .pipe(flatten())
        .pipe(gulp.dest(config.production + '/' + config.paths.images));

    gulp.start('srcImages');
});

// Elaborazione delle immagini personalizzate
gulp.task('srcImages', function () {
    gulp.src([
            config.development + '/' + config.paths.images + '/**/*.{jpg,png,jpeg,gif}',
        ])
        .pipe(flatten())
        .pipe(gulp.dest(config.production + '/' + config.paths.images));
});

// Elaborazione dei fonts
gulp.task('fonts', ['clean'], function () {
    gulp.src(mainBowerFiles('**/*.{otf,eot,svg,ttf,woff,woff2}'))
        .pipe(flatten())
        .pipe(gulp.dest(config.production + '/' + config.paths.fonts));

    gulp.start('srcFonts');
});

// Elaborazione dei fonts personalizzati
gulp.task('srcFonts', function () {
    gulp.src([
            config.development + '/' + config.paths.fonts + '/**/*.{otf,eot,svg,ttf,woff,woff2}',
        ])
        .pipe(flatten())
        .pipe(gulp.dest(config.production + '/' + config.paths.fonts));
});

gulp.task('tinymce', ['clean'], function () {
    //gulp.src([config.bower + '/tinymce/plugins/**/*'])
    //    .pipe(gulp.dest(config.production + '/' + config.paths.js + '/plugins'));

    gulp.src([config.bower + '/tinymce/themes/**/*'])
        .pipe(gulp.dest(config.production + '/' + config.paths.js + '/themes'));

    gulp.src([config.bower + '/tinymce/skins/**/*'])
        .pipe(gulp.dest(config.production + '/' + config.paths.js + '/skins'));

    gulp.src([config.bower + '/tinymce/tinymce.min.js'])
        .pipe(gulp.dest(config.production + '/' + config.paths.js));
});

gulp.task('php-debugbar', ['clean'], function () {
    gulp.src([
            './vendor/maximebf/debugbar/src/DebugBar/Resources/**/*',
        ])
        .pipe(gulpIf('*.css', minifyCSS(), gulpIf('*.js', minifyJS())))
        .pipe(gulp.dest(config.production + '/php-debugbar'));
});

// Elaborazione e minificazione delle informazioni sull'internazionalizzazione
gulp.task('i18n', ['clean'], function () {
    gulp.src([
            config.bower + '/**/{i18n,lang}/*.{js,json}',
            config.development + '/' + config.paths.js + '/i18n/**/*.{js,json}',
            '!' + config.bower + '/**/{src,plugins}/**',
            '!' + config.bower + '/tinymce/**',
        ])
        .pipe(gulpIf('*.js', minifyJS(), gulpIf('*.json', minifyJSON())))
        .pipe(gulpIf('!*.min.*', rename({
            suffix: '.min'
        })))
        .pipe(flatten({
            includeParents: 1
        }))
        .pipe(gulp.dest(config.production + '/' + config.paths.js + '/i18n'));
});

// Pulizia
gulp.task('clean', function () {
    return del([config.production]);
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
    gulp.watch(config.development + '/**/*', ['src']);
});

gulp.task('src&watch', ['src', 'watch']);

gulp.task('default', ['clean', 'bower']);
