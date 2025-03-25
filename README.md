# Отчёт по уязвимостям Semgrep

## 1. Server-Side Request Forgery (SSRF)
**Файл:** `admin.php` (строка 11)  
**Описание:**  
> Использование пользовательского ввода (`$_GET`) для формирования имени файла в `file_get_contents($file)`. Злоумышленник может указать произвольный путь или URL, что приведёт к несанкционированным запросам.

**Рекомендации:**
- Валидируйте и ограничивайте допустимые пути/домены.
- Используйте белые списки разрешённых значений.
- Рассмотрите замену на безопасные методы (например, загрузку файлов через контролируемые механизмы).

---

## 2. Command Injection
**Файлы:**  
- `function.php` (строка 4): Использование `shell_exec($command)` с неконтролируемым входом.  
- `function.php` (строка 10): Вызов `executeCommand($userInput)` с прямым использованием пользовательских данных.  

**Описание:**  
> Пользовательский ввод передаётся в системные команды без санации, что позволяет выполнить произвольный код.

**Рекомендации:**
- Избегайте использования функций вроде `shell_exec`.
- Если необходимо, экранируйте аргументы с помощью `escapeshellarg()`.
- Используйте безопасные альтернативы (например, встроенные функции PHP для работы с файлами).

---

## 3. SQL Injection
**Файл:** `login.php` (строка 9)  
**Описание:**  
> Прямая подстановка переменных `$username` и `$password` в SQL-запрос, что позволяет злоумышленнику модифицировать логику запроса.

**Рекомендации:**
- Перейдите на подготовленные запросы (Prepared Statements) с использованием PDO или MySQLi.
- Внедрите ORM-библиотеки для автоматической санации данных.

---

## 4. Plaintext HTTP Links
**Файлы:** Множество ссылок в `report.html` (например, строки 386, 417, 448 и др.).  
**Описание:**  
> Использование HTTP вместо HTTPS подвергает данные риску перехвата.

**Рекомендации:**
- Замените все ссылки на HTTPS-версии.
- Настройте сервер для принудительного использования HTTPS (редирект с HTTP).

---

## 5. Прочие замечания
**Файлы с Zone.Identifier:**  
> В списке сканируемых путей присутствуют файлы вида `admin.php:Zone.Identifier`. Это артефакты Windows, указывающие на происхождение файлов из ненадёжных источников (например, загрузок из интернета). Удалите их из production-окружения.

---

## Итог
**Инструмент:** Semgrep (Community rules)  
**Критичность:**  
- **ERROR:** SQL Injection, Command Injection (требуют немедленного исправления).  
- **WARNING:** SSRF, HTTP-ссылки (рекомендуется исправить в ближайшее время).

# Отчёт о сканировании OWASP ZAP

## Уязвимости уровня **Medium**

### 1. Content Security Policy (CSP) Header Not Set
- **Описание:**  
  Отсутствует заголовок CSP, что повышает риск XSS-атак и инъекций.
- **Затронутые URL:**  
  `Главная страница`, `/admin.php`, `/login.php`, `/logout.php`, `/robots.txt`.
- **Рекомендации:**  
  Добавить заголовок `Content-Security-Policy` с ограничениями на источники скриптов, стилей и других ресурсов. Использовать генераторы CSP (например, [CSP Evaluator](https://csp-evaluator.withgoogle.com/)).

---

### 2. Missing Anti-clickjacking Header
- **Описание:**  
  Отсутствуют заголовки `X-Frame-Options` или `frame-ancestors` в CSP, что позволяет встраивать страницы в `<iframe>`.
- **Затронутые URL:**  
  `Главная страница`, `/admin.php`.
- **Рекомендации:**  
  - Установить `X-Frame-Options: DENY`.  
  - Добавить директиву `frame-ancestors 'none'` в CSP.

---

## Уязвимости уровня **Low**

### 1. Cookie No HttpOnly Flag
- **Описание:**  
  Куки `PHPSESSID` не помечены как `HttpOnly`, что делает их доступными для JavaScript.
- **Затронутые URL:**  
  `/login.php`.
- **Рекомендации:**  
  Добавить атрибут `HttpOnly` к куки.

---

### 2. Cookie without SameSite Attribute
- **Описание:**  
  Куки `PHPSESSID` не имеют атрибута `SameSite`, что может привести к CSRF-атакам.
- **Затронутые URL:**  
  `/login.php`.
- **Рекомендации:**  
  Установить `SameSite=Lax` или `SameSite=Strict`.

---

### 3. Insufficient Site Isolation Against Spectre Vulnerability
- **Описание:**  
  Отсутствуют заголовки `Cross-Origin-Resource-Policy`, `Cross-Origin-Embedder-Policy`, `Cross-Origin-Opener-Policy`.
- **Затронутые URL:**  
  `Главная страница`, `/admin.php`, `/login.php`.
- **Рекомендации:**  
  Добавить заголовки:  
  ```http
  Cross-Origin-Resource-Policy: same-origin
  Cross-Origin-Embedder-Policy: require-corp
  Cross-Origin-Opener-Policy: same-origin
  
---

## 4. Permissions Policy Header Not Set
- **Описание:**  
  Отсутствует заголовок `Permissions-Policy`, ограничивающий доступ к функциям браузера (камера, микрофон и т.д.).
- **Затронутые URL:**  
  `Главная страница`, `/admin.php`, `/login.php`, `/logout.php`, `/robots.txt`, `/sitemap.xml`.
- **Рекомендации:**  
  Настроить заголовок с разрешением только необходимых функций:  
  ```http
  Permissions-Policy: camera=(), microphone=(), geolocation=()
  
---

## 5. Server Leaks Information via "X-Powered-By" Header  
- **Описание:**  
  Заголовок `X-Powered-By: PHP/8.3.6` раскрывает информацию о сервере.  
- **Затронутые URL:**  
  `Главная страница`, `/admin.php`, `/login.php`.  
- **Рекомендации:**  
  Удалить заголовок в настройках сервера (PHP или веб-сервера).  

---

## 6. X-Content-Type-Options Header Missing  
- **Описание:**  
  Отсутствует заголовок `X-Content-Type-Options: nosniff`, что позволяет браузерам менять MIME-тип.  
- **Затронутые URL:**  
  `Главная страница`, `/admin.php`, `/login.php`.  
- **Рекомендации:**  
  Добавить заголовок:  
  ```http
  X-Content-Type-Options: nosniff

---

## Информационные предупреждения

1. **Authentication Request Identified**  
   - **Описание:** Обнаружена форма аутентификации на `/login.php` (POST-запрос с параметрами `username` и `password`).  
   - **URL:** `/login.php`.

2. **Non-Storable Content**  
   - **Описание:** Ответы содержат `Cache-Control: no-store`, что полностью отключает кэширование контента.  
   - **Затронутые URL:** Все динамические страницы.

3. **Session Management Response Identified**  
   - **Описание:** Для управления сессиями используется куки `PHPSESSID` без атрибутов безопасности.  
   - **URL:** Все страницы с аутентификацией.

4. **Storable and Cacheable Content**  
   - **Описание:** Статические страницы (например, главная, `/logout.php`) кэшируются браузером.  
   - **Риск:** Потенциальная утечка данных через кэш.  

---

## Рекомендации по устранению

### Для информационных предупреждений:
- **Authentication Request:**  
  Добавьте защиту от брутфорса (например, лимит попыток входа).  
  Внедрите CAPTCHA для форм аутентификации.

- **Non-Storable Content:**  
  Для статических ресурсов (CSS, JS, изображения) разрешите кэширование:  
  ```http
  Cache-Control: public, max-age=604800
  ```

- **Session Management:**  
  Обновите параметры куки:  
  ```http
  Set-Cookie: PHPSESSID=...; HttpOnly; Secure; SameSite=Strict
  ```

### Общие рекомендации:
1. **Заголовки безопасности:**  
   - Добавьте `X-Content-Type-Options: nosniff`.  
   - Удалите `X-Powered-By` через настройки сервера.  
   - Включите `Cross-Origin`-заголовки для изоляции ресурсов.

2. **HTTPS:**  
   - Настройте принудительное перенаправление с HTTP на HTTPS.  
   - Используйте HSTS-заголовок:  
     ```http
     Strict-Transport-Security: max-age=31536000; includeSubDomains
     ```

3. **Кэширование:**  
   - Для динамических страниц сохраните `Cache-Control: no-store`.  
   - Для статики добавьте длительный TTL:  
     ```http
     Cache-Control: public, max-age=31536000, immutable
     ```
