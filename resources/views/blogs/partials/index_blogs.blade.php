            @forelse($blogs as $blog)
                <a href="{{ route('blogs.show', $blog->slug) }}" class="group flex flex-col bg-white dark:bg-[#121212] rounded-3xl border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm hover:shadow-xl hover:border-teal-200 dark:hover:border-teal-500/30 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="aspect-video w-full bg-gray-100 dark:bg-white/5 relative overflow-hidden">
                        @if($blog->featured_image)
                            <img src="{{ $blog->featured_image }}" alt="{{ $blog->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        
                        <!-- Overlay gradient on hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                            <span class="text-white font-medium text-sm flex items-center gap-2">
                                Read Full Article 
                                <svg class="w-4 h-4 translate-y-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-[10px] font-bold text-white shadow-sm shadow-indigo-500/20">
                                {{ substr($blog->author->name ?? 'A', 0, 1) }}
                            </div>
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $blog->author->name ?? 'Guest Author' }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $blog->published_at->format('M j, Y') }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 leading-snug group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors line-clamp-2">
                            {{ $blog->title }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 mb-6 flex-1">{{ $blog->excerpt }}</p>
                        
                        <div class="mt-auto border-t border-gray-100 dark:border-white/5 pt-4">
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-500 uppercase tracking-widest flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                Explore
                                <svg class="w-3 h-3 translate-y-[0.5px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-24 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Articles Yet</h3>
                    <p class="text-gray-500 dark:text-gray-400">Our authors haven't published anything yet. Check back soon!</p>
                </div>
            @endforelse