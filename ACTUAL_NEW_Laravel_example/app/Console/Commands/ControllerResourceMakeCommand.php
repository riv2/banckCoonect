<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str;

class ControllerResourceMakeCommand extends ControllerMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:controller:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает новый класс контроллера ресурса';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return  app_path() . '/Console/Commands/Stubs/controller.model.stub';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if (!$this->option('model') || !is_subclass_of('\\App\\Models\\' . $this->option('model'), \Illuminate\Database\Eloquent\Model::class)) {
            $modelClass = $this->askWithCompletion('Выберите модель, которая будет использоваться в контроллере', function () {
                return collect(\Illuminate\Support\Facades\File::allFiles(app_path('Models')))->map(function ($item) {
                    $rel   = $item->getRelativePathName();
                    $class = implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.'))));
                    return class_exists('\App\Models\\' . $class) ? $class : null;
                })->filter(function ($model) {
                    return !Str::startsWith($model, 'Base\\') && !is_subclass_of('\\App\\Models\\' . $model, \Illuminate\Database\Eloquent\Model::class);
                })->toArray();
            });
            $modelClass = '\\App\\Models\\' . $modelClass;
            $this->input->setOption('model', $modelClass);
        }

        $controllerNamespace = $this->getNamespace($name);
        $role_name = strtolower(class_basename($controllerNamespace));
        $route_name = strtolower(str_replace('Controller', '', class_basename($name)));
        $class_name = class_basename($name);

        $replace = [];
        $replace = $this->buildModelReplacements($replace);
        $replace['{{ viewFolderVariable }}'] = sprintf('%s.%s', $role_name, $route_name);
        $replace['{{viewFolderVariable}}'] = sprintf('%s.%s', $role_name, $route_name);

        $this->line('');
        $this->info('Добавь в файл роутинга следующую строку:');
        $this->comment("Route::resource('" . $route_name . "', '" . $class_name . "')->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);");
        $this->line('');

        return str_replace(array_keys($replace), array_values($replace), GeneratorCommand::buildClass($name));
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
        ];
    }
}
