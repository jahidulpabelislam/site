const gulp = require("gulp");

const include = require("gulp-include")

const rename = require("gulp-rename");

const uglify = require("gulp-uglify");

const cleanCss = require("gulp-clean-css");
const autoPrefix = require("gulp-autoprefixer");

const sass = require("gulp-sass")(require("node-sass"));
const sassVariables = require("gulp-sass-variables");

const livereload = require("gulp-livereload");

const del = require("del");

const { jsDir, jsDevDir, cssDir, scssDir } = require("./config");

const colourVariables = {};

var coloursJson = require("../colours.json");

for (const colour in coloursJson) {
    colourVariables[`$${colour}`] = coloursJson[colour];
}

let defaultTasks = [];

gulp.task("reload-listen", function(callback) {
    livereload.listen();
    callback();
});

gulp.task("clean-js-folder", function(callback) {
    del(`${jsDir}/**`, {force: true});
    callback();
});

gulp.task("compile-js", function() {
    return gulp.src(`${jsDevDir}/*.js`)
               .pipe(include({
                   hardFail: true,
               }))
               .pipe(gulp.dest(`${jsDir}/`))
               .pipe(livereload())
        ;
});

gulp.task("watch-js", function(callback) {
    gulp.watch(`${jsDevDir}/**/*.js`, gulp.parallel("compile-js"));
    callback();
});

gulp.task("minify-js", function() {
    return gulp.src([`${jsDir}/*.js`, `!${jsDir}/*.min.js`])
               .pipe(rename({suffix: ".min"}))
               .pipe(uglify())
               .pipe(gulp.dest(`${jsDir}/`))
        ;
});

// Get JavaScript files ready for production
defaultTasks.push("js");
gulp.task("js", gulp.series(["clean-js-folder", "compile-js", "minify-js"]));

gulp.task("clean-css-folder", function(callback) {
    del(`${cssDir}/**`, {force: true});
    callback();
});

defaultTasks.push("compile-css");
gulp.task("compile-css", function() {
    return gulp.src(`${scssDir}/*.scss`)
               .pipe(sassVariables(colourVariables))
               .pipe(sass().on("error", sass.logError))
               .pipe(gulp.dest(`${cssDir}/`))
               .pipe(livereload())
        ;
});

// Watch SCSS file changes to compile to CSS
gulp.task("watch-scss", function(callback) {
    gulp.watch(`${scssDir}/**/*.scss`, gulp.parallel("compile-css"));
    callback();
});

gulp.task("minify-css", function() {
    return gulp.src([`${cssDir}/*.css`, `!${cssDir}/*.min.css`])
               .pipe(rename({suffix: ".min"}))
               .pipe(autoPrefix({remove: false}))
               .pipe(cleanCss({compatibility: "ie8"}))
               .pipe(gulp.dest(`${cssDir}/`))
        ;
});

// Get CSS files ready for production
defaultTasks.push("css");
gulp.task("css", gulp.series(["clean-css-folder", "compile-css", "minify-css"]));

// Watch files for changes to then compile
gulp.task("watch", gulp.series(["reload-listen", "compile-css", "compile-js", "watch-scss", "watch-js"]));

gulp.task("default", gulp.series(defaultTasks));
