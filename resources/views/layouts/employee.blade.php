<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة الموظف - Mindlytics')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo-removebg-preview.png') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        * {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
        }
        /* إخفاء شريط التمرير في سايدبار الموظف مع بقاء التمرير يعمل */
        .employee-sidebar-nav {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .employee-sidebar-nav::-webkit-scrollbar {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
         x-init="
          // إغلاق السايدبار عند النقر على الروابط
          window.addEventListener('close-sidebar', () => {
              sidebarOpen = false;
          });
          
          // إغلاق السايدبار عند تغيير حجم النافذة إلى desktop
          let resizeTimeout;
          window.addEventListener('resize', () => {
              clearTimeout(resizeTimeout);
              resizeTimeout = setTimeout(() => {
                  if (window.innerWidth >= 1024) {
                      sidebarOpen = false;
                  }
              }, 150);
          });
      "
      @close-sidebar.window="sidebarOpen = false">
    <div class="flex min-h-screen lg:h-screen overflow-x-hidden">
        <!-- Sidebar - Fixed -->
        <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:right-0 lg:z-20 flex-shrink-0 inset-y-0">
            @include('layouts.employee-sidebar')
        </aside>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click.away="if (window.innerWidth < 1024) sidebarOpen = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 lg:hidden"
             style="display: none;">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            <div class="absolute inset-y-0 right-0 flex flex-col w-64 bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform transition-transform duration-150 ease-out border-l border-slate-700/50"
                 :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
                <div class="absolute top-4 left-4 z-50">
                    <button @click="sidebarOpen = false" class="flex items-center justify-center h-10 w-10 rounded-full bg-slate-700/50 hover:bg-slate-600/50 text-slate-200 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                @include('layouts.employee-sidebar')
            </div>
        </div>

        <!-- Main content area -->
        <div class="flex flex-col flex-1 min-w-0 lg:pr-64 w-full lg:h-screen">
            <!-- Top navigation -->
            <header class="sticky top-0 z-30 flex-shrink-0 flex h-14 sm:h-16 bg-gradient-to-r from-slate-50 via-blue-50 to-slate-100 shadow-lg border-b border-slate-200/50 backdrop-blur-sm">
                <button @click="sidebarOpen = true" class="px-3 sm:px-4 border-l border-slate-200/50 text-slate-700 hover:bg-slate-100/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-400 lg:hidden transition-colors">
                    <i class="fas fa-bars text-base sm:text-lg"></i>
                </button>
                
                <div class="flex-1 px-3 sm:px-6 flex justify-between items-center gap-2">
                    <div class="flex-1 flex items-center gap-2 sm:gap-4 min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                            @yield('header', 'لوحة الموظف')
                        </h1>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-bell text-lg"></i>
                            </button>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                @php
                                    $user = auth()->user();
                                    $profileImage = $user->profile_image_url;
                                @endphp
                                @if($profileImage)
                                    <img src="{{ $profileImage }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-blue-200">
                                @else
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                    </div>
                                @endif
                                <span class="hidden sm:block">{{ $user->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="{{ route('employee.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-home mr-2"></i>لوحة التحكم
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-3 sm:p-4 md:p-6">
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    </div>

    <!-- Employee Notification Popup -->
    <div id="employeeNotificationPopup" 
         x-data="{ 
             show: false, 
             notification: null,
             readNotifications: JSON.parse(localStorage.getItem('readEmployeeNotifications') || '[]'),
             checkNotifications() {
                 fetch('{{ route('employee.notifications.unread') }}')
                     .then(response => response.json())
                     .then(data => {
                         if (data.success && data.notifications.length > 0) {
                             // عرض أول إشعار غير مقروء (لم يتم قراءته من قبل)
                             const unreadNotification = data.notifications.find(n => 
                                 !this.readNotifications.includes(n.id.toString())
                             );
                             
                             if (unreadNotification && !this.show) {
                                 this.notification = unreadNotification;
                                 this.show = true;
                             }
                         } else {
                             this.show = false;
                         }
                     })
                     .catch(error => console.error('Error fetching notifications:', error));
             },
             dismissNotification() {
                 // إغلاق فقط بدون قراءة - سيظهر مرة أخرى
                 this.show = false;
                 setTimeout(() => {
                     this.checkNotifications();
                 }, 1000);
             },
             markAsRead() {
                 if (this.notification) {
                     const notificationId = this.notification.id.toString();
                     const actionUrl = this.notification.action_url;
                     
                     fetch(`/employee/api/notifications/${this.notification.id}/mark-read`, {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json'
                         }
                     })
                     .then(response => response.json())
                     .then(data => {
                         if (data.success) {
                             // إضافة إلى قائمة المقروءة
                             if (!this.readNotifications.includes(notificationId)) {
                                 this.readNotifications.push(notificationId);
                                 localStorage.setItem('readEmployeeNotifications', JSON.stringify(this.readNotifications));
                             }
                             
                             this.show = false;
                             this.notification = null;
                             
                             // إذا كان هناك رابط إجراء، انتقل إليه
                             if (actionUrl) {
                                 setTimeout(() => {
                                     window.location.href = actionUrl;
                                 }, 300);
                             } else {
                                 // فحص إشعارات جديدة بعد قراءة هذا
                                 setTimeout(() => {
                                     this.checkNotifications();
                                 }, 1000);
                             }
                         }
                     })
                     .catch(error => {
                         console.error('Error marking as read:', error);
                         this.show = false;
                     });
                 }
             }
         }"
         x-init="
             // فحص الإشعارات عند تحميل الصفحة
             setTimeout(() => {
                 checkNotifications();
             }, 1000);
             
             // فحص الإشعارات كل 30 ثانية (Real-time)
             setInterval(() => {
                 if (!show) {
                     checkNotifications();
                 }
             }, 30000);
         "
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border-2 border-blue-200 animate-scale-in"
             @click.away="dismissNotification()">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white"
                         :class="{
                             'from-red-500 to-red-600': notification?.priority === 'urgent',
                             'from-orange-500 to-orange-600': notification?.priority === 'high',
                             'from-yellow-500 to-yellow-600': notification?.priority === 'normal',
                             'from-blue-500 to-blue-600': !notification?.priority || notification?.priority === 'low'
                         }">
                        <i class="fas fa-bell text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900" x-text="notification?.title"></h3>
                        <span class="text-xs text-gray-500" x-text="notification?.created_at"></span>
                    </div>
                </div>
                <button @click="dismissNotification()" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="notification?.message"></p>
            </div>
            
            <div class="flex items-center gap-3">
                <button @click="markAsRead()" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-check ml-2"></i>
                    قرأت الإشعار
                </button>
                <button @click="dismissNotification()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .animate-scale-in {
            animation: scale-in 0.3s ease-out;
        }
        [x-cloak] { display: none !important; }
    </style>

    @stack('scripts')
</body>
</html>
