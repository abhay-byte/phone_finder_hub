                @foreach($posts as $post)
                    <li class="group hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors duration-300">
                        <div class="px-6 py-5">
                            <div class="grid grid-cols-12 gap-4 sm:items-center">
                                
                                <div class="col-span-12 sm:col-span-8 lg:col-span-9 flex items-start sm:items-center gap-4">
                                    <div class="shrink-0 hidden sm:block">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-500 to-indigo-500 text-white flex items-center justify-center font-bold text-sm shadow-inner shrink-0 group-hover:scale-105 transition-transform">
                                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('forum.post.show', $post->slug) }}" class="block focus:outline-none">
                                            <h2 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors truncate">
                                                {{ $post->title }}
                                            </h2>
                                        </a>
                                        <div class="mt-1.5 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                            <div class="flex items-center gap-1.5 font-medium">
                                                <div class="w-4 h-4 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 flex sm:hidden items-center justify-center font-bold text-[8px]">
                                                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                                </div>
                                                <span class="text-slate-700 dark:text-slate-300">{{ $post->user->name }}</span>
                                            </div>
                                            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full shrink-0"></span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <time datetime="{{ $post->created_at->toIso8601String() }}">
                                                    {{ $post->created_at->diffForHumans() }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-span-12 sm:col-span-4 lg:col-span-3 flex justify-start sm:justify-end gap-5 sm:gap-6 mt-3 sm:mt-0 pl-14 sm:pl-0">
                                    <div class="flex flex-col items-center {{ $post->upvotes > 0 ? 'text-teal-600 dark:text-teal-400' : '' }}">
                                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 {{ $post->upvotes > 0 ? '!text-teal-600 dark:text-teal-400' : '' }}">{{ number_format($post->upvotes) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5 {{ $post->upvotes > 0 ? '!text-teal-600 dark:text-teal-400' : '' }}">Upvotes</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ number_format($post->comments_count) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Replies</span>
                                    </div>
                                    <div class="flex flex-col items-center opacity-70">
                                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ number_format($post->views) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase tracking-widest mt-0.5">Views</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>