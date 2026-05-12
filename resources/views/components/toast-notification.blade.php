<div
    x-data="{
        notifications: [],
        add(e) {
            this.notifications.push({
                id: e.timeStamp,
                type: e.detail.type || 'success',
                message: e.detail.message,
            });
            setTimeout(() => { this.remove(e.timeStamp) }, 4000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    @notify.window="add($event)"
    class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none"
>
    <!-- Soporte para Session Flash -->
    @if(session()->has('status'))
        <div x-init="add({ detail: { type: 'success', message: '{{ session('status') }}' }, timeStamp: Date.now() })"></div>
    @endif
    @if(session()->has('notify'))
        <div x-init="add({ detail: { type: '{{ session('notify')['type'] ?? 'info' }}', message: '{{ session('notify')['message'] }}' }, timeStamp: Date.now() })"></div>
    @endif

    <template x-for="notification in notifications" :key="notification.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="flex items-center p-4 rounded-lg shadow-lg pointer-events-auto bg-white dark:bg-zinc-800 border-l-4"
            :class="{
                'border-green-500 text-green-700 dark:text-green-400': notification.type === 'success',
                'border-red-500 text-red-700 dark:text-red-400': notification.type === 'error',
                'border-yellow-500 text-yellow-700 dark:text-yellow-400': notification.type === 'warning',
                'border-blue-500 text-blue-700 dark:text-blue-400': notification.type === 'info'
            }"
        >
            <div class="flex-1 text-sm font-medium" x-text="notification.message"></div>
            <button @click="remove(notification.id)" class="ml-4 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>
</div>
