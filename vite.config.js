import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
    plugins: [vue()],
    root: 'assets',
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true
    },
    build: {
        outDir: '../public/build',
        emptyOutDir: true,
        manifest: false,
        rollupOptions: {
            input: resolve(__dirname, 'assets/app.js'),
            output: {
                entryFileNames: 'app.js',
                chunkFileNames: 'chunks/[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.endsWith('.css')) {
                        return 'app.css'
                    }
                    return 'assets/[name][extname]'
                }
            }
        }
    }
})