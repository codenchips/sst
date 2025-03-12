'use strict'


const { src, dest, watch } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sync = require("browser-sync").create();
const minify = require('gulp-minify');

function compileSass(done) {
    src('scss/app.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(dest('css'))
    done();
}

function minifyJS(done) {
    src('js/scripts.js')
    .pipe(  minify().on('error', sass.logError))
    .pipe(dest('js'))
    done();
}


function watchStuff() {
    watch('scss/app.scss', compileSass);
    //watch('app/js/scripts.js', minifyJS);
}

function browserSync(cb) {
    sync.init({
        injectChanges: true,
        server: {
            baseDir: "/"
        }
    });

    watchStuff();
}

exports.sync = browserSync;
exports.compileSass = compileSass
exports.minify = minifyJS;
exports.watchStuff = watchStuff

