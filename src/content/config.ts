import { defineCollection, z } from 'astro:content';

const posts = defineCollection({
  type: 'content',
  schema: z.object({
    title: z.string(),
    description: z.string().max(160).optional(),
    lang: z.enum(['tr', 'en']),
    pubDate: z.date(),
    updatedDate: z.date().optional(),
    draft: z.boolean().default(false),
    tags: z.array(z.string()).default([]),
    translationOf: z.string().optional(),
  }),
});

export const collections = { posts };

