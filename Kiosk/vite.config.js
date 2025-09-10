import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { resolve } from 'path'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'public/build',
    assetsDir: 'assets',
    manifest: true,
    rollupOptions: {
      input: {
        kiosk: resolve(__dirname, 'src/main.jsx')
      },
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]'
      }
    }
  },
  server: {
    hmr: {
      host: 'localhost'
    }
  }
})
