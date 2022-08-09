<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_models', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('outer_id')->unique()->comment('внешний ID');
            $table->bigInteger('mark_id');
            $table->bigInteger('color_id');
            $table->bigInteger('body_type_id');
            $table->bigInteger('engine_type_id');
            $table->bigInteger('transmission_id');
            $table->bigInteger('gear_type_id');
            $table->string('name')->comment('Наименование');
            $table->string('slug')->comment('slug (для поиска)');
            $table->string('generation')->nullable()->comment('Поколение');
            $table->integer('year')->comment('Год произовдства');
            $table->string('outer_generation_id')->nullable()->comment('Внешний ID поколения');
            $table->bigInteger('run')->comment('Мощность');
            $table->integer('row_version')->default(1)->comment('Контроль дублей');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_models');
    }
};
