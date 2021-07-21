let mix = require('laravel-mix');
const purgeCss = require('@fullhuman/postcss-purgecss');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const source = 'platform/themes/' + directory;
const dist = 'public/themes/' + directory;

mix
mix
    .sass(
        source + '/assets/sass/style.scss',
        dist + '/css',
        {},
        [
            purgeCss({
                content: [
                    source + '/assets/js/components/*.vue',
                    source + '/layouts/*.blade.php',
                    source + '/partials/*.blade.php',
                    source + '/partials/**/*.blade.php',
                    source + '/views/*.blade.php',
                    source + '/views/**/*.blade.php',
                    source + '/views/**/**/*.blade.php',
                    source + '/views/**/**/**/*.blade.php',
                    source + '/widgets/**/templates/frontend.blade.php',
                ],
                defaultExtractor: content => content.match(/[\w-/.:]+(?<!:)/g) || [],
                safelist: [
                    /^mfp-/,
                    /^loading_/,
                    /^owl-/,
                    /^ui-/,
                    /^mfp-/,
                    /^button-loading/,
                    /text/,
                    /shadow/,
                    /^slick-/,
                    /^noUi-/,
                    /^pagination/,
                    /^page-/,
                    /^label-/,
                    /^zoom/,
                    /show-admin-bar/,
                    /active/,
                    /selected/,
                    /show/
                ],
            })
        ])

    .scripts([
        source + '/assets/js/backend.js',
        source + '/assets/js/scripts.js',
    ], dist + '/js/scripts.js')

    .js(source + '/assets/js/app.js', dist + '/js')

    .copyDirectory(dist + '/css', source + '/public/css')
    .copyDirectory(dist + '/js', source + '/public/js');
