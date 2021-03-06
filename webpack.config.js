let Encore = require('@symfony/webpack-encore');

Encore

    /**
     * Fix Webpack with PHPStorm compatibility
     * @see https://github.com/symfony/webpack-encore/pull/115
     */
    .configureRuntimeEnvironment('dev-server', {
        keepPublicPath: true,
        https: true,
    })

    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // Public path used by the web server to access the output path
    .setPublicPath('/build')
    // Only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. `app.js`)
     * and one CSS file (e.g. `app.css`) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')

    // Polyfills
    .addEntry('conic_gradient', './assets/js/polyfills/conic_gradient.js')


    // When enabled,
    // Webpack "splits" your files into smaller pieces
    // for greater optimization.
    .splitEntryChunks()

    // Will require an extra script tag for `runtime.js`
    // but, you probably want this,
    // unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below.
     * For a full list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // Enables hashed filenames (e.g. `app.abc123.css`)
    .enableVersioning(Encore.isProduction())

    // Enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // Enables Sass/SCSS support
    .enableSassLoader()

    // Uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // Uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes()

    // Uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // Uncomment if you use API Platform Admin (`composer req api-admin`)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')

    /**
     * Copy and rename static files into your final output directory.
     * Hash for file names will be used only in Production environment
     * @see https://symfony.com/doc/current/frontend/encore/copy-files.html
     */
    .copyFiles([{
            from: './assets/images',
            to: Encore.isProduction()
                ? 'images/[path][name].[hash:8].[ext]'
                : 'images/[path][name].[ext]'
        }, {
            from: './assets/static', pattern: /\.(php)$/,
            to: '../[name].[ext]'
        }, {
            from: './assets/static', pattern: /\.(htaccess)$/,
            to: '../.[ext]'
        }
    ])
;

module.exports = Encore.getWebpackConfig();
