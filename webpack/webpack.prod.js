const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

module.exports = {
  name: 'scripts-min',
  mode: 'production',
  entry: {
    'scripts.min': './resources/js/scripts.js',
  },
  output: {
    filename: 'scripts.min.[contenthash].js',
    path: path.resolve(__dirname, '../dist'),
    publicPath: '',
    clean: false,
  },
  optimization: {
    minimize: true,
    minimizer: [new TerserPlugin()],
  },
  plugins: [
    new WebpackManifestPlugin({
      fileName: 'manifest-partial-scripts-min.json',
      generate: (seed, files) => {
        const manifest = {};
        files.forEach(file => {
          if (file.name.endsWith('.js')) {
            manifest['scripts.min.js'] = file.path;
          }
        });
        return manifest;
      },
    }),
  ],
};
