<?php

namespace YusufTogtay\Generator\Commands;

use YusufTogtay\Generator\Common\GeneratorConfig;
use YusufTogtay\Generator\Generators\API\APIControllerGenerator;
use YusufTogtay\Generator\Generators\API\APIRequestGenerator;
use YusufTogtay\Generator\Generators\API\APIRoutesGenerator;
use YusufTogtay\Generator\Generators\API\APITestGenerator;
use YusufTogtay\Generator\Generators\FactoryGenerator;
use YusufTogtay\Generator\Generators\MigrationGenerator;
use YusufTogtay\Generator\Generators\ModelGenerator;
use YusufTogtay\Generator\Generators\RepositoryGenerator;
use YusufTogtay\Generator\Generators\RepositoryTestGenerator;
use YusufTogtay\Generator\Generators\Scaffold\ControllerGenerator;
use YusufTogtay\Generator\Generators\Scaffold\MenuGenerator;
use YusufTogtay\Generator\Generators\Scaffold\RequestGenerator;
use YusufTogtay\Generator\Generators\Scaffold\RoutesGenerator;
use YusufTogtay\Generator\Generators\Scaffold\ViewGenerator;
use YusufTogtay\Generator\Generators\SeederGenerator;
use Symfony\Component\Console\Input\InputArgument;

class RollbackGeneratorCommand extends BaseCommand
{
    public GeneratorConfig $config;

    protected $name = 'infyom:rollback';

    protected $description = 'Rollback a full CRUD API and Scaffold for given model';

    public function handle()
    {
        $this->config = app(GeneratorConfig::class);
        $this->config->setCommand($this);
        $this->config->init();

        $type = $this->argument('type');
        if (!in_array($type, ['api', 'scaffold', 'api_scaffold'])) {
            $this->error('Invalid rollback type');

            return 1;
        }

        $this->fireFileDeletingEvent($type);
        $views = $this->option('views');
        if (!empty($views)) {
            $views = explode(',', $views);
            $viewGenerator = new ViewGenerator();
            $viewGenerator->rollback($views);

            $this->info('Generating autoload files');
            $this->composer->dumpOptimized();
            $this->fireFileDeletedEvent($type);

            return 0;
        }

        $migrationGenerator = app(MigrationGenerator::class);
        $migrationGenerator->rollback();

        $modelGenerator = app(ModelGenerator::class);
        $modelGenerator->rollback();

        if ($this->config->options->repositoryPattern) {
            $repositoryGenerator = app(RepositoryGenerator::class);
            $repositoryGenerator->rollback();
        }

        if (in_array($type, ['api', 'api_scaffold'])) {
            $requestGenerator = app(APIRequestGenerator::class);
            $requestGenerator->rollback();

            $controllerGenerator = app(APIControllerGenerator::class);
            $controllerGenerator->rollback();

            $routesGenerator = app(APIRoutesGenerator::class);
            $routesGenerator->rollback();
        }

        if (in_array($type, ['scaffold', 'api_scaffold'])) {
            $requestGenerator = app(RequestGenerator::class);
            $requestGenerator->rollback();

            $controllerGenerator = app(ControllerGenerator::class);
            $controllerGenerator->rollback();

            $viewGenerator = app(ViewGenerator::class);
            $viewGenerator->rollback();

            $routeGenerator = app(RoutesGenerator::class);
            $routeGenerator->rollback();

            $menuGenerator = app(MenuGenerator::class);
            $menuGenerator->rollback();
        }

        if ($this->config->options->tests) {
            $repositoryTestGenerator = app(RepositoryTestGenerator::class);
            $repositoryTestGenerator->rollback();

            $apiTestGenerator = app(APITestGenerator::class);
            $apiTestGenerator->rollback();
        }

        if ($this->config->options->factory or $this->config->options->tests) {
            $factoryGenerator = app(FactoryGenerator::class);
            $factoryGenerator->rollback();
        }

        if ($this->config->options->seeder) {
            $seederGenerator = app(SeederGenerator::class);
            $seederGenerator->rollback();
        }

        $this->info('Generating autoload files');
        $this->composer->dumpOptimized();

        $this->fireFileDeletedEvent($type);

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'Singular Model name'],
            ['type', InputArgument::REQUIRED, 'Rollback type: (api / scaffold / api_scaffold)'],
        ];
    }
}
