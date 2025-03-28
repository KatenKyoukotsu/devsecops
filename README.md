### Отчет об обнаруженных уязвимостях (SCA)

---

#### **1. composer.lock (PHP-зависимости)**
**Всего уязвимостей:** 7 (CRITICAL: 1, HIGH: 6)

##### **phpmailer/phpmailer** (Установленная версия: v5.2.18)
- **CVE-2016-10045** (CRITICAL):  
  Удаленное выполнение кода (RCE).  
  **Рекомендация:** Обновить до версии 5.2.20.
- **CVE-2018-19296** (HIGH):  
  Уязвимость в обработке объектов.  
  **Рекомендация:** Обновить до 5.2.27 или 6.0.6.
- **CVE-2020-13625** (HIGH):  
  Ошибка экранирования вывода.  
  **Рекомендация:** Обновить до 6.1.6.
- **CVE-2021-34551** (HIGH):  
  Удаленное выполнение кода через UNC-пути (Windows).  
  **Рекомендация:** Обновить до 6.5.0.
- **CVE-2021-3603** (HIGH):  
  Уязвимость, приводящая к утечке данных.  
  **Рекомендация:** Обновить до актуальной версии (6.4.2+).

##### **twig/twig** (Установленная версия: v1.44.0)
- **CVE-2022-39261** (HIGH):  
  Уязвимость в шаблонизаторе Twig.  
  **Рекомендация:** Обновить до 1.44.7, 2.15.3 или 3.4.3.
- **CVE-2024-45411** (HIGH):  
  Ошибка санитизации данных.  
  **Рекомендация:** Обновить до 1.44.7, 2.16.0, 3.11.0 или 3.14.0.

---

#### **2. package-lock.json (npm-зависимости)**
**Всего уязвимостей:** 4 (HIGH: 4)

##### **body-parser** (Установленная версия: 1.19.0)
- **CVE-2024-45590** (HIGH):  
  Уязвимость к DoS-атакам.  
  **Рекомендация:** Обновить до 1.20.3.

##### **path-to-regexp** (Установленная версия: 0.1.7)
- **CVE-2024-45296** (HIGH):  
  ReDoS из-за неоптимальных регулярных выражений.  
  **Рекомендация:** Обновить до 0.1.10, 1.9.0 или выше.
- **CVE-2024-52798** (HIGH):  
  Неисправленный ReDoS в версиях 0.1.x.  
  **Рекомендация:** Перейти на версию 0.1.12 или выше.

##### **qs** (Установленная версия: 6.7.0)
- **CVE-2022-24999** (HIGH):  
  Прототипное загрязнение, приводящее к зависанию процесса.  
  **Рекомендация:** Обновить до 6.10.3, 6.9.7 или выше.

---

### **Общие рекомендации **
1. **Обновление зависимостей:**  
   Приоритет — обновить уязвимые пакеты до рекомендованных версий. Проверьте совместимость новых версий с вашим проектом.
2. **Тестирование:**  
   После обновления проведите тестирование, чтобы убедиться в отсутствии конфликтов.

---

## **Обнаруженные уязвимости из отчёта Semgrep SAST**
1. **Использование HTTP вместо HTTPS**:
   - Уязвимость: Передача данных через незашифрованный HTTP-протокол.
   - Категория: *Sensitive Data Exposure* (OWASP A03:2017) и *Cryptographic Failures* (OWASP A02:2021).
   - CWE: CWE-319 (*Cleartext Transmission of Sensitive Information*).
   - Рекомендация: Использовать HTTPS для шифрования передаваемых данных[1].

2. **Уязвимость регулярных выражений (ReDoS)**:
   - Уязвимость: Использование динамически создаваемых регулярных выражений может привести к блокировке основного потока приложения при обработке сложных входных данных.
   - Категория: *Denial-of-Service (DoS)*, OWASP A05:2021 и A06:2017 (*Security Misconfiguration*).
   - CWE: CWE-1333 (*Inefficient Regular Expression Complexity*).
   - Рекомендация:
     - Использовать статические регулярные выражения.
     - Если входные данные контролируются пользователем, проводить их валидацию.
     - Рассмотреть использование библиотек для проверки регулярных выражений, например, [Recheck](https://www.npmjs.com/package/recheck)[1].

---

## **Рекомендации по устранению**
1. Применить шифрование на уровне транспортного протокола (HTTPS) для всех ссылок и передаваемых данных.
2. Провести аудит всех используемых регулярных выражений:
   - Заменить динамические выражения на статические.
   - Внедрить проверку входных данных для предотвращения атак ReDoS.

--- 

### Отчет о результатах сканирования OWASP ZAP 

---

#### **Общая сводка:**
- **Высокий риск (High):** 0
- **Средний риск (Medium):** 3
- **Низкий риск (Low):** 5
- **Информационные предупреждения (Informational):** 3

---

### **Уязвимости среднего риска (Medium)**

#### 1. **Отсутствие Anti-CSRF токенов**
- **Описание:** Форма на странице не содержит токенов для защиты от CSRF-атак, что позволяет злоумышленникам выполнять действия от имени пользователя без его согласия.
- **Пример URL:** `http://127.0.0.1:8001/page_1.php` (метод POST).
- **Рекомендации:**
  - Добавить уникальные токены (например, `CSRFToken`) в формы.
  - Использовать библиотеки безопасности (например, OWASP CSRFGuard).
  - Запретить использование метода GET для операций, изменяющих состояние.

#### 2. **Отсутствие заголовка Content Security Policy (CSP)**
- **Описание:** Не настроен заголовок CSP, что повышает риск XSS-атак и внедрения вредоносного контента.
- **Примеры URL:** Главная страница, `page_1.php`, `robots.txt`, `sitemap.xml`.
- **Рекомендации:**
  - Настроить заголовок `Content-Security-Policy` на сервере.
  - Ограничить источники загрузки скриптов, стилей и других ресурсов.

#### 3. **Отсутствие заголовка X-Frame-Options**
- **Описание:** Страница может быть встроена в злоумышленный сайт (clickjacking).
- **Пример URL:** `http://127.0.0.1:8001/page_1.php`.
- **Рекомендации:**
  - Добавить заголовок `X-Frame-Options: DENY` или `SAMEORIGIN`.
  - Использовать директиву `frame-ancestors` в CSP.

---

### **Уязвимости низкого риска (Low)**

#### 1. **Раскрытие ошибок приложения**
- **Описание:** Сервер возвращает детали ошибок (например, HTTP 500), что может помочь злоумышленникам.
- **Пример URL:** `http://127.0.0.1:8001/page_1.php` (метод POST).
- **Рекомендации:**
  - Настроить кастомные страницы ошибок.
  - Логировать ошибки на сервере, не отображая их пользователям.

#### 2. **Недостаточная изоляция от Spectre**
- **Описание:** Отсутствуют заголовки для защиты от атак на основе спекулятивного исполнения (например, Spectre).
- **Пример URL:** `http://127.0.0.1:8001/page_1.php`.
- **Рекомендации:**
  - Добавить заголовки: `Cross-Origin-Resource-Policy: same-origin`, `Cross-Origin-Embedder-Policy`, `Cross-Origin-Opener-Policy`.

#### 3. **Раскрытие версии PHP через X-Powered-By**
- **Описание:** Заголовок `X-Powered-By` раскрывает версию PHP (`PHP/8.3.6`).
- **Примеры URL:** `page_1.php` (GET/POST).
- **Рекомендации:**
  - Удалить или скрыть заголовок `X-Powered-By` в настройках сервера.

#### 4. **Отсутствие заголовка X-Content-Type-Options**
- **Описание:** Браузеры могут неправильно определить MIME-тип контента.
- **Пример URL:** `http://127.0.0.1:8001/page_1.php`.
- **Рекомендации:**
  - Добавить заголовок: `X-Content-Type-Options: nosniff`.

---

### **Информационные предупреждения (Informational)**

#### 1. **Обнаружена аутентификация**
- **Описание:** Запрос содержит поля `username` и `password`.
- **Пример URL:** `http://127.0.0.1:8001/page_1.php` (метод POST).
- **Рекомендации:** Убедиться, что аутентификация защищена HTTPS и использует современные методы (например, OAuth 2.0).

#### 2. **Контент не кэшируется**
- **Описание:** Ответы сервера (например, при ошибке 500) не кэшируются.
- **Пример URL:** `http://127.0.0.1:8001/page_1.php` (POST).
- **Рекомендации:** Настроить кэширование для статических ресурсов.

---

### **Общие рекомендации**
1. **Обновить зависимости:** Убедиться, что PHP и другие компоненты обновлены.
2. **Настроить HTTPS:** Защитить передачу данных с помощью SSL/TLS.
3. **Регулярное сканирование:** Использовать инструменты вроде ZAP для периодической проверки.
4. **Обучение команды:** Провести обучение по безопасности для разработчиков.

---
