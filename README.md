# Тестовое задание: Сервис Оценки Имущества

Реализация тестового задания на позицию Middle PHP Developer.
Приложение представляет собой форму заказа услуги с авторизацией и динамическим расчетом стоимости.

## Стек технологий
* **PHP 8.3**
* **Symfony 6.4 (LTS)**
* **SQLite** (в качестве БД для простоты развертывания)
* **PHPUnit** (тесты)
* **Docker** (не требуется, используется встроенный сервер Symfony)

## Функционал
1.  **Авторизация:** Реализована через `Symfony Security Bundle`.
2.  **Заказы:** Форма с выбором услуги. Цена меняется динамически (JS) и фиксируется на бэкенде.
3.  **Валидация:** Защита от пустых данных на уровне Entity.
4.  **Тесты:** Покрыты сценарии доступа, отображения формы и успешного создания заказа (`tests/OrderControllerTest.php`).

## Установка и запуск

1. **Клонирование репозитория**
   ```bash
   git clone https://github.com/IlnurSK/online-ocenka.git
   cd online-ocenka
   ```
2. **Установка зависимостей**
   ```bash
   composer install
   ```
3. **Настройка БД Приложение использует SQLite, база создастся автоматически в папке** `var/`
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate --no-interaction
   ```
4. **Создание тестового пользователя Я написал консольную команду для быстрого создания юзера:**
   ```bash
   php bin/console app:create-user
   ```
**Login**: `test@test.com` **Password**: `password`

5. **Запуск сервера**
   ```bash
   symfony server:start
   ```
Перейдите по адресу: `http://127.0.0.1:8000/login`

## Тестирование
Для запуска тестов необходимо подготовить тестовую базу данных (выполнить один раз):
   ```bash
  php bin/console --env=test doctrine:database:create
  php bin/console --env=test doctrine:schema:create
   ```
После этого запуск тестов:
   ```bash
  php bin/phpunit
   ```
