Описание сервиса Pricing
============================

# Архитектура

## Логические компоненты Pricing

- Парсинг сайтов конкурентов
    - Сбор урлов конкурентов
    - Сопоставление с номенклатурой ВИ
    - Настройка проектов парсинга и их расписания
    - Запуск проектов парсинг (в т.ч. по расписанию)
    - Парсинг 
    - Обработка и фильтрация спарсенных цен
- Расчет цен
    - Настройка проектов расчета цен
    - Расчет цен
    - Выгрузка цен в ПрайсФормер
    - Создание отчетов по рассчитанным ценам


## Стек
- Приложение на Yii2
- База данных PostgreSQL
- Сервер очередей RabbitMQ
- Дроиды-парсеры на NodeJS + Doker
- Построитель масок с встроенным тестировочным дроидом и хромом на Electron

### На данный момент задействованно 6 виртаульных машин:

1) Вебсервер (на данный момент: 10.250.17.125; 4 ядра; 10Gb RAM; )
2) БД (на данный момент: 10.250.17.126; 4 ядра; 8Gb RAM; )
3) RabbitMQ (на данный момент: 10.250.17.127; 2 ядра; 4Gb RAM; )
4) Три хоста дроидов (10.250.17.121 - 10.250.17.123; 2 ядра; 4Gb RAM;)

**Необходимо вынести из Вебсервиса:**
5) Микросервис консумер rabbitmq очереди
6) Сервис процессинга спарсенных цен
7) Сервис расчета цен

**Необходимо добавить:**
4) Реплика БД для построения отчетов


### Связанные системы
- LDAP (in)
- PDM (in)
- PriceFormer (in / out)
- Сайт (in)
- BI (out)


# Описание приложения

## Настройки сервера

Юзер: pricestat

Stack:
nginx
php7.0-fpm
nodejs

### Кронджобы

```
    */2 * * * * php /home/pricestat/app/yii util/import-items
    45 14 * * * php /home/pricestat/app/yii util/enqueue-items
    15 */2 * * * php /home/pricestat/app/yii util/reference-import
    * * * * * php /home/pricestat/app/yii util/refine-prices ; php /home/pricestat/app/yii util/buffer-prices
    * * * * * php /home/pricestat/app/yii task/file-exchange-next
    * * * * * php /home/pricestat/app/yii task/next
    * * * * * php /home/pricestat/app/yii schedule/project
    * * * * * php /home/pricestat/app/yii schedule
    0 4 * * * php /home/pricestat/app/yii util/item-update-prices
    0 10 * * * php /home/pricestat/app/yii util/item-update-prices
    30 14 * * * php /home/pricestat/app/yii util/item-update-prices
    0 5 * * * php /home/pricestat/app/yii schedule/clear-old-data
    * * * * * php /home/pricestat/app/yii util/publish-screenshots
    */10 * * * * php /home/pricestat/app/yii util/clear-rabbit
    * * * * * php /home/pricestat/app/yii util/matching-vi
    0 */3 * * * php /home/pricestat/app/yii util/matching-parsed
    * * * * * php /home/pricestat/app/yii util/calc-log
    0 15 * * * php /home/pricestat/app/yii util/item-deduplicate
    * * * * * php /home/pricestat/app/yii util/relaunch-errors
```

Описание | Жоб 
--- | ---
Импорт измененных товаров из PDM | ```*/2 * * * * php /home/pricestat/app/yii util/import-items``` 
Получение списка измененных товаров | ```45 14 * * * php /home/pricestat/app/yii util/enqueue-items``` 
Импорт справочников Пользователей User и Типов цен прайсформера PriceFormerTypes  | ```15 */2 * * * php /home/pricestat/app/yii util/reference-import``` 
Обработка спарсенных цен | ```* * * * * php /home/pricestat/app/yii util/refine-prices``` 
```deprecated```  Обработка буффера из ContentDownloader| ```php /home/pricestat/app/yii util/buffer-prices``` 
Запуск следующией задачи по имепорту/экспорту файлов | ```* * * * * php /home/pricestat/app/yii task/file-exchange-next``` 
Запуск следующей задачи из очереди задач | ```* * * * * php /home/pricestat/app/yii task/next``` 
Запуск проектов расчета по расписанию | ```* * * * * php /home/pricestat/app/yii schedule/project``` 
Запуск проектов парсинга по расписанию  | ```* * * * * php /home/pricestat/app/yii schedule``` 
Обновление цен из PDM (неск. раз в день) | ```0 4 * * * php /home/pricestat/app/yii util/item-update-prices``` ```0 10 * * * php /home/pricestat/app/yii util/item-update-prices``` ```30 14 * * * php /home/pricestat/app/yii util/item-update-prices```  
Очищение БД от устаревших данных | ```0 5 * * * php /home/pricestat/app/yii schedule/clear-old-data``` 
```deprecated``` публикация скриншотов в WebDav | ```* * * * * php /home/pricestat/app/yii util/publish-screenshots``` 
Периодическая очистка RabbitMQ от зависших очередей | ```*/10 * * * * php /home/pricestat/app/yii util/clear-rabbit``` 
Сопоставление новой номенклатуры конкурентов с сайтом ВИ (автоматическое), что не сопоставилось - уходит на ручной разбор | ```* * * * * php /home/pricestat/app/yii util/matching-vi``` 
Поиск в яндексе по сайту ВИ новопоступившей номенклатуры конкурентов | ```0 */3 * * * php /home/pricestat/app/yii util/matching-parsed``` 
Подтягивание из RabbitMQ лога истории и вставка его в базу | ```* * * * * php /home/pricestat/app/yii util/calc-log``` 
Дедубликация товаров из PDM | ```0 15 * * * php /home/pricestat/app/yii util/item-deduplicate``` 
Перезапуск парсинга или запуск перепарсинга по урлам которые не спарсились | ```* * * * * php /home/pricestat/app/yii util/relaunch-errors``` 


## Парсинг

Справочники:
- **ParsingProject** - проект парсинга
- **ParsingStatus** - статус парсинга
- **Masks** - маски парсинга

Связи: 
- **ParsingProjectMasks** - маски привязанные к проекту парсинга
- **ParsingProjectRegion** - регионы с куками привызанные к проекту парсинга

Регистры:
- **Parsing** - запуск парсинга
- **ParsingError** - ошибки парсинга
- **PriceParsed** - Спарсенные цены
- **PriceRefined** - Обработанные цены (Цены конкурентов)

Deprecated:
- **ParsingBuffer** - буффер для записи цен из ContentDownloader ***deprecated***
- **ParsingProjectProject** - связь с проектами расчета ***deprecated***

### ParsingProject
Поле | Тип | Описание 
--- | --- | --- 
id | uuid | id 
name | Строка | Название 
last_parsing_id | uuid | Последний парсинг по данному проекту
competitor_id | uuid |  Конкурент
index | int | Числовой автоинкримент
split_by | bigint | Разделить парсинг на несколько частей по указанному кол-ву урлов
max_connections | int | Одновременно потоков парсинга
rate_limit | int | Промежуток между запросами в миллисекундах
retry_timeout | int | Таймаут перед повторойной попыткой (мс)
timeout | int | Таймаут HTTP запроса (мс)
retries | int | Кол-во повторных попыток 
~~domain~~ | string |  ```deprecated``` домен
proxies | text |  Список проксей в столбик
user_agents | text |  Список юхерагентов в столбик
cookies | text |  Кукисы по умолчанию
url | text |  Список урлов для парсинга в столбик. Если указаны то используются они вместо автоурлов.
~~is_phantom~~ | boolean | ```deprecated``` Legacy флажок который говорит о том, необходим ли проекту Headless Chrome (ранее PhantomJS) или проект может использовать простые HTTP запросы (что быстрее)
is_our_regions | boolean |  Флажок который указывает использовать стандартный список регионов
ping_url | string | Пингует этот URL для проверки работоспособности проксей
parsing_type | string | Тип парсинга. normal - обычный; collecting - сбор номенклатуры; matching - Сопоставление по Яндексу    
prepare_pages | boolean | ```deprecated``` Флажок говорит дроиду о том что ему необходимо сперва пролистать все страницы (многостраничный урл), сложить их в очередь, а уже потом парсить.
parallel_droids | int | Позволяет парсить этот проект параллельно нескольким дроидам
comment | text |  Просто коммент который ни на что не влияет
source_id | integer | Source. Сайты конкурентов или Яндекс.Маркет
created_at | DateTime | Когда создан
updated_at | DateTime | Когда изменен
created_user_id | int |  Кем создан
updated_user_id | int |  Кем изменен
status_id | int | Status. активен/удален

### Masks
Поле | Тип | Описание 
--- | --- | --- 
id | uuid | id 
name | Строка | Название 
domain | string | Домен к которому применима маска
test_urls | string | Тестовые урлы
source_id | integer | Source. Сайты конкурентов или Яндекс.Маркет
bounds_json | text(json) |  конфигурация маски границ товаров
masks_json | text(json) | конфигурация масок
replaces_json | text(json) | конфигурация замен
out_of_stock_json | text(json) | конфигурация масок наличия
~~requirements~~ | string | ```deprecated```
strip_returns | bool | вырезать переносы строк (только для http запросов) 
strip_tags | bool | вырезать теги (только для http запросов) 
strip_js | bool | вырезать скрипты (только для http запросов) 
strip_spaces | bool | вырезать пробелы (только для http запросов) 
status_id | int | Status. активен/удален
special_json | text(json) | Конфигурация спец. скриптов и листалок
test_cookies | text | Тестовые куки
check_urls | bool | Проверять ли спарсенные урл? Совершается переход на этот УРЛ со всеми редиректами и из временного получется постоянный урл.
remove_utm_json | text(json) | вырезать УТМ метки из спарсенных урлов
abort_res_json | text(json) | забанить лишние запросы (напрмер к счетчикам)
date_parse_json | text(json) | Конфигурация парсинга даты
created_at | DateTime | Когда создан
updated_at | DateTime | Когда изменен
created_user_id | int |  Кем создан
updated_user_id | int |  Кем изменен

### Parsing
Поле | Тип | Описание 
--- | --- | --- 
id | uuid | id 
name | Строка | Название 
~~domain~~ | string |  ```deprecated``` домен
~~is_phantom~~ | boolean |  ```deprecated``` Legacy флажок который говорит о том, необходим ли проекту Headless Chrome (ранее PhantomJS) или проект может использовать простые HTTP запросы (что быстрее)
parsing_type | string | Тип парсинга. normal - обычный; collecting - сбор номенклатуры; matching - Сопоставление по Яндексу    
prepare_pages | boolean | ```deprecated``` Флажок говорит дроиду о том что ему необходимо сперва пролистать все страницы (многостраничный урл), сложить их в очередь, а уже потом парсить.
parsing_project_id | uuid | ParsingProject
region_id | int | Region
parsing_status_id | int | ParsingStatus
total_count | int | Кол-во всего урлов
parsed_count | int | Кол-во товаров спарсено
errors_count | int | Кол-во ошибок парсинга
requests_count | int | Кол-во запросов
connected_count | int | Кол-во соединений
success_count | int | Кол-во успешно спарсенных урлов
unreached_count | int | Кол-во провалившихся соединений
with_retries_count | int | Кол-во запросов вместе с повторными попытками
in_stock_count | int | Кол-во товаров в налчии
passed_filter_count | int | Кол-во прошедших фильтр при обработке спарценных цен
started_at | DateTime | Дата-время начала
finished_at | DateTime | Дата-время конца
~~is_chain~~ | bool | ```deprecated```
scope_info | text(json) | Инфо для подтягивания урлов в парсинг, и клонирования парсинга
settings_json | text(json) | Инфо о настройках проекта парсига на момент запуска
regions | string | Айдишники регионов через запятую. В этих регионах будет создан экземпляр спарсенной цены
source_id | integer | Source. Сайты конкурентов или Яндекс.Маркет
created_at | DateTime | Когда создан
updated_at | DateTime | Когда изменен
created_user_id | int |  Кем создан
updated_user_id | int |  Кем изменен
status_id | int | Status. активен/удален
index | int | Числовой автоинкримент

### PriceParsed
Поле | Тип | Описание 
--- | --- | --- 
id | uuid | id 
created_at | DateTime | Когда создан
price | string  |  Спарсенная цена товара
extracted_at | DateTime |  Дата парсинга
source_id | int | Источник
item_id | uuid | ID товара
competitor_id | uuid |  ID Конкурента
competitor_shop_name | string | Название магазина
competitor_shop_domain | string | Домен магазина
competitor_item_name | string | Названия товара у конкурента
competitor_item_sku | string | Артикул товара у конкурента
url | string | Урл парсинга
out_of_stock | string | Неналичие
delivery | string | Инфа о доставке
parsing_project_id | uuid | ID проекта парсинга
parsing_id | uuid | ID парсинга
price_parsed_status_id | int | Статус спарсенной цены PriceParsedStatus
error_message | string | ошибка
screenshot |string  | скрин
index | int | Индекс
robot_id | string | ID хоста дроидов
proxy | string | Прокси с которой парсился данный урл
competitor_item_count | int | Кол-во товаров
time_ms |bigint  | время
regions | string | Регионы для которых нужно создать PriceRefined
competitor_item_url | string | Урл товара у конкурента
original_url | string | Исходный урл (до редиректов и листаний)
delivery_days | int | Кол-во дней доставки

### PriceRefined
Поле | Тип | Описание 
--- | --- | --- 
id | uuid | id 
created_at | DateTime | Когда создан
price | float  | Цена товара
extracted_at | DateTime | Дата парсинга
source_id | int | Источник
regions | int | Регион
item_id | uuid | ID товара
competitor_id | uuid |  ID Конкурента
price_parsed_id | uuid | ID спарсенной цены
competitor_shop_name | string | Название магаза
out_of_stock | bool | Флаг не наличия товара
index | int | Индекс
screenshot | string | Урл скриншота на цену
url | string | Урл
robot_id | string | ID хоста дроидов
delivery_days | int | Кол-во дней доставки

## Расчет цен

Справочники:
- **Project** - проект расчета цены
- **PriceFormerType** - типы цен прайсформера
- **PriceExportMode** - тип выгрузки цены в прайсформер
- **ProjectExecutionStatus** - статус запуска проекта расчета
- **SelectPriceLogic** - логика выбора цены для расчета

Связи: 
- **ProjectCompetitor** - конкуренты проекта
- **ProjectCompetitorBrand** - бренды конкурентов проекта
- **ProjectCompetitorCategory** - категории товаров конкурентов проекта
- **ProjectItem** - товары в проекте расчета
- **ProjectRegion** - регионы проекта расчета
- **ProjectSource** - источинки проектов расчета
- **ProjectTheme** - категория проекта расчта

Регистры:
- **PriceRefined** - Обработанные цены (Цены конкурентов)
- **PriceCalculated** - Расчитанные цены
- **ProjectExecution** - запуск проекта расчета
- **LogPriceCalculation** - лог расчета каждой цены, включая учавствовавших конкурентов
- **LogProjectExecution** - лог рачитанных цен


Развертывание окружения
============================

Всё очень плохо. Старый как говно мамонта Вагрант. Не уверен что на данный момент оно вообще поднимется.

Требования
------------

Установленный [Vagrant](https://www.vagrantup.com/downloads.html)



Установка
------------

1) Создать папку pricing
~~~
mkdir pricing
~~~

2) Зайти в нее
~~~
cd pricing
~~~

3) Cклонировать репозиторий [git@git.vseinstrumenti.net:pricestat/pricing-env.git](https://git.vseinstrumenti.net/pricestat/pricing-env)
~~~
git clone git@git.vseinstrumenti.net:pricestat/pricing-env.git
~~~

4) Зайти в появившуюся папку pricing-env
~~~
cd pricing-env
~~~

5) Запустить vagrant up
~~~
vagrant up
~~~

6) Дождаться завершения. Много времени займет действие:
~~~
==> default:   * execute[install-composer-project-dependencies] action run
~~~
Это нормально.


7) Убедиться что появилась папка pricing-app. В ней находится GIT репозиторий исходников с которым и будем рабоать.
```
pricing/
|-- pricing-env/
|-- pricing-app/
   |-- assets/             contains assets definition
   |-- commands/           contains console commands (controllers)
   |-- config/             contains application configurations
   |-- controllers/        contains Web controller classes
   |-- mail/               contains view files for e-mails
   |-- models/             contains model classes
   |-- runtime/            contains files generated during runtime
   |-- tests/              contains various tests for the basic application
   |-- vendor/             contains dependent 3rd-party packages
   |-- views/              contains view files for the Web application
   |-- web/                contains the entry script and Web resources

```
8) Добавить в свой GIT клиент этот существующий локальный репозиторий.

9) Прописать в хостах
```
192.168.56.101  www.pricing.local
192.168.56.101  pricing.local
192.168.56.101  pricing
```

10) В браузере открыть http://pricing.local/
```
Логин : pricing-admin
Пароль: 111111
```


## Снятие бекапа с боевой БД для теста

```bash
$ ssh super-admin@pricing-test.vseinstrumenti.ru
$ sudo su deploy
$ cd ~/www
$ docker-compose -f docker-compose.dev.yml down
$ rm -rf .pg_data
$ docker-compose -f docker-compose.dev.yml up -d
$ docker-compose -f docker-compose.dev.yml exec postgres bash
$ pg_dump -h 10.250.17.128 -U postgres -W -c -C -v \
  --exclude-table-data=prc_robot \
  --exclude-table-data=prc_schedule \
  --exclude-table-data=prc_task \
  --exclude-table-data=prc_exchange_export \
  --exclude-table-data=prc_exchange_import \
  --exclude-table-data=prc_error \
  --exclude-table-data=prc_parsing \
  --exclude-table-data=prc_price_parsed \
  --exclude-table-data=prc_parsing_buffer \
  --exclude-table-data=prc_log_price_calculation \
  --exclude-table-data=prc_log_project_execution \
  --exclude-table-data=prc_error \
  --exclude-table-data=prc_log_kpi \
  --exclude-table-data=prc_price_calculated \
  --exclude-table-data=prc_anti_captcha_task \
  pricing | psql -U postgres pricing
```
