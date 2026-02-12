<?php

namespace App\Http\Controllers;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use Illuminate\Http\Request;

class OperatorQuickCheckController extends Controller
{
    public function store(Request $request, EntitySecurityTask $entitySecurityTask)
    {
        $request->validate([
            'esito' => 'required|in:ok,ko,na',
        ]);

        // Controllo sicurezza: l'utente deve appartenere all'ente
        if (!auth()->user()->entities->contains($entitySecurityTask->entity_id)) {
            abort(403);
        }

        SecurityCheck::create([
            'entity_security_task_id' => $entitySecurityTask->id,
            'esito' => $request->esito,
            'note' => $request->note,
            'checked_at' => now(),
            'checked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Controllo registrato.');
    }
}
