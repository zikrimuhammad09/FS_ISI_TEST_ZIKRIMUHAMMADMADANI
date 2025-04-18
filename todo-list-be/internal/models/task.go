package models

import "time"

type Task struct {
	ID          int64      `json:"id"`
	Title       string     `json:"title" validate:"required,min=3,max=100"`
	IsCompleted bool       `json:"is_completed"`
	CompletedAt *time.Time `json:"completed_at,omitempty"`
	CreatedAt   time.Time  `json:"created_at"`
	UpdatedAt   time.Time  `json:"updated_at"`
}

type CreateTaskRequest struct {
	Title string `json:"title" validate:"required,min=3,max=100"`
}

type UpdateTaskRequest struct {
	Title       *string `json:"title,omitempty" validate:"omitempty,min=3,max=100"`
	IsCompleted *bool   `json:"is_completed,omitempty"`
}
