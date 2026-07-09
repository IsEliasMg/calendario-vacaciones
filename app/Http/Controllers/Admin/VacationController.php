<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Vacation\Actions\DeleteVacationAction;
use App\Domain\Vacation\Models\Vacation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VacationController extends Controller
{
    public function destroy(Vacation $vacation, DeleteVacationAction $action): JsonResponse
    {
        $this->authorize('delete', $vacation);

        $action->execute($vacation);

        return response()->json(['message' => 'Vacación eliminada correctamente.']);
    }
}
