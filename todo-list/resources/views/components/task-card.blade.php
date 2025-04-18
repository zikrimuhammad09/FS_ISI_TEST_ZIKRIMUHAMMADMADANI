@props(['task'])
<div class="bg-white rounded-xl shadow-md p-4 flex justify-between items-center transition hover:shadow-lg">
    <div class="flex-1">
        <div class="flex items-center gap-2">
            <h3 class="text-lg font-medium {{ $task['is_completed'] ? 'line-through text-gray-400' : 'text-gray-800' }}">
                {{ $task['title'] }}
            </h3>
            @if (!$task['is_completed'])
                <a href="{{ route('todo.index', ['edit' => $task['id']]) }}"
                    class="text-gray-500 hover:text-blue-500 transition">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
        <div class="text-sm text-gray-500 mt-1">
            {{ $task['created_at']->format('d M Y H:i') }}
        </div>
    </div>

    <div class="flex items-center gap-3 ">
        <form method="POST" action="{{ route('todo.destroy', $task['id']) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-gray-500 cursor-pointer hover:text-red-500 transition"
                onclick="return confirm('Are you sure you want to delete this task?')">
                <i data-lucide="circle-x" class="w-5 h-5"></i>
            </button>
        </form>

        <form method="POST" action="{{ route('todo.mark-complete', $task['id']) }}" class="inline cursor-pointer">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_status" value="{{ $task['is_completed'] ? '1' : '0' }}">
            <label class="inline-flex items-center ">
                <input name="is_completed"  type="checkbox" class="rounded-checkbox" {{ $task['is_completed'] ? 'checked' : '' }}
                    onchange="this.form.submit()">
            </label>
        </form>
    </div>
</div>
