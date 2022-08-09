<?php

namespace Database\Seeders;

use App\Models\BodyType;
use App\Models\Color;
use App\Models\EngineType;
use App\Models\GearType;
use App\Models\Mark;
use App\Models\Transmisson;
use Illuminate\Database\Seeder;

class DefaultSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classes = [
            Mark::class,
            Color::class,
            BodyType::class,
            EngineType::class,
            GearType::class,
            Transmisson::class,
        ];

        foreach ($classes as $class) {
            $model = new ($class);
            $model->name = 'Не указано';
            $model->slug = 'Not Set';
            $model->save();
        }
    }
}
