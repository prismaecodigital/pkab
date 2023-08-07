const mix = require('laravel-mix')

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

mix.webpackConfig({
  output: { chunkFilename: 'adminapp/js/chunks/[name].js?id=[chunkhash]' },  // output path and filename pattern
  resolve: {  // module resolution
    extensions: ['.js', '.vue', '.json'],
    alias: {  // shorter aliases instead of long directory paths.
      '@': __dirname + '/resources/adminapp/js',
      '@pages': __dirname + '/resources/adminapp/js/pages',
      '@components': __dirname + '/resources/adminapp/js/components',
      '@cruds': __dirname + '/resources/adminapp/js/cruds'
    }
  }
})

// Admin App
mix // asset compilation tasks
  .js('resources/adminapp/js/app.js', 'public/adminapp/js')
  .sass('resources/adminapp/sass/app.scss', 'public/adminapp/css')
  .version()

if (mix.inProduction()) {
  mix.version()
}