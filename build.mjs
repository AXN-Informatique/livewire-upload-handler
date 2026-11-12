// Build script : compile `resources/js/scripts.js` et `resources/css/styles.css`
// dans `dist/`, sous des noms hachés, et écrit `dist/manifest.json`, dont les
// clés (`scripts.js`, `scripts.min.js`, `styles.css`) sont lues par
// `ServiceProvider::registerBladeDirectives()`.
//
// Usage : node build.mjs

import { build } from 'esbuild'
import { mkdirSync, rmSync, writeFileSync } from 'node:fs'
import { basename, dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'

const ROOT = dirname(fileURLToPath(import.meta.url))
const DIST = resolve(ROOT, 'dist')

// Aucune cible (`target`) : le code est livré tel qu'écrit, sans transpilation,
// imbrication CSS native comprise, comme le faisait la chaîne webpack.
const variants = [
    // Chargé quand `app.debug` est vrai
    { key: 'scripts.js', name: 'scripts', entry: 'resources/js/scripts.js', options: { format: 'iife' } },
    { key: 'scripts.min.js', name: 'scripts.min', entry: 'resources/js/scripts.js', options: { format: 'iife', minify: true } },
    { key: 'styles.css', name: 'styles', entry: 'resources/css/styles.css', options: { minify: true } },
]

rmSync(DIST, { recursive: true, force: true })
mkdirSync(DIST)

const manifest = {}

for (const { key, name, entry, options } of variants) {
    const result = await build({
        entryPoints: { [name]: resolve(ROOT, entry) },
        entryNames: '[name].[hash]',
        outdir: DIST,
        // Le script n'importe rien : sans regroupement, il sort dans une simple
        // IIFE. Avec `bundle`, l'`eval()` direct ci-dessous le ferait traiter
        // comme un module CommonJS, sous une enveloppe inutile. Un `import`
        // ajouté un jour imposera de repasser à `bundle: true`.
        metafile: true,
        logLevel: 'info',
        // Le script lit les bibliothèques externes par `eval()` direct sur un
        // nom de variable globale configurable (`compressorjs_var`,
        // `sortablejs_var`) : esbuild le signale, et ne renomme alors pas les
        // variables des portées concernées, ce qui préserve le comportement.
        logOverride: { 'direct-eval': 'silent' },
        ...options,
    })

    manifest[key] = basename(Object.keys(result.metafile.outputs)[0])
}

writeFileSync(resolve(DIST, 'manifest.json'), JSON.stringify(manifest, null, 2) + '\n')

console.log('\n✅ Build terminé')
