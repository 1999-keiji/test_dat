<?php

declare(strict_types=1);

namespace App\Observers;

use App\Exceptions\OptimisticLockException;
use App\Models\Model;

class UpdatedDatetimeObserver
{
    /**
     * @return void
     * @throws OptimisticLockException
     */
    public function updating(Model $model): void
    {
        if ((string)$model->getAttribute('updated_at') !== $model->getOriginal('updated_at')) {
            throw new OptimisticLockException('interuptted by ' . $model->getOriginal('updated_by'));
        }
    }
}
