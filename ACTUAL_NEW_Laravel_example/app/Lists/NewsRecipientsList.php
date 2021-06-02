<?php
/**
 * Project qpartners
 * Created by danila 24.06.2020 @ 2:48
 */

namespace App\Lists;

use App\Models\News;

class NewsRecipientsList
{
    public static function getList()
    {
        return [
            News::NEWS_SEND_TO_ALL_USERS => 'Все',
            News::NEWS_SEND_TO_USER_BY_CATEGORY => 'Определенной категории партнеров',
            News::NEWS_SEND_TO_USER_BY_IDS => 'Выбранным партнерам',
            News::NEWS_SEND_TO_USER_BY_IDS_EXCLUDE => 'Всем партнерам, кроме выбранных',
            News::NEWS_SEND_TO_USER_BY_TAG => 'Партнерам с тегом',
        ];
    }
}
