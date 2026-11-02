import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue2'
import path from 'node:path'

const root = path.resolve(import.meta.dirname)

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/adminapp/js/app.js',
        'resources/adminapp/sass/app.scss'
      ],
      refresh: ['resources/views/**']
    }),
    vue()
  ],
  resolve: {
    alias: {
      '@': path.resolve(root, 'resources/adminapp/js'),
      '@pages': path.resolve(root, 'resources/adminapp/js/pages'),
      '@components': path.resolve(root, 'resources/adminapp/js/components'),
      '@cruds': path.resolve(root, 'resources/adminapp/js/cruds')
    },
    extensions: ['.mjs', '.js', '.json', '.vue']
  },
  optimizeDeps: {
    // vue2-datatable ships its sources, so Vite does not pre-bundle it and its
    // CommonJS deep import of lodash would be served raw to the browser.
    include: ['lodash/groupBy', 'lodash/debounce', 'lodash/throttle', 'browser-downloads']
  },
  server: {
    host: '0.0.0.0',
    port: Number(process.env.VITE_PORT ?? 5173),
    strictPort: true,
    hmr: {
      host: process.env.VITE_HMR_HOST || 'localhost'
    }
  },
  build: {
    chunkSizeWarningLimit: 2048
  }
})
