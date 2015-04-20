var elixir = require('laravel-elixir');

var paths = {
 'jquery': './resources/assets/vendor/jquery/',
 'bootstrap': './resources/assets/vendor/bootstrap-sass/assets/'
};
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
 mix
     .sass("app.scss", 'public/assets/css/', {includePaths: [paths.bootstrap + 'stylesheets']})
     .copy(paths.bootstrap + 'fonts/bootstrap/**', 'public/assets/fonts')
     .scripts([
      paths.jquery + "dist/jquery.js",
      paths.bootstrap + "javascripts/bootstrap.js"
     ], 'public/assets/js/app.js', './');
});