<?php

namespace App\Console\Commands;

use App\Services\CatalogService;
use Illuminate\Console\Command;

class Parser extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запускает парсера каталога с xml';

    private $defaultFile = 'data_light.xml';

    private CatalogService $catalogService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CatalogService $catalogService)
    {
        $this->signature = 'tasks:xml {--F|file= : файл для загрузки} {--L|link : ссылка для загрузки}';

        parent::__construct();

        $this->catalogService = $catalogService;
    }



    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $file = $this->option('file') ? $this->option('file') : $this->defaultFile;
        $this->info(sprintf('Начинаем парсить файл %s', $file));

        $rawData = $this->catalogService->getRawDataFromXMLFile($file);
        if ($rawData['error']) {
            $this->error(sprintf('Ошибка парсинга файла - %s', $rawData['errorMessage']));

            return 1;
        }

        if (empty($rawData['data'])) {
            $this->info('Не получилось спарсить данные, проеверьте структуру файла.');

            return 1;
        }

        $this->info('Обрабатываем данные');
        if ($this->catalogService->setRawData($rawData['data'])) {
            $this->info('Обработано');

            return 0;
        }
        $this->info('Ошибка обработки. Подробности в логе');

        return 1;
    }
}
