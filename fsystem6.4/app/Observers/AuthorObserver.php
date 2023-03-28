<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Auth\AuthManager;
use App\Models\Model;

class AuthorObserver
{
    /**
     * @var string
     */
    private $user_code;

    /**
     * @return void
     */
    public function __construct(AuthManager $auth)
    {
        $this->user_code = $auth->id() ?: Model::BATCH_USER;
    }

    /**
     * @return void
     */
    public function creating(Model $model): void
    {
        $model->created_by = $this->user_code;
        $model->updated_by = $this->user_code;
    }

    /**
     * @return void
     */
    public function updating(Model $model): void
    {
        $model->updated_by = $this->user_code;
    }
}
