import { defineConfig } from 'astro/config';

const SITE = process.env.SITE_URL || 'https://mustafaguler.me';
const BASE = process.env.BLOG_BASE || '/blog/'; 
export default defineConfig({
  site: SITE,
  base: BASE,
  i18n: {
    locales: ['tr', 'en'],
    defaultLocale: 'tr',
    routing: {
      prefixDefaultLocale: true,
      redirectToDefaultLocale: true
    }
  }
});

