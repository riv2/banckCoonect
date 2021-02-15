<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class QrCode
 * @package App
 * @property int id
 * @property int teacher_id
 * @property int discipline_id
 * @property string code
 * @property int numeric_code
 * @property string meta
 * @property int expire_sec
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class QrCode extends Model
{
    protected $table = 'qr_codes';

    public static $adminTeachers = [15380, 8981, 4873, 4883, 16646, 8601, 19065];

    /** @var int Timeout in seconds */
    protected static $timeout = 15;

    /**
     * @param array $data
     * @param null $expireSec
     * @return array
     */
    static function generate($data = []) : array
    {
        $code = str_random(50);
        $numericCode = rand(100000, 999999);

        $model = new self();
        $model->code = $code;
        $model->numeric_code = $numericCode;
        $model->meta = base64_encode(json_encode($data));
        $model->expire_sec = self::$timeout;
        $model->teacher_id = $data['teacher_id'] ?? null;
        $model->save();

        return [$code, $numericCode];
    }

    /**
     * @param string $code
     * @return QrCode|null
     */
    static function get($code) : ?self
    {
        return self
            ::where('code', $code)
            ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, "' . date('Y-m-d H:i:s', time()) . '") <= expire_sec')
            ->first();
    }

    /**
     * @param int $numericCode
     * @return QrCode|null
     */
    static function getByNumericCode(int $numericCode) : ?self
    {
        return self::where('numeric_code', $numericCode)
            ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, "' . date('Y-m-d H:i:s', time()) . '") <= expire_sec')
            ->first();
    }

    /**
     * @param int $teacherId
     * @param int $disciplineId
     * @return array
     */
    static function generateCode(int $teacherId, int $disciplineId) : array
    {
        self::deleteOld($teacherId, $disciplineId);

        $code = str_random(60);
        $numericCode = rand(100000, 999999);

        QrCode::insert([
            'teacher_id' => $teacherId,
            'discipline_id' => $disciplineId,
            'code' => $code,
            'numeric_code' => $numericCode,
            'created_at' => DB::raw('now()')
        ]);

        return [$code, $numericCode];
    }

    public static function isValid(int $disciplineId, string $code) : bool
    {
        return self::where('discipline_id', $disciplineId)
            ->where('code', $code)
            ->exists();
    }

    public static function isNumericCodeValid(int $disciplineId, int $code) : bool
    {
        return self
            ::where('discipline_id', $disciplineId)
            ->where('numeric_code', $code)
            ->exists();
    }

    private static function deleteOld(int $teacherId, int $disciplineId) : void
    {
        self::where('teacher_id', $teacherId)
            ->where('discipline_id', $disciplineId)
            ->whereRaw('(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`created_at`)) > ' . self::$timeout)
            ->delete();
    }
}
