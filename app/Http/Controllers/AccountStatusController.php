<?php

namespace App\Http\Controllers;

use App\Models\AccountStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('AccountStatuses/Index', [
            'statuses' => AccountStatus::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:account_statuses',
            'description' => 'nullable|string',
        ]);

        AccountStatus::create($validated);
        return redirect()->back()->with('message', 'Estado creado.');
    }

    public function update(Request $request, AccountStatus $accountStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:account_statuses,name,' . $accountStatus->id,
            'description' => 'nullable|string',
        ]);

        $accountStatus->update($validated);
        return redirect()->back()->with('message', 'Estado actualizado.');
    }
}