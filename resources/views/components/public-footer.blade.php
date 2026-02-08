<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About Us -->
            <div>
                <h4 class="text-lg font-bold mb-6">Mindlytics</h4>
                <p class="text-gray-400 text-sm leading-relaxed">
                    أكاديمية Mindlytics هي بوابتك لتعلم البرمجة من الصفر حتى الاحتراف. نقدم كورسات متكاملة ومدربين خبراء لمساعدتك في بناء مستقبل مشرق في عالم التكنولوجيا.
                </p>
                <div class="flex space-x-4 space-x-reverse mt-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-bold mb-6">روابط سريعة</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('public.about') }}" class="text-gray-400 hover:text-white transition-colors">من نحن</a></li>
                    <li><a href="{{ route('public.courses') }}" class="text-gray-400 hover:text-white transition-colors">الكورسات</a></li>
                    <li><a href="{{ route('public.pricing') }}" class="text-gray-400 hover:text-white transition-colors">الأسعار</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="text-lg font-bold mb-6">الدعم</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('public.faq') }}" class="text-gray-400 hover:text-white transition-colors">الأسئلة الشائعة</a></li>
                    <li><a href="{{ route('public.help') }}" class="text-gray-400 hover:text-white transition-colors">مركز المساعدة</a></li>
                    <li><a href="{{ route('public.contact') }}" class="text-gray-400 hover:text-white transition-colors">تواصل معنا</a></li>
                    <li><a href="{{ route('public.media.index') }}" class="text-gray-400 hover:text-white transition-colors">معرض الصور</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-bold mb-6">تواصل معنا</h4>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li class="flex items-center">
                        <i class="fas fa-map-marker-alt ml-3 text-sky-500"></i>
                        <span>123 شارع الأكاديمية، مدينة المعرفة</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone ml-3 text-sky-500"></i>
                        <span>+20 100 123 4567</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope ml-3 text-sky-500"></i>
                        <a href="mailto:info@mindlytics-academy.com" class="hover:text-white transition-colors">info@mindlytics-academy.com</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center">
            <p class="text-gray-400 text-sm">
                &copy; 2024 Mindlytics - أكاديمية البرمجة. جميع الحقوق محفوظة.
            </p>
            <div class="flex justify-center space-x-6 space-x-reverse mt-4 text-sm">
                <a href="{{ route('public.terms') }}" class="text-gray-400 hover:text-white transition-colors">الشروط والأحكام</a>
                <a href="{{ route('public.privacy') }}" class="text-gray-400 hover:text-white transition-colors">سياسة الخصوصية</a>
                <a href="{{ route('public.refund') }}" class="text-gray-400 hover:text-white transition-colors">سياسة الاسترجاع</a>
            </div>
        </div>
    </div>
</footer>

