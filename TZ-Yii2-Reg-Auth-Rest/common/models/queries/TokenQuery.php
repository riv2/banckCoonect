<?php

namespace common\models\queries;

use yii\db\ActiveQuery;

/**
 * Active query для сущности токена.
 */
class TokenQuery extends ActiveQuery
{
    /**
     * Устанавливает условие для выборки токена по коду.
     * @param string $code
     * @return TokenQuery
     */
    public function byCode(string $code): TokenQuery
    {
        return $this->andWhere('code = :code', [':code' => $code]);
    }
}
