const mix = require('laravel-mix');
const del = require('del');

mix.options({
    hmrOptions: {
        host: 'cloud.localhost',
        port: 8080,
    },
});

del('public/js/chunks')
del('public/vendors~js/chunks')

// fix css files 404 issue
mix.webpackConfig({
    // add any webpack dev server config here
    devServer: {
        proxy: {
            host: '192.168.1.228', // host machine ip
            port: 8080,
        },
        watchOptions:{
            aggregateTimeout: 200,
            poll: 5000,
        },
    },
    // externals: {
    //     'moment': 'moment',
    // },
});

mix.browserSync({
    ui: false,
    open: false,
    notify: false,
    host: 'cloud.localhost',
    proxy: 'cloud.localhost',
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/gocpa.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps(false);

if (mix.inProduction()) {
    mix.version();
}
