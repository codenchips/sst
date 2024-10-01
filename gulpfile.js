'use strict'


const { src, dest, watch } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sync = require("browser-sync").create();
const refresh = require('gulp-refresh');


function compileSass(done) {
    src('app/scss/app.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(dest('app/css'))
        .pipe(refresh({stream: true}));
    done();
}

function watchSass() {
    refresh.listen();
    watch('app/scss/app.scss', compileSass);
}

function browserSync(cb) {
    sync.init({
        injectChanges: true,

        server: {
            baseDir: "app/"
        }
    });

    refresh.listen();
    watchSass();
    //watch('app/scss/app.scss', compileSass);
}

exports.sync = browserSync;
exports.compileSass = compileSass
exports.watchSass = watchSass

