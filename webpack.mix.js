const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');

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

mix.options({
    terser: {
        terserOptions: {
            compress: {
                drop_console: true,
            },
        },
    },
    processCssUrls: false,
})
    .setPublicPath('public')
    .js('resources/js/app.js', 'public')
    .vue()
    .sass('resources/sass/app.scss', 'public', {
        sassOptions: {
            quietDeps: true,
            silenceDeprecations: ['legacy-js-api', 'import', 'global-builtin', 'color-functions', 'abs-percent', 'mixed-decls']
        }
    })
    .sass('resources/sass/app-dark.scss', 'public', {
        sassOptions: {
            quietDeps: true,
            silenceDeprecations: ['legacy-js-api', 'import', 'global-builtin', 'color-functions', 'abs-percent', 'mixed-decls']
        }
    })
    .version()
    .copy('resources/img', 'public/img')
    .webpackConfig({
        resolve: {
            symlinks: false,
            alias: {
                '@': path.resolve(__dirname, 'resources/js/'),
            },
        },
        plugins: [
            new webpack.IgnorePlugin({
                resourceRegExp: /^\.\/locale$/,
                contextRegExp: /moment$/,
            }),
        ],
    });
