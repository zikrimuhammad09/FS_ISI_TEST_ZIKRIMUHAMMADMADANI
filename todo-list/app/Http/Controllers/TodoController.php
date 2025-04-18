<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\GoTodoServices;
use Illuminate\Support\Facades\Http;

class TodoController extends Controller
{
    protected $goService;

    public function __construct(GoTodoServices $goService)
    {
        $this->goService = $goService;
    }

    /**
     * Menampilkan semua task
     */
    public function index(Request $request)
{
    try {
        $response = $this->goService->getAllTodo();

        $tasks = collect($response)->map(function ($task) {
            $task['created_at'] = Carbon::parse($task['created_at']);
            return $task;
        });

        $editId = $request->query('edit');
        $editTask = $editId ? $tasks->firstWhere('id', (int) $editId) : null;

        $activeTasks = $tasks->filter(fn($task) => !$task['is_completed'])
            ->sortBy('created_at')
            ->values()
            ->all();

        $completedTasks = $tasks->filter(fn($task) => $task['is_completed'])
            ->sortByDesc('created_at')
            ->values()
            ->all();

        return view('todo.index', [
            'activeTasks' => $activeTasks,
            'completedTasks' => $completedTasks,
            'task' => $editTask,
            'isEditing' => (bool) $editTask,
        ]);
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to fetch tasks: ' . $e->getMessage());
    }
}

    /**
     * Menyimpan task baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:100',
        ]);

        try { 
            $response = $this->goService->createTodo([
                'title' => $request->title,
            ]);

            return redirect()->route('todo.index')
                ->with('success', 'Task created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    /**
     * Mengupdate task
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:100',
        ]);

        try {
            $response = $this->goService->updateTodo($id, [
                'title' => $request->title,
            ]);

            return redirect()->route('todo.index')
                ->with('success', 'Task updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus task
     */
    public function destroy($id)
    {
        try {
            $this->goService->deleteTodo($id);

            return redirect()->route('todo.index')
                ->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Menandai task selesai/belum
     */
    public function markComplete(Request $request, $id)
    {
        $request->validate([
            'current_status' => 'required|in:0,1',
        ]);
    
        // Toggle status
        $newStatus = $request->current_status == '1' ? false : true;
    
        try {
            $response = $this->goService->updateTodo($id, [
                'is_completed' => $newStatus
            ]);
    
            return redirect()->route('todo.index')
                ->with('success', 'Task status updated');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update task status: ' . $e->getMessage());
        }
    }
    
}
