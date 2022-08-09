<?php

namespace App\Services;

use App\Models\AutoModel;
use App\Models\BodyType;
use App\Models\Color;
use App\Models\EngineType;
use App\Models\GearType;
use App\Models\Mark;
use App\Models\Transmisson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogService
{
    private array $marks;

    private array $colors;

    private array $bodyTypes;

    private array $engineTypes;

    private array $gearTypes;

    private array $transmissions;

    private int $rowVersion;

    private const RELATIONS = [
        'mark' => [
            'model' => Mark::class,
            'field' => 'marks'
        ],
        'color' => [
            'model' => Color::class,
            'field' => 'colors'
        ],
        'body-type' => [
            'model' => BodyType::class,
            'field' => 'bodyTypes'
        ],
        'engine-type' => [
            'model' => EngineType::class,
            'field' => 'engineTypes'
        ],
        'gear-type' => [
            'model' => GearType::class,
            'field' => 'gearTypes'
        ],
        'transmission' => [
            'model' => Transmisson::class,
            'field' => 'transmissions'
        ],
    ];

    /**
     * Подготовка данных для быстрого маппинга
     */
    private function prepareRelations(): void
    {
        $this->marks = Mark::select('id', 'slug')->get()->pluck('id', 'slug')->toArray();
        $this->colors = Color::select('id', 'slug')->get()->pluck('id', 'slug')->toArray();
        $this->bodyTypes = BodyType::select('id', 'slug')->get()->pluck('id', 'slug')->toArray();
        $this->engineTypes = EngineType::select('id', 'slug')->get()->pluck('id', 'slug')->toArray();
        $this->gearTypes = GearType::select('id', 'slug')->get()->pluck('id', 'slug')->toArray();
        $this->transmissions = Transmisson::select('id', 'slug')->get()->pluck('id', 'slug')->toArray();
        $first = AutoModel::first();
        $this->rowVersion = $first == null ? 1 : $first->row_version;
    }

    /**
     * Парсер данных из xml-файла каталога
     */
    public function getRawDataFromXMLFile(string $file): array
    {
        try {
            $rawData = Storage::disk('public')->get($file);
            if ($rawData == null) {
                return [
                       'error' => true,
                       'errorMessage' => 'Файл не найден'
                   ];
            }
            $data = [];
            foreach (simplexml_load_string($rawData)->offers->offer as $offer) {
                $data[] = (array)$offer;
            }
        } catch (\Throwable $th) {
            return [
                'error' => true,
                'errorMessage' => $th->getMessage()
            ];
        }

        return [
            'error' => false,
            'data' => $data
        ];
    }

    /**
     * Добавление записи отношения в таблицу
     */
    private function addNewRelations(array $row): void
    {
        foreach (self::RELATIONS as $key => $relation) {
            if (isset($this->{$relation['field']}[Str::slug($row[$key])])
                || $row[$key] == null
                || $row[$key] == ''
            ) {
                continue;
            }

            $model = new ($relation['model']);
            $model->name = $row[$key];
            $model->slug = Str::slug(trim($row[$key]));
            $model->save();

            $this->{$relation['field']}[$model->slug] = $model->id;
        }
    }

    /**
     * Обрабатываем сырые данные
     */
    public function setRawData(array $data): bool
    {
        try {
            $this->prepareRelations();
            $upsert = [];
            foreach ($data as $row) {
                $this->addNewRelations($row);
                $upsert[] = [
                    'outer_id' => $row['id'],
                    'mark_id' => $this->marks[Str::slug($row['mark'])] ?? $this->marks['Not Set'],
                    'color_id' => $this->colors[Str::slug($row['color'])] ?? $this->marks['Not Set'],
                    'body_type_id' => $this->bodyTypes[Str::slug($row['body-type'])] ?? $this->marks['Not Set'],
                    'engine_type_id' => $this->engineTypes[Str::slug($row['engine-type'])] ?? $this->marks['Not Set'],
                    'transmission_id' => $this->transmissions[Str::slug($row['transmission'])] ?? $this->marks['Not Set'],
                    'gear_type_id' => $this->gearTypes[Str::slug($row['gear-type'])] ?? $this->marks['Not Set'],
                    'name' => $row['model'],
                    'slug' => Str::slug($row['model']),
                    'generation' => $row['generation'],
                    'year' => $row['year'],
                    'outer_generation_id' => $row['generation_id'],
                    'run' => $row['run'],
                    'row_version' => $this->rowVersion + 1,
                ];
            }

            $this->upsertNewData($upsert);

            return true;
        } catch (\Throwable $th) {
            Log::error(sprintf('Ошибка обработки данных - %s', $th->getMessage()), ['error' => $th]);

            return false;
        }
    }

    /**
     * Вставка данных в БД
     *
     * @return void
     */
    public function upsertNewData(array $data)
    {
        if ($this->rowVersion > 1) {
            DB::table('auto_models')
                ->where('row_version', $this->rowVersion)
                ->delete();
        }

        foreach (array_chunk($data, 1000) as $chunk) {
            DB::table('auto_models')->upsert(
                $chunk,
                ['outer_id'],
                [
                    'outer_id',
                    'mark_id',
                    'color_id',
                    'body_type_id',
                    'engine_type_id',
                    'transmission_id',
                    'gear_type_id',
                    'name',
                    'slug',
                    'generation',
                    'year',
                    'outer_generation_id',
                    'run',
                    'row_version'
                ]
            );
        }
    }
}
