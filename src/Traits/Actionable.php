<?php
/**
 * Created by PhpStorm
 * Filename: Actionable.php
 * User: Nguyễn Văn Ước
 * Date: 09/11/2023
 * Time: 16:15
 */

namespace Uocnv\OrchidAction\Traits;

use Illuminate\Http\Request;
use Uocnv\OrchidAction\Action;
use Uocnv\OrchidAction\ActionFinder;

trait Actionable
{
    public function action(Request $request)
    {
        /** @var Action $action */
        $finder = app(ActionFinder::class);
        $action = $finder->find($request->query('_action'));
        $action = $action ?: $finder->build()->find($request->query('_action'));
        $action = is_string($action) ? resolve($action) : $action;

        if (!$action->hasPermission()) {
            abort(403);
        }

        return $action->handle($request);
    }
}
