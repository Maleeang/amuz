<template>
  <AppLayout>
    <Head :title="`주문 #${order.id} 상세`" />
    
    <div class="min-h-screen bg-gray-50">
      
      <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">주문 #{{ order.id }}</h1>
              <p class="text-lg text-gray-600 mt-1">{{ order.ordered_at }}</p>
            </div>
            
            <Link
              :href="'/orders'"
              class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-200"
            >
              ← 주문 목록
            </Link>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <div class="lg:col-span-2">
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
              <h2 class="text-xl font-semibold text-gray-900 mb-6">주문 상품</h2>
              
              <div class="space-y-6">
                <div
                  v-for="item in order.items"
                  :key="item.id"
                  class="flex items-start space-x-4 pb-6 border-b border-gray-200 last:border-b-0 last:pb-0"
                >
                  
                  <div class="flex-shrink-0">
                    <img
                      v-if="item.product.image_url"
                      :src="item.product.image_url"
                      :alt="item.product.name"
                      class="w-20 h-20 object-cover rounded-md"
                    />
                    <div
                      v-else
                      class="w-20 h-20 bg-gray-200 rounded-md flex items-center justify-center"
                    >
                      <span class="text-gray-400 text-xs">이미지 없음</span>
                    </div>
                  </div>

                  
                  <div class="flex-1">
                    <div class="flex justify-between items-start">
                      <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ item.product.name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ item.product.category_name }}</p>
                        
                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                          <span>단가: {{ item.formatted_price }}</span>
                          <span>수량: {{ item.quantity }}개</span>
                        </div>
                      </div>
                      
                      <div class="text-right">
                        <p class="text-lg font-semibold text-gray-900">{{ item.formatted_total }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            
            <div v-if="order.notes" class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">주문 메모</h2>
              <div class="bg-gray-50 rounded-md p-4">
                <p class="text-gray-700">{{ order.notes }}</p>
              </div>
            </div>
          </div>

          
          <div class="lg:col-span-1">
            
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">주문 요약</h2>
              
              <div class="space-y-4">
                
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">주문 상태</span>
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-sm font-medium',
                      getStatusColor(order.status)
                    ]"
                  >
                    {{ order.status_label }}
                  </span>
                </div>

                
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">주문 일시</span>
                  <span class="text-sm text-gray-900">{{ order.ordered_at }}</span>
                </div>

                <hr class="border-gray-200">

                
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">총 상품 수</span>
                  <span class="text-sm text-gray-900">{{ order.items.length }}종</span>
                </div>

                
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">총 수량</span>
                  <span class="text-sm text-gray-900">{{ totalQuantity }}개</span>
                </div>

                <hr class="border-gray-200">

                
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-gray-900">총 결제 금액</span>
                  <span class="text-lg font-bold text-blue-600">{{ order.formatted_amount }}</span>
                </div>
              </div>
            </div>

            
            <div class="bg-white rounded-lg shadow-sm p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">주문 관리</h2>
              
              <div class="space-y-3">
                
                <button
                  v-if="order.can_cancel"
                  @click="cancelOrder"
                  class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200"
                >
                  주문 취소
                </button>

                
                <div class="text-sm text-gray-600 bg-gray-50 rounded-md p-3">
                  <div v-if="order.status === 'pending'">
                    ⏳ 주문이 접수되었습니다. 곧 처리될 예정입니다.
                  </div>
                  <div v-else-if="order.status === 'paid'">
                    💳 결제가 완료되었습니다. 상품 준비 중입니다.
                  </div>
                  <div v-else-if="order.status === 'shipped'">
                    🚚 상품이 발송되었습니다. 곧 받아보실 수 있어요!
                  </div>
                  <div v-else-if="order.status === 'delivered'">
                    ✅ 배송이 완료되었습니다. 상품을 확인해주세요.
                  </div>
                  <div v-else-if="order.status === 'cancelled'">
                    ❌ 주문이 취소되었습니다.
                  </div>
                </div>

                
                <Link
                  :href="'/'"
                  class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200"
                >
                  계속 쇼핑하기
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  order: Object,
})

const totalQuantity = computed(() => {
  return props.order.items.reduce((total, item) => total + item.quantity, 0)
})

const getStatusColor = (status) => {
  const colors = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'paid': 'bg-blue-100 text-blue-800', 
    'shipped': 'bg-purple-100 text-purple-800',
    'delivered': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const cancelOrder = async () => {
  if (!confirm('정말로 이 주문을 취소하시겠습니까? 취소된 주문은 복구할 수 없습니다.')) {
    return
  }

  try {
    const response = await axios.post(`/orders/${props.order.id}/cancel`)
    
    if (response.data.success) {
      alert('주문이 취소되었습니다.')
      router.visit('/orders')
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