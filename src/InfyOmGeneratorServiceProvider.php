<?php

namespace YusufTogtay\Generator;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use YusufTogtay\Generator\Commands\API\APIControllerGeneratorCommand;
use YusufTogtay\Generator\Commands\API\APIGeneratorCommand;
use YusufTogtay\Generator\Commands\API\APIRequestsGeneratorCommand;
use YusufTogtay\Generator\Commands\API\TestsGeneratorCommand;
use YusufTogtay\Generator\Commands\APIScaffoldGeneratorCommand;
use YusufTogtay\Generator\Commands\Common\MigrationGeneratorCommand;
use YusufTogtay\Generator\Commands\Common\ModelGeneratorCommand;
use YusufTogtay\Generator\Commands\Common\RepositoryGeneratorCommand;
use YusufTogtay\Generator\Commands\Publish\GeneratorPublishCommand;
use YusufTogtay\Generator\Commands\Publish\PublishTablesCommand;
use YusufTogtay\Generator\Commands\Publish\PublishUserCommand;
use YusufTogtay\Generator\Commands\RollbackGeneratorCommand;
use YusufTogtay\Generator\Commands\Scaffold\ControllerGeneratorCommand;
use YusufTogtay\Generator\Commands\Scaffold\RequestsGeneratorCommand;
use YusufTogtay\Generator\Commands\Scaffold\ScaffoldGeneratorCommand;
use YusufTogtay\Generator\Commands\Scaffold\ViewsGeneratorCommand;
use YusufTogtay\Generator\Common\FileSystem;
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

class InfyOmGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $configPath = __DIR__.'/../config/laravel_generator.php';
            $this->publishes([
                $configPath => config_path('laravel_generator.php'),
            ], 'laravel-generator-config');

            $this->publishes([
                __DIR__.'/../views' => resource_path('views/vendor/laravel-generator'),
            ], 'laravel-generator-templates');
        }

        $this->registerCommands();
        $this->loadViewsFrom(__DIR__.'/../views', 'laravel-generator');

        View::composer('*', function ($view) {
            $view->with(['config' => app(GeneratorConfig::class)]);
        });

        Blade::directive('tab', function () {
            return '<?php echo infy_tab() ?>';
        });

        Blade::directive('tabs', function ($count) {
            return "<?php echo infy_tabs($count) ?>";
        });

        Blade::directive('nl', function () {
            return '<?php echo infy_nl() ?>';
        });

        Blade::directive('nls', function ($count) {
            return "<?php echo infy_nls($count) ?>";
        });
    }

    private function registerCommands()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            APIScaffoldGeneratorCommand::class,

            APIGeneratorCommand::class,
            APIControllerGeneratorCommand::class,
            APIRequestsGeneratorCommand::class,
            TestsGeneratorCommand::class,

            MigrationGeneratorCommand::class,
            ModelGeneratorCommand::class,
            RepositoryGeneratorCommand::class,

            GeneratorPublishCommand::class,
            PublishTablesCommand::class,
            PublishUserCommand::class,

            ControllerGeneratorCommand::class,
            RequestsGeneratorCommand::class,
            ScaffoldGeneratorCommand::class,
            ViewsGeneratorCommand::class,

            RollbackGeneratorCommand::class,
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel_generator.php', 'laravel_generator');

        $this->app->singleton(GeneratorConfig::class, function () {
            return new GeneratorConfig();
        });

        $this->app->singleton(FileSystem::class, function () {
            return new FileSystem();
        });

        $this->app->singleton(MigrationGenerator::class);
        $this->app->singleton(ModelGenerator::class);
        $this->app->singleton(RepositoryGenerator::class);

        $this->app->singleton(APIRequestGenerator::class);
        $this->app->singleton(APIControllerGenerator::class);
        $this->app->singleton(APIRoutesGenerator::class);

        $this->app->singleton(RequestGenerator::class);
        $this->app->singleton(ControllerGenerator::class);
        $this->app->singleton(ViewGenerator::class);
        $this->app->singleton(RoutesGenerator::class);
        $this->app->singleton(MenuGenerator::class);

        $this->app->singleton(RepositoryTestGenerator::class);
        $this->app->singleton(APITestGenerator::class);

        $this->app->singleton(FactoryGenerator::class);
        $this->app->singleton(SeederGenerator::class);
    }
}
