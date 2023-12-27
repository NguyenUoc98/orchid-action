<?php
/**
 * Created by PhpStorm
 * Filename: Action.php
 * User: Nguyễn Văn Ước
 * Date: 09/11/2023
 * Time: 17:27
 */

namespace Uocnv\OrchidAction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Uocnv\OrchidAction\Requests\ActionRequest;

abstract class Action
{
    public static function init(array $parameters = []): ?Button
    {
        return (new static)->getButton()?->method('action')
            ->parameters(array_merge(
                $parameters,
                ['_action' => self::name()]
            ));
    }

    protected string $permission = '';

    /**
     * The button of the action.
     *
     * @return Button
     */
    abstract protected function button(): Button;

    /**
     * Perform the action on the given models.
     *
     * @param Request $request
     */
    abstract public function handle(Request $request);

    /**
     * Unique method string to use in the request
     *
     * @return string
     */
    public static function name(): string
    {
        return Str::of(static::class)->replace('\\', '-')->slug();
    }

    public function hasPermission(): bool
    {
        return Auth::user()->hasAccess($this->permission);
    }

    public function getButton(): ?Button
    {
        if ($this->hasPermission()) {
            return $this->button();
        }
        return null;
    }
}