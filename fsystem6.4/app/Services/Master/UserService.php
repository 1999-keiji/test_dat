<?php
declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\PageOverException;
use App\Mail\Master\UserCreateMail;
use App\Mail\Master\UserEditMail;
use App\Models\Master\User;
use App\Models\Master\Collections\PermissionCollection;
use App\Repositories\Master\PermissionRepository;
use App\Repositories\Master\UserRepository;
use App\Repositories\Master\UserFactoryRepository;
use App\Repositories\Master\UserPermissionRepository;
use App\ValueObjects\String\Password;

class UserService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\UserRepository
     */
    private $user_repo;

    /**
     * @var \App\Repositories\Master\PermissionRepository
     */
    private $permission_repo;

    /**
     * @var \App\Repositories\Master\UserPermissionRepository
     */
    private $user_permission_repo;

    /**
     * @var \App\Repositories\Master\UserFactoryRepository
     */
    private $user_factory_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\UserRepository $user_repositry
     * @param  \App\Repositories\Master\PermissionRepository $permission_repositry
     * @param  \App\Repositories\Master\UserPermissionRepository $user_permission_repositry
     * @param  \App\Repositories\Master\UserFactoryRepository $user_factory_repositry
     * @return void
     */
    public function __construct(
        Connection $db,
        UserRepository $user_repositry,
        PermissionRepository $permission_repositry,
        UserPermissionRepository $user_permission_repositry,
        UserFactoryRepository $user_factory_repositry
    ) {
        $this->db = $db;
        $this->user_repo = $user_repositry;
        $this->permission_repo = $permission_repositry;
        $this->user_permission_repo = $user_permission_repositry;
        $this->user_factory_repo = $user_factory_repositry;
    }

    /**
     * ユーザマスタの検索
     *
     * @param  array $params 検索条件
     * @param  int $page 表示ページ
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchUsers(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'user_code' => $params['user_code'] ?? null,
            'user_name' => $params['user_name'] ?? null,
            'affiliation' => $params['affiliation'] ?? null,
            'factory_code' => $params['factory_code'] ?? null
        ];

        $users = $this->user_repo->search($params);
        if ($page > $users->lastPage() && $users->lastPage() !== 0) {
            throw new PageOverException('target page does not exists.');
        }

        return $users;
    }

    /**
     * 指定された所属のデフォルト権限を取得する
     *
     * @param  int $affiliation
     * @return \App\Models\Master\Collections\PermissionCollection
     */
    public function getDefaultPermissions(int $affiliation): PermissionCollection
    {
        return $this->permission_repo->getDefaultPermissions($affiliation);
    }

    /**
     * 現行のユーザ権限を取得する
     *
     * @param  \App\Models\Master\User $user
     * @return \App\Models\Master\Collections\PermissionCollection
     */
    public function getCurrentPermissions(User $user): PermissionCollection
    {
        return $this->permission_repo->getCurrentPermissions($user);
    }

    /**
     * ユーザマスタの登録
     * 同時にユーザ権限マスタ、ユーザ工場マスタも登録
     *
     * @param  array $params
     * @return \App\Models\Master\User
     */
    public function createUser(array $params): User
    {
        return $this->db->transaction(function () use ($params) {
            $password = new Password();
            $user = $this->user_repo->create($params, $password->hashPassword());

            $this->user_permission_repo->create($user, $params['permissions']);
            $this->user_factory_repo->create($user, $params['factory_code'] ?? []);

            $params['password'] = $password->value();
            Mail::to($params['mail_address'])->send(new UserCreateMail($params));

            return $user;
        });
    }

    /**
     * ユーザマスタの更新
     * 紐づくユーザ権限マスタ、ユーザ工場マスタを削除して追加
     *
     * @param  \App\Models\Master\User $user
     * @param  array $params
     * @return \App\Models\Master\User $user
     */
    public function updateUser(User $user, array $params): User
    {
        return $this->db->transaction(function () use ($user, $params) {
            $user = $this->user_repo->update($user, $params);

            $this->user_permission_repo->delete($user);
            $this->user_permission_repo->create($user, $params['permissions']);

            $this->user_factory_repo->delete($user);
            $this->user_factory_repo->create($user, $params['factory_code'] ?? []);

            return $user;
        });
    }

    /**
     * ユーザマスタの削除
     * 紐づくユーザ権限マスタ、ユーザ工場マスタも削除
     *
     * @param  \App\Models\Master\User $user
     * @return void
     */
    public function deleteUser(User $user): void
    {
        $this->db->transaction(function () use ($user) {
            $this->user_permission_repo->delete($user);
            $this->user_factory_repo->delete($user);
            $user->delete();
        });
    }

    /**
     * パスワードリセット
     *
     * @param  \App\Models\Master\User $user
     * @param  string $password
     * @return \App\Models\Master\User
     */
    public function resetPassword(User $user, string $requested_password = ''): User
    {
        $password = new Password($requested_password);

        $user->password = $password->hashPassword();
        $user->save();

        if ($requested_password === '') {
            $user->password = $password->value();
            Mail::to($user->mail_address)->send(new UserEditMail($user));
        }

        return $user;
    }

    /**
     * API用にユーザーマスタを検索
     *
     * @param  array $params
     * @return array
     */
    public function searchUserForSearchingApi(array $params): array
    {
        return $this->user_repo->searchUserForSearchingApi($params)->all();
    }
}
