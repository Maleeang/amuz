<template>
  <div class="min-h-screen bg-gradient-to-br from-sage-50 via-cream-50 to-terracotta-50">
    
         <nav class="bg-gradient-to-r from-elegant-100 via-white to-warm-100 shadow-lg border-b border-elegant-200 backdrop-blur-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
          
          <div class="flex items-center">
            <Link :href="'/'" class="flex items-center group">
              <div class="relative">
                                                  <h1 class="text-2xl font-elegant font-bold text-elegant-800 group-hover:text-elegant-600 transition-colors duration-300">
                   아뮤즈
                 </h1>
                 <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-elegant-400 to-warm-400 group-hover:w-full transition-all duration-300"></div>
              </div>
            </Link>

            <div class="hidden sm:ml-12 sm:flex sm:space-x-8">
              <Link
                :href="'/'"
                                 :class="[
                   'inline-flex items-center px-4 py-2 rounded-lg text-sm font-serif font-medium transition-all duration-300 relative group',
                   $page.url === '/' || $page.url.startsWith('/products')
                     ? 'bg-gradient-to-r from-elegant-600 to-elegant-700 text-white shadow-md'
                     : 'text-elegant-600 hover:text-elegant-800 hover:bg-elegant-100'
                 ]"
              >
                                 <span class="relative z-10">상품 목록</span>
                                 <div v-if="$page.url === '/' || $page.url.startsWith('/products')" class="absolute inset-0 bg-gradient-to-r from-elegant-700 to-elegant-800 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </Link>

                             <Link
                 v-if="$page.props.auth.user"
                 :href="'/orders'"
                 :class="[
                   'inline-flex items-center px-4 py-2 rounded-lg text-sm font-serif font-medium transition-all duration-300 relative group',
                   $page.url.startsWith('/orders')
                     ? 'bg-gradient-to-r from-warm-600 to-warm-700 text-white shadow-md'
                     : 'text-elegant-600 hover:text-elegant-800 hover:bg-elegant-100'
                 ]"
              >
                                 <span class="relative z-10">내 주문</span>
                                 <div v-if="$page.url.startsWith('/orders')" class="absolute inset-0 bg-gradient-to-r from-warm-700 to-warm-800 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </Link>

                             <Link
                 v-if="$page.props.auth.user"
                 :href="'/dashboard'"
                 :class="[
                   'inline-flex items-center px-4 py-2 rounded-lg text-sm font-serif font-medium transition-all duration-300 relative group',
                   $page.url === '/dashboard'
                     ? 'bg-gradient-to-r from-soft-600 to-soft-700 text-white shadow-md'
                     : 'text-elegant-600 hover:text-elegant-800 hover:bg-elegant-100'
                 ]"
              >
                                 <span class="relative z-10">내 정보</span>
                                 <div v-if="$page.url === '/dashboard'" class="absolute inset-0 bg-gradient-to-r from-soft-700 to-soft-800 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </Link>
            </div>
          </div>

          
          <div class="flex items-center space-x-6">
            
            <div v-if="!$page.props.auth.user" class="flex items-center space-x-4">
                             <Link
                 :href="'/login'"
                 class="bg-gradient-to-r from-warm-600 to-warm-700 text-white px-6 py-2 rounded-xl text-sm font-serif font-medium hover:from-warm-700 hover:to-warm-800 shadow-md hover:shadow-lg transition-all duration-300"
               >
                 로그인
               </Link>
            </div>

            
            <div v-else class="flex items-center space-x-4">
              <div class="text-right">
                                 <span class="text-sm text-elegant-600 font-serif">
                   안녕하세요, <strong class="text-elegant-800">{{ $page.props.auth.user.name }}</strong>님
                 </span>
              </div>

              
              <a
                 v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.includes('admin')"
                 href="/admin"
                 class="bg-gradient-to-r from-warm-600 to-warm-700 text-white px-4 py-2 rounded-xl text-sm font-serif font-medium hover:from-warm-700 hover:to-warm-800 shadow-md hover:shadow-lg transition-all duration-300"
               >
                 관리자
               </a>

              
                             <button
                 @click="logout"
                 class="text-elegant-600 hover:text-elegant-800 px-4 py-2 rounded-xl text-sm font-serif font-medium hover:bg-elegant-100 transition-all duration-300"
               >
                 로그아웃
               </button>
            </div>

            
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            >
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                  :class="{ 'hidden': showMobileMenu, 'inline-flex': !showMobileMenu }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{ 'hidden': !showMobileMenu, 'inline-flex': showMobileMenu }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      
             <div v-show="showMobileMenu" class="sm:hidden border-t border-purple-200 bg-gradient-to-br from-white via-purple-50 to-red-50">
        <div class="pt-4 pb-6 space-y-2 px-4">
          <Link
            :href="'/'"
            :class="[
              'block px-4 py-3 rounded-xl text-base font-serif font-medium transition-all duration-300',
                             $page.url === '/' || $page.url.startsWith('/products')
                 ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md'
                 : 'text-purple-600 hover:text-purple-800 hover:bg-purple-100'
            ]"
          >
                         상품 목록
          </Link>

          <Link
            v-if="$page.props.auth.user"
            :href="'/orders'"
            :class="[
              'block px-4 py-3 rounded-xl text-base font-serif font-medium transition-all duration-300',
                             $page.url.startsWith('/orders')
                 ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md'
                 : 'text-purple-600 hover:text-purple-800 hover:bg-purple-100'
            ]"
          >
                         내 주문
          </Link>

          <Link
            v-if="$page.props.auth.user"
            :href="'/dashboard'"
            :class="[
              'block px-4 py-3 rounded-xl text-base font-serif font-medium transition-all duration-300',
                             $page.url === '/dashboard'
                 ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md'
                 : 'text-purple-600 hover:text-purple-800 hover:bg-purple-100'
            ]"
          >
                         대시보드
          </Link>
        </div>

        
                 <div class="pt-6 pb-6 border-t border-purple-200 bg-white/50 backdrop-blur-sm">
          <div v-if="!$page.props.auth.user" class="px-4">
            <Link
              :href="'/login'"
                             class="block w-full text-center px-6 py-3 text-base font-serif font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-300"
            >
                             로그인
            </Link>
          </div>
          
          <div v-else class="px-4">
                         <div class="text-base font-serif font-medium text-purple-800">{{ $page.props.auth.user.name }}</div>
             <div class="text-sm font-serif text-purple-500">{{ $page.props.auth.user.email }}</div>
            
            <div class="mt-4 space-y-2">
              <a
                v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.includes('admin')"
                href="/admin"
                                 class="block w-full text-center px-4 py-3 text-base font-serif font-medium bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transition-all duration-300"
              >
                                 관리자
              </a>
              
              <button
                @click="logout"
                                 class="block w-full text-center px-4 py-3 text-base font-serif font-medium text-purple-600 hover:text-purple-800 hover:bg-purple-100 rounded-xl transition-all duration-300"
              >
                                 로그아웃
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    
    <main>
      <slot />
    </main>

    
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="text-center text-sm text-gray-500">
          <p>&copy; 2025 아뮤즈 상품관리시스템. All rights reserved.</p>
          <p class="mt-1">Laravel + Vue3 + Inertia.js + Orchid | 아뮤즈 입사 과제 프로젝트</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const showMobileMenu = ref(false)

const logout = () => {
  router.post('/logout')
}
</script>