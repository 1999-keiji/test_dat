<?php

declare(strict_types=1);

namespace App\Services\Master;

use Illuminate\Database\Connection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Master\Species;
use App\Models\Master\Collections\SpeciesCollection;
use App\Repositories\Master\SpeciesRepository;
use App\Repositories\Master\SpeciesConverterRepository;

class SpeciesService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $db;

    /**
     * @var \App\Repositories\Master\SpeciesRepository
     */
    private $species_repo;

    /**
     * @var \App\Repositories\Master\SpeciesConverterRepository
     */
    private $species_converter_repo;

    /**
     * @param  \Illuminate\Database\Connection $db
     * @param  \App\Repositories\Master\SpeciesRepository $species_repositry
     * @param  \App\Repositories\Master\SpeciesConverterRepository $species_converter_repository
     * @return void
     */
    public function __construct(
        Connection $db,
        SpeciesRepository $species_repositry,
        SpeciesConverterRepository $species_converter_repository
    ) {
        $this->db = $db;
        $this->species_repo = $species_repositry;
        $this->species_converter_repo = $species_converter_repository;
    }

    /**
     * 品種マスタを取得
     *
     * @param  string $species_code
     * @return \App\Models\Master\Species
     */
    public function find(string $species_code): Species
    {
        return $this->species_repo->find($species_code);
    }

    /**
     * すべての品種マスタを取得
     *
     * @return \App\Models\Master\Collections\SpeciesCollection
     */
    public function getAllSpecies(): SpeciesCollection
    {
        return $this->species_repo->all();
    }

    /**
     * すべての品種マスタをJSON形式のオプションに変更
     *
     * @return string
     */
    public function getAllSpeciesAsJsonOptions(): string
    {
        return $this->getAllSpecies()->toJsonOptions();
    }

    /**
     * 品種マスタの検索
     *
     * @param  array $params 検索条件
     * @param  int $page 表示ページ
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws \App\Exceptions\PageOverException
     */
    public function searchSpecies(array $params, int $page): LengthAwarePaginator
    {
        $params = [
            'species_code' => $params['species_code'] ?? null,
            'species_name' => $params['species_name'] ?? null
        ];

        $species = $this->species_repo->search($params);
        if ($page > $species->lastPage() && $species->lastPage() !== 0) {
            throw new PageOverException('target page does not exist.');
        }

        return $species;
    }

    /**
     * 品種マスタの登録
     * 同時に品種変換マスタも登録
     *
     * @param  array $params
     * @return \App\Models\Master\Species
     */
    public function createSpecies(array $params): Species
    {
        return $this->db->transaction(function () use ($params) {
            $species = $this->species_repo->create($params);
            $this->species_converter_repo->create($species, $params['species_converters']);

            return $species;
        });
    }

    /**
     * 品種マスタの更新
     * 同時に品種変換マスタも登録
     *
     * @param  \App\Models\Master\Species $species
     * @param  array $params
     * @return \App\Models\Master\Species
     */
    public function updateSpecies(Species $species, array $params): Species
    {
        return $this->db->transaction(function () use ($species, $params) {
            $species = $this->species_repo->update($species, $params);

            $this->species_converter_repo->delete($species);
            $this->species_converter_repo->create($species, $params['species_converters']);

            return $species;
        });
    }

    /**
     * 品種マスタの削除
     * 紐づく品種変換マスタも削除
     *
     * @param  \App\Models\Master\Species $species
     * @return void
     */
    public function deleteSpecies(Species $species): void
    {
        $this->db->transaction(function () use ($species) {
            $this->species_converter_repo->delete($species);
            $species->delete();
        });
    }
}
