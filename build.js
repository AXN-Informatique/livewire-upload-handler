const webpack = require('webpack');
const path = require('path');
const fs = require('fs');

// Import des configurations individuelles
const scriptsDev = require('./webpack/webpack.dev.js');
const scriptsMin = require('./webpack/webpack.prod.js');
const styles = require('./webpack/webpack.styles.js');

// Fonction pour exécuter une config Webpack
function runWebpack(config) {
  return new Promise((resolve, reject) => {
    webpack(config, (err, stats) => {
      if (err || stats.hasErrors()) {
        console.error(stats?.toString({ colors: true }));
        return reject(err || new Error('Webpack error'));
      }
      console.log(stats.toString({ colors: true }));
      resolve();
    });
  });
}

// Fonction pour fusionner les manifests partiels
function mergeManifests() {
  const distPath = path.resolve(__dirname, 'dist');
  const manifestFiles = [
    'manifest-partial-scripts-dev.json',
    'manifest-partial-scripts-min.json',
    'manifest-partial-styles.json',
  ];

  const finalManifest = {};

  manifestFiles.forEach(file => {
    const filePath = path.join(distPath, file);
    if (fs.existsSync(filePath)) {
      const content = JSON.parse(fs.readFileSync(filePath, 'utf8'));
      Object.assign(finalManifest, content);
      fs.unlinkSync(filePath); // Nettoyage des fichiers partiels
    }
  });

  fs.writeFileSync(
    path.join(distPath, 'manifest.json'),
    JSON.stringify(finalManifest, null, 2)
  );

  console.log('✅ Manifest merged successfully.');
}

// Exécution séquentielle
async function buildAll() {
  try {
    console.log('🔨 Compilation JS (non minifié)…');
    await runWebpack(scriptsDev);

    console.log('🔨 Compilation JS (minifié)…');
    await runWebpack(scriptsMin);

    console.log('🎨 Compilation CSS…');
    await runWebpack(styles);

    console.log('🧩 Fusion des manifests…');
    mergeManifests();

    console.log('✅ Build complet terminé avec succès !');
  } catch (error) {
    console.error('❌ Build échoué :', error);
    process.exit(1);
  }
}

buildAll();
