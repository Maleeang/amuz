<template>
  <AppLayout>
    <Head title="대시보드" />

    <div class="min-h-screen bg-gradient-to-br from-elegant-50 via-white to-warm-50">
      
      <div class="bg-elegant-600 border-b border-elegant-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div class="text-center">
            <h1 class="text-4xl font-elegant font-bold text-white mb-4">
              안녕하세요, {{ $page.props.auth.user.name }}님
            </h1>
            <p class="text-xl text-white/90 font-serif">아뮤즈 상품관리시스템 대시보드에 오신 것을 환영합니다</p>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          
          <Link
            :href="'/'"
            class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 hover:shadow-xl hover:scale-105 transition-all duration-300 p-6 block"
          >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-elegant-500 to-elegant-600 rounded-xl flex items-center justify-center shadow-md">
                  <span class="text-2xl">📦</span>
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-elegant-800 font-elegant">상품 목록</h3>
                <p class="text-sm text-elegant-600 font-serif">상품 목록을 확인해보세요</p>
              </div>
            </div>
          </Link>

          
          <Link
            :href="'/orders'"
            class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 hover:shadow-xl hover:scale-105 transition-all duration-300 p-6 block"
          >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-warm-500 to-warm-600 rounded-xl flex items-center justify-center shadow-md">
                  <span class="text-2xl">🛒</span>
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-elegant-800 font-elegant">내 주문 보기</h3>
                <p class="text-sm text-elegant-600 font-serif">주문 내역을 확인하세요</p>
              </div>
            </div>
          </Link>

                     
           <a
             v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.includes('admin')"
             href="/admin"
             class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 hover:shadow-xl hover:scale-105 transition-all duration-300 p-6 block"
           >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-soft-500 to-soft-600 rounded-xl flex items-center justify-center shadow-md">
                  <span class="text-2xl">⚙️</span>
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-elegant-800 font-elegant">관리자 페이지</h3>
                <p class="text-sm text-elegant-600 font-serif">관리자 페이지로 이동합니다</p>
              </div>
            </div>
          </a>
        </div>

        
        <div class="bg-gradient-to-br from-white via-elegant-50 to-warm-50 backdrop-blur-sm rounded-2xl shadow-lg border border-elegant-200 p-8 mb-8">
          <h2 class="text-2xl font-elegant font-bold text-elegant-800 mb-6">계정 정보</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-elegant-700 font-serif">이름</label>
              <p class="mt-2 text-sm text-elegant-800 font-medium">{{ $page.props.auth.user.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-elegant-700 font-serif">이메일</label>
              <p class="mt-2 text-sm text-elegant-800 font-medium">{{ $page.props.auth.user.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-elegant-700 font-serif">가입일</label>
              <p class="mt-2 text-sm text-elegant-800 font-medium">
                {{ formatDate($page.props.auth.user.created_at) }}
              </p>
            </div>
            <div v-if="$page.props.auth.user.roles">
              <label class="block text-sm font-medium text-elegant-700 font-serif">권한</label>
              <div class="mt-2">
                <span
                  v-for="role in $page.props.auth.user.roles"
                  :key="role"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-elegant-500 to-elegant-600 text-white mr-2 shadow-sm"
                >
                  {{ getRoleLabel(role) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('ko-KR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getRoleLabel = (role) => {
  const labels = {
    'admin': '관리자',
    'user': '일반사용자',
    'moderator': '운영자'
  }
  return labels[role] || role
}
</script>