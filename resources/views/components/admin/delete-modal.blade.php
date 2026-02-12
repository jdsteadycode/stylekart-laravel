@props(['id' => 'modal', 'title' => 'Delete Item', 'message' => 'Are you sure', 'action' => '#'])
<button @click="open = true" class="text-red-600 hover:underline">
    Delete
</button>

<div x-show="open" x-cloak
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm">
        <h2 class="text-lg font-semibold mb-2">{{ $title }}</h2>
        <p class="text-sm text-slate-600 mb-4">{{ $message }}</p>
        <div class="flex justify-end gap-3">
            <button @click="open = false" class="px-4 py-2 text-sm border rounded-md">Cancel</button>
            <form method="POST" action="{{ $action }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
