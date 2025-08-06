<template>
  <AppLayout>
    <Head title="내 주문" />
    
    <div class="min-h-screen bg-gradient-to-br from-elegant-50 via-white to-warm-50">
      
      <div class="bg-elegant-600 border-b border-elegant-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div class="text-center">
            <h1 class="text-4xl font-elegant font-bold text-white mb-4">내 주문</h1>
            <p class="text-xl text-white/90 font-serif">주문 내역을 확인하세요</p>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 p-8 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-3 font-serif">주문 상태</label>
              <select
                v-model="filterForm.status"
                @change="applyFilters"
                class="w-full px-4 py-3 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              >
                <option v-for="status in statuses" :key="status.value" :value="status.value">
                  {{ status.label }}
                </option>
              </select>
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-3 font-serif">카테고리</label>
              <select
                v-model="filterForm.category_id"
                @change="applyFilters"
                class="w-full px-4 py-3 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              >
                <option value="">전체 카테고리</option>
                <option v-for="category in categories" :key="category.value" :value="category.value">
                  {{ category.label }}
                </option>
              </select>
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-3 font-serif">시작 날짜</label>
              <input
                v-model="filterForm.start_date"
                @change="applyFilters"
                type="date"
                class="w-full px-4 py-3 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              />
            </div>

            
            <div>
              <label class="block text-sm font-medium text-elegant-700 mb-3 font-serif">종료 날짜</label>
              <input
                v-model="filterForm.end_date"
                @change="applyFilters"
                type="date"
                class="w-full px-4 py-3 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm"
              />
            </div>
          </div>

          <div class="flex justify-end mt-6 pt-6 border-t border-elegant-200">
            <button
              @click="resetFilters"
              class="px-6 py-3 bg-gradient-to-r from-warm-500 to-warm-600 text-white rounded-xl hover:from-warm-600 hover:to-warm-700 shadow-md hover:shadow-lg transition-all duration-200 font-medium"
            >
              필터 초기화
            </button>
          </div>
        </div>

        
        <div v-if="orders.data.length > 0" class="space-y-6 mb-8">
          <div
            v-for="order in orders.data"
            :key="order.id"
            class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 p-8 hover:shadow-xl transition-all duration-300"
          >
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
              <div class="mb-4 sm:mb-0">
                <h3 class="text-xl font-elegant font-semibold text-elegant-800">
                  주문 #{{ order.id }}
                </h3>
                <p class="text-sm text-elegant-600 font-serif mt-1">{{ order.ordered_at }}</p>
              </div>
              
              <div class="flex items-center space-x-4">
                <span
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-medium shadow-sm',
                    getStatusColor(order.status)
                  ]"
                >
                  {{ order.status_label }}
                </span>
                
                <span class="text-2xl font-elegant font-bold text-elegant-800">
                  {{ order.formatted_amount }}
                </span>
              </div>
            </div>

            
            <div class="border-t border-elegant-200 pt-6">
              <h4 class="text-sm font-medium text-elegant-700 mb-4 font-serif">주문 상품 ({{ order.items.length }}종)</h4>
              <div class="space-y-4">
                <div
                  v-for="item in order.items"
                  :key="item.product_name"
                  class="bg-gradient-to-br from-elegant-50 to-warm-50 rounded-xl p-4 border border-elegant-200"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center space-x-3 mb-2">
                        <h4 class="text-sm font-medium text-elegant-800 font-elegant">{{ item.product_name }}</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-elegant-500 to-elegant-600 text-white shadow-sm">
                          {{ item.category_name }}
                        </span>
                      </div>
                      
                      <p v-if="item.product_description" class="text-xs text-elegant-600 mb-3 font-serif line-clamp-2">
                        {{ item.product_description }}
                      </p>
                      
                      <div class="flex items-center space-x-4 text-sm text-elegant-600 font-serif">
                        <span>{{ item.formatted_price }} × {{ item.quantity }}개</span>
                        <span class="text-xs">재고: {{ item.stock }}개</span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium text-elegant-800 font-elegant">
                        {{ item.formatted_total }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              
              <div class="mt-6 pt-4 border-t border-elegant-200">
                <div class="grid grid-cols-2 gap-6 text-center">
                  <div>
                    <div class="text-xs text-elegant-500 font-serif">총 상품</div>
                    <div class="text-sm font-medium text-elegant-800 font-elegant">{{ order.items.length }}종</div>
                  </div>
                  <div>
                    <div class="text-xs text-elegant-500 font-serif">총 수량</div>
                    <div class="text-sm font-medium text-elegant-800 font-elegant">
                      {{ order.items.reduce((sum, item) => sum + item.quantity, 0) }}개
                    </div>
                  </div>
                </div>
              </div>

              
              <div v-if="order.notes" class="mt-4 pt-4 border-t border-elegant-200">
                <p class="text-sm text-elegant-600 font-serif">
                  <span class="font-medium">📝 주문 메모:</span> {{ order.notes }}
                </p>
              </div>
            </div>

            
            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-elegant-200">
              <Link
                :href="`/orders/${order.id}`"
                class="px-6 py-3 bg-gradient-to-r from-elegant-500 to-elegant-600 text-white rounded-xl hover:from-elegant-600 hover:to-elegant-700 shadow-md hover:shadow-lg transition-all duration-200 font-medium"
              >
                상세보기
              </Link>
              
              <button
                v-if="order.can_cancel"
                @click="cancelOrder(order.id)"
                class="px-6 py-3 bg-gradient-to-r from-warm-500 to-warm-600 text-white rounded-xl hover:from-warm-600 hover:to-warm-700 shadow-md hover:shadow-lg transition-all duration-200 font-medium"
              >
                주문취소
              </button>
            </div>
          </div>
        </div>

        
        <div v-else class="text-center py-12">
          <div class="text-elegant-400 text-6xl mb-4">주문</div>
          <h3 class="text-lg font-medium text-elegant-800 mb-2 font-elegant">주문 내역이 없습니다</h3>
          <Link
            :href="'/'"
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-elegant-500 to-elegant-600 text-white rounded-xl hover:from-elegant-600 hover:to-elegant-700 shadow-md hover:shadow-lg transition-all duration-200 font-medium"
          >
            상품 둘러보기
          </Link>
        </div>

        
        <div v-if="orders.links && orders.links.length > 3" class="flex justify-center">
          <nav class="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px" aria-label="Pagination">
            <template v-for="link in orders.links" :key="link.label">
              
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
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  orders: Object,
  filters: Object,
  statuses: Array,
  categories: Array,
})

const filterForm = reactive({
  status: props.filters.status || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  category_id: props.filters.category_id || '',
})

const applyFilters = () => {
  router.get('/orders', filterForm, {
    preserveState: true,
    preserveScroll: true,
  })
}

const resetFilters = () => {
  Object.keys(filterForm).forEach(key => {
    filterForm[key] = ''
  })
  applyFilters()
}

const getStatusColor = (status) => {
  const colors = {
    'pending': 'bg-gradient-to-r from-warm-100 to-warm-200 text-warm-800 border border-warm-300',
    'paid': 'bg-gradient-to-r from-elegant-100 to-elegant-200 text-elegant-800 border border-elegant-300', 
    'shipped': 'bg-gradient-to-r from-soft-100 to-soft-200 text-soft-800 border border-soft-300',
    'delivered': 'bg-gradient-to-r from-warm-100 to-warm-200 text-warm-800 border border-warm-300',
    'cancelled': 'bg-gradient-to-r from-elegant-100 to-elegant-200 text-elegant-600 border border-elegant-300',
  }
  return colors[status] || 'bg-gradient-to-r from-elegant-100 to-elegant-200 text-elegant-800 border border-elegant-300'
}

const cancelOrder = async (orderId) => {
  if (!confirm('정말로 이 주문을 취소하시겠습니까?')) {
    return
  }

  try {
    const response = await axios.post(`/orders/${orderId}/cancel`)
    
    if (response.data.success) {
      router.reload()
    } else {
      alert(response.data.message || '주문 취소에 실패했습니다.')
    }
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      alert(error.response.data.message)
    } else {
      alert('주문 취소 중 오류가 발생했습니다.')
    }
  }
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