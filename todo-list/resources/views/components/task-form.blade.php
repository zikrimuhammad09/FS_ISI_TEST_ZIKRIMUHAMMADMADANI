@props(['task' => null, 'isEditing' => false])

<div class="w-full max-w-lg space-y-4">
    @if($isEditing)
        <form method="POST" action="{{ route('todo.update', $task['id']) }}">
            @csrf
            @method('PUT')
    @else
        <form method="POST" action="{{ route('todo.store') }}">
            @csrf
    @endif

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input 
            id="title" 
            name="title" 
            type="text" 
            value="{{ $isEditing ? $task['title'] : old('title') }}"
            class="py-2.5 px-4 block w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            placeholder="Add task title..."
            
        >
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-3 mt-4 justify-center">
        <button
            type="submit"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition"
        >
            {{ $isEditing ? 'Update Task' : 'Add Task' }}
        </button>

        @if($isEditing)
            <a
                href="{{ route('todo.index') }}"
                class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition"
            >
                Cancel
            </a>
        @endif
    </div>

    </form>
</div>