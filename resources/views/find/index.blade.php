@extends('layouts.app')

@section('title', 'AI Phone Finder')
@section('description', 'Chat with our AI Phone Finder to get personalized smartphone recommendations and insights based on detailed specs and benchmarks.')
@section('hide_footer', true)

@section('content')
<div class="h-[calc(100vh-64px)] w-full flex bg-white dark:bg-[#131314] overflow-hidden" 
     x-data="chatAgent({{ json_encode($chats ?? []) }})"
     @send-msg.window="inputMessage = $event.detail; sendMessage();">

    <!-- Mobile Sidebar Toggle -->
    <div class="absolute top-4 left-4 z-50 lg:hidden" x-show="messages.length > 0 && !sidebarOpen">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition shadow-md">
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </button>
    </div>

    <!-- Sidebar (History) -->
    <div class="bg-gray-50 dark:bg-[#1e1f20] w-[260px] xl:w-[calc(100%/6)] h-full flex flex-col shrink-0 border-r border-gray-200 dark:border-gray-800 absolute lg:relative z-40 transform transition-transform duration-300"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="absolute top-2 right-2 p-2 bg-gray-200 dark:bg-[#333538] text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-full z-[60] lg:hidden transition shadow-sm">
             <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <!-- New Chat Button -->
        <div class="p-4 pt-4 lg:pt-6">
            <button @click="startNewChat()" class="flex items-center gap-3 w-full p-3 bg-white dark:bg-[#282a2c] hover:bg-gray-100 dark:hover:bg-[#333538] rounded-full text-gray-800 dark:text-gray-200 font-medium transition shadow-sm border border-gray-200 dark:border-gray-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New chat
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto px-3 pb-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-3 mt-2">Recent</h3>
            @auth
            <div class="space-y-1">
                <template x-for="chat in chatHistoryList" :key="chat.id">
                    <div class="relative group flex items-center w-full">
                        <button @click="loadChat(chat.id)" class="w-full text-left py-2.5 px-3 pr-8 rounded-xl border border-transparent hover:bg-gray-200 dark:hover:bg-[#282a2c] transition flex items-center gap-3"
                                :class="currentChatId === chat.id ? 'bg-gray-200 dark:bg-[#282a2c]' : 'text-gray-700 dark:text-gray-300'">
                            <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span x-text="chat.title || 'Conversation ' + chat.id" class="block text-sm truncate filter opacity-90"></span>
                        </button>
                        <button @click="deleteChat(chat.id)" class="absolute right-2 p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-white dark:hover:bg-gray-700 opacity-0 group-hover:opacity-100 transition focus:opacity-100" title="Delete Chat">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </template>
                <div x-show="chatHistoryList.length === 0" class="text-center text-sm text-gray-500 dark:text-gray-500 py-4 italic">
                    No recent chats.
                </div>
            </div>
            @else
            <div class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-[#282a2c] rounded-xl border border-gray-200 dark:border-gray-700 text-center">
                <p class="mb-2">Log in to save your chat history.</p>
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Log in</a>
            </div>
            @endauth
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="absolute inset-0 bg-black/50 z-30 lg:hidden backdrop-blur-sm" x-transition.opacity style="display: none;"></div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col h-full relative">
        <div class="absolute top-4 left-4 z-50 lg:hidden" x-show="messages.length === 0 && !sidebarOpen">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition shadow-md">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </button>
        </div>
        
        <!-- Messages Container -->
        <div id="messages-container" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:px-24 xl:px-48 pb-[150px] sm:pb-[200px] scroll-smooth w-full">
            
            <!-- Welcome State -->
            <div x-show="messages.length === 0 && !isLoading" class="h-full flex flex-col items-center pt-[15vh] lg:pt-[20vh] text-center w-full">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-semibold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400 p-2">
                    Hello, {{ auth()->check() ? explode(' ', auth()->user()->name)[0] : 'User' }}
                </h2>
                <h3 class="text-2xl sm:text-3xl md:text-4xl font-medium text-gray-400 dark:text-[#444746] mb-8 lg:mb-12">How can I help you find a phone today?</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 w-full max-w-4xl px-4">
                    <button type="button" @click="inputMessage = 'Suggest some phones for heavy gaming under ₹45,000.'; sendMessage()" class="p-4 bg-gray-50 hover:bg-gray-100 dark:bg-[#1e1f20] dark:hover:bg-[#282a2c] rounded-2xl text-left transition relative h-32 group border border-gray-100 dark:border-transparent">
                        <p class="text-[15px] font-medium text-gray-700 dark:text-gray-200 line-clamp-3 group-hover:text-black dark:group-hover:text-white transition">Suggest some phones for heavy gaming under ₹45,000.</p>
                        <div class="absolute bottom-3 right-3 bg-purple-100 dark:bg-purple-900/30 p-2 rounded-full text-purple-600 dark:text-purple-400 opacity-0 group-hover:opacity-100 transition transform translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 translate-x-[1px] translate-y-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                    </button>
                    <button type="button" @click="inputMessage = 'Which phones have the highest Camera Matrix Score (CMS) right now?'; sendMessage()" class="p-4 bg-gray-50 hover:bg-gray-100 dark:bg-[#1e1f20] dark:hover:bg-[#282a2c] rounded-2xl text-left transition relative h-32 group border border-gray-100 dark:border-transparent">
                        <p class="text-[15px] font-medium text-gray-700 dark:text-gray-200 line-clamp-3 group-hover:text-black dark:group-hover:text-white transition">Which phones have the highest Camera Matrix Score (CMS) right now?</p>
                        <div class="absolute bottom-3 right-3 bg-red-100 dark:bg-red-900/30 p-2 rounded-full text-red-600 dark:text-red-400 opacity-0 group-hover:opacity-100 transition transform translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 translate-x-[1px] translate-y-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                    </button>
                    <button type="button" @click="inputMessage = 'Compare the top two phones based on their overall Expert Score.'; sendMessage()" class="p-4 bg-gray-50 hover:bg-gray-100 dark:bg-[#1e1f20] dark:hover:bg-[#282a2c] rounded-2xl text-left transition relative h-32 group border border-gray-100 dark:border-transparent hidden sm:block">
                        <p class="text-[15px] font-medium text-gray-700 dark:text-gray-200 line-clamp-3 group-hover:text-black dark:group-hover:text-white transition">Compare the top two phones based on their overall Expert Score.</p>
                        <div class="absolute bottom-3 right-3 bg-yellow-100 dark:bg-yellow-900/30 p-2 rounded-full text-yellow-600 dark:text-yellow-400 opacity-0 group-hover:opacity-100 transition transform translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 translate-x-[1px] translate-y-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                    </button>
                    <button type="button" @click="inputMessage = 'Show me phones with great Battery Endurance that cost under ₹30,000.'; sendMessage()" class="p-4 bg-gray-50 hover:bg-gray-100 dark:bg-[#1e1f20] dark:hover:bg-[#282a2c] rounded-2xl text-left transition relative h-32 group border border-gray-100 dark:border-transparent hidden lg:block">
                        <p class="text-[15px] font-medium text-gray-700 dark:text-gray-200 line-clamp-3 group-hover:text-black dark:group-hover:text-white transition">Show me phones with great Battery Endurance that cost under ₹30,000.</p>
                        <div class="absolute bottom-3 right-3 bg-green-100 dark:bg-green-900/30 p-2 rounded-full text-green-600 dark:text-green-400 opacity-0 group-hover:opacity-100 transition transform translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 translate-x-[1px] translate-y-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Skeleton Loader State -->
            <div x-show="messages.length === 0 && isLoading" class="flex flex-col items-center justify-center pt-[15vh] w-full gap-4 max-w-4xl mx-auto">
                <div class="h-8 w-48 bg-gray-200 dark:bg-[#1e1f20] rounded-lg animate-pulse"></div>
                <div class="h-4 w-64 bg-gray-100 dark:bg-[#1e1f20] rounded-lg animate-pulse mb-8"></div>
                
                <div class="w-full flex flex-col gap-6">
                    <div class="self-end w-3/4 max-w-[400px] h-16 bg-gray-100 dark:bg-[#1e1f20] rounded-[24px] rounded-br-[8px] animate-pulse"></div>
                    <div class="self-start w-full max-w-[500px] flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-gray-200 dark:bg-[#1e1f20] animate-pulse hidden sm:block"></div>
                        <div class="flex-1 space-y-3 pt-2">
                            <div class="h-4 w-full bg-gray-200 dark:bg-[#1e1f20] rounded animate-pulse"></div>
                            <div class="h-4 w-5/6 bg-gray-200 dark:bg-[#1e1f20] rounded animate-pulse"></div>
                            <div class="h-4 w-4/6 bg-gray-200 dark:bg-[#1e1f20] rounded animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Loop -->
            <div class="space-y-8 max-w-4xl mx-auto w-full pt-4 pb-12">
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="flex flex-col gap-2 relative">
                        <!-- User Message -->
                        <template x-if="msg.role === 'user'">
                            <div class="self-end max-w-[85%] sm:max-w-[75%] rounded-[24px] rounded-br-[8px] bg-gray-100 dark:bg-[#1e1f20] px-5 py-3.5 text-black dark:text-[#e3e3e3] text-[15px] leading-relaxed relative group">
                                <span x-html="renderMarkdown(msg.content)"></span>
                            </div>
                        </template>

                        <!-- Assistant Message -->
                        <template x-if="msg.role === 'assistant'">
                            <div class="self-start max-w-full group w-full flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0 mt-3 hidden sm:flex">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="prose prose-base dark:prose-invert prose-p:text-gray-800 dark:prose-p:text-[#e3e3e3] prose-headings:text-black dark:prose-headings:text-white prose-strong:text-black dark:prose-strong:text-white max-w-none w-full bg-transparent dark:bg-transparent rounded-2xl px-2 py-2 mt-1 whitespace-pre-line break-words" x-html="renderMarkdown(msg.content)">
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Loading Indicator -->
                <div x-show="isLoading" class="flex justify-start max-w-4xl mx-auto w-full mb-12">
                    <div class="flex items-start gap-4 w-full">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0 hidden sm:flex">
                             <svg class="w-5 h-5 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="flex items-center h-8 gap-2">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                            <span class="w-2 h-2 bg-purple-500 rounded-full animate-pulse animation-delay-200"></span>
                            <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse animation-delay-400"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Input Area -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-white via-white to-white/0 dark:from-[#131314] dark:via-[#131314] dark:to-[#131314]/0 pt-6 pb-6 px-4 sm:px-6 lg:px-24 xl:px-48 pointer-events-none w-full">
            <div class="max-w-4xl mx-auto pointer-events-auto w-full">
                <form @submit.prevent="sendMessage" class="relative group bg-gray-50 dark:bg-[#1e1f20] rounded-[24px] border border-gray-200 dark:border-transparent focus-within:bg-white dark:focus-within:bg-[#282a2c] shadow-sm focus-within:shadow-md transition-all duration-300">
                    <textarea 
                        x-ref="inputField"
                        x-model="inputMessage" 
                        @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                        @input="resizeTextarea"
                        placeholder="Ask Phone Finder..." 
                        class="w-full max-h-[200px] min-h-[56px] bg-transparent border-none py-4 px-6 pr-14 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-0 resize-none overflow-y-auto text-[15px] rounded-[24px]"
                        rows="1"
                        :disabled="isLoading"
                        autofocus
                    ></textarea>
                    
                    <button type="submit" 
                            :disabled="!inputMessage.trim() || isLoading"
                            class="absolute right-3 bottom-3 p-2 rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed group-focus-within:text-black text-gray-400 hover:text-gray-800 dark:text-gray-400 dark:hover:text-[#e3e3e3] dark:group-focus-within:text-white"
                            :class="inputMessage.trim() && !isLoading ? 'bg-indigo-600 hover:bg-indigo-700 text-white dark:text-white dark:hover:bg-indigo-500' : ''">
                        <svg class="w-5 h-5 translate-x-px rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
                <div class="text-xs text-center text-gray-500 dark:text-gray-400 mt-3 flex justify-center items-center gap-1.5 opacity-80">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    Phone Finder AI can make mistakes. Verify important information.
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Marked.js for Markdown Rendering -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<!-- DOMPurify for security -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.6/purify.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatAgent', (initialChats) => ({
            chatHistoryList: initialChats,
            currentChatId: null,
            messages: [],
            inputMessage: '',
            isLoading: false,
            sidebarOpen: false,

            init() {
                this.$nextTick(() => {
                    this.resizeTextarea();
                    this.$refs.inputField.focus();
                });
            },

            resizeTextarea() {
                const el = this.$refs.inputField;
                el.style.height = 'auto';
                let newHeight = el.scrollHeight;
                if(newHeight > 200) newHeight = 200;
                el.style.height = newHeight + 'px';
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: date.getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined });
            },

            renderMarkdown(text) {
                const rawHtml = marked.parse(text);
                let sanitized = DOMPurify.sanitize(rawHtml, { USE_PROFILES: { html: true } });
                
                let finalHtml = sanitized.replace(/\[CARD\|([\s\S]*?)\|([\s\S]*?)\|([\s\S]*?)\|([\s\S]*?)\|([\s\S]*?)\|([\s\S]*?)\|([\s\S]*?)\|([\s\S]*?)\]/g, (match, name, price, image, link, soc, battery, amazon, flipkart) => {
                    const extractUrl = (str) => {
                        if (!str) return '';
                        str = str.trim();
                        if (str.includes('href="')) return str.split('href="')[1].split('"')[0];
                        return str.replace(/<[^>]*>?/gm, '').trim();
                    };
                    const stripHtml = (str) => str ? str.replace(/<[^>]*>?/gm, '').trim() : '';

                    let safeName = stripHtml(name).replace(/"/g, '&quot;') || 'Phone';
                    let safePrice = stripHtml(price);
                    let safeImage = extractUrl(image);
                    let safeLink = extractUrl(link) || '#';
                    let safeSoc = stripHtml(soc);
                    let safeBattery = stripHtml(battery);
                    let safeAmazon = extractUrl(amazon);
                    let safeFlipkart = extractUrl(flipkart);
                    
                    let imgSrc = safeImage || '/images/placeholder.png';
                    
                    let batteryHtml = safeBattery ? `
                        <div class="flex items-center gap-1.5 text-left">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span class="truncate">${safeBattery}</span>
                        </div>` : '';

                    let buttonsHtml = '';
                    if (safeAmazon || safeFlipkart) {
                        let amzBtn = safeAmazon ? `<a href="${safeAmazon}" target="_blank" class="flex-1 flex justify-center items-center gap-1.5 bg-[#FFF2EA] hover:bg-[#FFE3D0] dark:bg-[#43352A] dark:hover:bg-[#534030] text-[#D97706] font-semibold text-[11px] py-1.5 px-2 rounded-lg transition-colors no-underline shadow-sm" title="Buy on Amazon">
                            <img src="/assets/amazon-icon.png" class="w-3.5 h-3.5 object-contain" alt="Amazon"> Amazon
                        </a>` : '';
                        let flpBtn = safeFlipkart ? `<a href="${safeFlipkart}" target="_blank" class="flex-1 flex justify-center items-center gap-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 font-semibold text-[11px] py-1.5 px-2 rounded-lg transition-colors no-underline shadow-sm" title="Buy on Flipkart">
                            <img src="/assets/flipkart-icon.png" class="w-3.5 h-3.5 object-contain" alt="Flipkart"> Flipkart
                        </a>` : '';
                        
                        buttonsHtml = `<div class="flex items-center gap-2 pt-3 mt-2 border-t border-gray-100 dark:border-gray-800/80 w-full">${amzBtn}${flpBtn}</div>`;
                    }
                    
                    return `<div class="not-prose my-3 flex flex-col p-3 border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-[#1e1f20] shadow-sm hover:shadow-md transition-all group w-full max-w-[220px] shrink-0 inline-block align-top mr-1">
                        <div class="cursor-pointer flex flex-col items-center" onclick="window.open('${safeLink}', '_blank')">
                            <div class="h-28 w-full bg-gray-50 dark:bg-[#282a2c] rounded-xl flex items-center justify-center p-2 mb-3 overflow-hidden border border-gray-100 dark:border-gray-800 transition-colors group-hover:bg-gray-100 dark:group-hover:bg-[#333538]">
                                <img src="${imgSrc}" alt="${safeName}" class="max-h-full max-w-full object-contain mix-blend-multiply dark:mix-blend-normal hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="text-center w-full">
                                <h4 class="font-bold text-gray-900 dark:text-white text-[15px] truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors m-0 leading-tight">${safeName}</h4>
                                <p class="text-[14px] font-bold text-indigo-600 dark:text-indigo-400 mt-1 mb-0 leading-tight">${safePrice}</p>
                            </div>
                            <div class="w-full mt-2 pt-2 border-t border-gray-100 dark:border-gray-800 text-[12px] text-gray-500 dark:text-gray-400 flex flex-col gap-1">
                                <div class="flex items-center gap-1.5 whitespace-normal leading-snug text-left">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                    <span class="truncate">${safeSoc}</span>
                                </div>
                                ${batteryHtml}
                            </div>
                        </div>
                        ${buttonsHtml}
                    </div>`;
                });

                return finalHtml.replace(/\[BTN\|(.*?)\]/g, (match, text) => {
                    const safeText = text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                    return `<button type="button" onclick="window.dispatchEvent(new CustomEvent('send-msg', {detail: '${safeText}'}))" class="not-prose block w-full text-left my-2 px-4 py-3 bg-white dark:bg-[#282a2c] hover:bg-indigo-50 dark:hover:bg-indigo-900/30 border border-gray-200 dark:border-gray-700 rounded-xl text-[15px] font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shadow-sm hover:border-indigo-200 dark:hover:border-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        ${text}
                    </button>`;
                });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = document.getElementById('messages-container');
                    container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                });
            },

            startNewChat() {
                this.currentChatId = null;
                this.messages = [];
                this.sidebarOpen = false;
                this.$nextTick(() => {
                    this.$refs.inputField.focus();
                    this.resizeTextarea();
                });
            },

            async loadChat(id) {
                this.messages = [];
                this.isLoading = true;
                this.sidebarOpen = false;
                try {
                    const response = await fetch(`/find/chat/${id}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Could not load chat history.');
                    }
                    
                    const data = await response.json();
                    this.messages = data.messages;
                    this.currentChatId = data.chat_id;
                    this.scrollToBottom();
                } catch (error) {
                    console.error('Failed to load chat:', error);
                    alert('Failed to load chat history. Please try again later.');
                } finally {
                    this.isLoading = false;
                    this.$nextTick(() => {
                        this.resizeTextarea();
                    });
                }
            },

            async deleteChat(id) {
                if (!confirm('Are you sure you want to delete this chat?')) {
                     return;
                }
                
                try {
                    const response = await fetch(`/find/chat/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Could not delete chat.');
                    }
                    
                    this.chatHistoryList = this.chatHistoryList.filter(c => c.id !== id);
                    if (this.currentChatId === id) {
                         this.startNewChat();
                    }
                } catch (error) {
                    console.error('Failed to delete chat:', error);
                    alert('Failed to delete chat. Please try again later.');
                }
            },

            async sendMessage() {
                const text = this.inputMessage.trim();
                if (!text || this.isLoading) return;

                this.messages.push({ role: 'user', content: text });
                this.inputMessage = '';
                this.$nextTick(() => {
                    this.resizeTextarea();
                });
                this.isLoading = true;
                this.scrollToBottom();

                const messageIndex = this.messages.length;
                this.messages.push({ role: 'assistant', content: '' });

                try {
                    const response = await fetch('{{ route('find.chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'text/event-stream'
                        },
                        body: JSON.stringify({
                            message: text,
                            chat_id: this.currentChatId,
                            history: this.messages.slice(0, -2)
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        throw new Error(errorData.error || 'Server responded with an error');
                    }

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let buffer = '';
                    let assistantContent = '';

                    while (true) {
                        const { value, done } = await reader.read();
                        if (done) break;
                        buffer += decoder.decode(value, { stream: true });
                        
                        const lines = buffer.split('\n');
                        buffer = lines.pop(); // keep the last partial line in the buffer
                        
                        for (const line of lines) {
                            if (line.startsWith('data: ')) {
                                const dataStr = line.replace('data: ', '').trim();
                                if(!dataStr) continue;
                                try {
                                    const json = JSON.parse(dataStr);
                                    if (json.type === 'meta') {
                                        if (!this.currentChatId && json.chat_id) {
                                            this.currentChatId = json.chat_id;
                                            this.chatHistoryList.unshift({
                                                id: json.chat_id,
                                                title: json.title || 'New Chat',
                                                updated_at: new Date().toISOString()
                                            });
                                        }
                                    } else if (json.type === 'chunk') {
                                        assistantContent += json.content;
                                        this.messages[messageIndex].content = assistantContent;
                                        this.scrollToBottom();
                                    } else if (json.type === 'error') {
                                        throw new Error(json.message);
                                    }
                                } catch (e) {
                                    // Ignore JSON parse errors for SSE parts
                                }
                            }
                        }
                    }

                } catch (error) {
                    console.error('Chat Error:', error);
                    let errMsg = error.message;
                    if(errMsg && errMsg.includes('usage limit')) {
                        errMsg = `<span class="text-red-500 dark:text-red-400 font-semibold mb-2 block">Rate Limit Exceeded</span>You have hit the daily limits for the AI service free tier. Please try again tomorrow, or consider upgrading API keys!`;
                    } else if(errMsg && (errMsg.includes('unavailable') || errMsg.includes('Failed to connect'))) {
                         errMsg = `<span class="text-red-500 dark:text-red-400 font-semibold mb-2 block">Connection Failed</span>Failed to connect to the AI service. The model might be temporarily down or heavily loaded.`;
                    }
                    this.messages[messageIndex].content += `\n\n${errMsg}`;
                } finally {
                    this.isLoading = false;
                    this.scrollToBottom();
                    this.$nextTick(() => {
                        this.$refs.inputField.focus();
                        this.resizeTextarea();
                    });
                }
            }
        }));
    });
</script>

<style>
    /* Styling for nested markdown elements */
    .prose p { margin-top: 0.35em; margin-bottom: 0.35em; line-height: 1.6; }
    .prose p:first-child { margin-top: 0; }
    .prose p:last-child { margin-bottom: 0; }
    .prose ul { margin-top: 0.25em; margin-bottom: 0.25em; list-style-type: disc; padding-left: 1.5em; }
    .prose ol { margin-top: 0.25em; margin-bottom: 0.25em; list-style-type: decimal; padding-left: 1.5em; }
    .prose li { margin-top: 0.15em; margin-bottom: 0.15em; line-height: 1.6;}
    .prose h1, .prose h2, .prose h3 { margin-top: 1em; margin-bottom: 0.5em; font-weight: 700; }
    .prose h1:first-child, .prose h2:first-child, .prose h3:first-child { margin-top: 0; }
    .prose strong { font-weight: 600; color: inherit; }
    .prose a { text-decoration: underline; text-underline-offset: 2px; color: #6366f1; }
    .dark .prose a { color: #818cf8; }
    
    .animation-delay-200 { animation-delay: 200ms; }
    .animation-delay-400 { animation-delay: 400ms; }

    /* Custom scrollbar for dark text areas */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
</style>
@endsection
