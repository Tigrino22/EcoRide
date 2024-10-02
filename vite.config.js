import { defineConfig } from "rollup"
// vite.config.js
export default defineConfig({
    server: {
        port: '3000',
        origin: 'http://localhost'
    } ,
    base: '/',
    build: {
        copyPublicDir: false,
        outDir: 'Public/assets',
        assetsDir: '',
      // generate .vite/manifest.json in outDir
        manifest: true,
        rollupOptions: {
          // overwrite default .html entry
            input: 'assets/ts/main.ts',
        },
    },
  })