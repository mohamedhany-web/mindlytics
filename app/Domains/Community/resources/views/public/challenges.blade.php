@extends('layouts.public')

@section('title', config('app.name') . ' - ' . __('public.challenges_title', [], app()->getLocale()))

@section('content')
    <section class="pt-28 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-10">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 mb-3">
                        التحديات
                    </h1>
                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                        شارك في تحديات ومنافسات، وارتقِ بمستواك خطوة بخطوة.
                    </p>
                </div>

                @if($competitions->isEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200 p-10 shadow-sm text-center">
                        <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bolt text-2xl"></i>
                        </div>
                        <h2 class="text-lg font-black text-slate-900 mb-2">لا توجد تحديات متاحة حالياً</h2>
                        <p class="text-slate-600">تابعنا قريباً — سيتم إضافة تحديات جديدة بشكل مستمر.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($competitions as $competition)
                            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                                <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center mb-4">
                                    <i class="fas fa-trophy text-xl"></i>
                                </div>

                                <h3 class="text-lg font-black text-slate-900 mb-2">{{ $competition->title }}</h3>

                                @if($competition->description)
                                    <p class="text-slate-600 text-sm mb-4 leading-relaxed line-clamp-3">
                                        {{ \Illuminate\Support\Str::limit($competition->description, 140) }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-2 text-xs text-slate-500">
                                    @if($competition->start_at)
                                        <span class="inline-flex items-center gap-1">
                                            <i class="far fa-calendar"></i>
                                            <span>بداية: {{ $competition->start_at->translatedFormat('Y-m-d') }}</span>
                                        </span>
                                    @endif
                                    @if($competition->end_at)
                                        <span class="inline-flex items-center gap-1">
                                            <i class="far fa-calendar-check"></i>
                                            <span>نهاية: {{ $competition->end_at->translatedFormat('Y-m-d') }}</span>
                                        </span>
                                    @endif
                                </div>

                                @if($competition->rules)
                                    <div class="mt-4 pt-4 border-t border-slate-100">
                                        <details class="group">
                                            <summary class="cursor-pointer select-none text-sm font-bold text-slate-800 flex items-center justify-between">
                                                <span>قواعد التحدي</span>
                                                <span class="text-slate-400 group-open:rotate-180 transition-transform">
                                                    <i class="fas fa-chevron-down"></i>
                                                </span>
                                            </summary>
                                            <div class="mt-3 text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                                {{ $competition->rules }}
                                            </div>
                                        </details>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($competitions->hasPages())
                        <div class="mt-10">
                            {{ $competitions->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>
@endsection

