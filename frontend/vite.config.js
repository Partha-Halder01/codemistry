import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'
import fs from 'node:fs'
import path from 'node:path'

// Inlines the generated stylesheet into <style> in index.html — eliminates render-blocking CSS.
const inlineCriticalCss = () => ({
  name: 'inline-critical-css',
  apply: 'build',
  closeBundle() {
    const distDir = path.resolve(__dirname, 'dist')
    const htmlPath = path.join(distDir, 'index.html')
    if (!fs.existsSync(htmlPath)) return
    let html = fs.readFileSync(htmlPath, 'utf8')
    // Match Vite's emitted stylesheet link (any attribute order, ignoring noscript blocks)
    const linkRe = /<link\b(?=[^>]*\brel=["']stylesheet["'])(?=[^>]*\bhref=["'](\/assets\/[^"']+\.css)["'])[^>]*\/?>/i
    const linkMatch = html.match(linkRe)
    if (!linkMatch) return
    const cssHref = linkMatch[1].replace(/^\//, '')
    const cssPath = path.join(distDir, cssHref)
    if (!fs.existsSync(cssPath)) return
    const css = fs.readFileSync(cssPath, 'utf8')
    html = html.replace(linkMatch[0], `<style>${css}</style>`)
    fs.writeFileSync(htmlPath, html, 'utf8')
  },
})

export default defineConfig({
  plugins: [react(), inlineCriticalCss()],
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          'react-vendor': ['react', 'react-dom', 'react-router-dom'],
          'ui-vendor': ['lucide-react'],
          'markdown': ['react-markdown', 'dompurify'],
          'seo': ['react-helmet-async'],
          'oauth': ['@react-oauth/google'],
          'axios': ['axios'],
        },
      },
    },
    chunkSizeWarningLimit: 500,
  },
  server: {
    allowedHosts: ['.ngrok-free.dev', 'johnie-acotyledonous-overhugely.ngrok-free.dev'],
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        }
      },
      '/sanctum': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      }
    }
  }
})
