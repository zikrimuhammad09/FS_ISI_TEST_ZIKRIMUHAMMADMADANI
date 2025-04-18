<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoTodoServices
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('GO_TODO_SERVICE_URL');
    }

    public function getAllTodo()
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/todo");
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception("Service returned status: " . $response->status());
            
        } catch (\Exception $e) {
            Log::error("GoTodoService getAll error: " . $e->getMessage());
            throw $e;
        }
    }

    public function createTodo(array $data)
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/todo", $data);
                
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception("Service returned status: " . $response->status());
            
        } catch (\Exception $e) {
            Log::error("GoTodoService create error: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateTodo($id, array $data)
    {
        try {
            $response = Http::put("{$this->baseUrl}/api/todo/{$id}", $data);
                
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception("Service returned status: " . $response->status());
            
        } catch (\Exception $e) {
            Log::error("GoTodoService update error: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteTodo($id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/api/todo/{$id}");
                
            if ($response->successful()) {
                return true;
            }
            
            throw new \Exception("Service returned status: " . $response->status());
            
        } catch (\Exception $e) {
            Log::error("GoTodoService delete error: " . $e->getMessage());
            throw $e;
        }
    }

    public function markComplete($id, $data)
    {
        try {
            $response = Http::put("{$this->baseUrl}/api/todo/{$id}", $data);
                
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception("Service returned status: " . $response->status());
            
        } catch (\Exception $e) {
            Log::error("GoTodoService toggleComplete error: " . $e->getMessage());
            throw $e;
        }
    }
}