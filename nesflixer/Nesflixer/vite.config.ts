/// <reference types="vitest" />

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import legacy from '@vitejs/plugin-legacy';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(), // Vue plugin voor Vite
    legacy({
      targets: ['defaults', 'not IE 11'], // Legacy ondersteuning
    }),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src')
    }
  },
  build: {
    target: 'esnext', // Modernere build voor betere prestaties
  },
  test: {
    globals: true, // Maak globale testinstellingen mogelijk
    environment: 'jsdom', // Virtuele DOM voor unit tests
  },
});
