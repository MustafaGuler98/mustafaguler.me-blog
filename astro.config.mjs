import { defineConfig } from 'astro/config';

export default defineConfig({
  site: 'https://mustafaguler.me',
  base: '/blog',                
  i18n: {
    locales: ['tr', 'en'],
    defaultLocale: 'tr',
    routing: {
      prefixDefaultLocale: true,
      redirectToDefaultLocale: true,
    },
  },
});
