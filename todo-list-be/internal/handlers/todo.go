package handlers

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"strings"
	"todo-list-be/internal/models"
	"todo-list-be/internal/repositories"

	"github.com/go-chi/chi/v5"
	"github.com/go-playground/validator/v10"
)

type TodoHandler struct {
	repo      repositories.TodoRepository
	validator *validator.Validate
}

func NewTodoHandler(repo repositories.TodoRepository) *TodoHandler {
	return &TodoHandler{
		repo:      repo,
		validator: validator.New(),
	}
}

func (h *TodoHandler) GetAllTasks(w http.ResponseWriter, r *http.Request) {
	tasks, err := h.repo.GetAll()
	if err != nil {
		respondWithError(w, http.StatusInternalServerError, "Failed to fetch tasks")
		return
	}
	respondWithJSON(w, http.StatusOK, tasks)
}

func (h *TodoHandler) CreateTask(w http.ResponseWriter, r *http.Request) {
	var req models.CreateTaskRequest

	// Decode request body
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		log.Printf("Error decoding request: %v", err)
		http.Error(w, "Invalid request body", http.StatusBadRequest)
		return
	}

	// Validate request
	if err := h.validator.Struct(req); err != nil {
		log.Printf("Validation error: %v", err)
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	// Create task in repository
	task, err := h.repo.Create(&req)
	if err != nil {
		log.Printf("Repository error: %v", err)
		http.Error(w, "Failed to create task", http.StatusInternalServerError)
		return
	}

	// Return created task
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(task)
}

func (h *TodoHandler) UpdateTask(w http.ResponseWriter, r *http.Request) {
	// Ambil ID dari URL
	idStr := chi.URLParam(r, "id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		respondWithError(w, http.StatusBadRequest, "ID task tidak valid")
		return
	}

	var req models.UpdateTaskRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		respondWithError(w, http.StatusBadRequest, "Format request tidak valid")
		return
	}

	// Validasi hanya field yang diisi
	if err := h.validator.Struct(req); err != nil {
		respondWithError(w, http.StatusBadRequest, err.Error())
		return
	}

	// Panggil repository
	updatedTask, err := h.repo.Update(id, &req)
	if err != nil {
		log.Printf("Error updating task: %v", err)
		respondWithError(w, http.StatusInternalServerError, "Gagal mengupdate task")
		return
	}

	respondWithJSON(w, http.StatusOK, updatedTask)
}

func (h *TodoHandler) DeleteTask(w http.ResponseWriter, r *http.Request) {
	// Ambil ID dari URL
	idStr := chi.URLParam(r, "id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		respondWithError(w, http.StatusBadRequest, "ID task tidak valid")
		return
	}

	// Panggil repository untuk delete
	err = h.repo.Delete(id)
	if err != nil {
		if strings.Contains(err.Error(), "tidak ditemukan") {
			respondWithError(w, http.StatusNotFound, "Task tidak ditemukan")
		} else {
			log.Printf("Error deleting task: %v", err)
			respondWithError(w, http.StatusInternalServerError, "Gagal menghapus task")
		}
		return
	}

	// Return success response
	respondWithJSON(w, http.StatusOK, map[string]interface{}{
		"success": true,
		"message": "Task berhasil dihapus",
	})
}

// Helper functions
func respondWithError(w http.ResponseWriter, code int, message string) {
	respondWithJSON(w, code, map[string]string{"error": message})
}

func respondWithJSON(w http.ResponseWriter, code int, payload interface{}) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(code)
	json.NewEncoder(w).Encode(payload)
}
