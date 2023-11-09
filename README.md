# Orchid Action

Added action buttons with permissions to the list panel for Orchid

## Installation

You can install the package via composer:

```bash
composer require uocnv/orchid-action
```

## Usage

### Create an action

```php
php artisan orchid-action:make CustomAction
```

By default, all actions are placed in the app/Actions/Orchid directory. The action necessarily consists of two methods and permission. Method button defines name, icon, dialog box, etc. And the handler method directly handles the action.

```php
namespace App\Actions\Orchid;

use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Toast;
use Uocnv\OrchidAction\Action;

class CustomAction extends Action
{
    protected string $permission = 'action.custom';

    /**
     * The button of the action.
     *
     * @return Button
     */
    public function button(): Button
    {
        return Button::make('Run Custom Action')->icon('bs.fire');
    }

    /**
     * Perform the action on the given models.
     *
     * @param Request $request
     */
    public function handle(Request $request)
    {
        Toast::message('It worked!');
    }
}
```

Within the `handle` method, you may perform whatever tasks are necessary to complete the action.

And then in Layout, add Action to use

```php
...
TD::make('Action')
    ->alignCenter()
    ->render(function (User $user) {
        return ActiveUser::init([
            'userId' => $user->use_id,
            'type'   => ActiveStatus::INACTIVE
        ])?->cansee(!is_null($user->deleted_at));
    })
...
```

In Screen must use `Actionable` trait
```php
namespace App\Http\Controllers\Screens;

use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Uocnv\OrchidAction\Traits\Actionable;

class Idea extends Screen
{
    use Actionable;
    
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query() : array
    {
        return [];
    }

    /**
     * The name of the screen is displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return "Idea Screen";
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout() : array
    {
        return [];
    }
}
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email uocnv.soict.hust@gmail.com instead of using the issue tracker.

## Credits

-   [Nguyễn Văn Ước](https://github.com/uocnv)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
