<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <Head title="로그인" />
    
    <div class="max-w-md w-full space-y-8">
      
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          로그인
        </h2>
      </div>

      
      <form class="mt-8 space-y-6" @submit.prevent="submit">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="email" class="sr-only">이메일 주소</label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': form.errors.email }"
              placeholder="이메일 주소"
            />
            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>
          
          <div>
            <label for="password" class="sr-only">비밀번호</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': form.errors.password }"
              placeholder="비밀번호"
            />
            <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
              {{ form.errors.password }}
            </div>
          </div>
        </div>

        
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="form.remember"
              name="remember"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
              로그인 상태 유지
            </label>
          </div>
        </div>

        
        <div>
          <button
            type="submit"
            :disabled="form.processing"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="form.processing">로그인 중...</span>
            <span v-else>로그인</span>
          </button>
        </div>

        
        <div v-if="form.errors.message" class="rounded-md bg-red-50 p-4">
          <div class="text-sm text-red-600">
            {{ form.errors.message }}
          </div>
        </div>

        
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
          <h4 class="text-sm font-medium text-blue-800 mb-2">테스트 계정</h4>
          <div class="text-sm text-blue-700 space-y-1">
            <div><strong>관리자:</strong> admin@amuz.com / password</div>
            <div><strong>일반사용자:</strong> user@amuz.com / password</div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>