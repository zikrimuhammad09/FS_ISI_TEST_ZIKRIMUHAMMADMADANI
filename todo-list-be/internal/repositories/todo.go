package repositories

import (
	"database/sql"
	"fmt"
	"strings"
	"todo-list-be/internal/models"
)

type TodoRepository interface {
	GetAll() ([]models.Task, error)
	Create(task *models.CreateTaskRequest) (*models.Task, error)
	Update(id int64, task *models.UpdateTaskRequest) (*models.Task, error)
	Delete(id int64) error
}

type todoRepository struct {
	db *sql.DB
}

func NewTodoRepository(db *sql.DB) TodoRepository {
	return &todoRepository{db: db}
}

func (r *todoRepository) GetAll() ([]models.Task, error) {
	rows, err := r.db.Query("SELECT id, title, is_completed,completed_at, created_at, updated_at FROM tasks ORDER BY id DESC")

	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var todos []models.Task
	for rows.Next() {
		var todo models.Task
		if err := rows.Scan(&todo.ID, &todo.Title, &todo.IsCompleted, &todo.CompletedAt, &todo.CreatedAt, &todo.UpdatedAt); err != nil {
			return nil, err
		}
		todos = append(todos, todo)
	}
	return todos, nil
}

func (r *todoRepository) Create(task *models.CreateTaskRequest) (*models.Task, error) {
	query := `
        INSERT INTO tasks (title)
        VALUES ($1)
        RETURNING id, title, created_at, updated_at
    `

	var newTask models.Task
	err := r.db.QueryRow(
		query,
		task.Title,
	).Scan(
		&newTask.ID,
		&newTask.Title,
		&newTask.CreatedAt,
		&newTask.UpdatedAt,
	)

	if err != nil {
		return nil, err
	}

	return &newTask, nil
}

func (r *todoRepository) Update(id int64, task *models.UpdateTaskRequest) (*models.Task, error) {
	// Mulai membangun query dinamis
	query := "UPDATE tasks SET "
	var params []interface{}
	var setClauses []string
	paramCount := 1

	// Update title jika disediakan
	if task.Title != nil {
		setClauses = append(setClauses, fmt.Sprintf("title = $%d", paramCount))
		params = append(params, *task.Title)
		paramCount++
	}

	// Update status jika disediakan
	if task.IsCompleted != nil {
		setClauses = append(setClauses, fmt.Sprintf("is_completed = $%d", paramCount))
		params = append(params, *task.IsCompleted)

		// Jika menyelesaikan task, set completed_at
		if *task.IsCompleted {
			setClauses = append(setClauses, fmt.Sprintf("completed_at = NOW()"))
		} else {
			setClauses = append(setClauses, fmt.Sprintf("completed_at = NULL"))
		}
		paramCount++
	}

	// Pastikan ada yang diupdate
	if len(setClauses) == 0 {
		return nil, fmt.Errorf("tidak ada field yang diupdate")
	}

	// Tambahkan updated_at
	setClauses = append(setClauses, "updated_at = NOW()")

	// Gabungkan query
	query += strings.Join(setClauses, ", ")
	query += fmt.Sprintf(" WHERE id = $%d RETURNING id, title, is_completed, created_at, updated_at, completed_at", paramCount)
	params = append(params, id)

	var updatedTask models.Task
	err := r.db.QueryRow(query, params...).Scan(
		&updatedTask.ID,
		&updatedTask.Title,
		&updatedTask.IsCompleted,
		&updatedTask.CreatedAt,
		&updatedTask.UpdatedAt,
		&updatedTask.CompletedAt,
	)

	if err != nil {
		return nil, fmt.Errorf("gagal update task: %w", err)
	}

	return &updatedTask, nil
}

func (r *todoRepository) Delete(id int64) error {
	query := "DELETE FROM tasks WHERE id = $1"

	result, err := r.db.Exec(query, id)
	if err != nil {
		return fmt.Errorf("Gagal menghapus task: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("Gagal memeriksa rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("Task tidak ditemukan")
	}

	return nil
}
