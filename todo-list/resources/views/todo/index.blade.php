@extends('layouts.app')

@section('title', 'Task Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col items-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Task Management</h1>

        <!-- Task Form -->
        @include('components.task-form', ['task' => $task ?? null, 'isEditing' => $isEditing ?? false])

        <!-- Ongoing Tasks -->
        <div class="w-full max-w-lg mt-8 space-y-4">
            <h2 class="text-xl font-semibold text-gray-700">Ongoing Tasks</h2>
            
            @if(count($activeTasks) > 0)
                <div class="space-y-3">
                    @foreach($activeTasks as $task)
                        @include('components.task-card', ['task' => $task])
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No active tasks found</p>
            @endif
        </div>

        <!-- Completed Tasks -->
        <div class="w-full max-w-lg mt-8 space-y-4">
            <h2 class="text-xl font-semibold text-gray-700">Completed Tasks</h2>
            
            @if(count($completedTasks) > 0)
                <div class="space-y-3">
                    @foreach($completedTasks as $task)
                        @include('components.task-card', ['task' => $task])
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No completed tasks yet</p>
            @endif
        </div>
    </div>
</div>

@endsection