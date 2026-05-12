<div x-data="{ open: false, method: null, params: null }"
     @open-confirm.window="open = true; method = $event.detail.method; params = $event.detail.params">
    
    <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="open = false" class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6 mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Confirmación Requerida</h3>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Esta acción cambiará el estado del usuario. ¿Deseas continuar?</p>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button @click="open = false" class="px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition-colors">
                    Cancelar
                </button>
                <button @click="params !== undefined ? $wire[method](params) : $wire[method](); open = false;" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Sí, confirmar
                </button>
            </div>
        </div>
    </div>
</div>
