# API
#### Auth Api
Регистрация, аутентификация и авторизация для всех пользователей системы
```
POST /register
{
    "user_type": "student",
    "phone": "79996339776",
    "verification_code": "12345",
    "last_name": "Dots",
    "first_name": "Roman",
    "patronymic_name": "A.",
    "gender": "m",
    "birth_date": "1986-01-08",
    "password": "123456"
}

POST /register/verify
{
    "phone": "79996339776",
    "verification_code": "12345"
}

POST /login
{
    "username": "79996339776",
    "password": "123456"
}

POST /logout

GET /user

PATCH /user/password

* POST /user/passsword/recover
```

#### Admin Api
Непубличный АПИ для интерфейса администраторов (менеджеров, руководителей)
```
/admin
```

#### Student Api
Непубличный АПИ для клиентских приложений (покупателей и студентов, преподавателей)
```
/student
```

#### Instructor Api 
Непубличный АПИ для приложения преподавателей
```
/instructor
```

#### Public Api
Публичный АПИ для сайтов, виджетов и приложений
```
/
```

