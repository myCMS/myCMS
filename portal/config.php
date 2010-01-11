<?php
/*
 * * * * *  *    *   *****   *  *    * * * * *
 * *        **   *  *     *     **   * *
 * * * * *  * *  *  *  ***   *  * *  * * * * *
 * *        *  * *  *     *  *  *  * * *
 * * * * *  *   **   *****   *  *   ** * * * *
 *
 */
/* ************************************************************************************************
 * ************************* ENGINE PATH SETTINGS *************************************************
 * ************************************************************************************************
 */
define("PROJECT_ROOT",                                      "D:/www/engine_test/");
define("ENGINE_ROOT",                                       PROJECT_ROOT."engine/classes/");
define("ENGINE_CONFIG_ROOT",                                PROJECT_ROOT."engine/");

define("SITE_NAME",                                         "site");
define("USER_DEFINED_CLASSES",                              PROJECT_ROOT."site/classes/");
define("USER_DEFINED_MODULE_CLASSES",                       USER_DEFINED_CLASSES."modules/");
define("SMARTY_FOLDER",                                     PROJECT_ROOT."site/smarty/");
define("WEBROOT_FOLDER",                                    PROJECT_ROOT."site/webroot/");
define("IMAGES_FOLDER",                                     WEBROOT_FOLDER."img/");
define("IMAGES_FOLDER_WEB_PREFIX",                          "img/");

/* ************************************************************************************************
 * ************************* DATABASE SETTINS *****************************************************
 * ************************************************************************************************
 */
define("MYSQL_DATABASE_NAME",                               "engine");

/* ************************************************************************************************
 * ************************* PORTAL SETTINS *******************************************************
 * ************************************************************************************************
 */
define("PORTAL_URL",                                        "engine_test/site/webroot");
define("WEB_PORTAL_URL",                                    "engine_test1/site/webroot");
define("FULL_SITE_URL",                                     "http://localhost/engine_test/site/webroot");

/* ************************************************************************************************
 * ************************* DEBUG SETTINS ********************************************************
 * ************************************************************************************************
 */
define("DISPLAY_PHP_ERRORS",                                1);
define("ENABLE_DEBUG",                                      0);
define("ENABLE_CACHING",                                    0);
define("ENABLE_SMARTY_DEBUG",                               1);
define("ENABLE_FIREPHP",                                    0);

/* ************************************************************************************************
 * ************************* SMARTY SETTINS *****************************************************
 * ************************************************************************************************
 */
define("SMARTY_CACHE",                                      SMARTY_FOLDER."cache/");
define("SMARTY_CONFIGS",                                    SMARTY_FOLDER."configs/");
define("SMARTY_TEMPLATES",                                  SMARTY_FOLDER."templates/");
define("SMARTY_TEMPLATES_C",                                SMARTY_FOLDER."templates_c/");
define("SMARTY_CACHE_LIFETIME",                             60*60*24);

/* ************************************************************************************************
 * ************************* SESSION SETTINS ******************************************************
 * ************************************************************************************************
 */
define("AUTHENTICATION_SESSION_LIFETIME",                   3600*24);

define("WEB_PORTAL_SKIP_URL",                               3);
define("INDEX_FILE_NAME",                                   "index.php");

/* ************************************************************************************************
 * ************************* MODULES ACTIVATIONS ****************************************************
 * ************************************************************************************************
 */

define("AUTHENTICATION_USE_ONLY_ANONIMOUS_ACCESS",          0);
define("GENERATE_RANDOM_QUESTION",                          1);
define("GENERATE_RANDOM_PICTURE",                           1);
define("USE_COUNTER",                                       1);
define("NAF_MODULE_ENABLE",                                 1);
define("CATALOGUE_MODULE_ENABLE",                           1);
define("GALLERY_MODULE_ENABLE",                             0);
define("SEARCH_ENABLE",                                     1);
define("FILES_MODULE_ENABLE",                               1);
define("FEEDBACK_MODULE_ENABLE",                            1);
define("VOTE_MODULE_ENABLE",                                1);
define("STATIC_MODULE_ENABLE",                              1);

/* ************************************************************************************************
 * ************************* TEMPLATES SETTINS ****************************************************
 * ************************************************************************************************
 */
$PAGES_CONFIG = array(
	"RU" => array(
		"SMARTY_TEMPLATE_MAIN_PAGE_TEMPLATE_NAME"           => "page/Index.tpl",
		"SMARTY_TEMPLATE_PAGE_TEMPLATE_NAME"                => "page/Inside.tpl"
		),
	"EN" => array(
		"SMARTY_TEMPLATE_MAIN_PAGE_TEMPLATE_NAME"           => "page/Index_en.tpl",
		"SMARTY_TEMPLATE_PAGE_TEMPLATE_NAME"                => "page/Inside_en.tpl"
	)
);
define("SMARTY_TEMPLATE_TOP_LEVEL_MENU_TEMPLATE_NAME",      "menu/Level1.tpl");
define("SMARTY_TEMPLATE_SUB_MENU_TEMPLATE_NAME",            "menu/Level2.tpl");
define("SMARTY_TEMPLATE_SUB2_MENU_TEMPLATE_NAME",           "menu/Level3.tpl");
define("SMARTY_TEMPLATE_404_PAGE",                          "technical/404.tpl");

/* ************************* MENU IMAGES istead of text *******************************************
*/
define("MENU_TOP_LEVEL_IMG",                                "1");
define("MENU_TOP_LEVEL_IMG_PATH",                           "img/menu-items/menu-{id}-{lang}.png");// I believe that it's stuipid, but it seems pretty much shorter this way...

/* ************************************************************************************************
 * ************************* BLOCK SETTINS ********************************************************
 * ************************************************************************************************

/* ************************* SITE MAP SETTINGS ****************************************************
*/
define("SITE_MAP_TEMPLATE_NAME",                            "technical/sitemap.tpl");

/* ************************* RANDOM BLOCKS SETTINGS (FACTS, IMAGES) *******************************
*/
define("RANDOM_QUESTION_TEMPLATE_NAME",                     "other/random/facts.tpl");

define("RANDOM_PICTURE_FOLDER",                             IMAGES_FOLDER."random/images/");
define("RANDOM_PICTURE_URL",                                "img/random/images/");
define("RANDOM_PICTURE_TEMPLATE_NAME",                      "other/random/images.tpl");

/* ************************* COUNTER SETTINGS *****************************************************
*/

/* ************************* COPYRIGHT SETTINGS ***************************************************
*/
define("COPYRIGHT_START_YEAR",                              2007);

/* ************************************************************************************************
 * ************************* MODULE SETTINS *******************************************************
 * ************************************************************************************************
*/

/* ************************************************************************************************
 * ************************* NAF SETTINS **********************************************************
 * ************************************************************************************************
*/

/* ************************* NAF MODULE FLAG ******************************************************
*/

/* ************************* NAF MODULE SETTINGS **************************************************
*/
$NAF_CONFIG = array(
	array(
		/* news */
		'MODULE_NAME'                                       => 'news',
		'MODULE_DATABASE_TABLE_NAME'                        => 'naf_news',
		'MODULE_LIST_TEMPLATE_NAME'                         => 'naf/news.tpl',
		'MODULE_SINGLE_TEMPLATE_NAME'                       => 'naf/news.tpl',
		'MODULE_RELATIVE_URL'                               => 'news',
		'MODULE_DISPLAY_CALENDAR'                           => '1',
		'MODULE_LIST_ELEMENTS_LIMIT'                        => '30',
		'MODULE_MAX_COUNT_OF_TEXT_FIELDS'                   => '3',
		'MODULE_LIST_DISPLAY_FIELDS'                        => '1',
		'MODULE_SINGLE_DISPLAY_FIELDS'                      => '3',
		'MODULE_SPLIT_TEXT'                                 => '1',
		'MODULE_SPLIT_LEFT_TAG'                             => '[',
		'MODULE_SPLIT_RIGHT_TAG'                            => ']',
		'MODULE_USE_ADMIN'                                  => '1'
	),
	array(
		/* articles */
		'MODULE_NAME'                                       => 'articles',
		'MODULE_DATABASE_TABLE_NAME'                        => 'naf_articles',
		'MODULE_LIST_TEMPLATE_NAME'                         => 'naf/articles.tpl',
		'MODULE_SINGLE_TEMPLATE_NAME'                       => 'naf/articles.tpl',
		'MODULE_RELATIVE_URL'                               => 'articles',
		'MODULE_DISPLAY_CALENDAR'                           => '0',
		'MODULE_LIST_ELEMENTS_LIMIT'                        => '20',
		'MODULE_MAX_COUNT_OF_TEXT_FIELDS'                   => '2',
		'MODULE_LIST_DISPLAY_FIELDS'                        => '1',
		'MODULE_SINGLE_DISPLAY_FIELDS'                      => '2',
		'MODULE_SPLIT_TEXT'                                 => '0',
		'MODULE_SPLIT_LEFT_TAG'                             => '[',
		'MODULE_SPLIT_RIGHT_TAG'                            => ']',
		'MODULE_USE_ADMIN'                                  => '1'
	),
	array(
		/* faqs */
		'MODULE_NAME'                                       => 'faqs',
		'MODULE_DATABASE_TABLE_NAME'                        => 'naf_faqs',
		'MODULE_LIST_TEMPLATE_NAME'                         => 'naf/faqs.tpl',
		'MODULE_SINGLE_TEMPLATE_NAME'                       => 'naf/faqs.tpl',
		'MODULE_RELATIVE_URL'                               => 'faq',
		'MODULE_DISPLAY_CALENDAR'                           => '0',
		'MODULE_LIST_ELEMENTS_LIMIT'                        => '20',
		'MODULE_MAX_COUNT_OF_TEXT_FIELDS'                   => '2',
		'MODULE_LIST_DISPLAY_FIELDS'                        => '2',
		'MODULE_SINGLE_DISPLAY_FIELDS'                      => '2',
		'MODULE_SPLIT_TEXT'                                 => '0',
		'MODULE_SPLIT_LEFT_TAG'                             => '[',
		'MODULE_SPLIT_RIGHT_TAG'                            => ']',
		'MODULE_USE_ADMIN'                                  => '1'
	)
);

/* ************************* NAF STATIC BLOCK *****************************************************
*/
define("NAF_LATEST_ELEMENTS_DISPLAY",                       "1");
define("NAF_LATEST_ELEMENTS_TYPE",                          "news");
define("NAF_LATEST_ELEMENTS_LIMIT",                         "5");
define("NAF_LATEST_ELEMENTS_SPLIT_TEXT",                    "1");
define("NAF_LATEST_ELEMENTS_SPLIT_LEFT_TAG",                "[");
define("NAF_LATEST_ELEMENTS_SPLIT_RIGHT_TAG",               "]");
define("NAF_LATEST_ELEMENTS_TEMPLATE_NAME",                 "other/latest/naf_latest_block.tpl");
define("NAF_MODULE_IMG_FOLDER_WEB",                         IMAGES_FOLDER_WEB_PREFIX."naf/");
define("NAF_MODULE_IMG_FOLDER_FILE_SYSTEM",                 IMAGES_FOLDER."naf/");
define("NAF_MODULE_IMG_FOLDER_TEMP_WEB",                    IMAGES_FOLDER_WEB_PREFIX."naf/tmp/");
define("NAF_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM",            IMAGES_FOLDER."naf/tmp/");
define("NAF_MODULE_IMG_ADDITIONAL_FOLDER_WEB",              IMAGES_FOLDER_WEB_PREFIX."naf/additional/");
define("NAF_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM",      IMAGES_FOLDER."naf/additional/");
define("NAF_MODULE_IMG_CKEDITOR_FOLDER_WEB",                IMAGES_FOLDER_WEB_PREFIX."naf/ckeditor/");
define("NAF_MODULE_IMG_CKEDITOR_FOLDER_FILE_SYSTEM",        IMAGES_FOLDER."naf/ckeditor/");
define("NAF_MODULE_ADMIN_ADDITIONAL_QUICK_TEMPLATE_NAME",   "admin/pictures/naf.tpl");
define("NAF_MODULE_ADMIN_ADDITIONAL_FULL_TEMPLATE_NAME",    "admin/pictures/additional.tpl");

/* ************************* NAF PICTURES BLOCK ***************************************************
*/

$NAF_IMAGE_TYPES = array(
	'Small'  => array(
			'Size'                                          => "90x90",
			'MaskName'                                      => "%d-s.jpg",
            'DefaultName'                                   => "00-s.gif",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "1"
		),
	'Medium' => array(
			'Size'                                          => "180x180",
			'MaskName'                                      => "%d-m.jpg",
            'DefaultName'                                   => "00-m.gif",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		),
	'Big'    => array(
			'Size'                                          => "270x270",
			'MaskName'                                      => "%d-b.jpg",
            'DefaultName'                                   => "00-b.gif",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		)
);

$NAF_ADDITIONAL_IMAGE_TYPES = array(
	'Small'  => array(
			'Size'                                          => "90x90",
			'MaskName'                                      => "%s-s.jpg",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "1"
		),
	'Big'    => array(
			'Size'                                          => "270x270",
			'MaskName'                                      => "%s-b.jpg",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		),
	'Medium' => array(
			'Size'                                          => "180x180",
			'MaskName'                                      => "%s-m.jpg",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		)
);

/* ************************************************************************************************
 * ************************* CATALOGUE SETTINS ****************************************************
 * ************************************************************************************************
*/

/* ************************* CATALOGUE MODULE FLAG ************************************************
*/
/*
 *  ************************* CATALOGUE MODULE SETTINGS ********************************************
*/
define("CATALOGUE_DISPLAY_MENU",                            "1");
define("CATALOGUE_DISPLAY_MENU_ALWAYS",                     "0");
define("CATALOGUE_MODULE_USE_ADMIN",                        "1");
define("CATALOGUE_USE_EXPAND_MENU_MODE",                    "0");
define("CATALOGUE_USE_PRODUCTS_NESTING_MODE",               "1");
define("CATALOGUE_RELATIVE_URL",                            "catalogue");
define("CATALOGUE_MODULE_MENU_TEMPLATE_NAME",               "cat/menu.tpl");
define("CATALOGUE_MODULE_LIST_TEMPLATE_NAME",               "cat/catalogue.tpl");
define("CATALOGUE_MODULE_SINGLE_TEMPLATE_NAME",             "cat/catalogue.tpl");
define("CATALOGUE_MODULE_INVISIBLE_TEMPLATE_NAME",          "cat/invcatalogue.tpl");
define("CATALOGUE_MODULE_PRODUCTS_LIST_LIMIT",              "20");
define("CATALOGUE_MODULE_DEFAULT_CURRENCY_NAME",            "грн.");

/* ************************* CATALOGUE LATEST / RANDOM SETTINGS ***********************************
*/
define("CATALOGUE_MODULE_DISPLAY_LATEST_PRODUCTS",          "1");
define("CATALOGUE_MODULE_LATEST_PRODUCTS_DISPLAY_PICTURE",  "1");
define("CATALOGUE_MODULE_LATEST_PRODUCTS_LIMIT",            "2");
define("CATALOGUE_MODULE_LATEST_PRODUCTS_TEMPLATE_NAME",    "other/random/catalogue.tpl");
define("CATALOGUE_MODULE_DISPLAY_RANDOM_PRODUCTS",          "1");
define("CATALOGUE_MODULE_RANDOM_PRODUCTS_DISPLAY_PICTURE",  "1");
define("CATALOGUE_MODULE_RANDOM_PRODUCTS_LIMIT",            "5");
define("CATALOGUE_MODULE_RANDOM_PRODUCTS_TEMPLATE_NAME",    "other/random/catalogue.tpl");

/* ************************* CATALOGUE IMAGES SETTINGS ********************************************
*/
define("CATALOGUE_DISPLAY_PICTURES",                        "1");
define("CATALOGUE_IMG_IN_ONE_FOLDER",                       "1");
define("CATALOGUE_MODULE_IMG_FOLDER_WEB",                   IMAGES_FOLDER_WEB_PREFIX."catalogue/");
define("CATALOGUE_MODULE_IMG_FOLDER_FILE_SYSTEM",           IMAGES_FOLDER."catalogue/");
define("CATALOGUE_MODULE_IMG_FOLDER_TEMP_WEB",              IMAGES_FOLDER_WEB_PREFIX."catalogue/tmp/");
define("CATALOGUE_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM",      IMAGES_FOLDER."catalogue/tmp/");
define("CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_WEB",        IMAGES_FOLDER_WEB_PREFIX."catalogue/additional/");
define("CATALOGUE_MODULE_IMG_ADDITIONAL_FOLDER_FILE_SYSTEM",IMAGES_FOLDER."catalogue/additional/");
define("CATALOGUE_MODULE_IMG_CKEDITOR_FOLDER_WEB",          IMAGES_FOLDER_WEB_PREFIX."catalogue/ckeditor/");
define("CATALOGUE_MODULE_IMG_CKEDITOR_FOLDER_FILE_SYSTEM",  IMAGES_FOLDER."catalogue/ckeditor/");
define("CATALOGUE_MODULE_ADMIN_ADDITIONAL_QUICK_TEMPLATE_NAME","admin/pictures/cat.tpl");
define("CATALOGUE_MODULE_ADMIN_ADDITIONAL_FULL_TEMPLATE_NAME","admin/pictures/additional.tpl");

$CATALOGUE_IMAGE_TYPES = array(
	'Small'  => array(
			'Size'                                          => "90x90",
			'MaskName'                                      => "%d-s.jpg",
            'DefaultName'                                   => "00-s.gif",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "1"
		),
	'Medium' => array(
			'Size'                                          => "180x180",
			'MaskName'                                      => "%d-m.jpg",
            'DefaultName'                                   => "00-m.gif",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		),
	'Big'    => array(
			'Size'                                          => "270x270",
			'MaskName'                                      => "%d-b.jpg",
            'DefaultName'                                   => "00-b.gif",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		)
);

$CATALOGUE_ADDITIONAL_IMAGE_TYPES = array(
	'Small'  => array(
			'Size'                                          => "90x90",
			'MaskName'                                      => "%s-s.jpg",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "1"
		),
	'Big'    => array(
			'Size'                                          => "270x270",
			'MaskName'                                      => "%s-b.jpg",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		),
	'Medium' => array(
			'Size'                                          => "180x180",
			'MaskName'                                      => "%s-m.jpg",
			'DisableCrop'                                   => "0",
            'UseOriginalSize'                               => "0",
            'UseAsPreview'                                  => "0"
		)
);

/* ************************************************************************************************
 * ************************* GALLERY SETTINS ******************************************************
 * ************************************************************************************************
*/
define("GALLERY_DISPLAY_MENU",                              "1");
define("GALLERY_DISPLAY_MENU_ALWAYS",                       "0");
define("GALLERY_RELATIVE_URL",                              "gallery");
define("GALLERY_ISSET_TYPE",                                "1");
define("GALLERY_MODULE_IMG_FOLDER",                         "img/gallery/");
define("GALLERY_MODULE_LIST_TEMPLATE_NAME",                 "gal/gallery.tpl");
define("GALLERY_MODULE_SINGLE_TEMPLATE_NAME",               "gal/gallery.tpl");
define("GALLERY_MODULE_MENU_TEMPLATE_NAME",                 "gal/menu.tpl");
define("GALLERY_MODULE_DISPLAY_LATEST_PRODUCTS",            "1");
define("GALLERY_MODULE_LATEST_PRODUCTS_DISPLAY_PICTURE",    "1");
define("GALLERY_MODULE_LATEST_PRODUCTS_LIMIT",              "2");
define("GALLERY_MODULE_LATEST_PRODUCTS_TEMPLATE_NAME",      "other/gallery_latest_products.tpl");
define("GALLERY_MODULE_DISPLAY_RANDOM_PRODUCTS",            "1");
define("GALLERY_MODULE_RANDOM_PRODUCTS_DISPLAY_PICTURE",    "1");
define("GALLERY_MODULE_RANDOM_PRODUCTS_LIMIT",              "2");
define("GALLERY_MODULE_RANDOM_PRODUCTS_TEMPLATE_NAME",      "other/gallery_random_products.tpl");
define("GALLERY_DISPLAY_PICTURES",                          "1");
define("GALLERY_IMG_IN_ONE_FOLDER",                         "1");
define("GALLERY_IMG_COUNT_ROW",                             "2");
define("GALLERY_MODULE_IMG_FOLDER_WEB",                     "img/gallery/");
define("GALLERY_MODULE_IMG_FOLDER_FILE_SYSTEM",             IMAGES_FOLDER."img/gallery/");
define("GALLERY_MODULE_IMG_FOLDER_TEMP_WEB",                "img/gallery/tmp/");
define("GALLERY_MODULE_IMG_FOLDER_TEMP_FILE_SYSTEM",        IMAGES_FOLDER."img/gallery/tmp/");
define("GALLERY_MODULE_BIG_PICTURE_SIZE",                   "210x210");
define("GALLERY_MODULE_SMALL_PICTURE_SIZE",                 "90x90");
define("GALLERY_MODULE_NOT_USE_CROP_PICTURE",               "1");
define("GALLERY_MODULE_USE_BLACK_AND_WHITE_PICTURES",       "0");
define("GALLERY_MODULE_USE_ADMIN",                          "1");

/* ************************************************************************************************
 * ************************* SEARCH SETTINS *******************************************************
 * ************************************************************************************************
*/
define("SEARCH_TEMPLATE_NAME",                              "technical/search.tpl");

/* ************************************************************************************************
 * ************************* FEEDBACK SETTINS *****************************************************
 * ************************************************************************************************
*/
define("FEEDBACK_MODULE_TEMPLATE_NAME",                     "other/feedback.tpl");
define("FEEDBACK_MODULE_CORPORATE_EMAIL",                   "mart.mail.2@gmail.com");
$FEEDBACK_CONFIG = array(
	array(
		'MODULE_NAME'                                       => 'main_feedback',
		'MODULE_CORPORATE_EMAIL'                            => 'mart.mail.2@gmail.com',
		'MODULE_TEMPLATE_NAME_RU'                           => 'other/forms/feedback.tpl',
		'MODULE_TEMPLATE_NAME_EN'                           => 'other/forms/feedback_en.tpl',
		'MODULE_TEMPLATE_NAME_UA'                           => 'other/forms/feedback.tpl',
	),
	array(
		'MODULE_NAME'                                       => 'additional_feedback',
		'MODULE_CORPORATE_EMAIL'                            => 'mart.mail.2@gmail.com',
		'MODULE_TEMPLATE_NAME_RU'                           => 'other/forms/feedback.tpl',
		'MODULE_TEMPLATE_NAME_EN'                           => 'other/forms/feedback.tpl',
		'MODULE_TEMPLATE_NAME_UA'                           => 'other/forms/feedback.tpl',
	)
);

/* ************************************************************************************************
 * ************************* FILES SETTINS ********************************************************
 * ************************************************************************************************
*/

define("FILES_MODULE_TEMPLATE_NAME",                        "other/files.tpl");
define("FILES_FOLDER",                                      WEBROOT_FOLDER."files/");
define("FILES_FOLDER_WEB",                                  "files/");
define("FILES_PRICELIST_NAME",                              "test");

/* ************************************************************************************************
 * ************************* VOTE SETTINS *********************************************************
 * ************************************************************************************************
*/
define("VOTE_MODULE_TEMPLATE_NAME",                         "other/vote/vote.tpl");
define("VOTE_MODULE_ADMIN_TEMPLATE_NAME",                   "external/vote.tpl");

/* ************************************************************************************************
 * ************************* CMS SETTINS **********************************************************
 * ************************************************************************************************
*/

define("STATIC_PAGES_MODULE_TEMPLATE_NAME",                 "technical/static.tpl");

/* ************************* TOP ADMIN PANEL SETTINGS *********************************************
*/
define("ADMIN_RESPONSE_WRAPPER_TEMPLATE_NAME",/*........*/ "admin/wrapper/edit_form.tpl");
define("ADMIN_ELEMENT_WRAPPER_TEMPLATE_NAME",/*.........*/ "admin/wrapper/edit_box.tpl");
define("ADMIN_EMPTY_TEMPLATE_NAME",/*...................*/ "admin/wrapper/edit_answer.tpl");
define("ADMIN_TOP_PANEL_TEMPLATE_NAME",/*...............*/ "admin/panel.tpl");
define("ADMIN_WINDOW_TEMPLATE_NAME",/*..................*/ "admin/window.tpl");
define("ADMIN_AUTHORIZE_TEMPLATE_NAME",/*...............*/ "admin/auth.tpl");

/* ************************* TOP ADMIN PANEL MENU SETTINGS ****************************************
*/
$ADMIN_TOP_MENU = array(
	'Новости'/*.........................................*/ => 'news',
	'Статик'/*..........................................*/ => 'static',
	'Статьи'/*..........................................*/ => 'articles',
	'Часто задаваемые вопросы'/*........................*/ => 'faqs',
	'Каталог'/*.........................................*/ => 'catalogue',
	'Структура Каталога'/*..............................*/ => 'invcatalogue',
	'Голосование'/*.....................................*/ => 'vote',
	'Галерея'/*.........................................*/ => 'gallery',
	'Файлы'/*...........................................*/ => 'file'
);

/* ************************* ADMIN WINDOW SETTINGS ************************************************
*/
$ADMIN_WINDOW = array(
	'news' => array(
		array(
			'Name'/*....................................*/ => 'picture',
			'Type'/*....................................*/ => 'file',
			'Description'/*.............................*/ => 'Изображение новости'
		),
		array(
			'Name'/*....................................*/ => 'text1',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Короткий текст'
		),
		array(
			'Name'/*....................................*/ => 'text2',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Полный текст'
		),
		array(
			'Name'/*....................................*/ => 'date',
			'Type'/*....................................*/ => 'date',
			'Description'/*.............................*/ => 'Дата публикации'
		),
		array(
			'Name'/*....................................*/ => 'active',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Активность'
		),
	),
	'articles' => array(
		array(
			'Name'/*....................................*/ => 'text1',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Заголовок'
		),
		array(
			'Name'/*....................................*/ => 'text2',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Полный текст'
		),
		array(
			'Name'/*....................................*/ => 'date',
			'Type'/*....................................*/ => 'date',
			'Description'/*.............................*/ => 'Дата публикации'
		),
		array(
			'Name'/*....................................*/ => 'active',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Активность'
		),
	),
	'catalogue' => array(
		array(
			'Name'/*....................................*/ => 'ProductName',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Название продукта'
		),
		array(
			'Name'/*....................................*/ => 'ProductDescription',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Описание продукта'
		),
		array(
			'Name'/*....................................*/ => 'ProductArticle',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Артикул'
		),
		array(
			'Name'/*....................................*/ => 'BrandName',
			'Type'/*....................................*/ => 'select',
			'Description'/*.............................*/ => 'Бренд'
		),
		array(
			'Name'/*....................................*/ => 'ProductPrice',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Цена, грн.'
		),
		array(
			'Name'/*....................................*/ => 'Picture',
			'Type'/*....................................*/ => 'file',
			'Description'/*.............................*/ => 'Изображение товара'
		),
		array(
			'Name'/*....................................*/ => 'MultiPicture',
			'Type'/*....................................*/ => 'multifile',
			'Description'/*.............................*/ => 'Дополнительные изображения'
		),
		array(
			'Name'/*....................................*/ => 'ProductExist',
			'Type'/*....................................*/ => 'radio',
			'Description'/*.............................*/ => 'Наличие'
		),
		array(
			'Name'/*....................................*/ => 'Active',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Активность'
		),
	),
	'vote' => array(
		array(
			'Name'/*....................................*/ => 'VoteName',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вопрос'
		),
		array(
			'Name'/*....................................*/ => 'Question1',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #1'
		),
		array(
			'Name'/*....................................*/ => 'Answer1',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question2',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #2'
		),
		array(
			'Name'/*....................................*/ => 'Answer2',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question3',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #3'
		),
		array(
			'Name'/*....................................*/ => 'Answer3',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question4',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #4'
		),
		array(
			'Name'/*....................................*/ => 'Answer4',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question5',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #5'
		),
		array(
			'Name'/*....................................*/ => 'Answer5',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question6',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #6'
		),
		array(
			'Name'/*....................................*/ => 'Answer6',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question7',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #7'
		),
		array(
			'Name'/*....................................*/ => 'Answer7',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question8',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #8'
		),
		array(
			'Name'/*....................................*/ => 'Answer8',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question9',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #9'
		),
		array(
			'Name'/*....................................*/ => 'Answer9',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Question10',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Вариант ответа #10'
		),
		array(
			'Name'/*....................................*/ => 'Answer10',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Голосов:'
		),
		array(
			'Name'/*....................................*/ => 'Active',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Акстивность'
		),
		array(
			'Name'/*....................................*/ => 'Finished',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Опрос окончен'
		),
    ),
	'static' => array(
		array(
			'Name'/*....................................*/ => 'Name',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Имя страницы'
		),
		array(
			'Name'/*....................................*/ => 'Text',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Тело страницы'
		),
		array(
			'Name'/*....................................*/ => 'Title',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Заголовок'
		),
		array(
			'Name'/*....................................*/ => 'Description',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Описание'
		),
		array(
			'Name'/*....................................*/ => 'Keywords',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Ключевые слова'
		),
		array(
			'Name'/*....................................*/ => 'Cpu',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'ЧПУ'
		),
		array(
			'Name'/*....................................*/ => 'Active',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Активность'
		)
    ),
	'invcatalogue' => array(
		array(
			'Name'/*....................................*/ => 'Name',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'Имя'
		),
		array(
			'Name'/*....................................*/ => 'Description',
			'Type'/*....................................*/ => 'text',
			'Description'/*.............................*/ => 'Описание'
		),
		array(
			'Name'/*....................................*/ => 'Cpu',
			'Type'/*....................................*/ => 'textline',
			'Description'/*.............................*/ => 'ЧПУ'
		),
		array(
			'Name'/*....................................*/ => 'Active',
			'Type'/*....................................*/ => 'checkbox',
			'Description'/*.............................*/ => 'Активность'
		)
	)
);
?>
