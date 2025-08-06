<template>
  <AppLayout>
    <Head title="상품 목록" />
    
    <div class="min-h-screen bg-gradient-to-br from-elegant-50 via-white to-warm-50">
      
      <div class="relative bg-elegant-600 border-b border-elegant-200 overflow-hidden">
        
        <div class="absolute inset-0">
          
          <div class="absolute top-10 left-10 w-32 h-32 border-2 border-white/30 rounded-full"></div>
          <div class="absolute top-20 right-20 w-24 h-24 border-2 border-white/30 rounded-full"></div>
          <div class="absolute bottom-10 left-1/4 w-20 h-20 border-2 border-white/30 rounded-full"></div>
          
          
          <div class="absolute top-32 left-1/3 w-8 h-8 bg-white/40 rounded-full"></div>
          <div class="absolute top-40 right-1/4 w-6 h-6 bg-white/40 rounded-full"></div>
          <div class="absolute bottom-20 right-1/3 w-10 h-10 bg-white/40 rounded-full"></div>
          
          
          <div class="absolute top-1/4 left-0 w-16 h-px bg-gradient-to-r from-transparent via-white/50 to-transparent"></div>
          <div class="absolute bottom-1/4 right-0 w-20 h-px bg-gradient-to-l from-transparent via-white/50 to-transparent"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
          <div class="text-center">
            
            <div class="inline-block mb-6">
              <span class="text-sm font-serif text-white/90 tracking-widest uppercase bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full border border-white/30">
                아뮤즈 입사 과제 프로젝트
              </span>
            </div>
            
            
            <h1 class="text-5xl md:text-7xl font-elegant font-bold text-white mb-6 leading-tight">
              아뮤즈 상품관리시스템
            </h1>
            
            
            <p class="text-xl md:text-2xl text-white/90 font-serif max-w-3xl mx-auto leading-relaxed mb-8">
              "Laravel + Vue3 + Inertia.js + Orchid"
            </p>
            
            
            <div class="flex items-center justify-center space-x-4 mb-8">
              <div class="w-12 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent"></div>
              <div class="w-3 h-3 bg-white/60 rounded-full"></div>
              <div class="w-12 h-px bg-gradient-to-l from-transparent via-white/60 to-transparent"></div>
            </div>
            
            
            <p class="text-sm text-white/80 font-serif max-w-2xl mx-auto">
              개발기간 2025.07.31 ~ 2025.08.06
            </p>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-xl shadow-lg border border-elegant-200 p-6 md:p-8 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 md:gap-6">
            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-2 font-serif">검색</label>
              <div class="relative">
                <input
                  v-model="searchForm.search"
                  @input="debouncedSearch"
                  @keyup.enter="applyFilters"
                  type="text"
                  placeholder="상품을 검색하세요..."
                  class="w-full px-4 py-3 pl-10 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
                />
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-elegant-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-2 font-serif">카테고리</label>
              <select
                v-model="searchForm.category_id"
                @change="applyFilters"
                class="w-full px-3 py-2 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              >
                <option value="">전체 카테고리</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }} ({{ category.products_count }})
                </option>
              </select>
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-2 font-serif">최소 가격</label>
              <input
                v-model="searchForm.min_price"
                @input="debouncedSearch"
                type="number"
                min="0"
                placeholder="0"
                class="w-full px-3 py-2 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-2 font-serif">최대 가격</label>
              <input
                v-model="searchForm.max_price"
                @input="debouncedSearch"
                type="number"
                min="0"
                placeholder="무제한"
                class="w-full px-3 py-2 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              />
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-2 font-serif">등록일 (시작)</label>
              <input
                v-model="searchForm.start_date"
                @change="debouncedSearch"
                type="date"
                class="w-full px-3 py-2 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              />
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-2 font-serif">등록일 (종료)</label>
              <input
                v-model="searchForm.end_date"
                @change="debouncedSearch"
                type="date"
                class="w-full px-3 py-2 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              />
            </div>
          </div>

          
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 pt-4 border-t border-elegant-200">
            <div class="flex items-center space-x-2 md:space-x-4 mb-4 sm:mb-0">
              <label class="text-xs md:text-sm font-medium text-elegant-700 font-serif">정렬:</label>
              <select
                v-model="searchForm.sort_by"
                @change="applyFilters"
                class="px-2 md:px-3 py-1 text-xs md:text-sm border border-elegant-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-elegant-500 bg-white/80 backdrop-blur-sm"
              >
                <option value="created_at">최신순</option>
                <option value="name">이름순</option>
                <option value="price">가격순</option>
              </select>

              <select
                v-model="searchForm.sort_order"
                @change="applyFilters"
                class="px-2 md:px-3 py-1 text-xs md:text-sm border border-elegant-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-elegant-500 bg-white/80 backdrop-blur-sm"
              >
                <option value="desc">내림차순</option>
                <option value="asc">오름차순</option>
              </select>
            </div>

            <button
              @click="resetFilters"
              class="px-4 md:px-6 py-2 bg-gradient-to-r from-warm-500 to-warm-600 text-white rounded-xl hover:from-warm-600 hover:to-warm-700 shadow-md hover:shadow-lg transition-all duration-200 flex items-center space-x-2 text-sm md:text-base font-medium"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span class="hidden sm:inline">필터 초기화</span>
              <span class="sm:hidden">초기화</span>
            </button>
          </div>
        </div>

        
        <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
          <div
            v-for="product in products.data"
            :key="product.id"
            class="group relative bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-500"
          >
            
            <div class="relative overflow-hidden">
              <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700"
              />
              <div
                v-else
                class="w-full h-64 bg-gradient-to-br from-elegant-100 to-elegant-200 flex items-center justify-center"
              >
                <div class="text-center">
                  <svg class="w-12 h-12 text-elegant-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span class="text-elegant-500 text-sm font-serif">이미지 없음</span>
                </div>
              </div>
              
              
              <div class="absolute inset-0 bg-gradient-to-t from-elegant-800/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              
              
              <div class="absolute top-4 left-4">
                <span class="inline-block px-3 py-1 bg-gradient-to-r from-elegant-500 to-elegant-600 text-white text-xs font-medium rounded-full border border-elegant-400 shadow-sm">
                  {{ product.first_category?.name || '미분류' }}
                </span>
              </div>
            </div>

            
            <div class="p-6">
              <div class="mb-4">
                <h3 class="text-xl font-elegant font-semibold text-elegant-800 line-clamp-2 mb-2 leading-tight">{{ product.name }}</h3>
                <p class="text-sm text-elegant-600 font-serif line-clamp-2 leading-relaxed">{{ product.description }}</p>
              </div>
              
              <div class="flex items-center justify-between mb-4">
                <div>
                  <span class="text-2xl font-elegant font-bold text-elegant-800">{{ product.formatted_price }}</span>
                  <div class="text-xs text-elegant-500 font-serif mt-1">재고: {{ product.stock_quantity }}개</div>
                </div>
                <div class="text-right">
                  <div class="w-3 h-3 bg-warm-400 rounded-full animate-pulse"></div>
                  <div class="text-xs text-elegant-500 font-serif mt-1">재고 있음</div>
                </div>
              </div>
              
              <div class="flex space-x-2">
                <Link
                  :href="`/products/${product.id}`"
                  class="flex-1 px-4 py-2 bg-gradient-to-r from-elegant-500 to-elegant-600 text-white text-sm rounded-xl hover:from-elegant-600 hover:to-elegant-700 shadow-md hover:shadow-lg transition-all duration-300 text-center font-medium"
                >
                  상세보기
                </Link>
                
                <button
                  v-if="$page.props.auth.user"
                  @click="openOrderModal(product)"
                  :disabled="product.stock_quantity <= 0"
                  class="flex-1 px-4 py-2 bg-gradient-to-r from-warm-500 to-warm-600 text-white text-sm rounded-xl hover:from-warm-600 hover:to-warm-700 shadow-md hover:shadow-lg disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed transition-all duration-300 font-medium"
                >
                  {{ product.stock_quantity > 0 ? '주문하기' : '품절' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        
        <div v-else class="text-center py-12">
          <div class="text-elegant-400 text-lg mb-4"></div>
          <h3 class="text-lg font-medium text-elegant-800 mb-2">상품을 찾을 수 없습니다</h3>
          <p class="text-elegant-600 font-serif">다른 검색어나 필터를 시도해보세요.</p>
        </div>

        
        <div v-if="products.links && products.links.length > 3" class="flex justify-center">
          <nav class="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px" aria-label="Pagination">
            <template v-for="link in products.links" :key="link.label">
              
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-all duration-200',
                  link.active
                    ? 'z-10 bg-elegant-50 border-elegant-500 text-elegant-600'
                    : 'bg-white border-elegant-300 text-elegant-500 hover:bg-elegant-50 hover:text-elegant-700'
                ]"
                v-html="link.label"
              />
              
              <span
                v-else
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  'bg-white border-elegant-300 text-elegant-500 cursor-not-allowed opacity-50'
                ]"
                v-html="link.label"
              />
            </template>
          </nav>
        </div>
      </div>
    </div>

    
    <OrderModal
      v-if="showOrderModal"
      :product="selectedProduct"
      @close="closeOrderModal"
      @order-success="handleOrderSuccess"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/Layouts/AppLayout.vue'
import OrderModal from '@/Components/OrderModal.vue'

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
})

const searchForm = reactive({
  search: props.filters.search || '',
  category_id: props.filters.category_id || '',
  min_price: props.filters.min_price || '',
  max_price: props.filters.max_price || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  sort_by: props.filters.sort_by || 'created_at',
  sort_order: props.filters.sort_order || 'desc',
})

const showOrderModal = ref(false)
const selectedProduct = ref(null)

const applyFilters = () => {
  router.get('/products', searchForm, {
    preserveState: true,
    preserveScroll: true,
  })
}

const debouncedSearch = debounce(() => {
  applyFilters()
}, 500)

const resetFilters = () => {
  Object.keys(searchForm).forEach(key => {
    if (key === 'sort_by') {
      searchForm[key] = 'created_at'
    } else if (key === 'sort_order') {
      searchForm[key] = 'desc'
    } else {
      searchForm[key] = ''
    }
  })
  applyFilters()
}

const openOrderModal = (product) => {
  selectedProduct.value = product
  showOrderModal.value = true
}

const closeOrderModal = () => {
  showOrderModal.value = false
  selectedProduct.value = null
}

const handleOrderSuccess = (order) => {
  closeOrderModal()
  router.visit(`/orders/${order.order_id}`)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>