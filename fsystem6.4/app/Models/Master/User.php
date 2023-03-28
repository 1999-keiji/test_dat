<?php

declare(strict_types=1);

namespace App\Models\Master;

use BadMethodCallException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Model;
use App\Models\Master\Collections\UserCollection;
use App\Models\Master\Collections\FactoryCollection;
use App\Traits\AuthorObservable;
use App\Traits\UpdatedDatetimeObservable;
use App\ValueObjects\Enum\Affiliation;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable,
        Authorizable,
        Sortable,
        AuthorObservable,
        UpdatedDatetimeObservable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_code';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    public $sortbale = ['user_code', 'user_name', 'affiliation', 'factory'];

    /**
     * @return null
     */
    public function getRememberToken()
    {
        return null; // not supported
    }

    /**
     * @return void
     */
    public function setRememberToken($value)
    {
        // not supported
    }

    /**
     * @return null
     */
    public function getRememberTokenName()
    {
        return null; // not supported
    }

    /**
     * Overrides the method to ignore the remember token.
     */
    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (! $isRememberTokenAttribute) {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \App\Models\Master\Collections\UserCollection
     */
    public function newCollection(array $models = []): UserCollection
    {
        return new UserCollection($models);
    }

    /**
     * 画面にアクセスが可能かどうか判定する
     *
     * @param  string $path
     * @return bool
     */
    public function canAccess(string $path): bool
    {
        return $this->user_permissions->canAccess($path);
    }

    /**
     * データ保存が可能かどうか判定する
     *
     * @param  string $path
     * @return bool
     */
    public function canSave(string $path): bool
    {
        return $this->user_permissions->canSave($path);
    }

    /**
     * 工場所属のユーザであるか判定する
     *
     * @return bool
     */
    public function belongsToFactory(): bool
    {
        return $this->affiliation->belongsToFactory();
    }

    /**
     * 所属する工場の情報を取得
     *
     * @return \App\Models\Master\Collections\FactoryCollection;
     * @throws \BadMethodCallException
     */
    public function getAffilicatedFactories(): FactoryCollection
    {
        if (! $this->belongsToFactory()) {
            throw new BadMethodCallException('This user is not belongs to factory.');
        }

        return $this->user_factories->toFactoryCollection();
    }

    /**
     * ユーザに紐づく権限を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_code');
    }

    /**
     * ユーザに紐づく工場を取得
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_factories(): HasMany
    {
        return $this->hasMany(UserFactory::class, 'user_code');
    }

    /**
     * @return \App\ValueObjects\Enum\Affiliation
     */
    public function getAffiliationAttribute($value): Affiliation
    {
        return new Affiliation($value);
    }

    /**
     * ユーザ工場によるソート
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function factorySortable(Builder $query, string $direction): Builder
    {
        $order = UserFactory::select('user_code')
            ->orderBy('factory_code', $direction)
            ->orderBy('user_code', 'ASC')
            ->get()
            ->pluck('user_code')
            ->unique()
            ->reduce(function ($order, $user_code) {
                return $order .= ",'{$user_code}'";
            }, 'FIELD (user_code');

        return $query
            ->orderByRaw('(CASE WHEN `affiliation` = ? THEN 1 WHEN `affiliation` = ? THEN 2 ELSE 3 END) ASC', [
                Affiliation::FACTORY, Affiliation::FACTORY_OTHER
            ])
            ->orderByRaw($order.')');
    }
}
