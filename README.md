See English readme [below](#1-system-requirements).

1. Требования к CMS
-------------------

* Версия UMI.CMS: 2.9.7 и выше.
* Редакция: "Сommerce" или "Shop".

2. Установка модуля 
-------------------

**Через UMI.Market**

1. Перейдите на [страницу модуля Convead](http://market.umi-cms.ru/module/convead/) в UMI.Market.
2. Нажмите синюю кнопку "Установить бесплатно".
3. В появившемся окне введите лицензионный ключ вашей CMS.
4. Запустите принудительное обновление в административной панели вашего магазина на UMI.CMS.

**Вручную через FTP**

1. [Скачайте архив модуля](https://d2p70fm3k6a3cb.cloudfront.net/public/plugins/umi/convead-1.0.0.0.tar) из нашего репозитория.
2. Распакуйте архив с модулем и загрузите его содержимое через FTP в корень вашего сайта. Замените все существующие файлы при необходимости.
3. Перейдите в раздел «Конфигурация» → «Модули» в административной панели магазина.
4. Укажите путь до инсталляционного файла: `classes/modules/convead/install.php` и нажмите кнопку «Установить».

После установки модуля перейдите на страницу его настройки и впишите в соответствующее поле API-ключ вашего аккаунта Convead.

3. Интеграция в шаблоны
-----------------------

Добавьте следующую строку в шаблон вашего магазина перед закрывающим тегом `</head>`:

Для PHP-шаблонизатора:  

```php
<?= $this->macros('convead', 'getConveadScript'); ?>
```

Для XSLT-шаблонизатора:  
```xslt
<xsl:value-of select="document('udata://convead/getConveadScript/')/udata" disable-output-escaping="yes" />
```

Для TPL-шаблонизатора:  

```php
%convead getConveadScript()%
```

----------------------

1. System requirements
----------------------

* UMI.CMS version: 2.9.7 and above.
* CMS Editions: "Сommerce" or "Shop".

2. Installing module
--------------------

**From UMI.Market**

1. Navigate to [Convead module page](http://market.umi-cms.ru/module/convead/) at UMI.Market.
2. Click blue button "Install for free".
3. Type in your CMS licence key into the pop-up.
4. Launch force update in admin panel of your UMI.CMS shop.

**Manually via FTP**

1. [Download module archive](https://d2p70fm3k6a3cb.cloudfront.net/public/plugins/umi/convead-1.0.0.0.tar) from our repository.
2. Unpack the archive and upload its contents via FTP to the root folder of your site. Replace all existing files if necessary.
3. Navigate to the "Configuration" → "Modules" section in admin panel of your shop.
4. Specify an installation path: `classes/modules/convead/install.php` and press "Install" button.

After module installation navigate to its setting page and type in your Convead account API-key into a corresponding field.

3. Templates integration
------------------------

Add the following code into your shop's template before closing `</head>` tag:

For PHP-engine templates:  

```php
<?= $this->macros('convead', 'getConveadScript'); ?>
```

For XSLT-engine templates:  
```xslt
<xsl:value-of select="document('udata://convead/getConveadScript/')/udata" disable-output-escaping="yes" />
```

For TPL-engine templates:  

```php
%convead getConveadScript()%
```
