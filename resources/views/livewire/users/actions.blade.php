<div class="flex items-center gap-2">
    <flux:button
        wire:click="$dispatch('edit-user', { userId: {{ $entry->id }} })"
        variant="subtle"
        size="sm"
    >
        Editar
    </flux:button>

    <button
        type="button"
        x-data
        @click="$dispatch('open-confirm', { method: 'toggleEstatus', params: {{ $entry->id }} })"
        class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium transition-colors {{ $entry->estatus ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}"
    >
        {{ $entry->estatus ? 'Desactivar' : 'Activar' }}
    </button>
</div>
