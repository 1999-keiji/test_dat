<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use Illuminate\Database\Connection;
use Cake\Chronos\Chronos;
use App\Models\Order\AssignNumber;

class AssignNumberRepository
{
    /**
     * @var string
     */
    private const ORDER_NUMBER_INITIAL = 'F';

    /**
     * @var string
     */
    private const ORDER_NUMBER_SHARING_INITIAL = 'B';

    /**
     * @var string
     */
    private const ORDER_NUMBER_FORMAT = '%04d';

    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Order\AssignNumber
     */
    private $model;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Models\Order\AssignNumber $model
     * @return void
     */
    public function __construct(Connection $db, AssignNumber $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * 採番取得
     *
     * @param  string $table_name
     * @param  string $symbolic_code
     * @param  bool $sharing_flag
     * @return string
     */
    public function getAssignedNumber(string $table_name, string $symbolic_code, bool $sharing_flag = false): string
    {
        $today = Chronos::today();
        $assign_number = $this->model
            ->where('table_name', $table_name)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        $assigned_number = 1;
        if (is_null($assign_number)) {
            $this->model->create([
                'table_name' => $table_name,
                'date' => $today->format('Y-m-d'),
                'assign_number' => $assigned_number
            ]);
        }
        if (! is_null($assign_number)) {
            $this->db->statement(
                "UPDATE {$this->model->getTable()} SET assign_number = LAST_INSERT_ID(assign_number + 1), ".
                "updated_by = 'BATCH', updated_at = NOW() ".
                "WHERE table_name = '{$table_name}' AND date = '{$today->format('Y-m-d')}'"
            );

            $assigned_number = $this->model->selectRaw('LAST_INSERT_ID() AS assign_number')
                ->where('table_name', $table_name)
                ->where('date', $today->format('Y-m-d'))
                ->first()
                ->assign_number;
        }

        return implode('', [
            $sharing_flag ? self::ORDER_NUMBER_SHARING_INITIAL : self::ORDER_NUMBER_INITIAL,
            $symbolic_code,
            $today->format('ymd'),
            sprintf(self::ORDER_NUMBER_FORMAT, $assigned_number)
        ]);
    }
}
