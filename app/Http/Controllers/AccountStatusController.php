<?php

namespace App\Http\Controllers;

use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AccountStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('AccountStatuses/Index', [
            'statuses' => AccountStatus::withCount('members')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        AccountStatus::create($this->validated($request));

        return redirect()->back()->with('message', 'Estado creado.');
    }

    public function update(Request $request, AccountStatus $accountStatus)
    {
        $accountStatus->update($this->validated($request, $accountStatus));

        return redirect()->back()->with('message', 'Estado actualizado.');
    }

    public function destroy(AccountStatus $accountStatus)
    {
        if ($accountStatus->members()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: hay matriculados con este estado.');
        }

        $accountStatus->delete();

        return redirect()->back()->with('message', 'Estado eliminado.');
    }

    private function validated(Request $request, ?AccountStatus $status = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('account_statuses', 'name')->ignore($status?->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
