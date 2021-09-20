const gulp = require("gulp");

const include = require("gulp-include");

const sourcemaps = require("gulp-sourcemaps");

const rename = require("gulp-rename");

const uglify = require("gulp-uglify");

const cleanCss = require("gulp-clean-css");
const autoPrefix = require("gulp-autoprefixer");

const sass = require("gulp-sass")(require("node-sass"));
const sassVars = require("gulp-sass-vars");
const jsonImporter = require("node-sass-json-importer");

const livereload = require("gulp-livereload");

const del = require("del");

const { devDir, jsDir, jsDevDir, cssDir, scssDir } = require("./config");

const colourVariables = {};

var coloursJson = require("../config/colours.json");

for (const colour in coloursJson) {
    colourVariables[`${colour}-colour`] = coloursJson[colour];
}

let defaultTasks = [];

gulp.task("reload-listen", function(callback) {
    livereload.listen();
    callback();
});

gulp.task("clean-js-folder", function(callback) {
    del(`${jsDir}/*.js`);
    callback();
});

gulp.task("compile-js", function() {
    return gulp.src(`${jsDevDir}/*.js`)
               .pipe(sourcemaps.init())
               .pipe(include({
                   hardFail: true,
               }))
               .pipe(sourcemaps.write("maps/"))
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
    del(`${cssDir}/*.css`);
    callback();
});

gulp.task("compile-css", function() {
    return gulp.src(`${scssDir}/*.scss`)
               .pipe(sourcemaps.init())
               .pipe(sassVars(colourVariables))
               .pipe(
                   sass({
                       importer: jsonImporter(),
                       sourceStyle: "nested",
                       includePaths: [
                           `${devDir}/config`,
                       ],
                   })
                   .on("error", sass.logError)
               )
               .pipe(sourcemaps.write("maps/"))
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