<?PHP

require dirname(__FILE__) . "/engine/Core_modules/InstallUpdate.php";

require_once(ROOT_DIR.'/language/'.$config['langs'].'/car-market.lng');
require_once ENGINE_DIR.'/car-market/other_fields_array.php';
require_once ENGINE_DIR.'/car-market/version.php';
require(ENGINE_DIR . "/Core_modules/TemplateAdmin.php");
require(ENGINE_DIR . "/car-market/functions.php");

$tpl = new TemplateAdmin();
Func::$tpl =& $tpl;
$tpl->echo = FALSE;
		
$version = VERSION;
define('MODULE_NAME', 'Car-Market(Авторынок)');
$licence = /*lic*/'.'/*/lic*/;
define('CONFIG_VARNAME', 'car_conf');
define('CONFIG_FILE', 'car-market_conf.php');
define('REQUIRED_DLE', 5.3);
define('REQUIRED_PHP', 5.0);
define('REQUIRED_MYSQL', 4.1);
define('YEAR', 2007);
$image_patch = "engine/car-market/images/install";
$lic = new Licencing($licence);
$important_files = array();

$text_main = <<<HTML
<b>Основные возможности:</b>
- Скрипт ставиться как отдельный модуль и требует минимум изменений файлов скрипта
- Модуль имеет гибкие настройки, которые можно проводить из админки или путём изменения шаблонов
- Использует для хранения данных MySQL
- Мощная система безопасности
- Минимальная нагрузка на базу данных (от 0 до 5 запросов)
- Использование продвинутой технологии AJAX.
- Поддержка ЧПУ (человеко-понятный URL) позволяет использовать тему статьи или любой другой текст в URL броузера, благодаря этому индексация сайта в поисковых системах будет более качественной (необходим modrewrite)
- Возможность отключение ЧПУ
- Общая статистика объявлений
- Создание марок автомобилей и моделей
- Создание стран, регионов и городов
- Возможность использование только некотрые сущности мз стран/регонов/городов
- Поиск автомобилей по любым параметрам
- Мощный, полностью настраивающийся фильтр автомобилей
- Поддержка нескольких языков
- Вывод новых автомобилей в отдельном блоке
- Вывод срочных объявлений в отдельном блоке(задаются админом)
- Добавление, редактирование и удаление авто
- Ограничение сущестования объявления на сайте и в блоке "срочные объявления"
- Кэширование практически всех страниц, что намного поднимает скорость работы скрипта и уменьшает нагрузку на SQL сервер
- Возможность загружать фотографии авто (количество для каждой группы настраивается в админки)
- Расширенная настройка дополнительных полей (создание своих пока не предусмотренно)
- Возможность выделять один из стран/регионов на главной
- Сортировка по нескольким параметрам
- RSS канал практически по любым параметрам
- Добавление объявлений пользователями с последующей модерацией или без (настройки)
- Администратор может назначать группы пользователей которые смогут модерировать объявления
- Полностью задействован тег &lt;title&gt;
- Встараивается в стандартный SpeedBar скрипта
- Несколько вариантов просмотра фотографий
- Возможность сравнить автомобили по параметрам
- Функции очисти кэша и пересчета показателей
- У каждой группы пользователей разные возможности и разрешшеное количество фотографий
- Возможность редактирование объявлений ользоателями (настройки)
- Практически каждому тегу {tag} соотвествует свой блок [tag][/tag], для убирание текста, если значение отсутствует
- Тесное взаимодействие с гостями, при определённых настройках они могу добавлять и редактировать свои объявление не регистрируясь
- У каждого пользователя есть личный кабинет с его объявлениями, блокнотом (избранные) и настройками фильтра
- Модуль может быть установлен на гавную страницу сайта
- Возможность указывать обязательные поля при добавлении (только остновные поля)
- Функция отправки мыло автору объявления сразу с сайта
- Функция печати объявления
- Выбор цвета автомобиля происходит с помощью ColorPiker
- Функция продливания объявления на сайте для разрешенных групп (настройки)
- Динамически блок, который отображает разные данные при различных действиях пользователя (настраивается в админки)
- Блок поиска и статистики
- Фукнция регистрации гостя во врремя добавления объявления (настрофки)
- Добавлена сортировка значений дополнительных полей в админки
- Различные виды просмотра объявлений (табличный/расширенный)
- Система дополнительных полей
- Предварительная загрузка фотографии
- И многое другое
HTML;
$text_main = nl2br($text_main);

if ($_POST['type'] == "update")
{
	$obj = new install_update(MODULE_NAME, $version, array(), $licence, $db, $image_patch);
	$obj->year = YEAR;
	require(ENGINE_DIR . "/data/" . CONFIG_FILE);
	$module_config = ${CONFIG_VARNAME};
	
	switch ($module_config['version_id'])
	{
		case VERSION:
			$obj->Finish("<div style=\"text-align:center;font-size:150%;\">Вы используете актуальную версию скрипта. Обновление не требуется</div>");
			break;
			
		case '2.3.0':
	       $to_version = VERSION;
            $obj->steps_array = array(
                                    "ChangeLog",
                                    "Проверка хостинга",
                                    "Работа с базой данных",
                                    "Завершение обновления"
                                    );
                                    $ChangeLog = <<<TEXT
<b>Обновление до версии $to_version</b>
            
[+] - JavaScript проверка заполнения дополнительных полей
[fix] - Добавлена несколько вариантов заполнения

TEXT;
                                    $ChangeLog = nl2br($ChangeLog);
                                    $important_files = array(
                        './install.php',
                        './engine/data/',
                                    );
                                    
                                    $table_schema[] = "ALTER TABLE `" . PREFIX . "_auto_images` add column `user_id` INT(10) UNSIGNED DEFAULT '0' NOT NULL";
                                    $table_schema[] = "ALTER TABLE `" . PREFIX . "_auto_images` add column `guest_session` VARCHAR(32) DEFAULT '' NOT NULL";
                                    
                                    $module_config = array_merge($module_config, array('photo_upload_type' => "1"));
                                    
                                    
                                    $finish_text = <<<HTML
<div style="text-align:center;">Обновление модуля до версии $to_version прошло успешно.</div>
HTML;
                                    switch (intval($_POST['step']))
                                    {
                                        case 0:
                                            $obj->Main($ChangeLog, 'Начать обновление');
                                            break;

                                        case 1:
                                            $obj->CheckHost($important_files, REQUIRED_DLE, REQUIRED_PHP, REQUIRED_MYSQL);
                                            break;
                                            
                                        case 2:
                                            $obj->Database($table_schema);
                                            break;
                                                
                                        case 3:
                                            $obj->ChangeVersion(CONFIG_FILE, CONFIG_VARNAME, $module_config, array(), $to_version);
                                            $obj->Finish($finish_text, $to_version);
                                            break;
                                    }
		    break;
			
		case "2.2.0":
		    $to_version = '2.3.0';
            $obj->steps_array = array(
                                    "ChangeLog",
                                    "Проверка хостинга",
                                    "Работа с базой данных",
                                    "Завершение обновления"
                                    );
                                    $ChangeLog = <<<TEXT
<b>Обновление до версии $to_version</b>
            
[+] - Опция обмена
[+] - поддержка более новых версий ДЛЕ 
[+] - возможность поиска сразу по нескольким моделям и городам 
[+] - система дополнительных полей
[+] - разграничение доступа к разделам в админке модуля
[+] - случайный вывод авто в блоке срочных объявлений с кешированием
[+] - извещение пользователю о продлении объявления
[+] - сортировка по дате окончания строка
[+] - при установки можно выбрать импорт регионов/городов только определённой страны/региона
[fix] - улучшена загрука фотогрфафий
[fix] - крон теперь запускается автоматически
[fix] - исправлены все найденные ошибки

TEXT;
                                    $ChangeLog = nl2br($ChangeLog);
                                    $important_files = array(
                        './install.php',
                        './engine/data/',
                                    );
                                    
                                    $table_schema[] = "ALTER TABLE `" . PREFIX . "_auto_autos` add column `exchange` TINYINT(1) DEFAULT '0' NOT NULL";
                                    $table_schema[] = "ALTER TABLE `" . PREFIX . "_auto_autos` add column `xfields` MEDIUMTEXT NOT NULL";
                                    
                                    $table_schema[PREFIX . "_auto_fields"] = "CREATE TABLE `" . PREFIX . "_auto_fields` (
                                                                               `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
                                                                              `title` VARCHAR(110) NOT NULL DEFAULT '',
                                                                              `description` VARCHAR(255) NOT NULL DEFAULT '',
                                                                              `type` ENUM('text','select','textarea','checkbox') DEFAULT NULL,
                                                                              `data` MEDIUMTEXT,
                                                                              `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
                                                                              `regex` VARCHAR(55) NOT NULL DEFAULT '',
                                                                              `default` VARCHAR(255) DEFAULT NULL,
                                                                              `active` tinyint(1) unsigned NOT NULL default '1',
                                                                              PRIMARY KEY  (`id`)
                                                                         ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
                                    
                                    $module_config = array_merge($module_config, array('user_send_mail' => "0",
                                                                                'block_hot_auto_time' => "",
                                                                                'admin_main' => array (
                                                                                                        '0' => "1",
                                                                                                        ),
                                                                                'admin_add' => array (
                                                                                                        '0' => "1",
                                                                                                        ),
                                                                                'admin_edit' => array (
                                                                                                        '0' => "1",
                                                                                                        ),
                                                                                'admin_city' => array (
                                                                                                        '0' => "1",
                                                                                                        ),
                                                                                'admin_model' => array (
                                                                                                        '0' => "1",
                                                                                                        ),
                                                                                'admin_fields' => array (
                                                                                                        '0' => "1",
                                                                                                        ),
                                                                                'admin_settings' => array (
                                                                                                        '0' => "1",
                                                    )));
                                    
                                    
                                    $finish_text = <<<HTML
<div style="text-align:center;">Обновление модуля до версии $to_version прошло успешно.</div>
HTML;
                                    switch (intval($_POST['step']))
                                    {
                                        case 0:
                                            $obj->Main($ChangeLog, 'Начать обновление');
                                            break;

                                        case 1:
                                            $obj->CheckHost($important_files, REQUIRED_DLE, REQUIRED_PHP, REQUIRED_MYSQL);
                                            break;
                                            
                                        case 2:
                                            $obj->Database($table_schema);
                                            break;
                                                
                                        case 3:
                                            $obj->ChangeVersion(CONFIG_FILE, CONFIG_VARNAME, $module_config, array(), $to_version);
                                            $obj->Finish($finish_text, $to_version);
                                            break;
                                    }
                                    break;
			
		case "2.1.0":
            $to_version = '2.2.0';
            $obj->steps_array = array(
                                    "ChangeLog",
                                    "Проверка хостинга",
                                    "Завершение обновления"
                                    );
                                    $ChangeLog = <<<TEXT
<b>Обновление до версии $to_version</b>
            
[+] - добавлен блок поиска 
[+] - добавлен блок статистики 
[+] - статус авто в акаунте пользователя 
[+] - массовые действия с объявлениями 
[+] - сортировка по автору 
[+] - фильтр по автору в админке 
[+] - использование метатегов на страницах модуля 
[+] - Экспорт объявлений в Яндекс.Авто
[fix] - удаление авто с сайта


TEXT;
                                    $ChangeLog = nl2br($ChangeLog);
                                    $important_files = array(
                        './install.php',
                        './engine/data/',
                                    );
                                    $finish_text = <<<HTML
<div style="text-align:center;">Обновление модуля до версии $to_version прошло успешно.</div>
HTML;
                                    switch (intval($_POST['step']))
                                    {
                                        case 0:
                                            $obj->Main($ChangeLog, 'Начать обновление');
                                            break;

                                        case 1:
                                            $obj->CheckHost($important_files, REQUIRED_DLE, REQUIRED_PHP, REQUIRED_MYSQL);
                                            break;
                                                
                                        case 2:
                                            $obj->ChangeVersion(CONFIG_FILE, CONFIG_VARNAME, $module_config, array(), $to_version);
                                            $obj->Finish($finish_text, $to_version);
                                            break;
                                    }
                                    break;
			
	    case "2.0.0":
        case "2.0.5":
            $to_version = '2.1.0';
            $obj->steps_array = array(
                                    "ChangeLog",
                                    "Проверка хостинга",
                                    "Работа с базой данных",
                                    "Завершение обновления"
                                    );
                                    $ChangeLog = <<<TEXT
<b>Обновление до версии $to_version</b>
            
[+] - добавлен счетчик просмотров объявлений
[+] - добавлено форматирование чисел
[+] - добавлена колонка автора в списке авто (админка)
[+] - возможность устанавливать на последние версии ДЛЕ

TEXT;
                                    $ChangeLog = nl2br($ChangeLog);
                                    $table_schema[PREFIX . "_auto_autos"] = "ALTER TABLE `" . PREFIX . "_auto_autos` add column `view_count` smallint(5) DEFAULT '0' NOT NULL";
                                    $important_files = array(
                        './install.php',
                        './engine/data/',
                                    );
                                    $finish_text = <<<HTML
<div style="text-align:center;">Обновление модуля до версии $to_version прошло успешно.</div>
HTML;
                                    switch (intval($_POST['step']))
                                    {
                                        case 0:
                                            $obj->Main($ChangeLog, 'Начать обновление');
                                            break;

                                        case 1:
                                            $obj->CheckHost($important_files, REQUIRED_DLE, REQUIRED_PHP, REQUIRED_MYSQL);
                                            break;
                                                
                                        case 2:
                                            $obj->database($table_schema);
                                            break;
                                                
                                        case 3:
                                            $obj->ChangeVersion(CONFIG_FILE, CONFIG_VARNAME, $module_config, array(), $to_version);
                                            $obj->Finish($finish_text, $to_version);
                                            break;
                                    }
                                    break;
			
		default:
			$text = <<<TEXT
<b>Не известная версия модуля. Переустановите модуль.</b>
TEXT;
			$obj->OtherPage($text);
			break;
	}
}
else 
{
	$title = array(
					"Описание модуля",
                    "Лицензионное соглашение",
                    "Проверка хостинга",
                    "Страны/Регионы/Города",
                    "Создание файла настроек",
                    "Заполнение базы",
                    "Работа с базой данных",
                    "Завершение установки"
				);
				
	$obj = new install_update(MODULE_NAME, $version, $title, $licence, $db, $image_patch);
	$obj->year = YEAR;

	switch ($_POST['step'])
	{
	    case 1:
	        $module_name = MODULE_NAME;
	        $head_licence = <<<HTML
Пожалуйста внимательно прочитайте и примите пользовательское соглашение по использованию модуля "$module_name".
HTML;

	        $text_licence = <<<HTML
Покупатель имеет право:</b><ul><li>Изменять дизайн и структуру программного продукта в соответствии с нуждами своего сайта.</li><br /><li>Производить и распространять инструкции по созданным Вами модификациям шаблонов и языковых файлов, если в них будет иметься указание на оригинального разработчика программного продукта до Ваших модификаций.</li><br /><li>Переносить программный продукт на другой сайт после обязательного уведомления меня об этом, а также полного удаления скрипта с предыдущего сайта.</li><br /></ul><br /><b>Покупатель не имеет право:</b><br /><ul><li>Передавать права на использование интеграции третьим лицам, кроме случаев, перечисленных выше в нашем соглашении.</li><br /><li>Изменять структуру программных кодов, функции программы или создавать родственные продукты, базирующиеся на нашем программном коде</li><br /><li>Использовать более одной копии модуля <b>$module_name</b> по одной лицензии</li><br /><li>Рекламировать, продавать или публиковать на своем сайте пиратские копии модуля</li><br /><li>Распространять или содействовать распространению нелицензионных копий модуля <b>$module_name</b></li><br /></ul>
HTML;
	        
			$obj->Licence($head_licence, $text_licence);
			
			
		case 2:
		    $important_files = array(
						'./install.php',
                        './engine/data/',
                        './uploads/auto_foto/',
                        './engine/car-market/cache/',
                        './engine/car-market/cache/array/',
                        './engine/car-market/logs/database.log',
                        './engine/car-market/logs/errors.log',
                        './engine/car-market/logs/HandlerErrors.log',
						);
						
            foreach ($other_fields_array as $field)
            {
                $important_files[] = './engine/car-market/array/'.$field['file'].'.php';
            }
		    
			$obj->CheckHost($important_files, REQUIRED_DLE, REQUIRED_PHP, REQUIRED_MYSQL);
			
		case 3:
		    $text_use = <<<HTML
<div style="padding:15px;" >
<input type="checkbox" value="1" name="fields[use_country]" style="vertical-align:middle" />&nbsp; Использовать страны в модуле<br/>
<input style="vertical-align:middle" type="checkbox" value="1" name="fields[use_region]" />&nbsp; Использовать регионы в модуле<br/>
<input style="vertical-align:middle" type="checkbox" checked="checked" disabled="disabled" />&nbsp; Использовать города в модуле
</div>	
HTML;
        	$use_status = "Изменить эти параметры в сторону увеления потом будет невозможно";
        	
        	function CheckUse(install_update $obj)
        	{
        	    if (!empty($_POST['use_country']))
        	    {
        	        $obj->SetAdditionalField('use_country', 1);
        		}
        		if (!empty($_POST['use_region']))
        		{
        		    $obj->SetAdditionalField('use_region', 1);
        		}
        		
        		return false;
        	}
        	
		    $obj->OtherPage($text_use, $use_status, 'CheckUse');
		    
        case 4:
            $settings = array(
                            'block_last_allow' => "1",
                            'block_last_count_auto' => "5",
                            'block_last_auto_photo' => "1",
                            'block_last_auto_user' => "0",
                            'block_hot_allow' => "1",
                            'block_hot_count_auto' => "2",
                            'block_hot_auto_photo' => "0",
                            'block_dimanic_allow' => "1",
                            'block_dimanic_on_main_site' => "3",
                            'block_dimanic_on_main_module' => "0",
                            'block_dimanic_on_add' => "2",
                            'block_dimanic_on_search' => "3",
                            'block_dimanic_on_default' => "4",
                            'user_int_allow_add' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            '3' => "4",
                            '4' => "5",
                            ),
                            'user_int_allow_no_code' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            '3' => "4",
                            ),
                            'user_int_allow_no_moder' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            '3' => "4",
                            ),
                            'user_int_allow_edit' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            '3' => "4",
                            ),
                            'user_int_allow_del' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            '3' => "4",
                            ),
                            'user_int_allow_extend' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            ),
                            'user_int_allow_change_exp' => array (
                            '0' => "1",
                            '1' => "2",
                            '2' => "3",
                            '3' => "4",
                            ),
                            'user_int_default_day_count' => "0",
                            'count_photo' => array (
                            '1' => "16",
                            '2' => "8",
                            '3' => "6",
                            '4' => "4",
                            '5' => "4",
                            ),
                            'user_int_default_sort' => "date",
                            'user_int_default_subsort' => "DESC",
                            'user_int_show_counter' => "1",
                            'user_int_show_marks_no_auto' => "1",
                            'user_int_pre_page' => array (
                            'table' => "25",
                            'modern' => "10",
                            ),
                            'photo_size_byte' => "100",
                            'photo_size_width' => "600",
                            'photo_size_width_th' => "150",
                            'photo_quality' => "85",
                            'photo_logo' => "1",
                            'photo_size_for_logo' => "500",
                            'general_allow_module' => "1",
                            'general_name_module' => "car-market",
                            'general_allow_add' => "1",
                            'general_mod_rewrite' => "1",
                            'general_AJAX' => "1",
                            'general_cache' => "1",
                            'general_main_page' => "0",
                            'general_RSS' => "1",
                            'general_inform' => "1",
                            'general_email' => "kaliostro@kaliostro.net",
                            'general_view_mode' => "table",
                            'general_main_country' => "0",
                            'need_field' => array (
                            'race' => "1",
                            'contact_person' => "1",
                            'year' => "1",
                            'phone' => "1",
                            ),
                            'currency' => array (
                            'RUR' => "33",
                            'EUR' => "0.8",
                            'USD' => "1",
                            ),
                            'general_moderator' => array (
                            '0' => "1",
                            '1' => "2",
                            ),
                            'general_show_moder' => "0",
                            'general_allow_reg' => "0",
                            'general_count_main_auto' => "6",
                            'general_auto_photos' => "1",
                            'general_allow_statistic' => "1",
                            'count_yandex_export' => "150",
                            'general_allow_block_statistic' => "0",
                            'general_allow_block_search' => "0",
                            'general_debug' => "0",
                            'user_send_mail' => "0",
                            'block_hot_auto_time' => "",
                            'admin_main' => array (
                                                    '0' => "1",
                                                    ),
                            'admin_add' => array (
                                                    '0' => "1",
                                                    ),
                            'admin_edit' => array (
                                                    '0' => "1",
                                                    ),
                            'admin_city' => array (
                                                    '0' => "1",
                                                    ),
                            'admin_model' => array (
                                                    '0' => "1",
                                                    ),
                            'admin_fields' => array (
                                                    '0' => "1",
                                                    ),
                            'admin_settings' => array (
                                                    '0' => "1",
                                                    ),
                            'use_country' => (empty($obj->fields['use_country']))?0:1,
                            'use_region' => (empty($obj->fields['use_region']))?0:1,
                            );
        
            $auto = new Spacer($settings, $lang_car);
            Func::$obj =& $auto;
            
            $obj->setting_menu = array(
				$auto->lang['block1_title'] => '/engine/car-market/images/admin/submenu/block1.png', 
				$auto->lang['block2_title'] => '/engine/car-market/images/admin/submenu/block2.png', 
				$auto->lang['block3_title'] => '/engine/car-market/images/admin/submenu/block3.png', 
				$auto->lang['user_title'] => '/engine/car-market/images/admin/submenu/user.png', 
				$auto->lang['photo_title'] => '/engine/car-market/images/admin/submenu/photo.png', 
				$auto->lang['general_title']=> '/engine/car-market/images/admin/submenu/setting.png'
				);
        	
        	require(ENGINE_DIR . "/car-market/admin/settings_array.php");
        	
			$obj->Settings($settings_array, $settings, CONFIG_VARNAME, CONFIG_FILE);
			
			$obj->setting_menu = array();
			
		case 5:
		            $fill_country = $fill_regions = '';
		            $fill_cities = '<input style="vertical-align:middle" type="checkbox" name="cities" value="1" OnClick="UseCity(this)" />&nbsp; Заполнть базу данных городов (10912 городов)';
                    if ($obj->fields['use_country'])
                    {
                        $fill_country = '<input style="vertical-align:middle" type="checkbox" value="1" OnClick="UseCountry(this)" name="countries" />&nbsp; Заполнить базу данных стран (105 стран)<br/>';
                    }

                    if ($obj->fields['use_region'])
                    {
                        if ($obj->fields['use_country'])
                        {
                            $fill_regions = '<input style="vertical-align:middle" type="checkbox" OnClick="UseRegion(this)" value="1" name="regions" />&nbsp; Заполнить базу данных регионов (913 регионов)<br/>';
                        }
                        else 
                        {
                            $fill_regions = '<input style="vertical-align:middle" type="checkbox" OnClick="UseRegion(this)" value="1" name="regions" /> Заполнить БД регионов cтраны : <select name=\'country_id\'><option value=\'\'>Регионы всех стран</option>\n';
                            
                            include_once(ENGINE_DIR . "/car-market/import/import_countries.php");
                        
                            foreach ($countries as $id=>$country)
                            {
                                $fill_regions .= "<option value='$id'>{$country['name']}</option>\n";
                            }
                            
                            $fill_regions .= "</select><br/>";
                        }
                    }
                    else 
                    {
                        if (!$obj->fields['use_country'])
                        {
                            $fill_cities = '<input style="vertical-align:middle" type="checkbox" name="cities" value="1" OnClick="UseCity(this)" /> Заполнить БД городов региона : <select name=\'region_id\'><option value=\'\'>Города всех регионов</option>\n';
                            
                            include_once(ENGINE_DIR . "/car-market/import/import_regions.php");
                            include_once(ENGINE_DIR . "/car-market/import/import_countries.php");
                        
                            foreach ($regions as $cid=>$region_a)
                            {
                                $fill_cities .= "<optgroup label='{$countries[$cid]['name']}'>";
                                foreach ($region_a as $rid => $region)
                                {
                                    $fill_cities .= "<option value='$rid'>{$region['name']}</option>\n";
                                }
                                $fill_cities .= "</optgroup>";
                            }
                            
                            $fill_cities .= "</select><br/>";
                        }
                    }

                    $fill_database = <<<HTML
<div style="padding:15px;" >
<input type="checkbox" value="1" name="marks" style="vertical-align:middle" />&nbsp; Заполнть базу данных марок и моделей (123 марки и 1542 модели)<br/> $fill_country$fill_regions$fill_cities
</div>  
<script type="text/javascript">
var form = document.form;
function UseCity(obj)
{
    if (obj.checked)
    {
        if (form.countries)
            form.countries.checked = true;
            
        if (form.regions)
            form.regions.checked = true;
    }
}
function UseRegion(obj)
{
    if (obj.checked)
    {
        if (form.countries)
            form.countries.checked = true;
    }
    else
        form.cities.checked = false;
}
function UseCountry(obj)
{
    if (!obj.checked)
    {
        if (form.regions)
            form.regions.checked = false;
        form.cities.checked = false;
    }
}
</script>
HTML;
            
            	$fill_database_status = "Изменить эти параметры в сторону увеления потом будет невозможно";
            	
            	function CheckFillDataBase(install_update $obj)
            	{
            	    foreach (array('marks', 'countries', 'regions', 'cities', 'country_id', 'region_id') as $value)
            	    {
                	    if (!empty($_POST[$value]))
                		{
                		    $obj->SetAdditionalField($value, $_POST[$value]);
                		}
            	    }
           		
            		return false;
            	}
            	
		        $obj->OtherPage($fill_database, $fill_database_status, 'CheckFillDataBase');
		    
		    
        case 6:
            if (!empty($obj->fields['use_country']))
            {
                $table_schema[PREFIX . "_auto_countries"] = "CREATE TABLE `" . PREFIX . "_auto_countries` (
                      `id` int(10) unsigned NOT NULL auto_increment,  
                      `name` varchar(255) NOT NULL default '',        
                      PRIMARY KEY  (`id`)                  
                 ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
            }
            
            if (!empty($obj->fields['use_region']))
            {
                $table_schema[PREFIX . "_auto_regions"] = "CREATE TABLE `" . PREFIX . "_auto_regions` (
                    `id` int(10) unsigned NOT NULL auto_increment,  
                    `country_id` int(10) unsigned NOT NULL,         
                    `name` varchar(255) NOT NULL default '',        
                    PRIMARY KEY  (`id`),
                    KEY `country_id` (`country_id`)
                 ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
		    }
		    
		    $table_schema[PREFIX . "_auto_cities"] = "CREATE TABLE `" . PREFIX . "_auto_cities` (
                   `id` int(10) unsigned NOT NULL auto_increment,  
                   `country_id` int(10) unsigned NOT NULL,         
                   `region_id` int(10) unsigned NOT NULL,          
                   `name` varchar(255) NOT NULL default '',        
                   PRIMARY KEY  (`id`),
                   KEY `country_id` (`country_id`),
                   KEY `region_id` (`region_id`)
             ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
		    
		    $table_schema[PREFIX . "_auto_fields"] = "CREATE TABLE `" . PREFIX . "_auto_fields` (
                   `id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `title` VARCHAR(110) NOT NULL DEFAULT '',
                  `description` VARCHAR(255) NOT NULL DEFAULT '',
                  `type` ENUM('text','select','textarea','checkbox') DEFAULT NULL,
                  `data` MEDIUMTEXT,
                  `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
                  `regex` VARCHAR(55) NOT NULL DEFAULT '',
                  `default` VARCHAR(255) DEFAULT NULL,
                  `active` tinyint(1) unsigned NOT NULL default '1',
                  PRIMARY KEY  (`id`)
             ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
		    
		    $fields = "";
                    foreach ($other_fields_array as $field)
                    {
                        $name = str_replace("_array", "", $field['file']);
                        $fields .= "`$name` smallint(5) UNSIGNED NOT NULL DEFAULT '0',";
                    }
                    foreach ($checkboxes_array as $colum=>$name)
                    {
                        $fields .= "`$colum` tinyint(1) NOT NULL DEFAULT '0',";
                    }

                    $table_schema[PREFIX . "_auto_autos"] = "CREATE TABLE `" . PREFIX . "_auto_autos` (
                   `id` int(11) NOT NULL auto_increment,                         
                  `country_id` int(11) NOT NULL default '0',                    
                  `region_id` int(11) NOT NULL default '0',                     
                  `city_id` int(11) NOT NULL default '0',                       
                  `city_other` varchar(255) NOT NULL default '',                
                  `mark_id` int(11) NOT NULL default '0',                       
                  `model_id` int(11) NOT NULL default '0',                      
                  `model_other` varchar(255) NOT NULL default '',               
                  `capacity_motor` float NOT NULL default '0',                  
                  `power` float NOT NULL default '0',                           
                  `race` int(11) NOT NULL default '0',                          
                  `color` varchar(7) NOT NULL default '', 
                  `year` smallint(5) unsigned NOT NULL default '0',                      
                  `cost` int(10) NOT NULL default '0',                          
                  `cost_search` int(10) NOT NULL default '0',                   
                  `currency` enum('USD','EUR','RUR') NOT NULL default 'USD',    
                  `auction` tinyint(1) NOT NULL default '0',                    
                  `exchange` tinyint(1) NOT NULL default '0',                    
                  `description` varchar(255) NOT NULL default '',               
                  `contact_person` varchar(110) NOT NULL default '',            
                  `email` varchar(110) NOT NULL default '',                     
                  `phone` varchar(110) NOT NULL default '',                     
                  `photo` int(10) NOT NULL default '0',                         
                  `photo_count` smallint(5) NOT NULL default '0',               
                  `exp_date` int(10) unsigned NOT NULL default '0',             
                  `add_date` int(10) unsigned NOT NULL default '0',             
                  `update_date` int(10) unsigned NOT NULL default '0',          
                  `allow_block` tinyint(1) NOT NULL default '0',                
                  `block_date` int(10) unsigned NOT NULL default '0',           
                  `allow_site` tinyint(1) NOT NULL default '0',                 
                  `author` varchar(40) NOT NULL default '',                     
                  `author_id` int(10) unsigned NOT NULL,                        
                  `author_ip` varchar(15) NOT NULL default '',                  
                  `guest_session` varchar(32) NOT NULL default '',   
                  `view_count` smallint(5) NOT NULL default '0',   
                  `xfields` MEDIUMTEXT NOT NULL,   
                  $fields
                  PRIMARY KEY  (`id`),                                          
                  KEY `city_id` (`city_id`),                                    
                  KEY `model_id` (`model_id`),                                  
                  KEY `country_id` (`country_id`),                              
                  KEY `region_id` (`region_id`),                                
                  KEY `mark_id` (`mark_id`),                                    
                  KEY `cost_search` (`cost_search`),                            
                  KEY `add_date` (`add_date`)                       
                 ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
                  
                  $table_schema[PREFIX . "_auto_images"] = "CREATE TABLE `" . PREFIX . "_auto_images` (
                   `id` int(10) unsigned NOT NULL auto_increment,     
                   `model_id` int(10) unsigned NOT NULL default '0',  
                   `auto_id` int(10) unsigned NOT NULL default '0',   
                   `image_name` varchar(255) NOT NULL default '',     
                   `add_date` int(10) unsigned NOT NULL default '0',  
                   `user_id` int(10) unsigned NOT NULL default '0',
                   `guest_session` varchar(32) NOT NULL default '',
                   PRIMARY KEY  (`id`),
                   KEY `auto_id` (`auto_id`)          
           ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
                  
                  $table_schema[PREFIX . "_auto_marks"] = "CREATE TABLE `" . PREFIX . "_auto_marks` (
                  `id` int(10) unsigned NOT NULL auto_increment,         
                  `name` varchar(255) NOT NULL default '',               
                  `auto_num` smallint(5) unsigned NOT NULL default '0',  
                  PRIMARY KEY  (`id`)
             ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
                  
                  $table_schema[PREFIX . "_auto_models"] = "CREATE TABLE `" . PREFIX . "_auto_models` (
                   `id` int(10) unsigned NOT NULL auto_increment,         
                   `mark_id` int(10) unsigned NOT NULL,                   
                   `name` varchar(255) NOT NULL default '',               
                   `auto_num` smallint(5) unsigned NOT NULL default '0',  
                   PRIMARY KEY  (`id`),
                   KEY `mark_id` (`mark_id`)
             ) ENGINE=MyISAM /*!40101 DEFAULT CHARACTER SET " . COLLATE . " COLLATE " . COLLATE . "_general_ci */";
                    
        	if (!empty($obj->fields['countries']))
        	{
        	    include(ENGINE_DIR . "/car-market/import/import_countries.php");
        	    
        	    $values = '';
                foreach ($countries as $id=>$country)
                {
                    $name = $db->safesql($country['name']);
                    
                    if ($values)
                    {
                        $values .= ", ";
                    }
                    
                    $values .= "($id, '$name')";
                }
                
                $table_schema[] = "INSERT INTO `" . PREFIX . "_auto_countries` VALUES $values";
        	}
        	
        	if (!empty($obj->fields['regions']))
        	{
        	    include(ENGINE_DIR . "/car-market/import/import_regions.php");
        	    
        	    if (empty($obj->fields['countries']) && !empty($obj->fields['country_id']))
        	    {
        	        $regions_tmp = $regions[$obj->fields['country_id']];
        	        $regions = array();
        	        $regions[0] = $regions_tmp;
        	    }
        	    
        	    foreach ($regions as $cid => $region_a)
        	    {
        	        $values = '';
        	        foreach ($region_a as $id => $region)
        	        {
        	            $name = $db->safesql($region['name']);
                    
                        if ($values)
                        {
                            $values .= ", ";
                        }
                        
                        $values .= "($id, {$region['country_id']}, '$name')";
        	        }
        	        $table_schema[] = "INSERT INTO `" . PREFIX . "_auto_regions` VALUES $values";
        	    }
        	}
        	
        	if (!empty($obj->fields['cities']))
        	{
        	    ini_set("memory_limit","128M");
        	    
        	    include(ENGINE_DIR . "/car-market/import/import_cities.php");
        	    
        	    if (empty($obj->fields['countries']) && !empty($obj->fields['country_id']))
        	    {
        	        $cities_tmp = $cities[$obj->fields['country_id']];
        	        $cities = array();
        	        $cities[0] = $cities_tmp;
        	    }
        	    else if (empty($obj->fields['regions']) && !empty($obj->fields['region_id']))
        	    {
        	        foreach ($cities as $cid => $regions)
        	        {
        	            if (isset($regions[$obj->fields['region_id']]))
        	            {
        	                $cities_tmp = $regions[$obj->fields['region_id']];
                            $cities = array();
                            $cities[0][0] = $cities_tmp;
        	                break;
        	            }
        	        }
        	    }
        	    
        	    foreach ($cities as $$cid => $regions)
        	    {
        	        $values = '';
        	        foreach ($regions as $rid => $city_array)
        	        {
        	            foreach ($city_array as $cid => $city)
        	            {
        	                $name = $db->safesql($city['name']);
                    
                            if ($values)
                            {
                                $values .= ", ";
                            }
                            
                            $values .= "($cid, {$city['country_id']}, {$city['region_id']},'$name')";
        	            }
        	        }
        	        $table_schema[] = "INSERT INTO `" . PREFIX . "_auto_cities` VALUES $values";
        	    }
        	}
        	
        	if (!empty($obj->fields['marks']))
        	{
        	    include(ENGINE_DIR . "/car-market/import/marks_import.php");
                include(ENGINE_DIR . "/car-market/import/models_import.php");
        	}
        	
        	if ($config['version_id'] >= 8.2)
        	{
        	    $table_schema[] = "INSERT IGNORE `" . PREFIX . "_admin_sections` (allow_groups, name, icon, title, descr) VALUES ('all', 'car-market', 'auto.jpg', 'Car Market (Авторынок)', 'Add auto and Edit settings')";
        	}
        	
			$obj->Database($table_schema);
			
		case 7:
		    $text_finish = <<<TEXT
	<div style="font-size:120%;text-align:center">Благодарим вас за покупку модуля. Надеемся что работа с ним доставит Вам только удовольствие!!! Все возникшие вопросы вы можете найти в документации или задать их на форуме поддержки <a href="http://forum.kaliostro.net/" >http://forum.kaliostro.net/</a> . </div>
TEXT;
			$obj->Finish($text_finish);
			break;
			
		default:
			if (file_exists(ENGINE_DIR.'/data/'.CONFIG_FILE) && empty($_POST['type']))
			{
				require(ENGINE_DIR . "/data/" . CONFIG_FILE);
				$config = ${CONFIG_VARNAME};
				$obj->steps_array = array();
				$obj->steps_array[] = "Описание модуля";
				
				switch ($config['version_id'])
				{
				    case "2.0.0":
                        $obj->steps_array[] = "2.1.0";
                        
					case "2.0.5":
					case "2.1.0":
						$obj->steps_array[] = '2.2.0';
						
					case "2.2.0":
						$obj->steps_array[] = VERSION;
						
					default:
						$obj->steps_array[] = "Завершение обновления";
				}
				$obj->SetType("update", "Начать обновление");
				$obj->Main($text_main, "Начать обновление");
			}
			else 
			{
				$obj->SetType("install");
				$obj->Main($text_main, "Начать установку");
			}
			
			break;
	}
}

?>
