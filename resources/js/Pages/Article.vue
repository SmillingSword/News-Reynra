<template>
  <Head>
    <title>{{ article.meta_title || article.title }}</title>
    <meta name="description" :content="article.meta_description || article.excerpt || ''" />
    <meta name="keywords" :content="(article.tags || []).map(t => t?.name).join(', ')" />

    <meta property="og:title" :content="article.meta_title || article.title" />
    <meta property="og:description" :content="article.meta_description || article.excerpt || ''" />
    <meta property="og:image" :content="article.featured_image || ''" />
    <meta property="og:url" :content="safeRoute('articles.show', article.slug, `/articles/${article.slug || ''}`)" />
    <meta property="og:type" content="article" />
    <meta property="article:published_time" :content="article.published_at || ''" />
    <meta property="article:author" :content="article.author?.name || ''" />
    <meta property="article:section" :content="article.category?.name || ''" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="article.meta_title || article.title" />
    <meta name="twitter:description" :content="article.meta_description || article.excerpt || ''" />
    <meta name="twitter:image" :content="article.featured_image || ''" />

    <link rel="canonical" :href="safeRoute('articles.show', article.slug, `/articles/${article.slug || ''}`)" />
  </Head>

  <PublicLayout>
    <!-- JSON-LD Structured Data -->
    <component :is="'script'" type="application/ld+json" v-html="structuredData" />

    <!-- Reading Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1 bg-transparent z-50">
      <div class="h-full" :style="{ background: progressGradient, width: `${readingProgress}%`, transition: 'width 300ms ease' }" />
    </div>

    <!-- Hero -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0 pointer-events-none opacity-80 dark:opacity-40" aria-hidden>
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full blur-3xl bg-blue-500/20" />
        <div class="absolute -bottom-24 -right-24 w-[32rem] h-[32rem] rounded-full blur-3xl bg-purple-500/20" />
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="grid lg:grid-cols-12 gap-8 items-center">
          <div class="lg:col-span-7">
            <div v-if="article?.category?.name || article?.is_featured" class="flex flex-wrap items-center gap-2 mb-5">
              <span v-if="article?.category?.name" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ring-1 ring-inset ring-blue-200 text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20">
                {{ article.category.name }}
              </span>
              <span v-if="article?.is_featured" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ring-1 ring-yellow-200 text-yellow-800 dark:text-yellow-200 bg-yellow-50 dark:bg-yellow-900/20">
                Featured
              </span>
            </div>

            <h1 class="text-3xl lg:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight mb-4">
              {{ article?.title || 'Untitled' }}
            </h1>

            <p v-if="article?.excerpt" class="text-lg text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
              {{ article.excerpt }}
            </p>

            <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-gray-600 dark:text-gray-400">
              <div v-if="article?.author" class="flex items-center gap-2">
                <!-- Use default headshot avatar for author -->
                <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-white dark:ring-gray-900 bg-gray-100 dark:bg-gray-800">
                  <svg class="w-full h-full text-gray-400 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ article.author?.name || 'Unknown' }}</p>
                  <p class="text-xs">{{ article.author?.role || 'Author' }}</p>
                </div>
              </div>

              <div v-if="article?.published_at" class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>{{ formatDate(article.published_at) }}</span>
              </div>

              <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ readingTime }} min read</span>
              </div>

              <div v-if="article?.views_count" class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                <span>{{ formatNumber(article.views_count) }} views</span>
              </div>
            </div>

            <div v-if="(article?.tags || []).length" class="mt-6">
              <div class="flex flex-wrap gap-2">
                <span v-for="tag in article.tags" :key="tag.id || tag.name" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                  #{{ tag.name }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="article?.featured_image" class="lg:col-span-5">
            <div class="relative group rounded-2xl overflow-hidden shadow-2xl ring-1 ring-black/5 dark:ring-white/5">
              <img :src="article.featured_image" :alt="article?.title || 'Feature image'" class="w-full h-72 lg:h-96 object-cover transition-transform duration-500 group-hover:scale-[1.02]" loading="lazy" />
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Body + Sidebar -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <article class="lg:col-span-8">
          <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur rounded-2xl ring-1 ring-black/5 dark:ring-white/10 p-6 lg:p-8 shadow-lg">
            <div class="prose prose-lg dark:prose-invert max-w-none" v-html="article?.content || ''" />

            <!-- Share -->
            <div class="mt-8 pt-6 border-t border-gray-200/70 dark:border-gray-700/70">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Share this article</h3>
              <div class="flex flex-wrap gap-3">
                <button @click="shareOnTwitter" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1DA1F2] text-white hover:opacity-90 transition">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                  Twitter
                </button>
                <button @click="shareOnFacebook" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1877F2] text-white hover:opacity-90 transition">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                  Facebook
                </button>
                <button @click="shareOnLinkedIn" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#0A66C2] text-white hover:opacity-90 transition">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.27-3.037-2.052-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                  LinkedIn
                </button>
              </div>
            </div>

            <!-- Author Bio -->
            <div v-if="article?.author" class="mt-8 p-6 rounded-xl bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 ring-1 ring-black/5 dark:ring-white/10">
              <div class="flex items-start gap-4">
                <!-- Use default headshot avatar for author bio -->
                <div class="w-16 h-16 rounded-full overflow-hidden ring-2 ring-white dark:ring-gray-900 bg-gray-100 dark:bg-gray-800">
                  <svg class="w-full h-full text-gray-400 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                  </svg>
                </div>
                <div>
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ article.author?.name }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ article.author?.role || 'Author' }}</p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ article.author?.bio || 'Gaming enthusiast and writer.' }}</p>
                </div>
              </div>
            </div>
          </div>
        </article>

        <!-- Sidebar -->
        <aside class="lg:col-span-4">
          <!-- TOC -->
          <div v-if="tableOfContents.length" class="sticky top-24 bg-white/70 dark:bg-gray-900/60 backdrop-blur rounded-2xl ring-1 ring-black/5 dark:ring-white/10 p-6 shadow-lg mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Table of Contents</h3>
            <nav>
              <ul class="space-y-2">
                <li v-for="item in tableOfContents" :key="item.id">
                  <a :href="`#${item.id}`" class="group inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :class="{ 'ml-4': item.level === 3, 'ml-8': item.level === 4 }">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-blue-500 mr-2" />
                    {{ item.text }}
                  </a>
                </li>
              </ul>
            </nav>
          </div>

          <!-- Related -->
          <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur rounded-2xl ring-1 ring-black/5 dark:ring-white/10 p-6 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Articles</h3>
            <div class="space-y-4">
              <article v-for="related in relatedArticles" :key="related.id" class="group">
                <Link :href="route('articles.show', related.slug)" class="block">
                  <div class="relative overflow-hidden rounded-lg">
                    <img :src="related.featured_image" :alt="related.title" class="w-full h-32 object-cover rounded-lg transition-transform duration-500 group-hover:scale-[1.02]" loading="lazy" />
                    <div class="absolute inset-0 ring-1 ring-inset ring-black/5 rounded-lg" />
                  </div>
                  <h4 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ related.title }}</h4>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(related.published_at) }}</p>
                </Link>
              </article>
            </div>
          </div>
        </aside>
      </div>
    </section>

    <!-- Comments -->
    <section v-if="article?.allow_comments" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="bg-white/70 dark:bg-gray-900/60 backdrop-blur rounded-2xl ring-1 ring-black/5 dark:ring-white/10 p-6 lg:p-8 shadow-lg">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Comments ({{ commentsCount }})</h2>
          <div class="flex items-center gap-3 text-sm">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
              <input type="checkbox" v-model="sortNewestFirst" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
              <span class="text-gray-600 dark:text-gray-300">Newest first</span>
            </label>
            <button v-if="!showAllComments && commentsCount > initialCommentLimit" @click="showAllComments = true" class="px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">
              Show all ({{ commentsCount }})
            </button>
            <button v-else-if="showAllComments && commentsCount > initialCommentLimit" @click="showAllComments = false" class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-700">
              Show less
            </button>
          </div>
        </div>

        <!-- Comment Form -->
        <div class="mb-8">
          <form @submit.prevent="submitComment" class="space-y-4">
            <!-- User Info (for anonymous users) -->
            <div v-if="!$page.props.auth?.user" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                <input 
                  v-model="commentForm.author_name" 
                  type="text" 
                  placeholder="Your name" 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" 
                  required 
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                <input 
                  v-model="commentForm.author_email" 
                  type="email" 
                  placeholder="your@email.com" 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" 
                  required 
                />
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Your email won't be published</p>
              </div>
            </div>

            <!-- Comment Content -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment *</label>
              <textarea 
                v-model="commentForm.content" 
                placeholder="Share your thoughts..." 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white" 
                rows="4" 
                required 
              />
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Be respectful. Keep it civil and on topic.</p>
                <div v-if="!$page.props.auth?.user" class="flex items-center space-x-2">
                  <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-xs text-green-600 dark:text-green-400">Anonymous comments allowed</span>
                </div>
              </div>
              <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105" :disabled="submittingComment">
                {{ submittingComment ? 'Posting…' : 'Post Comment' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Comments List -->
        <TransitionGroup name="list" tag="div" class="space-y-4">
          <div v-for="comment in displayedComments" :key="comment.id" class="rounded-xl p-4 ring-1 ring-black/5 dark:ring-white/10 bg-white dark:bg-gray-800">
            <div class="flex items-start gap-3">
              <div v-if="comment.user?.avatar" class="w-10 h-10 rounded-full overflow-hidden">
                <img :src="comment.user.avatar" :alt="comment.user?.name || 'User'" class="w-full h-full object-cover" />
              </div>
              <div v-else class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                {{ getInitials(comment.user?.name || comment.author_name || 'Anonymous') }}
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white leading-none">{{ comment.user?.name || comment.author_name || 'Anonymous' }}</h4>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatRelativeTime(comment.created_at) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <button 
                      v-if="$page.props.auth?.user"
                      @click="likeComment(comment.id)" 
                      class="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                      <span>{{ comment.likes_count || 0 }}</span>
                    </button>
                    <div 
                      v-else 
                      class="inline-flex items-center gap-1 text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed"
                      title="Login to like comments"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                      <span>{{ comment.likes_count || 0 }}</span>
                    </div>
                    <button @click="toggleReplyForm(comment.id)" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Reply</button>
                  </div>
                </div>
                <p class="text-gray-800 dark:text-gray-200 mt-2">{{ comment.content }}</p>

                <!-- Reply Form -->
                <div v-if="showReplyForm[comment.id]" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <form @submit.prevent="submitReply(comment.id)" class="space-y-3">
                    <div v-if="!$page.props.auth?.user" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <input 
                        v-model="replyForms[comment.id].author_name" 
                        type="text" 
                        placeholder="Your name" 
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm" 
                        required 
                      />
                      <input 
                        v-model="replyForms[comment.id].author_email" 
                        type="email" 
                        placeholder="your@email.com" 
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm" 
                        required 
                      />
                    </div>
                    <textarea 
                      v-model="replyForms[comment.id].content" 
                      placeholder="Write your reply..." 
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm" 
                      rows="3" 
                      required 
                    />
                    <div class="flex items-center gap-2">
                      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Post Reply
                      </button>
                      <button type="button" @click="cancelReply(comment.id)" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors text-sm">
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Replies -->
                <div v-if="(comment.replies || []).length" class="mt-4 space-y-3 border-l border-gray-200 dark:border-gray-700 pl-4">
                  <div v-for="reply in comment.replies" :key="reply.id" class="flex items-start gap-3">
                    <div v-if="reply.user?.avatar" class="w-8 h-8 rounded-full overflow-hidden">
                      <img :src="reply.user.avatar" :alt="reply.user?.name || 'User'" class="w-full h-full object-cover" />
                    </div>
                    <div v-else class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white font-semibold text-xs">
                      {{ getInitials(reply.user?.name || reply.author_name || 'Anonymous') }}
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white">{{ reply.user?.name || reply.author_name || 'Anonymous' }}</h5>
                        <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ formatRelativeTime(reply.created_at) }}</span>
                      </div>
                      <p class="text-sm text-gray-800 dark:text-gray-200 mt-1">{{ reply.content }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>
    </section>
  </PublicLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { formatDate, formatNumber } from '@/lib/utils'

// Props
const props = defineProps({
  article: { type: Object, required: true },
  relatedArticles: { type: Array, default: () => [] },
  comments: { type: Array, default: () => [] }
})

// Reactive
const readingProgress = ref(0)
const tableOfContents = ref([])
const submittingComment = ref(false)
const sortNewestFirst = ref(true)
const showAllComments = ref(false)
const initialCommentLimit = 10

// Comment form data
const commentForm = ref({
  content: '',
  author_name: '',
  author_email: '',
  parent_id: null
})

// Reply form data
const replyForms = ref({})
const showReplyForm = ref({})

// Helper: safe route (Ziggy) fallback
const safeRoute = (name, param, fallback) => {
  try { if (typeof route === 'function') return route(name, param) } catch (e) {}
  return fallback
}

// Helper: slugify for heading IDs
const slugify = (s) => String(s || '')
  .toLowerCase()
  .replace(/<[^>]+>/g, '')
  .replace(/[^\w\s-]/g, '')
  .trim()
  .replace(/\s+/g, '-')

// Computed
const readingTime = computed(() => {
  const wordsPerMinute = 200
  const raw = (props.article?.content || '').replace(/<[^>]*>/g, '')
  const wordCount = raw ? raw.trim().split(/\s+/).length : 0
  return Math.max(1, Math.ceil(wordCount / wordsPerMinute))
})

const structuredData = computed(() => {
  const a = props.article || {}
  const author = a.author || {}
  const category = a.category || {}
  return JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: a.title || '',
    description: a.excerpt || '',
    image: a.featured_image || '',
    author: { '@type': 'Person', name: author.name || '', url: safeRoute('authors.show', author.slug, `/authors/${author.slug || ''}`) },
    publisher: { '@type': 'Organization', name: 'News Reynra', logo: { '@type': 'ImageObject', url: '/images/logo.png' } },
    datePublished: a.published_at || '',
    dateModified: a.updated_at || '',
    mainEntityOfPage: { '@type': 'WebPage', '@id': safeRoute('articles.show', a.slug, `/articles/${a.slug || ''}`) },
    articleSection: category.name || '',
    keywords: (a.tags || []).map(t => t?.name).join(', ')
  })
})

const commentsCount = computed(() => (props.comments || []).length)

const sortedComments = computed(() => {
  const arr = [...(props.comments || [])]
  arr.sort((a, b) => {
    const da = new Date(a?.created_at || 0).getTime()
    const db = new Date(b?.created_at || 0).getTime()
    return sortNewestFirst.value ? db - da : da - db
  })
  return arr
})

const displayedComments = computed(() => {
  if (showAllComments.value) return sortedComments.value
  return sortedComments.value.slice(0, initialCommentLimit)
})

const progressGradient = computed(() => 'linear-gradient(90deg, rgba(59,130,246,1) 0%, rgba(147,51,234,1) 100%)')

// Methods
const formatRelativeTime = (date) => {
  if (!date) return ''
  const now = new Date()
  const d = new Date(date)
  const diffH = Math.floor((now - d) / (1000 * 60 * 60))
  if (isNaN(diffH)) return ''
  if (diffH < 1) return 'Just now'
  if (diffH < 24) return `${diffH}h ago`
  if (diffH < 168) return `${Math.floor(diffH / 24)}d ago`
  return formatDate(date)
}

const shareOnTwitter = () => {
  const url = encodeURIComponent(window.location.href)
  const text = encodeURIComponent(props.article?.title || '')
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank')
}

const shareOnFacebook = () => {
  const url = encodeURIComponent(window.location.href)
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
}

const shareOnLinkedIn = () => {
  const url = encodeURIComponent(window.location.href)
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank')
}

const submitComment = async () => {
  if (!commentForm.value.content.trim()) return
  
  submittingComment.value = true
  
  try {
    router.post(route('articles.comments.store', props.article.slug), {
      content: commentForm.value.content,
      author_name: commentForm.value.author_name,
      author_email: commentForm.value.author_email,
      parent_id: commentForm.value.parent_id
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Reset form
        commentForm.value = {
          content: '',
          author_name: '',
          author_email: '',
          parent_id: null
        }
      },
      onFinish: () => {
        submittingComment.value = false
      }
    })
  } catch (error) {
    console.error('Error submitting comment:', error)
    submittingComment.value = false
  }
}

const likeComment = async (commentId) => {
  try {
    // Optimistically update the UI first
    const comment = props.comments.find(c => c.id === commentId)
    if (comment) {
      comment.likes_count = (comment.likes_count || 0) + 1
    }

    // Then make the API call
    router.post(route('comments.like', commentId), {}, {
      preserveScroll: true,
      onError: (errors) => {
        // Revert the optimistic update on error
        if (comment) {
          comment.likes_count = Math.max(0, (comment.likes_count || 1) - 1)
        }
        console.error('Error liking comment:', errors)
      }
    })
  } catch (error) {
    console.error('Error liking comment:', error)
  }
}

const toggleReplyForm = (commentId) => {
  showReplyForm.value[commentId] = !showReplyForm.value[commentId]
  
  if (showReplyForm.value[commentId] && !replyForms.value[commentId]) {
    replyForms.value[commentId] = {
      content: '',
      author_name: '',
      author_email: ''
    }
  }
}

const submitReply = async (commentId) => {
  const replyData = replyForms.value[commentId]
  if (!replyData?.content.trim()) return
  
  try {
    router.post(route('comments.reply', commentId), {
      content: replyData.content,
      author_name: replyData.author_name,
      author_email: replyData.author_email
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Reset reply form and hide it
        replyForms.value[commentId] = {
          content: '',
          author_name: '',
          author_email: ''
        }
        showReplyForm.value[commentId] = false
      }
    })
  } catch (error) {
    console.error('Error submitting reply:', error)
  }
}

const cancelReply = (commentId) => {
  showReplyForm.value[commentId] = false
  replyForms.value[commentId] = {
    content: '',
    author_name: '',
    author_email: ''
  }
}

// Helper function to get initials from name
const getInitials = (name) => {
  if (!name) return 'A'
  return name
    .split(' ')
    .map(word => word.charAt(0).toUpperCase())
    .slice(0, 2)
    .join('')
}

const extractTableOfContents = () => {
  const container = document.querySelector('.prose')
  if (!container) return []
  const nodes = Array.from(container.querySelectorAll('h2, h3, h4'))
  return nodes.map((el) => { 
    if (!el.id) el.id = slugify(el.textContent)
    return { 
      id: el.id, 
      text: el.textContent, 
      level: Number(el.tagName.substring(1)) 
    } 
  })
}

const handleScroll = () => {
  const scrollTop = window.pageYOffset
  const docHeight = document.documentElement.scrollHeight - window.innerHeight
  const progress = (scrollTop / (docHeight || 1)) * 100
  readingProgress.value = Math.min(100, Math.max(0, progress))
}

// Lifecycle
onMounted(async () => {
  window.addEventListener('scroll', handleScroll)
  await nextTick()
  tableOfContents.value = extractTableOfContents()
})

onUnmounted(() => { 
  window.removeEventListener('scroll', handleScroll) 
})
</script>

<style scoped>
/* Typography tweaks */
.prose :deep(h2){ @apply text-2xl font-bold text-gray-900 dark:text-white mt-8 mb-4; }
.prose :deep(h3){ @apply text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-3; }
.prose :deep(p){ @apply text-gray-700 dark:text-gray-300 leading-relaxed mb-4; }
.prose :deep(img){ @apply rounded-lg shadow-lg my-6 mx-auto; }
.prose :deep(blockquote){ @apply border-l-4 border-blue-500 pl-4 italic text-gray-600 dark:text-gray-400 my-4; }
.prose :deep(code){ @apply bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-sm; }
.prose :deep(pre){ @apply bg-gray-900 text-white p-4 rounded-lg overflow-x-auto my-4; }
.prose :deep(ul), .prose :deep(ol){ @apply my-4 ml-6; }
.prose :deep(li){ @apply mb-2; }

/* Smooth scroll */
html{ scroll-behavior: smooth; }

/* Transition group for comments */
.list-enter-active, .list-leave-active { transition: all .25s ease; }
.list-enter-from { opacity: 0; transform: translateY(6px); }
.list-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
