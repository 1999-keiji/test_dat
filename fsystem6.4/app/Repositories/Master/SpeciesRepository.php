<?php

declare(strict_types=1);

namespace App\Repositories\Master;

use App\Models\Master\Species;
use App\Models\Master\Collections\SpeciesCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class SpeciesRepository
{
    /**
     * @var \App\Models\Master\Species
     */
    private $model;

    /**
     * @param  \App\Models\Master\Species $species
     * @return void
     */
    public function __construct(Species $model)
    {
        $this->model = $model;
    }

    /**
     * 品種マスタを取得
     *
     * @param  string $species_code
     * @return \App\Models\Master\Species
     */
    public function find(string $species_code): Species
    {
        return $this->model->find($species_code);
    }

    /**
     * すべての品種マスタを取得
     *
     * @return \App\Models\Master\Collections\SpeciesCollection
     */
    public function all(): SpeciesCollection
    {
        return $this->model
            ->select([
                'species_code',
                'species_name',
                'species_abbreviation',
                'remark'
            ])
            ->orderBy('species_code', 'ASC')
            ->get();
    }

    /**
     * 品種マスタ一覧の検索
     *
     * @param  array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(array $params): LengthAwarePaginator
    {
        return $this->model->select([
            'species_code',
            'species_name',
            'species_abbreviation',
            'remark',
            'updated_at'
        ])
            ->where(function ($query) use ($params) {
                if ($species_code = $params['species_code']) {
                    $query->where('species_code', $species_code);
                }
            })
            ->where(function ($query) use ($params) {
                if ($species_name = $params['species_name']) {
                    $query->where('species_name', 'LIKE', "%{$species_name}%")
                        ->orWhere('species_abbreviation', 'LIKE', "%{$species_name}%");
                }
            })
            ->sortable(['species_code' => 'ASC'])
            ->paginate();
    }

    /**
     * 品種マスタの登録
     *
     * @param  array $params
     * @return \App\Models\Master\Species
     */
    public function create(array $params): Species
    {
        return $this->model->create([
            'species_code' => $params['species_code'],
            'species_name' => $params['species_name'],
            'species_abbreviation' => $params['species_abbreviation'],
            'remark' => $params['remark'] ?: '',
        ]);
    }

    /**
     * 品種マスタの更新
     *
     * @param  \App\Models\Master\Species
     * @param  array $params
     * @return \App\Models\Master\Species
     */
    public function update(Species $species, array $params): Species
    {
        $species->fill([
            'species_name' => $params['species_name'],
            'species_abbreviation' => $params['species_abbreviation'],
            'remark' => $params['remark'] ?: ''
        ])
            ->save();

        return $species;
    }
}
