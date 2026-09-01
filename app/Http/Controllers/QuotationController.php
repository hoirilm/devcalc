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
        $project->load(['user', 'client.contacts', 'items.module']);

        // Recalculate grand total to ensure precision
        $project->recalculateGrandTotal();

        $pdf = Pdf::loadView('pdf.quotation', [
            'project' => $project,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $code = $project->getQuotationCode();
        $filename = 'Penawaran-' . $code . '-' . str($project->client_name)->slug() . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
