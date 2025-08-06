# 아뮤즈 상품 재고·주문 관리 시스템 - 환경 구성 가이드

## 개발환경

### 백엔드
- **PHP**: 8.4.11
- **Laravel**: 12.21.0
- **Orchid Platform**: 14.52
- **MySQL**: 데이터베이스

### 프론트엔드
- **Vue.js**: 3.5.18 
- **Inertia.js**: 2.0.17
- **Pinia**: 3.0.3 
- **TailwindCSS**: 4.1.11

## 설치 및 설정

### 1. 프로젝트 클론
```bash
git clone [프로젝트_URL]
cd amuz
```

### 2. 의존성 설치
```bash
# PHP 의존성 설치
composer install

# Node.js 의존성 설치
npm install
```

### 3. 환경 설정
```bash
# .env 파일 복사
cp .env.example .env

# 애플리케이션 키 생성
php artisan key:generate
```

### 4. .env 파일 설정
```env
APP_NAME="아뮤즈 상품 재고·주문 관리 시스템"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8002

# 데이터베이스 설정
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amuz
DB_USERNAME=root
DB_PASSWORD=
```

### 5. 데이터베이스 생성
```sql
CREATE DATABASE amuz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. 마이그레이션 실행
```bash
# 데이터베이스 테이블 생성
php artisan migrate

# 시드 데이터 생성
php artisan db:seed
```

### 7. 스토리지 설정
```bash
# 스토리지 링크 생성
php artisan storage:link
```

### 8. 프론트엔드 빌드
```bash
# 개발 모드
npm run dev

# 또는 프로덕션 빌드
npm run build
```

## 🔐 기본 계정 정보

### 관리자 계정
- **이메일**: admin@amuz.com
- **비밀번호**: password
- **권한**: 모든 기능 접근 가능

### 테스트 사용자 계정
- **이메일**: user@amuz.com
- **비밀번호**: password
- **권한**: 일반 사용자 기능만 접근

## 📊 생성되는 샘플 데이터

### 사용자
- 관리자 1명
- 테스트 사용자 1명
- 추가 사용자 20명 (팩토리 생성)

### 카테고리
- 전자제품, 의류, 도서, 홈&리빙, 스포츠, 뷰티, 식품, 취미 (8개)

### 상품
- 각 카테고리별 상품

### 주문
- 총 50개의 주문 데이터
- 다양한 상태 (pending, paid, shipped, delivered, cancelled)

### Orchid 관리자 패널

# 관리자 패널 접속
```bash
http://localhost:8002/admin


### 사용자 페이지

# 사용자 페이지 접속
http://localhost:8002
```

## 📁 프로젝트 구조

```
amuz/
├── app/
│   ├── Http/Controllers/     # 컨트롤러
│   ├── Models/              # Eloquent 모델
│   └── Orchid/              # Orchid 관리자 패널
│       ├── Screens/         # 관리자 화면
│       └── Layouts/         # 레이아웃
│   
├── database/
│   ├── migrations/          # 데이터베이스 마이그레이션
│   └── seeders/            # 샘플 데이터
├── resources/
│   ├── js/                 # Vue.js 컴포넌트
│   └── views/              # Blade 템플릿
├── routes/
│   ├── web.php             # 웹 라우트
│   └── platform.php        # Orchid 라우트
└── public/                 # 공개 파일
```