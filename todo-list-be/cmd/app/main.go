package main

import (
	"log"
	"net/http"
	"todo-list-be/internal/config"
	"todo-list-be/internal/handlers"
	"todo-list-be/internal/repositories"
	"todo-list-be/internal/route"

	"github.com/joho/godotenv"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Println("No .env file found")
	}

	db, err := config.ConnectDB()
	if err != nil {
		log.Fatal("Failed to connect to DB:", err)
	}
	defer db.Close()

	todoRepo := repositories.NewTodoRepository(db)
	todoHandler := handlers.NewTodoHandler(todoRepo)
	r := route.NewRouter(todoHandler)

	log.Println("Server running on :8080")
	log.Fatal(http.ListenAndServe(":8080", r))
}
