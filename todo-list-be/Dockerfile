FROM golang:1.24

WORKDIR /app

COPY go.mod go.sum ./
RUN go mod download

COPY . .

RUN go build -o /go/bin/main ./cmd/app

RUN ls -lah /go/bin

EXPOSE 8080

CMD ["/go/bin/main"]
