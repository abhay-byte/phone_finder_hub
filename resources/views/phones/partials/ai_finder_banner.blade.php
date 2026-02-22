<div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 relative z-20">
    <div class="relative overflow-hidden rounded-[2.5rem] bg-[#0c0c0c] border border-white/10 shadow-2xl group">
        <!-- Neural Network / AI Glow Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-teal-900/40 via-purple-900/20 to-black/90 mix-blend-screen opacity-60"></div>
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-teal-500/20 rounded-full blur-[100px] group-hover:bg-teal-500/30 transition-colors duration-1000"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[80px] group-hover:bg-purple-600/30 transition-colors duration-1000"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz4KPC9zdmc+')] opacity-20"></div>

        <div class="relative flex flex-col lg:flex-row items-center justify-between p-10 md:p-16 lg:p-20 gap-12 lg:gap-8">
            
            <!-- Left Side: Text & CTA -->
            <div class="flex-1 text-left max-w-2xl relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-bold font-mono tracking-widest uppercase text-teal-400 mb-6 backdrop-blur-sm animate-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                    Powered by AI
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.1] mb-6">
                    Can't decide? <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 via-emerald-300 to-cyan-400">Let AI find your perfect match.</span>
                </h2>
                
                <p class="text-lg text-gray-300 mb-10 max-w-xl leading-relaxed font-medium">
                    Chat with our intelligent assistant. Describe exactly what you need—gaming performance, massive battery, or pro-grade cameras—and get instant, highly personalized, data-driven recommendations.
                </p>
                
                <a href="{{ route('find.index') }}" class="group/btn relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-black font-black text-lg rounded-2xl hover:scale-105 active:scale-95 transition-all w-full sm:w-auto overflow-hidden shadow-[0_0_40px_rgba(255,255,255,0.15)] hover:shadow-[0_0_60px_rgba(45,212,191,0.4)]">
                    <span class="relative z-10 flex items-center gap-2">
                        Start AI Chat
                        <svg class="w-5 h-5 group-hover/btn:translate-x-1.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                    <!-- Button Hover Glow -->
                    <div class="absolute inset-0 bg-gradient-to-r from-teal-200 via-emerald-100 to-teal-200 opacity-0 group-hover/btn:opacity-10 transition-opacity"></div>
                </a>
            </div>

            <!-- Right Side: Animated Mockup -->
            <div class="flex-1 w-full lg:w-auto relative perspective-1000 lg:pl-10 select-none hidden md:block">
                <!-- Floating Elements Container -->
                <div class="relative w-full max-w-md mx-auto aspect-[4/3] transform-gpu preserve-3d group-hover:rotate-y-[-5deg] transition-transform duration-1000 ease-out">
                    
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

                    <!-- Chat Results Card (Behind) -->
                    <div class="absolute top-[65%] right-[5%] w-[65%] bg-[#1a1a1a]/95 border border-white/5 rounded-2xl p-3 shadow-xl transform-gpu animate-[float_8s_ease-in-out_infinite_2s] z-10 blur-[1px] opacity-80 scale-95">
                         <div class="flex items-center gap-3">
                              <div class="w-10 h-10 bg-gray-800 rounded-lg"></div>
                              <div class="space-y-1.5 flex-1">
                                  <div class="h-2.5 w-3/4 bg-gray-700 rounded-full"></div>
                                  <div class="h-2 w-1/2 bg-gray-700 rounded-full"></div>
                              </div>
                         </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(1deg); }
    }
</style>
