<template>
  <Teleport to="body">
    
    <div
      class="fixed inset-0 overflow-y-auto h-full w-full bg-black/20 backdrop-blur-sm"
      style="z-index: 9999;"
      @click="$emit('close')"
    >
      
      <div
        class="relative top-20 mx-auto p-8 w-11/12 md:w-2/3 lg:w-1/2 xl:w-1/3 shadow-2xl rounded-2xl bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm border border-elegant-200 transform transition-all duration-300 ease-out"
        @click.stop
      >
        
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-2xl font-elegant font-bold text-elegant-800">주문하기</h3>
            <p class="text-sm text-elegant-600 font-serif mt-1">상품을 주문하세요</p>
          </div>
          <button
            @click="$emit('close')"
            class="text-elegant-400 hover:text-elegant-600 transition-colors duration-200 p-2 rounded-full hover:bg-elegant-100"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="mb-6">
          <div class="flex items-start space-x-4">
            
            <div class="flex-shrink-0">
              <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="w-20 h-20 object-cover rounded-xl border border-elegant-200"
              />
              <div
                v-else
                class="w-20 h-20 bg-gradient-to-br from-elegant-100 to-elegant-200 rounded-xl border border-elegant-200 flex items-center justify-center"
              >
                <div class="text-center">
                  <svg class="w-6 h-6 text-elegant-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span class="text-elegant-500 text-xs font-serif">이미지 없음</span>
                </div>
              </div>
            </div>


            <div class="flex-1">
              <h4 class="text-lg font-semibold text-elegant-800 font-elegant">{{ product.name }}</h4>
              <p class="text-sm text-elegant-600 mt-1 font-serif">{{ product.description }}</p>
              <div class="mt-2">
                <span class="text-sm bg-gradient-to-r from-elegant-500 to-elegant-600 text-white px-3 py-1 rounded-full font-medium">
                  {{ product.category.name }}
                </span>
              </div>
              <div class="mt-2 flex items-center justify-between">
                <span class="text-xl font-bold text-elegant-800 font-elegant">{{ product.formatted_price }}</span>
                <span class="text-sm text-elegant-500 font-serif">재고: {{ product.stock_quantity }}개</span>
              </div>
            </div>
          </div>
        </div>


        <form @submit.prevent="submitOrder">

          <div class="mb-6">
            <label class="block text-sm font-medium text-elegant-700 mb-3 font-serif">수량</label>
            <div class="flex items-center space-x-4">
              <button
                type="button"
                @click="decreaseQuantity"
                :disabled="quantity <= 1"
                class="w-10 h-10 rounded-full border border-elegant-300 flex items-center justify-center hover:bg-elegant-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 text-elegant-600 hover:text-elegant-800"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
              </button>
              
              <input
                v-model.number="quantity"
                type="number"
                min="1"
                :max="product.stock_quantity"
                class="w-24 text-center px-4 py-2 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm font-medium"
              />
              
              <button
                type="button"
                @click="increaseQuantity"
                :disabled="quantity >= product.stock_quantity"
                class="w-10 h-10 rounded-full border border-elegant-300 flex items-center justify-center hover:bg-elegant-100 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 text-elegant-600 hover:text-elegant-800"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </button>
            </div>
            
            <p class="text-xs text-elegant-500 mt-2 font-serif">
              최대 {{ product.stock_quantity }}개까지 주문 가능합니다.
            </p>
          </div>


          <div class="mb-6 p-4 bg-gradient-to-br from-elegant-50 to-warm-50 rounded-xl border border-elegant-200">
            <div class="flex justify-between items-center">
              <span class="text-sm text-elegant-600 font-serif">단가:</span>
              <span class="text-sm text-elegant-800 font-medium">{{ product.formatted_price }}</span>
            </div>
            <div class="flex justify-between items-center mt-2">
              <span class="text-sm text-elegant-600 font-serif">수량:</span>
              <span class="text-sm text-elegant-800 font-medium">{{ quantity }}개</span>
            </div>
            <hr class="my-3 border-elegant-200">
            <div class="flex justify-between items-center">
              <span class="text-lg font-semibold text-elegant-800 font-elegant">총 가격:</span>
              <span class="text-lg font-bold text-warm-600 font-elegant">{{ formatPrice(totalAmount) }}</span>
            </div>
          </div>


          <div class="mb-6">
            <label class="block text-sm font-medium text-elegant-700 mb-3 font-serif">주문 메모 (선택사항)</label>
            <textarea
              v-model="notes"
              rows="3"
              placeholder="요청사항이 있으시면 적어주세요"
              class="w-full px-4 py-3 border border-elegant-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elegant-500 focus:border-elegant-500 bg-white/80 backdrop-blur-sm font-serif"
            ></textarea>
          </div>


          <div v-if="error" class="mb-6 p-4 bg-gradient-to-r from-warm-50 to-warm-100 border border-warm-200 rounded-xl">
            <p class="text-sm text-warm-700 font-serif">{{ error }}</p>
          </div>


          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="$emit('close')"
              class="px-6 py-3 border border-elegant-300 rounded-xl text-sm font-medium text-elegant-700 hover:bg-elegant-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-elegant-500 transition-all duration-200 font-serif"
            >
              취소
            </button>
            
            <button
              type="submit"
              :disabled="loading || quantity <= 0 || quantity > product.stock_quantity"
              class="px-6 py-3 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-warm-500 to-warm-600 hover:from-warm-600 hover:to-warm-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warm-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 font-medium"
            >
              <span v-if="loading">처리중...</span>
              <span v-else>주문하기</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'order-success'])

const quantity = ref(1)
const notes = ref('')
const loading = ref(false)
const error = ref('')

const totalAmount = computed(() => {
  return props.product.price * quantity.value
})

onMounted(() => {
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.body.style.overflow = 'unset'
})

watch(quantity, (newQuantity) => {
  if (newQuantity < 1) {
    quantity.value = 1
  } else if (newQuantity > props.product.stock_quantity) {
    quantity.value = props.product.stock_quantity
  }
  error.value = ''
})

const increaseQuantity = () => {
  if (quantity.value < props.product.stock_quantity) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const formatPrice = (price) => {
  return '₩' + price.toLocaleString()
}

const submitOrder = async () => {
  if (quantity.value <= 0 || quantity.value > props.product.stock_quantity) {
    error.value = '올바른 수량을 선택해주세요.'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const response = await axios.post('/orders', {
      items: [{
        product_id: props.product.id,
        quantity: quantity.value
      }],
      notes: notes.value || null
    })

    if (response.data.success) {
      emit('order-success', response.data)
    } else {
      error.value = response.data.message || '주문 처리 중 오류가 발생했습니다.'
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message
    } else {
      error.value = '주문 처리 중 오류가 발생했습니다.'
    }
  } finally {
    loading.value = false
  }
}
</script>