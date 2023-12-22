<?php
/**
 * Created by PhpStorm
 * Filename: ActionFinder.php
 * User: Nguyễn Văn Ước
 * Date: 09/11/2023
 * Time: 17:45
 */

namespace Uocnv\OrchidAction;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class ActionFinder
{
    protected array $manifest = [];

    public function __construct(
        protected Filesystem $files,
        protected string $manifestPath,
        protected string $path
    ) {
    }

    public function find($alias)
    {
        try {
            $manifest = $this->getManifest();

            return $manifest[$alias] ?? null;
        } catch (FileNotFoundException|Exception) {
            return null;
        }
    }

    /**
     * @throws Exception
     */
    public function build(): static
    {
        $this->manifest = $this->getClassNames()
            ->mapWithKeys(function ($class) {
                return [$class::name() => $class];
            })->toArray();

        $this->write($this->manifest);

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function write(array $manifest): void
    {
        if (!is_writable(dirname($this->manifestPath))) {
            throw new Exception('The ' . dirname($this->manifestPath) . ' directory must be present and writable.');
        }

        $this->files->put($this->manifestPath, '<?php return ' . var_export($manifest, true) . ';', true);
    }

    public function getClassNames(): Collection
    {
        if (!$this->files->exists($this->path)) {
            return collect();
        }

        return collect($this->files->allFiles($this->path))
            ->map(function (SplFileInfo $file) {
                return app()->getNamespace() .
                    str($file->getPathname())
                        ->after(app_path() . '/')
                        ->replace(['/', '.php'], ['\\', ''])->__toString();
            })
            ->filter(function (string $class) {
                return is_subclass_of($class, Action::class) &&
                    !(new ReflectionClass($class))->isAbstract();
            });
    }

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function getManifest()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (!file_exists($this->manifestPath)) {
            $this->build();
        }

        return $this->manifest = $this->files->getRequire($this->manifestPath);
    }
}