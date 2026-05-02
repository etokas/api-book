## Clone Project

```bash
git clone git@github.com:etokas/api-book.git api
cd api
```

---

## Installation

```bash
make start
```

## Test

Get all book
```
GET http://localhost:8080/api/books
```

Get one book (you will see the image for the book here)
```
GET http://localhost:8080/api/books/1
```

Upload new image
```
POST http://localhost:8080/api/books/3/image

Content-Type: multipart/form-data
form-data; name="image"
```