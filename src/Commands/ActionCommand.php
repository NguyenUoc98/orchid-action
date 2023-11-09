<?php

namespace Uocnv\OrchidAction\Commands;

use Illuminate\Console\GeneratorCommand;
use Uocnv\OrchidAction\ActionFinder;

class ActionCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'orchid-action:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/action.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Actions\Orchid';
    }

    public function handle()
    {
        $result = parent::handle();
        app(ActionFinder::class)->build();
        return $result;
    }
}
