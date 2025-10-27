const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

function CleanEmptyJsPlugin() {
  return {
    apply: (compiler) => {
      compiler.hooks.emit.tap('CleanEmptyJsPlugin', (compilation) => {
        Object.keys(compilation.assets).forEach((filename) => {
          if (
            (filename.startsWith('styles') && filename.endsWith('.js')) ||
            (filename.startsWith('styles') && filename.endsWith('.js.map'))
          ) {
            delete compilation.assets[filename];
          }
        });
      });
    },
  };
}

module.exports = {
  name: 'styles',
  mode: 'production',
  entry: {
    styles: './resources/css/styles.css',
  },
  output: {
    path: path.resolve(__dirname, '../dist'),
    publicPath: '',
    clean: false,
  },
  module: {
    rules: [
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader'],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'styles.[contenthash].css',
    }),
    new CssMinimizerPlugin(),
    new WebpackManifestPlugin({
      fileName: 'manifest-partial-styles.json',
      generate: (seed, files) => {
        const manifest = {};
        files.forEach(file => {
          if (file.name.endsWith('.css')) {
            manifest['styles.css'] = file.path;
          }
        });
        return manifest;
      },
    }),
    CleanEmptyJsPlugin(), // 👈 suppression des fichiers JS vides
  ],
  optimization: {
    minimize: true,
  },
};
