<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\User;
use App\Models\Master\Collections\UserCollection;

class UserRepository
{
    /**
     * @var \App\Models\Master\User
     */
    private $model;

    /**
     *
     * @param \App\Models\Master\User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * ユーザマスタの検索
     *
     * @param  array $params
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params): LengthAwarePaginator
    {
        return $this->model
            ->select(['user_code', 'user_name', 'affiliation'])
            ->where(function ($query) use ($params) {
                if ($user_code = $params['user_code']) {
                    $query->where('user_code', $user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($user_name = $params['user_name']) {
                    $query->where('user_name', 'LIKE', "%{$user_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($affiliation = $params['affiliation']) {
                    $query->where('affiliation', $affiliation);
                }
            })
            ->where(function ($query) use ($params) {
                if ($factory_code = $params['factory_code']) {
                    $query->whereIn('user_code', function ($query) use ($factory_code) {
                        $query->select('user_code')
                            ->from('user_factories')
                            ->where('factory_code', $factory_code);
                    });
                }
            })
            ->with(['user_factories', 'user_factories.factory'])
            ->sortable(['user_code' => 'ASC'])
            ->paginate();
    }

    /**
     * ユーザマスタの登録
     *
     * @param  array $params
     * @param  string $hased_password
     * @return \App\Models\Master\User
     */
    public function create(array $params, string $hased_password): User
    {
        return $this->model->create([
            'user_code' => $params['user_code'],
            'user_name' => $params['user_name'],
            'affiliation' => $params['affiliation'],
            'mail_address' => $params['mail_address'],
            'password' => $hased_password
        ]);
    }

    /**
     * ユーザマスタの更新
     *
     * @param  \App\Models\Master\User $corporation
     * @param  array $params
     * @return \App\Models\Master\User $corporation
     */
    public function update(User $user, array $params): User
    {
        $user->fill([
            'user_name' => $params['user_name'],
            'affiliation' => $params['affiliation'],
            'mail_address' => $params['mail_address']
        ])
            ->save();

        return $user;
    }

    /**
     * 選択した条件から一致するユーザーを検索
     *
     * @param  array $params
     * @return \App\Models\Master\Collections\UserCollection
     */
    public function searchUserForSearchingApi(array $params): UserCollection
    {
        return $this->model
            ->select([
                'users.user_code',
                'users.user_name',
                'users.affiliation',
                'users.mail_address'
            ])
            ->where(function ($query) use ($params) {
                if ($user_code = $params['user_code'] ?? null) {
                    $query->where('user_code', $user_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($user_name = $params['user_name'] ?? null) {
                    $query->where('user_name', 'LIKE', "%{$user_name}%");
                }
            })
            ->where(function ($query) use ($params) {
                if ($affiliation = $params['affiliation'] ?? null) {
                    $query->where('affiliation', $affiliation);
                }
            })
            ->where(function ($query) use ($params) {
                if ($mail_address = $params['mail_address'] ?? null) {
                    $query->where('mail_address', $mail_address);
                }
            })
            ->orderBy('users.user_code', 'ASC')
            ->get();
    }
}
