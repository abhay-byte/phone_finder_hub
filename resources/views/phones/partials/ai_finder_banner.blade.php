<div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 relative z-20">
    <!-- Featured Hub: AI Finder & Editorials -->
    <div class="flex flex-col xl:flex-row overflow-hidden rounded-[2.5rem] bg-[#0c0c0c] border border-white/10 shadow-2xl group/featured">
        
        <!-- Left/Main: AI Finder (takes ~2/3 space) -->
        <div class="flex-1 relative p-10 md:p-12 lg:p-16 flex flex-col items-start justify-center overflow-hidden border-b xl:border-b-0 xl:border-r border-white/5">
            <!-- Neural Network / AI Glow Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-teal-900/40 via-purple-900/20 to-black/90 mix-blend-screen opacity-60"></div>
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-teal-500/20 rounded-full blur-[100px] group-hover/featured:bg-teal-500/30 transition-colors duration-1000"></div>
            <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[80px] group-hover/featured:bg-purple-600/30 transition-colors duration-1000"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz4KPC9zdmc+')] opacity-20"></div>

            <div class="relative flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-8 w-full z-10">
                <!-- Left Side: Text & CTA -->
                <div class="flex-1 text-left max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-[10px] md:text-xs font-bold font-mono tracking-widest uppercase text-teal-400 mb-6 backdrop-blur-sm animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                        Powered by AI
                    </div>
                    
                    <h2 class="text-3xl md:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.1] mb-6">
                        Can't decide? <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 via-emerald-300 to-cyan-400">Let AI find your perfect match.</span>
                    </h2>
                    
                    <p class="text-base md:text-lg text-gray-300 mb-8 max-w-lg leading-relaxed font-medium">
                        Chat with our intelligent assistant. Describe exactly what you need—gaming performance, massive battery, or pro-grade cameras—and get instant, personalized recommendations.
                    </p>
                    
                    <a href="{{ route('find.index') }}" class="group/btn relative inline-flex items-center justify-center gap-3 px-8 py-3.5 md:py-4 bg-white text-black font-black text-base md:text-lg rounded-2xl hover:scale-105 active:scale-95 transition-all w-full sm:w-auto shadow-[0_0_30px_rgba(255,255,255,0.15)] hover:shadow-[0_0_50px_rgba(45,212,191,0.4)] overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            Start AI Chat
                            <svg class="w-5 h-5 group-hover/btn:translate-x-1.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                        <!-- Button Hover Glow -->
                        <div class="absolute inset-0 bg-gradient-to-r from-teal-200 via-emerald-100 to-teal-200 opacity-0 group-hover/btn:opacity-10 transition-opacity"></div>
                    </a>
                </div>

                <!-- Right Side: Animated Mockup -->
                <div class="hidden md:block w-full max-w-sm lg:w-[45%] relative perspective-1000 select-none">
                    <!-- Floating Elements Container -->
                    <div class="relative w-full aspect-[4/3] transform-gpu preserve-3d group-hover/featured:rotate-y-[-5deg] transition-transform duration-1000 ease-out">
                        
                        <!-- Chat Bubble 1 (User) -->
                        <div class="absolute top-[10%] right-[10%] w-[70%] bg-[#212121] border border-white/10 rounded-2xl rounded-tr-sm p-4 shadow-2xl backdrop-blur-md transform-gpu animate-[float_6s_ease-in-out_infinite] z-20">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-6 h-6 rounded-full bg-gray-600 flex items-center justify-center text-white text-[10px] font-bold">U</div>
                                <span class="text-xs font-bold text-gray-400">You</span>
                            </div>
                            <p class="text-sm text-gray-200 leading-snug">I need a phone with insane battery life and a decent camera under ₹40,000.</p>
                        </div>

                        <!-- Chat Bubble 2 (AI Typing) -->
                        <div class="absolute top-[45%] left-[5%] w-[80%] bg-gradient-to-br from-teal-900/80 to-[#121E1C]/80 border border-teal-500/30 rounded-2xl rounded-tl-sm p-4 shadow-2xl backdrop-blur-lg transform-gpu animate-[float_7s_ease-in-out_infinite_1s] z-30">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-full bg-teal-500 flex items-center justify-center text-white shadow-[0_0_15px_rgba(45,212,191,0.5)]">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <span class="text-xs font-black tracking-widest uppercase text-teal-400">PhoneFinder AI</span>
                                </div>
                                <svg class="w-4 h-4 text-teal-500/50" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15 8L21 9L16 14L17 20L12 17L7 20L8 14L3 9L9 8L12 2Z"/></svg>
                            </div>
                            
                            <div class="flex gap-1.5 items-center pl-10 mb-2">
                                <div class="w-1.5 h-1.5 bg-teal-400 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                                <div class="w-1.5 h-1.5 bg-teal-400 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                                <div class="w-1.5 h-1.5 bg-teal-400 rounded-full animate-bounce"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Editorials & Tech News (takes ~1/3 space) -->
        <div class="w-full xl:w-[420px] 2xl:w-[480px] bg-[#0c0c0c] relative z-10 flex flex-col p-8 md:p-10 border-t xl:border-t-0 border-white/5">
            <div class="absolute inset-0 bg-gradient-to-b from-white/[0.03] to-transparent pointer-events-none"></div>
            @if(isset($latestBlogs) && $latestBlogs->count() > 0)
            <div class="mb-8 flex flex-col justify-between items-start relative z-10">
                <h2 class="text-2xl font-black text-white flex items-center gap-2 tracking-tight">
                    <span class="w-2 h-6 md:h-7 rounded-full bg-teal-500 block"></span>
                    Editorials
                </h2>
                <p class="text-xs text-slate-400 mt-2 uppercase tracking-widest font-bold ml-4">Latest tech guides & news</p>
            </div>

            <div class="flex flex-col gap-4 flex-1 justify-center relative z-10">
                @foreach($latestBlogs->take(3) as $blog)
                <a href="{{ route('blogs.show', $blog->slug) }}" class="group block overflow-hidden rounded-[1.25rem] bg-white/[0.04] border border-white/5 shadow-sm hover:border-teal-500/50 hover:bg-white/10 transition-all duration-300 relative">
                    <div class="flex items-center gap-4 p-4">
                        @if($blog->featured_image)
                        <div class="h-20 w-24 shrink-0 rounded-xl overflow-hidden bg-black/50">
                            <img src="{{ $blog->featured_image }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" alt="{{ $blog->title }}">
                        </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 text-[10px] text-gray-400">
                                <span class="font-bold uppercase tracking-wider text-teal-400 font-mono">{{ $blog->author->name ?? 'News' }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-600"></span>
                                <span>{{ $blog->published_at->format('M j') }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-white group-hover:text-teal-300 transition-colors line-clamp-2 leading-snug drop-shadow-sm">
                                {{ $blog->title }}
                            </h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            
            <a href="{{ route('blogs.index') }}" class="mt-8 relative z-10 flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-white/5 text-sm text-white font-bold hover:bg-white/10 transition-all border border-white/10 group">
                View All Editorials
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform text-white/50 group-hover:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            @endif
        </div>
        
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(1deg); }
    }
</style>
