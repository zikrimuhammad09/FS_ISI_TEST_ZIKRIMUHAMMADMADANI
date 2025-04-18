package route

import (
	"net/http"
	"todo-list-be/internal/handlers"

	"github.com/go-chi/chi/v5"
	"github.com/go-chi/chi/v5/middleware"
)

func NewRouter(todoHandler *handlers.TodoHandler) http.Handler {
	r := chi.NewRouter()

	// Middlewares
	r.Use(middleware.Logger)
	r.Use(middleware.Recoverer)

	// Routes
	r.Get("/", func(w http.ResponseWriter, r *http.Request) {
		w.Write([]byte("Welcome to Todo API"))
	})

	r.Route("/api/todo", func(r chi.Router) {
		r.Get("/", todoHandler.GetAllTasks)
		r.Post("/", todoHandler.CreateTask)
		r.Put("/{id}", todoHandler.UpdateTask)
		r.Delete("/{id}", todoHandler.DeleteTask)
	})

	return r
}
