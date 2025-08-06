<template>
  <AppLayout>
    <Head :title="product.name" />
    
    <div class="min-h-screen bg-gradient-to-br from-elegant-50 via-white to-warm-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="mb-6">
          <button
            @click="goBack"
            class="inline-flex items-center text-sm text-elegant-600 hover:text-elegant-800 font-serif transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            상품 목록
          </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
          
          <div class="aspect-w-1 aspect-h-1">
            <img
              v-if="product.image_url"
              :src="product.image_url"
              :alt="product.name"
              class="w-full h-96 object-cover rounded-2xl shadow-xl border border-elegant-200"
            />
            <div
              v-else
              class="w-full h-96 bg-gradient-to-br from-elegant-100 to-elegant-200 rounded-2xl shadow-xl border border-elegant-200 flex items-center justify-center"
            >
              <div class="text-center">
                <svg class="w-16 h-16 text-elegant-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-elegant-500 text-lg font-serif">이미지 없음</span>
              </div>
            </div>
          </div>

          
          <div class="flex flex-col justify-between">
            <div>
              
              <div class="mb-6">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-elegant-500 to-elegant-600 text-white shadow-md">
                  {{ product.category.name }}
                </span>
              </div>

              
              <h1 class="text-4xl font-elegant font-bold text-elegant-800 mb-6 leading-tight">{{ product.name }}</h1>

              
              <div class="mb-8">
                <span class="text-4xl font-elegant font-bold text-elegant-800">{{ product.formatted_price }}</span>
              </div>

              
              <div class="mb-8">
                <div class="flex items-center space-x-3">
                  <span class="text-sm font-medium text-elegant-700 font-serif">재고:</span>
                  <span
                    :class="[
                      'text-sm font-medium font-serif',
                      product.stock_quantity > 10 ? 'text-warm-600' : 
                      product.stock_quantity > 0 ? 'text-warm-500' : 'text-elegant-500'
                    ]"
                  >
                    {{ product.stock_quantity }}개
                  </span>
                  <span v-if="product.stock_quantity <= 5 && product.stock_quantity > 0" class="text-xs text-warm-600 font-serif">
                    (재고 부족)
                  </span>
                  <span v-else-if="product.stock_quantity === 0" class="text-xs text-elegant-500 font-serif">
                    (품절)
                  </span>
                  <div v-if="product.stock_quantity > 0" class="w-3 h-3 bg-warm-400 rounded-full animate-pulse"></div>
                </div>
              </div>

              
              <div class="mb-8">
                <h3 class="text-lg font-medium text-elegant-800 mb-4 font-serif">상품 설명</h3>
                <p class="text-elegant-600 leading-relaxed font-serif">{{ product.description }}</p>
              </div>
            </div>

            
            <div class="space-y-4">
              <button
                v-if="$page.props.auth.user && product.in_stock"
                @click="openOrderModal"
                class="w-full bg-gradient-to-r from-warm-500 to-warm-600 text-white py-4 px-6 rounded-xl font-medium hover:from-warm-600 hover:to-warm-700 shadow-lg hover:shadow-xl transition-all duration-300 text-lg"
              >
                🛒 주문하기
              </button>

              <button
                v-else-if="$page.props.auth.user && !product.in_stock"
                disabled
                class="w-full bg-gradient-to-r from-elegant-300 to-elegant-400 text-white py-4 px-6 rounded-xl font-medium cursor-not-allowed text-lg"
              >
                😞 품절
              </button>

              <Link
                v-else
                :href="'/login'"
                class="w-full block text-center bg-gradient-to-r from-elegant-500 to-elegant-600 text-white py-4 px-6 rounded-xl font-medium hover:from-elegant-600 hover:to-elegant-700 shadow-lg hover:shadow-xl transition-all duration-300 text-lg"
              >
                로그인 후 주문하기
              </Link>
            </div>
          </div>
        </div>

        
        <div v-if="relatedProducts.length > 0" class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 p-8">
          <h2 class="text-3xl font-elegant font-bold text-elegant-800 mb-8">관련 상품</h2>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
              v-for="relatedProduct in relatedProducts"
              :key="relatedProduct.id"
              class="group cursor-pointer bg-white rounded-xl shadow-md border border-elegant-200 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300"
              @click="goToProduct(relatedProduct.id)"
            >
              <div class="aspect-w-1 aspect-h-1 bg-gradient-to-br from-elegant-100 to-elegant-200 overflow-hidden">
                <img
                  v-if="relatedProduct.image_url"
                  :src="relatedProduct.image_url"
                  :alt="relatedProduct.name"
                  class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500"
                />
                <div
                  v-else
                  class="w-full h-48 bg-gradient-to-br from-elegant-100 to-elegant-200 flex items-center justify-center"
                >
                  <div class="text-center">
                    <svg class="w-8 h-8 text-elegant-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-elegant-500 text-xs font-serif">이미지 없음</span>
                  </div>
                </div>
              </div>
              
              <div class="p-4">
                <h3 class="text-sm font-medium text-elegant-800 group-hover:text-elegant-600 transition-colors duration-200 font-serif line-clamp-2">
                  {{ relatedProduct.name }}
                </h3>
                <p class="text-sm font-bold text-elegant-700 mt-2 font-elegant">{{ relatedProduct.formatted_price }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <OrderModal
      v-if="showOrderModal"
      :product="product"
      @close="closeOrderModal"
      @order-success="handleOrderSuccess"
    />
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import OrderModal from '@/Components/OrderModal.vue'

const props = defineProps({
  product: Object,
  relatedProducts: Array,
})

const showOrderModal = ref(false)

const openOrderModal = () => {
  showOrderModal.value = true
}

const closeOrderModal = () => {
  showOrderModal.value = false
}

const handleOrderSuccess = (order) => {
  closeOrderModal()
  router.visit(`/orders/${order.order_id}`)
}

const goToProduct = (productId) => {
  router.visit(`/products/${productId}`)
}

const goBack = () => {
  router.visit('/')
}
</script>