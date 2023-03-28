<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Species;
use App\Models\Master\SpeciesConverter;
use App\Models\Master\Collections\SpeciesConverterCollection;

class SpeciesConverterRepository
{
    /**
     * @var \App\Models\Master\SpeciesConverter
     */
    private $model;

    /**
     * @param \App\Models\Master\SpeciesConverter $model
     * @return void
     */
    public function __construct(SpeciesConverter $model)
    {
        $this->model = $model;
    }

    /**
     * 仕入先コードと適用開始日により一意のレコードを取得
     *
     * @param  string $product_large_category
     * @param  string $product_middle_category
     */
    public function getSpeciesConverter($product_large_category, $product_middle_category)
    {
        return $this->model
            ->where('product_large_category', $product_large_category)
            ->where('product_middle_category', $product_middle_category)
            ->first();
    }

    /**
     * 品種変換マスタの登録
     *
     * @param  \App\Models\Master\Species $species
     * @param  array $species_converters
     * @return void
     */
    public function create(Species $species, array $species_converters): void
    {
        foreach ($species_converters as $sc) {
            $this->model->create([
                'species_code' => $species->species_code,
                'product_large_category' => $sc['product_large_category'],
                'product_middle_category' => $sc['product_middle_category']
            ]);
        }
    }

    /**
     * 品種変換マスタの削除
     *
     * @param  \App\Models\Master\Species $species
     * @return void
     */
    public function delete(Species $species): void
    {
        $species->species_converters->each(function ($sc) {
            $sc->delete();
        });
    }
}
