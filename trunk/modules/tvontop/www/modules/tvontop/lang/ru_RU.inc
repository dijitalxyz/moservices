<?php
require_once 'interfaces/l10n.inc';

class Locale_ru_RU extends Localization {
    function Locale_ru_RU() {
        $this->lang ='ru_RU';

        $this->messages = array(
            '_TEST_' => 'Test %3$d,%2$d, %1$d русский',
            'EPG_TITLE' => ' - Программа передач за %1$s',
            'Default template' => 'Шаблон по умолчанию. Используется только при разработке!',
            'Our folders' => 'Наши папки',
            'Available Services' => 'Доступные сервисы',
            'All Categories' => 'Все категории',
            'Change account' => 'Cменить аккаунт',
            'Season' => 'Сезон',
            'Episode' => 'Эпизод',
            'All' => 'Все',
            'Favorites' => 'Избранные',
            'Last added' => 'Новые',
            'Favorite added' => 'Добавлено в избранные',
            'Favorite not added' => 'Не добавлено в избранные',
            'Favorite removed' => 'Удалено из избранных',
            'Favorite not removed' => 'Не удалено из избранных',
            'Error' => 'Ошибка',
            'Message' => 'Сообщение',
            'Wrong password' => 'Неверный пароль',
            'Add to favorites' => 'В избранные',
            'Remove from favorites' => 'Удалить из избранных',
            'Film list' => 'Список фильмов',
            'Page' => 'Страница',
            'To page' => 'К странице',
            'Jump to page' => 'Переход к странице %1$d/%2$d, записям %3$d-%4$d/%5$d',
            'Search' => 'Поиск',
            'Genres' => 'Жанры',
            'Authorization error' => 'Ошибка входа',
            'Quality' => 'Качество',
            'year' => 'Год',
            'length' => 'Время',
            'Lang1' => 'Язык 1',
            'Lang2' => 'Язык 2',
            'rating' => 'Рейтинг',
            'genre' => 'Жанр',
            'country' => 'Страна',
            'director' => 'Режиссер',
            'scenario' => 'Сценарий',
            'actors' => 'В ролях',
            'studio' => 'Студия',
            'awards' => 'Награды',
            'budget' => 'Бюджет',
            'images' => 'Картинки',
            'ktv_CHANNELGROUP_1_descr' => 'Каналы общего содержания: Первый, ORT International, Россия 1, РТР-Планета, НТВ, ТВЦ, РенТВ, ТНТ, СТС International, ДТВ, Домашний, 5 канал Петербург, World Fashion',
        	'new_version_title' => 'Доступна новая версия.',
        	'new_version_msg' => 'Версия %1$s доступна для скачивания на http://tvontop.com. Пожалуйста обновите плагин.',
        	'Best' => 'Лучшие',
        	'Loading video...' => 'Загрузка видео...',
        	'Watch' => 'Смотреть',
        	'Protected channel password:' => 'Пароль для закрытого канала:'
        );
    }
}
?>
