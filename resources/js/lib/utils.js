// Date formatting utilities
export const formatDate = (date, options = {}) => {
  const defaultOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    ...options
  }
  
  return new Date(date).toLocaleDateString('id-ID', defaultOptions)
}

export const formatRelativeTime = (date) => {
  const now = new Date()
  const articleDate = new Date(date)
  const diffInSeconds = Math.floor((now - articleDate) / 1000)
  
  if (diffInSeconds < 60) return 'Baru saja'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} hari lalu`
  if (diffInSeconds < 2629746) return `${Math.floor(diffInSeconds / 604800)} minggu lalu`
  if (diffInSeconds < 31556952) return `${Math.floor(diffInSeconds / 2629746)} bulan lalu`
  
  return `${Math.floor(diffInSeconds / 31556952)} tahun lalu`
}

// Number formatting utilities
export const formatNumber = (number) => {
  if (number >= 1000000) {
    return (number / 1000000).toFixed(1) + 'M'
  }
  if (number >= 1000) {
    return (number / 1000).toFixed(1) + 'K'
  }
  return number.toString()
}

// String utilities
export const truncateText = (text, length = 100) => {
  if (text.length <= length) return text
  return text.substring(0, length).trim() + '...'
}

export const stripHtml = (html) => {
  return html.replace(/<[^>]*>/g, '')
}

// URL utilities
export const getAbsoluteUrl = (path) => {
  return `${window.location.origin}${path}`
}

// SEO utilities
export const generateMetaTags = (meta) => {
  const tags = []
  
  if (meta.title) {
    tags.push({ name: 'title', content: meta.title })
    tags.push({ property: 'og:title', content: meta.title })
    tags.push({ name: 'twitter:title', content: meta.title })
  }
  
  if (meta.description) {
    tags.push({ name: 'description', content: meta.description })
    tags.push({ property: 'og:description', content: meta.description })
    tags.push({ name: 'twitter:description', content: meta.description })
  }
  
  if (meta.image) {
    tags.push({ property: 'og:image', content: meta.image })
    tags.push({ name: 'twitter:image', content: meta.image })
  }
  
  if (meta.url) {
    tags.push({ property: 'og:url', content: meta.url })
    tags.push({ rel: 'canonical', href: meta.url })
  }
  
  return tags
}

// Performance utilities
export const debounce = (func, wait) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

export const throttle = (func, limit) => {
  let inThrottle
  return function() {
    const args = arguments
    const context = this
    if (!inThrottle) {
      func.apply(context, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

// Image utilities
export const getImageUrl = (path, size = 'original') => {
  if (!path) return '/images/placeholder.jpg'
  
  const sizes = {
    thumbnail: 'w_200,h_200,c_fill',
    medium: 'w_600,h_400,c_fill',
    large: 'w_1200,h_800,c_fill',
    original: ''
  }
  
  if (path.includes('cloudinary.com')) {
    return path.replace('/upload/', `/upload/${sizes[size]}/`)
  }
  
  return path
}

// Validation utilities
export const isValidEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

export const isValidUrl = (url) => {
  try {
    new URL(url)
    return true
  } catch {
    return false
  }
}

// Local storage utilities
export const storage = {
  get(key, defaultValue = null) {
    try {
      const item = localStorage.getItem(key)
      return item ? JSON.parse(item) : defaultValue
    } catch {
      return defaultValue
    }
  },
  
  set(key, value) {
    try {
      localStorage.setItem(key, JSON.stringify(value))
      return true
    } catch {
      return false
    }
  },
  
  remove(key) {
    try {
      localStorage.removeItem(key)
      return true
    } catch {
      return false
    }
  }
}

// Color utilities
export const generateColorFromString = (str) => {
  let hash = 0
  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash)
  }
  
  const color = (hash & 0x00FFFFFF).toString(16).toUpperCase()
  return '00000'.substring(0, 6 - color.length) + color
}

// Array utilities
export const shuffleArray = (array) => {
  const newArray = [...array]
  for (let i = newArray.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[newArray[i], newArray[j]] = [newArray[j], newArray[i]]
  }
  return newArray
}

export const chunkArray = (array, size) => {
  const chunks = []
  for (let i = 0; i < array.length; i += size) {
    chunks.push(array.slice(i, i + size))
  }
  return chunks
}

// Object utilities
export const deepClone = (obj) => {
  if (obj === null || typeof obj !== 'object') return obj
  if (obj instanceof Date) return new Date(obj.getTime())
  if (obj instanceof Array) return obj.map(deepClone)
  if (typeof obj === 'object') {
    const cloned = {}
    Object.keys(obj).forEach(key => {
      cloned[key] = deepClone(obj[key])
    })
    return cloned
  }
}

// Browser utilities
export const isMobile = () => {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
}

export const isIOS = () => {
  return /iPad|iPhone|iPod/.test(navigator.userAgent)
}

export const isAndroid = () => {
  return /Android/.test(navigator.userAgent)
}

// Analytics utilities
export const trackEvent = (eventName, properties = {}) => {
  if (typeof gtag !== 'undefined') {
    gtag('event', eventName, properties)
  }
  
  if (typeof fbq !== 'undefined') {
    fbq('track', eventName, properties)
  }
}

// Social sharing utilities
export const shareArticle = (platform, url, title, description) => {
  const encodedUrl = encodeURIComponent(url)
  const encodedTitle = encodeURIComponent(title)
  const encodedDescription = encodeURIComponent(description)
  
  const shareUrls = {
    twitter: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
    whatsapp: `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`,
    telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`
  }
  
  if (shareUrls[platform]) {
    window.open(shareUrls[platform], '_blank', 'width=600,height=400')
  }
}

// Export all utilities
export default {
  formatDate,
  formatRelativeTime,
  formatNumber,
  truncateText,
  stripHtml,
  getAbsoluteUrl,
  generateMetaTags,
  debounce,
  throttle,
  getImageUrl,
  isValidEmail,
  isValidUrl,
  storage,
  generateColorFromString,
  shuffleArray,
  chunkArray,
  deepClone,
  isMobile,
  isIOS,
  isAndroid,
  trackEvent,
  shareArticle
}
