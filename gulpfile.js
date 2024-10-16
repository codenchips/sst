'use strict'


const { src, dest, watch } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sync = require("browser-sync").create();
const minify = require('gulp-minify');

function compileSass(done) {
    src('app/scss/app.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(dest('app/css'))
    done();
}

function minifyJS(done) {
    src('app/js/scripts.js')
    .pipe(  minify().on('error', sass.logError))
    .pipe(dest('app/js'))
    done();
}


function watchStuff() {
    watch('app/scss/app.scss', compileSass);
    //watch('app/js/scripts.js', minifyJS);
}

function browserSync(cb) {
    sync.init({
        injectChanges: true,
        server: {
            baseDir: "app/"
        }
    });

    watchStuff();
}

exports.sync = browserSync;
exports.compileSass = compileSass
exports.minify = minifyJS;
exports.watchStuff = watchStuff

