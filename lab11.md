# Лабораторна робота №11
**Тема:** Застосування інтерфейсів, трейтів та обробки винятків у PHP

## Мета роботи
Закріпити поглиблені концепції об’єктно-орієнтованого програмування у PHP — проєктування інтерфейсів як абстрактних контрактів, горизонтальне перевикористання коду за допомогою трейтів, створення власних класів винятків та побудову надійної архітектури взаємодії шару представлення з бізнес-логікою.

## Хід роботи
1. Спроєктовано та створено інтерфейс `TaskStorageInterface.php` для абстрагування операцій сховища.
2. Реалізовано трейт `TimestampableTrait.php` для відстеження часу створення та оновлення сутностей.
3. Створено ієрархію доменних винятків (`TaskManagerException`, `ValidationException`, `TaskNotFoundException`).
4. Оновлено клас `TaskRepository.php` з урахуванням імплементації інтерфейсу та генерації винятків.
5. Налаштовано безпечну обробку запитів у `index.php` через багаторівневі блоки `try...catch...finally`.

## Кодова база (Основні створені файли)

### 1. TaskStorageInterface.php
```php
<?php
declare(strict_types=1);

require_once __DIR__ . '/Task.php';

interface TaskStorageInterface
{
    public function findAll(): array;
    public function findById(int $id): ?Task;
    public function add(string $title, ?string $deadline = null): Task;
    public function complete(int $id): bool;
    public function delete(int $id): bool;
    public function countCompleted(): int;
    public function countOverdue(): int;
}
```
### 2. TimestampableTrait.php
```php
<?php
declare(strict_types=1);

trait TimestampableTrait
{
    private string $createdAt;
    private ?string $updatedAt = null;

    public function initTimestamps(?string $createdAt = null, ?string $updatedAt = null): void
    {
        $this->createdAt =$createdAt ?? date('Y-m-d H:i:s');
        $this->updatedAt =$updatedAt;
    }

    public function touch(): void
    {
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
```
### 3. Exceptions.php
```php
<?php
declare(strict_types=1);

class TaskManagerException extends RuntimeException {}

class TaskNotFoundException extends TaskManagerException
{
    public function __construct(int $taskId)
    {
        parent::__construct("Завдання з ідентифікатором #{$taskId} не знайдено.", 404);
    }
}

class ValidationException extends TaskManagerException
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors =$errors;
        parent::__construct('Помилка валідації даних форми: ' . implode(', ', $errors), 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```
## Аналіз сховища даних (tasks.json)
Фрагмент файлу із серіалізованими полями `createdAt` та `updatedAt`:
```json
[
    {
        "id": 1,
        "title": "Зробити лабу 4",
        "isCompleted": true,
        "deadline": "2026-10-21",
        "createdAt": "2026-10-27 15:53:46",
        "updatedAt": "2026-10-27 16:10:15"
    }
]
```
## Контрольні питання
1. **Різниця між інтерфейсом та абстрактним класом:** Інтерфейс задає лише методи без реалізації (контракт), а абстрактний клас може мати готові методи і змінні. Для сховища обрано інтерфейс, оскільки він дозволяє повністю відокремити контракт від конкретної реалізації носія даних.

2. **Переваги слабого зв’язку (Loose Coupling):** Використання інтерфейсу дозволяє легко змінювати джерело даних (наприклад, переходити з JSON на MySQL/PDO) без зміни коду контролера чи шару представлення.

3. **Горизонтальне перевикористання через трейти:** `TimestampableTrait` вирішує проблему відсутності множинного наслідування в PHP, дозволяючи підключати логіку аудиту часу до будь-яких класів.

4. **Черговість перехоплення у блоках catch:** Більш специфічні винятки (`ValidationException`, `TaskNotFoundException`) мають стояти вище за загальні, інакше базовий клас перехопить помилку раніше.

5. **Призначення секції finally:** Код у блоці `finally` виконується завжди (незалежно від виникнення винятку) і використовується для вивільнення ресурсів чи закриття з'єднань.