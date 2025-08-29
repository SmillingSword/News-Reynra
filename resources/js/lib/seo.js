import { usePage } from '@inertiajs/vue3'

/**
 * SEO Helper for News Reynra
 * Provides utilities for managing meta tags, OpenGraph, and SEO optimization
 */

export function setMeta({
  title = null,
  description = null,
  ogImage = null,
  canonical = null,
  noindex = false,
  keywords = null,
  author = null,
  publishedTime = null,
  modifiedTime = null,
  section = null,
  tags = null,
  type = 'website'
} = {}) {
  const page = usePage()
  const baseUrl = page.props.app?.url || 'https://newsreynra.com'
  const siteName = 'News Reynra'
  const defaultDescription = 'Hub berita gaming terpercaya dengan kurasi berkualitas, pencarian cerdas, dan personalisasi untuk komunitas gamer Indonesia.'
  
  // Build complete title
  const fullTitle = title ? `${title} | ${siteName}` : siteName
  
  // Build canonical URL
  const canonicalUrl = canonical || `${baseUrl}${page.url}`
  
  // Meta tags object
  const metaTags = {
    // Basic meta tags
    title: fullTitle,
    'meta:description': description || defaultDescription,
    'meta:keywords': keywords,
    'meta:author': author,
    'meta:robots': noindex ? 'noindex,nofollow' : 'index,follow',
    'meta:canonical': canonicalUrl,
    
    // OpenGraph tags
    'meta:og:title': title || siteName,
    'meta:og:description': description || defaultDescription,
    'meta:og:image': ogImage || `${baseUrl}/images/og-default.jpg`,
    'meta:og:url': canonicalUrl,
    'meta:og:type': type,
    'meta:og:site_name': siteName,
    'meta:og:locale': 'id_ID',
    
    // Twitter Card tags
    'meta:twitter:card': 'summary_large_image',
    'meta:twitter:site': '@newsreynra',
    'meta:twitter:title': title || siteName,
    'meta:twitter:description': description || defaultDescription,
    'meta:twitter:image': ogImage || `${baseUrl}/images/og-default.jpg`,
    
    // Article specific tags (for news articles)
    ...(type === 'article' && {
      'meta:article:published_time': publishedTime,
      'meta:article:modified_time': modifiedTime,
      'meta:article:author': author,
      'meta:article:section': section,
      'meta:article:tag': Array.isArray(tags) ? tags.join(',') : tags,
    }),
    
    // JSON-LD structured data will be handled separately
  }
  
  // Filter out null/undefined values
  const filteredMeta = Object.fromEntries(
    Object.entries(metaTags).filter(([_, value]) => value != null)
  )
  
  return filteredMeta
}

/**
 * Generate JSON-LD structured data for articles
 */
export function generateArticleSchema({
  headline,
  description,
  image,
  datePublished,
  dateModified,
  author,
  publisher = 'News Reynra',
  url,
  category,
  keywords = []
} = {}) {
  const baseUrl = usePage().props.app?.url || 'https://newsreynra.com'
  
  return {
    '@context': 'https://schema.org',
    '@type': 'NewsArticle',
    headline,
    description,
    image: image ? [image] : [`${baseUrl}/images/og-default.jpg`],
    datePublished,
    dateModified: dateModified || datePublished,
    author: {
      '@type': 'Person',
      name: author
    },
    publisher: {
      '@type': 'Organization',
      name: publisher,
      logo: {
        '@type': 'ImageObject',
        url: `${baseUrl}/images/logo.png`
      }
    },
    url,
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': url
    },
    articleSection: category,
    keywords: Array.isArray(keywords) ? keywords : [keywords],
    inLanguage: 'id-ID'
  }
}

/**
 * Generate JSON-LD structured data for website
 */
export function generateWebsiteSchema() {
  const baseUrl = usePage().props.app?.url || 'https://newsreynra.com'
  
  return {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: 'News Reynra',
    description: 'Hub berita gaming terpercaya dengan kurasi berkualitas, pencarian cerdas, dan personalisasi untuk komunitas gamer Indonesia.',
    url: baseUrl,
    potentialAction: {
      '@type': 'SearchAction',
      target: {
        '@type': 'EntryPoint',
        urlTemplate: `${baseUrl}/search?q={search_term_string}`
      },
      'query-input': 'required name=search_term_string'
    },
    publisher: {
      '@type': 'Organization',
      name: 'News Reynra',
      logo: {
        '@type': 'ImageObject',
        url: `${baseUrl}/images/logo.png`
      },
      sameAs: [
        'https://twitter.com/newsreynra',
        'https://discord.gg/newsreynra'
      ]
    }
  }
}

/**
 * Generate JSON-LD structured data for game reviews
 */
export function generateReviewSchema({
  itemReviewed,
  reviewRating,
  author,
  datePublished,
  reviewBody,
  url
} = {}) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Review',
    itemReviewed: {
      '@type': 'VideoGame',
      name: itemReviewed.name,
      genre: itemReviewed.genre,
      platform: itemReviewed.platform,
      developer: itemReviewed.developer,
      publisher: itemReviewed.publisher
    },
    reviewRating: {
      '@type': 'Rating',
      ratingValue: reviewRating.value,
      bestRating: reviewRating.max || 10,
      worstRating: reviewRating.min || 1
    },
    author: {
      '@type': 'Person',
      name: author
    },
    datePublished,
    reviewBody,
    url,
    inLanguage: 'id-ID'
  }
}

/**
 * Generate breadcrumb JSON-LD
 */
export function generateBreadcrumbSchema(breadcrumbs = []) {
  const baseUrl = usePage().props.app?.url || 'https://newsreynra.com'
  
  const itemListElement = breadcrumbs.map((crumb, index) => ({
    '@type': 'ListItem',
    position: index + 1,
    name: crumb.name,
    item: crumb.url.startsWith('http') ? crumb.url : `${baseUrl}${crumb.url}`
  }))
  
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement
  }
}

/**
 * Helper to inject JSON-LD script into head
 */
export function injectJsonLd(schema) {
  if (typeof window !== 'undefined') {
    const script = document.createElement('script')
    script.type = 'application/ld+json'
    script.textContent = JSON.stringify(schema)
    document.head.appendChild(script)
  }
}

/**
 * Generate sitemap entry data
 */
export function generateSitemapEntry({
  url,
  lastmod = new Date().toISOString(),
  changefreq = 'weekly',
  priority = 0.8
} = {}) {
  return {
    url,
    lastmod,
    changefreq,
    priority
  }
}

/**
 * Generate RSS feed item data
 */
export function generateRSSItem({
  title,
  description,
  link,
  pubDate,
  author,
  category,
  guid
} = {}) {
  return {
    title,
    description,
    link,
    pubDate: new Date(pubDate).toUTCString(),
    author,
    category,
    guid: guid || link
  }
}

/**
 * Utility to format reading time
 */
export function calculateReadingTime(content, wordsPerMinute = 200) {
  if (!content) return 0
  
  // Strip HTML tags and count words
  const text = content.replace(/<[^>]*>/g, '')
  const wordCount = text.trim().split(/\s+/).length
  const readingTime = Math.ceil(wordCount / wordsPerMinute)
  
  return readingTime
}

/**
 * Generate meta description from content
 */
export function generateMetaDescription(content, maxLength = 160) {
  if (!content) return ''
  
  // Strip HTML tags
  const text = content.replace(/<[^>]*>/g, '')
  
  // Truncate to maxLength
  if (text.length <= maxLength) return text
  
  // Find last complete sentence within limit
  const truncated = text.substring(0, maxLength)
  const lastSentence = truncated.lastIndexOf('.')
  
  if (lastSentence > maxLength * 0.7) {
    return truncated.substring(0, lastSentence + 1)
  }
  
  // Fallback: truncate at word boundary
  const lastSpace = truncated.lastIndexOf(' ')
  return truncated.substring(0, lastSpace) + '...'
}

export default {
  setMeta,
  generateArticleSchema,
  generateWebsiteSchema,
  generateReviewSchema,
  generateBreadcrumbSchema,
  injectJsonLd,
  generateSitemapEntry,
  generateRSSItem,
  calculateReadingTime,
  generateMetaDescription
}
