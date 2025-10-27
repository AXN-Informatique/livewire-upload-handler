const path = require('path');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

module.exports = {
  name: 'scripts-dev',
  mode: 'development',
  entry: {
    scripts: './resources/js/scripts.js',
  },
  output: {
    filename: 'scripts.[contenthash].js',
    path: path.resolve(__dirname, '../dist'),
    publicPath: '',
    clean: true,
    sourceMapFilename: 'scripts.[contenthash].js.map',
  },
  devtool: 'source-map',
  optimization: {
    minimize: false,
  },
  plugins: [
    new WebpackManifestPlugin({
      fileName: 'manifest-partial-scripts-dev.json',
      generate: (seed, files) => {
        const manifest = {};
        files.forEach(file => {
          if (file.name.endsWith('.js')) {
            manifest['scripts.js'] = file.path;
          }
        });
        return manifest;
      },
    }),
  ],
};
