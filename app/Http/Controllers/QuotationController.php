<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuotationController extends Controller
{
    /**
     * Download or stream quotation as PDF.
     */
    public function downloadPdf(Request $request, Project $project)
    {
        Gate::authorize('view', $project);

        // Ensure relations are loaded
        $project->load(['user', 'items.module']);

        // Recalculate grand total to ensure precision
        $grandTotal = $project->items->sum('calculated_price');
        if ((float) $project->grand_total !== (float) $grandTotal) {
            $project->grand_total = $grandTotal;
            $project->saveQuietly();
        }

        $pdf = Pdf::loadView('pdf.quotation', [
            'project' => $project,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Quotation-' . str_pad($project->id, 5, '0', STR_PAD_LEFT) . '-' . str($project->client_name)->slug() . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
