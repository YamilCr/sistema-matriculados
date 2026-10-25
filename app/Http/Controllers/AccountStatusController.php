<?php

namespace App\Http\Controllers;

use App\Models\AccountStatus;
use Inertia\Inertia;

/**
 * Los estados de cuenta son datos fijos (Al día, Moroso, Suspendido):
 * solo se consultan. Se asignan a cada matriculado desde una lista desplegable.
 */
class AccountStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('AccountStatuses/Index', [
            'statuses' => AccountStatus::withCount('members')->orderBy('id')->get(),
        ]);
    }
}
