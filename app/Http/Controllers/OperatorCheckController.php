<?php

namespace App\Http\Controllers;

use App\Models\EntitySecurityTask;
use App\Models\SecurityCheck;
use Illuminate\Http\Request;

class OperatorCheckController extends Controller
{
    public function show(EntitySecurityTask $entitySecurityTask)
    {
        $user = auth()->user();

        // Autorizzazione: l’utente deve appartenere all’ente
        abort_unless(
            $user->entities()
                ->where('entities.id', $entitySecurityTask->entity_id)
                ->exists(),
            403
        );

        $entitySecurityTask->load(['entity', 'securityTask', 'latestCheck']);

        return view('operator.check', compact('entitySecurityTask'));
    }

    public function store(Request $request, EntitySecurityTask $entitySecurityTask)
    {
        $user = auth()->user();

        abort_unless(
            $user->entities()
                ->where('entities.id', $entitySecurityTask->entity_id)
                ->exists(),
            403
        );

        $validated = $request->validate([
            'esito' => ['required', 'in:ok,ko,na'],
            'note' => ['nullable', 'string'],
        ]);

        SecurityCheck::create([
            'entity_security_task_id' => $entitySecurityTask->id,
            'esito' => $validated['esito'],
            'note' => $validated['note'] ?? null,
            'checked_at' => now(),
            'checked_by' => $user->id,
        ]);

        return redirect()
            ->route('operator.dashboard')
            ->with('success', 'Controllo registrato correttamente.');
    }
}
