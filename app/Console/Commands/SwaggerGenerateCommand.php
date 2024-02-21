<?php

namespace App\Console\Commands;

use App\Services\Swagger\SwaggerService;
use Illuminate\Console\Command;
use OpenApi\Generator;

class SwaggerGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates changes in public/swagger.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $openApi = Generator::scan([SwaggerService::SCAN_PATH]);

        file_put_contents(
            public_path() . '/' . SwaggerService::FILENAME,
            $openApi->toJson()
        );

        $this->info('Swagger UI was successfully updated.');
    }
}
