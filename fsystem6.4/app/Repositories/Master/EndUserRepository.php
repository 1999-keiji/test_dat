<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\EndUser;
use App\Models\Master\Collections\EndUserCollection;
use App\ValueObjects\String\EndUserCode;

class EndUserRepository
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Models\Master\EndUser
     */
    private $model;

    /**
     * @param \Illuminate\Database\Connection $db
     * @param \App\Models\Master\EndUser $model
     * @return void
     */
    public function __construct(Connection $db, EndUser $model)
    {
        $this->db = $db;
        $this->model = $model;
    }

    /**
     * エンドユーザコードでマスタを取得
     *
     * @param  \App\ValueObjects\String\EndUserCode $end_user_code
     * @return \App\Models\Master\Collections\EndUserCollection
     */
    public function getendUsersByEndUserCode(EndUserCode $end_user_code): EndUserCollection
    {
        return $this->model->where('end_user_code', $end_user_code)->get();
    }

    /**
     * 既存エンドユーザ検索
     *
     * @param  \App\ValueObjects\String\EndUserCode $end_user_code
     * @return \App\Models\Master\Collections\EndUserCollection
     */
    public function getExistingEndUsers(): EndUserCollection
    {
        return $this->model
            ->select(['end_user_code'])
            ->groupBy('end_user_code')
            ->orderBy('end_user_code')
            ->get();
    }

    /**
     * エンドユーザを条件に応じて検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($params): LengthAwarePaginator
    {
        $end_user_query = $this->model
            ->select([
                'end_users.end_user_code',
                'end_users.application_started_on',
                'customers.customer_code',
                'customers.customer_abbreviation',
                'end_user_name',
                'end_users.address',
                'end_users.phone_number',
                'creating_type'
            ])
            ->join('customers', 'end_users.customer_code', '=', 'customers.customer_code')
            ->where(function ($query) use ($params) {
                if ($customer_code = $params['customer_code']) {
                    $query->where('end_users.customer_code', $customer_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($customer_name = $params['customer_name']) {
                    $query->where('customers.customer_name', 'LIKE', "%{$customer_name}%")
                        ->orWhere('customers.customer_name2', 'LIKE', "%{$customer_name}%")
                        ->orWhere('customers.customer_abbreviation', 'LIKE', "%{$customer_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_code = $params['end_user_code']) {
                    $query->where('end_users.end_user_code', $end_user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_name = $params['end_user_name']) {
                    $query->where('end_user_name', 'LIKE', "%{$end_user_name}%")
                        ->orWhere('end_user_name2', 'LIKE', "%{$end_user_name}%")
                        ->orWhere('end_user_abbreviation', 'LIKE', "%{$end_user_name}%");
                }
            })
            ->sortable('customer_abbreviation');

        if (! $params['past_flag']) {
            $sub_query = $this->getApplicationEndUserQuery();
            $end_user_query->join($this->db->raw("({$sub_query}) as current"), function ($join) {
                $join->on('end_users.end_user_code', '=', 'current.end_user_code')
                    ->on('end_users.application_started_on', '=', 'current.application_started_on');
            });
        }

        return $end_user_query->paginate();
    }

    /**
     * 一意エンドユーザの検索
     *
     * @param $end_user_code
     * @param $application_started_on
     * @return \App\Models\Master\EndUser|null
     */
    public function searchPrimary($end_user_code, $application_started_on)
    {
        return $this->model
            ->where('end_user_code', $end_user_code)
            ->where('application_started_on', $application_started_on)
            ->first();
    }

    /**
     * エンドユーザマスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\EndUser
     */
    public function create(array $params): EndUser
    {
        unset($params['selected_master'], $params['delivery_destination_name']);
        return $this->model->create(array_filter($params, 'is_not_null'));
    }

    /**
     * エンドユーザマスタの更新
     *
     * @param  \App\Models\Master\EndUser $end_user
     * @param  array $params
     * @return \App\Models\Master\EndUser $end_user
     */
    public function update(EndUser $end_user, array $params): EndUser
    {
        unset($params['selected_master'], $params['delivery_destination_name']);

        $end_user->fill(array_filter($params, 'is_not_null'))->save();
        return $end_user;
    }

    /**
     * 各エンドユーザコードごとに、現時点で適用されるデータを取得
     *
     * @param  array $end_user_code_list
     * @return \App\Models\Master\Collections\EndUserCollection
     */
    public function getCurrentApplicatedEndUsers(?array $end_user_code_list = [])
    {
        $sub_query = $this->getApplicationEndUserQuery();
        return $this->model
            ->select([
                'end_users.end_user_code',
                'end_users.end_user_name',
                'end_users.end_user_abbreviation'
            ])
            ->join(
                $this->db->raw("({$sub_query}) AS current"),
                function ($join) {
                    $join->on('end_users.end_user_code', '=', 'current.end_user_code')
                        ->on('end_users.application_started_on', '=', 'current.application_started_on');
                }
            )
            ->where(function ($query) use ($end_user_code_list) {
                if (count($end_user_code_list) !== 0) {
                    $query->whereIn('end_users.end_user_code', $end_user_code_list);
                }
            })
            ->get();
    }

    /**
     * 指定された日付時点で適用されるエンドユーザの情報を取得
     *
     * @param  string $end_user_code
     * @param  string $application_started_on
     * @return \App\Models\Master\EndUser
     */
    public function getApplicatedEndUser(string $end_user_code, ?string $application_started_on = null): ?EndUser
    {
        $date = 'CURRENT_DATE';
        if (! is_null($application_started_on)) {
            $date = "'{$application_started_on}'";
        }

        return $this->model
            ->select([
                'end_user_code',
                'customer_code',
                'end_user_name',
                'end_user_abbreviation',
                'seller_name',
                'statement_of_delivery_class'
            ])
            ->where('end_user_code', $end_user_code)
            ->whereRaw("application_started_on <= {$date}")
            ->orderBy('application_started_on', 'DESC')
            ->first();
    }

    /**
     * API用にエンドユーザマスタを条件に応じて検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\EndUserCollection
     */
    public function searchForSearchingApi($params): EndUserCollection
    {
        $sub_query = $this->getApplicationEndUserQuery();
        $query = $this->model
            ->select([
                'end_users.end_user_code',
                'end_users.end_user_abbreviation',
                'end_users.address',
                'end_users.phone_number'
            ])
            ->join($this->db->raw("({$sub_query}) as current"), function ($join) {
                $join->on('end_users.end_user_code', '=', 'current.end_user_code')
                    ->on('end_users.application_started_on', '=', 'current.application_started_on');
            })
            ->where(function ($query) use ($params) {
                if ($customer_code = $params['customer_code']) {
                    $query->where('end_users.customer_code', $customer_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_code = $params['end_user_code']) {
                    $query->where('end_users.end_user_code', $end_user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_name = $params['end_user_name']) {
                    $query->where('end_user_name', 'LIKE', "%{$end_user_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_name2 = $params['end_user_name2']) {
                    $query->where('end_user_name2', 'LIKE', "%{$end_user_name2}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_abbreviation = $params['end_user_abbreviation']) {
                    $query->where('end_user_abbreviation', 'LIKE', "%{$end_user_abbreviation}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($end_user_name_kana = $params['end_user_name_kana']) {
                    $query->where('end_user_name_kana', 'LIKE', "%{$end_user_name_kana}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($address = $params['address']) {
                    $query->where('end_users.address', 'LIKE', "%{$address}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($phone_number = $params['phone_number']) {
                    $query->where('end_users.phone_number', 'LIKE', "%{$phone_number}%");
                }
            })
            ->orderBy('end_users.end_user_code', 'ASC')
            ->limit(EndUser::API_SEARCHING_LIMIT);

        if ($factory_code = $params['factory_code']) {
            $query->join('end_user_factories', function ($join) use ($factory_code) {
                $join->on('end_users.end_user_code', '=', 'end_user_factories.end_user_code')
                    ->where('end_user_factories.factory_code', $factory_code);
            });
        }

        return $query->get();
    }

    /**
     * エンドユーザコードごとにマスタを集約するためのクエリを取得
     *
     * @param  string $application_started_on
     * @return \Illuminate\Database\Query\Expression
     */
    private function getApplicationEndUserQuery(?string $application_started_on = null): Expression
    {
        $date = 'CURRENT_DATE';
        if (! is_null($application_started_on)) {
            $date = "'{$application_started_on}'";
        }

        return $this->db->raw(
            'SELECT end_user_code, MAX(application_started_on) AS application_started_on '.
            'FROM end_users '.
            "WHERE application_started_on <= {$date} ".
            'AND (base_plus_delete_flag = FALSE OR base_plus_delete_flag IS NULL) '.
            'GROUP BY end_user_code'
        );
    }
}
