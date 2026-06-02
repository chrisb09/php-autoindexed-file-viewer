<?php
// Single-file file browser with streaming zip, mediainfo, async folder sizes, search/filter
set_time_limit(0);
ignore_user_abort(true);

// --- VERSION ---
define('APP_VERSION', '0.5.2');

// --- I18N ---
// Language definitions: each key is a locale code.
// 'en' is the fallback. Add new languages by copying the 'en' array and translating.
// dateFormat/timeFormat use PHP date() tokens for server-side, and a JS format string for client-side.
$I18N = [
    'en' => [
        '_flag' => '🇬🇧',
        '_name' => 'English',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Download folder',
        'toggle_theme' => 'Toggle light/dark mode',
        'search_placeholder' => 'Search by name…',
        'all_types' => 'All types',
        'type_folder' => 'Folders',
        'type_image' => 'Images',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documents',
        'type_archive' => 'Archives',
        'type_other' => 'Other',
        'recursive' => 'Recursive',
        'col_name' => 'Name',
        'col_size' => 'Size',
        'col_info' => 'Info',
        'col_modified' => 'Modified',
        'col_actions' => 'Actions',
        'parent_folder' => 'Parent folder',
        'folder_empty' => 'This folder is empty.',
        'view' => 'View',
        'download' => 'Download',
        'open' => 'Open',
        'zip' => 'Zip',
        'loading' => 'Loading…',
        'error_loading' => 'Error loading info',
        'no_info' => 'No info available',
        'config_error' => '⚠️ Configuration Error',
        'cookie_text' => 'This site uses essential cookies to save your preferences (theme, sort order, language). No tracking or third-party cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Download',
        'viewer_newtab' => '↗ New Tab',
        'viewer_close' => 'Close (Esc)',
        'viewer_prev' => 'Previous',
        'viewer_next' => 'Next',
        'file_too_large' => 'File too large for text preview. Use "New Tab" or download.',
        'could_not_load' => 'Could not load file.',
        'queue_processing' => '{running} processing, {pending} queued',
        'queue_no_tasks' => 'No active tasks',
        'file_singular' => '{n} file',
        'file_plural' => '{n} files',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folders',
        'empty' => 'empty',
        'share' => 'Share',
        'share_copied' => 'Share link copied to clipboard!',
        'share_expires' => 'Expiration:',
        'share_never' => 'Never',
        'share_1h' => '1 hour',
        'share_1d' => '1 day',
        'share_7d' => '7 days',
        'share_30d' => '30 days',
        'background_tasks' => 'Background tasks',
        'info_format' => 'Format',
        'info_pages' => 'Pages',
        'info_title' => 'Title',
        'info_author' => 'Author',
        'info_language' => 'Language',
        'info_creator' => 'Creator',
        'info_producer' => 'Producer',
        'info_pdf_version' => 'PDF version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encrypted',
        'info_page_size' => 'Page size',
        'info_created' => 'Created',
        'info_resolution' => 'Resolution',
        'info_type' => 'Type',
        'info_vector' => 'Vector graphic',
        'info_megapixels' => 'Megapixels',
        'info_color' => 'Color',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animated',
        'info_yes' => 'Yes',
        'info_duration' => 'Duration',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Overall bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit depth',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Channels',
    ],
    'af' => [
        '_flag' => '🇿🇦',
        '_name' => 'Afrikaans',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Tuis',
        'download_folder' => '⤓ Laai afmap',
        'toggle_theme' => 'Skakel lig/donkermodus aan/af',
        'search_placeholder' => 'Soek deur naam…',
        'all_types' => 'Alle tipes',
        'type_folder' => 'Mappe',
        'type_image' => 'Beelde',
        'type_audio' => 'Audios',
        'type_video' => 'Video\'s',
        'type_document' => 'Dokumente',
        'type_archive' => 'Argiewe',
        'type_other' => 'Ander',
        'recursive' => 'Rekursief',
        'col_name' => 'Naam',
        'col_size' => 'Grootte',
        'col_info' => 'Inligting',
        'col_modified' => 'Gewysig',
        'col_actions' => 'Aksies',
        'parent_folder' => 'Oudermap',
        'folder_empty' => 'Hierdie map is leeg.',
        'view' => 'Bekyk',
        'download' => 'Laai af',
        'open' => 'Oopmaak',
        'zip' => 'Zip',
        'loading' => 'Lêer word gelaaide…',
        'error_loading' => 'Fout tydens laai-in van inligting',
        'no_info' => 'Geen inligting beskikbaar',
        'config_error' => '⚠️ Konfigurasiefout',
        'cookie_text' => 'Hierdie webwerf gebruik noodsaaklike koekies om jou voorkeure te stoor (tema, sorteervolgorde, taal). Geen sporing of derdeparty-koekies nie.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Laai af',
        'viewer_newtab' => '↗ Nuwe tabblad',
        'viewer_close' => 'Sluit (Esc)',
        'viewer_prev' => 'Vorige',
        'viewer_next' => 'Volgende',
        'file_too_large' => 'Lêer te groot vir teksvoorbeeld. Gebruik "Nuwe tabblad" of laai af.',
        'could_not_load' => 'Kon lêer nie laai nie.',
        'queue_processing' => '{running} verwerking, {pending} in wachtrye',
        'queue_no_tasks' => 'Geen aktiewe take nie',
        'file_singular' => '{n} lêer',
        'file_plural' => '{n} lêers',
        'folder_singular' => '{n} map',
        'folder_plural' => '{n} mappe',
        'empty' => 'leeg',
        'share' => 'Deel',
        'share_copied' => 'Deellink gekopiëer na klembord!',
        'share_expires' => 'Verstrykking:',
        'share_never' => 'Nooit',
        'share_1h' => '1 uur',
        'share_1d' => '1 dag',
        'share_7d' => '7 dae',
        'share_30d' => '30 dae',
        'background_tasks' => 'Agtergrondtake',
        'info_format' => 'Formaat',
        'info_pages' => 'Bladsye',
        'info_title' => 'Titel',
        'info_author' => 'Auteur',
        'info_language' => 'Taal',
        'info_creator' => 'Skepper',
        'info_producer' => 'Produsent',
        'info_pdf_version' => 'PDF-versie',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Versleutel',
        'info_page_size' => 'Bladsy grootte',
        'info_created' => 'Geskep',
        'info_resolution' => 'Oplossing',
        'info_type' => 'Tipe',
        'info_vector' => 'Vektorgrafiek',
        'info_megapixels' => 'Megapiksele',
        'info_color' => 'Kleur',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animasie',
        'info_yes' => 'Ja',
        'info_duration' => 'Duur',
        'info_container' => 'Behouer',
        'info_overall_bitrate' => 'Oorgewigte bitkoers',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitkoers',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitdiepte',
        'info_sample_rate' => 'Staatsfrekwensie',
        'info_channels' => 'Kanale',
    ],
    'al' => [
        '_flag' => '🇦🇱',
        '_name' => 'Shqip',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Shtëpia',
        'download_folder' => '⤓ Shkarko dosje',
        'toggle_theme' => 'Kthe në mënyrë të lehtë/mënyrë të errët',
        'search_placeholder' => 'Kërko sipas emrit…',
        'all_types' => 'Të gjitha llojet',
        'type_folder' => 'Dosjet',
        'type_image' => 'Imazhet',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumente',
        'type_archive' => 'Arkiva',
        'type_other' => 'Të tjerë',
        'recursive' => 'Rekursiv',
        'col_name' => 'Emri',
        'col_size' => 'Madhësia',
        'col_info' => 'Informacioni',
        'col_modified' => 'Ndryshuar',
        'col_actions' => 'Veprime',
        'parent_folder' => 'Dosja e prindit',
        'folder_empty' => 'Kjo dosje është bosh.',
        'view' => 'Shiko',
        'download' => 'Shkarko',
        'open' => 'Hap',
        'zip' => 'Zip',
        'loading' => 'Po ngarkohet…',
        'error_loading' => 'Gabim gjatë ngarkimit të informacionit',
        'no_info' => 'Asnjë informacion i disponueshëm',
        'config_error' => '⚠️ Gabim në konfigurim',
        'cookie_text' => 'Ky vend i përdor kokakëmbët e nevojshme për të ruajtur preferencat tuaja (tema, renditja, gjuha). Asnjë monitorim ose kokakëmbje nga të tretë.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Shkarko',
        'viewer_newtab' => '↗ Tab i ri',
        'viewer_close' => 'Mbyll (Esc)',
        'viewer_prev' => 'E mëparshme',
        'viewer_next' => 'E ardhmja',
        'file_too_large' => 'Fajlli është shumë i madh për paraqitjen e tekstit. Përdorni "Tab i ri" ose shkarkoni.',
        'could_not_load' => 'Nuk mund të ngarkohet fajlli.',
        'queue_processing' => '{running} procesim, {pending} në rresht',
        'queue_no_tasks' => 'Asnjë detyrë aktive',
        'file_singular' => '{n} fajl',
        'file_plural' => '{n} fajla',
        'folder_singular' => '{n} dosje',
        'folder_plural' => '{n} dosjet',
        'empty' => 'bosh',
        'share' => 'Ndani',
        'share_copied' => 'Linja e ndarjes kopjuar në klipboard!',
        'share_expires' => 'Vendosja:',
        'share_never' => 'Asnjëherë',
        'share_1h' => '1 orë',
        'share_1d' => '1 ditë',
        'share_7d' => '7 ditë',
        'share_30d' => '30 ditë',
        'background_tasks' => 'Detyra të gjitha',
        'info_format' => 'Format',
        'info_pages' => 'Faqe',
        'info_title' => 'Titulli',
        'info_author' => 'Autori',
        'info_language' => 'Gjuha',
        'info_creator' => 'Krijuesi',
        'info_producer' => 'Prodhuesi',
        'info_pdf_version' => 'Versioni i PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Enkriptuar',
        'info_page_size' => 'Madhësia e faqes',
        'info_created' => 'Krijuar',
        'info_resolution' => 'Zgjerimi',
        'info_type' => 'Lloji',
        'info_vector' => 'Grafik vektoral',
        'info_megapixels' => 'Megapiksel',
        'info_color' => 'Ngjyra',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animuar',
        'info_yes' => 'Po',
        'info_duration' => 'Kohëzgjatja',
        'info_container' => 'Kontainieri',
        'info_overall_bitrate' => 'Bitrate i përgjithshëm',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Gjëndja e bitit',
        'info_sample_rate' => 'Frekuenca e shembullit',
        'info_channels' => 'Kanale',
    ],
    'ar' => [
        '_flag' => '🇸🇦',
        '_name' => 'العربية',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'الرئيسية',
        'download_folder' => '⤓ تنزيل المجلد',
        'toggle_theme' => 'تبديل الوضع الفاتح/المظلم',
        'search_placeholder' => 'ابحث حسب الاسم…',
        'all_types' => 'جميع الأنواع',
        'type_folder' => 'المجلدات',
        'type_image' => 'الصور',
        'type_audio' => 'الصوتيات',
        'type_video' => 'الفيديوهات',
        'type_document' => 'الوثائق',
        'type_archive' => 'الأرشيفات',
        'type_other' => 'أخرى',
        'recursive' => 'تكراري',
        'col_name' => 'الاسم',
        'col_size' => 'الحجم',
        'col_info' => 'المعلومات',
        'col_modified' => 'تم التعديل',
        'col_actions' => 'الإجراءات',
        'parent_folder' => 'المجلد الأصلية',
        'folder_empty' => 'هذا المجلد فارغ.',
        'view' => 'عرض',
        'download' => 'تنزيل',
        'open' => 'فتح',
        'zip' => 'ضغط',
        'loading' => 'جارٍ التحميل…',
        'error_loading' => 'خطأ في تحميل المعلومات',
        'no_info' => 'لا توجد معلومات متاحة',
        'config_error' => '⚠️ خطأ في الإعدادات',
        'cookie_text' => 'يستخدم هذا الموقع ملفات تعريف ارتباط ضرورية لحفظ تفضيلاتك (الوضع، ترتيب الفرز، اللغة). لا يتم تتبع أو استخدام ملفات تعريف الارتباط من طرف ثالث.',
        'cookie_ok' => 'موافق',
        'viewer_download' => '⤓ تنزيل',
        'viewer_newtab' => '↗ علامة تبويب جديدة',
        'viewer_close' => 'إغلاق (Esc)',
        'viewer_prev' => 'السابق',
        'viewer_next' => 'التالي',
        'file_too_large' => 'ملف كبير جدًا لعرضه نصيًا. استخدم "علامة تبويب جديدة" أو التنزيل.',
        'could_not_load' => 'لم يتم إمكانية فتح الملف.',
        'queue_processing' => '{running} جاري المعالجة، {pending} في قائمة الانتظار',
        'queue_no_tasks' => 'لا يوجد مهام نشطة',
        'file_singular' => '{n} ملف',
        'file_plural' => '{n} ملفات',
        'folder_singular' => '{n} مجلد',
        'folder_plural' => '{n} مجلدات',
        'empty' => 'فارغ',
        'share' => 'مشاركة',
        'share_copied' => 'تم نسخ رابط المشاركة إلى الحافظة!',
        'share_expires' => 'الانتهاء:',
        'share_never' => 'أبدًا',
        'share_1h' => 'ساعة واحدة',
        'share_1d' => 'يوم واحد',
        'share_7d' => '7 أيام',
        'share_30d' => '30 يومًا',
        'background_tasks' => 'المهام الخلفية',
        'info_format' => 'التنسيق',
        'info_pages' => 'الصفحات',
        'info_title' => 'العنوان',
        'info_author' => 'المؤلف',
        'info_language' => 'اللغة',
        'info_creator' => 'المُنشئ',
        'info_producer' => 'المنتج',
        'info_pdf_version' => 'نسخة PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'مُشفّر',
        'info_page_size' => 'حجم الصفحة',
        'info_created' => 'تم الإنشاء',
        'info_resolution' => 'الدقة',
        'info_type' => 'النوع',
        'info_vector' => 'رسم متجهي',
        'info_megapixels' => 'ميجا بكسل',
        'info_color' => 'الألوان',
        'info_alpha' => 'ألفا',
        'info_animated' => 'متحرك',
        'info_yes' => 'نعم',
        'info_duration' => 'المدة',
        'info_container' => 'الحاوية',
        'info_overall_bitrate' => 'معدل بت الكلي',
        'info_video' => 'فيديو',
        'info_audio' => 'صوت',
        'info_bitrate' => 'معدل البت',
        'info_fps' => 'إطارات في الثانية',
        'info_bit_depth' => 'عمق البت',
        'info_sample_rate' => 'معدل العينة',
        'info_channels' => 'القنوات',
    ],
    'bg' => [
        '_flag' => '🇧🇬',
        '_name' => 'Български',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Начало',
        'download_folder' => '⤓ Изтегли папка',
        'toggle_theme' => 'Превключване между светло/тъмно режим',
        'search_placeholder' => 'Търсене по име…',
        'all_types' => 'Всички типове',
        'type_folder' => 'Папки',
        'type_image' => 'Снимки',
        'type_audio' => 'Аудио',
        'type_video' => 'Видео',
        'type_document' => 'Документи',
        'type_archive' => 'Архиви',
        'type_other' => 'Други',
        'recursive' => 'Рекурсивно',
        'col_name' => 'Име',
        'col_size' => 'Размер',
        'col_info' => 'Инфо',
        'col_modified' => 'Модифициран',
        'col_actions' => 'Действия',
        'parent_folder' => 'Родителска папка',
        'folder_empty' => 'Тази папка е празна.',
        'view' => 'Преглед',
        'download' => 'Изтегли',
        'open' => 'Отвори',
        'zip' => 'Сгърчи',
        'loading' => 'Зареждане…',
        'error_loading' => 'Грешка при зареждане на информацията',
        'no_info' => 'Няма налична информация',
        'config_error' => '⚠️ Грешка в конфигурацията',
        'cookie_text' => 'Този сайт използва задължителни бисквитки, за да запази вашите предпочитания (режим, ред на сортиране, език). Няма проследяване или трета страна бисквитки.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Изтегли',
        'viewer_newtab' => '↗ Нов прозорец',
        'viewer_close' => 'Затвори (Esc)',
        'viewer_prev' => 'Предишна',
        'viewer_next' => 'Следваща',
        'file_too_large' => 'Файлът е твърде голям за текстова преглед. Използвайте „Нов прозорец“ или изтеглянето.',
        'could_not_load' => 'Не може да се зареди файла.',
        'queue_processing' => '{running} обработка, {pending} в опашка',
        'queue_no_tasks' => 'Няма активни задачи',
        'file_singular' => '{n} файл',
        'file_plural' => '{n} файла',
        'folder_singular' => '{n} папка',
        'folder_plural' => '{n} папки',
        'empty' => 'празен',
        'share' => 'Сподели',
        'share_copied' => 'Линкът за споделяне е копиран в клипборда!',
        'share_expires' => 'Изтичане:',
        'share_never' => 'Никога',
        'share_1h' => '1 час',
        'share_1d' => '1 ден',
        'share_7d' => '7 дни',
        'share_30d' => '30 дни',
        'background_tasks' => 'Фонови задачи',
        'info_format' => 'Формат',
        'info_pages' => 'Страници',
        'info_title' => 'Заглавие',
        'info_author' => 'Автор',
        'info_language' => 'Език',
        'info_creator' => 'Създател',
        'info_producer' => 'Производител',
        'info_pdf_version' => 'Версия PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Шифрован',
        'info_page_size' => 'Размер на страница',
        'info_created' => 'Създаден',
        'info_resolution' => 'Резолюция',
        'info_type' => 'Тип',
        'info_vector' => 'Векторна графика',
        'info_megapixels' => 'Мегапиксели',
        'info_color' => 'Цвят',
        'info_alpha' => 'Алфа',
        'info_animated' => 'Анимиран',
        'info_yes' => 'Да',
        'info_duration' => 'Продължителност',
        'info_container' => 'Контейнер',
        'info_overall_bitrate' => 'Обща скорост на пренос',
        'info_video' => 'Видео',
        'info_audio' => 'Аудио',
        'info_bitrate' => 'Скорост на пренос',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Битова дълбочина',
        'info_sample_rate' => 'Честота на извличане',
        'info_channels' => 'Канали',
    ],
    'bn' => [
        '_flag' => '🇧🇩',
        '_name' => 'বাংলা',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Download folder',
        'toggle_theme' => 'Toggle light/dark mode',
        'search_placeholder' => 'Search by name…',
        'all_types' => 'All types',
        'type_folder' => 'Folders',
        'type_image' => 'Images',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documents',
        'type_archive' => 'Archives',
        'type_other' => 'Other',
        'recursive' => 'Recursive',
        'col_name' => 'Name',
        'col_size' => 'Size',
        'col_info' => 'Info',
        'col_modified' => 'Modified',
        'col_actions' => 'Actions',
        'parent_folder' => 'Parent folder',
        'folder_empty' => 'This folder is empty.',
        'view' => 'View',
        'download' => 'Download',
        'open' => 'Open',
        'zip' => 'Zip',
        'loading' => 'Loading…',
        'error_loading' => 'Error loading info',
        'no_info' => 'No info available',
        'config_error' => '⚠️ Configuration Error',
        'cookie_text' => 'This site uses essential cookies to save your preferences (theme, sort order, language). No tracking or third-party cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Download',
        'viewer_newtab' => '↗ New Tab',
        'viewer_close' => 'Close (Esc)',
        'viewer_prev' => 'Previous',
        'viewer_next' => 'Next',
        'file_too_large' => 'File too large for text preview. Use "New Tab" or download.',
        'could_not_load' => 'Could not load file.',
        'queue_processing' => '{running} processing, {pending} queued',
        'queue_no_tasks' => 'No active tasks',
        'file_singular' => '{n} file',
        'file_plural' => '{n} files',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folders',
        'empty' => 'empty',
        'share' => 'Share',
        'share_copied' => 'Share link copied to clipboard!',
        'share_expires' => 'Expiration:',
        'share_never' => 'Never',
        'share_1h' => '1 hour',
        'share_1d' => '1 day',
        'share_7d' => '7 days',
        'share_30d' => '30 days',
        'background_tasks' => 'Background tasks',
        'info_format' => 'Format',
        'info_pages' => 'Pages',
        'info_title' => 'Title',
        'info_author' => 'Author',
        'info_language' => 'Language',
        'info_creator' => 'Creator',
        'info_producer' => 'Producer',
        'info_pdf_version' => 'PDF version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encrypted',
        'info_page_size' => 'Page size',
        'info_created' => 'Created',
        'info_resolution' => 'Resolution',
        'info_type' => 'Type',
        'info_vector' => 'Vector graphic',
        'info_megapixels' => 'Megapixels',
        'info_color' => 'Color',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animated',
        'info_yes' => 'Yes',
        'info_duration' => 'Duration',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Overall bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit depth',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Channels',
    ],
    'ca' => [
        '_flag' => '🇪🇸',
        '_name' => 'Català',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Inici',
        'download_folder' => '⤓ Descarregar carpeta',
        'toggle_theme' => 'Alternar mode clar/fosc',
        'search_placeholder' => 'Cercar per nom…',
        'all_types' => 'Tot tipus',
        'type_folder' => 'Carpetes',
        'type_image' => 'Imatges',
        'type_audio' => 'Àudio',
        'type_video' => 'Vídeo',
        'type_document' => 'Documents',
        'type_archive' => 'Arxius',
        'type_other' => 'Altres',
        'recursive' => 'Recursiu',
        'col_name' => 'Nom',
        'col_size' => 'Mida',
        'col_info' => 'Informació',
        'col_modified' => 'Modificat',
        'col_actions' => 'Accions',
        'parent_folder' => 'Carpeta parent',
        'folder_empty' => 'Aquesta carpeta està buida.',
        'view' => 'Veure',
        'download' => 'Descarregar',
        'open' => 'Obre',
        'zip' => 'Zip',
        'loading' => 'Carregant…',
        'error_loading' => 'Error carregant informació',
        'no_info' => 'No hi ha informació disponible',
        'config_error' => '⚠️ Error de configuració',
        'cookie_text' => 'Aquest lloc utilitza galetes essencials per desar les teves preferències (tema, ordre de classificació, idioma). No s\'utilitzen galetes de seguiment ni de tercers.',
        'cookie_ok' => 'D\'acord',
        'viewer_download' => '⤓ Descarregar',
        'viewer_newtab' => '↗ Nova pestanya',
        'viewer_close' => 'Tancar (Esc)',
        'viewer_prev' => 'Anterior',
        'viewer_next' => 'Següent',
        'file_too_large' => 'El fitxer és massa gran per a una previsualització de text. Utilitzeu «Nova pestanya» o descarregueu-lo.',
        'could_not_load' => 'No s\'ha pogut carregar el fitxer.',
        'queue_processing' => '{running} processant, {pending} en cua',
        'queue_no_tasks' => 'Cap tasca activa',
        'file_singular' => '{n} fitxer',
        'file_plural' => '{n} fitxers',
        'folder_singular' => '{n} carpeta',
        'folder_plural' => '{n} carpetes',
        'empty' => 'buit',
        'share' => 'Compartir',
        'share_copied' => 'Enllaç de compartició copiat al porta-retalls!',
        'share_expires' => 'Expiració:',
        'share_never' => 'Mai',
        'share_1h' => '1 hora',
        'share_1d' => '1 dia',
        'share_7d' => '7 dies',
        'share_30d' => '30 dies',
        'background_tasks' => 'Tasques en segon pla',
        'info_format' => 'Format',
        'info_pages' => 'Pàgines',
        'info_title' => 'Títol',
        'info_author' => 'Autor',
        'info_language' => 'Idioma',
        'info_creator' => 'Creador',
        'info_producer' => 'Productor',
        'info_pdf_version' => 'Versió PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encriptat',
        'info_page_size' => 'Mida de pàgina',
        'info_created' => 'Creat',
        'info_resolution' => 'Resolució',
        'info_type' => 'Tipus',
        'info_vector' => 'Gràfic vectorial',
        'info_megapixels' => 'Megapíxels',
        'info_color' => 'Color',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animat',
        'info_yes' => 'Sí',
        'info_duration' => 'Durada',
        'info_container' => 'Contenedor',
        'info_overall_bitrate' => 'Bitrate general',
        'info_video' => 'Vídeo',
        'info_audio' => 'Àudio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Profunditat de bits',
        'info_sample_rate' => 'Freqüència d\'amostratge',
        'info_channels' => 'Canals',
    ],
    'cy' => [
        '_flag' => '🇬🇧',
        '_name' => 'Cymraeg',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Gartref',
        'download_folder' => '⤓ Llwyddo ffolder',
        'toggle_theme' => 'Swyddio rhagor/llawn y modd',
        'search_placeholder' => 'Chwilio yn ôl enw…',
        'all_types' => 'Pob math',
        'type_folder' => 'Ffolderiau',
        'type_image' => 'Llyfrau',
        'type_audio' => 'Audio',
        'type_video' => 'Fideo',
        'type_document' => 'Ddogfenion',
        'type_archive' => 'Archifau',
        'type_other' => 'Eithafer',
        'recursive' => 'Gyflwr gynhyrchol',
        'col_name' => 'Enw',
        'col_size' => 'Maint',
        'col_info' => 'Gwybodaeth',
        'col_modified' => 'Newidwyd',
        'col_actions' => 'Gweithredu',
        'parent_folder' => 'Ffolder pen',
        'folder_empty' => 'Mae\'r ffolder yn wag.',
        'view' => 'Gweld',
        'download' => 'Llwyddo',
        'open' => 'Agor',
        'zip' => 'Zip',
        'loading' => 'Yn llenwi…',
        'error_loading' => 'Gwall yn llenwi gwybodaeth',
        'no_info' => 'Dim gwybodaeth ar gael',
        'config_error' => '⚠️ Gwall ymgyrchu',
        'cookie_text' => 'Mae\'r wefan hwn yn defnyddio cwcis hanfodol i gadarnhau eich dewisiadau (modd, ardyn gwneud ystyried, iaith). Dim cyfrif tracu na cwcis gylchredol.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Llwyddo',
        'viewer_newtab' => '↗ Tab newydd',
        'viewer_close' => 'Cau (Esc)',
        'viewer_prev' => 'Gwaelod',
        'viewer_next' => 'Nesaf',
        'file_too_large' => 'Ffeil yn rhy fawr am ddynodwr testun. Defnyddiwch "Tab newydd" neu llwyddo.',
        'could_not_load' => 'Ni chafodd y ffeil ei llenwi.',
        'queue_processing' => '{running} yn gweithredu, {pending} wedi\'u gosod yn y rhesymwl',
        'queue_no_tasks' => 'Dim gweithredu\'n barod',
        'file_singular' => '{n} ffeil',
        'file_plural' => '{n} ffeiliau',
        'folder_singular' => '{n} ffolder',
        'folder_plural' => '{n} ffolderiau',
        'empty' => 'wag',
        'share' => 'Rhannu',
        'share_copied' => 'Linc rhannu wedi\'i gopïo i\'r ystod!',
        'share_expires' => 'Gorffen:',
        'share_never' => 'Bydd yn aros am byd',
        'share_1h' => '1 awr',
        'share_1d' => '1 dydd',
        'share_7d' => '7 diwrnod',
        'share_30d' => '30 diwrnod',
        'background_tasks' => 'Gweithredu ar y fath',
        'info_format' => 'Fformat',
        'info_pages' => 'Tudalennau',
        'info_title' => 'Teitl',
        'info_author' => 'Awdur',
        'info_language' => 'Iaith',
        'info_creator' => 'Creuwr',
        'info_producer' => 'Gweithredwr',
        'info_pdf_version' => 'Fersiwn PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Cyfrifol',
        'info_page_size' => 'Maint tudalen',
        'info_created' => 'Aroddwyd',
        'info_resolution' => 'Ymchwiliad',
        'info_type' => 'Math',
        'info_vector' => 'Grafeg ymlaen',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Lliw',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Anifail',
        'info_yes' => 'Ydy',
        'info_duration' => 'Cyfleu',
        'info_container' => 'Cofnodwr',
        'info_overall_bitrate' => 'Bithrydd cyfan',
        'info_video' => 'Fideo',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bithrydd',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Gylchrededd bith',
        'info_sample_rate' => 'Cyfradd samlu',
        'info_channels' => 'Cynhyrchion',
    ],
    'cz' => [
        '_flag' => '🇨🇿',
        '_name' => 'Čeština',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Domů',
        'download_folder' => '⤓ Stáhnout složku',
        'toggle_theme' => 'Přepnout režim světlý/tmavý',
        'search_placeholder' => 'Hledat podle názvu…',
        'all_types' => 'Všechny typy',
        'type_folder' => 'Složky',
        'type_image' => 'Obrázky',
        'type_audio' => 'Zvuk',
        'type_video' => 'Video',
        'type_document' => 'Dokumenty',
        'type_archive' => 'Archivy',
        'type_other' => 'Jiné',
        'recursive' => 'Rekurzivně',
        'col_name' => 'Název',
        'col_size' => 'Velikost',
        'col_info' => 'Info',
        'col_modified' => 'Upraveno',
        'col_actions' => 'Akce',
        'parent_folder' => 'Nadřazená složka',
        'folder_empty' => 'Tato složka je prázdná.',
        'view' => 'Zobrazit',
        'download' => 'Stáhnout',
        'open' => 'Otevřít',
        'zip' => 'Sbalit do ZIPu',
        'loading' => 'Načítání…',
        'error_loading' => 'Chyba při načítání informací',
        'no_info' => 'Žádné dostupné informace',
        'config_error' => '⚠️ Chyba konfigurace',
        'cookie_text' => 'Tato stránka používá esenciální soubory cookie k uložení vašich preferencí (téma, pořadí třídění, jazyk). Žádné sledování nebo třetí strany cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Stáhnout',
        'viewer_newtab' => '↗ Nová karta',
        'viewer_close' => 'Zavřít (Esc)',
        'viewer_prev' => 'Předchozí',
        'viewer_next' => 'Další',
        'file_too_large' => 'Soubor je příliš velký pro textový náhled. Použijte „Novou kartu“ nebo stáhněte ho.',
        'could_not_load' => 'Nelze načíst soubor.',
        'queue_processing' => '{running} zpracování, {pending} v čekající frontě',
        'queue_no_tasks' => 'Žádné aktivity',
        'file_singular' => '{n} soubor',
        'file_plural' => '{n} soubory',
        'folder_singular' => '{n} složka',
        'folder_plural' => '{n} složky',
        'empty' => 'prázdný',
        'share' => 'Sdílet',
        'share_copied' => 'Odkaz pro sdílení zkopírován do schránky!',
        'share_expires' => 'Platnost:',
        'share_never' => 'Nikdy',
        'share_1h' => '1 hodina',
        'share_1d' => '1 den',
        'share_7d' => '7 dní',
        'share_30d' => '30 dní',
        'background_tasks' => 'Pozadové úkoly',
        'info_format' => 'Formát',
        'info_pages' => 'Stránky',
        'info_title' => 'Název',
        'info_author' => 'Autor',
        'info_language' => 'Jazyk',
        'info_creator' => 'Vytvořil',
        'info_producer' => 'Výrobce',
        'info_pdf_version' => 'Verze PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Šifrovaný',
        'info_page_size' => 'Velikost stránky',
        'info_created' => 'Vytvořeno',
        'info_resolution' => 'Rozlišení',
        'info_type' => 'Typ',
        'info_vector' => 'Vektorová grafika',
        'info_megapixels' => 'Megapixelů',
        'info_color' => 'Barva',
        'info_alpha' => 'Alfa kanál',
        'info_animated' => 'Animovaný',
        'info_yes' => 'Ano',
        'info_duration' => 'Trvání',
        'info_container' => 'Kontejner',
        'info_overall_bitrate' => 'Celková bitová rychlost',
        'info_video' => 'Video',
        'info_audio' => 'Zvuk',
        'info_bitrate' => 'Bitová rychlost',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Hloubka bitů',
        'info_sample_rate' => 'Vzorkovací frekvence',
        'info_channels' => 'Kanály',
    ],
    'da' => [
        '_flag' => '🇩🇰',
        '_name' => 'Dansk',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Hjem',
        'download_folder' => '⤓ Download mappe',
        'toggle_theme' => 'Skift mellem lys/mørk tilstand',
        'search_placeholder' => 'Søg efter navn…',
        'all_types' => 'Alle typer',
        'type_folder' => 'Mapper',
        'type_image' => 'Billeder',
        'type_audio' => 'Lyd',
        'type_video' => 'Video',
        'type_document' => 'Dokumenter',
        'type_archive' => 'Arkiver',
        'type_other' => 'Andre',
        'recursive' => 'Rekursivt',
        'col_name' => 'Navn',
        'col_size' => 'Størrelse',
        'col_info' => 'Info',
        'col_modified' => 'Ændret',
        'col_actions' => 'Handlinger',
        'parent_folder' => 'Overordnet mappe',
        'folder_empty' => 'Denne mappe er tom.',
        'view' => 'Vis',
        'download' => 'Download',
        'open' => 'Åbn',
        'zip' => 'Zip',
        'loading' => 'Indlæser…',
        'error_loading' => 'Fejl ved indlæsning af information',
        'no_info' => 'Ingen info tilgængelig',
        'config_error' => '⚠️ Konfigurationsfejl',
        'cookie_text' => 'Denne side bruger essentielle cookies til at gemme dine præferencer (tema, sortering, sprog). Ingen sporings- eller tredjepartscookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Download',
        'viewer_newtab' => '↗ Ny fane',
        'viewer_close' => 'Luk (Esc)',
        'viewer_prev' => 'Forrige',
        'viewer_next' => 'Næste',
        'file_too_large' => 'Fil for stor til tekstforhåndsvisning. Brug "Ny fane" eller download.',
        'could_not_load' => 'Kunne ikke indlæse filen.',
        'queue_processing' => '{running} behandler, {pending} i kø',
        'queue_no_tasks' => 'Ingen aktive opgaver',
        'file_singular' => '{n} fil',
        'file_plural' => '{n} filer',
        'folder_singular' => '{n} mappe',
        'folder_plural' => '{n} mapper',
        'empty' => 'tom',
        'share' => 'Del',
        'share_copied' => 'Delingslink kopieret til udklipsholder!',
        'share_expires' => 'Udløber:',
        'share_never' => 'Aldrig',
        'share_1h' => '1 time',
        'share_1d' => '1 dag',
        'share_7d' => '7 dage',
        'share_30d' => '30 dage',
        'background_tasks' => 'Baggrundsopgaver',
        'info_format' => 'Format',
        'info_pages' => 'Sider',
        'info_title' => 'Titel',
        'info_author' => 'Forfatter',
        'info_language' => 'Sprog',
        'info_creator' => 'Oprettet af',
        'info_producer' => 'Producent',
        'info_pdf_version' => 'PDF version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Krypteret',
        'info_page_size' => 'Sides størrelse',
        'info_created' => 'Oprettet',
        'info_resolution' => 'Oppløsning',
        'info_type' => 'Type',
        'info_vector' => 'Vektorgrafik',
        'info_megapixels' => 'Megapiksler',
        'info_color' => 'Farve',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animeret',
        'info_yes' => 'Ja',
        'info_duration' => 'Varighed',
        'info_container' => 'Beholder',
        'info_overall_bitrate' => 'Samlet bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Lyd',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitdybde',
        'info_sample_rate' => 'Prøvefrekvens',
        'info_channels' => 'Kanaler',
    ],
    'de' => [
        '_flag' => '🇩🇪',
        '_name' => 'Deutsch',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Startseite',
        'download_folder' => '⤓Ordner herunterladen',
        'toggle_theme' => 'Hell/Dunkelmodus umschalten',
        'search_placeholder' => 'Nach Namen suchen…',
        'all_types' => 'Alle Typen',
        'type_folder' => 'Ordner',
        'type_image' => 'Bilder',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumente',
        'type_archive' => 'Archivdateien',
        'type_other' => 'Andere',
        'recursive' => 'Rekursiv',
        'col_name' => 'Name',
        'col_size' => 'Größe',
        'col_info' => 'Info',
        'col_modified' => 'Geändert',
        'col_actions' => 'Aktionen',
        'parent_folder' => 'Übergeordneter Ordner',
        'folder_empty' => 'Dieser Ordner ist leer.',
        'view' => 'Ansicht',
        'download' => 'Herunterladen',
        'open' => 'Öffnen',
        'zip' => 'ZIP',
        'loading' => 'Laden…',
        'error_loading' => 'Fehler beim Laden der Informationen',
        'no_info' => 'Keine Informationen verfügbar',
        'config_error' => '⚠️ Konfigurationsfehler',
        'cookie_text' => 'Diese Seite verwendet notwendige Cookies, um Ihre Einstellungen (Theme, Sortierung, Sprache) zu speichern. Keine Tracking- oder Drittanbieter-Cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Herunterladen',
        'viewer_newtab' => '↗ Neuer Tab',
        'viewer_close' => 'Schließen (Esc)',
        'viewer_prev' => 'Zurück',
        'viewer_next' => 'Weiter',
        'file_too_large' => 'Datei ist zu groß für Textvorschau. Verwenden Sie „Neuen Tab“ oder laden Sie sie herunter.',
        'could_not_load' => 'Datei konnte nicht geladen werden.',
        'queue_processing' => '{running} verarbeitet, {pending} in Warteschlange',
        'queue_no_tasks' => 'Keine aktiven Aufgaben',
        'file_singular' => '{n} Datei',
        'file_plural' => '{n} Dateien',
        'folder_singular' => '{n} Ordner',
        'folder_plural' => '{n} Ordner',
        'empty' => 'leer',
        'share' => 'Teilen',
        'share_copied' => 'Link zum Teilen in die Zwischenablage kopiert!',
        'share_expires' => 'Ablaufdatum:',
        'share_never' => 'Nie',
        'share_1h' => '1 Stunde',
        'share_1d' => '1 Tag',
        'share_7d' => '7 Tage',
        'share_30d' => '30 Tage',
        'background_tasks' => 'Hintergrundaufgaben',
        'info_format' => 'Format',
        'info_pages' => 'Seiten',
        'info_title' => 'Titel',
        'info_author' => 'Autor',
        'info_language' => 'Sprache',
        'info_creator' => 'Ersteller',
        'info_producer' => 'Produzent',
        'info_pdf_version' => 'PDF-Version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Verschlüsselt',
        'info_page_size' => 'Seitengröße',
        'info_created' => 'Erstellt',
        'info_resolution' => 'Auflösung',
        'info_type' => 'Typ',
        'info_vector' => 'Vektorgrafik',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Farbe',
        'info_alpha' => 'Alpha-Kanal',
        'info_animated' => 'Animiert',
        'info_yes' => 'Ja',
        'info_duration' => 'Dauer',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Gesamtbittate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bittate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit-Tiefe',
        'info_sample_rate' => 'Abtastrate',
        'info_channels' => 'Kanäle',
    ],
    'ee' => [
        '_flag' => '🇪🇪',
        '_name' => 'Eesti',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Download folder',
        'toggle_theme' => 'Toggle light/dark mode',
        'search_placeholder' => 'Search by name…',
        'all_types' => 'All types',
        'type_folder' => 'Folders',
        'type_image' => 'Images',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documents',
        'type_archive' => 'Archives',
        'type_other' => 'Other',
        'recursive' => 'Recursive',
        'col_name' => 'Name',
        'col_size' => 'Size',
        'col_info' => 'Info',
        'col_modified' => 'Modified',
        'col_actions' => 'Actions',
        'parent_folder' => 'Parent folder',
        'folder_empty' => 'This folder is empty.',
        'view' => 'View',
        'download' => 'Download',
        'open' => 'Open',
        'zip' => 'Zip',
        'loading' => 'Loading…',
        'error_loading' => 'Error loading info',
        'no_info' => 'No info available',
        'config_error' => '⚠️ Configuration Error',
        'cookie_text' => 'This site uses essential cookies to save your preferences (theme, sort order, language). No tracking or third-party cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Download',
        'viewer_newtab' => '↗ New Tab',
        'viewer_close' => 'Close (Esc)',
        'viewer_prev' => 'Previous',
        'viewer_next' => 'Next',
        'file_too_large' => 'File too large for text preview. Use "New Tab" or download.',
        'could_not_load' => 'Could not load file.',
        'queue_processing' => '{running} processing, {pending} queued',
        'queue_no_tasks' => 'No active tasks',
        'file_singular' => '{n} file',
        'file_plural' => '{n} files',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folders',
        'empty' => 'empty',
        'share' => 'Share',
        'share_copied' => 'Share link copied to clipboard!',
        'share_expires' => 'Expiration:',
        'share_never' => 'Never',
        'share_1h' => '1 hour',
        'share_1d' => '1 day',
        'share_7d' => '7 days',
        'share_30d' => '30 days',
        'background_tasks' => 'Background tasks',
        'info_format' => 'Format',
        'info_pages' => 'Pages',
        'info_title' => 'Title',
        'info_author' => 'Author',
        'info_language' => 'Language',
        'info_creator' => 'Creator',
        'info_producer' => 'Producer',
        'info_pdf_version' => 'PDF version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encrypted',
        'info_page_size' => 'Page size',
        'info_created' => 'Created',
        'info_resolution' => 'Resolution',
        'info_type' => 'Type',
        'info_vector' => 'Vector graphic',
        'info_megapixels' => 'Megapixels',
        'info_color' => 'Color',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animated',
        'info_yes' => 'Yes',
        'info_duration' => 'Duration',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Overall bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit depth',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Channels',
    ],
    'eu' => [
        '_flag' => '🇪🇸',
        '_name' => 'Euskera',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Etxea',
        'download_folder' => '⤓ Deskargatu karpeta',
        'toggle_theme' => 'Aldatu argi/egun modua',
        'search_placeholder' => 'Bilatu izenarekin…',
        'all_types' => 'Denak',
        'type_folder' => 'Karpeta',
        'type_image' => 'Irudiak',
        'type_audio' => 'Audioa',
        'type_video' => 'Videoa',
        'type_document' => 'Dokumentuak',
        'type_archive' => 'Arkibat',
        'type_other' => 'Besteak',
        'recursive' => 'Rekursibo',
        'col_name' => 'Izena',
        'col_size' => 'Tamaina',
        'col_info' => 'Informazioa',
        'col_modified' => 'Aldatu',
        'col_actions' => 'Ekintzak',
        'parent_folder' => 'Alderdi karpeta',
        'folder_empty' => 'Karpeta hau hutsik dago.',
        'view' => 'Ikusi',
        'download' => 'Deskargatu',
        'open' => 'Ireki',
        'zip' => 'Zip',
        'loading' => 'Kargatzen…',
        'error_loading' => 'Errorea informazioa kargatzeko',
        'no_info' => 'Ez dago informaziorik',
        'config_error' => '⚠️ Konfigurazio errorea',
        'cookie_text' => 'Webgune hau zure hobespenak (modua, ordena, hizkuntza) gordetzeko esentsialko cookieak erabiltzen ditu. Ez da jasotze edo beste webguneen cookieak erabiltzen.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Deskargatu',
        'viewer_newtab' => '↗ Tabua berria',
        'viewer_close' => 'Itxi (Esc)',
        'viewer_prev' => 'Aurrekoa',
        'viewer_next' => 'Hurrengoa',
        'file_too_large' => 'Fitxategia testu aurrebistaarentzat gehiegizko tamaina du. Erabili "Tabua berria" edo deskargatu.',
        'could_not_load' => 'Ezin izan da fitxategia kargatu.',
        'queue_processing' => '{running} prozesatzen, {pending} kokatuta',
        'queue_no_tasks' => 'Ez dago aktiboak izan diren gertakarik',
        'file_singular' => '{n} fitxategia',
        'file_plural' => '{n} fitxategi',
        'folder_singular' => '{n} karpeta',
        'folder_plural' => '{n} karpeta',
        'empty' => 'hutsik',
        'share' => 'Partekatu',
        'share_copied' => 'Partekatze esteka arkupean kopiatuta!',
        'share_expires' => 'Expiroa:',
        'share_never' => 'Inoiz ez',
        'share_1h' => 'Ordu bat',
        'share_1d' => 'Egun bat',
        'share_7d' => '7 egun',
        'share_30d' => '30 egun',
        'background_tasks' => 'Atzetik gertakarik',
        'info_format' => 'Formatua',
        'info_pages' => 'Orrialdeak',
        'info_title' => 'Izenburua',
        'info_author' => 'Egilea',
        'info_language' => 'Hizkuntza',
        'info_creator' => 'Sortzailea',
        'info_producer' => 'Producera',
        'info_pdf_version' => 'PDF bertsioa',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Enkriptatuta',
        'info_page_size' => 'Orrialdearen tamaina',
        'info_created' => 'Sortuta',
        'info_resolution' => 'Irudimentsua',
        'info_type' => 'Mota',
        'info_vector' => 'Bektore irudi',
        'info_megapixels' => 'Megapikeldunak',
        'info_color' => 'Kolorea',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animatua',
        'info_yes' => 'Bai',
        'info_duration' => 'Iraupena',
        'info_container' => 'Behin-behineko beharraren kontainera',
        'info_overall_bitrate' => 'Bitrate orokorra',
        'info_video' => 'Videoa',
        'info_audio' => 'Audioa',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitaren luzera',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Kanpoak',
    ],
    'eo' => [
        '_flag' => '🟢',
        '_name' => 'Esperanto',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Hejmpaĝo',
        'download_folder' => '⤓ Elŝuti dosierujo',
        'toggle_theme' => 'Ŝalti lum/malhela reĝimo',
        'search_placeholder' => 'Serĉi per nomo…',
        'all_types' => 'Ĉiuj tipoj',
        'type_folder' => 'Dosierujoj',
        'type_image' => 'Bildoj',
        'type_audio' => 'Aŭdio',
        'type_video' => 'Video',
        'type_document' => 'Dokumentoj',
        'type_archive' => 'Arkivoj',
        'type_other' => 'Aliaj',
        'recursive' => 'Rekursiva',
        'col_name' => 'Nomo',
        'col_size' => 'Grando',
        'col_info' => 'Informo',
        'col_modified' => 'Modifita',
        'col_actions' => 'Akcioj',
        'parent_folder' => 'Superdosierujo',
        'folder_empty' => 'Ĉi tiu dosierujo estas malplena.',
        'view' => 'Vidi',
        'download' => 'Elŝuti',
        'open' => 'Malfermi',
        'zip' => 'Pakigi',
        'loading' => 'Ŝarĝante…',
        'error_loading' => 'Eraro dum ŝarĝado de informoj',
        'no_info' => 'Neniu informo disponebla',
        'config_error' => '⚠️ Konfigura Eraro',
        'cookie_text' => 'Ĉi tiu retejo uzas esencajn koĉikojn por konservi viajn preferojn (temo, ordigo, lingvo). Neniu sekurigado aŭ eksteraj koĉikoj.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Elŝuti',
        'viewer_newtab' => '↗ Nova Sekcio',
        'viewer_close' => 'Fermi (Esc)',
        'viewer_prev' => 'Antaŭa',
        'viewer_next' => 'Sekva',
        'file_too_large' => 'Dosiero tro granda por teksta aperanto. Uzu "Nova Sekcio" aŭ elŝuti.',
        'could_not_load' => 'Ne eblis ŝarĝi dosieron.',
        'queue_processing' => '{running} procesado, {pending} en atendo',
        'queue_no_tasks' => 'Neniu aktiva tasko',
        'file_singular' => '{n} dosiero',
        'file_plural' => '{n} dosieroj',
        'folder_singular' => '{n} dosierujo',
        'folder_plural' => '{n} dosierujoj',
        'empty' => 'malplena',
        'share' => 'Kunigi',
        'share_copied' => 'Ligilo por kunigo kopita al la tonduro!',
        'share_expires' => 'Eksvalidiĝo:',
        'share_never' => 'Neniam',
        'share_1h' => '1 horo',
        'share_1d' => '1 tago',
        'share_7d' => '7 tagoj',
        'share_30d' => '30 tagoj',
        'background_tasks' => 'Fonaj taskoj',
        'info_format' => 'Formato',
        'info_pages' => 'Paĝoj',
        'info_title' => 'Titolo',
        'info_author' => 'Aŭtoro',
        'info_language' => 'Lingvo',
        'info_creator' => 'Kreadinto',
        'info_producer' => 'Producinto',
        'info_pdf_version' => 'PDF versio',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Ĉifrita',
        'info_page_size' => 'Paĝograndeco',
        'info_created' => 'Kreita',
        'info_resolution' => 'Rizado',
        'info_type' => 'Tipo',
        'info_vector' => 'Vektora bildo',
        'info_megapixels' => 'Megapikseloj',
        'info_color' => 'Koloro',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animaĵa',
        'info_yes' => 'Jes',
        'info_duration' => 'Duraĵo',
        'info_container' => 'Kontentigilo',
        'info_overall_bitrate' => 'Tuta bitrapideco',
        'info_video' => 'Video',
        'info_audio' => 'Aŭdio',
        'info_bitrate' => 'Bitrapideco',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitprofundo',
        'info_sample_rate' => 'Echantigrapideco',
        'info_channels' => 'Kanaloj',
    ],
    'es' => [
        '_flag' => '🇪🇸',
        '_name' => 'Español',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Inicio',
        'download_folder' => '⤓ Descargar carpeta',
        'toggle_theme' => 'Cambiar modo claro/oscuro',
        'search_placeholder' => 'Buscar por nombre…',
        'all_types' => 'Todos los tipos',
        'type_folder' => 'Carpetas',
        'type_image' => 'Imágenes',
        'type_audio' => 'Audio',
        'type_video' => 'Vídeo',
        'type_document' => 'Documentos',
        'type_archive' => 'Archivos comprimidos',
        'type_other' => 'Otros',
        'recursive' => 'Recursivo',
        'col_name' => 'Nombre',
        'col_size' => 'Tamaño',
        'col_info' => 'Información',
        'col_modified' => 'Modificado',
        'col_actions' => 'Acciones',
        'parent_folder' => 'Carpeta superior',
        'folder_empty' => 'Esta carpeta está vacía.',
        'view' => 'Ver',
        'download' => 'Descargar',
        'open' => 'Abrir',
        'zip' => 'Comprimir en ZIP',
        'loading' => 'Cargando…',
        'error_loading' => 'Error al cargar la información',
        'no_info' => 'No hay información disponible',
        'config_error' => '⚠️ Error de configuración',
        'cookie_text' => 'Este sitio usa cookies esenciales para guardar tus preferencias (tema, orden de clasificación, idioma). No se usan cookies de seguimiento ni de terceros.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Descargar',
        'viewer_newtab' => '↗ Nueva pestaña',
        'viewer_close' => 'Cerrar (Esc)',
        'viewer_prev' => 'Anterior',
        'viewer_next' => 'Siguiente',
        'file_too_large' => 'El archivo es demasiado grande para la vista previa de texto. Use "Nueva pestaña" o descargue el archivo.',
        'could_not_load' => 'No se pudo cargar el archivo.',
        'queue_processing' => '{running} procesando, {pending} en cola',
        'queue_no_tasks' => 'No hay tareas activas',
        'file_singular' => '{n} archivo',
        'file_plural' => '{n} archivos',
        'folder_singular' => '{n} carpeta',
        'folder_plural' => '{n} carpetas',
        'empty' => 'vacío',
        'share' => 'Compartir',
        'share_copied' => 'Enlace copiado al portapapeles!',
        'share_expires' => 'Caducidad:',
        'share_never' => 'Nunca',
        'share_1h' => '1 hora',
        'share_1d' => '1 día',
        'share_7d' => '7 días',
        'share_30d' => '30 días',
        'background_tasks' => 'Tareas en segundo plano',
        'info_format' => 'Formato',
        'info_pages' => 'Páginas',
        'info_title' => 'Título',
        'info_author' => 'Autor',
        'info_language' => 'Idioma',
        'info_creator' => 'Creador',
        'info_producer' => 'Productor',
        'info_pdf_version' => 'Versión PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encriptado',
        'info_page_size' => 'Tamaño de página',
        'info_created' => 'Creado',
        'info_resolution' => 'Resolución',
        'info_type' => 'Tipo',
        'info_vector' => 'Gráfico vectorial',
        'info_megapixels' => 'Megapíxeles',
        'info_color' => 'Color',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animado',
        'info_yes' => 'Sí',
        'info_duration' => 'Duración',
        'info_container' => 'Contenedor',
        'info_overall_bitrate' => 'Bitrate total',
        'info_video' => 'Vídeo',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Profundidad de bit',
        'info_sample_rate' => 'Frecuencia de muestreo',
        'info_channels' => 'Canales',
    ],
    'fi' => [
        '_flag' => '🇫🇮',
        '_name' => 'Suomi',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Koti',
        'download_folder' => '⤓ Lataa kansio',
        'toggle_theme' => 'Vaihda valo-/tumma-tilaa',
        'search_placeholder' => 'Etsi nimellä…',
        'all_types' => 'Kaikki tyypit',
        'type_folder' => 'Kansiot',
        'type_image' => 'Kuvat',
        'type_audio' => 'Äänitiedostot',
        'type_video' => 'Videot',
        'type_document' => 'Dokumentit',
        'type_archive' => 'Arkistot',
        'type_other' => 'Muut',
        'recursive' => 'Rekursiivinen',
        'col_name' => 'Nimi',
        'col_size' => 'Koko',
        'col_info' => 'Tiedot',
        'col_modified' => 'Muokattu',
        'col_actions' => 'Toiminnot',
        'parent_folder' => 'Ylähakemisto',
        'folder_empty' => 'Tämä kansio on tyhjä.',
        'view' => 'Näytä',
        'download' => 'Lataa',
        'open' => 'Avaa',
        'zip' => 'Zip',
        'loading' => 'Ladataan…',
        'error_loading' => 'Virhe tiedon latauksessa',
        'no_info' => 'Tietoa ei saatavilla',
        'config_error' => '⚠️ Konfiguraatiivirhe',
        'cookie_text' => 'Tämä sivusto käyttää välttämättömiä evästeitä tallentaakseen asetuksesi (tila, järjestys, kieli). Ei seurantaa tai kolmannen osapuolen evästeitä.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Lataa',
        'viewer_newtab' => '↗ Uusi välilehti',
        'viewer_close' => 'Sulje (Esc)',
        'viewer_prev' => 'Edellinen',
        'viewer_next' => 'Seuraava',
        'file_too_large' => 'Tiedosto on liian suuri tekstiesitykseen. Käytä "Uusi välilehti" tai lataa.',
        'could_not_load' => 'Tiedostoa ei voitu ladata.',
        'queue_processing' => '{running} käsitellään, {pending} odotetaan',
        'queue_no_tasks' => 'Ei aktiivisia tehtäviä',
        'file_singular' => '{n} tiedosto',
        'file_plural' => '{n} tiedostoa',
        'folder_singular' => '{n} kansio',
        'folder_plural' => '{n} kansiota',
        'empty' => 'tyhjä',
        'share' => 'Jaa',
        'share_copied' => 'Jaa-linkki kopioitu leikepöydälle!',
        'share_expires' => 'Kadun aika:',
        'share_never' => 'Ei koskaan',
        'share_1h' => '1 tunti',
        'share_1d' => '1 päivä',
        'share_7d' => '7 päivää',
        'share_30d' => '30 päivää',
        'background_tasks' => 'Taustatehtävät',
        'info_format' => 'Muoto',
        'info_pages' => 'Sivut',
        'info_title' => 'Otsikko',
        'info_author' => 'Tekijä',
        'info_language' => 'Kieli',
        'info_creator' => 'Luojat',
        'info_producer' => 'Tuottaja',
        'info_pdf_version' => 'PDF-versio',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Salattu',
        'info_page_size' => 'Sivukoko',
        'info_created' => 'Luotu',
        'info_resolution' => 'Resoluutio',
        'info_type' => 'Tyyppi',
        'info_vector' => 'Vektorikuva',
        'info_megapixels' => 'Megapikselit',
        'info_color' => 'väri',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animaatio',
        'info_yes' => 'Kyllä',
        'info_duration' => 'Kesto',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Yhteensä bittinopeus',
        'info_video' => 'Video',
        'info_audio' => 'Ääni',
        'info_bitrate' => 'Bittinopeus',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bittisyvyys',
        'info_sample_rate' => 'Näytteenottotaajuus',
        'info_channels' => 'Kanavat',
    ],
    'fr' => [
        '_flag' => '🇫🇷',
        '_name' => 'Français',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Accueil',
        'download_folder' => '⤓ Télécharger le dossier',
        'toggle_theme' => 'Basculer entre le mode clair/sombre',
        'search_placeholder' => 'Rechercher par nom…',
        'all_types' => 'Tous les types',
        'type_folder' => 'Dossiers',
        'type_image' => 'Images',
        'type_audio' => 'Audio',
        'type_video' => 'Vidéo',
        'type_document' => 'Documents',
        'type_archive' => 'Archives',
        'type_other' => 'Autre',
        'recursive' => 'Récursif',
        'col_name' => 'Nom',
        'col_size' => 'Taille',
        'col_info' => 'Info',
        'col_modified' => 'Modifié',
        'col_actions' => 'Actions',
        'parent_folder' => 'Dossier parent',
        'folder_empty' => 'Ce dossier est vide.',
        'view' => 'Afficher',
        'download' => 'Télécharger',
        'open' => 'Ouvrir',
        'zip' => 'Zipper',
        'loading' => 'Chargement…',
        'error_loading' => 'Erreur lors du chargement des informations',
        'no_info' => 'Aucune information disponible',
        'config_error' => '⚠️ Erreur de configuration',
        'cookie_text' => 'Ce site utilise des cookies essentiels pour sauvegarder vos préférences (thème, ordre de tri, langue). Aucun cookie de suivi ou tiers n\'est utilisé.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Télécharger',
        'viewer_newtab' => '↗ Nouvelle onglet',
        'viewer_close' => 'Fermer (Échapper)',
        'viewer_prev' => 'Précédent',
        'viewer_next' => 'Suivant',
        'file_too_large' => 'Fichier trop volumineux pour un aperçu en texte. Utilisez « Nouvelle onglet » ou téléchargez-le.',
        'could_not_load' => 'Impossible de charger le fichier.',
        'queue_processing' => '{running} traitement en cours, {pending} en attente',
        'queue_no_tasks' => 'Aucune tâche active',
        'file_singular' => '{n} fichier',
        'file_plural' => '{n} fichiers',
        'folder_singular' => '{n} dossier',
        'folder_plural' => '{n} dossiers',
        'empty' => 'vide',
        'share' => 'Partager',
        'share_copied' => 'Lien de partage copié dans le presse-papiers !',
        'share_expires' => 'Expiration :',
        'share_never' => 'Jamais',
        'share_1h' => '1 heure',
        'share_1d' => '1 jour',
        'share_7d' => '7 jours',
        'share_30d' => '30 jours',
        'background_tasks' => 'Tâches en arrière-plan',
        'info_format' => 'Format',
        'info_pages' => 'Pages',
        'info_title' => 'Titre',
        'info_author' => 'Auteur',
        'info_language' => 'Langue',
        'info_creator' => 'Créateur',
        'info_producer' => 'Producteur',
        'info_pdf_version' => 'Version PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Chiffré',
        'info_page_size' => 'Taille de page',
        'info_created' => 'Créé',
        'info_resolution' => 'Résolution',
        'info_type' => 'Type',
        'info_vector' => 'Graphique vectoriel',
        'info_megapixels' => 'Mégapixels',
        'info_color' => 'Couleur',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animé',
        'info_yes' => 'Oui',
        'info_duration' => 'Durée',
        'info_container' => 'Conteneur',
        'info_overall_bitrate' => 'Bitrate global',
        'info_video' => 'Vidéo',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Profondeur de bit',
        'info_sample_rate' => 'Fréquence d\'échantillonnage',
        'info_channels' => 'Canaux',
    ],
    'gr' => [
        '_flag' => '🇬🇷',
        '_name' => 'Ελληνικά',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Αρχική',
        'download_folder' => '⤓ Λήψη φακέλου',
        'toggle_theme' => 'Εναλλαγή λευκού/σκοτεινού χρώματος',
        'search_placeholder' => 'Αναζήτηση κατά ονόματος…',
        'all_types' => 'Όλα τα είδη',
        'type_folder' => 'Φάκελοι',
        'type_image' => 'Εικόνες',
        'type_audio' => 'Ήχος',
        'type_video' => 'Βίντεο',
        'type_document' => 'Έγγραφα',
        'type_archive' => 'Αρχεία',
        'type_other' => 'Άλλα',
        'recursive' => 'Αναδρομική',
        'col_name' => 'Όνομα',
        'col_size' => 'Μέγεθος',
        'col_info' => 'Πληροφορίες',
        'col_modified' => 'Τροποποιήθηκε',
        'col_actions' => 'Ενέργειες',
        'parent_folder' => 'Γονικός φάκελος',
        'folder_empty' => 'Ο φάκελος είναι κενός.',
        'view' => 'Προβολή',
        'download' => 'Λήψη',
        'open' => 'Άνοιγμα',
        'zip' => 'Συμπίεση',
        'loading' => 'Φόρτωση…',
        'error_loading' => 'Σφάλμα φόρτωσης πληροφοριών',
        'no_info' => 'Δεν υπάρχουν διαθέσιμες πληροφορίες',
        'config_error' => '⚠️ Σφάλμα Ρυθμίσεων',
        'cookie_text' => 'Αυτό το site χρησιμοποιεί απαραιτήτους cookies για να αποθηκεύσει τις προτιμήσεις σας (χρώμα, διάταξη, γλώσσα). Δεν υπάρχει παρακολούθηση ή cookies από τρίτους.',
        'cookie_ok' => 'ΟΚ',
        'viewer_download' => '⤓ Λήψη',
        'viewer_newtab' => '↗ Νέο Παράθυρο',
        'viewer_close' => 'Κλείσιμο (Esc)',
        'viewer_prev' => 'Προηγούμενο',
        'viewer_next' => 'Επόμενο',
        'file_too_large' => 'Το αρχείο είναι πολύ μεγάλο για προβολή κειμένου. Χρησιμοποιήστε το «Νέο Παράθυρο» ή λήψη.',
        'could_not_load' => 'Δεν μπόρεσε να φορτωθεί το αρχείο.',
        'queue_processing' => '{running} επεξεργασία, {pending} στην αναμονή',
        'queue_no_tasks' => 'Δεν υπάρχουν ενεργές εργασίες',
        'file_singular' => '{n} αρχείο',
        'file_plural' => '{n} αρχεία',
        'folder_singular' => '{n} φάκελος',
        'folder_plural' => '{n} φάκελοι',
        'empty' => 'κενό',
        'share' => 'Μοιρασμός',
        'share_copied' => 'Ο σύνδεσμος μοιρασμού αντέγραψε στο πρόχειρο!',
        'share_expires' => 'Λήξη:',
        'share_never' => 'Ποτέ',
        'share_1h' => '1 ώρα',
        'share_1d' => '1 ημέρα',
        'share_7d' => '7 ημέρες',
        'share_30d' => '30 ημέρες',
        'background_tasks' => 'Επεξεργασίες στο παρασκήνιο',
        'info_format' => 'Μορφή',
        'info_pages' => 'Σελίδες',
        'info_title' => 'Τίτλος',
        'info_author' => 'Συγγραφέας',
        'info_language' => 'Γλώσσα',
        'info_creator' => 'Δημιουργός',
        'info_producer' => 'Παραγωγός',
        'info_pdf_version' => 'Έκδοση PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Κρυπτογραφημένο',
        'info_page_size' => 'Μέγεθος σελίδας',
        'info_created' => 'Δημιουργήθηκε',
        'info_resolution' => 'Ανάλυση',
        'info_type' => 'Τύπος',
        'info_vector' => 'Διανυσματικό γραφικό',
        'info_megapixels' => 'Μεγαπίξελ',
        'info_color' => 'Χρώμα',
        'info_alpha' => 'Αλφά',
        'info_animated' => 'Κινούμενο σχέδιο',
        'info_yes' => 'Ναι',
        'info_duration' => 'Διάρκεια',
        'info_container' => 'Περιέχοντας',
        'info_overall_bitrate' => 'Συνολική bit rate',
        'info_video' => 'Βίντεο',
        'info_audio' => 'Ήχος',
        'info_bitrate' => 'Bit rate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Περιθωριακό βάθος',
        'info_sample_rate' => 'Συχνότητα δειγματοληψίας',
        'info_channels' => 'Κανάλια',
    ],
    'gu' => [
        '_flag' => '🇮🇳',
        '_name' => 'ગુજરાતી',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Download folder',
        'toggle_theme' => 'Toggle light/dark mode',
        'search_placeholder' => 'Search by name…',
        'all_types' => 'All types',
        'type_folder' => 'Folders',
        'type_image' => 'Images',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documents',
        'type_archive' => 'Archives',
        'type_other' => 'Other',
        'recursive' => 'Recursive',
        'col_name' => 'Name',
        'col_size' => 'Size',
        'col_info' => 'Info',
        'col_modified' => 'Modified',
        'col_actions' => 'Actions',
        'parent_folder' => 'Parent folder',
        'folder_empty' => 'This folder is empty.',
        'view' => 'View',
        'download' => 'Download',
        'open' => 'Open',
        'zip' => 'Zip',
        'loading' => 'Loading…',
        'error_loading' => 'Error loading info',
        'no_info' => 'No info available',
        'config_error' => '⚠️ Configuration Error',
        'cookie_text' => 'This site uses essential cookies to save your preferences (theme, sort order, language). No tracking or third-party cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Download',
        'viewer_newtab' => '↗ New Tab',
        'viewer_close' => 'Close (Esc)',
        'viewer_prev' => 'Previous',
        'viewer_next' => 'Next',
        'file_too_large' => 'File too large for text preview. Use "New Tab" or download.',
        'could_not_load' => 'Could not load file.',
        'queue_processing' => '{running} processing, {pending} queued',
        'queue_no_tasks' => 'No active tasks',
        'file_singular' => '{n} file',
        'file_plural' => '{n} files',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folders',
        'empty' => 'empty',
        'share' => 'Share',
        'share_copied' => 'Share link copied to clipboard!',
        'share_expires' => 'Expiration:',
        'share_never' => 'Never',
        'share_1h' => '1 hour',
        'share_1d' => '1 day',
        'share_7d' => '7 days',
        'share_30d' => '30 days',
        'background_tasks' => 'Background tasks',
        'info_format' => 'Format',
        'info_pages' => 'Pages',
        'info_title' => 'Title',
        'info_author' => 'Author',
        'info_language' => 'Language',
        'info_creator' => 'Creator',
        'info_producer' => 'Producer',
        'info_pdf_version' => 'PDF version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encrypted',
        'info_page_size' => 'Page size',
        'info_created' => 'Created',
        'info_resolution' => 'Resolution',
        'info_type' => 'Type',
        'info_vector' => 'Vector graphic',
        'info_megapixels' => 'Megapixels',
        'info_color' => 'Color',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animated',
        'info_yes' => 'Yes',
        'info_duration' => 'Duration',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Overall bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit depth',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Channels',
    ],
    'hi' => [
        '_flag' => '🇮🇳',
        '_name' => 'हिन्दी',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'होम',
        'download_folder' => '⤓ डाउनलोड फ़ोल्डर',
        'toggle_theme' => 'प्रकाश/अंधेरा मोड स्विच करें',
        'search_placeholder' => 'नाम द्वारा खोज…',
        'all_types' => 'सभी प्रकार',
        'type_folder' => 'फ़ोल्डर',
        'type_image' => 'चित्र',
        'type_audio' => 'ऑडियो',
        'type_video' => 'वीडियो',
        'type_document' => 'दस्तावेज',
        'type_archive' => 'अभिलेख',
        'type_other' => 'अन्य',
        'recursive' => 'पुनरावृत्ति',
        'col_name' => 'नाम',
        'col_size' => 'आकार',
        'col_info' => 'जानकारी',
        'col_modified' => 'संशोधित',
        'col_actions' => 'क्रियाएँ',
        'parent_folder' => 'मूल फ़ोल्डर',
        'folder_empty' => 'यह फ़ोल्डर खाली है।',
        'view' => 'देखें',
        'download' => 'डाउनलोड करें',
        'open' => 'खोलें',
        'zip' => 'जिप',
        'loading' => 'लोड हो रहा है…',
        'error_loading' => 'जानकारी लोड करते समय त्रुटि',
        'no_info' => 'कोई जानकारी उपलब्ध नहीं',
        'config_error' => '⚠️ पूर्वनिर्मित त्रुटि',
        'cookie_text' => 'इस साइट के द्वारा आवश्यक कुकीज़ उपयोगकर्ता पसंद (थीम, क्रमबद्धता, भाषा) को सहेजने के लिए उपयोग किया जाता है। कोई ट्रैकिंग या तीसरे पक्ष के कुकीज़ नहीं।',
        'cookie_ok' => 'ठीक है',
        'viewer_download' => '⤓ डाउनलोड',
        'viewer_newtab' => '↗ नई टैब',
        'viewer_close' => 'बंद करें (Esc)',
        'viewer_prev' => 'पिछला',
        'viewer_next' => 'अगला',
        'file_too_large' => 'टेक्स्ट पूर्वाभ्यास के लिए फ़ाइल बहुत बड़ी है। \'नई टैब\' का उपयोग करें या डाउनलोड करें।',
        'could_not_load' => 'फ़ाइल लोड नहीं कर सकते।',
        'queue_processing' => '{running} प्रसंस्करण, {pending} अपेक्षित',
        'queue_no_tasks' => 'कोई सक्रिय कार्य नहीं',
        'file_singular' => '{n} फ़ाइल',
        'file_plural' => '{n} फ़ाइलें',
        'folder_singular' => '{n} फ़ोल्डर',
        'folder_plural' => '{n} फ़ोल्डर',
        'empty' => 'खाली',
        'share' => 'साझा करें',
        'share_copied' => 'साझा लिंक क्लिपबोर्ड में कॉपी कर दिया गया है!',
        'share_expires' => 'मौजूदगी:',
        'share_never' => 'कभी नहीं',
        'share_1h' => 'एक घंटा',
        'share_1d' => 'एक दिन',
        'share_7d' => '7 दिन',
        'share_30d' => '30 दिन',
        'background_tasks' => 'पृष्ठभूमि कार्य',
        'info_format' => 'फ़ॉर्मेट',
        'info_pages' => 'पृष्ठ',
        'info_title' => 'शीर्षक',
        'info_author' => 'लेखक',
        'info_language' => 'भाषा',
        'info_creator' => 'निर्माता',
        'info_producer' => 'उत्पादक',
        'info_pdf_version' => 'PDF संस्करण',
        'info_mime' => 'MIME',
        'info_encrypted' => 'एन्क्रिप्टेड',
        'info_page_size' => 'पृष्ठ आकार',
        'info_created' => 'बनाया गया',
        'info_resolution' => 'संकल्प',
        'info_type' => 'प्रकार',
        'info_vector' => 'वेक्टर ग्राफिक',
        'info_megapixels' => 'मेगापिक्सल',
        'info_color' => 'रंग',
        'info_alpha' => 'अल्फा',
        'info_animated' => 'एनिमेटेड',
        'info_yes' => 'हां',
        'info_duration' => 'काला',
        'info_container' => 'संग्रह',
        'info_overall_bitrate' => 'ओवरऑल बिट दर',
        'info_video' => 'वीडियो',
        'info_audio' => 'ऑडियो',
        'info_bitrate' => 'बिट दर',
        'info_fps' => 'एफपीएस',
        'info_bit_depth' => 'बिट गहराई',
        'info_sample_rate' => 'नमूना दर',
        'info_channels' => 'चैनल',
    ],
    'he' => [
        '_flag' => '🇮🇱',
        '_name' => 'עברית',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'בית',
        'download_folder' => '⤓ הורד תיקייה',
        'toggle_theme' => 'تبديل מצב תём/חיוור',
        'search_placeholder' => 'חפש לפי שם…',
        'all_types' => 'כל הסוגים',
        'type_folder' => 'תיקיות',
        'type_image' => 'תמונות',
        'type_audio' => 'אודיו',
        'type_video' => 'ווידאו',
        'type_document' => 'מסמכים',
        'type_archive' => 'ארחיבות',
        'type_other' => 'אחר',
        'recursive' => 'רקורסיבי',
        'col_name' => 'שם',
        'col_size' => 'גודל',
        'col_info' => 'מידע',
        'col_modified' => 'נערך',
        'col_actions' => 'פעולות',
        'parent_folder' => 'תיקייה האם',
        'folder_empty' => 'התיקייה ריקה.',
        'view' => 'הצג',
        'download' => 'הורד',
        'open' => 'פתח',
        'zip' => 'ZIP',
        'loading' => 'טוען…',
        'error_loading' => 'שגיאה בטעינת המידע',
        'no_info' => 'אין מידע זמין',
        'config_error' => '⚠️ שגיאת תצורה',
        'cookie_text' => 'האתר משתמש בקוקיז חובה כדי לשמור את העדפותך (מצב, סדר מיון, שפה). אין מעקב או קוקיז של צד שלישי.',
        'cookie_ok' => 'אישור',
        'viewer_download' => '⤓ הורדה',
        'viewer_newtab' => '↗ תג מודעה חדשה',
        'viewer_close' => 'סגור (Esc)',
        'viewer_prev' => 'קודם',
        'viewer_next' => 'הבא',
        'file_too_large' => 'הקובץ גדול מדי כדי להצגת טקסט. השתמש בـ"תג מודעה חדש" או הורד.',
        'could_not_load' => 'לא ניתן לטעון את הקובץ.',
        'queue_processing' => '{running} עיבוד, {pending} בשורה',
        'queue_no_tasks' => 'אין משימות פעילות',
        'file_singular' => '{n} קובץ',
        'file_plural' => '{n} קבצים',
        'folder_singular' => '{n} תיקייה',
        'folder_plural' => '{n} תיקיות',
        'empty' => 'فارג',
        'share' => 'שתף',
        'share_copied' => 'הקישור להשתף הועתק ללוח!',
        'share_expires' => 'תוקף:',
        'share_never' => 'לעולם',
        'share_1h' => 'שעה אחת',
        'share_1d' => 'יום אחד',
        'share_7d' => '7 ימים',
        'share_30d' => '30 ימים',
        'background_tasks' => 'משימות רקע',
        'info_format' => 'פורמט',
        'info_pages' => 'עמודים',
        'info_title' => 'כותרת',
        'info_author' => 'מחבר',
        'info_language' => 'שפה',
        'info_creator' => 'יוצר',
        'info_producer' => 'מייצר',
        'info_pdf_version' => 'גרסה PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'מוצפן',
        'info_page_size' => 'גודל עמוד',
        'info_created' => 'נוצר',
        'info_resolution' => 'תמונה',
        'info_type' => 'סוג',
        'info_vector' => 'איור וקטורי',
        'info_megapixels' => 'מגה פיקסלים',
        'info_color' => 'צבע',
        'info_alpha' => 'אלפא',
        'info_animated' => 'נ动漫',
        'info_yes' => 'כן',
        'info_duration' => 'משך',
        'info_container' => 'حاوية',
        'info_overall_bitrate' => 'קצב ביט כולל',
        'info_video' => 'ווידאו',
        'info_audio' => 'אודיו',
        'info_bitrate' => 'קצב ביט',
        'info_fps' => 'תדירות מסגרות',
        'info_bit_depth' => ' עומק ביט',
        'info_sample_rate' => 'อัตราการตัวอย่าง',
        'info_channels' => ' ערוצים',
    ],
    'hu' => [
        '_flag' => '🇭🇺',
        '_name' => 'Magyar',
        '_dateFormat' => 'Y.m.d H:i',
        '_dateFormatJS' => 'YYYY.MM.DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Kezdőlap',
        'download_folder' => '⤓ Letöltés mappája',
        'toggle_theme' => 'Váltás világos/sötét módra',
        'search_placeholder' => 'Keresés név alapján…',
        'all_types' => 'Minden típus',
        'type_folder' => 'Mappák',
        'type_image' => 'Képek',
        'type_audio' => 'Hangfájlok',
        'type_video' => 'Videók',
        'type_document' => 'Dokumentumok',
        'type_archive' => 'Archívumok',
        'type_other' => 'Egyéb',
        'recursive' => 'Rekurzív',
        'col_name' => 'Név',
        'col_size' => 'Méret',
        'col_info' => 'Info',
        'col_modified' => 'Módosítva',
        'col_actions' => 'Cselekvések',
        'parent_folder' => 'Szülőmappa',
        'folder_empty' => 'Ez a mappa üres.',
        'view' => 'Megtekintés',
        'download' => 'Letöltés',
        'open' => 'Nyisd meg',
        'zip' => 'Zip',
        'loading' => 'Töltés…',
        'error_loading' => 'Hiba az információ betöltésekor',
        'no_info' => 'Nincs elérhető információ',
        'config_error' => '⚠️ Konfigurációs hiba',
        'cookie_text' => 'Ez a webhely szükséges sütiket használ a beállítások (mód, rendezési sorrend, nyelv) mentéséhez. Nincs nyomonkövetés vagy harmadik féltől származó süti.',
        'cookie_ok' => 'Rendben',
        'viewer_download' => '⤓ Letöltés',
        'viewer_newtab' => '↗ Új lap',
        'viewer_close' => 'Bezárás (Esc)',
        'viewer_prev' => 'Előző',
        'viewer_next' => 'Következő',
        'file_too_large' => 'A fájl túl nagy a szöveg előnézethez. Használja az „Új lap” vagy töltsön le.',
        'could_not_load' => 'Nem sikerült betölteni a fájlt.',
        'queue_processing' => '{running} feldolgozás, {pending} várakozik',
        'queue_no_tasks' => 'Nincs aktív feladat',
        'file_singular' => '{n} fájl',
        'file_plural' => '{n} fájl',
        'folder_singular' => '{n} mappa',
        'folder_plural' => '{n} mappa',
        'empty' => 'üres',
        'share' => 'Megosztás',
        'share_copied' => 'Megosztási hivatkozás másolva a vágólapra!',
        'share_expires' => 'Lejárati idő:',
        'share_never' => 'Soha',
        'share_1h' => '1 óra',
        'share_1d' => '1 nap',
        'share_7d' => '7 nap',
        'share_30d' => '30 nap',
        'background_tasks' => 'Háttérbeli feladatok',
        'info_format' => 'Formátum',
        'info_pages' => 'Oldalak',
        'info_title' => 'Cím',
        'info_author' => 'Szerző',
        'info_language' => 'Nyelv',
        'info_creator' => 'Létrehozó',
        'info_producer' => 'Gyártó',
        'info_pdf_version' => 'PDF verzió',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Titkosítva',
        'info_page_size' => 'Oldalméret',
        'info_created' => 'Létrehozva',
        'info_resolution' => 'Felbontás',
        'info_type' => 'Típus',
        'info_vector' => 'Vektorgrafika',
        'info_megapixels' => 'Megapixelek',
        'info_color' => 'Szín',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animálva',
        'info_yes' => 'Igen',
        'info_duration' => 'Időtartam',
        'info_container' => 'Tároló',
        'info_overall_bitrate' => 'Átlagos bitráta',
        'info_video' => 'Videó',
        'info_audio' => 'Hang',
        'info_bitrate' => 'Bitráta',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitmélység',
        'info_sample_rate' => 'Mintavételi frekvencia',
        'info_channels' => 'Kanálok',
    ],
    'ga' => [
        '_flag' => '🇮🇪',
        '_name' => 'Gaeilge',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Barr',
        'download_folder' => '⤓ Táilsiú folachta',
        'toggle_theme' => 'Cuir i gcomhar an mód fheilte/dhóthain',
        'search_placeholder' => 'Cuardaigh le hainm…',
        'all_types' => 'Gan stíl',
        'type_folder' => 'Fóidir',
        'type_image' => 'Imeachtaí',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dochtúir',
        'type_archive' => 'Archives',
        'type_other' => 'Eile',
        'recursive' => 'Réadúil',
        'col_name' => 'Ainm',
        'col_size' => 'Méid',
        'col_info' => 'Feidhmeanna',
        'col_modified' => 'Cuirtear in ucht',
        'col_actions' => 'Gníomhaíochtaí',
        'parent_folder' => 'Fóid leathan',
        'folder_empty' => 'Tá an fóid seo bheo.',
        'view' => 'Féach',
        'download' => 'Táilsiú',
        'open' => 'Oscail',
        'zip' => 'Zip',
        'loading' => 'Luchtú…',
        'error_loading' => 'Earrach ar luchtú sonraí',
        'no_info' => 'Níl sonraí ar fáil',
        'config_error' => '⚠️ Earrach Cuirreann',
        'cookie_text' => 'Úsáidtear córais éagsúla le haghaidh an ghearrchomhartha (thema, ord scoir, teangacha). Ní bhfuil aon scannán nó scannáin trí pháirtí.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Táilsiú',
        'viewer_newtab' => '↗ Tab nua',
        'viewer_close' => 'Dún (Esc)',
        'viewer_prev' => 'Roinn roimhe',
        'viewer_next' => 'Roinn seoithine',
        'file_too_large' => 'Tá an fail seo go mór le haghaidh taispeántas téacs. Bain úsáid as "Tab Nua" nó táilsiú.',
        'could_not_load' => 'Ní féidir leis an ghearrchomhartha a luchtú.',
        'queue_processing' => '{running} a dhéanamh, {pending} ar bhuailt',
        'queue_no_tasks' => 'Níl gníomhaíochtaí ar siúl',
        'file_singular' => '{n} fail',
        'file_plural' => '{n} fáil',
        'folder_singular' => '{n} fóid',
        'folder_plural' => '{n} fóid',
        'empty' => 'bheo',
        'share' => 'Riachtanaigh',
        'share_copied' => 'Cuirfidh an t-seo le haghaidh an ghearrchomhartha!',
        'share_expires' => 'Tábhail:',
        'share_never' => 'Níos mó',
        'share_1h' => '1 uair',
        'share_1d' => '1 lá',
        'share_7d' => '7 lá',
        'share_30d' => '30 lá',
        'background_tasks' => 'Gníomhaíochtaí ar fud',
        'info_format' => 'Formáid',
        'info_pages' => 'Páid',
        'info_title' => 'Céad',
        'info_author' => 'Críochnaitheoir',
        'info_language' => 'Teanga',
        'info_creator' => 'Críochnaitheoir',
        'info_producer' => 'Tógálaí',
        'info_pdf_version' => 'Bheith PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Craoltha',
        'info_page_size' => 'Méid an pháid',
        'info_created' => 'Cruthaithe',
        'info_resolution' => 'Riachtanaigh',
        'info_type' => 'Stíl',
        'info_vector' => 'Gráifín vector',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Cóir',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Ainmhithe',
        'info_yes' => 'Tá',
        'info_duration' => 'Dhealbhú',
        'info_container' => 'Crann',
        'info_overall_bitrate' => 'Bitrate ar fad',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Dhéthlachas bit',
        'info_sample_rate' => 'Cineál sampla',
        'info_channels' => 'Canálaí',
    ],
    'is' => [
        '_flag' => '🇮🇸',
        '_name' => 'Íslenska',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Heim',
        'download_folder' => '⤓ Sækja mappu',
        'toggle_theme' => 'Víxla á ljós/dark mode',
        'search_placeholder' => 'Leita eftir nafni…',
        'all_types' => 'Allar tegundir',
        'type_folder' => 'Mappur',
        'type_image' => 'Myndir',
        'type_audio' => 'Ljóð',
        'type_video' => 'Video',
        'type_document' => 'Dokument',
        'type_archive' => 'Skrámyndir',
        'type_other' => 'Annað',
        'recursive' => 'Rekstróðleg',
        'col_name' => 'Nafn',
        'col_size' => 'Stærð',
        'col_info' => 'Upplýsingar',
        'col_modified' => 'Breytt',
        'col_actions' => 'Aðgerðir',
        'parent_folder' => 'Eftirmappu',
        'folder_empty' => 'Þessi mappa er tóma.',
        'view' => 'Sýna',
        'download' => 'Sækja',
        'open' => 'Opna',
        'zip' => 'ZIP',
        'loading' => 'Hleður…',
        'error_loading' => 'Villa við að hlaða upplýsingum',
        'no_info' => 'Engar upplýsingar til staðar',
        'config_error' => '⚠️ Stillingarvilla',
        'cookie_text' => 'Þessi vefsíða notar þróttægjarskóríu til að vista uppsetningar þínar (þema, röðun, tungumál). Engin sporing eða þriðja aðila skoríur.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Sækja',
        'viewer_newtab' => '↗ Ný tímablöð',
        'viewer_close' => 'Loka (Esc)',
        'viewer_prev' => 'Fyrri',
        'viewer_next' => 'Næsta',
        'file_too_large' => 'Skrá of stór til að sýna texta. Notaðu „Ný tímablöð“ eða sækja.',
        'could_not_load' => 'Ekki var hægt að opna skrána.',
        'queue_processing' => '{running} keyrslur, {pending} í kö',
        'queue_no_tasks' => 'Engar virka aðgerðir',
        'file_singular' => '{n} skrá',
        'file_plural' => '{n} skrár',
        'folder_singular' => '{n} mappa',
        'folder_plural' => '{n} mappur',
        'empty' => 'tóma',
        'share' => 'Deila',
        'share_copied' => 'Deilislínur afritað til klippiborðs!',
        'share_expires' => 'Gildistími:',
        'share_never' => 'Eldri aldrei',
        'share_1h' => '1 tíma',
        'share_1d' => '1 dagur',
        'share_7d' => '7 daga',
        'share_30d' => '30 daga',
        'background_tasks' => 'Bakgrunnar aðgerðir',
        'info_format' => 'Form',
        'info_pages' => 'Síður',
        'info_title' => 'Titill',
        'info_author' => 'Höfundur',
        'info_language' => 'Tungumál',
        'info_creator' => 'Skapaði',
        'info_producer' => 'Utgáfufyrirtæki',
        'info_pdf_version' => 'PDF útgáfa',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Kóðuð',
        'info_page_size' => 'Síðustærð',
        'info_created' => 'Búin til',
        'info_resolution' => 'Lýsing',
        'info_type' => 'Tegund',
        'info_vector' => 'Veðurmynd',
        'info_megapixels' => 'Megaþúsund pixla',
        'info_color' => 'Fjöldi lita',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Hreyfð',
        'info_yes' => 'Já',
        'info_duration' => 'Tími',
        'info_container' => 'Hýsa',
        'info_overall_bitrate' => 'Heildarbitstæði',
        'info_video' => 'Video',
        'info_audio' => 'Ljóð',
        'info_bitrate' => 'Bitstæði',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitastærð',
        'info_sample_rate' => 'Tökuhlaða',
        'info_channels' => 'Snið',
    ],
    'id' => [
        '_flag' => '🇮🇩',
        '_name' => 'Bahasa Indonesia',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Beranda',
        'download_folder' => '⤓ Unduh folder',
        'toggle_theme' => 'Ubah mode terang/gelap',
        'search_placeholder' => 'Cari berdasarkan nama…',
        'all_types' => 'Semua jenis',
        'type_folder' => 'Folder',
        'type_image' => 'Gambar',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumen',
        'type_archive' => 'Arsip',
        'type_other' => 'Lainnya',
        'recursive' => 'Rekursif',
        'col_name' => 'Nama',
        'col_size' => 'Ukuran',
        'col_info' => 'Info',
        'col_modified' => 'Diubah',
        'col_actions' => 'Aksi',
        'parent_folder' => 'Folder induk',
        'folder_empty' => 'Folder ini kosong.',
        'view' => 'Lihat',
        'download' => 'Unduh',
        'open' => 'Buka',
        'zip' => 'Zip',
        'loading' => 'Memuat…',
        'error_loading' => 'Kesalahan memuat informasi',
        'no_info' => 'Tidak ada informasi tersedia',
        'config_error' => '⚠️ Kesalahan Konfigurasi',
        'cookie_text' => 'Situs ini menggunakan kuki esensial untuk menyimpan preferensi Anda (tema, urutan pengurutan, bahasa). Tidak ada pelacakan atau kuki pihak ketiga.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Unduh',
        'viewer_newtab' => '↗ Tab Baru',
        'viewer_close' => 'Tutup (Esc)',
        'viewer_prev' => 'Sebelumnya',
        'viewer_next' => 'Berikutnya',
        'file_too_large' => 'Berkas terlalu besar untuk pratinjau teks. Gunakan "Tab Baru" atau unduh.',
        'could_not_load' => 'Tidak dapat memuat berkas.',
        'queue_processing' => '{running} diproses, {pending} dalam antrian',
        'queue_no_tasks' => 'Tidak ada tugas aktif',
        'file_singular' => '{n} berkas',
        'file_plural' => '{n} berkas',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folder',
        'empty' => 'kosong',
        'share' => 'Bagikan',
        'share_copied' => 'Link bagikan disalin ke clipboard!',
        'share_expires' => 'Kadaluarsa:',
        'share_never' => 'Tidak pernah',
        'share_1h' => '1 jam',
        'share_1d' => '1 hari',
        'share_7d' => '7 hari',
        'share_30d' => '30 hari',
        'background_tasks' => 'Tugas latar belakang',
        'info_format' => 'Format',
        'info_pages' => 'Halaman',
        'info_title' => 'Judul',
        'info_author' => 'Penulis',
        'info_language' => 'Bahasa',
        'info_creator' => 'Pembuat',
        'info_producer' => 'Produsen',
        'info_pdf_version' => 'Versi PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Terenkripsi',
        'info_page_size' => 'Ukuran halaman',
        'info_created' => 'Dibuat',
        'info_resolution' => 'Resolusi',
        'info_type' => 'Tipe',
        'info_vector' => 'Grafik vektor',
        'info_megapixels' => 'Megapiksel',
        'info_color' => 'Warna',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Beranimasi',
        'info_yes' => 'Ya',
        'info_duration' => 'Durasi',
        'info_container' => 'Kontainer',
        'info_overall_bitrate' => 'Bitrate keseluruhan',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Kedalaman bit',
        'info_sample_rate' => 'Frekuensi sampel',
        'info_channels' => 'Saluran',
    ],
    'it' => [
        '_flag' => '🇮🇹',
        '_name' => 'Italiano',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Scarica cartella',
        'toggle_theme' => 'Attiva modalità luce/scuro',
        'search_placeholder' => 'Cerca per nome…',
        'all_types' => 'Tutti i tipi',
        'type_folder' => 'Cartelle',
        'type_image' => 'Immagini',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documenti',
        'type_archive' => 'Archivi',
        'type_other' => 'Altro',
        'recursive' => 'Ricorsivo',
        'col_name' => 'Nome',
        'col_size' => 'Dimensione',
        'col_info' => 'Info',
        'col_modified' => 'Modificato',
        'col_actions' => 'Azioni',
        'parent_folder' => 'Cartella padre',
        'folder_empty' => 'Questa cartella è vuota.',
        'view' => 'Visualizza',
        'download' => 'Scarica',
        'open' => 'Apri',
        'zip' => 'Zip',
        'loading' => 'Caricamento…',
        'error_loading' => 'Errore nel caricamento delle informazioni',
        'no_info' => 'Nessuna informazione disponibile',
        'config_error' => '⚠️ Errore di configurazione',
        'cookie_text' => 'Questo sito utilizza cookie essenziali per salvare le tue preferenze (tema, ordine di ordinamento, lingua). Nessun tracciamento o cookie di terze parti.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Scarica',
        'viewer_newtab' => '↗ Nuova scheda',
        'viewer_close' => 'Chiudi (Esc)',
        'viewer_prev' => 'Precedente',
        'viewer_next' => 'Successivo',
        'file_too_large' => 'Il file è troppo grande per la anteprima testuale. Utilizza "Nuova scheda" o scaricalo.',
        'could_not_load' => 'Impossibile caricare il file.',
        'queue_processing' => '{running} in elaborazione, {pending} in coda',
        'queue_no_tasks' => 'Nessun compito attivo',
        'file_singular' => '{n} file',
        'file_plural' => '{n} files',
        'folder_singular' => '{n} cartella',
        'folder_plural' => '{n} cartelle',
        'empty' => 'vuoto',
        'share' => 'Condividi',
        'share_copied' => 'Link di condivisione copiato negli appunti!',
        'share_expires' => 'Scadenza:',
        'share_never' => 'Mai',
        'share_1h' => '1 ora',
        'share_1d' => '1 giorno',
        'share_7d' => '7 giorni',
        'share_30d' => '30 giorni',
        'background_tasks' => 'Task in background',
        'info_format' => 'Formato',
        'info_pages' => 'Pagine',
        'info_title' => 'Titolo',
        'info_author' => 'Autore',
        'info_language' => 'Lingua',
        'info_creator' => 'Creatore',
        'info_producer' => 'Produttore',
        'info_pdf_version' => 'Versione PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Criptato',
        'info_page_size' => 'Dimensione pagina',
        'info_created' => 'Creato',
        'info_resolution' => 'Risoluzione',
        'info_type' => 'Tipo',
        'info_vector' => 'Grafico vettoriale',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Colore',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animato',
        'info_yes' => 'Sì',
        'info_duration' => 'Durata',
        'info_container' => 'Contenitore',
        'info_overall_bitrate' => 'Bitrate complessivo',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Profondità bit',
        'info_sample_rate' => 'Frequenza campionamento',
        'info_channels' => 'Canali',
    ],
    'jp' => [
        '_flag' => '🇯🇵',
        '_name' => '日本語',
        '_dateFormat' => 'Y/m/d H:i',
        '_dateFormatJS' => 'YYYY/MM/DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'ホーム',
        'download_folder' => '⤓ ダウンロードフォルダ',
        'toggle_theme' => '明るい/暗いモードを切り替え',
        'search_placeholder' => '名前で検索…',
        'all_types' => 'すべてのタイプ',
        'type_folder' => 'フォルダ',
        'type_image' => '画像',
        'type_audio' => 'オーディオ',
        'type_video' => '動画',
        'type_document' => 'ドキュメント',
        'type_archive' => 'アーカイブ',
        'type_other' => 'その他',
        'recursive' => '再帰的',
        'col_name' => '名前',
        'col_size' => 'サイズ',
        'col_info' => '情報',
        'col_modified' => '変更日時',
        'col_actions' => 'アクション',
        'parent_folder' => '親フォルダ',
        'folder_empty' => 'このフォルダは空です。',
        'view' => '表示',
        'download' => 'ダウンロード',
        'open' => '開く',
        'zip' => 'ZIP',
        'loading' => '読み込み中…',
        'error_loading' => '情報の読み込みエラー',
        'no_info' => '情報を取得できません',
        'config_error' => '⚠️ 設定エラー',
        'cookie_text' => 'このサイトは、テーマや並べ替え順序、言語などの設定を保存するために必須クッキーを使用しています。トラッキングや第三者のクッキーは使用しません。',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ ダウンロード',
        'viewer_newtab' => '↗ 新しいタブ',
        'viewer_close' => '閉じる (Esc)',
        'viewer_prev' => '前へ',
        'viewer_next' => '次へ',
        'file_too_large' => 'ファイルがテキストプレビューに大きすぎるため、『新しいタブ』かダウンロードしてください。',
        'could_not_load' => 'ファイルの読み込みができませんでした。',
        'queue_processing' => '{running} 処理中, {pending} キュー待ち',
        'queue_no_tasks' => 'アクティブなタスクはありません',
        'file_singular' => '{n} ファイル',
        'file_plural' => '{n} ファイル',
        'folder_singular' => '{n} フォルダ',
        'folder_plural' => '{n} フォルダ',
        'empty' => '空',
        'share' => '共有',
        'share_copied' => '共有リンクをクリップボードにコピーしました！',
        'share_expires' => '期限:',
        'share_never' => 'なし',
        'share_1h' => '1時間',
        'share_1d' => '1日',
        'share_7d' => '7日',
        'share_30d' => '30日',
        'background_tasks' => 'バックグラウンドタスク',
        'info_format' => '形式',
        'info_pages' => 'ページ数',
        'info_title' => 'タイトル',
        'info_author' => '著者',
        'info_language' => '言語',
        'info_creator' => '作成者',
        'info_producer' => '制作会社',
        'info_pdf_version' => 'PDFバージョン',
        'info_mime' => 'MIME',
        'info_encrypted' => '暗号化済み',
        'info_page_size' => 'ページサイズ',
        'info_created' => '作成日時',
        'info_resolution' => '解像度',
        'info_type' => 'タイプ',
        'info_vector' => 'ベクター画像',
        'info_megapixels' => 'メガピクセル',
        'info_color' => '色',
        'info_alpha' => 'アルファチャンネル',
        'info_animated' => 'アニメーションあり',
        'info_yes' => 'はい',
        'info_duration' => '長さ',
        'info_container' => 'コンテナ形式',
        'info_overall_bitrate' => '全体ビットレート',
        'info_video' => '動画',
        'info_audio' => 'オーディオ',
        'info_bitrate' => 'ビットレート',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'ビット深度',
        'info_sample_rate' => 'サンプルレート',
        'info_channels' => 'チャンネル',
    ],
    'ko' => [
        '_flag' => '🇰🇷',
        '_name' => '한국어',
        '_dateFormat' => 'Y.m.d H:i',
        '_dateFormatJS' => 'YYYY.MM.DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => '홈',
        'download_folder' => '⤓ 다운로드 폴더',
        'toggle_theme' => '라이트/다크 모드 전환',
        'search_placeholder' => '이름으로 검색…',
        'all_types' => '모든 유형',
        'type_folder' => '폴더',
        'type_image' => '이미지',
        'type_audio' => '오디오',
        'type_video' => '비디오',
        'type_document' => '문서',
        'type_archive' => '아카이브',
        'type_other' => '기타',
        'recursive' => '재귀적',
        'col_name' => '이름',
        'col_size' => '크기',
        'col_info' => '정보',
        'col_modified' => '수정됨',
        'col_actions' => '작업',
        'parent_folder' => '상위 폴더',
        'folder_empty' => '이 폴더는 비어 있습니다.',
        'view' => '보기',
        'download' => '다운로드',
        'open' => '열기',
        'zip' => 'Zip',
        'loading' => '로딩 중…',
        'error_loading' => '정보 로딩 오류',
        'no_info' => '사용 가능한 정보 없음',
        'config_error' => '⚠️ 구성 오류',
        'cookie_text' => '이 사이트는 테마, 정렬 순서, 언어와 같은 선호도를 저장하기 위해 필수 쿠키를 사용합니다. 추적 또는 제3자 쿠키는 없습니다.',
        'cookie_ok' => '확인',
        'viewer_download' => '⤓ 다운로드',
        'viewer_newtab' => '↗ 새 탭',
        'viewer_close' => '닫기 (Esc)',
        'viewer_prev' => '이전',
        'viewer_next' => '다음',
        'file_too_large' => '파일 크기가 텍스트 미리보기에 너무 큽니다. \'새 탭\' 또는 다운로드를 사용하세요.',
        'could_not_load' => '파일을 로드할 수 없습니다.',
        'queue_processing' => '{running} 처리 중, {pending} 대기',
        'queue_no_tasks' => '활성 작업 없음',
        'file_singular' => '{n} 파일',
        'file_plural' => '{n} 파일들',
        'folder_singular' => '{n} 폴더',
        'folder_plural' => '{n} 폴더들',
        'empty' => '비어 있음',
        'share' => '공유',
        'share_copied' => '공유 링크가 클립보드에 복사되었습니다!',
        'share_expires' => '만료 시간:',
        'share_never' => '전혀 없음',
        'share_1h' => '1시간',
        'share_1d' => '1일',
        'share_7d' => '7일',
        'share_30d' => '30일',
        'background_tasks' => '배경 작업',
        'info_format' => '포맷',
        'info_pages' => '페이지 수',
        'info_title' => '제목',
        'info_author' => '작성자',
        'info_language' => '언어',
        'info_creator' => '만든 사람',
        'info_producer' => '생산자',
        'info_pdf_version' => 'PDF 버전',
        'info_mime' => 'MIME',
        'info_encrypted' => '암호화됨',
        'info_page_size' => '페이지 크기',
        'info_created' => '작성 시간',
        'info_resolution' => '해상도',
        'info_type' => '유형',
        'info_vector' => '벡터 그래픽',
        'info_megapixels' => '메가 픽셀',
        'info_color' => '색상',
        'info_alpha' => '알파 채널',
        'info_animated' => '애니메이션',
        'info_yes' => '예',
        'info_duration' => '지속 시간',
        'info_container' => '컨테이너',
        'info_overall_bitrate' => '전체 비트레이트',
        'info_video' => '비디오',
        'info_audio' => '오디오',
        'info_bitrate' => '비트레이트',
        'info_fps' => 'FPS',
        'info_bit_depth' => '비트 깊이',
        'info_sample_rate' => '샘플 레이트',
        'info_channels' => '채널',
    ],
    'ku' => [
        '_flag' => '🇮🇶',
        '_name' => 'کوردی',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'ئەوڵام',
        'download_folder' => '⤓ دۆخ بەردەن',
        'toggle_theme' => 'چوون و گەڕانی مودە کورت/سەرەکی',
        'search_placeholder' => 'بە ناڤ لە بارکردن…',
        'all_types' => 'ھەموو جۆرە',
        'type_folder' => 'فولدرەکان',
        'type_image' => 'گۆڕاوەکان',
        'type_audio' => 'ئاوی',
        'type_video' => 'ویدیۆ',
        'type_document' => 'دۆکومێنتەکان',
        'type_archive' => 'آرشیڤەکان',
        'type_other' => 'سەرەتای',
        'recursive' => 'بە گەشەپ',
        'col_name' => 'ناڤ',
        'col_size' => 'چوون',
        'col_info' => 'ئەمێل',
        'col_modified' => 'گۆڕانکاری کراو',
        'col_actions' => 'کردارەکان',
        'parent_folder' => 'فولدری گەشەپ',
        'folder_empty' => 'ئەم فولدرە خالیە.',
        'view' => 'بین',
        'download' => 'دۆخ بەردەن',
        'open' => 'گەڕان',
        'zip' => 'زیپ',
        'loading' => 'بارکردن…',
        'error_loading' => 'خۆشەویستنی مەلا بە هەڵە',
        'no_info' => 'هیچ مەلا نیە',
        'config_error' => '⚠️ خۆشەویستنی کانفیگوراسیۆن',
        'cookie_text' => 'ئەم سایتە دەکارێت ڕەسمی کووکیز بکات بۆ چوونی تەواوەتی (مودە، ڕێژەی گەڕان، زمان). هیچ چاکردن یان کووکیزی سێدەمەن نیە.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ دۆخ بەردەن',
        'viewer_newtab' => '↗ تاب نوێ',
        'viewer_close' => 'بەکردن (Esc)',
        'viewer_prev' => 'پێش',
        'viewer_next' => 'دواتر',
        'file_too_large' => 'فایل چوونی زیادە بۆ گەڕانی تەکست. لە گەڕانی تاب نوێ یان دۆخ بەردەن کار بکرە.',
        'could_not_load' => 'دەکرێت فایل بە هەڵە ببارێت.',
        'queue_processing' => '{running} پرۆسەس، {pending} چاک کراو',
        'queue_no_tasks' => 'هیچ کارێک نیە',
        'file_singular' => '{n} فایل',
        'file_plural' => '{n} فایلەکان',
        'folder_singular' => '{n} فولدر',
        'folder_plural' => '{n} فولدرەکان',
        'empty' => 'خالی',
        'share' => 'بەپارێز',
        'share_copied' => 'لینکی بەپارێز لە ڕۆنەکاندا کەش کراوە!',
        'share_expires' => 'تەواوتە:',
        'share_never' => 'هەموو گەڵا',
        'share_1h' => 'ساعتێک',
        'share_1d' => 'یەک ڕۆژ',
        'share_7d' => '7 ڕۆژ',
        'share_30d' => '30 ڕۆژ',
        'background_tasks' => 'کارەکانی پشتگیر',
        'info_format' => 'فرمەت',
        'info_pages' => 'پەڕەکان',
        'info_title' => 'ناو',
        'info_author' => 'نووسەر',
        'info_language' => 'زمان',
        'info_creator' => 'سازکار',
        'info_producer' => 'سازکار',
        'info_pdf_version' => 'وەرژەی PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'دەفران',
        'info_page_size' => 'چوونی پەڕە',
        'info_created' => 'سەیرکراو',
        'info_resolution' => 'ناخۆشەویستن',
        'info_type' => 'جۆر',
        'info_vector' => 'گرافیکی بەردار',
        'info_megapixels' => 'مێگاپیکسل',
        'info_color' => 'ڕەنگ',
        'info_alpha' => 'ئلفا',
        'info_animated' => 'هەڵبژاردە',
        'info_yes' => 'بەڵێ',
        'info_duration' => 'دۆزینەوە',
        'info_container' => 'کانتینەر',
        'info_overall_bitrate' => 'بەتەرەتی گشتی',
        'info_video' => 'ویدیۆ',
        'info_audio' => 'ئاوی',
        'info_bitrate' => 'بەتەرەت',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'دەمەندەی بەت',
        'info_sample_rate' => 'ڕێژەی نمونە',
        'info_channels' => 'کانالەکان',
    ],
    'lt' => [
        '_flag' => '🇱🇹',
        '_name' => 'Lietuvių',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Pradžia',
        'download_folder' => '⤓ Atsisiųsti aplanką',
        'toggle_theme' => 'Perjungti šviesią/tamsią temą',
        'search_placeholder' => 'Ieškokite pagal pavadinimą…',
        'all_types' => 'Visi tipai',
        'type_folder' => 'Aplankai',
        'type_image' => 'Vaizdai',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumentai',
        'type_archive' => 'Archyvai',
        'type_other' => 'Kiti',
        'recursive' => 'Rekursyvus',
        'col_name' => 'Pavadinimas',
        'col_size' => 'Dydis',
        'col_info' => 'Informacija',
        'col_modified' => 'Redaguota',
        'col_actions' => 'Veiksmai',
        'parent_folder' => 'Aukštesnis aplankas',
        'folder_empty' => 'Šis aplankas tuščias.',
        'view' => 'Peržiūrėti',
        'download' => 'Atsisiųsti',
        'open' => 'Atidaryti',
        'zip' => 'Sutraukti',
        'loading' => 'Kraunama…',
        'error_loading' => 'Klaida įkeliant informaciją',
        'no_info' => 'Nėra prieinamos informacijos',
        'config_error' => '⚠️ Konfigūracijos klaida',
        'cookie_text' => 'Šis svetainė naudoja esminius slapukus, kad išlaikytumėte savo nuostatas (tema, rikiavimo tvarka, kalba). Niekas neįrašo ar nenaudoja trečiųjų šalių slapukų.',
        'cookie_ok' => 'Gerai',
        'viewer_download' => '⤓ Atsisiųsti',
        'viewer_newtab' => '↗ Naujas langas',
        'viewer_close' => 'Uždaryti (Esc)',
        'viewer_prev' => 'Ankstesnis',
        'viewer_next' => 'Sekantis',
        'file_too_large' => 'Failas per didelis tekstui peržiūrai. Naudokite „Naują langą“ arba atsisiųskite.',
        'could_not_load' => 'Nepavyko įkelti failo.',
        'queue_processing' => '{running} apdorojimas, {pending} eilėje',
        'queue_no_tasks' => 'Jokių aktyvių užduočių',
        'file_singular' => '{n} failas',
        'file_plural' => '{n} failai',
        'folder_singular' => '{n} aplankas',
        'folder_plural' => '{n} aplankai',
        'empty' => 'tuščias',
        'share' => 'Dalintis',
        'share_copied' => 'Dalintis nuoroda nukopijuota į iškarpinę!',
        'share_expires' => 'Baigiasi:',
        'share_never' => 'Niekada',
        'share_1h' => '1 valanda',
        'share_1d' => '1 diena',
        'share_7d' => '7 dienos',
        'share_30d' => '30 dienų',
        'background_tasks' => 'Fono užduotys',
        'info_format' => 'Formatas',
        'info_pages' => 'Puslapiai',
        'info_title' => 'Pavadinimas',
        'info_author' => 'Autorius',
        'info_language' => 'Kalba',
        'info_creator' => 'Kūrėjas',
        'info_producer' => 'Gamintojas',
        'info_pdf_version' => 'PDF versija',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Užšifruotas',
        'info_page_size' => 'Puslapio dydis',
        'info_created' => 'Sukurtas',
        'info_resolution' => 'Apsisprendimas',
        'info_type' => 'Tipas',
        'info_vector' => 'Vektorinė nuotrauka',
        'info_megapixels' => 'Megapikseliai',
        'info_color' => 'Spalva',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animuotas',
        'info_yes' => 'Taip',
        'info_duration' => 'Trukmė',
        'info_container' => 'Kontaineris',
        'info_overall_bitrate' => 'Viso bitų našumas',
        'info_video' => 'Vaizdas',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitų našumas',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitų gylis',
        'info_sample_rate' => 'Išvedimo dažnis',
        'info_channels' => 'Kanalai',
    ],
    'la' => [
        '_flag' => '🏛️',
        '_name' => 'Latina',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Domus',
        'download_folder' => '⤓ Descargare directorem',
        'toggle_theme' => 'Convertere modum claram/tenebricosum',
        'search_placeholder' => 'Quære per nomen…',
        'all_types' => 'Omnes typi',
        'type_folder' => 'Directoria',
        'type_image' => 'Imagines',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documenta',
        'type_archive' => 'Archiva',
        'type_other' => 'Alii',
        'recursive' => 'Recursivus',
        'col_name' => 'Nomen',
        'col_size' => 'Magnitudo',
        'col_info' => 'Informatio',
        'col_modified' => 'Modificatum',
        'col_actions' => 'Acta',
        'parent_folder' => 'Directorem parentem',
        'folder_empty' => 'Hic directore est vacuus.',
        'view' => 'Visum',
        'download' => 'Descargare',
        'open' => 'Aperire',
        'zip' => 'Zip',
        'loading' => 'Cargando…',
        'error_loading' => 'Error in carpendo informatio',
        'no_info' => 'Nihil informatio disponibilis',
        'config_error' => '⚠️ Error configuratio',
        'cookie_text' => 'Hic locus utitur cookie essentiales pro salvando preferentia tua (thema, ordine sortis, lingua). Nihil tractatio aut cookie tertii ordinis.',
        'cookie_ok' => 'Bene',
        'viewer_download' => '⤓ Descargare',
        'viewer_newtab' => '↗ Tabula nova',
        'viewer_close' => 'Claudere (Esc)',
        'viewer_prev' => 'Anterior',
        'viewer_next' => 'Posterior',
        'file_too_large' => 'Finitus est nimis magnus pro praevisione textus. Utere "Tabula nova" aut descargare.',
        'could_not_load' => 'Non potest carpare finitum.',
        'queue_processing' => '{running} processando, {pending} in serie',
        'queue_no_tasks' => 'Nihil taska activa',
        'file_singular' => '{n} finitus',
        'file_plural' => '{n} finita',
        'folder_singular' => '{n} directorem',
        'folder_plural' => '{n} directoria',
        'empty' => 'vacuus',
        'share' => 'Participare',
        'share_copied' => 'Ligamen participatio copiata in tabulam!',
        'share_expires' => 'Expiratio:',
        'share_never' => 'Numquam',
        'share_1h' => '1 hora',
        'share_1d' => '1 dies',
        'share_7d' => '7 dies',
        'share_30d' => '30 dies',
        'background_tasks' => 'Taska in fundo',
        'info_format' => 'Formatus',
        'info_pages' => 'Paginae',
        'info_title' => 'Titulus',
        'info_author' => 'Auctor',
        'info_language' => 'Lingua',
        'info_creator' => 'Creatorem',
        'info_producer' => 'Producens',
        'info_pdf_version' => 'Versionem PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Cifratum',
        'info_page_size' => 'Magnitudo paginae',
        'info_created' => 'Creatum',
        'info_resolution' => 'Resolutio',
        'info_type' => 'Typus',
        'info_vector' => 'Graphicum vectoris',
        'info_megapixels' => 'Megapixelles',
        'info_color' => 'Color',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animatum',
        'info_yes' => 'Sí',
        'info_duration' => 'Duratio',
        'info_container' => 'Continentem',
        'info_overall_bitrate' => 'Bitrate totale',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Profunditas bit',
        'info_sample_rate' => 'Ratio exempli',
        'info_channels' => 'Canalis',
    ],
    'lv' => [
        '_flag' => '🇱🇻',
        '_name' => 'Latviešu',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Māja',
        'download_folder' => '⤓ Lejupielādēt mapi',
        'toggle_theme' => 'Pārslēgt starp gaismu un tumšo režīmu',
        'search_placeholder' => 'Meklēt pēc nosaukuma…',
        'all_types' => 'Visi veidi',
        'type_folder' => 'Mapes',
        'type_image' => 'Attēli',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumenti',
        'type_archive' => 'Arhivi',
        'type_other' => 'Citi',
        'recursive' => 'Rekurzīvi',
        'col_name' => 'Nosaukums',
        'col_size' => 'Lielums',
        'col_info' => 'Informācija',
        'col_modified' => 'Modificēts',
        'col_actions' => 'Darbības',
        'parent_folder' => 'Augstākā mape',
        'folder_empty' => 'Šī mape ir tukša.',
        'view' => 'Skatīt',
        'download' => 'Lejupielādēt',
        'open' => 'Atvērt',
        'zip' => 'Sakraut',
        'loading' => 'Ielāde…',
        'error_loading' => 'Kļūda ielādes laikā',
        'no_info' => 'Nav pieejama informācija',
        'config_error' => '⚠️ Konfigurācijas kļūda',
        'cookie_text' => 'Šī vietne izmanto esences saskarnes, lai saglabātu jūsu iestatījumus (režīms, kārtotās kārtība, valoda). Nav uzraudzības vai trešo pušu saskarnes.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Lejupielādēt',
        'viewer_newtab' => '↗ Jauns cilnis',
        'viewer_close' => 'Aizvērt (Esc)',
        'viewer_prev' => 'Iepriekšējais',
        'viewer_next' => 'Nākamais',
        'file_too_large' => 'Faila lielums ir pārāk liels, lai to varētu parādīt teksta priekšskatā. Izmantojiet „Jauns cilnis” vai lejupielādi.',
        'could_not_load' => 'Nevar ielādēt failu.',
        'queue_processing' => '{running} apstrāde, {pending} gaida',
        'queue_no_tasks' => 'Nav aktīvu uzdevumu',
        'file_singular' => '{n} fails',
        'file_plural' => '{n} faili',
        'folder_singular' => '{n} mape',
        'folder_plural' => '{n} mapes',
        'empty' => 'tukšs',
        'share' => 'Dalīties',
        'share_copied' => 'Saite koplietotās atmiņas!',
        'share_expires' => 'Beigu laiks:',
        'share_never' => 'Nekad',
        'share_1h' => '1 stundu',
        'share_1d' => '1 dienu',
        'share_7d' => '7 dienas',
        'share_30d' => '30 dienas',
        'background_tasks' => 'Atrases uzdevumi',
        'info_format' => 'Formāts',
        'info_pages' => 'Lappuses',
        'info_title' => 'Nosaukums',
        'info_author' => 'Autors',
        'info_language' => 'Valoda',
        'info_creator' => 'Izveidotājs',
        'info_producer' => 'Ražotājs',
        'info_pdf_version' => 'PDF versija',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Šifrēts',
        'info_page_size' => 'Lappuses izmērs',
        'info_created' => 'Izveidots',
        'info_resolution' => 'Risinājums',
        'info_type' => 'Tips',
        'info_vector' => 'Vektora attēls',
        'info_megapixels' => 'Megapikseļi',
        'info_color' => 'Krāsa',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animēts',
        'info_yes' => 'Jā',
        'info_duration' => 'Ilgums',
        'info_container' => 'Kontainers',
        'info_overall_bitrate' => 'Vispārējais bitu ātrums',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitu ātrums',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitu dziļums',
        'info_sample_rate' => 'Parauga biežums',
        'info_channels' => 'Kanāli',
    ],
    'ms' => [
        '_flag' => '🇲🇾',
        '_name' => 'Bahasa Melayu',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Laman Utama',
        'download_folder' => '⤓ Muat Turun Folder',
        'toggle_theme' => 'Togol mod cahaya/gelap',
        'search_placeholder' => 'Cari mengikut nama…',
        'all_types' => 'Semua jenis',
        'type_folder' => 'Folder',
        'type_image' => 'Gambar',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumen',
        'type_archive' => 'Arkib',
        'type_other' => 'Lain-lain',
        'recursive' => 'Rekursif',
        'col_name' => 'Nama',
        'col_size' => 'Saiz',
        'col_info' => 'Maklumat',
        'col_modified' => 'Diubahsuai',
        'col_actions' => 'Tindakan',
        'parent_folder' => 'Folder induk',
        'folder_empty' => 'Folder ini kosong.',
        'view' => 'Lihat',
        'download' => 'Muat Turun',
        'open' => 'Buka',
        'zip' => 'Zip',
        'loading' => 'Memuatkan…',
        'error_loading' => 'Ralat memuatkan maklumat',
        'no_info' => 'Tiada maklumat tersedia',
        'config_error' => '⚠️ Ralat Konfigurasi',
        'cookie_text' => 'Laman ini menggunakan kuki penting untuk menyimpan keutamaan anda (tema, turutan penyusunan, bahasa). Tiada kuki pelacakan atau pihak ketiga.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Muat Turun',
        'viewer_newtab' => '↗ Tab Baru',
        'viewer_close' => 'Tutup (Esc)',
        'viewer_prev' => 'Sebelumnya',
        'viewer_next' => 'Seterusnya',
        'file_too_large' => 'Fail terlalu besar untuk pratonton teks. Gunakan "Tab Baru" atau muat turun.',
        'could_not_load' => 'Gagal memuatkan fail.',
        'queue_processing' => '{running} memproses, {pending} dalam barisan',
        'queue_no_tasks' => 'Tiada tugas aktif',
        'file_singular' => '{n} fail',
        'file_plural' => '{n} fail',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folder',
        'empty' => 'kosong',
        'share' => 'Kongsikan',
        'share_copied' => 'Pautan kongsian disalin ke clipboard!',
        'share_expires' => 'Tarikh luput:',
        'share_never' => 'Tidak pernah',
        'share_1h' => '1 jam',
        'share_1d' => '1 hari',
        'share_7d' => '7 hari',
        'share_30d' => '30 hari',
        'background_tasks' => 'Tugas Latar Belakang',
        'info_format' => 'Format',
        'info_pages' => 'Muka Surat',
        'info_title' => 'Tajuk',
        'info_author' => 'Pengarang',
        'info_language' => 'Bahasa',
        'info_creator' => 'Pencipta',
        'info_producer' => 'Pengeluar',
        'info_pdf_version' => 'Versi PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Dikodkan',
        'info_page_size' => 'Saiz Muka Surat',
        'info_created' => 'Dicipta',
        'info_resolution' => 'Kadar Penyelesaian',
        'info_type' => 'Jenis',
        'info_vector' => 'Gambar vektor',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Warna',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animasi',
        'info_yes' => 'Ya',
        'info_duration' => 'Tempoh',
        'info_container' => 'Kotak Simpanan',
        'info_overall_bitrate' => 'Bitrate keseluruhan',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Kedalaman Bit',
        'info_sample_rate' => 'Kadar Sampel',
        'info_channels' => 'Saluran',
    ],
    'mr' => [
        '_flag' => '🇮🇳',
        '_name' => 'मराठी',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'मुख्यपृष्ठ',
        'download_folder' => '⤓ डाउनलोड फोल्डर',
        'toggle_theme' => 'प्रकाश/अंधार मोड सुरू करा',
        'search_placeholder' => 'नावाने शोधा…',
        'all_types' => 'सर्व प्रकार',
        'type_folder' => 'फोल्डर',
        'type_image' => 'चित्रे',
        'type_audio' => 'ऑडिओ',
        'type_video' => 'व्हिडिओ',
        'type_document' => 'दस्तऐवज',
        'type_archive' => 'आर्काईव्ह',
        'type_other' => 'इतर',
        'recursive' => 'पुनरावृत्ति',
        'col_name' => 'नाम',
        'col_size' => 'मोठा',
        'col_info' => 'जाणकारी',
        'col_modified' => 'बदलले',
        'col_actions' => 'क्रिया',
        'parent_folder' => 'पालक फोल्डर',
        'folder_empty' => 'हा फोल्डर खाली आहे।',
        'view' => 'पहा',
        'download' => 'डाउनलोड करा',
        'open' => 'खुला',
        'zip' => 'जिप',
        'loading' => 'लोड होत आहे…',
        'error_loading' => 'जाणकारी लोड करण्यात चूक झाली',
        'no_info' => 'जाणकारी उपलब्ध नाही',
        'config_error' => '⚠️ सेटिंग चूक',
        'cookie_text' => 'हा वेबसाइट आवश्यक कुकीज वापरतो जे तुमचे प्राधिकरण (थीम, सॉर्ट क्रम, भाषा) जपतात. कोणतेही ट्रॅकिंग किंवा तृतीय-पक्ष कुकीज नाहीत।',
        'cookie_ok' => 'ठीक',
        'viewer_download' => '⤓ डाउनलोड',
        'viewer_newtab' => '↗ नवी टॅब',
        'viewer_close' => 'बंद (Esc)',
        'viewer_prev' => 'पूर्वीचा',
        'viewer_next' => 'अगोदरचा',
        'file_too_large' => 'फाइल टेक्स्ट प्रिव्ह्यूसाठी खूप मोठी आहे. \'नवी टॅब\' किंवा डाउनलोड वापरा।',
        'could_not_load' => 'फाइल लोड करता आली नाही।',
        'queue_processing' => '{running} प्रक्रिया, {pending} लेन',
        'queue_no_tasks' => 'कोणतीही सक्रिय कामे नाहीत',
        'file_singular' => '{n} फाइल',
        'file_plural' => '{n} फाइल्स',
        'folder_singular' => '{n} फोल्डर',
        'folder_plural' => '{n} फोल्डर्स',
        'empty' => 'खाली',
        'share' => 'सामायिक करा',
        'share_copied' => 'सामायिक केलेला लिंक क्लिपबोर्डमध्ये कॉपी केले!',
        'share_expires' => 'अंत:',
        'share_never' => 'हमखास',
        'share_1h' => '1 तास',
        'share_1d' => '1 दिवस',
        'share_7d' => '7 दिवस',
        'share_30d' => '30 दिवस',
        'background_tasks' => 'पार्श्वभूमि काम',
        'info_format' => 'फॉरमॅट',
        'info_pages' => 'पृष्ठे',
        'info_title' => 'शीर्षक',
        'info_author' => 'लेखक',
        'info_language' => 'भाषा',
        'info_creator' => 'निर्माता',
        'info_producer' => 'उत्पादक',
        'info_pdf_version' => 'PDF आवृत्ती',
        'info_mime' => 'MIME',
        'info_encrypted' => 'एन्क्रिप्ट केले',
        'info_page_size' => 'पृष्ठ मोठा',
        'info_created' => 'निर्माण झाले',
        'info_resolution' => 'समाधान',
        'info_type' => 'प्रकार',
        'info_vector' => 'वेक्टर ग्राफिक',
        'info_megapixels' => 'मेगापिक्सल',
        'info_color' => 'रंग',
        'info_alpha' => 'अल्फा',
        'info_animated' => 'एनिमेशन केले',
        'info_yes' => 'होय',
        'info_duration' => 'काळाची अवधि',
        'info_container' => 'कंटेनर',
        'info_overall_bitrate' => 'समग्र बिट दर',
        'info_video' => 'व्हिडिओ',
        'info_audio' => 'ऑडिओ',
        'info_bitrate' => 'बिट दर',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'बिट गहूळता',
        'info_sample_rate' => 'नमुना दर',
        'info_channels' => 'चॅनेल',
    ],
    'nl' => [
        '_flag' => '🇳🇱',
        '_name' => 'Nederlands',
        '_dateFormat' => 'd-m-Y H:i',
        '_dateFormatJS' => 'DD-MM-YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Map downloaden',
        'toggle_theme' => 'Wissel tussen licht/donkere modus',
        'search_placeholder' => 'Zoek op naam…',
        'all_types' => 'Alle types',
        'type_folder' => 'Mappen',
        'type_image' => 'Afbeeldingen',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documenten',
        'type_archive' => 'Archieven',
        'type_other' => 'Anders',
        'recursive' => 'Rekursief',
        'col_name' => 'Naam',
        'col_size' => 'Grootte',
        'col_info' => 'Informatie',
        'col_modified' => 'Aangepast',
        'col_actions' => 'Acties',
        'parent_folder' => 'Onderliggende map',
        'folder_empty' => 'Deze map is leeg.',
        'view' => 'Bekijk',
        'download' => 'Downloaden',
        'open' => 'Openen',
        'zip' => 'Zip',
        'loading' => 'Laden…',
        'error_loading' => 'Fout bij het laden van informatie',
        'no_info' => 'Geen informatie beschikbaar',
        'config_error' => '⚠️ Configuratiefout',
        'cookie_text' => 'Deze site gebruikt essentiële cookies om je voorkeuren op te slaan (thema, sortering, taal). Geen tracking of derde-partijcookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Downloaden',
        'viewer_newtab' => '↗ Nieuwe tabblad',
        'viewer_close' => 'Sluiten (Esc)',
        'viewer_prev' => 'Vorige',
        'viewer_next' => 'Volgende',
        'file_too_large' => 'Bestand te groot voor tekstvoorbeeld. Gebruik "Nieuwe tab" of download.',
        'could_not_load' => 'Kon bestand niet laden.',
        'queue_processing' => '{running} verwerken, {pending} in wachtrij',
        'queue_no_tasks' => 'Geen actieve taken',
        'file_singular' => '{n} bestand',
        'file_plural' => '{n} bestanden',
        'folder_singular' => '{n} map',
        'folder_plural' => '{n} mappen',
        'empty' => 'leeg',
        'share' => 'Delen',
        'share_copied' => 'Deelkoppeling gekopieerd naar klembord!',
        'share_expires' => 'Verloopdatum:',
        'share_never' => 'Nooit',
        'share_1h' => '1 uur',
        'share_1d' => '1 dag',
        'share_7d' => '7 dagen',
        'share_30d' => '30 dagen',
        'background_tasks' => 'Achtergrondtaken',
        'info_format' => 'Formaat',
        'info_pages' => 'Pagina\'s',
        'info_title' => 'Titel',
        'info_author' => 'Auteur',
        'info_language' => 'Taal',
        'info_creator' => 'Maker',
        'info_producer' => 'Producer',
        'info_pdf_version' => 'PDF-versie',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Versleuteld',
        'info_page_size' => 'Paginagrootte',
        'info_created' => 'Aangemaakt',
        'info_resolution' => 'Resolutie',
        'info_type' => 'Type',
        'info_vector' => 'Vectorgrafisch',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Kleur',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animatie',
        'info_yes' => 'Ja',
        'info_duration' => 'Duur',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Totale bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bits diepte',
        'info_sample_rate' => 'Bemonsteringsfrequentie',
        'info_channels' => 'Kanalen',
    ],
    'no' => [
        '_flag' => '🇳🇴',
        '_name' => 'Norsk',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Hjem',
        'download_folder' => '⤓ Last ned mappe',
        'toggle_theme' => 'Bytt mellom lydt/mørk modus',
        'search_placeholder' => 'Søk etter navn…',
        'all_types' => 'Alle typer',
        'type_folder' => 'Mapper',
        'type_image' => 'Bilder',
        'type_audio' => 'Lyd',
        'type_video' => 'Video',
        'type_document' => 'Dokumenter',
        'type_archive' => 'Arkiver',
        'type_other' => 'Annet',
        'recursive' => 'Rekursivt',
        'col_name' => 'Navn',
        'col_size' => 'Størrelse',
        'col_info' => 'Info',
        'col_modified' => 'Modifisert',
        'col_actions' => 'Handlinger',
        'parent_folder' => 'Overordnet mappe',
        'folder_empty' => 'Denne mappen er tom.',
        'view' => 'Vis',
        'download' => 'Last ned',
        'open' => 'Åpne',
        'zip' => 'Zip',
        'loading' => 'Laster…',
        'error_loading' => 'Feil ved lasting av informasjon',
        'no_info' => 'Ingen informasjon tilgjengelig',
        'config_error' => '⚠️ Konfigurasjonsfeil',
        'cookie_text' => 'Denne nettsiden bruker nødvendige cookies for å lagre dine innstillinger (tema, sortering, språk). Ingen sporing eller tredjepartscookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Last ned',
        'viewer_newtab' => '↗ Ny fane',
        'viewer_close' => 'Lukk (Esc)',
        'viewer_prev' => 'Forrige',
        'viewer_next' => 'Neste',
        'file_too_large' => 'Fil for stor til tekstforhåndsvisning. Bruk «Ny fane» eller last ned.',
        'could_not_load' => 'Kunne ikke laste filen.',
        'queue_processing' => '{running} prosesserer, {pending} i kø',
        'queue_no_tasks' => 'Ingen aktive oppgaver',
        'file_singular' => '{n} fil',
        'file_plural' => '{n} filer',
        'folder_singular' => '{n} mappe',
        'folder_plural' => '{n} mapper',
        'empty' => 'tomt',
        'share' => 'Del',
        'share_copied' => 'Delingslenke kopiert til utklippstavlen!',
        'share_expires' => 'Utløpsdato:',
        'share_never' => 'Nevér',
        'share_1h' => '1 time',
        'share_1d' => '1 dag',
        'share_7d' => '7 dager',
        'share_30d' => '30 dager',
        'background_tasks' => 'Bakgrunnsoppgaver',
        'info_format' => 'Format',
        'info_pages' => 'Sider',
        'info_title' => 'Tittel',
        'info_author' => 'Forfatter',
        'info_language' => 'Språk',
        'info_creator' => 'Opphavsmann',
        'info_producer' => 'Produsent',
        'info_pdf_version' => 'PDF-versjon',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Kryptert',
        'info_page_size' => 'Sidestørrelse',
        'info_created' => 'Opprettet',
        'info_resolution' => 'Oppløsning',
        'info_type' => 'Type',
        'info_vector' => 'Vektorgrafikk',
        'info_megapixels' => 'Megapiksel',
        'info_color' => 'Farge',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animert',
        'info_yes' => 'Ja',
        'info_duration' => 'Varighet',
        'info_container' => 'Beholder',
        'info_overall_bitrate' => 'Total bitrateg',
        'info_video' => 'Video',
        'info_audio' => 'Lyd',
        'info_bitrate' => 'Bitrateg',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitdybde',
        'info_sample_rate' => 'Prøvefrekvens',
        'info_channels' => 'Kanaler',
    ],
    'pl' => [
        '_flag' => '🇵🇱',
        '_name' => 'Polski',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Strona główna',
        'download_folder' => '⤓ Pobierz folder',
        'toggle_theme' => 'Przełącz tryb jasny/ciemny',
        'search_placeholder' => 'Wyszukaj po nazwie…',
        'all_types' => 'Wszystkie typy',
        'type_folder' => 'Foldery',
        'type_image' => 'Obrazy',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumenty',
        'type_archive' => 'Archwium',
        'type_other' => 'Inne',
        'recursive' => 'Rekursywnie',
        'col_name' => 'Nazwa',
        'col_size' => 'Rozmiar',
        'col_info' => 'Informacje',
        'col_modified' => 'Zmodyfikowany',
        'col_actions' => 'Akcje',
        'parent_folder' => 'Folder nadrzędny',
        'folder_empty' => 'Ten folder jest pusty.',
        'view' => 'Podgląd',
        'download' => 'Pobierz',
        'open' => 'Otwórz',
        'zip' => 'Zip',
        'loading' => 'Ładowanie…',
        'error_loading' => 'Błąd ładowania informacji',
        'no_info' => 'Brak dostępnych informacji',
        'config_error' => '⚠️ Błąd konfiguracji',
        'cookie_text' => 'Ta strona używa koniecznych plików cookie, aby zapamiętać Twoje preferencje (tryb, kolejność sortowania, język). Brak śledzenia ani plików cookie od stron trzecich.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Pobierz',
        'viewer_newtab' => '↗ Nowa karta',
        'viewer_close' => 'Zamknij (Esc)',
        'viewer_prev' => 'Poprzedni',
        'viewer_next' => 'Następny',
        'file_too_large' => 'Plik zbyt duży na podgląd tekstu. Użyj „Nowej karty” lub pobierz.',
        'could_not_load' => 'Nie można załadować pliku.',
        'queue_processing' => '{running} przetwarzanie, {pending} w kolejce',
        'queue_no_tasks' => 'Brak aktywnych zadań',
        'file_singular' => '{n} plik',
        'file_plural' => '{n} pliki',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} foldery',
        'empty' => 'pusty',
        'share' => 'Udostępnij',
        'share_copied' => 'Link do udostępnienia skopiowany do schowka!',
        'share_expires' => 'Wygaśnięcie:',
        'share_never' => 'Nigdy',
        'share_1h' => '1 godzina',
        'share_1d' => '1 dzień',
        'share_7d' => '7 dni',
        'share_30d' => '30 dni',
        'background_tasks' => 'Zadania w tle',
        'info_format' => 'Format',
        'info_pages' => 'Strony',
        'info_title' => 'Tytuł',
        'info_author' => 'Autor',
        'info_language' => 'Język',
        'info_creator' => 'Twórca',
        'info_producer' => 'Producent',
        'info_pdf_version' => 'Wersja PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Zaszyfrowany',
        'info_page_size' => 'Rozmiar strony',
        'info_created' => 'Utworzono',
        'info_resolution' => 'Rozdzielczość',
        'info_type' => 'Typ',
        'info_vector' => 'Grafika wektorowa',
        'info_megapixels' => 'Megapiksele',
        'info_color' => 'Kolor',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animowany',
        'info_yes' => 'Tak',
        'info_duration' => 'Trwałość',
        'info_container' => 'Obudowa',
        'info_overall_bitrate' => 'Całkowita przepustowość',
        'info_video' => 'Wideo',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Przepustowość',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Głębia bitów',
        'info_sample_rate' => 'Częstotliwość próbkowania',
        'info_channels' => 'Kanały',
    ],
    'pt_BR' => [
        '_flag' => '🇧🇷',
        '_name' => 'Português (BR)',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Início',
        'download_folder' => '⤓ Baixar pasta',
        'toggle_theme' => 'Alternar modo claro/escuro',
        'search_placeholder' => 'Buscar por nome…',
        'all_types' => 'Todos os tipos',
        'type_folder' => 'Pastas',
        'type_image' => 'Imagens',
        'type_audio' => 'Áudio',
        'type_video' => 'Vídeo',
        'type_document' => 'Documentos',
        'type_archive' => 'Arquivos compactados',
        'type_other' => 'Outros',
        'recursive' => 'Recursivo',
        'col_name' => 'Nome',
        'col_size' => 'Tamanho',
        'col_info' => 'Informações',
        'col_modified' => 'Modificado',
        'col_actions' => 'Ações',
        'parent_folder' => 'Pasta superior',
        'folder_empty' => 'Esta pasta está vazia.',
        'view' => 'Visualizar',
        'download' => 'Baixar',
        'open' => 'Abrir',
        'zip' => 'Compactar',
        'loading' => 'Carregando…',
        'error_loading' => 'Erro ao carregar informações',
        'no_info' => 'Nenhuma informação disponível',
        'config_error' => '⚠️ Erro de Configuração',
        'cookie_text' => 'Este site usa cookies essenciais para salvar suas preferências (tema, ordem de classificação, idioma). Nenhum rastreamento ou cookies de terceiros.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Baixar',
        'viewer_newtab' => '↗ Nova aba',
        'viewer_close' => 'Fechar (Esc)',
        'viewer_prev' => 'Anterior',
        'viewer_next' => 'Próximo',
        'file_too_large' => 'Arquivo muito grande para pré-visualização de texto. Use "Nova aba" ou baixe-o.',
        'could_not_load' => 'Não foi possível carregar o arquivo.',
        'queue_processing' => '{running} processando, {pending} na fila',
        'queue_no_tasks' => 'Nenhuma tarefa ativa',
        'file_singular' => '{n} arquivo',
        'file_plural' => '{n} arquivos',
        'folder_singular' => '{n} pasta',
        'folder_plural' => '{n} pastas',
        'empty' => 'vazio',
        'share' => 'Compartilhar',
        'share_copied' => 'Link copiado para a área de transferência!',
        'share_expires' => 'Expiração:',
        'share_never' => 'Nunca',
        'share_1h' => '1 hora',
        'share_1d' => '1 dia',
        'share_7d' => '7 dias',
        'share_30d' => '30 dias',
        'background_tasks' => 'Tarefas em segundo plano',
        'info_format' => 'Formato',
        'info_pages' => 'Páginas',
        'info_title' => 'Título',
        'info_author' => 'Autor',
        'info_language' => 'Idioma',
        'info_creator' => 'Criador',
        'info_producer' => 'Produtor',
        'info_pdf_version' => 'Versão PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Criptografado',
        'info_page_size' => 'Tamanho da página',
        'info_created' => 'Criado',
        'info_resolution' => 'Resolução',
        'info_type' => 'Tipo',
        'info_vector' => 'Gráfico vetorial',
        'info_megapixels' => 'Megapixels',
        'info_color' => 'Cor',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animado',
        'info_yes' => 'Sim',
        'info_duration' => 'Duração',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Taxa de bits geral',
        'info_video' => 'Vídeo',
        'info_audio' => 'Áudio',
        'info_bitrate' => 'Taxa de bits',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Profundidade de bit',
        'info_sample_rate' => 'Taxa de amostragem',
        'info_channels' => 'Canais',
    ],
    'ro_RO' => [
        '_flag' => '🇷🇴',
        '_name' => 'Română',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Acasă',
        'download_folder' => '⤓ Descarcă dosarul',
        'toggle_theme' => 'Comută modul clar/închis',
        'search_placeholder' => 'Caută după nume…',
        'all_types' => 'Toate tipurile',
        'type_folder' => 'Dosare',
        'type_image' => 'Imagini',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documente',
        'type_archive' => 'Arhive',
        'type_other' => 'Altele',
        'recursive' => 'Recursiv',
        'col_name' => 'Nume',
        'col_size' => 'Dimensiune',
        'col_info' => 'Info',
        'col_modified' => 'Modificat',
        'col_actions' => 'Acțiuni',
        'parent_folder' => 'Dosarul părinte',
        'folder_empty' => 'Acest dosar este gol.',
        'view' => 'Vizualizează',
        'download' => 'Descarcă',
        'open' => 'Deschide',
        'zip' => 'Zip',
        'loading' => 'Încărcare…',
        'error_loading' => 'Eroare la încărcarea informațiilor',
        'no_info' => 'Nu sunt disponibile informații',
        'config_error' => '⚠️ Eroare de configurare',
        'cookie_text' => 'Această pagină folosește cookie-uri esențiale pentru a salva preferințele tale (temă, ordine de sortare, limbă). Nu se efectuează urmărire sau cookie-uri de terți.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Descarcă',
        'viewer_newtab' => '↗ Fereastră nouă',
        'viewer_close' => 'Închide (Esc)',
        'viewer_prev' => 'Anterior',
        'viewer_next' => 'Următor',
        'file_too_large' => 'Fișierul este prea mare pentru previzualizare. Folosiți „Fereastră nouă” sau descărcați-l.',
        'could_not_load' => 'Nu s-a putut încărca fișierul.',
        'queue_processing' => '{running} procese în curs, {pending} în coadă',
        'queue_no_tasks' => 'Niciun task activ',
        'file_singular' => '{n} fișier',
        'file_plural' => '{n} fișiere',
        'folder_singular' => '{n} dosar',
        'folder_plural' => '{n} dosare',
        'empty' => 'gol',
        'share' => 'Partajează',
        'share_copied' => 'Linkul de partajare a fost copiat în clipboard!',
        'share_expires' => 'Expiră:',
        'share_never' => 'Niciodată',
        'share_1h' => '1 oră',
        'share_1d' => '1 zi',
        'share_7d' => '7 zile',
        'share_30d' => '30 de zile',
        'background_tasks' => 'Taskuri de fundal',
        'info_format' => 'Format',
        'info_pages' => 'Pagini',
        'info_title' => 'Titlu',
        'info_author' => 'Autor',
        'info_language' => 'Limbă',
        'info_creator' => 'Creator',
        'info_producer' => 'Producător',
        'info_pdf_version' => 'Versiune PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Criptat',
        'info_page_size' => 'Dimensiune pagină',
        'info_created' => 'Creat',
        'info_resolution' => 'Rezoluție',
        'info_type' => 'Tip',
        'info_vector' => 'Grafic vectorial',
        'info_megapixels' => 'Megapixeli',
        'info_color' => 'Culoare',
        'info_alpha' => 'Canal alpha',
        'info_animated' => 'Animat',
        'info_yes' => 'Da',
        'info_duration' => 'Durată',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Bitrată totală',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrată',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Adâncime de bit',
        'info_sample_rate' => 'Frecvență de eșantionare',
        'info_channels' => 'Canale',
    ],
    'ru' => [
        '_flag' => '🇷🇺',
        '_name' => 'Русский',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Домашняя страница',
        'download_folder' => '⤓ Скачать папку',
        'toggle_theme' => 'Переключить светлый/темный режим',
        'search_placeholder' => 'Поиск по имени…',
        'all_types' => 'Все типы',
        'type_folder' => 'Папки',
        'type_image' => 'Изображения',
        'type_audio' => 'Аудио',
        'type_video' => 'Видео',
        'type_document' => 'Документы',
        'type_archive' => 'Архивы',
        'type_other' => 'Прочее',
        'recursive' => 'Рекурсивно',
        'col_name' => 'Название',
        'col_size' => 'Размер',
        'col_info' => 'Инфо',
        'col_modified' => 'Изменено',
        'col_actions' => 'Действия',
        'parent_folder' => 'Родительская папка',
        'folder_empty' => 'Эта папка пуста.',
        'view' => 'Просмотреть',
        'download' => 'Скачать',
        'open' => 'Открыть',
        'zip' => 'Зип',
        'loading' => 'Загрузка…',
        'error_loading' => 'Ошибка загрузки информации',
        'no_info' => 'Нет доступной информации',
        'config_error' => '⚠️ Ошибка конфигурации',
        'cookie_text' => 'Этот сайт использует необходимые cookies для сохранения ваших предпочтений (тема, порядок сортировки, язык). Нет отслеживания или сторонних cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Скачать',
        'viewer_newtab' => '↗ Новая вкладка',
        'viewer_close' => 'Закрыть (Esc)',
        'viewer_prev' => 'Предыдущий',
        'viewer_next' => 'Следующий',
        'file_too_large' => 'Файл слишком большой для текстового предварительного просмотра. Используйте "Новую вкладку" или скачайте.',
        'could_not_load' => 'Не удалось загрузить файл.',
        'queue_processing' => '{running} обработка, {pending} в очереди',
        'queue_no_tasks' => 'Нет активных задач',
        'file_singular' => '{n} файл',
        'file_plural' => '{n} файлов',
        'folder_singular' => '{n} папка',
        'folder_plural' => '{n} папок',
        'empty' => 'пусто',
        'share' => 'Совместить',
        'share_copied' => 'Ссылка на совместный доступ скопирована в буфер обмена!',
        'share_expires' => 'Истечение срока:',
        'share_never' => 'Никогда',
        'share_1h' => '1 час',
        'share_1d' => '1 день',
        'share_7d' => '7 дней',
        'share_30d' => '30 дней',
        'background_tasks' => 'Фоновые задачи',
        'info_format' => 'Формат',
        'info_pages' => 'Страницы',
        'info_title' => 'Название',
        'info_author' => 'Автор',
        'info_language' => 'Язык',
        'info_creator' => 'Создатель',
        'info_producer' => 'Производитель',
        'info_pdf_version' => 'Версия PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Шифровано',
        'info_page_size' => 'Размер страницы',
        'info_created' => 'Создано',
        'info_resolution' => 'Разрешение',
        'info_type' => 'Тип',
        'info_vector' => 'Векторная графика',
        'info_megapixels' => 'Мегапиксели',
        'info_color' => 'Цвет',
        'info_alpha' => 'Альфа-канал',
        'info_animated' => 'Анимировано',
        'info_yes' => 'Да',
        'info_duration' => 'Продолжительность',
        'info_container' => 'Контейнер',
        'info_overall_bitrate' => 'Общая скорость передачи данных',
        'info_video' => 'Видео',
        'info_audio' => 'Аудио',
        'info_bitrate' => 'Скорость передачи данных',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Глубина бита',
        'info_sample_rate' => 'Частота дискретизации',
        'info_channels' => 'Каналы',
    ],
    'sk' => [
        '_flag' => '🇸🇰',
        '_name' => 'Slovenčina',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Domov',
        'download_folder' => '⤓ Stiahnuť zložku',
        'toggle_theme' => 'Prepnúť na svetlý/tmavý režim',
        'search_placeholder' => 'Hľadať podľa mena…',
        'all_types' => 'Všetky typy',
        'type_folder' => 'Zložky',
        'type_image' => 'Obrázky',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumenty',
        'type_archive' => 'Archívy',
        'type_other' => 'Iné',
        'recursive' => 'Rekurzívne',
        'col_name' => 'Názov',
        'col_size' => 'Veľkosť',
        'col_info' => 'Info',
        'col_modified' => 'Upravené',
        'col_actions' => 'Akcie',
        'parent_folder' => 'Rodičovská zložka',
        'folder_empty' => 'Táto zložka je prázdna.',
        'view' => 'Zobraziť',
        'download' => 'Stiahnuť',
        'open' => 'Otvoriť',
        'zip' => 'Zipovať',
        'loading' => 'Načítavanie…',
        'error_loading' => 'Chyba pri načítaní informácií',
        'no_info' => 'Nie sú dostupné žiadne informácie',
        'config_error' => '⚠️ Chyba konfigurácie',
        'cookie_text' => 'Tento web používa nezbytočné súbory cookie na uloženie vašich preferencií (téma, poradie triedenia, jazyk). Žiadne sledovanie alebo tretie strany cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Stiahnuť',
        'viewer_newtab' => '↗ Nová karta',
        'viewer_close' => 'Zatvoriť (Esc)',
        'viewer_prev' => 'Predchádzajúci',
        'viewer_next' => 'Nasledujúci',
        'file_too_large' => 'Súbor je príliš veľký na zobrazenie v texte. Použite „Novú kartu“ alebo stiahnite ho.',
        'could_not_load' => 'Nie je možné načítať súbor.',
        'queue_processing' => '{running} spracováva, {pending} čakajúce',
        'queue_no_tasks' => 'Žiadne aktívne úlohy',
        'file_singular' => '{n} súbor',
        'file_plural' => '{n} súborov',
        'folder_singular' => '{n} zložka',
        'folder_plural' => '{n} zložiek',
        'empty' => 'prázdny',
        'share' => 'Zdieľať',
        'share_copied' => 'Odkaz na zdieľanie skopírovaný do schránky!',
        'share_expires' => 'Dátum expirácie:',
        'share_never' => 'Nikdy',
        'share_1h' => '1 hodinu',
        'share_1d' => '1 deň',
        'share_7d' => '7 dní',
        'share_30d' => '30 dní',
        'background_tasks' => 'Pozadové úlohy',
        'info_format' => 'Formát',
        'info_pages' => 'Strany',
        'info_title' => 'Názov',
        'info_author' => 'Autor',
        'info_language' => 'Jazyk',
        'info_creator' => 'Vytvoril',
        'info_producer' => 'Výrobca',
        'info_pdf_version' => 'Verzia PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Šifrované',
        'info_page_size' => 'Veľkosť strany',
        'info_created' => 'Vytvorené',
        'info_resolution' => 'Rozlíšenie',
        'info_type' => 'Typ',
        'info_vector' => 'Vektorový obrázok',
        'info_megapixels' => 'Megapixelov',
        'info_color' => 'Farba',
        'info_alpha' => 'Alfa kanál',
        'info_animated' => 'Animované',
        'info_yes' => 'Áno',
        'info_duration' => 'Trvanie',
        'info_container' => 'Kontajner',
        'info_overall_bitrate' => 'Celková bitová rýchlosť',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitová rýchlosť',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Hĺbka bitu',
        'info_sample_rate' => 'Frekvencia vzorkovania',
        'info_channels' => 'Kanály',
    ],
    'sr_LA' => [
        '_flag' => '🇷🇸',
        '_name' => 'Srpski (lat.)',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Početna',
        'download_folder' => '⤓ Preuzmi mapu',
        'toggle_theme' => 'Prebaci na svetlo/mračni režim',
        'search_placeholder' => 'Pretraži po imenu…',
        'all_types' => 'Svi tipovi',
        'type_folder' => 'Mapa',
        'type_image' => 'Slike',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Dokumenti',
        'type_archive' => 'Arhive',
        'type_other' => 'Ostalo',
        'recursive' => 'Rekurzivno',
        'col_name' => 'Ime',
        'col_size' => 'Veličina',
        'col_info' => 'Info',
        'col_modified' => 'Izmijenjeno',
        'col_actions' => 'Akcije',
        'parent_folder' => 'Rođena mapa',
        'folder_empty' => 'Ova mapa je prazna.',
        'view' => 'Pregledaj',
        'download' => 'Preuzmi',
        'open' => 'Otvori',
        'zip' => 'Zip',
        'loading' => 'Učitavanje…',
        'error_loading' => 'Greška pri učitavanju informacija',
        'no_info' => 'Nema dostupnih informacija',
        'config_error' => '⚠️ Greška konfiguracije',
        'cookie_text' => 'Ovaj sajt koristi esencijalne kolačiće za čuvanje vaših preferencija (tema, redosled sortiranja, jezik). Nema praćenje ili trećepartijne kolačiće.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Preuzmi',
        'viewer_newtab' => '↗ Nova kartica',
        'viewer_close' => 'Zatvori (Esc)',
        'viewer_prev' => 'Prethodna',
        'viewer_next' => 'Naredna',
        'file_too_large' => 'Datoteka je prevelika za prikaz teksta. Koristite „Nova kartica“ ili preuzimanje.',
        'could_not_load' => 'Ne može se učitati datoteka.',
        'queue_processing' => '{running} obrada, {pending} u redu',
        'queue_no_tasks' => 'Nema aktivnih zadataka',
        'file_singular' => '{n} fajl',
        'file_plural' => '{n} fajlova',
        'folder_singular' => '{n} mapa',
        'folder_plural' => '{n} mapa',
        'empty' => 'prazno',
        'share' => 'Deljenje',
        'share_copied' => 'Link za deljenje kopiran u clipboard!',
        'share_expires' => 'Ističe:',
        'share_never' => 'Nikada',
        'share_1h' => '1 sat',
        'share_1d' => '1 dan',
        'share_7d' => '7 dana',
        'share_30d' => '30 dana',
        'background_tasks' => 'Pozadinske zadatke',
        'info_format' => 'Format',
        'info_pages' => 'Stranice',
        'info_title' => 'Naslov',
        'info_author' => 'Autor',
        'info_language' => 'Jezik',
        'info_creator' => 'Kreator',
        'info_producer' => 'Proizvođač',
        'info_pdf_version' => 'PDF verzija',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Šifrovano',
        'info_page_size' => 'Veličina stranice',
        'info_created' => 'Kreirano',
        'info_resolution' => 'Rezolucija',
        'info_type' => 'Tip',
        'info_vector' => 'Vektorska slika',
        'info_megapixels' => 'Megapikseli',
        'info_color' => 'Boja',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animirano',
        'info_yes' => 'Da',
        'info_duration' => 'Trajanje',
        'info_container' => 'Kontejner',
        'info_overall_bitrate' => 'Ukupna brzina prenosa',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Brzina prenosa',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Dubina bita',
        'info_sample_rate' => 'Frekvencija uzorkovanja',
        'info_channels' => 'Kanali',
    ],
    'sr_CY' => [
        '_flag' => '🇷🇸',
        '_name' => 'Српски (ћир.)',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Почетна',
        'download_folder' => '⤓ Преузимање фасцикле',
        'toggle_theme' => 'Промени светли/тамни режим',
        'search_placeholder' => 'Претрага по имени…',
        'all_types' => 'Сви типови',
        'type_folder' => 'Фасцикле',
        'type_image' => 'Слике',
        'type_audio' => 'Аудио',
        'type_video' => 'Видео',
        'type_document' => 'Документи',
        'type_archive' => 'Архиве',
        'type_other' => 'Остали',
        'recursive' => 'Рекурзивно',
        'col_name' => 'Име',
        'col_size' => 'Величина',
        'col_info' => 'Информације',
        'col_modified' => 'Изменjено',
        'col_actions' => 'Акције',
        'parent_folder' => 'Родитељска фасцикла',
        'folder_empty' => 'Ова фасцикла је празна.',
        'view' => 'Приказ',
        'download' => 'Преузимање',
        'open' => 'Отвори',
        'zip' => 'Zip',
        'loading' => 'Учитавање…',
        'error_loading' => 'Грешка при учитавању информација',
        'no_info' => 'Нема доступних података',
        'config_error' => '⚠️ Грешка конфигурације',
        'cookie_text' => 'Овај сајт користи есенцијалне колачиће да би сачували ваше поставке (тема, редослед сортирања, језик). Није укључено праћење или треће стране колачиће.',
        'cookie_ok' => 'ОК',
        'viewer_download' => '⤓ Преузимање',
        'viewer_newtab' => '↗ Нови таб',
        'viewer_close' => 'Затвори (Esc)',
        'viewer_prev' => 'Претходни',
        'viewer_next' => 'Следећи',
        'file_too_large' => 'Фајл је превелик за текстуалну препреглу. Користите "Нови таб" или преузимање.',
        'could_not_load' => 'Не може се учитати фајл.',
        'queue_processing' => '{running} обраде, {pending} у реду',
        'queue_no_tasks' => 'Нема активних задатака',
        'file_singular' => '{n} фајл',
        'file_plural' => '{n} фајлови',
        'folder_singular' => '{n} фасцикла',
        'folder_plural' => '{n} фасцикле',
        'empty' => 'празно',
        'share' => 'Дељење',
        'share_copied' => 'Линк за дељење копиран на клипборд!',
        'share_expires' => 'Истиче:',
        'share_never' => 'Никада',
        'share_1h' => '1 сат',
        'share_1d' => '1 дан',
        'share_7d' => '7 дана',
        'share_30d' => '30 дана',
        'background_tasks' => 'Позадински задаци',
        'info_format' => 'Формат',
        'info_pages' => 'Странице',
        'info_title' => 'Наслов',
        'info_author' => 'Аутор',
        'info_language' => 'Језик',
        'info_creator' => 'Креатор',
        'info_producer' => 'Произвођач',
        'info_pdf_version' => 'PDF верзија',
        'info_mime' => 'Мајм',
        'info_encrypted' => 'Шифровано',
        'info_page_size' => 'Величина странице',
        'info_created' => 'Креирано',
        'info_resolution' => 'Резолуција',
        'info_type' => 'Тип',
        'info_vector' => 'Векторска слика',
        'info_megapixels' => 'Мегапиксели',
        'info_color' => 'Боja',
        'info_alpha' => 'Алфа',
        'info_animated' => 'Анимирано',
        'info_yes' => 'Да',
        'info_duration' => 'Трајање',
        'info_container' => 'Контенер',
        'info_overall_bitrate' => 'Укупна бит-кочница',
        'info_video' => 'Видео',
        'info_audio' => 'Аудио',
        'info_bitrate' => 'Бит-кочница',
        'info_fps' => 'ФПС',
        'info_bit_depth' => 'Дубина бита',
        'info_sample_rate' => 'Честота узорка',
        'info_channels' => 'Канали',
    ],
    'sv' => [
        '_flag' => '🇸🇪',
        '_name' => 'Svenska',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Hem',
        'download_folder' => '⤓ Ladda ner mapp',
        'toggle_theme' => 'Växla mellan ljus/mörk läge',
        'search_placeholder' => 'Sök efter namn…',
        'all_types' => 'Alla typer',
        'type_folder' => 'Mappar',
        'type_image' => 'Bilder',
        'type_audio' => 'Ljud',
        'type_video' => 'Video',
        'type_document' => 'Dokument',
        'type_archive' => 'Arkiv',
        'type_other' => 'Övrigt',
        'recursive' => 'Rekursiv',
        'col_name' => 'Namn',
        'col_size' => 'Storlek',
        'col_info' => 'Info',
        'col_modified' => 'Ändrad',
        'col_actions' => 'Åtgärder',
        'parent_folder' => 'Överordnad mapp',
        'folder_empty' => 'Den här mappen är tom.',
        'view' => 'Visa',
        'download' => 'Ladda ner',
        'open' => 'Öppna',
        'zip' => 'Zip',
        'loading' => 'Laddar…',
        'error_loading' => 'Fel vid laddning av info',
        'no_info' => 'Ingen information tillgänglig',
        'config_error' => '⚠️ Konfigurationsfel',
        'cookie_text' => 'Den här webbplatsen använder nödvändiga cookies för att spara dina inställningar (tema, sortering, språk). Inga spårnings- eller tredjepartscookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Ladda ner',
        'viewer_newtab' => '↗ Ny flik',
        'viewer_close' => 'Stäng (Esc)',
        'viewer_prev' => 'Föregående',
        'viewer_next' => 'Nästa',
        'file_too_large' => 'Filens storlek är för stor för textförhandsgranskning. Använd "Ny flik" eller ladda ner.',
        'could_not_load' => 'Kunde inte ladda filen.',
        'queue_processing' => '{running} bearbetar, {pending} i kö',
        'queue_no_tasks' => 'Inga aktiva uppgifter',
        'file_singular' => '{n} fil',
        'file_plural' => '{n} filer',
        'folder_singular' => '{n} mapp',
        'folder_plural' => '{n} mappar',
        'empty' => 'tom',
        'share' => 'Dela',
        'share_copied' => 'Delalänk kopierad till urklipp!',
        'share_expires' => 'Utgångstid:',
        'share_never' => 'När som helst',
        'share_1h' => '1 timme',
        'share_1d' => '1 dag',
        'share_7d' => '7 dagar',
        'share_30d' => '30 dagar',
        'background_tasks' => 'Bakgrundsaktiviteter',
        'info_format' => 'Format',
        'info_pages' => 'Sidor',
        'info_title' => 'Titel',
        'info_author' => 'Författare',
        'info_language' => 'Språk',
        'info_creator' => 'Skapare',
        'info_producer' => 'Producent',
        'info_pdf_version' => 'PDF-version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Krypterad',
        'info_page_size' => 'Sidstorlek',
        'info_created' => 'Skapad',
        'info_resolution' => 'Upplösning',
        'info_type' => 'Typ',
        'info_vector' => 'Vektorgrafik',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Färg',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animerad',
        'info_yes' => 'Ja',
        'info_duration' => 'Varaktighet',
        'info_container' => 'Behållare',
        'info_overall_bitrate' => 'Total bitflöde',
        'info_video' => 'Video',
        'info_audio' => 'Ljud',
        'info_bitrate' => 'Bitflöde',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bitdjup',
        'info_sample_rate' => 'Provspegel',
        'info_channels' => 'Kanaler',
    ],
    'sw' => [
        '_flag' => '🇰🇪',
        '_name' => 'Kiswahili',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Nyumbani',
        'download_folder' => '⤓ Folder ya upakia',
        'toggle_theme' => 'Fungua/mfunga mizigo ya kipinduzi/kibao',
        'search_placeholder' => 'Tafuta kwa jina…',
        'all_types' => 'Zote',
        'type_folder' => 'Mapengo',
        'type_image' => 'Picha',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Mbilu',
        'type_archive' => 'Archiwi',
        'type_other' => 'Mengine',
        'recursive' => 'Kwepo pamoja na matokeo ya kipinduzi',
        'col_name' => 'Jina',
        'col_size' => 'Ukubwa',
        'col_info' => 'Taarifa',
        'col_modified' => 'Imetengenezwa',
        'col_actions' => 'Mishahara',
        'parent_folder' => 'Mapengo ya mpya',
        'folder_empty' => 'Folder hii ina matokeo ya kipinduzi.',
        'view' => 'Tazama',
        'download' => 'Pakia',
        'open' => 'Fungua',
        'zip' => 'Zip',
        'loading' => 'Inapangwa…',
        'error_loading' => 'Kosa ya kuchagua taarifa',
        'no_info' => 'Hakuna taarifa inayofanana',
        'config_error' => '⚠️ Kosa ya mipango',
        'cookie_text' => 'Mchoro huu inatumia cookies ya kipande kwa kujifunza upakia wewe (mizigo, urudumu wa kuchagua, lugha). Hakuna uchunguzi au cookies ya mbinu ya tatu.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Pakia',
        'viewer_newtab' => '↗ Tabu ya jinsi ya',
        'viewer_close' => 'Funga (Esc)',
        'viewer_prev' => 'Mpya',
        'viewer_next' => 'Iliyotangulia',
        'file_too_large' => 'Faili inapakia kubeba matokeo ya maelezo. Tumia "Tabu ya jinsi ya" au upakie.',
        'could_not_load' => 'Hakuna faili iliyojifunza.',
        'queue_processing' => '{running} inapangwa, {pending} zilizotengenezwa',
        'queue_no_tasks' => 'Hakuna matokeo ya kipinduzi',
        'file_singular' => '{n} faili',
        'file_plural' => '{n} faili',
        'folder_singular' => '{n} mapengo',
        'folder_plural' => '{n} mapengo',
        'empty' => 'kafu',
        'share' => 'Tushauri',
        'share_copied' => 'Linki ya tushauri imekamilwa kwenye board ya paka!',
        'share_expires' => 'Kutangaza:',
        'share_never' => 'Hakuna muda',
        'share_1h' => 'Saa moja',
        'share_1d' => 'Siku moja',
        'share_7d' => 'Sita siku',
        'share_30d' => 'Miezi 30',
        'background_tasks' => 'Matokeo ya kipinduzi ya background',
        'info_format' => 'Fomati',
        'info_pages' => 'Huduma',
        'info_title' => 'Jina',
        'info_author' => 'Mfanyaji',
        'info_language' => 'Lugha',
        'info_creator' => 'Mfanyaji',
        'info_producer' => 'Mfanyaji',
        'info_pdf_version' => 'Fomati ya PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Imetengenezwa',
        'info_page_size' => 'Ukubwa wa huduma',
        'info_created' => 'Imetengenezwa',
        'info_resolution' => 'Kipenzi',
        'info_type' => 'Fomati',
        'info_vector' => 'Grafi ya vector',
        'info_megapixels' => 'Megapixeli',
        'info_color' => 'Rangi',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Imetengenezwa',
        'info_yes' => 'Ndiyo',
        'info_duration' => 'Muda',
        'info_container' => 'Kipande',
        'info_overall_bitrate' => 'Bitrate ya kuzungusha',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Ukubwa wa biti',
        'info_sample_rate' => 'Kipenzi cha sampuli',
        'info_channels' => 'Mipande',
    ],
    'ta' => [
        '_flag' => '🇮🇳',
        '_name' => 'தமிழ்',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'முகப்பு',
        'download_folder' => '⤓ தரவிறக்கு அடுக்கு',
        'toggle_theme' => 'ஒளி/இருட்டு மோடு மாற்று',
        'search_placeholder' => 'பெயரின் படி தேடவும்…',
        'all_types' => 'அனைத்து வகைகள்',
        'type_folder' => 'அடுக்குகள்',
        'type_image' => 'படங்கள்',
        'type_audio' => 'ஆடியோ',
        'type_video' => 'வீடியோ',
        'type_document' => 'ஆவணங்கள்',
        'type_archive' => 'அர்ச்சைகள்',
        'type_other' => 'மற்றவை',
        'recursive' => 'தொடர்',
        'col_name' => 'பெயர்',
        'col_size' => 'அளவு',
        'col_info' => 'தகவல்',
        'col_modified' => 'மாற்றப்பட்ட நேரம்',
        'col_actions' => 'செயல்பாடுகள்',
        'parent_folder' => 'முன்னாள் அடுக்கு',
        'folder_empty' => 'இந்த அடுக்கு வெறியாக உள்ளது.',
        'view' => 'முகப்பு',
        'download' => 'பதிவிறக்கு',
        'open' => 'திற',
        'zip' => 'சீர்',
        'loading' => 'ஏற்படுத்தும்…',
        'error_loading' => 'தகவல் ஏற்படுத்தும் பிழை',
        'no_info' => 'தகவல் கிடையாது',
        'config_error' => '⚠️ அமைப்பு பிழை',
        'cookie_text' => 'இந்த தளம் உங்கள் முறைகளை (அடிப்படை, வரிசை வரிசை, மொழி) சேமிக்க அவசிய கூகிள்களைப் பயன்படுத்துகிறது. பின்பற்றும் அல்லது மூன்றாம் தரப்பு கூகிள்கள் இல்லை.',
        'cookie_ok' => 'சரி',
        'viewer_download' => '⤓ பதிவிறக்கு',
        'viewer_newtab' => '↗ புதிய தாவல்',
        'viewer_close' => 'மூடு (Esc)',
        'viewer_prev' => 'முந்தையது',
        'viewer_next' => 'அடுத்தது',
        'file_too_large' => 'இந்த கூட்டு மென்பொருள் பார்வையில் அதிகமாக உள்ளது. "புதிய தாவல்" அல்லது பதிவிறக்கு பயன்படுத்தவும்.',
        'could_not_load' => 'கூட்டு ஏற்படுத்த முடியாது.',
        'queue_processing' => '{running} செயல்பாடு, {pending} காத்திருப்பது',
        'queue_no_tasks' => 'செயல்பாடுகள் இல்லை',
        'file_singular' => '{n} கூட்டு',
        'file_plural' => '{n} கூட்டுகள்',
        'folder_singular' => '{n} அடுக்கு',
        'folder_plural' => '{n} அடுக்குகள்',
        'empty' => 'வெறியாக',
        'share' => 'பகிரல்',
        'share_copied' => 'பகிரல் இணைப்பு காப்பி செய்யப்பட்டது!',
        'share_expires' => 'வருமானம்:',
        'share_never' => 'என்றும்',
        'share_1h' => '1 மணி',
        'share_1d' => '1 நாள்',
        'share_7d' => '7 நாட்கள்',
        'share_30d' => '30 நாட்கள்',
        'background_tasks' => 'பின்னணி செயல்பாடுகள்',
        'info_format' => 'வடிவம்',
        'info_pages' => 'பக்கங்கள்',
        'info_title' => 'தலைப்பு',
        'info_author' => 'ஆசிரியர்',
        'info_language' => 'மொழி',
        'info_creator' => 'வடிவமைப்பாளர்',
        'info_producer' => 'தயாரிப்பாளர்',
        'info_pdf_version' => 'PDF பதிப்பு',
        'info_mime' => 'MIME',
        'info_encrypted' => 'சிப்பிக்கப்பட்டது',
        'info_page_size' => 'பக்க அளவு',
        'info_created' => 'உருவானது',
        'info_resolution' => 'தீர்மானம்',
        'info_type' => 'வகை',
        'info_vector' => 'வெக்டர் படம்',
        'info_megapixels' => 'மெகாபிக்சல்கள்',
        'info_color' => 'நிறம்',
        'info_alpha' => 'அல்ஃபா',
        'info_animated' => 'சேர்த்து வடிவமைக்கப்பட்டது',
        'info_yes' => 'ஆம்',
        'info_duration' => 'வரி',
        'info_container' => 'கலவையான தொகுப்பு',
        'info_overall_bitrate' => 'சேர்க்கை விகிதம்',
        'info_video' => 'வீடியோ',
        'info_audio' => 'ஆடியோ',
        'info_bitrate' => 'விகிதம்',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'பிட் ஆழம்',
        'info_sample_rate' => 'சாம்பிள் விகிதம்',
        'info_channels' => 'துறைகள்',
    ],
    'te' => [
        '_flag' => '🇮🇳',
        '_name' => 'తెలుగు',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'హోమ్',
        'download_folder' => '⤓ డౌన్‌లోడ్ ఫోల్డర్',
        'toggle_theme' => 'ప్రకాశవంతమైన/గుండె మోడ్‌ను అమలు చేయండి',
        'search_placeholder' => 'పేరు ద్వారా వెతకండి…',
        'all_types' => 'అన్ని రకాలు',
        'type_folder' => 'ఫోల్డర్లు',
        'type_image' => 'చిత్రాలు',
        'type_audio' => 'ఆడియో',
        'type_video' => 'వీడియో',
        'type_document' => 'దస్తావేజులు',
        'type_archive' => 'అర్కైవ్‌లు',
        'type_other' => 'ఇతర',
        'recursive' => 'రికర్సివ్',
        'col_name' => 'పేరు',
        'col_size' => 'పరిమాణం',
        'col_info' => 'సమాచారం',
        'col_modified' => 'సవరించబడిన',
        'col_actions' => 'కార్యాలు',
        'parent_folder' => 'పై ఫోల్డర్',
        'folder_empty' => 'ఈ ఫోల్డర్‌లో ఏదీ లేదు.',
        'view' => 'వీక్షించండి',
        'download' => 'డౌన్‌లోడ్ చేయండి',
        'open' => 'ఓపెన్ చేయండి',
        'zip' => 'జిప్',
        'loading' => 'లోడ్ అవుతోంది…',
        'error_loading' => 'సమాచారాన్ని లోడ్ చేయడంలో ఫాల్ట్',
        'no_info' => 'అందుబాటులో సమాచారం లేదు',
        'config_error' => '⚠️ కాన్ఫిగరేషన్ తప్పు',
        'cookie_text' => 'ఈ వెబ్‌సైట్ మీ అభిరుచులను (థీం, క్రమీకరణ, భాష) నిల్వ చేయడానికి అవసరమైన కుక్కీలను ఉపయోగిస్తుంది. ట్రాకింగ్ లేదా మూడవ-పక్క కుక్కీలు లేవు.',
        'cookie_ok' => 'సరైనది',
        'viewer_download' => '⤓ డౌన్‌లోడ్',
        'viewer_newtab' => '↗ కొత్త టాబ్',
        'viewer_close' => 'మూసివేయండి (Esc)',
        'viewer_prev' => 'ప్రివియస్',
        'viewer_next' => 'నెక్స్ట్',
        'file_too_large' => 'పಠ్య చూపింపుకు ఫైల్ ఎక్కువగా ఉంది. "కొత్త టాబ్" లేదా డౌన్‌లోడ్ చేయండి.',
        'could_not_load' => 'ఫైలును లోడ్ చేయలేకపోయారు.',
        'queue_processing' => '{running} ప్రస్తుతం, {pending} క్వీయెంట్',
        'queue_no_tasks' => 'కొనసాగే కార్యాలు లేవు',
        'file_singular' => '{n} ఫైల్',
        'file_plural' => '{n} ఫైళ్లు',
        'folder_singular' => '{n} ఫోల్డర్',
        'folder_plural' => '{n} ఫోల్డర్లు',
        'empty' => 'ఖాళీ',
        'share' => 'షేర్',
        'share_copied' => 'షేర్ లింక్ క్లిప్‌బోర్డుకు నకలు తీసుకోబడింది!',
        'share_expires' => 'విధిగత కాలం:',
        'share_never' => 'ఎన్నడూ',
        'share_1h' => '1 గంట',
        'share_1d' => '1 రోజు',
        'share_7d' => '7 రోజులు',
        'share_30d' => '30 రోజులు',
        'background_tasks' => 'బ్యాక్‌గ్రౌండ్ కార్యాలు',
        'info_format' => 'ఫార్మాట్',
        'info_pages' => 'పేజీలు',
        'info_title' => 'శీర్షిక',
        'info_author' => 'రచయిత',
        'info_language' => 'భాష',
        'info_creator' => 'నిర్మాత',
        'info_producer' => 'ఉత్పత్తిదారుడు',
        'info_pdf_version' => 'PDF వెర్షన్',
        'info_mime' => 'MIME',
        'info_encrypted' => 'కోడింగ్ చేయబడినది',
        'info_page_size' => 'పేజీ సైజు',
        'info_created' => 'సృష్టించబడిన',
        'info_resolution' => 'రిజల్యూషన్',
        'info_type' => 'రకం',
        'info_vector' => 'వెక్టర్ గ్రాఫిక్',
        'info_megapixels' => 'మెగాపిక్సల్స్',
        'info_color' => 'రంగు',
        'info_alpha' => 'ఆల్ఫా',
        'info_animated' => 'అనిమేటెడ్',
        'info_yes' => 'హా, నిజం',
        'info_duration' => 'కాలం',
        'info_container' => 'కంటైనర్',
        'info_overall_bitrate' => 'సముదాయ బిట్‌రేట్',
        'info_video' => 'వీడియో',
        'info_audio' => 'ఆడియో',
        'info_bitrate' => 'బిట్‌రేట్',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'బిట్ గాఢత',
        'info_sample_rate' => 'నమూనా రేటు',
        'info_channels' => 'ఛానెల్స్',
    ],
    'tl' => [
        '_flag' => '🇵🇭',
        '_name' => 'Tagalog',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Tahanan',
        'download_folder' => '⤓ Kumuha ng Folder',
        'toggle_theme' => 'Ibaba o Ilawit ang Mode ng Liwanag/Maliwanag',
        'search_placeholder' => 'Maghanap ayon sa pangalan…',
        'all_types' => 'Lahat ng uri',
        'type_folder' => 'Mga Folder',
        'type_image' => 'Mga Larawan',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Mga Dokumento',
        'type_archive' => 'Mga Archive',
        'type_other' => 'Iba pang',
        'recursive' => 'Rekursibo',
        'col_name' => 'Pangalan',
        'col_size' => 'Laki',
        'col_info' => 'Impormasyon',
        'col_modified' => 'Binago',
        'col_actions' => 'Mga Aksyon',
        'parent_folder' => 'Parent Folder',
        'folder_empty' => 'Walang laman ang folder na ito.',
        'view' => 'Tingnan',
        'download' => 'I-download',
        'open' => 'Bukas',
        'zip' => 'Zip',
        'loading' => 'Naglo-load…',
        'error_loading' => 'Kamaliang naglo-load ng impormasyon',
        'no_info' => 'Walang magagamit na impormasyon',
        'config_error' => '⚠️ Kamaliang Pagkakabuo',
        'cookie_text' => 'Ginagamit ng site na ito ang mga kritikal na cookies para i-save ang iyong mga preferensya (tema, order ng pag-ayos, wika). Walang pagsubaybay o third-party cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ I-download',
        'viewer_newtab' => '↗ Bagong Tab',
        'viewer_close' => 'Ibura (Esc)',
        'viewer_prev' => 'Nakaraan',
        'viewer_next' => 'Susunod',
        'file_too_large' => 'Ang file ay masyadong malaki para sa text preview. Gamitin ang "Bagong Tab" o i-download.',
        'could_not_load' => 'Hindi ma-load ang file.',
        'queue_processing' => '{running} na nagproseso, {pending} na naka-queue',
        'queue_no_tasks' => 'Walang aktibong mga gawain',
        'file_singular' => '{n} file',
        'file_plural' => '{n} mga file',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} mga folder',
        'empty' => 'walang laman',
        'share' => 'Ibahagi',
        'share_copied' => 'Nakopya ang link ng pagbabahagi sa clipboard!',
        'share_expires' => 'Paghahati:',
        'share_never' => 'Hindi kailanman',
        'share_1h' => '1 oras',
        'share_1d' => '1 araw',
        'share_7d' => '7 araw',
        'share_30d' => '30 araw',
        'background_tasks' => 'Mga Task sa Background',
        'info_format' => 'Formato',
        'info_pages' => 'Mga Pahina',
        'info_title' => 'Pamagat',
        'info_author' => 'Manunulat',
        'info_language' => 'Wika',
        'info_creator' => 'Tagalikha',
        'info_producer' => 'Tagapagtanim',
        'info_pdf_version' => 'Versyon ng PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Nakaimbento',
        'info_page_size' => 'Laki ng Pahina',
        'info_created' => 'Nilikha',
        'info_resolution' => 'Resolusyon',
        'info_type' => 'Uri',
        'info_vector' => 'Vector graphic',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Kulay',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Nakaimbento',
        'info_yes' => 'Oo',
        'info_duration' => 'Panahon',
        'info_container' => 'Kontainer',
        'info_overall_bitrate' => 'Overall bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit depth',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Mga Channel',
    ],
    'th' => [
        '_flag' => '🇹🇭',
        '_name' => 'ไทย',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'หน้าแรก',
        'download_folder' => '⤓ ดาวน์โหลดโฟลเดอร์',
        'toggle_theme' => 'สลับโหมดแสง/มืด',
        'search_placeholder' => 'ค้นหาตามชื่อ…',
        'all_types' => 'ทั้งหมด',
        'type_folder' => 'โฟลเดอร์',
        'type_image' => 'รูปภาพ',
        'type_audio' => 'เสียง',
        'type_video' => 'วิดีโอ',
        'type_document' => 'เอกสาร',
        'type_archive' => 'ไฟล์บีบอัด',
        'type_other' => 'อื่นๆ',
        'recursive' => 'แบบสืบเนื่อง',
        'col_name' => 'ชื่อ',
        'col_size' => 'ขนาด',
        'col_info' => 'ข้อมูล',
        'col_modified' => 'แก้ไขล่าสุด',
        'col_actions' => 'การกระทำ',
        'parent_folder' => 'โฟลเดอร์แม่',
        'folder_empty' => 'โฟลเดอร์นี้ว่างเปล่า',
        'view' => 'ดู',
        'download' => 'ดาวน์โหลด',
        'open' => 'เปิด',
        'zip' => 'บีบอัดเป็น ZIP',
        'loading' => 'กำลังโหลด…',
        'error_loading' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
        'no_info' => 'ไม่มีข้อมูลที่ใช้งานได้',
        'config_error' => '⚠️ ข้อผิดพลาดการตั้งค่า',
        'cookie_text' => 'เว็บไซต์นี้ใช้คุกกี้จำเป็นเพื่อจัดเก็บการตั้งค่าของคุณ (ธีม, ลำดับการเรียง, ภาษา) โดยไม่มีการติดตามหรือคุกกี้จากบุคคลที่สาม',
        'cookie_ok' => 'ตกลง',
        'viewer_download' => '⤓ ดาวน์โหลด',
        'viewer_newtab' => '↗ เปิดแท็บใหม่',
        'viewer_close' => 'ปิด (Esc)',
        'viewer_prev' => 'ก่อนหน้า',
        'viewer_next' => 'ถัดไป',
        'file_too_large' => 'ไฟล์มีขนาดใหญ่เกินไปสำหรับการดูแบบย่อหน้า โปรดใช้ "เปิดแท็บใหม่" หรือดาวน์โหลด',
        'could_not_load' => 'ไม่สามารถโหลดไฟล์ได้',
        'queue_processing' => '{running} กำลังประมวลผล, {pending} ค้างอยู่ในคิว',
        'queue_no_tasks' => 'ไม่มีงานที่กำลังดำเนินการ',
        'file_singular' => '{n} ไฟล์',
        'file_plural' => '{n} ไฟล์',
        'folder_singular' => '{n} โฟลเดอร์',
        'folder_plural' => '{n} โฟลเดอร์',
        'empty' => 'ว่างเปล่า',
        'share' => 'แบ่งปัน',
        'share_copied' => 'ลิงก์สำหรับการแบ่งปันถูกคัดลอกไปยังคลิปบอร์ดแล้ว!',
        'share_expires' => 'หมดอายุ:',
        'share_never' => 'ไม่มีกำหนด',
        'share_1h' => '1 ชั่วโมง',
        'share_1d' => '1 วัน',
        'share_7d' => '7 วัน',
        'share_30d' => '30 วัน',
        'background_tasks' => 'งานพื้นหลัง',
        'info_format' => 'รูปแบบ',
        'info_pages' => 'หน้า',
        'info_title' => 'หัวข้อ',
        'info_author' => 'ผู้แต่ง',
        'info_language' => 'ภาษา',
        'info_creator' => 'ผู้สร้าง',
        'info_producer' => 'ผู้ผลิต',
        'info_pdf_version' => 'เวอร์ชัน PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'เข้ารหัส',
        'info_page_size' => 'ขนาดหน้า',
        'info_created' => 'สร้างขึ้นเมื่อ',
        'info_resolution' => 'ความละเอียด',
        'info_type' => 'ประเภท',
        'info_vector' => 'กราฟิกเวกเตอร์',
        'info_megapixels' => 'เมกะพิกเซล',
        'info_color' => 'สี',
        'info_alpha' => 'แอลฟา',
        'info_animated' => 'เคลื่อนไหว',
        'info_yes' => 'ใช่',
        'info_duration' => 'ระยะเวลา',
        'info_container' => 'คอนเทนเนอร์',
        'info_overall_bitrate' => 'อัตราบิตทั้งหมด',
        'info_video' => 'วิดีโอ',
        'info_audio' => 'เสียง',
        'info_bitrate' => 'อัตราบิต',
        'info_fps' => 'เฟรมต่อวินาที',
        'info_bit_depth' => 'ความลึกของบิต',
        'info_sample_rate' => 'อัตราการเก็บตัวอย่าง',
        'info_channels' => 'ช่องทาง',
    ],
    'tr' => [
        '_flag' => '🇹🇷',
        '_name' => 'Türkçe',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Ana Sayfa',
        'download_folder' => '⤓ Klasörü İndir',
        'toggle_theme' => 'Açık/Karanlık Mod Arasında Geçiş Yap',
        'search_placeholder' => 'İsime Göre Ara…',
        'all_types' => 'Tüm Türler',
        'type_folder' => 'Klasörler',
        'type_image' => 'Görseller',
        'type_audio' => 'Ses Dosyaları',
        'type_video' => 'Video',
        'type_document' => 'Belgeler',
        'type_archive' => 'Arşivler',
        'type_other' => 'Diğer',
        'recursive' => 'Özyinelemeli',
        'col_name' => 'İsim',
        'col_size' => 'Boyut',
        'col_info' => 'Bilgi',
        'col_modified' => 'Değiştirildi',
        'col_actions' => 'Eylemler',
        'parent_folder' => 'Üst Klasör',
        'folder_empty' => 'Bu klasör boş.',
        'view' => 'Göster',
        'download' => 'İndir',
        'open' => 'Aç',
        'zip' => 'Sıkıştır',
        'loading' => 'Yükleniyor…',
        'error_loading' => 'Bilgi yüklenirken hata oluştu',
        'no_info' => 'Kullanılabilir bilgi yok',
        'config_error' => '⚠️ Yapılandırma Hatası',
        'cookie_text' => 'Bu site, tercihlerinizi (tema, sıralama, dil) kaydetmek için gerekli çerezler kullanır. İzleme veya üçüncü taraf çerezleri kullanmaz.',
        'cookie_ok' => 'Tamam',
        'viewer_download' => '⤓ İndir',
        'viewer_newtab' => '↗ Yeni Sekme',
        'viewer_close' => 'Kapat (Esc)',
        'viewer_prev' => 'Önceki',
        'viewer_next' => 'Sonraki',
        'file_too_large' => 'Dosya metin önizlemesi için çok büyük. "Yeni Sekme" kullanın veya indirin.',
        'could_not_load' => 'Dosya yüklenemedi.',
        'queue_processing' => '{running} işleme devam ediyor, {pending} bekliyor',
        'queue_no_tasks' => 'Aktif görev yok',
        'file_singular' => '{n} dosya',
        'file_plural' => '{n} dosya',
        'folder_singular' => '{n} klasör',
        'folder_plural' => '{n} klasör',
        'empty' => 'boş',
        'share' => 'Paylaş',
        'share_copied' => 'Paylaşım bağlantısı panoya kopyalandı!',
        'share_expires' => 'Sonuç:',
        'share_never' => 'Asla',
        'share_1h' => '1 saat',
        'share_1d' => '1 gün',
        'share_7d' => '7 gün',
        'share_30d' => '30 gün',
        'background_tasks' => 'Arka Plan Görevleri',
        'info_format' => 'Biçim',
        'info_pages' => 'Sayfalar',
        'info_title' => 'Başlık',
        'info_author' => 'Yazar',
        'info_language' => 'Dil',
        'info_creator' => 'Oluşturan',
        'info_producer' => 'Üretici',
        'info_pdf_version' => 'PDF sürümü',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Şifrelenmiş',
        'info_page_size' => 'Sayfa boyutu',
        'info_created' => 'Oluşturuldu',
        'info_resolution' => 'Çözünürlük',
        'info_type' => 'Tip',
        'info_vector' => 'Vektörel grafik',
        'info_megapixels' => 'Megapiksel',
        'info_color' => 'Renk',
        'info_alpha' => 'Alfa',
        'info_animated' => 'Animasyonlu',
        'info_yes' => 'Evet',
        'info_duration' => 'Süre',
        'info_container' => 'Kapsayıcı',
        'info_overall_bitrate' => 'Genel bit hızı',
        'info_video' => 'Video',
        'info_audio' => 'Ses',
        'info_bitrate' => 'Bit hızı',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit derinliği',
        'info_sample_rate' => 'Örnekleme oranı',
        'info_channels' => 'Kanallar',
    ],
    'uk' => [
        '_flag' => '🇺🇦',
        '_name' => 'Українська',
        '_dateFormat' => 'd.m.Y H:i',
        '_dateFormatJS' => 'DD.MM.YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Головна',
        'download_folder' => '⤓ Завантажити папку',
        'toggle_theme' => 'Перемикач світлого/темного режиму',
        'search_placeholder' => 'Пошук за назвою…',
        'all_types' => 'Всі типи',
        'type_folder' => 'Папки',
        'type_image' => 'Зображення',
        'type_audio' => 'Аудіо',
        'type_video' => 'Відео',
        'type_document' => 'Документи',
        'type_archive' => 'Архіви',
        'type_other' => 'Інше',
        'recursive' => 'Рекурсивно',
        'col_name' => 'Назва',
        'col_size' => 'Розмір',
        'col_info' => 'Інформація',
        'col_modified' => 'Змінено',
        'col_actions' => 'Дії',
        'parent_folder' => 'Батьківська папка',
        'folder_empty' => 'Ця папка порожня.',
        'view' => 'Переглянути',
        'download' => 'Завантажити',
        'open' => 'Відкрити',
        'zip' => 'Стиснути в ZIP',
        'loading' => 'Завантаження…',
        'error_loading' => 'Помилка завантаження інформації',
        'no_info' => 'Інформація недоступна',
        'config_error' => '⚠️ Помилка конфігурації',
        'cookie_text' => 'Цей сайт використовує обов\'язкові куки для збереження ваших налаштувань (тема, порядок сортування, мова). Жодної трекінгу або сторонніх куків.',
        'cookie_ok' => 'ОК',
        'viewer_download' => '⤓ Завантажити',
        'viewer_newtab' => '↗ Нова вкладка',
        'viewer_close' => 'Закрити (Esc)',
        'viewer_prev' => 'Попередній',
        'viewer_next' => 'Наступний',
        'file_too_large' => 'Файл занадто великий для текстового перегляду. Використовуйте «Нова вкладка» або завантаження.',
        'could_not_load' => 'Не вдалося завантажити файл.',
        'queue_processing' => '{running} обробки, {pending} очікують',
        'queue_no_tasks' => 'Жодних активних завдань',
        'file_singular' => '{n} файл',
        'file_plural' => '{n} файли',
        'folder_singular' => '{n} папка',
        'folder_plural' => '{n} папок',
        'empty' => 'порожній',
        'share' => 'Поділитися',
        'share_copied' => 'Посилання на поділ скопійовано в буфер обміну!',
        'share_expires' => 'Термін дії:',
        'share_never' => 'Ніколи',
        'share_1h' => '1 година',
        'share_1d' => '1 день',
        'share_7d' => '7 днів',
        'share_30d' => '30 днів',
        'background_tasks' => 'Фонові завдання',
        'info_format' => 'Формат',
        'info_pages' => 'Сторінки',
        'info_title' => 'Назва',
        'info_author' => 'Автор',
        'info_language' => 'Мова',
        'info_creator' => 'Створювач',
        'info_producer' => 'Виробник',
        'info_pdf_version' => 'Версія PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Шифрований',
        'info_page_size' => 'Розмір сторінки',
        'info_created' => 'Створено',
        'info_resolution' => 'Роздільна здатність',
        'info_type' => 'Тип',
        'info_vector' => 'Векторна графіка',
        'info_megapixels' => 'Мегапікселі',
        'info_color' => 'Колір',
        'info_alpha' => 'Альфа',
        'info_animated' => 'Анімований',
        'info_yes' => 'Так',
        'info_duration' => 'Тривалість',
        'info_container' => 'Контейнер',
        'info_overall_bitrate' => 'Загальна швидкість передачі даних',
        'info_video' => 'Відео',
        'info_audio' => 'Аудіо',
        'info_bitrate' => 'Швидкість передачі даних',
        'info_fps' => 'Кадр/с',
        'info_bit_depth' => 'Глибина біта',
        'info_sample_rate' => 'Частота дискретизації',
        'info_channels' => 'Канали',
    ],
    'ur' => [
        '_flag' => '🇵🇰',
        '_name' => 'اردو',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Home',
        'download_folder' => '⤓ Download folder',
        'toggle_theme' => 'Toggle light/dark mode',
        'search_placeholder' => 'Search by name…',
        'all_types' => 'All types',
        'type_folder' => 'Folders',
        'type_image' => 'Images',
        'type_audio' => 'Audio',
        'type_video' => 'Video',
        'type_document' => 'Documents',
        'type_archive' => 'Archives',
        'type_other' => 'Other',
        'recursive' => 'Recursive',
        'col_name' => 'Name',
        'col_size' => 'Size',
        'col_info' => 'Info',
        'col_modified' => 'Modified',
        'col_actions' => 'Actions',
        'parent_folder' => 'Parent folder',
        'folder_empty' => 'This folder is empty.',
        'view' => 'View',
        'download' => 'Download',
        'open' => 'Open',
        'zip' => 'Zip',
        'loading' => 'Loading…',
        'error_loading' => 'Error loading info',
        'no_info' => 'No info available',
        'config_error' => '⚠️ Configuration Error',
        'cookie_text' => 'This site uses essential cookies to save your preferences (theme, sort order, language). No tracking or third-party cookies.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Download',
        'viewer_newtab' => '↗ New Tab',
        'viewer_close' => 'Close (Esc)',
        'viewer_prev' => 'Previous',
        'viewer_next' => 'Next',
        'file_too_large' => 'File too large for text preview. Use "New Tab" or download.',
        'could_not_load' => 'Could not load file.',
        'queue_processing' => '{running} processing, {pending} queued',
        'queue_no_tasks' => 'No active tasks',
        'file_singular' => '{n} file',
        'file_plural' => '{n} files',
        'folder_singular' => '{n} folder',
        'folder_plural' => '{n} folders',
        'empty' => 'empty',
        'share' => 'Share',
        'share_copied' => 'Share link copied to clipboard!',
        'share_expires' => 'Expiration:',
        'share_never' => 'Never',
        'share_1h' => '1 hour',
        'share_1d' => '1 day',
        'share_7d' => '7 days',
        'share_30d' => '30 days',
        'background_tasks' => 'Background tasks',
        'info_format' => 'Format',
        'info_pages' => 'Pages',
        'info_title' => 'Title',
        'info_author' => 'Author',
        'info_language' => 'Language',
        'info_creator' => 'Creator',
        'info_producer' => 'Producer',
        'info_pdf_version' => 'PDF version',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Encrypted',
        'info_page_size' => 'Page size',
        'info_created' => 'Created',
        'info_resolution' => 'Resolution',
        'info_type' => 'Type',
        'info_vector' => 'Vector graphic',
        'info_megapixels' => 'Megapixels',
        'info_color' => 'Color',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Animated',
        'info_yes' => 'Yes',
        'info_duration' => 'Duration',
        'info_container' => 'Container',
        'info_overall_bitrate' => 'Overall bitrate',
        'info_video' => 'Video',
        'info_audio' => 'Audio',
        'info_bitrate' => 'Bitrate',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Bit depth',
        'info_sample_rate' => 'Sample rate',
        'info_channels' => 'Channels',
    ],
    'vi' => [
        '_flag' => '🇻🇳',
        '_name' => 'Tiếng Việt',
        '_dateFormat' => 'd/m/Y H:i',
        '_dateFormatJS' => 'DD/MM/YYYY HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => 'Trang chủ',
        'download_folder' => '⤓ Tải xuống thư mục',
        'toggle_theme' => 'Chuyển đổi chế độ sáng/tối',
        'search_placeholder' => 'Tìm kiếm theo tên…',
        'all_types' => 'Tất cả loại',
        'type_folder' => 'Thư mục',
        'type_image' => 'Hình ảnh',
        'type_audio' => 'Âm thanh',
        'type_video' => 'Video',
        'type_document' => 'Tài liệu',
        'type_archive' => 'Tập tin nén',
        'type_other' => 'Khác',
        'recursive' => 'Đệ quy',
        'col_name' => 'Tên',
        'col_size' => 'Kích thước',
        'col_info' => 'Thông tin',
        'col_modified' => 'Thay đổi',
        'col_actions' => 'Hành động',
        'parent_folder' => 'Thư mục cha',
        'folder_empty' => 'Thư mục này trống.',
        'view' => 'Xem',
        'download' => 'Tải xuống',
        'open' => 'Mở',
        'zip' => 'Nén',
        'loading' => 'Đang tải…',
        'error_loading' => 'Lỗi khi tải thông tin',
        'no_info' => 'Không có thông tin khả dụng',
        'config_error' => '⚠️ Lỗi cấu hình',
        'cookie_text' => 'Trang web này sử dụng cookie cần thiết để lưu trữ sở thích của bạn (chế độ, thứ tự sắp xếp, ngôn ngữ). Không theo dõi hoặc cookie bên thứ ba.',
        'cookie_ok' => 'OK',
        'viewer_download' => '⤓ Tải xuống',
        'viewer_newtab' => '↗ Tab mới',
        'viewer_close' => 'Đóng (Esc)',
        'viewer_prev' => 'Trước',
        'viewer_next' => 'Tiếp theo',
        'file_too_large' => 'Tập tin quá lớn để xem trước văn bản. Sử dụng "Tab mới" hoặc tải xuống.',
        'could_not_load' => 'Không thể tải tập tin.',
        'queue_processing' => '{running} đang xử lý, {pending} đã xếp hàng',
        'queue_no_tasks' => 'Không có tác vụ nào đang hoạt động',
        'file_singular' => '{n} tệp',
        'file_plural' => '{n} tệp',
        'folder_singular' => '{n} thư mục',
        'folder_plural' => '{n} thư mục',
        'empty' => 'trống',
        'share' => 'Chia sẻ',
        'share_copied' => 'Liên kết chia sẻ đã sao chép vào clipboard!',
        'share_expires' => 'Hết hạn:',
        'share_never' => 'Không bao giờ',
        'share_1h' => '1 giờ',
        'share_1d' => '1 ngày',
        'share_7d' => '7 ngày',
        'share_30d' => '30 ngày',
        'background_tasks' => 'Các tác vụ nền',
        'info_format' => 'Định dạng',
        'info_pages' => 'Trang',
        'info_title' => 'Tiêu đề',
        'info_author' => 'Tác giả',
        'info_language' => 'Ngôn ngữ',
        'info_creator' => 'Người tạo',
        'info_producer' => 'Nhà sản xuất',
        'info_pdf_version' => 'Phiên bản PDF',
        'info_mime' => 'MIME',
        'info_encrypted' => 'Được mã hóa',
        'info_page_size' => 'Kích thước trang',
        'info_created' => 'Tạo',
        'info_resolution' => 'Độ phân giải',
        'info_type' => 'Loại',
        'info_vector' => 'Hình ảnh vector',
        'info_megapixels' => 'Megapixel',
        'info_color' => 'Màu sắc',
        'info_alpha' => 'Alpha',
        'info_animated' => 'Hoạt hình',
        'info_yes' => 'Có',
        'info_duration' => 'Thời lượng',
        'info_container' => 'Chứa',
        'info_overall_bitrate' => 'Tốc độ bit tổng thể',
        'info_video' => 'Video',
        'info_audio' => 'Âm thanh',
        'info_bitrate' => 'Tốc độ bit',
        'info_fps' => 'FPS',
        'info_bit_depth' => 'Độ sâu bit',
        'info_sample_rate' => 'Tần số lấy mẫu',
        'info_channels' => 'Kênh',
    ],
    'zh_CN' => [
        '_flag' => '🇨🇳',
        '_name' => '简体中文',
        '_dateFormat' => 'Y-m-d H:i',
        '_dateFormatJS' => 'YYYY-MM-DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => '首页',
        'download_folder' => '⤓ 下载文件夹',
        'toggle_theme' => '切换浅色/深色模式',
        'search_placeholder' => '按名称搜索…',
        'all_types' => '所有类型',
        'type_folder' => '文件夹',
        'type_image' => '图片',
        'type_audio' => '音频',
        'type_video' => '视频',
        'type_document' => '文档',
        'type_archive' => '压缩包',
        'type_other' => '其他',
        'recursive' => '递归',
        'col_name' => '名称',
        'col_size' => '大小',
        'col_info' => '信息',
        'col_modified' => '修改时间',
        'col_actions' => '操作',
        'parent_folder' => '上一级文件夹',
        'folder_empty' => '此文件夹为空。',
        'view' => '查看',
        'download' => '下载',
        'open' => '打开',
        'zip' => '压缩',
        'loading' => '加载中…',
        'error_loading' => '无法加载信息',
        'no_info' => '无可用信息',
        'config_error' => '⚠️ 配置错误',
        'cookie_text' => '本网站使用必要 Cookie 保存您的偏好设置（主题、排序方式、语言）。不进行跟踪或第三方 Cookie。',
        'cookie_ok' => '确定',
        'viewer_download' => '⤓ 下载',
        'viewer_newtab' => '↗ 新标签页',
        'viewer_close' => '关闭 (Esc)',
        'viewer_prev' => '上一张',
        'viewer_next' => '下一张',
        'file_too_large' => '文件太大，无法预览文本。请使用“新标签页”或下载。',
        'could_not_load' => '无法加载文件。',
        'queue_processing' => '{running} 正在处理，{pending} 已排队',
        'queue_no_tasks' => '无活动任务',
        'file_singular' => '{n} 个文件',
        'file_plural' => '{n} 个文件',
        'folder_singular' => '{n} 个文件夹',
        'folder_plural' => '{n} 个文件夹',
        'empty' => '空的',
        'share' => '分享',
        'share_copied' => '分享链接已复制到剪贴板！',
        'share_expires' => '过期时间：',
        'share_never' => '从不',
        'share_1h' => '1 小时',
        'share_1d' => '1 天',
        'share_7d' => '7 天',
        'share_30d' => '30 天',
        'background_tasks' => '后台任务',
        'info_format' => '格式',
        'info_pages' => '页数',
        'info_title' => '标题',
        'info_author' => '作者',
        'info_language' => '语言',
        'info_creator' => '创建者',
        'info_producer' => '制作方',
        'info_pdf_version' => 'PDF 版本',
        'info_mime' => 'MIME',
        'info_encrypted' => '加密',
        'info_page_size' => '页面大小',
        'info_created' => '创建时间',
        'info_resolution' => '分辨率',
        'info_type' => '类型',
        'info_vector' => '矢量图形',
        'info_megapixels' => '百万像素',
        'info_color' => '颜色',
        'info_alpha' => '透明度',
        'info_animated' => '动画',
        'info_yes' => '是',
        'info_duration' => '时长',
        'info_container' => '容器',
        'info_overall_bitrate' => '总比特率',
        'info_video' => '视频',
        'info_audio' => '音频',
        'info_bitrate' => '比特率',
        'info_fps' => '帧率',
        'info_bit_depth' => '位深度',
        'info_sample_rate' => '采样率',
        'info_channels' => '声道',
    ],
    'zh_TW' => [
        '_flag' => '🇹🇼',
        '_name' => '繁體中文',
        '_dateFormat' => 'Y/m/d H:i',
        '_dateFormatJS' => 'YYYY/MM/DD HH:mm',
        'title' => 'PHP Autoindexed File-Viewer',
        'home' => '首頁',
        'download_folder' => '⤓ 下載資料夾',
        'toggle_theme' => '切換淺色/深色模式',
        'search_placeholder' => '依名稱搜尋…',
        'all_types' => '所有類型',
        'type_folder' => '資料夾',
        'type_image' => '圖片',
        'type_audio' => '音訊',
        'type_video' => '影片',
        'type_document' => '文件',
        'type_archive' => '壓縮檔',
        'type_other' => '其他',
        'recursive' => '遞歸',
        'col_name' => '名稱',
        'col_size' => '大小',
        'col_info' => '資訊',
        'col_modified' => '修改時間',
        'col_actions' => '操作',
        'parent_folder' => '上一層資料夾',
        'folder_empty' => '此資料夾為空。',
        'view' => '檢視',
        'download' => '下載',
        'open' => '開啟',
        'zip' => '壓縮成ZIP',
        'loading' => '載入中…',
        'error_loading' => '載入資訊時發生錯誤',
        'no_info' => '無可用資訊',
        'config_error' => '⚠️ 系統設定錯誤',
        'cookie_text' => '本網站使用必要 Cookie 來儲存您的偏好設定（主題、排序方式、語言）。不進行追蹤或第三方 Cookie。',
        'cookie_ok' => '確定',
        'viewer_download' => '⤓ 下載',
        'viewer_newtab' => '↗ 新分頁',
        'viewer_close' => '關閉 (Esc)',
        'viewer_prev' => '上一張',
        'viewer_next' => '下一張',
        'file_too_large' => '檔案太大無法預覽文字。請使用「新分頁」或下載。',
        'could_not_load' => '無法載入檔案。',
        'queue_processing' => '{running} 正在處理，{pending} 已排隊',
        'queue_no_tasks' => '無活動任務',
        'file_singular' => '{n} 個檔案',
        'file_plural' => '{n} 個檔案',
        'folder_singular' => '{n} 個資料夾',
        'folder_plural' => '{n} 個資料夾',
        'empty' => '空白',
        'share' => '分享',
        'share_copied' => '已複製分享連結到剪貼板！',
        'share_expires' => '過期時間：',
        'share_never' => '從不',
        'share_1h' => '1 小時',
        'share_1d' => '1 天',
        'share_7d' => '7 天',
        'share_30d' => '30 天',
        'background_tasks' => '背景任務',
        'info_format' => '格式',
        'info_pages' => '頁數',
        'info_title' => '標題',
        'info_author' => '作者',
        'info_language' => '語言',
        'info_creator' => '建立者',
        'info_producer' => '製造商',
        'info_pdf_version' => 'PDF 版本',
        'info_mime' => 'MIME',
        'info_encrypted' => '加密',
        'info_page_size' => '頁面大小',
        'info_created' => '建立時間',
        'info_resolution' => '解析度',
        'info_type' => '類型',
        'info_vector' => '向量圖形',
        'info_megapixels' => '百萬像素',
        'info_color' => '色彩',
        'info_alpha' => '透明通道',
        'info_animated' => '動畫',
        'info_yes' => '是',
        'info_duration' => '長度',
        'info_container' => '容器格式',
        'info_overall_bitrate' => '總體位元率',
        'info_video' => '視訊',
        'info_audio' => '音訊',
        'info_bitrate' => '位元率',
        'info_fps' => 'FPS',
        'info_bit_depth' => '位元深度',
        'info_sample_rate' => '取樣率',
        'info_channels' => '聲道',
    ],
];



// Determine active language: cookie > browser Accept-Language > 'en'
function detect_language() {
    global $I18N;
    // 1. Cookie override
    if (isset($_COOKIE['fb_lang']) && isset($I18N[$_COOKIE['fb_lang']])) {
        return $_COOKIE['fb_lang'];
    }
    // 2. Browser Accept-Language
    $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($accept) {
        // Parse e.g. "de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7"
        preg_match_all('/([a-zA-Z]{1,8}(?:-[a-zA-Z]{1,8})?)\s*(?:;q=([0-9.]+))?/', $accept, $matches, PREG_SET_ORDER);
        $langs = [];
        foreach ($matches as $m) {
            $code = strtolower($m[1]);
            $q = isset($m[2]) && $m[2] !== '' ? (float)$m[2] : 1.0;
            $langs[$code] = $q;
        }
        arsort($langs);
        foreach ($langs as $code => $q) {
            // Try exact match first (e.g. 'de-de'), then base language (e.g. 'de')
            if (isset($I18N[$code])) return $code;
            $base = explode('-', $code)[0];
            if (isset($I18N[$base])) return $base;
        }
    }
    return 'en';
}

// Get a translated string with fallback to English
function t($key) {
    global $I18N, $CURRENT_LANG;
    if (isset($I18N[$CURRENT_LANG][$key])) return $I18N[$CURRENT_LANG][$key];
    return $I18N['en'][$key] ?? $key;
}

$CURRENT_LANG = detect_language();

// --- CONFIG ---
$BASE_DIR = realpath(__DIR__ . '/files');
if ($BASE_DIR === false) {
    @mkdir(__DIR__ . '/files', 0755, true);
    $BASE_DIR = realpath(__DIR__ . '/files');
}
$MAX_FILES_IN_ZIP = 10000;

// Show hidden files/folders (names starting with ".") — false to hide them
$SHOW_HIDDEN_FILES = false;

// Whitelist: if non-empty, only show items matching any of these glob patterns (relative to BASE_DIR).
// E.g. ["data/*", "*.txt"] — only items under data/ or .txt files are shown.
$WHITELIST = [];

// Blacklist: hide items matching any of these glob patterns (applied after whitelist).
// E.g. ["*.bak", "tmp/*"]
$BLACKLIST = [];

// Maximum thumbnail dimension (width or height) in pixels
$THUMB_MAX_DIM = 400;

// Enable download/access logging (IP, file, timestamp stored in SQLite)
$ENABLE_LOGGING = false;

// --- Cache directory ---
$CACHE_DIR_OWNER="www-data"; // Set to your web server user (e.g. www-data, apache, nginx) or null to use current user
$CACHE_DIR = __DIR__ . '/.cache_fb';
$CACHE_ERROR = false;
if (!is_dir($CACHE_DIR)) {
    if (!@mkdir($CACHE_DIR, 0755, true)) {
        $CACHE_ERROR = 'Cache directory could not be created: ' . htmlspecialchars($CACHE_DIR);
    }
}
if (!$CACHE_ERROR && $CACHE_DIR_OWNER !== null) {
    $currentOwner = posix_getpwuid(fileowner($CACHE_DIR))['name'] ?? '';
    if ($currentOwner !== $CACHE_DIR_OWNER) {
        if (!@chown($CACHE_DIR, $CACHE_DIR_OWNER)) {
            $CACHE_ERROR = 'Cache directory ownership could not be changed to ' . htmlspecialchars($CACHE_DIR_OWNER) . '. Current owner is: ' . htmlspecialchars($currentOwner);
        }
    }
}
if (!$CACHE_ERROR && !is_writable($CACHE_DIR)) {
    $CACHE_ERROR = 'Cache directory is not writable: ' . htmlspecialchars($CACHE_DIR);
}

// --- Extension to icon/type mapping ---
function file_icon($name) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $map = [
        'jpg'=>'🖼️','jpeg'=>'🖼️','png'=>'🖼️','gif'=>'🖼️','bmp'=>'🖼️','svg'=>'🖼️','webp'=>'🖼️','ico'=>'🖼️','tiff'=>'🖼️','tif'=>'🖼️','avif'=>'🖼️','heic'=>'🖼️','heif'=>'🖼️',
        'mp3'=>'🎵','flac'=>'🎵','wav'=>'🎵','aac'=>'🎵','ogg'=>'🎵','wma'=>'🎵','m4a'=>'🎵','opus'=>'🎵','aiff'=>'🎵',
        'mp4'=>'🎬','mkv'=>'🎬','avi'=>'🎬','mov'=>'🎬','wmv'=>'🎬','flv'=>'🎬','webm'=>'🎬','m4v'=>'🎬','ts'=>'🎬','m2ts'=>'🎬','mpg'=>'🎬','mpeg'=>'🎬',
        'zip'=>'📦','tar'=>'📦','gz'=>'📦','bz2'=>'📦','xz'=>'📦','7z'=>'📦','rar'=>'📦','zst'=>'📦',
        'pdf'=>'📕','doc'=>'📝','docx'=>'📝','odt'=>'📝','rtf'=>'📝','txt'=>'📄','md'=>'📄','csv'=>'📄','log'=>'📄',
        'xls'=>'📊','xlsx'=>'📊','ods'=>'📊',
        'ppt'=>'��️','pptx'=>'📽️','odp'=>'📽️',
        'py'=>'💻','js'=>'💻','php'=>'💻','html'=>'💻','css'=>'💻','json'=>'💻','xml'=>'💻','sh'=>'💻','c'=>'💻','cpp'=>'💻','h'=>'💻','java'=>'💻','rs'=>'💻','go'=>'💻','rb'=>'💻','ts'=>'💻',
        'iso'=>'💿','img'=>'💿','dmg'=>'💿',
        'exe'=>'⚙️','msi'=>'⚙️','deb'=>'⚙️','rpm'=>'⚙️','AppImage'=>'⚙️',
    ];
    return $map[$ext] ?? '📄';
}

function file_category($name) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $cats = [
        'image' => ['jpg','jpeg','png','gif','bmp','svg','webp','ico','tiff','tif','avif','heic','heif','psd','raw','cr2','nef','arw','dng'],
        'audio' => ['mp3','flac','wav','aac','ogg','wma','m4a','opus','aiff','ape','alac','mid','midi'],
        'video' => ['mp4','mkv','avi','mov','wmv','flv','webm','m4v','ts','m2ts','mpg','mpeg','3gp','ogv','vob','asf','rm','rmvb','divx'],
        'document' => ['pdf','doc','docx','odt','rtf','ppt','pptx','odp','xls','xlsx','ods','epub'],
        'archive' => ['zip','tar','gz','bz2','xz','7z','rar','zst','tgz','cab','lz','lzma'],
    ];
    foreach ($cats as $cat => $exts) {
        if (in_array($ext, $exts)) return $cat;
    }
    return 'other';
}

// Determine how a file can be viewed in-browser: 'modal', 'tab', or false
function file_view_mode($name) {
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    // Modal: formats browsers can natively play/display
    $modal = [
        // Video (browser-native)
        'mp4','webm','ogv','mov',
        // Audio (browser-native)
        'mp3','ogg','wav','flac','m4a','opus','aac',
        // Images (browser-native)
        'jpg','jpeg','png','gif','bmp','svg','webp','ico','avif',
        // Text/code
        'txt','md','log','csv','json','xml','html','css','js','py','php','sh','c','cpp','h','java','rs','go','rb','ts',
    ];
    // New tab: formats that work better in their own tab (PDF, etc.)
    $tab = ['pdf'];
    if (in_array($ext, $modal)) return 'modal';
    if (in_array($ext, $tab)) return 'tab';
    return false;
}

// --- Helpers ---
function resolve_requested_path($requested) {
    global $BASE_DIR;
    $requested = $requested ?? '';
    $requested = str_replace(['\\', "\0"], ['/', ''], $requested);
    $requested = preg_replace('#^/+|/+$#', '', $requested);
    // Block path traversal via ".." segments in the logical path
    $parts = explode('/', $requested);
    $safe = [];
    foreach ($parts as $p) {
        if ($p === '' || $p === '.') continue;
        if ($p === '..') { if (count($safe)) array_pop($safe); else return [false, false]; }
        else $safe[] = $p;
    }
    $logicalRel = $safe ? implode('/', $safe) : '';
    $logical = $BASE_DIR . ($safe ? '/' . $logicalRel : '');
    $resolved = realpath($logical);
    if ($resolved !== false && file_exists($resolved)) return [$logicalRel, $resolved];
    return [false, false];
}

function format_size($bytes) {
    if ($bytes < 0) return '?';
    if ($bytes < 1024) return $bytes . ' B';
    $units = ['KB','MB','GB','TB','PB'];
    $i = -1;
    do { $bytes /= 1024; $i++; } while ($bytes >= 1024 && $i < count($units)-1);
    return round($bytes, 2) . ' ' . $units[$i];
}

function safe_basename($path) {
    return htmlspecialchars(basename($path), ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
}

function u($params) {
    global $SHARE_QUERY;
    return '?' . http_build_query(array_merge($SHARE_QUERY, $params));
}

function list_directory($path) {
    global $BASE_DIR;
    $items = [];
    if (!is_dir($path)) return $items;
    $dh = opendir($path);
    if (!$dh) return $items;
    while (($entry = readdir($dh)) !== false) {
        if ($entry === '.' || $entry === '..') continue;
        if (!should_show_entry($entry, $path . DIRECTORY_SEPARATOR . $entry)) continue;
        $full = $path . DIRECTORY_SEPARATOR . $entry;
        $isDir = is_dir($full);
        $size = $isDir ? -1 : (is_file($full) ? filesize($full) : 0);
        $mtime = filemtime($full) ?: 0;
        $items[] = [
            'name' => $entry, 'full' => $full,
            'is_dir' => $isDir, 'size' => $size, 'mtime' => $mtime,
        ];
    }
    closedir($dh);
    return $items;
}

// Determine if a file/folder should be shown, based on hidden/whitelist/blacklist config.
// Directories are shown if they directly match OR contain any visible descendants.
function should_show_entry($name, $absPath) {
    global $SHOW_HIDDEN_FILES, $WHITELIST, $BLACKLIST, $BASE_DIR;

    // Hidden files
    if (!$SHOW_HIDDEN_FILES && $name !== '' && $name[0] === '.') return false;

    // Compute relative path from BASE_DIR for glob matching
    $rel = ltrim(str_replace('\\', '/', substr($absPath, strlen($BASE_DIR))), '/');
    $isDir = is_dir($absPath);
    if ($isDir) $rel .= '/';

    // Blacklist: if item matches any pattern, hide it (check before whitelist/recurse)
    if (!empty($BLACKLIST)) {
        foreach ($BLACKLIST as $pattern) {
            if (_glob_match($pattern, $rel, $name)) return false;
        }
    }

    // Whitelist: if set, item must match at least one pattern
    if (!empty($WHITELIST)) {
        $matched = false;
        foreach ($WHITELIST as $pattern) {
            if (_glob_match($pattern, $rel, $name)) { $matched = true; break; }
        }
        if (!$matched) {
            // For directories: show if any descendant is visible
            if ($isDir) {
                return _dir_has_visible_descendant($absPath);
            }
            return false;
        }
    }

    return true;
}

// Recursively check if a directory contains at least one visible entry.
// Uses a short-circuit scan — returns true as soon as any visible child is found.
function _dir_has_visible_descendant($dirPath) {
    global $SHOW_HIDDEN_FILES, $WHITELIST, $BLACKLIST, $BASE_DIR;
    static $cache = [];
    $realDir = realpath($dirPath);
    if ($realDir === false) return false;
    if (isset($cache[$realDir])) return $cache[$realDir];

    // Prevent infinite recursion on symlink loops
    $cache[$realDir] = false;

    $dh = @opendir($dirPath);
    if (!$dh) return false;
    while (($entry = readdir($dh)) !== false) {
        if ($entry === '.' || $entry === '..') continue;
        if (!$SHOW_HIDDEN_FILES && $entry[0] === '.') continue;
        $child = $dirPath . DIRECTORY_SEPARATOR . $entry;
        $rel = ltrim(str_replace('\\', '/', substr($child, strlen($BASE_DIR))), '/');
        $childIsDir = is_dir($child);
        if ($childIsDir) $rel .= '/';

        // Check blacklist first
        $blacklisted = false;
        if (!empty($BLACKLIST)) {
            foreach ($BLACKLIST as $pattern) {
                if (_glob_match($pattern, $rel, $entry)) { $blacklisted = true; break; }
            }
        }
        if ($blacklisted) continue;

        // Check whitelist
        if (!empty($WHITELIST)) {
            $matched = false;
            foreach ($WHITELIST as $pattern) {
                if (_glob_match($pattern, $rel, $entry)) { $matched = true; break; }
            }
            if ($matched) {
                // Found a visible child — this directory should be shown
                closedir($dh);
                $cache[$realDir] = true;
                return true;
            }
            // Not matched: if it's a dir, recurse into it
            if ($childIsDir && _dir_has_visible_descendant($child)) {
                closedir($dh);
                $cache[$realDir] = true;
                return true;
            }
        } else {
            // No whitelist — any non-blacklisted child is visible
            closedir($dh);
            $cache[$realDir] = true;
            return true;
        }
    }
    closedir($dh);
    return false;
}

// Match a simple glob pattern against a relative path or basename.
// Supports: * (any non-/ chars), ** (any path), ? (single char).
// Patterns like "data/*" match anything under data/. Patterns like "*.txt" match by name.
function _glob_match($pattern, $relPath, $basename) {
    $pattern = rtrim($pattern, '/');
    $relClean = rtrim($relPath, '/');

    // If pattern has no '/', match against basename only
    if (strpos($pattern, '/') === false) {
        return fnmatch($pattern, $basename);
    }

    // Pattern has '/' — match against full relative path
    // Also allow prefix match for directories (so "data" matches "data/foo.txt")
    if (fnmatch($pattern, $relClean)) return true;
    if (fnmatch($pattern . '/*', $relClean)) return true;
    // For directories, also check if path starts with pattern
    if (fnmatch($pattern, dirname($relClean)) || fnmatch($pattern . '/*', dirname($relClean))) return true;

    return false;
}

function build_breadcrumbs($relPath) {
    $rel = trim(str_replace('\\','/',$relPath), '/');
    $parts = $rel === '' ? [] : explode('/', $rel);
    $crumbs = [['name' => t('home'), 'path' => '']];
    $acc = '';
    foreach ($parts as $p) {
        $acc = $acc === '' ? $p : ($acc . '/' . $p);
        $crumbs[] = ['name' => $p, 'path' => $acc];
    }
    return $crumbs;
}

function send_file_response($filePath, $downloadName = null) {
    if (!is_file($filePath) || !is_readable($filePath)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo "File not found."; exit;
    }
    if ($downloadName === null) $downloadName = basename($filePath);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
    finfo_close($finfo);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private');
    while (ob_get_level()) ob_end_flush();
    readfile($filePath);
    exit;
}

// --- Cache helpers (sqlite) ---
function get_cache_db() {
    global $CACHE_DIR;
    $dbPath = $CACHE_DIR . '/cache.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(2000);
    $db->exec('CREATE TABLE IF NOT EXISTS cache (key TEXT PRIMARY KEY, value TEXT, mtime INTEGER)');
    return $db;
}

function cache_get($key, $currentMtime) {
    $db = get_cache_db();
    $stmt = $db->prepare('SELECT value, mtime FROM cache WHERE key = :k');
    $stmt->bindValue(':k', $key, SQLITE3_TEXT);
    $r = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    $db->close();
    if ($r && (int)$r['mtime'] >= $currentMtime) return json_decode($r['value'], true);
    return null;
}

function cache_set($key, $value, $mtime) {
    $db = get_cache_db();
    $stmt = $db->prepare('INSERT OR REPLACE INTO cache (key, value, mtime) VALUES (:k, :v, :m)');
    $stmt->bindValue(':k', $key, SQLITE3_TEXT);
    $stmt->bindValue(':v', json_encode($value), SQLITE3_TEXT);
    $stmt->bindValue(':m', $mtime, SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();
}

// --- Folder size computation ---
function compute_dir_size($path) {
    $size = 0;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($it as $f) {
        if ($f->isFile()) $size += $f->getSize();
    }
    return $size;
}

// --- Mediainfo ---
function get_mediainfo($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'mi:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $cmd = 'mediainfo --Output=JSON ' . escapeshellarg($filePath) . ' 2>/dev/null';
    $json = shell_exec($cmd);
    $data = json_decode($json, true);
    if (!$data) return null;

    $result = ['streams' => []];
    $tracks = $data['media']['track'] ?? [];
    foreach ($tracks as $t) {
        $type = $t['@type'] ?? '';
        if ($type === 'General') {
            $dur = $t['Duration'] ?? null;
            if ($dur !== null) $result['duration'] = (float)$dur;
            $result['format'] = $t['Format'] ?? '';
            $result['overall_bitrate'] = $t['OverallBitRate'] ?? null;
        } else if ($type === 'Video') {
            $s = ['type'=>'video','format'=>$t['Format']??'','width'=>(int)($t['Width']??0),'height'=>(int)($t['Height']??0)];
            $s['bitrate'] = $t['BitRate'] ?? $t['BitRate_Nominal'] ?? null;
            $s['framerate'] = $t['FrameRate'] ?? null;
            $s['color_space'] = ($t['ColorSpace'] ?? '') . (isset($t['ChromaSubsampling']) ? ' '.$t['ChromaSubsampling'] : '');
            $s['bit_depth'] = $t['BitDepth'] ?? null;
            $s['language'] = $t['Language'] ?? null;
            $result['streams'][] = $s;
        } else if ($type === 'Audio') {
            $s = ['type'=>'audio','format'=>$t['Format']??'','channels'=>$t['Channels']??'','sample_rate'=>$t['SamplingRate']??''];
            $s['bitrate'] = $t['BitRate'] ?? $t['BitRate_Nominal'] ?? null;
            $s['language'] = $t['Language'] ?? null;
            $s['bit_depth'] = $t['BitDepth'] ?? null;
            $result['streams'][] = $s;
        } else if ($type === 'Text') {
            $s = ['type'=>'subtitle','format'=>$t['Format']??'','language'=>$t['Language']??null];
            $result['streams'][] = $s;
        }
    }
    cache_set($cacheKey, $result, $mtime);
    return $result;
}

function get_image_info($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'img:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $result = [];
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // SVG: parse dimensions from XML — vector format, no megapixels
    if ($ext === 'svg') {
        $result['format'] = 'SVG';
        $result['vector'] = true;
        $xml = @simplexml_load_file($filePath);
        if ($xml) {
            $w = (float)($xml['width'] ?? 0);
            $h = (float)($xml['height'] ?? 0);
            if (!$w && !$h && isset($xml['viewBox'])) {
                $vb = preg_split('/[\s,]+/', (string)$xml['viewBox']);
                if (count($vb) >= 4) { $w = (float)$vb[2]; $h = (float)$vb[3]; }
            }
            if ($w > 0 && $h > 0) {
                $result['width'] = (int)$w;
                $result['height'] = (int)$h;
            }
        }
        $result['has_alpha'] = true;
        $result['animated'] = false;
        cache_set($cacheKey, $result, $mtime);
        return $result;
    }

    $info = @getimagesize($filePath);
    if ($info) {
        $result['width'] = $info[0];
        $result['height'] = $info[1];
        $result['megapixels'] = round(($info[0] * $info[1]) / 1e6, 2);
        $typeConst = $info[2];
        $typeNames = [1=>'GIF',2=>'JPEG',3=>'PNG',4=>'SWF',5=>'PSD',6=>'BMP',9=>'JPC',13=>'SWC',14=>'IFF',15=>'WBMP',16=>'XBM',17=>'ICO',18=>'WEBP',19=>'AVIF'];
        $result['format'] = $typeNames[$typeConst] ?? image_type_to_mime_type($typeConst);
        $channels = $info['channels'] ?? null;
        $result['channels'] = $channels;
        $result['color_mode'] = $channels === 3 ? 'RGB' : ($channels === 4 ? 'RGBA/CMYK' : ($channels === 1 ? 'Grayscale' : null));
        $result['bits'] = $info['bits'] ?? null;
        $result['has_alpha'] = in_array($typeConst, [1,3,18,19]);
        $result['animated'] = false;
        if ($typeConst === 1) { $result['animated'] = _gif_is_animated($filePath); }
        else if ($typeConst === 3) { $result['animated'] = _png_is_animated($filePath); }
        else if ($typeConst === 18) { $result['animated'] = _webp_is_animated($filePath); }
    } else {
        // GD can't read it — try ImageMagick identify
        $cmd = 'identify -format "%w %h %m" ' . escapeshellarg($filePath . '[0]') . ' 2>/dev/null';
        $out = trim(shell_exec($cmd) ?? '');
        if (preg_match('/^(\d+)\s+(\d+)\s+(\S+)/', $out, $m)) {
            $result['width'] = (int)$m[1];
            $result['height'] = (int)$m[2];
            $result['megapixels'] = round(((int)$m[1] * (int)$m[2]) / 1e6, 2);
            $result['format'] = $m[3]; // e.g. TIFF, ICO, HEIC
        }
        $result['has_alpha'] = in_array($ext, ['ico','tiff','tif','heic','heif','psd']);
        $result['animated'] = false;
    }
    cache_set($cacheKey, $result, $mtime);
    return $result;
}

function _gif_is_animated($path) {
    $fh = fopen($path, 'rb');
    if (!$fh) return false;
    $count = 0;
    while (!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024*100);
        $count += substr_count($chunk, "\x00\x21\xF9\x04");
    }
    fclose($fh);
    return $count > 1;
}

function _png_is_animated($path) {
    $fh = fopen($path, 'rb');
    if (!$fh) return false;
    $data = fread($fh, min(filesize($path), 1024*512));
    fclose($fh);
    return strpos($data, 'acTL') !== false && strpos($data, 'fcTL') !== false;
}

function _webp_is_animated($path) {
    $fh = fopen($path, 'rb');
    if (!$fh) return false;
    $data = fread($fh, 64);
    fclose($fh);
    return strpos($data, 'ANIM') !== false;
}

function format_duration($seconds) {
    if (!$seconds || $seconds <= 0) return '';
    $s = (int)$seconds;
    $h = intdiv($s, 3600);
    $m = intdiv($s % 3600, 60);
    $sec = $s % 60;
    if ($h > 0) return sprintf('%d:%02d:%02d h', $h, $m, $sec);
    return sprintf('%d:%02d min', $m, $sec);
}

function format_bitrate($bps) {
    if (!$bps) return '';
    $kbps = (int)$bps / 1000;
    if ($kbps >= 1000) return round($kbps/1000, 1) . ' Mbps';
    return round($kbps) . ' kbps';
}

// --- Streaming ZIP ---
class StreamingZip {
    private $out;
    private $entries = [];
    private $centralDir = '';
    private $offset = 0;

    public function open() { $this->out = fopen('php://output', 'wb'); }

    public function addFileFromPath($localName, $filePath) {
        $size = filesize($filePath);
        $crc = hexdec(hash_file('crc32b', $filePath));
        $localName = str_replace('\\', '/', $localName);
        $nameLen = strlen($localName);
        $mtime = filemtime($filePath) ?: time();
        $dosTime = $this->toDosTime($mtime);
        $dosDate = $this->toDosDate($mtime);
        $useZip64 = ($size >= 0xFFFFFFFF);

        $h = "PK\x03\x04";
        $h .= pack('v', $useZip64 ? 45 : 20);
        $h .= pack('v', 0) . pack('v', 0);
        $h .= pack('v', $dosTime) . pack('v', $dosDate);
        $h .= pack('V', $crc);
        $h .= $useZip64 ? pack('V', 0xFFFFFFFF).pack('V', 0xFFFFFFFF) : pack('V', $size).pack('V', $size);
        $h .= pack('v', $nameLen);
        $extra = $useZip64 ? pack('v',0x0001).pack('v',16).$this->pack64($size).$this->pack64($size) : '';
        $h .= pack('v', strlen($extra)) . $localName . $extra;

        $headerOffset = $this->offset;
        fwrite($this->out, $h);
        $this->offset += strlen($h);

        $fh = fopen($filePath, 'rb');
        while (!feof($fh)) {
            $chunk = fread($fh, 1048576);
            fwrite($this->out, $chunk);
            $this->offset += strlen($chunk);
            flush();
        }
        fclose($fh);

        $c = "PK\x01\x02";
        $c .= pack('v', $useZip64?45:20) . pack('v', $useZip64?45:20);
        $c .= pack('v',0).pack('v',0);
        $c .= pack('v',$dosTime).pack('v',$dosDate);
        $c .= pack('V',$crc);
        $c .= $useZip64 ? pack('V',0xFFFFFFFF).pack('V',0xFFFFFFFF) : pack('V',$size).pack('V',$size);
        $c .= pack('v',$nameLen);
        $cdExtra = ($useZip64||$headerOffset>=0xFFFFFFFF) ? pack('v',0x0001).pack('v',24).$this->pack64($size).$this->pack64($size).$this->pack64($headerOffset) : '';
        $c .= pack('v',strlen($cdExtra)).pack('v',0).pack('v',0).pack('v',0).pack('V',0);
        $c .= $headerOffset>=0xFFFFFFFF ? pack('V',0xFFFFFFFF) : pack('V',$headerOffset);
        $c .= $localName . $cdExtra;

        $this->centralDir .= $c;
        $this->entries[] = true;
    }

    public function close() {
        $cdOff = $this->offset;
        fwrite($this->out, $this->centralDir);
        $cdSz = strlen($this->centralDir);
        $this->offset += $cdSz;
        $n = count($this->entries);
        if ($cdOff>=0xFFFFFFFF||$cdSz>=0xFFFFFFFF||$n>=0xFFFF) {
            $z="PK\x06\x06".$this->pack64(44).pack('v',45).pack('v',45).pack('V',0).pack('V',0).$this->pack64($n).$this->pack64($n).$this->pack64($cdSz).$this->pack64($cdOff);
            fwrite($this->out,$z); $this->offset+=strlen($z);
            $l="PK\x06\x07".pack('V',0).$this->pack64($cdOff+$cdSz).pack('V',1);
            fwrite($this->out,$l); $this->offset+=strlen($l);
        }
        $e="PK\x05\x06".pack('v',0).pack('v',0).pack('v',min($n,0xFFFF)).pack('v',min($n,0xFFFF)).pack('V',min($cdSz,0xFFFFFFFF)).pack('V',min($cdOff,0xFFFFFFFF)).pack('v',0);
        fwrite($this->out,$e);
        fflush($this->out); fclose($this->out);
    }

    private function toDosTime($ts){$h=(int)date('H',$ts);$m=(int)date('i',$ts);$s=(int)date('s',$ts);return($h<<11)|($m<<5)|($s>>1);}
    private function toDosDate($ts){$y=(int)date('Y',$ts)-1980;$mo=(int)date('n',$ts);$d=(int)date('j',$ts);return($y<<9)|($mo<<5)|$d;}
    private function pack64($v){return pack('V',$v&0xFFFFFFFF).pack('V',($v>>32)&0xFFFFFFFF);}
}

// ========== TASK QUEUE ==========
// SQLite-based task stack: LIFO priority, dedup, cross-session coordination
define('TASK_MAX_WORKERS', 3);       // max concurrent tasks across all sessions
define('TASK_STALE_SECONDS', 60);    // reclaim stuck tasks after this
define('TASK_BATCH_SIZE', 5);        // tasks processed per api_work call

function get_task_db() {
    global $CACHE_DIR;
    $dbPath = $CACHE_DIR . '/tasks.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(3000);
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('PRAGMA synchronous=NORMAL');
    $db->exec('CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        type TEXT NOT NULL,
        path TEXT NOT NULL,
        priority REAL NOT NULL,
        status TEXT NOT NULL DEFAULT "pending",
        claimed_at REAL,
        result TEXT,
        created_at REAL NOT NULL,
        UNIQUE(type, path)
    )');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status, priority DESC)');
    return $db;
}

function task_enqueue($db, $tasks) {
    // $tasks = [[type, path, priority], ...]
    // Insert or bump priority if already pending. Ignore if done/running.
    $now = microtime(true);
    $stmt = $db->prepare('INSERT INTO tasks (type, path, priority, status, created_at)
        VALUES (:t, :p, :pri, "pending", :now)
        ON CONFLICT(type, path) DO UPDATE SET
            priority = MAX(priority, :pri)
        WHERE status = "pending"');
    foreach ($tasks as $t) {
        $stmt->bindValue(':t', $t[0], SQLITE3_TEXT);
        $stmt->bindValue(':p', $t[1], SQLITE3_TEXT);
        $stmt->bindValue(':pri', $t[2] ?? $now, SQLITE3_FLOAT);
        $stmt->bindValue(':now', $now, SQLITE3_FLOAT);
        $stmt->execute();
        $stmt->reset();
    }
}

function task_reclaim_stale($db) {
    $cutoff = microtime(true) - TASK_STALE_SECONDS;
    $db->exec("UPDATE tasks SET status='pending', claimed_at=NULL WHERE status='running' AND claimed_at < $cutoff");
}

function task_claim($db, $limit) {
    // Count current running tasks — respect concurrency limit
    $now = microtime(true);
    $running = $db->querySingle("SELECT COUNT(*) FROM tasks WHERE status='running'");
    $canClaim = max(0, TASK_MAX_WORKERS - (int)$running);
    if ($canClaim <= 0) return [];
    $limit = min($limit, $canClaim);

    // Claim highest-priority pending tasks (LIFO — highest priority number first)
    $claimed = [];
    $stmt = $db->prepare("SELECT id, type, path FROM tasks WHERE status='pending' ORDER BY priority DESC LIMIT :lim");
    $stmt->bindValue(':lim', $limit, SQLITE3_INTEGER);
    $res = $stmt->execute();
    $ids = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $ids[] = $row['id'];
        $claimed[] = $row;
    }
    if ($ids) {
        $idList = implode(',', $ids);
        $db->exec("UPDATE tasks SET status='running', claimed_at=$now WHERE id IN ($idList)");
    }
    return $claimed;
}

function task_complete($db, $id, $result) {
    $stmt = $db->prepare("UPDATE tasks SET status='done', result=:r WHERE id=:id");
    $stmt->bindValue(':r', json_encode($result), SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
}

function task_fail($db, $id) {
    $db->exec("UPDATE tasks SET status='error' WHERE id=$id");
}

function task_get_status($db) {
    $pending = (int)$db->querySingle("SELECT COUNT(*) FROM tasks WHERE status='pending'");
    $running = (int)$db->querySingle("SELECT COUNT(*) FROM tasks WHERE status='running'");
    return ['pending' => $pending, 'running' => $running, 'total' => $pending + $running];
}

function task_get_results($db, $types, $paths) {
    // Get completed results for given type+path pairs
    if (empty($paths)) return [];
    $results = [];
    $stmt = $db->prepare("SELECT type, path, result, status FROM tasks WHERE type=:t AND path=:p");
    for ($i = 0; $i < count($paths); $i++) {
        $stmt->bindValue(':t', $types[$i], SQLITE3_TEXT);
        $stmt->bindValue(':p', $paths[$i], SQLITE3_TEXT);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        if ($row) {
            $results[] = [
                'type' => $row['type'],
                'path' => $row['path'],
                'status' => $row['status'],
                'result' => $row['status'] === 'done' ? json_decode($row['result'], true) : null,
            ];
        }
        $stmt->reset();
    }
    return $results;
}

function task_cleanup_done($db, $maxAge = 300) {
    $cutoff = microtime(true) - $maxAge;
    $db->exec("DELETE FROM tasks WHERE status IN ('done','error') AND created_at < $cutoff");
}

// --- Execute a single task and return its result ---
function task_execute($type, $path) {
    list(, $abs) = resolve_requested_path($path);
    if (!$abs) return null;

    switch ($type) {
        case 'dirsize':
            if (!is_dir($abs)) return null;
            $mtime = filemtime($abs);
            $cacheKey = 'ds:' . $abs;
            $cached = cache_get($cacheKey, $mtime);
            if ($cached !== null) return $cached;
            $size = compute_dir_size($abs);
            // Count direct children (files + dirs)
            $children = @scandir($abs);
            $files = 0; $dirs = 0;
            if ($children) {
                foreach ($children as $c) {
                    if ($c === '.' || $c === '..') continue;
                    if (is_dir($abs . '/' . $c)) $dirs++; else $files++;
                }
            }
            $result = ['size' => $size, 'formatted' => format_size($size), 'files' => $files, 'dirs' => $dirs];
            cache_set($cacheKey, $result, $mtime);
            return $result;

        case 'info':
            if (!is_file($abs)) return null;
            $cat = file_category(basename($abs));
            $result = ['category' => $cat];
            // Sort hierarchy (descending): folders(50000+) > video/audio(1000+) > document(500+) > image(1+) > other(0)
            if ($cat === 'image') {
                $info = get_image_info($abs);
                $result['info'] = $info;
                if (!empty($info['vector'])) {
                    if (isset($info['width'], $info['height'])) {
                        $result['summary'] = $info['width'] . '×' . $info['height'];
                    } else {
                        $result['summary'] = 'Vector';
                    }
                    $result['sort_value'] = 1;
                } else {
                    if (isset($info['megapixels'])) $result['summary'] = $info['megapixels'] . ' MP';
                    $result['sort_value'] = 1 + ($info['megapixels'] ?? 0);
                }
            } else if ($cat === 'audio' || $cat === 'video') {
                $info = get_mediainfo($abs);
                $result['info'] = $info;
                if (isset($info['duration'])) {
                    $result['summary'] = format_duration($info['duration']);
                    $result['sort_value'] = 1000 + (float)$info['duration'];
                } else {
                    $result['sort_value'] = 1000;
                }
            } else if ($cat === 'document') {
                $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
                if ($docExt === 'pdf') {
                    $info = get_pdf_info($abs);
                } else {
                    $info = get_document_info($abs);
                }
                $result['info'] = $info;
                if (isset($info['pages'])) {
                    $result['summary'] = $info['pages'] . ' pg';
                    $result['sort_value'] = 500 + (float)$info['pages'];
                } else if (isset($info['format'])) {
                    $result['summary'] = $info['format'];
                    $result['sort_value'] = 500;
                }
            }
            return $result;

        case 'thumb':
            if (!is_file($abs)) return null;
            $cat = file_category(basename($abs));
            if (!in_array($cat, ['image','video','document'])) return null;
            $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
            global $CACHE_DIR;
            $mtime = filemtime($abs);
            $thumbKey = md5($abs) . '_' . $mtime;
            $thumbPath = $CACHE_DIR . '/thumb_' . $thumbKey . '.jpg';
            if (!is_file($thumbPath)) {
                if ($cat === 'image') $thumbPath = generate_image_thumbnail($abs, $thumbPath);
                else if ($cat === 'video') $thumbPath = generate_video_thumbnail($abs, $thumbPath);
                else if ($cat === 'document' && $docExt === 'pdf') $thumbPath = generate_pdf_thumbnail($abs, $thumbPath);
                else $thumbPath = null; // Non-PDF documents: no thumbnail without LibreOffice
            }
            // Result is just a flag — client fetches the actual image via api_thumb
            return ($thumbPath && is_file($thumbPath)) ? ['ready' => true] : null;

        default:
            return null;
    }
}

// ========== API ENDPOINTS ==========
$action = $_GET['action'] ?? '';
$requested = $_GET['path'] ?? '';

// --- Handle Share Token ---
$SHARE_TOKEN = $_GET['s'] ?? '';
$SHARE_QUERY = [];
if ($SHARE_TOKEN) {
    $shareDb = get_share_db();
    $stmt = $shareDb->prepare('SELECT path, is_dir, expires_at FROM shares WHERE token = :t');
    $stmt->bindValue(':t', $SHARE_TOKEN, SQLITE3_TEXT);
    $share = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    $shareDb->close();

    if ($share) {
        if ($share['expires_at'] && $share['expires_at'] < time()) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
            die("Share link has expired.");
        }
        $SHARE_QUERY = ['s' => $SHARE_TOKEN];
        $absShared = realpath(__DIR__ . '/files/' . $share['path']);
        if ($absShared && file_exists($absShared)) {
            if ($share['is_dir']) {
                $BASE_DIR = $absShared;
            } else {
                // Shared a single file
                if ($requested !== '' && $requested !== basename($absShared)) {
                    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    die("Access denied.");
                }
                $BASE_DIR = dirname($absShared);
                $requested = basename($absShared);
                
                // If it's a file share and no specific action is requested, serve it immediately
                if ($_GET['path'] === '' && $action === '') {
                    log_download($share['path']);
                    send_file_response($absShared);
                }
            }
        } else {
            die("Shared path no longer exists.");
        }
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
        die("Invalid or expired share link.");
    }
}

// --- API: enqueue tasks (POST) ---
if ($action === 'api_enqueue') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['tasks']) || !is_array($input['tasks'])) {
        echo json_encode(['error' => 'invalid']); exit;
    }
    $db = get_task_db();
    task_cleanup_done($db);
    $toEnqueue = [];
    $basePriority = microtime(true);
    foreach ($input['tasks'] as $i => $t) {
        if (!isset($t['type'], $t['path'])) continue;
        // Lower index = lower priority (LIFO: last enqueued items get processed first)
        $toEnqueue[] = [$t['type'], $t['path'], $basePriority + $i * 0.0001];
    }
    task_enqueue($db, $toEnqueue);
    $status = task_get_status($db);
    $db->close();
    echo json_encode(['ok' => true, 'status' => $status]);
    exit;
}

// --- API: worker — claim and process tasks, return results ---
if ($action === 'api_work') {
    header('Content-Type: application/json');
    $db = get_task_db();
    task_reclaim_stale($db);
    $claimed = task_claim($db, TASK_BATCH_SIZE);
    $completed = [];
    foreach ($claimed as $task) {
        $result = task_execute($task['type'], $task['path']);
        if ($result !== null) {
            task_complete($db, $task['id'], $result);
            $completed[] = ['type' => $task['type'], 'path' => $task['path'], 'result' => $result];
        } else {
            task_fail($db, $task['id']);
            $completed[] = ['type' => $task['type'], 'path' => $task['path'], 'result' => null, 'error' => true];
        }
    }
    $status = task_get_status($db);
    $db->close();
    echo json_encode(['completed' => $completed, 'status' => $status]);
    exit;
}

// --- API: poll status + collect finished results ---
if ($action === 'api_poll') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $db = get_task_db();
    $status = task_get_status($db);
    $results = [];
    // Client sends list of tasks it's waiting for
    if ($input && isset($input['waiting']) && is_array($input['waiting'])) {
        $types = []; $paths = [];
        foreach ($input['waiting'] as $w) {
            $types[] = $w['type'] ?? '';
            $paths[] = $w['path'] ?? '';
        }
        $results = task_get_results($db, $types, $paths);
    }
    $db->close();
    echo json_encode(['status' => $status, 'results' => $results]);
    exit;
}

// --- API: queue detail (list of current tasks for hover popup) ---
if ($action === 'api_queue_detail') {
    header('Content-Type: application/json');
    $db = get_task_db();
    $status = task_get_status($db);
    $tasks = [];
    $res = $db->query("SELECT type, path, status FROM tasks WHERE status IN ('running','pending') ORDER BY CASE status WHEN 'running' THEN 0 ELSE 1 END, priority DESC LIMIT 20");
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $tasks[] = ['type' => $row['type'], 'path' => basename($row['path']), 'status' => $row['status']];
    }
    $db->close();
    echo json_encode(['status' => $status, 'tasks' => $tasks]);
    exit;
}

// --- API: directory change check (lightweight hash) ---
if ($action === 'api_dircheck') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    list($relPath, $absPath) = resolve_requested_path($requested);
    if ($absPath === false || !is_dir($absPath)) {
        echo json_encode(['hash' => '']); exit;
    }
    $entries = [];
    $dh = @opendir($absPath);
    if ($dh) {
        while (($entry = readdir($dh)) !== false) {
            if ($entry === '.' || $entry === '..') continue;
            $fp = $absPath . '/' . $entry;
            if (!should_show_entry($entry, $fp)) continue;
            $entries[] = $entry . ':' . @filesize($fp) . ':' . @filemtime($fp);
        }
        closedir($dh);
    }
    sort($entries);
    echo json_encode(['hash' => md5(implode("\n", $entries))]);
    exit;
}

// --- API: directory listing as JSON (for AJAX refresh) ---
if ($action === 'api_dirlist') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    list($relPath, $absPath) = resolve_requested_path($requested);
    if ($absPath === false || !is_dir($absPath)) {
        echo json_encode(['error' => 'invalid']); exit;
    }
    $items = list_directory($absPath);
    usort($items, function($a,$b) {
        if ($a['is_dir'] xor $b['is_dir']) return $a['is_dir'] ? -1 : 1;
        return strcasecmp($a['name'], $b['name']);
    });
    $out = [];
    foreach ($items as $it) {
        $cat = $it['is_dir'] ? 'folder' : file_category($it['name']);
        $icon = $it['is_dir'] ? '📁' : file_icon($it['name']);
        $rp = $relPath === '' ? $it['name'] : ($relPath . '/' . $it['name']);
        $hasMedia = in_array($cat, ['image','audio','video','document']);
        $viewMode = $it['is_dir'] ? false : file_view_mode($it['name']);
        $out[] = [
            'name' => $it['name'], 'is_dir' => $it['is_dir'],
            'size' => $it['size'], 'mtime' => $it['mtime'],
            'cat' => $cat, 'icon' => $icon, 'path' => $rp,
            'has_media' => $hasMedia,
            'view' => $viewMode ?: null,
        ];
    }
    // Also compute hash for future comparisons
    $entries = [];
    foreach ($items as $it) {
        $entries[] = $it['name'] . ':' . $it['size'] . ':' . $it['mtime'];
    }
    sort($entries);
    echo json_encode(['items' => $out, 'hash' => md5(implode("\n", $entries))]);
    exit;
}

// --- API: thumbnail (direct serve — kept for on-demand hover) ---
if ($action === 'api_thumb') {
    list(, $abs) = resolve_requested_path($requested);
    if (!$abs || !is_file($abs)) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); exit; }
    $cat = file_category(basename($abs));
    if (!in_array($cat, ['image','video','document'])) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); exit; }

    global $CACHE_DIR;
    $mtime = filemtime($abs);
    $thumbKey = md5($abs) . '_' . $mtime;
    $thumbPath = $CACHE_DIR . '/thumb_' . $thumbKey . '.jpg';

    if (!is_file($thumbPath)) {
        $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
        if ($cat === 'image') $thumbPath = generate_image_thumbnail($abs, $thumbPath);
        else if ($cat === 'video') $thumbPath = generate_video_thumbnail($abs, $thumbPath);
        else if ($cat === 'document' && $docExt === 'pdf') $thumbPath = generate_pdf_thumbnail($abs, $thumbPath);
        else $thumbPath = null;
    }

    if ($thumbPath && is_file($thumbPath)) {
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($thumbPath));
        header('Cache-Control: public, max-age=86400');
        readfile($thumbPath);
    } else {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    }
    exit;
}

// --- API: detailed media info (direct serve — kept for on-demand hover) ---
if ($action === 'api_detail') {
    header('Content-Type: application/json');
    list(, $abs) = resolve_requested_path($requested);
    if (!$abs || !is_file($abs)) { echo json_encode(['error'=>'invalid']); exit; }
    $cat = file_category(basename($abs));
    $result = ['category' => $cat, 'name' => basename($abs)];
    if ($cat === 'image') {
        $result['detail'] = get_image_info($abs);
    } else if ($cat === 'audio' || $cat === 'video') {
        $result['detail'] = get_mediainfo($abs);
    } else if ($cat === 'document') {
        $docExt = strtolower(pathinfo(basename($abs), PATHINFO_EXTENSION));
        $result['detail'] = ($docExt === 'pdf') ? get_pdf_info($abs) : get_document_info($abs);
    }
    echo json_encode($result);
    exit;
}

// --- API: stream file inline (for in-browser viewing) ---
if ($action === 'api_stream') {
    list($streamRel, $abs) = resolve_requested_path($requested);
    if (!$abs || !is_file($abs) || !is_readable($abs)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo "File not found."; exit;
    }
    // Log only on first request (not range sub-requests)
    if (!isset($_SERVER['HTTP_RANGE'])) {
        log_download($streamRel);
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $abs) ?: 'application/octet-stream';
    finfo_close($finfo);
    $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
    // Override MIME for known types that browsers handle well
    $mimeOverrides = [
        'mp4'=>'video/mp4','webm'=>'video/webm','ogv'=>'video/ogg','mov'=>'video/mp4',
        'mp3'=>'audio/mpeg','ogg'=>'audio/ogg','wav'=>'audio/wav','flac'=>'audio/flac',
        'm4a'=>'audio/mp4','opus'=>'audio/ogg','aac'=>'audio/aac',
        'pdf'=>'application/pdf',
        'svg'=>'image/svg+xml','webp'=>'image/webp','avif'=>'image/avif',
        'txt'=>'text/plain','md'=>'text/plain','log'=>'text/plain','csv'=>'text/csv',
        'json'=>'application/json','xml'=>'text/xml',
        'html'=>'text/html','css'=>'text/css','js'=>'text/javascript',
    ];
    if (isset($mimeOverrides[$ext])) $mime = $mimeOverrides[$ext];
    $size = filesize($abs);
    // Support HTTP Range requests for video/audio seeking
    $start = 0;
    $end = $size - 1;
    $statusCode = 200;
    if (isset($_SERVER['HTTP_RANGE'])) {
        if (preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m)) {
            $start = $m[1] !== '' ? (int)$m[1] : 0;
            $end = $m[2] !== '' ? min((int)$m[2], $size - 1) : $size - 1;
            if ($start > $end || $start >= $size) {
                header($_SERVER["SERVER_PROTOCOL"] . " 416 Range Not Satisfiable");
                header("Content-Range: bytes */$size");
                exit;
            }
            $statusCode = 206;
            header($_SERVER["SERVER_PROTOCOL"] . " 206 Partial Content");
            header("Content-Range: bytes $start-$end/$size");
        }
    }
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . ($end - $start + 1));
    header('Content-Disposition: inline; filename="' . rawurlencode(basename($abs)) . '"');
    header('Accept-Ranges: bytes');
    header('Cache-Control: public, max-age=3600');
    if ($statusCode === 200) {
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    }
    while (ob_get_level()) ob_end_flush();
    $fp = fopen($abs, 'rb');
    if ($start > 0) fseek($fp, $start);
    $remaining = $end - $start + 1;
    while ($remaining > 0 && !feof($fp)) {
        $chunk = fread($fp, min(8192, $remaining));
        echo $chunk;
        $remaining -= strlen($chunk);
        flush();
    }
    fclose($fp);
    exit;
}

// --- API: recursive search ---
if ($action === 'api_search') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');
    $q = trim($_GET['q'] ?? '');
    $searchPath = $_GET['path'] ?? '';
    $maxResults = 200;
    if ($q === '' || strlen($q) < 2) {
        echo json_encode(['error' => 'Query too short', 'items' => []]); exit;
    }
    list($relBase, $absBase) = resolve_requested_path($searchPath);
    if ($absBase === false || !is_dir($absBase)) {
        echo json_encode(['error' => 'Invalid path', 'items' => []]); exit;
    }
    $qLower = mb_strtolower($q);
    $results = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($absBase, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        $name = $file->getFilename();
        if (!should_show_entry($name, $file->getPathname())) continue;
        if (mb_strpos(mb_strtolower($name), $qLower) === false) continue;
        $isDir = $file->isDir();
        $fullRel = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($BASE_DIR))), '/');
        $cat = $isDir ? 'folder' : file_category($name);
        $icon = $isDir ? '📁' : file_icon($name);
        $hasMedia = in_array($cat, ['image','audio','video','document']);
        $viewMode = $isDir ? null : file_view_mode($name);
        $results[] = [
            'name' => $name, 'is_dir' => $isDir,
            'size' => $isDir ? -1 : $file->getSize(),
            'mtime' => $file->getMTime(),
            'cat' => $cat, 'icon' => $icon, 'path' => $fullRel,
            'has_media' => $hasMedia, 'view' => $viewMode ?: null,
        ];
        if (count($results) >= $maxResults) break;
    }
    echo json_encode(['items' => $results, 'truncated' => count($results) >= $maxResults]);
    exit;
}

// --- API: get download counts for a list of paths ---
if ($action === 'api_dlcounts') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $paths = $input['paths'] ?? [];
    if (!is_array($paths) || empty($paths)) {
        echo json_encode(['counts' => []]); exit;
    }
    $counts = get_download_counts_batch($paths);
    echo json_encode(['counts' => $counts]);
    exit;
}

// --- API: create share link ---
if ($action === 'api_share_create') {
    header('Content-Type: application/json');
    $path = $_GET['path'] ?? '';
    $hours = (int)($_GET['expires'] ?? 0);
    list($rel, $abs) = resolve_requested_path($path);
    if ($abs === false) { echo json_encode(['error' => 'Invalid path']); exit; }

    $token = bin2hex(random_bytes(16));
    $db = get_share_db();
    $stmt = $db->prepare('INSERT INTO shares (token, path, is_dir, created_at, expires_at) VALUES (:t, :p, :d, :c, :e)');
    $stmt->bindValue(':t', $token, SQLITE3_TEXT);
    $stmt->bindValue(':p', $rel, SQLITE3_TEXT);
    $stmt->bindValue(':d', is_dir($abs) ? 1 : 0, SQLITE3_INTEGER);
    $stmt->bindValue(':c', time(), SQLITE3_INTEGER);
    $stmt->bindValue(':e', $hours > 0 ? time() + ($hours * 3600) : null, SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();

    echo json_encode(['ok' => true, 'token' => $token]);
    exit;
}

function generate_image_thumbnail($srcPath, $dstPath) {
    global $THUMB_MAX_DIM;
    $maxDim = $THUMB_MAX_DIM ?: 240;
    $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));

    // Formats that need ImageMagick (convert): svg, ico, tiff, heic, heif, psd
    $imFormats = ['svg','ico','tiff','tif','heic','heif','psd'];
    if (in_array($ext, $imFormats)) {
        return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    }

    // Try GD first
    $info = @getimagesize($srcPath);
    if (!$info) {
        // GD can't read it — try ImageMagick as last resort
        return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    }
    $w = $info[0]; $h = $info[1]; $type = $info[2];
    $loaders = [
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG => 'imagecreatefrompng',
        IMAGETYPE_GIF => 'imagecreatefromgif',
        IMAGETYPE_WEBP => 'imagecreatefromwebp',
        IMAGETYPE_BMP => 'imagecreatefrombmp',
    ];
    // Add AVIF if GD supports it (PHP 8.1+)
    if (defined('IMAGETYPE_AVIF') && function_exists('imagecreatefromavif')) {
        $loaders[IMAGETYPE_AVIF] = 'imagecreatefromavif';
    }
    if (!isset($loaders[$type]) || !function_exists($loaders[$type])) {
        return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    }
    $src = @$loaders[$type]($srcPath);
    if (!$src) return _thumbnail_via_convert($srcPath, $dstPath, $maxDim);
    if ($w > $h) { $nw = min($w, $maxDim); $nh = (int)($h * $nw / $w); }
    else { $nh = min($h, $maxDim); $nw = (int)($w * $nh / $h); }
    if ($nw < 1) $nw = 1; if ($nh < 1) $nh = 1;
    $dst = imagecreatetruecolor($nw, $nh);
    // Preserve transparency for PNG/GIF/WEBP → fill white bg for JPEG output
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imagejpeg($dst, $dstPath, 80);
    imagedestroy($src); imagedestroy($dst);
    return $dstPath;
}

function _thumbnail_via_convert($srcPath, $dstPath, $maxDim = 240) {
    // Use ImageMagick convert as fallback
    // For SVG: specify density for quality rendering
    $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
    $density = ($ext === 'svg') ? '-density 150 ' : '';
    $cmd = 'convert ' . $density . escapeshellarg($srcPath . '[0]')
         . ' -thumbnail ' . $maxDim . 'x' . $maxDim
         . ' -background white -flatten -quality 80 '
         . escapeshellarg('jpeg:' . $dstPath) . ' 2>/dev/null';
    exec($cmd, $out, $ret);
    return ($ret === 0 && is_file($dstPath)) ? $dstPath : null;
}

function generate_video_thumbnail($srcPath, $dstPath) {
    global $THUMB_MAX_DIM;
    $maxDim = $THUMB_MAX_DIM ?: 240;
    // Determine seek position: 10% of duration (fallback to 1s)
    $seekSec = 1;
    $probe = shell_exec('ffprobe -v error -show_entries format=duration -of csv=p=0 '
        . escapeshellarg($srcPath) . ' 2>/dev/null');
    if ($probe !== null && is_numeric(trim($probe))) {
        $duration = (float)trim($probe);
        $seekSec = max(0, $duration * 0.10);
    }
    $cmd = 'ffmpeg -y -ss ' . $seekSec . ' -i ' . escapeshellarg($srcPath)
         . ' -vframes 1 -vf "scale=' . $maxDim . ':-1" -q:v 4 ' . escapeshellarg($dstPath)
         . ' 2>/dev/null';
    exec($cmd, $out, $ret);
    if ($ret !== 0 || !is_file($dstPath)) {
        // Fallback: try at 0 seconds
        $cmd = 'ffmpeg -y -ss 0 -i ' . escapeshellarg($srcPath)
             . ' -vframes 1 -vf "scale=' . $maxDim . ':-1" -q:v 4 ' . escapeshellarg($dstPath)
             . ' 2>/dev/null';
        exec($cmd, $out, $ret);
    }
    return (is_file($dstPath)) ? $dstPath : null;
}

function generate_pdf_thumbnail($srcPath, $dstPath) {
    global $THUMB_MAX_DIM;
    $maxDim = $THUMB_MAX_DIM ?: 240;
    // Use pdftoppm to render first page as JPEG thumbnail
    $tmpPrefix = $dstPath . '_tmp';
    $cmd = 'pdftoppm -jpeg -f 1 -l 1 -scale-to ' . (int)$maxDim . ' -singlefile '
         . escapeshellarg($srcPath) . ' ' . escapeshellarg($tmpPrefix) . ' 2>/dev/null';
    exec($cmd, $out, $ret);
    $tmpFile = $tmpPrefix . '.jpg';
    if ($ret === 0 && is_file($tmpFile)) {
        rename($tmpFile, $dstPath);
        return $dstPath;
    }
    @unlink($tmpFile);
    return null;
}

function get_pdf_info($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'pdf:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $result = [];
    $cmd = 'pdfinfo ' . escapeshellarg($filePath) . ' 2>/dev/null';
    $output = shell_exec($cmd);
    if ($output) {
        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^([^:]+):\s*(.+)$/', $line, $m)) {
                $key = trim($m[1]);
                $val = trim($m[2]);
                switch ($key) {
                    case 'Pages': $result['pages'] = (int)$val; break;
                    case 'Page size': $result['page_size'] = $val; break;
                    case 'Title': $result['title'] = $val; break;
                    case 'Author': $result['author'] = $val; break;
                    case 'Creator': $result['creator'] = $val; break;
                    case 'Producer': $result['producer'] = $val; break;
                    case 'PDF version': $result['pdf_version'] = $val; break;
                    case 'Encrypted': $result['encrypted'] = $val; break;
                    case 'CreationDate': $result['creation_date'] = $val; break;
                }
            }
        }
    }
    cache_set($cacheKey, $result, $mtime);
    return $result;
}

function get_document_info($filePath) {
    $mtime = filemtime($filePath);
    $cacheKey = 'doc:' . $filePath;
    $cached = cache_get($cacheKey, $mtime);
    if ($cached !== null) return $cached;

    $result = [];
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $formatNames = [
        'doc'=>'Word (legacy)','docx'=>'Word','odt'=>'OpenDocument Text','rtf'=>'Rich Text',
        'ppt'=>'PowerPoint (legacy)','pptx'=>'PowerPoint','odp'=>'OpenDocument Presentation',
        'xls'=>'Excel (legacy)','xlsx'=>'Excel','ods'=>'OpenDocument Spreadsheet',
        'epub'=>'EPUB',
    ];
    $result['format'] = $formatNames[$ext] ?? strtoupper($ext);

    // Try to get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $result['mime'] = finfo_file($finfo, $filePath) ?: 'unknown';
    finfo_close($finfo);

    // For EPUB: try to read metadata from OPF
    if ($ext === 'epub') {
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (preg_match('/\.opf$/i', $name)) {
                    $opf = $zip->getFromIndex($i);
                    if ($opf && preg_match('/<dc:title[^>]*>([^<]+)/i', $opf, $m)) $result['title'] = html_entity_decode(trim($m[1]));
                    if ($opf && preg_match('/<dc:creator[^>]*>([^<]+)/i', $opf, $m)) $result['author'] = html_entity_decode(trim($m[1]));
                    if ($opf && preg_match('/<dc:language[^>]*>([^<]+)/i', $opf, $m)) $result['language'] = trim($m[1]);
                    break;
                }
            }
            $zip->close();
        }
    }

    cache_set($cacheKey, $result, $mtime);
    return $result;
}

// --- Share-by-link ---
function get_share_db() {
    global $CACHE_DIR;
    $dbPath = $CACHE_DIR . '/shares.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(2000);
    $db->exec('CREATE TABLE IF NOT EXISTS shares (
        token TEXT PRIMARY KEY,
        path TEXT NOT NULL,
        is_dir INTEGER NOT NULL,
        created_at INTEGER NOT NULL,
        expires_at INTEGER
    )');
    return $db;
}

// --- Download/access logging ---
function get_log_db() {
    global $CACHE_DIR;
    $dbPath = $CACHE_DIR . '/logs.sqlite';
    $db = new SQLite3($dbPath);
    $db->busyTimeout(2000);
    $db->exec('CREATE TABLE IF NOT EXISTS download_counts (
        path TEXT PRIMARY KEY,
        count INTEGER NOT NULL DEFAULT 0
    )');
    $db->exec('CREATE TABLE IF NOT EXISTS access_log (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        path TEXT NOT NULL,
        ip TEXT,
        ts TEXT NOT NULL
    )');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_access_log_path ON access_log(path)');
    return $db;
}

function log_download($relPath) {
    global $ENABLE_LOGGING, $CACHE_ERROR;
    if ($CACHE_ERROR) return;
    $db = get_log_db();
    // Always increment download count
    $stmt = $db->prepare('INSERT INTO download_counts (path, count) VALUES (:p, 1)
        ON CONFLICT(path) DO UPDATE SET count = count + 1');
    $stmt->bindValue(':p', $relPath, SQLITE3_TEXT);
    $stmt->execute();
    // Detailed access log only if enabled
    if ($ENABLE_LOGGING) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt2 = $db->prepare('INSERT INTO access_log (path, ip, ts) VALUES (:p, :ip, :ts)');
        $stmt2->bindValue(':p', $relPath, SQLITE3_TEXT);
        $stmt2->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt2->bindValue(':ts', date('c'), SQLITE3_TEXT);
        $stmt2->execute();
    }
    $db->close();
}

function get_download_count($relPath) {
    global $CACHE_ERROR;
    if ($CACHE_ERROR) return 0;
    $db = get_log_db();
    $stmt = $db->prepare('SELECT count FROM download_counts WHERE path = :p');
    $stmt->bindValue(':p', $relPath, SQLITE3_TEXT);
    $r = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    $db->close();
    return $r ? (int)$r['count'] : 0;
}

function get_download_counts_batch($paths) {
    global $CACHE_ERROR;
    if ($CACHE_ERROR || empty($paths)) return [];
    $db = get_log_db();
    $result = [];
    $stmt = $db->prepare('SELECT count FROM download_counts WHERE path = :p');
    foreach ($paths as $p) {
        $stmt->bindValue(':p', $p, SQLITE3_TEXT);
        $r = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        $result[$p] = $r ? (int)$r['count'] : 0;
        $stmt->reset();
    }
    $db->close();
    return $result;
}

// --- Download ---
list($requestedRel, $absRequested) = resolve_requested_path($requested);
if ($absRequested === false) {
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid path."; exit;
}

if ($action === 'download') {
    if (!file_exists($absRequested)) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); echo "Not found."; exit; }
    if (is_file($absRequested)) {
        log_download($requestedRel);
        send_file_response($absRequested);
    }
    if (is_dir($absRequested)) {
        $fileCount = 0;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($absRequested, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($it as $f) { if ($f->isFile()) $fileCount++; }
        if ($fileCount > $MAX_FILES_IN_ZIP) { header($_SERVER["SERVER_PROTOCOL"]." 413 Payload Too Large"); echo "Too many files."; exit; }
        if ($fileCount === 0) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); echo "Empty folder."; exit; }
        $dn = preg_replace('/[^A-Za-z0-9._-]/','_', basename($absRequested)?:'folder').'.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.rawurlencode($dn).'"');
        header('Cache-Control: private');
        while (ob_get_level()) ob_end_flush();
        $zip = new StreamingZip(); $zip->open();
        $it2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($absRequested, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($it2 as $file) {
            if (!$file->isFile()) continue;
            $zip->addFileFromPath(ltrim(str_replace('\\','/',substr($file->getPathname(),strlen($absRequested))),'/'), $file->getPathname());
        }
        $zip->close(); exit;
    }
}

// --- Directory listing page ---
if (!file_exists($absRequested)) { header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); echo "Not found."; exit; }
if (!is_dir($absRequested)) {
    header('Location: '.u(['path'=>$requested,'action'=>'download'])); exit;
}

$items = list_directory($absRequested);
usort($items, function($a,$b) {
    if ($a['is_dir'] xor $b['is_dir']) return $a['is_dir'] ? -1 : 1;
    return strcasecmp($a['name'], $b['name']);
});

$crumbs = build_breadcrumbs($requestedRel);
$currentRel = $requestedRel;
$scriptName = htmlspecialchars($_SERVER['SCRIPT_NAME']);
?>
<?php
// Theme: use cookie if set, otherwise no class (JS will detect OS preference)
$themeCookie = $_COOKIE['fb_theme'] ?? '';
$themeClass = '';
if ($themeCookie === 'light') $themeClass = ' class="light"';
elseif ($themeCookie === 'dark') $themeClass = '';
// If no cookie → no class; JS will apply OS preference on load
?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($CURRENT_LANG); ?>"<?php echo $themeClass; ?>>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?php echo t('title'); ?> v<?php echo APP_VERSION; ?><?php echo $currentRel ? ' - /'.htmlspecialchars($currentRel) : ''; ?></title>
<?php if (!$themeCookie): ?>
<script>
// Detect OS preference before first paint to avoid flash
if(window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches){
  document.documentElement.classList.add('light');
}
</script>
<?php endif; ?>
<!-- lean inline favicon block -->
<link rel="shortcut icon" href="data:image/x-icon;base64,AAABAAIAEBAAAAAAIAA5AwAAJgAAACAgAAAAACAAGAgAAF8DAACJUE5HDQoaCgAAAA1JSERSAAAAEAAAABAIBgAAAB/z/2EAAAMASURBVHichZNNjFNlFIaf892vt52202kHOz9AnCIMmCEgOiQumInRxBg1szGWDTtXmBBMhsSoETuzA2JkoW5YuHBj7LhRo4lBDNEAakgkKFYqM+hYEFronb/e9vb23s/FjONG8VmdnDdvchbnEQDyRQuAkbxhjfxOZCZPiMj67t+Qe4WrGKGAsBPJAzAD5Jm5gmFaQgF46Oi5Z4n2vBT6nsYYRFtKIZWYUz787cn99XtekC8U7ZLZUtY9m4bwV0AUYRgSiSWRRvVcb2px0u0efsULJKbDVmcgZcvvTbsRa9SPnT+045K+4dpRlVCWt+IES42mAaTLtmgvLoUqlt7n3Are3KgXPyXed/zpB3tla8LjR7ebzy42+gUe15vibXO5HZrcgG3tfyJtBJEzJYddm5PWYI/d+WUhM/7B2dLC7hHeTUcSh86Ul5rjwyryUShxAJ3NZvEqMLRB88LYRgDavs9ze/vJdkf197NO+OF3mYnyfMvJJKvO8EAqoyMxRjISzAIaQIkR1zPcWfYQEarLHeZqTZQI846vgnaz09W7IXPh0q3TRb10au+O+4t2B9sAularYese84fT4tTZeSytuTjvsuD69KVsrtVa2FqpMOgYban0wcFarEL8+FVXxvPFoqWz2SytSsAD2S5em8gBcPKL35jY08e2/jhf/lzndOk6SUvR9ELHJO478HxO9784Uz74WG6PVq1GRIwJEYwBDGAshYnq1dm2MEYwSlmihC4xndqipB52ry7cfvuZ7Z6uuleChD0a/fWukcninCil+OmGy+U/b5KOaypOm7gdscKO7yoJqyYMfAn8sHXba6y/8ugbF456kfSRxeUVJRgVsxV+YAgCg7ZUkOpOauXVP75br7//1Oi2t57cld0++d4Phdd3z55Yd2Hs1c+zvelUxIoE5o4LcSAeB9eFqm+J8pfN3E3RY48Mfr1laHPuk/Olw9eP7XtntV0oqP+XapXcka8e3fryNyf+7v1jozHC1NR/2zk9tab1mt6FgmJ6OvwLk1dCk42GVvUAAAAASUVORK5CYIKJUE5HDQoaCgAAAA1JSERSAAAAIAAAACAIBgAAAHN6evQAAAffSURBVHic5Vd7jB5VFf+dc+/MfI99tNt22ZZuG9pq3JaKibZWC2yLCdAqxAdbiUHQiKh/GMWkJRjJ1yWICJr4SCSgUVDTml3bIFUDpC0tEXkuT3dLebS0wJaW7XYf3f1m5s49xz/m2+3WFhH1P04y39y55849v3vO75wzH/BeFzrpqVLhdqzid3ppN1YJOkn+v1AqlXc0fPJ6fXfr30YsAECVQCTnXHvXmVxesI6Ym4isehWC9wRDygBElUwQJhz3b3uik57p6FLTvY58R1eXOTKrg7Br17811rxklXavIz91jioV5c6N0GXX3bvYN8x/QMLGOeodlAxADEBBijxYqiA2QDoc2/jw55+86cJ7P3zNk0HPnR9x//mZlZDvmANAR5dB9zq/9LsP/YUbW9dm1ZEEgFHo2+9gwiBgyWy1/3NP3nThvat/9shX07q5JY1HUsOGAYEBgwNgZBxQEiqErEX2L+y4eskOnbCdowG6OjrMzUuuezHj4lkqqRIxn2RepwyIABVhtmSsegwdurR+QcsY6ubuRtSALK2CmSECVJ3HGWVC1SuGnUFjAKSDh/546PDfvngAr6bo7FQLAL9uabGiYsgYgpACmqeHIjdI+VhBYChAzCqZeG9N0DhnW3Lg9dVzz6a2t8bcY6mYsourUgiUbm1vREtJADLY3DuG7j2JntEy/7KzTLb9wNe+fEf7g2oZAOqbmlQVSqoAAaKEJEN+OUXiFJkoiIDUA3GmSDxzNc50zDH7hvkPvfzU/jnVoYMrrbg4gbXrl5fNaKL2toeH7fOvHbNXnR3aJTOZXOZEbbR8wqf2hJsFqgpRoGiAeY0MBaCae2M09jhWFTTXWdRHBqIKooAz7+TIGMCN83bgjddWzVlqVxrT8lRbcwHfuX+QXhoOMBSnmN2YYtncEJv2CbOXAACwa9cJAEQgYmD4eIYvrJyODWvmwUseB8OEnldH8JW7X8KvrliEBc11EJ2kKW/oflG27RXMmrVo++j+Vz71ZrnY0zvUtOzKDxb99x8eM2IsWmeU8UjPMRgqQfVEFkwWEwKDyQAqaCgwAmsRBRaFMEBgLaaXQxQtMK0cwVqDMLCTa6aXiyzOaeLIvpnO3Bq5RO/sGcWimUWz+TPT/I3n1+v0ksV580KoCsIpJSz3QB+gbTqZ8E4UqoCvxZ2JkHmBKuBq94lwERPSLIOKsrqqIiyXQg2WHz1y+OjXt0V+4exZzQcHxtAcDemWy1swDMU9T+u/AFgMQIRUFUQMyyYnf+1CDQSRwjADBBAIhBygqWUKsSEVkcw5GGETjvRXHh3TN8+cVvhtf1ZXuqzriFB5Bgr2NCEA0eTJ0iyvloYBppyEoopqpvDiQUANTP5qmpNlkhOqIhw1TFMN29/Y8P4tC6Kh9iKlx/ePB8Frw07DE9SfkgVgFQXqCwabHj+KnX1vQTmAEiMkj6EYyLiEy+/oQ2MEOArBBLA6vD5KqCvYWmYQoApVr+Bg4Pwbtq7Z94+Xpz2eDi5fsXThE1QulZEOZLnNVSc4gDYCESHNBEvnRbiorQEeBsQMS4L9b8XY1HMMl57ThOb6AE4NmAiMDFufHcELRzwim3sQBCiIBE72HCK5+tMrNz32yVmr23/x1DKf1j2n4NJp6oCCCahmivPe14gvnT8PU+Xg0XHc8/QAvrG6FXXF8GTd4D488/oQitYiy0smAAWLcLlYjpM41b39IzsvaZILfvfq3otK5dJlANDch1pPX4zJbgcCYifwokgzgfP5eHg8AwgYGs/gRZF5QZrlujib4MXEFw6BiWHYwLmqqSYpMbw+P0Q799739L5FB+5fj44u091NfkpGKuWNJsdxqmjt93TKScv5EwGkChEBKFIRRZYmPkVIM5bMX/j7H68f65hgHgCMDg7WEgtQFUQBwTAhtIzAMAwT6gsWREBjMYBhgjWM0Oa6yAIyFYxCoapMrLYGKAwDKhUjVQSJ1rrwFA4sAkTIi0cxtOg5WMV9zx2e6IkwpOjrH8fxhLH5sX6cNbMIX9tDVfB8fxUFyxDN2zUrLLGhTDLrfMr15RAEQuKEIFnNXV0TAJQ++nNyf7rh0XFw4MuR07/vq9KDe0cx6VcVEBFKhRC3PDBQYzrVuKYoBBYFQyAFjPoU6lOSuBgGZiwwIvsPjSCwdWgoGMSOhQCtLO5QALDtlV2msxPZMorv0qhwS+ISRKFBFAaTZCDOi5SoorEcTHbJmsPzk6uCbQj1Y8eC8TfWBu5oNXQjQwgbVlgboLW5Xg8dO4DIVM1Vld8U+tAtAFLe3bk6Q6XCT9y46rZs4KUfEvkBZHGCtJqST1L4JFUXp5TFqdU0VRenmlZTcnFKLk7h4pR9krKkqbjRqgkLzRI0fPPCH1z88ljdmZtNadbtMwqpOE9BKWKpa279Q1/WumXo+IEAqnTy/wIAK679ZVMWzZ7Gzqt4Q2z8JO3FO2IT6MQYKIDLXhHXFhQKEO8oScbLn23o7X1Azl1z1LT8+ZIPRP6ChYa6ezN96JUqZvv+cx++ee2jlVM+7Tu6zGly7L+TrnyvD31v57c+9qM9evVdvePLb92jZ1+//YrcVK4/xQNQJWzceOr8u5XOTmmvPGh3d67O2q7ffbefseRKO/DsT/tu+cS3J+b/ZxvvKKoEVbq48teGj1d2/+Saa+4IoEqYUgfeK6LU0aUGpwn5PwHf0wMTiCFXDQAAAABJRU5ErkJggg==">
<style>
/* --- Dark theme (default) --- */
:root{
  --bg:#1e2738;--card:#263044;--muted:#8899ab;--accent:#5b9cf6;--text:#dde5f0;
  --hover:#2e3d56;--border:rgba(255,255,255,0.06);--input-bg:#1b2233;--popup-bg:#1f2b3e;
  --btn-bg:transparent;--btn-border:var(--border)
}
/* --- Light theme --- */
html.light{
  --bg:#f0f2f5;--card:#fff;--muted:#666;--accent:#2563eb;--text:#1a1a1a;
  --hover:#e8edf3;--border:rgba(0,0,0,0.08);--input-bg:#fff;--popup-bg:#fff;
  --btn-bg:#fff;--btn-border:rgba(0,0,0,0.12)
}
*{box-sizing:border-box;font-family:Inter,system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;margin:0}
body{background:var(--bg);color:var(--text);padding:20px 24px}
a{color:var(--accent);text-decoration:none}
a:visited{color:var(--accent)}
.container{max-width:1200px;margin:0 auto}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px}
.title{font-size:18px;font-weight:600}
.toolbar{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.btn{background:var(--btn-bg);border:1px solid var(--btn-border);padding:4px 10px;border-radius:6px;color:var(--text);cursor:pointer;text-decoration:none;font-size:13px;white-space:nowrap;line-height:1.5}
.btn:hover{background:var(--hover)}
.path-bar{background:var(--card);padding:6px 12px;border-radius:6px;margin-bottom:8px;color:var(--muted);font-size:13px;display:flex;align-items:center;gap:5px;flex-wrap:wrap}
.path-bar a{color:var(--muted)}
.path-bar a:hover{color:var(--text)}
.path-bar .sep{opacity:0.4}
/* Search/filter bar */
.filterbar{display:flex;gap:6px;margin-bottom:8px;flex-wrap:wrap;align-items:center}
.filterbar input,.filterbar select{background:var(--input-bg);border:1px solid var(--border);border-radius:6px;padding:5px 10px;color:var(--text);font-size:13px;outline:none}
.filterbar input:focus,.filterbar select:focus{border-color:var(--accent)}
.filterbar input[type=text]{min-width:200px;flex:1;max-width:400px}
/* Table */
.table{width:100%;border-collapse:collapse;background:var(--card);border-radius:8px;overflow:visible}
.table thead{background:rgba(128,128,128,0.04)}
.table th{padding:7px 12px;color:var(--muted);font-size:12px;text-align:left;cursor:pointer;user-select:none;white-space:nowrap;border-bottom:1px solid var(--border);font-weight:500}
.table th:last-child{cursor:default}
.table th:hover{color:var(--text)}
.table th:last-child:hover{color:var(--muted)}
.table th .arrow{margin-left:3px;font-size:9px;opacity:0.3}
.table th.sorted .arrow{opacity:1;color:var(--accent)}
.table td{padding:4px 12px;border-bottom:1px solid var(--border);font-size:13px}
.table tbody tr:last-child td{border-bottom:none}
.table tbody tr:hover td{background:var(--hover)}
.table tbody tr.hidden{display:none}
.icon{display:inline-block;width:20px;margin-right:6px;text-align:center;font-size:13px;flex-shrink:0}
.namecell{display:flex;align-items:center}
.small{color:var(--muted);font-size:13px}
.actions{display:flex;gap:5px}
.empty{padding:30px;text-align:center;color:var(--muted)}
.info-cell{white-space:nowrap;color:var(--muted);font-size:12px}
.info-cell .media-tag{cursor:default}
/* Popup overlay */
.media-popup{display:none;position:fixed;z-index:10000;min-width:320px;max-width:480px;max-height:420px;overflow-x:hidden;overflow-y:auto;background:var(--popup-bg);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:12px;color:var(--text);box-shadow:0 8px 32px rgba(0,0,0,0.3);line-height:1.6;pointer-events:auto;word-wrap:break-word;overflow-wrap:break-word}
.media-popup.active{display:block}
.media-popup h4{margin:0 0 6px;font-size:13px;color:var(--accent);overflow-wrap:break-word;word-break:break-all}
.media-popup .mp-row{display:flex;gap:8px;flex-wrap:wrap}
.media-popup .mp-label{color:var(--muted);min-width:80px;flex-shrink:0}
.media-popup .mp-val{min-width:0;word-break:break-all}
.media-popup .stream{border-top:1px solid var(--border);margin-top:6px;padding-top:6px;overflow-wrap:break-word;word-break:break-word}
/* Thumbnail popup */
.thumb-popup{display:none;position:fixed;z-index:10001;background:var(--popup-bg);border:1px solid var(--border);border-radius:8px;padding:4px;box-shadow:0 8px 32px rgba(0,0,0,0.3);pointer-events:none}
.thumb-popup.active{display:block}
.thumb-popup img{display:block;max-width:<?php echo $THUMB_MAX_DIM; ?>px;max-height:<?php echo $THUMB_MAX_DIM; ?>px;border-radius:4px}
/* Theme toggle */
.theme-toggle{background:none;border:none;cursor:pointer;font-size:18px;padding:2px 6px;line-height:1;color:var(--text)}
/* Queue status */
.queue-status{font-size:11px;color:var(--muted);padding:3px 8px;border-radius:6px;background:var(--card);border:1px solid var(--border);white-space:nowrap;transition:opacity .3s}
.queue-status:empty{display:none}
.queue-status .qs-spinner{display:inline-block;width:10px;height:10px;border:2px solid var(--muted);border-top-color:var(--accent);border-radius:50%;animation:qs-spin .8s linear infinite;margin-right:4px;vertical-align:middle}
@keyframes qs-spin{to{transform:rotate(360deg)}}
/* Queue detail popup */
.queue-status{position:relative;cursor:default}
.queue-detail-popup{display:none;position:absolute;left:0;top:100%;margin-top:6px;min-width:260px;max-width:360px;max-height:320px;overflow-y:auto;background:var(--popup-bg);border:1px solid var(--border);border-radius:8px;padding:8px 0;font-size:11px;color:var(--text);box-shadow:0 8px 32px rgba(0,0,0,0.3);z-index:10002;line-height:1.5}
.queue-detail-popup.active{display:block}
.queue-detail-popup .qd-title{padding:2px 10px 6px;font-weight:600;font-size:12px;color:var(--accent);border-bottom:1px solid var(--border);margin-bottom:4px}
.queue-detail-popup .qd-row{padding:2px 10px;display:flex;gap:6px;align-items:center}
.queue-detail-popup .qd-type{color:var(--muted);min-width:50px;text-transform:capitalize}
.queue-detail-popup .qd-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.queue-detail-popup .qd-badge{font-size:9px;padding:1px 5px;border-radius:4px;font-weight:600}
.queue-detail-popup .qd-badge.running{background:var(--accent);color:#fff}
.queue-detail-popup .qd-badge.pending{background:var(--border);color:var(--muted)}
@media(max-width:700px){
  .table th:nth-child(4),.table td:nth-child(4){display:none}
  body{padding:12px}
  .filterbar input[type=text]{min-width:120px}
}
/* Cookie consent banner */
.cookie-banner{display:none;position:fixed;bottom:0;left:0;right:0;background:var(--card);border-top:1px solid var(--border);padding:10px 20px;font-size:12px;color:var(--muted);z-index:20000;text-align:center;box-shadow:0 -4px 16px rgba(0,0,0,0.15)}
.cookie-banner.active{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap}
.cookie-banner .cb-text{flex:1;min-width:200px;text-align:left}
.cookie-banner .cb-btn{background:var(--accent);color:#fff;border:none;padding:5px 16px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap}
.cookie-banner .cb-btn:hover{opacity:0.9}
/* Media viewer modal */
.viewer-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:30000;background:rgba(0,0,0,0.85);align-items:center;justify-content:center;flex-direction:column}
.viewer-overlay.active{display:flex}
.share-box{background:var(--bg);border:1px solid var(--border);padding:25px;border-radius:8px;max-width:400px;width:90%;box-shadow:0 10px 40px rgba(0,0,0,0.5)}
.share-box h3{margin-top:0;margin-bottom:10px}
.viewer-header{position:absolute;top:0;left:0;right:0;display:flex;align-items:center;justify-content:space-between;padding:10px 16px;background:linear-gradient(rgba(0,0,0,0.7),transparent);z-index:1;color:#fff;font-size:14px}
.viewer-title{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-right:12px;font-weight:500}
.viewer-actions{display:flex;gap:8px;flex-shrink:0}
.viewer-btn{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);color:#fff;padding:5px 12px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none;white-space:nowrap;backdrop-filter:blur(4px)}
.viewer-btn:hover{background:rgba(255,255,255,0.25)}
.viewer-btn.close-btn{font-size:20px;padding:2px 10px;line-height:1}
.viewer-content{display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:50px 16px 16px}
.viewer-content video,.viewer-content audio{max-width:95vw;max-height:85vh;border-radius:8px;outline:none}
.viewer-content video{background:#000}
.viewer-content audio{min-width:min(400px,90vw)}
.viewer-content img{max-width:95vw;max-height:85vh;object-fit:contain;border-radius:4px;cursor:zoom-in}
.viewer-content img.zoomed{max-width:none;max-height:none;cursor:zoom-out}
.viewer-content iframe{width:95vw;height:85vh;border:none;border-radius:8px;background:#fff}
.viewer-content pre{max-width:95vw;max-height:85vh;overflow:auto;background:var(--card);color:var(--text);padding:20px;border-radius:8px;font-size:13px;white-space:pre-wrap;word-break:break-all;margin:0}
.viewer-content .viewer-error{color:var(--muted);font-size:15px;text-align:center;padding:40px}
/* Nav arrows for image galleries */
.viewer-nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.1);border:none;color:#fff;font-size:28px;padding:12px 16px;cursor:pointer;border-radius:8px;z-index:2;backdrop-filter:blur(4px)}
.viewer-nav:hover{background:rgba(255,255,255,0.2)}
.viewer-nav.prev{left:12px}
.viewer-nav.next{right:12px}
.viewer-nav:disabled{opacity:0.2;cursor:default}
/* Error overlay */
.error-overlay{position:fixed;top:0;left:0;right:0;bottom:0;z-index:50000;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center}
.error-box{background:var(--card);border:1px solid #e74c3c;border-radius:12px;padding:24px 32px;max-width:500px;color:var(--text);text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.4)}
.error-box h2{color:#e74c3c;margin:0 0 12px;font-size:18px}
.error-box p{color:var(--muted);font-size:13px;margin:0 0 8px;line-height:1.5}
.error-box code{background:var(--input-bg);padding:2px 6px;border-radius:4px;font-size:12px}
/* Download count badge */
.dl-count{font-size:11px;color:var(--muted);opacity:0.7}
/* Language selector (custom dropdown) */
.lang-dropdown-wrapper{position:relative;display:inline-block}
.lang-select{background:var(--btn-bg);border:1px solid var(--btn-border);padding:3px 8px;border-radius:6px;color:var(--text);cursor:pointer;font-size:13px;line-height:1.5;min-width:60px}
.lang-select:hover{background:var(--hover)}
.lang-dropdown-menu{position:absolute;top:100%;right:0;background:var(--bg);border:1px solid var(--btn-border);border-radius:6px;z-index:1000;max-height:300px;overflow-y:auto;min-width:180px;margin-top:2px;box-shadow:0 4px 6px rgba(0,0,0,0.1);display:none}
.lang-dropdown-menu.show{display:block}
.lang-dropdown-menu button{display:block;width:100%;text-align:left;padding:8px 12px;border:none;background:var(--bg);color:var(--text);cursor:pointer;font-size:13px;white-space:nowrap;border-bottom:1px solid var(--btn-border)}
.lang-dropdown-menu button:last-child{border-bottom:none}
.lang-dropdown-menu button:hover{background:var(--hover)}
.lang-dropdown-menu button.active{background:var(--hover);font-weight:bold}
</style>
</head>
<body>
<?php if ($CACHE_ERROR): ?>
<div class="error-overlay">
  <div class="error-box">
    <h2><?php echo t('config_error'); ?></h2>
    <p><?php echo $CACHE_ERROR; ?></p>
    <p>Please create the directory and ensure the web server can write to it:</p>
    <p><code>mkdir -p <?php echo htmlspecialchars($CACHE_DIR); ?> && chmod 755 <?php echo htmlspecialchars($CACHE_DIR); ?> && chown -R <?php echo htmlspecialchars($CACHE_DIR_OWNER) . ":" . htmlspecialchars($CACHE_DIR_OWNER); ?> <?php echo htmlspecialchars($CACHE_DIR); ?></code></p>
  </div>
</div>
<?php endif; ?>
<div class="container">
  <div class="header">
    <div class="title"><?php echo t('title'); ?> v<?php echo APP_VERSION; ?></div>
    <div class="toolbar">
      <span class="queue-status" id="queueStatus" title="<?php echo t('background_tasks'); ?>"><span id="queueStatusText"></span><div class="queue-detail-popup" id="queueDetailPopup"></div></span>
      <a class="btn" href="<?php echo u(['path'=>$currentRel,'action'=>'download']); ?>" title="<?php echo t('download_folder'); ?>"><?php echo t('download_folder'); ?></a>
      <a class="btn" href="<?php echo $SHARE_TOKEN ? u(['path'=>'']) : $scriptName; ?>"><?php echo t('home'); ?></a>
      <div class="lang-dropdown-wrapper">
        <button class="lang-select" id="langSelect" title="Language"><?php echo $I18N[$CURRENT_LANG]['_flag'] . ' ' . $I18N[$CURRENT_LANG]['_name']; ?></button>
        <div class="lang-dropdown-menu" id="langDropdownMenu">
          <?php foreach ($I18N as $code => $lang): ?>
            <button data-lang="<?php echo $code; ?>"<?php if ($code === $CURRENT_LANG) echo ' class="active"'; ?>><?php echo $lang['_flag'] . ' ' . $lang['_name']; ?></button>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="theme-toggle" id="themeToggle" title="<?php echo t('toggle_theme'); ?>">&#9790;</button>
    </div>
  </div>

  <div class="path-bar">
    <?php foreach ($crumbs as $i => $c): ?>
      <?php if ($i > 0): ?><span class="sep">/</span><?php endif; ?>
      <?php if ($i === count($crumbs)-1): ?>
        <span><?php echo htmlspecialchars($c['name']); ?></span>
      <?php else: ?>
        <a href="<?php echo u(['path'=>$c['path']]); ?>"><?php echo htmlspecialchars($c['name']); ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <div class="filterbar">
    <input type="text" id="searchInput" placeholder="<?php echo t('search_placeholder'); ?>" autocomplete="off"/>
    <select id="filterType">
      <option value=""><?php echo t('all_types'); ?></option>
      <option value="folder"><?php echo t('type_folder'); ?></option>
      <option value="image"><?php echo t('type_image'); ?></option>
      <option value="audio"><?php echo t('type_audio'); ?></option>
      <option value="video"><?php echo t('type_video'); ?></option>
      <option value="document"><?php echo t('type_document'); ?></option>
      <option value="archive"><?php echo t('type_archive'); ?></option>
      <option value="other"><?php echo t('type_other'); ?></option>
    </select>
    <label style="display:flex;align-items:center;gap:4px;font-size:13px;color:var(--muted);cursor:pointer;white-space:nowrap">
      <input type="checkbox" id="searchRecursive" style="margin:0"/> <?php echo t('recursive'); ?>
    </label>
  </div>

  <?php if (count($items) === 0): ?>
    <div class="empty"><?php echo t('folder_empty'); ?></div>
  <?php else: ?>
  <table class="table" id="filetable">
    <thead>
      <tr>
        <th data-sort="name" class="sorted"><?php echo t('col_name'); ?> <span class="arrow">▲</span></th>
        <th data-sort="size" style="width:90px"><?php echo t('col_size'); ?> <span class="arrow">▲</span></th>
        <th data-sort="info" style="width:80px"><?php echo t('col_info'); ?> <span class="arrow">▲</span></th>
        <th data-sort="mtime" style="width:140px"><?php echo t('col_modified'); ?> <span class="arrow">▲</span></th>
        <th style="width:120px"><?php echo t('col_actions'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
        if (trim($currentRel) !== '') {
            $parent = dirname($currentRel);
            if ($parent === '.') $parent = '';
            echo '<tr data-isdir="1" data-name="" data-size="-1" data-mtime="0" data-info="-1" data-cat="folder" class="parentrow">';
            echo '<td><div class="namecell"><span class="icon">↰</span><a href="'.u(['path'=>$parent]).'">'.t('parent_folder').'</a></div></td>';
            echo '<td class="small"></td><td class="info-cell"></td><td class="small"></td><td></td>';
            echo '</tr>';
        }
      ?>
      <?php foreach ($items as $it):
        $cat = $it['is_dir'] ? 'folder' : file_category($it['name']);
        $icon = $it['is_dir'] ? '📁' : file_icon($it['name']);
        $relPath = $currentRel === '' ? $it['name'] : ($currentRel.'/'.$it['name']);
        $hasMedia = in_array($cat, ['image','audio','video','document']);
        $viewMode = $it['is_dir'] ? false : file_view_mode($it['name']);
      ?>
        <tr data-isdir="<?php echo $it['is_dir']?'1':'0'; ?>"
            data-name="<?php echo htmlspecialchars($it['name']); ?>"
            data-size="<?php echo $it['size']; ?>"
            data-mtime="<?php echo $it['mtime']; ?>"
            data-info="<?php echo $it['is_dir'] ? '50000' : '0'; ?>"
            data-cat="<?php echo $cat; ?>"
            data-path="<?php echo htmlspecialchars($relPath); ?>"
            <?php if ($hasMedia): ?>data-media="1"<?php endif; ?>
            <?php if ($viewMode): ?>data-view="<?php echo $viewMode; ?>"<?php endif; ?>>
          <td>
            <div class="namecell">
              <span class="icon"><?php echo $icon; ?></span>
              <?php if ($it['is_dir']): ?>
                <a href="<?php echo u(['path'=>$relPath]); ?>"><?php echo safe_basename($it['name']); ?></a>
              <?php else: ?>
                <a href="<?php echo u(['path'=>$relPath,'action'=>'download']); ?>"
                   <?php if ($viewMode === 'modal'): ?>onclick="return openViewer(this)"<?php endif; ?>
                   <?php if ($viewMode === 'tab'): ?>onclick="return openInTab(this)"<?php endif; ?>
                ><?php echo safe_basename($it['name']); ?></a>
              <?php endif; ?>
            </div>
          </td>
          <td class="small size-cell"><?php echo $it['is_dir'] ? '<span class="dir-size" data-path="'.htmlspecialchars($relPath).'">…</span>' : format_size($it['size']); ?></td>
          <td class="info-cell"><?php if ($it['is_dir']): ?><span class="dir-entry" data-path="<?php echo htmlspecialchars($relPath); ?>">…</span><?php elseif ($hasMedia): ?><span class="media-tag" data-path="<?php echo htmlspecialchars($relPath); ?>">…</span><div class="media-popup"></div><?php endif; ?></td>
          <td class="small"><?php echo $it['mtime'] ? date(t('_dateFormat'), $it['mtime']) : '-'; ?></td>
          <td>
            <div class="actions">
              <?php if (!$it['is_dir']): ?>
                <?php if ($viewMode === 'modal'): ?>
                  <a class="btn" href="#" onclick="return openViewerByPath('<?php echo htmlspecialchars($relPath, ENT_QUOTES); ?>')"><?php echo t('view'); ?></a>
                <?php elseif ($viewMode === 'tab'): ?>
                  <a class="btn" href="<?php echo u(['path'=>$relPath,'action'=>'api_stream']); ?>" target="_blank"><?php echo t('view'); ?></a>
                <?php endif; ?>
                <a class="btn" href="<?php echo u(['path'=>$relPath,'action'=>'download']); ?>"><?php echo t('download'); ?></a>
              <?php else: ?>
                <a class="btn" href="<?php echo u(['path'=>$relPath]); ?>"><?php echo t('open'); ?></a>
                <a class="btn" href="<?php echo u(['path'=>$relPath,'action'=>'download']); ?>"><?php echo t('zip'); ?></a>
              <?php endif; ?>
              <?php if (!$SHARE_TOKEN): ?>
                <button class="btn" onclick="sharePath('<?php echo htmlspecialchars($relPath, ENT_QUOTES); ?>')"><?php echo t('share'); ?></button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<!-- Media viewer overlay -->
<div class="viewer-overlay" id="viewerOverlay">
  <div class="viewer-header">
    <span class="viewer-title" id="viewerTitle"></span>
    <div class="viewer-actions">
      <a class="viewer-btn" id="viewerDownload" href="#" download><?php echo t('viewer_download'); ?></a>
      <a class="viewer-btn" id="viewerNewTab" href="#" target="_blank"><?php echo t('viewer_newtab'); ?></a>
      <button class="viewer-btn close-btn" id="viewerClose" title="<?php echo t('viewer_close'); ?>">✕</button>
    </div>
  </div>
  <button class="viewer-nav prev" id="viewerPrev" title="<?php echo t('viewer_prev'); ?>">‹</button>
  <button class="viewer-nav next" id="viewerNext" title="<?php echo t('viewer_next'); ?>">›</button>
  <div class="viewer-content" id="viewerContent"></div>
</div>

<div class="cookie-banner" id="cookieBanner">
  <span class="cb-text"><?php echo t('cookie_text'); ?></span>
  <button class="cb-btn" id="cookieAccept"><?php echo t('cookie_ok'); ?></button>
</div>

<!-- Share overlay -->
<div class="viewer-overlay" id="shareOverlay">
  <div class="share-box">
    <h3><?php echo t('share'); ?></h3>
    <p id="sharePathDisplay" style="word-break:break-all;font-size:13px;color:var(--muted)"></p>
    <div style="margin:20px 0">
      <label><?php echo t('share_expires'); ?></label>
      <select id="shareExpires" class="lang-select" style="width:100%;margin-top:8px">
        <option value="0"><?php echo t('share_never'); ?></option>
        <option value="1"><?php echo t('share_1h'); ?></option>
        <option value="24"><?php echo t('share_1d'); ?></option>
        <option value="168"><?php echo t('share_7d'); ?></option>
        <option value="720"><?php echo t('share_30d'); ?></option>
      </select>
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end">
      <button class="btn" id="shareCancel"><?php echo t('viewer_close'); ?></button>
      <button class="btn" id="shareConfirm" style="background:var(--accent);color:#fff;border-color:var(--accent)"><?php echo t('share'); ?></button>
    </div>
  </div>
</div>

<script>
(function(){
// --- Cookie helpers ---
function setCookie(name, value, days){
  var d = new Date(); d.setTime(d.getTime() + (days||365)*24*60*60*1000);
  document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
}
function getCookie(name){
  var m = document.cookie.match('(^|;)\\s*' + name + '=([^;]*)');
  return m ? decodeURIComponent(m[2]) : null;
}

// --- Cookie consent banner ---
if(!getCookie('fb_cookie_ok')){
  var banner = document.getElementById('cookieBanner');
  if(banner) banner.classList.add('active');
  document.getElementById('cookieAccept').addEventListener('click', function(){
    setCookie('fb_cookie_ok', '1', 365);
    banner.classList.remove('active');
  });
}

// --- Theme toggle ---
var html = document.documentElement;
// If no cookie was set, detect OS preference and persist it
if(!getCookie('fb_theme')){
  if(window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches){
    html.classList.add('light');
  }
  setCookie('fb_theme', html.classList.contains('light') ? 'light' : 'dark');
}
var toggleBtn = document.getElementById('themeToggle');
function updateToggleIcon(){ toggleBtn.textContent = html.classList.contains('light') ? '\u263D' : '\u2600'; }
updateToggleIcon();
toggleBtn.addEventListener('click', function(){
  html.classList.toggle('light');
  setCookie('fb_theme', html.classList.contains('light') ? 'light' : 'dark');
  updateToggleIcon();
});

// --- I18N (client-side) ---
var i18n = <?php echo json_encode($I18N[$CURRENT_LANG], JSON_UNESCAPED_UNICODE); ?>;
var shareToken = '<?php echo $SHARE_TOKEN; ?>';
function addShare(params) {
  if (shareToken) params.append('s', shareToken);
  return params;
}

// Language selector (custom dropdown)
var langSelect = document.getElementById('langSelect');
var langDropdownMenu = document.getElementById('langDropdownMenu');
if(langSelect && langDropdownMenu){
  // Toggle dropdown
  langSelect.addEventListener('click', function(e){
    e.stopPropagation();
    langDropdownMenu.classList.toggle('show');
  });
  // Language selection
  var langButtons = langDropdownMenu.querySelectorAll('button[data-lang]');
  langButtons.forEach(function(btn){
    btn.addEventListener('click', function(){
      setCookie('fb_lang', this.dataset.lang, 365);
      location.reload();
    });
  });
  // Close dropdown when clicking outside
  document.addEventListener('click', function(){
    langDropdownMenu.classList.remove('show');
  });
}

var table = document.getElementById('filetable');
if (!table) return;
var thead = table.querySelector('thead');
var tbody = table.querySelector('tbody');
var ths = thead.querySelectorAll('th[data-sort]');
var currentSort = 'name', currentDir = 1;
var folderPath = <?php echo json_encode($currentRel); ?>;

// Restore sort preference for this folder
var savedSort = getCookie('fb_sort');
if(savedSort){
  try {
    var prefs = JSON.parse(savedSort);
    if(prefs[folderPath]){
      currentSort = prefs[folderPath].k || 'name';
      currentDir = prefs[folderPath].d || 1;
    }
  } catch(e){}
}

function saveSortPref(){
  var prefs = {};
  try { prefs = JSON.parse(getCookie('fb_sort') || '{}'); } catch(e){}
  prefs[folderPath] = {k: currentSort, d: currentDir};
  // Keep max 50 folders to avoid cookie overflow
  var keys = Object.keys(prefs);
  if(keys.length > 50){ delete prefs[keys[0]]; }
  setCookie('fb_sort', JSON.stringify(prefs));
}

// --- Sorting ---
function getRows(){ return Array.from(tbody.querySelectorAll('tr:not(.parentrow)')); }

function sortTable(key, dir) {
  var rows = getRows();
  rows.sort(function(a,b){
    var ad=parseInt(a.dataset.isdir), bd=parseInt(b.dataset.isdir);
    if(ad!==bd) return bd-ad;
    var av,bv;
    if(key==='name'){
      av=a.dataset.name.toLowerCase(); bv=b.dataset.name.toLowerCase();
      return dir*(av<bv?-1:av>bv?1:0);
    } else if(key==='size'){
      av=parseFloat(a.dataset.size); bv=parseFloat(b.dataset.size);
      return dir*(av-bv);
    } else if(key==='info'){
      av=parseFloat(a.dataset.info)||0; bv=parseFloat(b.dataset.info)||0;
      return dir*(av-bv);
    } else {
      av=parseInt(a.dataset.mtime); bv=parseInt(b.dataset.mtime);
      return dir*(av-bv);
    }
  });
  rows.forEach(function(r){ tbody.appendChild(r); });
}

function updateHeaders(key,dir){
  ths.forEach(function(th){ th.classList.remove('sorted'); th.querySelector('.arrow').textContent='\u25B2'; });
  var act=thead.querySelector('th[data-sort="'+key+'"]');
  if(act){ act.classList.add('sorted'); act.querySelector('.arrow').textContent=dir===1?'\u25B2':'\u25BC'; }
}

ths.forEach(function(th){
  th.addEventListener('click', function(){
    var key=this.dataset.sort;
    if(currentSort===key) currentDir*=-1; else { currentSort=key; currentDir=(key==='info'||key==='size')?-1:1; }
    updateHeaders(currentSort,currentDir);
    sortTable(currentSort,currentDir);
    saveSortPref();
  });
});

// Apply saved sort on load
updateHeaders(currentSort, currentDir);
sortTable(currentSort, currentDir);

// --- Search & filter ---
var searchInput = document.getElementById('searchInput');
var filterType = document.getElementById('filterType');
var searchRecursive = document.getElementById('searchRecursive');
var recursiveDebounce = null;
var originalRows = null; // stash of original rows when showing recursive results
var isShowingRecursive = false;

function applyFilters(){
  // If recursive is checked and there's a query, do server-side recursive search
  if(searchRecursive.checked && searchInput.value.trim().length >= 2){
    clearTimeout(recursiveDebounce);
    recursiveDebounce = setTimeout(doRecursiveSearch, 400);
    return;
  }
  // Restore original rows if we were showing recursive results
  restoreOriginalRows();
  // Local filter
  var q = searchInput.value.toLowerCase();
  var t = filterType.value;
  getRows().forEach(function(r){
    var name = r.dataset.name.toLowerCase();
    var cat = r.dataset.cat;
    var matchName = !q || name.indexOf(q) !== -1;
    var matchType = !t || cat === t;
    r.classList.toggle('hidden', !(matchName && matchType));
  });
}

function doRecursiveSearch(){
  var q = searchInput.value.trim();
  if(q.length < 2) return;
  // Stash original rows
  if(!isShowingRecursive){
    originalRows = Array.from(tbody.querySelectorAll('tr'));
  }
  fetch('?'+addShare(new URLSearchParams({action:'api_search',path:folderPath,q:q})))
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(!d.items) return;
      var t = filterType.value;
      // Clear tbody except parentrow
      getRows().forEach(function(r){ r.remove(); });
      d.items.forEach(function(it){
        if(t && it.cat !== t) return;
        var tmp = document.createElement('tbody');
        tmp.innerHTML = buildRowHTML(it);
        var newRow = tmp.firstChild;
        tbody.appendChild(newRow);
        wireRowHandlers(newRow);
      });
      isShowingRecursive = true;
      sortTable(currentSort, currentDir);
      if(typeof loadDownloadCounts === 'function') loadDownloadCounts();
    })
    .catch(function(e){ console.error('Recursive search failed', e); });
}

function restoreOriginalRows(){
  if(!isShowingRecursive || !originalRows) return;
  getRows().forEach(function(r){ r.remove(); });
  originalRows.forEach(function(r){ tbody.appendChild(r); });
  isShowingRecursive = false;
}

searchInput.addEventListener('input', applyFilters);
filterType.addEventListener('change', applyFilters);
searchRecursive.addEventListener('change', applyFilters);

// =========== QUEUE-BASED ASYNC LOADING ===========
var queueStatusEl = document.getElementById('queueStatus');
var queueDetailPopup = document.getElementById('queueDetailPopup');
var pendingTasks = {}; // key = type:path → {type, path, applied: bool}
var workInterval = null;
var pollInterval = null;
var working = false;
var queueHoverTimer = null;
var queueDetailLoading = false;

function taskKey(type, path){ return type + ':' + path; }

function updateStatusUI(status){
  var textEl = document.getElementById('queueStatusText');
  if(!textEl) return;
  if(!status || status.total === 0){
    textEl.innerHTML = '';
    queueStatusEl.style.display = 'none';
  } else {
    queueStatusEl.style.display = '';
    var msg = (i18n.queue_processing||'{running} processing, {pending} queued').replace('{running}',status.running).replace('{pending}',status.pending);
    textEl.innerHTML = '<span class="qs-spinner"></span>' + msg;
  }
}

// Collect all tasks needed for this page and batch-enqueue them
function collectAndEnqueue(){
  if (shareToken) return;
  var tasks = [];

  // Folder sizes (lowest priority — enqueued first, so they sit at bottom of stack)
  document.querySelectorAll('.dir-size').forEach(function(el){
    var path = el.dataset.path;
    var row = el.closest('tr');
    var entryEl = row ? row.querySelector('.dir-entry') : null;
    var key = taskKey('dirsize', path);
    pendingTasks[key] = {type:'dirsize', path:path, applied:false, el:el, entryEl:entryEl};
    tasks.push({type:'dirsize', path:path});
  });

  // Media info summaries (medium priority)
  document.querySelectorAll('.media-tag').forEach(function(el){
    var path = el.dataset.path;
    var key = taskKey('info', path);
    pendingTasks[key] = {type:'info', path:path, applied:false, el:el};
    tasks.push({type:'info', path:path});
  });

  // Thumbnails for image/video/document (highest priority — enqueued last → top of stack)
  document.querySelectorAll('tr[data-media]').forEach(function(row){
    var cat = row.dataset.cat;
    if(cat !== 'image' && cat !== 'video' && cat !== 'document') return;
    var path = row.dataset.path;
    var key = taskKey('thumb', path);
    pendingTasks[key] = {type:'thumb', path:path, applied:false, row:row};
    tasks.push({type:'thumb', path:path});
  });

  if(tasks.length === 0) return;

  // Enqueue all at once
  fetch('?'+addShare(new URLSearchParams({action:'api_enqueue'})), {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({tasks:tasks})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    updateStatusUI(d.status);
    // Start the work loop
    startWorkLoop();
  })
  .catch(function(e){ console.error('Enqueue failed', e); });
}

function startWorkLoop(){
  if(workInterval) return;
  // Trigger workers rapidly at first, then slow down
  doWork();
  workInterval = setInterval(doWork, 1500);
  // Also poll for results from other workers/tabs
  pollInterval = setInterval(pollResults, 2000);
}

function stopWorkLoop(){
  if(workInterval){ clearInterval(workInterval); workInterval=null; }
  if(pollInterval){ clearInterval(pollInterval); pollInterval=null; }
  updateStatusUI(null);
}

function doWork(){
  if(working) return;
  working = true;
  fetch('?'+addShare(new URLSearchParams({action:'api_work'})))
    .then(function(r){ return r.json(); })
    .then(function(d){
      working = false;
      // Apply completed results
      if(d.completed) d.completed.forEach(applyResult);
      updateStatusUI(d.status);
      if(d.status && d.status.total === 0){
        // All done — one final poll to catch any stragglers, then stop
        pollResults().then(function(){ stopWorkLoop(); });
      }
    })
    .catch(function(){ working = false; });
}

function pollResults(){
  // Ask server for results of our pending (unapplied) tasks
  var waiting = [];
  for(var key in pendingTasks){
    if(!pendingTasks[key].applied) waiting.push({type:pendingTasks[key].type, path:pendingTasks[key].path});
  }
  if(waiting.length === 0){ stopWorkLoop(); return Promise.resolve(); }

  return fetch('?'+addShare(new URLSearchParams({action:'api_poll'})), {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({waiting:waiting})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if(d.results) d.results.forEach(applyResult);
    updateStatusUI(d.status);
    if(d.status && d.status.total === 0){
      // Check if all applied
      var allDone = true;
      for(var k in pendingTasks){ if(!pendingTasks[k].applied) allDone=false; }
      if(allDone) stopWorkLoop();
    }
  })
  .catch(function(){});
}

function applyResult(r){
  if(!r || !r.type || !r.path) return;
  var key = taskKey(r.type, r.path);
  var task = pendingTasks[key];
  if(!task || task.applied) return;
  if(r.status && r.status !== 'done') return; // not finished yet

  var result = r.result;
  if(!result){ task.applied=true; return; } // error or null

  if(r.type === 'dirsize' && task.el){
    task.el.textContent = result.formatted || '-';
    var row = task.el.closest('tr');
    if(row && result.size !== undefined) row.dataset.size = result.size;
    // Update entry count in info column
    var totalEntries = (result.files||0) + (result.dirs||0);
    if(task.entryEl){
      var parts = [];
      if(result.files){
        var ft = result.files===1 ? (i18n.file_singular||'{n} file') : (i18n.file_plural||'{n} files');
        parts.push(ft.replace('{n}',result.files));
      }
      if(result.dirs){
        var dt = result.dirs===1 ? (i18n.folder_singular||'{n} folder') : (i18n.folder_plural||'{n} folders');
        parts.push(dt.replace('{n}',result.dirs));
      }
      task.entryEl.textContent = parts.length ? parts.join(', ') : (i18n.empty||'empty');
    }
    if(row) row.dataset.info = 50000 + totalEntries;
    task.applied = true;
    if(currentSort === 'size' || currentSort === 'info') sortTable(currentSort, currentDir);
  }
  else if(r.type === 'info' && task.el){
    if(result.summary){
      task.el.textContent = result.summary;
      var row = task.el.closest('tr');
      if(row && result.sort_value !== undefined) row.dataset.info = result.sort_value;
    } else {
      task.el.textContent = '';
    }
    task.applied = true;
    if(currentSort === 'info') sortTable(currentSort, currentDir);
  }
  else if(r.type === 'thumb' && task.row){
    // Thumbnail generated — pre-warm the cache by marking it ready
    // Actual display happens on hover via api_thumb
    task.row.dataset.thumbReady = '1';
    task.applied = true;
  }
}

// Kick off!
collectAndEnqueue();

// --- Fetch and display download counts ---
function loadDownloadCounts(){
  var paths = [];
  getRows().forEach(function(r){
    if(r.dataset.isdir === '0') paths.push(r.dataset.path);
  });
  if(paths.length === 0) return;
  fetch('?'+addShare(new URLSearchParams({action:'api_dlcounts'})), {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({paths:paths})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if(!d.counts) return;
    getRows().forEach(function(r){
      if(r.dataset.isdir === '1') return;
      var count = d.counts[r.dataset.path];
      if(!count) return;
      var actionsDiv = r.querySelector('.actions');
      if(actionsDiv){
        var badge = actionsDiv.querySelector('.dl-count');
        if(!badge){ badge = document.createElement('span'); badge.className='dl-count'; actionsDiv.appendChild(badge); }
        badge.textContent = count + '×';
        badge.title = count + ' download' + (count !== 1 ? 's' : '');
      }
    });
  })
  .catch(function(){});
}
loadDownloadCounts();

// --- Queue status hover popup ---
queueStatusEl.addEventListener('mouseenter', function(){
  clearTimeout(queueHoverTimer);
  if(queueDetailPopup) queueDetailPopup.classList.add('active');
  loadQueueDetail();
});
queueStatusEl.addEventListener('mouseleave', function(){
  queueHoverTimer = setTimeout(function(){
    if(queueDetailPopup) queueDetailPopup.classList.remove('active');
  }, 200);
});

function loadQueueDetail(){
  if (shareToken || queueDetailLoading) return;
  queueDetailLoading = true;
  fetch('?'+addShare(new URLSearchParams({action:'api_queue_detail'})))
    .then(function(r){ return r.json(); })
    .then(function(d){
      queueDetailLoading = false;
      if(!queueDetailPopup) return;
      if(!d.tasks || d.tasks.length === 0){
        queueDetailPopup.innerHTML = '<div class="qd-title">'+(i18n.queue_no_tasks||'No active tasks')+'</div>';
        return;
      }
      var statusMsg = (i18n.queue_processing||'{running} processing, {pending} queued').replace('{running}',d.status.running||0).replace('{pending}',d.status.pending||0);
      var h = '<div class="qd-title">' + statusMsg + '</div>';
      d.tasks.forEach(function(t){
        h += '<div class="qd-row">';
        h += '<span class="qd-type">' + esc(t.type) + '</span>';
        h += '<span class="qd-name" title="' + esc(t.path) + '">' + esc(t.path) + '</span>';
        h += '<span class="qd-badge ' + t.status + '">' + t.status + '</span>';
        h += '</div>';
      });
      queueDetailPopup.innerHTML = h;
    })
    .catch(function(){ queueDetailLoading = false; });
}

// --- Hover popup for media detail (on-demand, not queued) ---
var activePopup = null;
var popupTimer = null;

function positionFixed(anchor, popup){
  var r = anchor.getBoundingClientRect();
  var vw = window.innerWidth, vh = window.innerHeight;
  // Clamp max-height so popup never exceeds viewport
  popup.style.maxHeight = (vh - 8) + 'px';
  popup.style.left = r.left + 'px';
  popup.style.top = (r.bottom + 4) + 'px';
  requestAnimationFrame(function(){
    var pr = popup.getBoundingClientRect();
    // Flip above anchor if overflows bottom
    if(pr.bottom > vh){
      var above = r.top - pr.height - 4;
      popup.style.top = (above >= 0 ? above : 4) + 'px';
    }
    // Constrain to viewport edges
    pr = popup.getBoundingClientRect();
    if(pr.top < 0) popup.style.top = '4px';
    pr = popup.getBoundingClientRect();
    if(pr.bottom > vh) popup.style.top = Math.max(4, vh - pr.height - 4) + 'px';
    if(pr.right > vw) popup.style.left = Math.max(4, vw - pr.width - 8) + 'px';
    pr = popup.getBoundingClientRect();
    if(pr.left < 0) popup.style.left = '4px';
  });
}

document.querySelectorAll('tr[data-media]').forEach(function(row){
  var tag = row.querySelector('.media-tag');
  var popup = row.querySelector('.media-popup');
  if(!tag || !popup) return;
  var loaded = false;

  tag.addEventListener('mouseenter', function(){
    clearTimeout(popupTimer);
    if(activePopup && activePopup !== popup){ activePopup.classList.remove('active'); }
    if(!loaded){
      var path = tag.dataset.path;
      popup.innerHTML = '<span style="color:var(--muted)">'+(i18n.loading||'Loading…')+'</span>';
      popup.classList.add('active');
      positionFixed(tag, popup);
      activePopup = popup;
      fetch('?'+addShare(new URLSearchParams({action:'api_detail',path:path})))
        .then(function(r){return r.json();})
        .then(function(d){ popup.innerHTML = renderDetail(d); loaded=true; positionFixed(tag, popup); })
        .catch(function(){ popup.innerHTML=(i18n.error_loading||'Error loading info'); });
    } else {
      popup.classList.add('active');
      positionFixed(tag, popup);
      activePopup = popup;
    }
  });

  tag.addEventListener('mouseleave', function(){
    popupTimer = setTimeout(function(){ popup.classList.remove('active'); activePopup=null; }, 200);
  });
  popup.addEventListener('mouseenter', function(){ clearTimeout(popupTimer); });
  popup.addEventListener('mouseleave', function(){ popup.classList.remove('active'); activePopup=null; });
});

// --- Thumbnail hover on name column for image/video ---
var thumbPopup = document.createElement('div');
thumbPopup.className = 'thumb-popup';
document.body.appendChild(thumbPopup);
var thumbCache = {};
var thumbTimer = null;

document.querySelectorAll('tr[data-media]').forEach(function(row){
  var cat = row.dataset.cat;
  if(cat !== 'image' && cat !== 'video' && cat !== 'document') return;
  var nc = row.querySelector('.namecell');
  if(!nc) return;
  var path = row.dataset.path;

  nc.addEventListener('mouseenter', function(){
    clearTimeout(thumbTimer);
    if(thumbCache[path]){
      showThumb(thumbCache[path], nc);
    } else if(thumbCache[path] !== false){
      // Try to load — will work if thumb is ready (queued or cached)
      thumbPopup.innerHTML = '<span style="padding:8px;color:var(--muted);font-size:12px">'+(i18n.loading||'Loading…')+'</span>';
      thumbPopup.classList.add('active');
      posThumb(nc);
      fetch('?'+addShare(new URLSearchParams({action:'api_thumb',path:path})))
        .then(function(r){ if(!r.ok) throw 0; return r.blob(); })
        .then(function(bl){ var url=URL.createObjectURL(bl); thumbCache[path]=url; showThumb(url, nc); })
        .catch(function(){ thumbCache[path]=false; thumbPopup.classList.remove('active'); });
    }
  });
  nc.addEventListener('mouseleave', function(){
    thumbTimer = setTimeout(function(){ thumbPopup.classList.remove('active'); }, 150);
  });
});

function showThumb(url, anchor){
  thumbPopup.innerHTML = '<img src="'+url+'">';
  thumbPopup.classList.add('active');
  posThumb(anchor);
}
function posThumb(anchor){
  var r = anchor.getBoundingClientRect();
  thumbPopup.style.left = (r.left + 20) + 'px';
  thumbPopup.style.top = (r.bottom + 4) + 'px';
  requestAnimationFrame(function(){
    var pr = thumbPopup.getBoundingClientRect();
    if(pr.bottom > window.innerHeight) thumbPopup.style.top = (r.top - pr.height - 4) + 'px';
    if(pr.right > window.innerWidth) thumbPopup.style.left = Math.max(4, window.innerWidth - pr.width - 8) + 'px';
  });
}

// --- Render helpers ---
function renderDetail(d){
  var h = '<h4>'+esc(d.name||'')+'</h4>';
  var det = d.detail;
  if(!det) return h+'<span style="color:var(--muted)">'+(i18n.no_info||'No info available')+'</span>';

  if(d.category === 'document'){
    if(det.format) h += mpRow(i18n.info_format, det.format);
    if(det.pages) h += mpRow(i18n.info_pages, det.pages);
    if(det.title) h += mpRow(i18n.info_title, det.title);
    if(det.author) h += mpRow(i18n.info_author, det.author);
    if(det.language) h += mpRow(i18n.info_language, det.language);
    if(det.creator) h += mpRow(i18n.info_creator, det.creator);
    if(det.producer) h += mpRow(i18n.info_producer, det.producer);
    if(det.pdf_version) h += mpRow(i18n.info_pdf_version, det.pdf_version);
    if(det.mime) h += mpRow(i18n.info_mime, det.mime);
    if(det.encrypted) h += mpRow(i18n.info_encrypted, det.encrypted);
    if(det.page_size) h += mpRow(i18n.info_page_size, det.page_size);
    if(det.creation_date) h += mpRow(i18n.info_created, det.creation_date);
  } else if(d.category === 'image'){
    if(det.width && det.height) h += mpRow(i18n.info_resolution, det.width+'\u00D7'+det.height);
    if(det.vector) h += mpRow(i18n.info_type, i18n.info_vector);
    else if(det.megapixels) h += mpRow(i18n.info_megapixels, det.megapixels+' MP');
    if(det.format) h += mpRow(i18n.info_format, det.format);
    if(det.color_mode) h += mpRow(i18n.info_color, det.color_mode + (det.bits ? ' '+det.bits+'bit' : ''));
    if(det.has_alpha) h += mpRow(i18n.info_alpha, i18n.info_yes);
    if(det.animated) h += mpRow(i18n.info_animated, i18n.info_yes);
  } else {
    if(det.duration) h += mpRow(i18n.info_duration, formatDur(det.duration));
    if(det.format) h += mpRow(i18n.info_container, det.format);
    if(det.overall_bitrate) h += mpRow(i18n.info_overall_bitrate, formatBr(det.overall_bitrate));
    if(det.streams && det.streams.length){
      det.streams.forEach(function(s){
        h += '<div class="stream">';
        h += '<strong style="color:var(--accent);text-transform:capitalize">'+esc(s.type==='video' ? i18n.info_video : i18n.info_audio)+'</strong>';
        if(s.format) h += ' \u2014 '+esc(s.format);
        if(s.language) h += ' ['+esc(s.language)+']';
        h += '<br>';
        if(s.type==='video'){
          if(s.width) h += mpRow(i18n.info_resolution, s.width+'\u00D7'+s.height);
          if(s.bitrate) h += mpRow(i18n.info_bitrate, formatBr(s.bitrate));
          if(s.framerate) h += mpRow(i18n.info_fps, s.framerate);
          if(s.color_space) h += mpRow(i18n.info_color, s.color_space.trim());
          if(s.bit_depth) h += mpRow(i18n.info_bit_depth, s.bit_depth);
        }
        if(s.type==='audio'){
          if(s.bitrate) h += mpRow(i18n.info_bitrate, formatBr(s.bitrate));
          if(s.sample_rate) h += mpRow(i18n.info_sample_rate, (parseInt(s.sample_rate)/1000)+'kHz');
          if(s.channels) h += mpRow(i18n.info_channels, s.channels);
          if(s.bit_depth) h += mpRow(i18n.info_bit_depth, s.bit_depth);
        }
        h += '</div>';
      });
    }
  }
  return h;
}

function mpRow(label,val){ return '<div class="mp-row"><span class="mp-label">'+esc(label)+'</span><span class="mp-val">'+esc(String(val))+'</span></div>'; }
function esc(s){ var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
function formatDur(s){ s=Math.round(s); var h=Math.floor(s/3600),m=Math.floor((s%3600)/60),sec=s%60; return h>0?(h+':'+pad(m)+':'+pad(sec)+' h'):(m+':'+pad(sec)+' min'); }
function pad(n){ return n<10?'0'+n:n; }
function formatBr(b){ var k=parseInt(b)/1000; return k>=1000?(Math.round(k/100)/10+' Mbps'):(Math.round(k)+' kbps'); }

// =========== LIVE DIRECTORY CHANGE DETECTION ===========
var dirCheckHash = null;
var dirCheckPath = <?php echo json_encode($currentRel); ?>;
var dirCheckBusy = false;

function formatSizeJS(bytes){
  if(bytes < 0) return '?';
  if(bytes < 1024) return bytes+' B';
  var u=['KiB','MiB','GiB','TiB'], i=-1;
  do { bytes/=1024; i++; } while(bytes>=1024 && i<u.length-1);
  return (Math.round(bytes*100)/100)+' '+u[i];
}
function formatDateJS(ts){
  if(!ts) return '-';
  var d=new Date(ts*1000);
  var fmt = i18n._dateFormatJS || 'YYYY-MM-DD HH:mm';
  var dd=String(d.getDate()).padStart(2,'0'), mm=String(d.getMonth()+1).padStart(2,'0');
  var hh=String(d.getHours()).padStart(2,'0'), mi=String(d.getMinutes()).padStart(2,'0');
  var ss=String(d.getSeconds()).padStart(2,'0');
  return fmt.replace('YYYY',d.getFullYear()).replace('MM',mm).replace('DD',dd).replace('HH',hh).replace('mm',mi).replace('ss',ss);
}

function buildRowHTML(it, scriptName){
  var safeName = esc(it.name);
  var rp = encodeURIComponent(it.path).replace(/%2F/gi,'/');
  var pathQ = 'path='+encodeURIComponent(it.path);
  if (shareToken) pathQ += '&s=' + shareToken;
  var vm = it.view || (it.is_dir ? null : getViewMode(it.name));
  var h = '<tr data-isdir="'+(it.is_dir?'1':'0')+'" data-name="'+esc(it.name)+'" data-size="'+it.size+'" data-mtime="'+it.mtime+'" data-info="'+(it.is_dir?'50000':'0')+'" data-cat="'+it.cat+'" data-path="'+esc(it.path)+'"';
  if(it.has_media) h += ' data-media="1"';
  if(vm) h += ' data-view="'+vm+'"';
  h += '>';
  h += '<td><div class="namecell"><span class="icon">'+it.icon+'</span>';
  if(it.is_dir){
    h += '<a href="?'+pathQ+'">'+safeName+'</a>';
  } else {
    h += '<a href="?'+pathQ+'&action=download"';
    if(vm==='modal') h += ' onclick="return openViewer(this)"';
    else if(vm==='tab') h += ' onclick="return openInTab(this)"';
    h += '>'+safeName+'</a>';
  }
  h += '</div></td>';
  h += '<td class="small size-cell">';
  if(it.is_dir) h += '<span class="dir-size" data-path="'+esc(it.path)+'">…</span>';
  else h += formatSizeJS(it.size);
  h += '</td>';
  h += '<td class="info-cell">';
  if(it.is_dir) h += '<span class="dir-entry" data-path="'+esc(it.path)+'">…</span>';
  else if(it.has_media) h += '<span class="media-tag" data-path="'+esc(it.path)+'">…</span><div class="media-popup"></div>';
  h += '</td>';
  h += '<td class="small">'+formatDateJS(it.mtime)+'</td>';
  h += '<td><div class="actions">';
  if(!it.is_dir){
    if(vm==='modal') h += '<a class="btn" href="#" onclick="return openViewerByPath(\''+esc(it.path).replace(/'/g,"\\'")+'\')">'+esc(i18n.view)+'</a>';
    else if(vm==='tab') h += '<a class="btn" href="?'+pathQ+'&action=api_stream" target="_blank">'+esc(i18n.view)+'</a>';
    h += '<a class="btn" href="?'+pathQ+'&action=download">'+esc(i18n.download)+'</a>';
  } else {
    h += '<a class="btn" href="?'+pathQ+'">'+esc(i18n.open)+'</a>';
    h += '<a class="btn" href="?'+pathQ+'&action=download">'+esc(i18n.zip)+'</a>';
  }
  if (!shareToken) {
    h += '<button class="btn" onclick="sharePath(\''+esc(it.path).replace(/'/g,"\\'")+'\')">'+esc(i18n.share)+'</button>';
  }
  h += '</div></td></tr>';
  return h;
}

function refreshDirectory(){
  if(dirCheckBusy || !table) return;
  dirCheckBusy = true;
  fetch('?'+addShare(new URLSearchParams({action:'api_dircheck',path:dirCheckPath})))
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(!d.hash){ dirCheckBusy=false; return; }
      if(dirCheckHash === null){ dirCheckHash = d.hash; dirCheckBusy=false; return; }
      if(d.hash === dirCheckHash){ dirCheckBusy=false; return; }
      // Hash changed — fetch full listing
      return fetch('?'+addShare(new URLSearchParams({action:'api_dirlist',path:dirCheckPath})))
        .then(function(r){ return r.json(); })
        .then(function(data){
          dirCheckHash = data.hash;
          dirCheckBusy = false;
          if(!data.items) return;
          applyDirectoryUpdate(data.items);
        });
    })
    .catch(function(){ dirCheckBusy=false; });
}

function applyDirectoryUpdate(items){
  if(!tbody) return;
  // Build map of current rows by name
  var existingRows = {};
  getRows().forEach(function(r){ existingRows[r.dataset.name] = r; });

  // Build map of new items by name
  var newItems = {};
  items.forEach(function(it){ newItems[it.name] = it; });

  // Remove rows not in new listing
  for(var name in existingRows){
    if(!newItems[name]){
      existingRows[name].remove();
    }
  }

  // Add or update rows
  var needsEnqueue = [];
  items.forEach(function(it){
    var existing = existingRows[it.name];
    if(existing){
      // Update size/mtime if changed
      if(String(it.size) !== existing.dataset.size){
        existing.dataset.size = it.size;
        var sc = existing.querySelector('.size-cell');
        if(sc && !it.is_dir) sc.textContent = formatSizeJS(it.size);
      }
      if(String(it.mtime) !== existing.dataset.mtime){
        existing.dataset.mtime = it.mtime;
        var cells = existing.querySelectorAll('td');
        if(cells[3]) cells[3].textContent = formatDateJS(it.mtime);
      }
    } else {
      // New item — insert into tbody
      var tmp = document.createElement('tbody');
      tmp.innerHTML = buildRowHTML(it);
      var newRow = tmp.firstChild;
      tbody.appendChild(newRow);
      // Wire up hover handlers for new row
      wireRowHandlers(newRow);
      needsEnqueue.push(it);
    }
  });

  // Re-sort with current settings
  sortTable(currentSort, currentDir);

  // Enqueue tasks for new items
  if(!shareToken && needsEnqueue.length > 0){
    var tasks = [];
    needsEnqueue.forEach(function(it){
      if(it.is_dir){
        var el = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"] .dir-size');
        if(el){
          var entryEl = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"] .dir-entry');
          var key = taskKey('dirsize', it.path);
          pendingTasks[key] = {type:'dirsize', path:it.path, applied:false, el:el, entryEl:entryEl};
          tasks.push({type:'dirsize', path:it.path});
        }
      }
      if(it.has_media){
        var tag = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"] .media-tag');
        if(tag){
          var key2 = taskKey('info', it.path);
          pendingTasks[key2] = {type:'info', path:it.path, applied:false, el:tag};
          tasks.push({type:'info', path:it.path});
        }
        if(it.cat==='image'||it.cat==='video'||it.cat==='document'){
          var row = tbody.querySelector('tr[data-path="'+CSS.escape(it.path)+'"]');
          if(row){
            var key3 = taskKey('thumb', it.path);
            pendingTasks[key3] = {type:'thumb', path:it.path, applied:false, row:row};
            tasks.push({type:'thumb', path:it.path});
          }
        }
      }
    });
    if(tasks.length > 0){
      fetch('?'+addShare(new URLSearchParams({action:'api_enqueue'})), {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({tasks:tasks})
      })
      .then(function(r){ return r.json(); })
      .then(function(d){ updateStatusUI(d.status); startWorkLoop(); })
      .catch(function(){});
    }
  }
}

function wireRowHandlers(row){
  // Media detail hover
  var tag = row.querySelector('.media-tag');
  var popup = row.querySelector('.media-popup');
  if(tag && popup){
    var loaded = false;
    tag.addEventListener('mouseenter', function(){
      clearTimeout(popupTimer);
      if(activePopup && activePopup !== popup){ activePopup.classList.remove('active'); }
      if(!loaded){
        popup.innerHTML = '<span style="color:var(--muted)">'+(i18n.loading||'Loading…')+'</span>';
        popup.classList.add('active');
        positionFixed(tag, popup);
        activePopup = popup;
        fetch('?'+addShare(new URLSearchParams({action:'api_detail',path:tag.dataset.path})))
          .then(function(r){return r.json();})
          .then(function(d){ popup.innerHTML = renderDetail(d); loaded=true; positionFixed(tag, popup); })
          .catch(function(){ popup.innerHTML=(i18n.error_loading||'Error loading info'); });
      } else {
        popup.classList.add('active'); positionFixed(tag, popup); activePopup = popup;
      }
    });
    tag.addEventListener('mouseleave', function(){
      popupTimer = setTimeout(function(){ popup.classList.remove('active'); activePopup=null; }, 200);
    });
    popup.addEventListener('mouseenter', function(){ clearTimeout(popupTimer); });
    popup.addEventListener('mouseleave', function(){ popup.classList.remove('active'); activePopup=null; });
  }
  // Thumb hover
  var cat = row.dataset.cat;
  if(cat==='image'||cat==='video'||cat==='document'){
    var nc = row.querySelector('.namecell');
    if(nc){
      var path = row.dataset.path;
      nc.addEventListener('mouseenter', function(){
        clearTimeout(thumbTimer);
        if(thumbCache[path]){ showThumb(thumbCache[path], nc); }
        else if(thumbCache[path] !== false){
          thumbPopup.innerHTML = '<span style="padding:8px;color:var(--muted);font-size:12px">'+(i18n.loading||'Loading…')+'</span>';
          thumbPopup.classList.add('active'); posThumb(nc);
          fetch('?'+addShare(new URLSearchParams({action:'api_thumb',path:path})))
            .then(function(r){ if(!r.ok) throw 0; return r.blob(); })
            .then(function(bl){ var url=URL.createObjectURL(bl); thumbCache[path]=url; showThumb(url, nc); })
            .catch(function(){ thumbCache[path]=false; thumbPopup.classList.remove('active'); });
        }
      });
      nc.addEventListener('mouseleave', function(){
        thumbTimer = setTimeout(function(){ thumbPopup.classList.remove('active'); }, 150);
      });
    }
  }
}

// =========== MEDIA VIEWER ===========
var viewerOverlay = document.getElementById('viewerOverlay');
var viewerContent = document.getElementById('viewerContent');
var viewerTitle = document.getElementById('viewerTitle');
var viewerDownload = document.getElementById('viewerDownload');
var viewerNewTab = document.getElementById('viewerNewTab');
var viewerClose = document.getElementById('viewerClose');
var viewerPrev = document.getElementById('viewerPrev');
var viewerNext = document.getElementById('viewerNext');
var viewerCurrentPath = null;
var viewerSiblings = []; // list of viewable paths in current directory for nav

function getViewMode(name){
  var ext = name.split('.').pop().toLowerCase();
  var modal = ['mp4','webm','ogv','mov','mp3','ogg','wav','flac','m4a','opus','aac',
    'jpg','jpeg','png','gif','bmp','svg','webp','ico','avif',
    'txt','md','log','csv','json','xml','html','css','js','py','php','sh','c','cpp','h','java','rs','go','rb','ts'];
  var tab = ['pdf'];
  if(modal.indexOf(ext) !== -1) return 'modal';
  if(tab.indexOf(ext) !== -1) return 'tab';
  return false;
}

function getFileType(name){
  var ext = name.split('.').pop().toLowerCase();
  if(['mp4','webm','ogv','mov'].indexOf(ext)!==-1) return 'video';
  if(['mp3','ogg','wav','flac','m4a','opus','aac'].indexOf(ext)!==-1) return 'audio';
  if(['jpg','jpeg','png','gif','bmp','svg','webp','ico','avif'].indexOf(ext)!==-1) return 'image';
  if(ext==='pdf') return 'pdf';
  return 'text';
}

function streamUrl(path){ return '?'+addShare(new URLSearchParams({action:'api_stream',path:path})); }
function downloadUrl(path){ return '?'+addShare(new URLSearchParams({action:'download',path:path})); }

function collectViewableSiblings(){
  var list = [];
  getRows().forEach(function(r){
    if(r.dataset.isdir==='1' || r.classList.contains('hidden')) return;
    var vm = r.dataset.view;
    if(vm === 'modal') list.push(r.dataset.path);
  });
  return list;
}

function showViewer(path){
  var name = path.split('/').pop();
  var type = getFileType(name);
  var url = streamUrl(path);

  viewerCurrentPath = path;
  viewerTitle.textContent = name;
  viewerDownload.href = downloadUrl(path);
  viewerNewTab.href = url;
  viewerContent.innerHTML = '';

  if(type === 'video'){
    var v = document.createElement('video');
    v.controls = true; v.autoplay = true; v.src = url;
    v.setAttribute('preload','metadata');
    viewerContent.appendChild(v);
  } else if(type === 'audio'){
    var a = document.createElement('audio');
    a.controls = true; a.autoplay = true; a.src = url;
    viewerContent.appendChild(a);
  } else if(type === 'image'){
    var img = document.createElement('img');
    img.src = url; img.alt = name;
    img.addEventListener('click', function(){ img.classList.toggle('zoomed'); });
    viewerContent.appendChild(img);
  } else if(type === 'pdf'){
    var ifr = document.createElement('iframe');
    ifr.src = url;
    viewerContent.appendChild(ifr);
  } else {
    // Text/code: fetch and display in <pre>
    var pre = document.createElement('pre');
    pre.textContent = i18n.loading||'Loading…';
    viewerContent.appendChild(pre);
    fetch(url).then(function(r){
      if(!r.ok) throw new Error('HTTP '+r.status);
      var ct = r.headers.get('content-length');
      // Limit text preview to ~2MB
      if(ct && parseInt(ct) > 2*1024*1024){
        pre.textContent = i18n.file_too_large||'File too large for text preview. Use "New Tab" or download.';
        return;
      }
      return r.text();
    }).then(function(t){
      if(t !== undefined) pre.textContent = t;
    }).catch(function(){
      pre.textContent = i18n.could_not_load||'Could not load file.';
    });
  }

  // Nav buttons
  viewerSiblings = collectViewableSiblings();
  updateNavButtons();

  viewerOverlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeViewer(){
  viewerOverlay.classList.remove('active');
  document.body.style.overflow = '';
  // Stop any playing media
  var v = viewerContent.querySelector('video');
  var a = viewerContent.querySelector('audio');
  if(v){ v.pause(); v.src=''; }
  if(a){ a.pause(); a.src=''; }
  viewerContent.innerHTML = '';
  viewerCurrentPath = null;
}

function updateNavButtons(){
  var idx = viewerSiblings.indexOf(viewerCurrentPath);
  viewerPrev.style.display = viewerSiblings.length > 1 ? '' : 'none';
  viewerNext.style.display = viewerSiblings.length > 1 ? '' : 'none';
  viewerPrev.disabled = idx <= 0;
  viewerNext.disabled = idx >= viewerSiblings.length - 1;
}

function navigateViewer(delta){
  var idx = viewerSiblings.indexOf(viewerCurrentPath);
  var newIdx = idx + delta;
  if(newIdx >= 0 && newIdx < viewerSiblings.length){
    showViewer(viewerSiblings[newIdx]);
  }
}

viewerClose.addEventListener('click', closeViewer);
viewerPrev.addEventListener('click', function(){ navigateViewer(-1); });
viewerNext.addEventListener('click', function(){ navigateViewer(1); });
viewerOverlay.addEventListener('click', function(e){
  // Close when clicking backdrop (not content)
  if(e.target === viewerOverlay || e.target === viewerContent) closeViewer();
});

document.addEventListener('keydown', function(e){
  if(!viewerOverlay.classList.contains('active')) return;
  if(e.key === 'Escape') closeViewer();
  else if(e.key === 'ArrowLeft') navigateViewer(-1);
  else if(e.key === 'ArrowRight') navigateViewer(1);
});

// Global functions called from onclick handlers
window.openViewer = function(anchor){
  var row = anchor.closest('tr');
  if(!row) return true;
  showViewer(row.dataset.path);
  return false;
};

window.openViewerByPath = function(path){
  showViewer(path);
  return false;
};

window.openInTab = function(anchor){
  var row = anchor.closest('tr');
  if(!row) return true;
  window.open(streamUrl(row.dataset.path), '_blank');
  return false;
};

window.sharePath = function(path) {
  var overlay = document.getElementById('shareOverlay');
  var pathDisplay = document.getElementById('sharePathDisplay');
  var confirmBtn = document.getElementById('shareConfirm');
  var cancelBtn = document.getElementById('shareCancel');
  var expiresSelect = document.getElementById('shareExpires');
  
  pathDisplay.textContent = path;
  overlay.classList.add('active');
  
  function closeShare() {
    overlay.classList.remove('active');
    confirmBtn.onclick = null;
    cancelBtn.onclick = null;
  }
  
  cancelBtn.onclick = closeShare;
  
  confirmBtn.onclick = function() {
    var expires = expiresSelect.value;
    fetch('?'+addShare(new URLSearchParams({action:'api_share_create',path:path,expires:expires})))
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          const baseUrl = window.location.href.split('?')[0].split('#')[0];
          const fullUrl = baseUrl + '?s=' + data.token;
          
          closeShare();
          
          if (navigator.clipboard) {
            navigator.clipboard.writeText(fullUrl).then(() => {
              alert('<?php echo t("share_copied"); ?>\n\n' + fullUrl);
            }).catch(() => {
              prompt('Copy share link:', fullUrl);
            });
          } else {
            prompt('Copy share link:', fullUrl);
          }
        } else {
          alert('Error: ' + (data.error || 'Unknown error'));
        }
      });
  };
};

setInterval(refreshDirectory, 10000);

})();
</script>
</body>
</html>
