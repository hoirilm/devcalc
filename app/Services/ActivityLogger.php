<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealActivity;
use App\Models\Module;
use App\Models\Project;

class ActivityLogger
{
    /**
     * Catat log aktivitas umum
     */
    public static function log(
        string $type,
        string $title,
        ?string $description = null,
        ?int $clientId = null,
        ?int $dealId = null,
        ?int $userId = null
    ): DealActivity {
        return DealActivity::create([
            'user_id' => $userId ?? auth()->id() ?? 1,
            'client_id' => $clientId,
            'deal_id' => $dealId,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'performed_at' => now(),
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /* 1. PENAWARAN HARGA (CPQ ENGINE)                                            */
    /* -------------------------------------------------------------------------- */

    public static function logProjectCreated(Project $project): DealActivity
    {
        $code = $project->getQuotationCode();
        $totalFormatted = 'Rp ' . number_format($project->grand_total, 0, ',', '.');
        $itemCount = $project->items()->count();
        $statusText = $project->status === 'Generated' ? 'Resmi (Generated)' : 'Draf';

        return self::log(
            type: 'project_created',
            title: "Penawaran Baru Dibuat (#{$code})",
            description: "Penawaran untuk {$project->client_name} senilai {$totalFormatted} ({$itemCount} item modul, status: {$statusText}) berhasil dibuat.",
            clientId: $project->client_id,
            dealId: $project->deal_id
        );
    }

    public static function logProjectUpdated(Project $project): DealActivity
    {
        $code = $project->getQuotationCode();
        $totalFormatted = 'Rp ' . number_format($project->grand_total, 0, ',', '.');
        $statusText = $project->status === 'Generated' ? 'Resmi (Generated)' : 'Draf';

        return self::log(
            type: 'project_updated',
            title: "Penawaran Diperbarui (#{$code})",
            description: "Perubahan parameter/modul penawaran {$project->client_name}. Nilai saat ini: {$totalFormatted} ({$statusText}).",
            clientId: $project->client_id,
            dealId: $project->deal_id
        );
    }

    public static function logProjectDeleted(string $code, ?string $clientName = null, ?int $clientId = null): DealActivity
    {
        return self::log(
            type: 'project_deleted',
            title: "Penawaran Dihapus (#{$code})",
            description: "Dokumen penawaran #{$code}" . ($clientName ? " untuk klien {$clientName}" : "") . " telah dihapus dari sistem.",
            clientId: $clientId
        );
    }

    public static function logBulkProjectsDeleted(int $count): DealActivity
    {
        return self::log(
            type: 'project_deleted',
            title: "Penghapusan Massal Penawaran",
            description: "Sebanyak {$count} dokumen penawaran harga dihapus secara massal."
        );
    }

    public static function logAddendumCreated(Project $parent, Project $addendum): DealActivity
    {
        $parentCode = $parent->getQuotationCode();
        $addendumCode = $addendum->getQuotationCode();
        $addendumTotal = 'Rp ' . number_format($addendum->grand_total, 0, ',', '.');

        $typeLabel = match ($addendum->addendum_type) {
            'module_expansion' => 'Penambahan Modul Fitur',
            'user_capacity' => 'Penambahan Kapasitas User',
            'contract_renewal' => 'Perpanjangan Durasi Kontrak',
            default => 'Amandemen Penawaran',
        };

        return self::log(
            type: 'addendum_created',
            title: "Dokumen Adendum Diterbitkan (#{$addendumCode})",
            description: "Adendum ({$typeLabel}) senilai {$addendumTotal} diterbitkan untuk penawaran induk #{$parentCode} ({$parent->client_name}).",
            clientId: $parent->client_id,
            dealId: $parent->deal_id
        );
    }

    /* -------------------------------------------------------------------------- */
    /* 2. SALES PIPELINE & KANBAN                                                 */
    /* -------------------------------------------------------------------------- */

    public static function logDealStageChanged(Deal $deal, string $oldStage, string $newStage): DealActivity
    {
        $oldLabel = Deal::STAGES[$oldStage]['label'] ?? ucfirst($oldStage);
        $newLabel = Deal::STAGES[$newStage]['label'] ?? ucfirst($newStage);
        $newProb = Deal::STAGES[$newStage]['probability'] ?? $deal->probability;

        $desc = "Peluang '{$deal->title}' berpindah dari stage {$oldLabel} ke {$newLabel} (Probabilitas: {$newProb}%).";
        if ($newStage === 'lost' && $deal->lost_reason) {
            $desc .= " Alasan: {$deal->lost_reason}.";
        }

        return self::log(
            type: 'stage_change',
            title: "Stage Kanban Berpindah: {$newLabel}",
            description: $desc,
            clientId: $deal->client_id,
            dealId: $deal->id
        );
    }

    public static function logDealUpdated(Deal $deal): DealActivity
    {
        $valFormatted = 'Rp ' . number_format($deal->expected_value, 0, ',', '.');
        return self::log(
            type: 'deal_updated',
            title: "Data Peluang Deal Diperbarui",
            description: "Pembaruan informasi deal '{$deal->title}'. Nilai estimasi: {$valFormatted}.",
            clientId: $deal->client_id,
            dealId: $deal->id
        );
    }

    /* -------------------------------------------------------------------------- */
    /* 3. KLIEN & KONTAK PIC B2B                                                  */
    /* -------------------------------------------------------------------------- */

    public static function logClientCreated(Client $client): DealActivity
    {
        return self::log(
            type: 'client_created',
            title: "Klien B2B Baru Terdaftar",
            description: "Perusahaan '{$client->name}' ({$client->industry}) didaftarkan ke dalam direktori CRM.",
            clientId: $client->id
        );
    }

    public static function logClientUpdated(Client $client): DealActivity
    {
        return self::log(
            type: 'client_updated',
            title: "Profil Klien Diperbarui",
            description: "Pembaruan profil data perusahaan '{$client->name}' (Status: {$client->status}).",
            clientId: $client->id
        );
    }

    public static function logClientDeleted(string $clientName): DealActivity
    {
        return self::log(
            type: 'client_deleted',
            title: "Klien Dihapus",
            description: "Perusahaan '{$clientName}' telah dihapus dari direktori klien CRM."
        );
    }

    public static function logContactCreated(Client $client, Contact $contact): DealActivity
    {
        $role = $contact->title ? " ({$contact->title})" : "";
        return self::log(
            type: 'contact_created',
            title: "Kontak PIC Ditambahkan",
            description: "PIC {$contact->name}{$role} ditambahkan ke profil {$client->name}.",
            clientId: $client->id
        );
    }

    public static function logContactUpdated(Client $client, Contact $contact): DealActivity
    {
        return self::log(
            type: 'contact_updated',
            title: "Kontak PIC Diperbarui",
            description: "Data PIC {$contact->name} pada klien {$client->name} telah diperbarui.",
            clientId: $client->id
        );
    }

    public static function logContactDeleted(Client $client, string $contactName): DealActivity
    {
        return self::log(
            type: 'contact_deleted',
            title: "Kontak PIC Dihapus",
            description: "PIC {$contactName} dihapus dari profil klien {$client->name}.",
            clientId: $client->id
        );
    }

    /* -------------------------------------------------------------------------- */
    /* 4. KATALOG MASTER MODUL                                                    */
    /* -------------------------------------------------------------------------- */

    public static function logModuleCreated(Module $module): DealActivity
    {
        $baseFormatted = 'Rp ' . number_format($module->base_price, 0, ',', '.');
        return self::log(
            type: 'module_created',
            title: "Modul Baru Ditambahkan",
            description: "Modul '{$module->name}' ({$module->category}) dengan tarif dasar {$baseFormatted} ditambahkan ke katalog master."
        );
    }

    public static function logModuleUpdated(Module $module): DealActivity
    {
        $baseFormatted = 'Rp ' . number_format($module->base_price, 0, ',', '.');
        return self::log(
            type: 'module_updated',
            title: "Modul Katalog Diperbarui",
            description: "Pembaruan informasi modul '{$module->name}' ({$module->category}). Harga dasar: {$baseFormatted}."
        );
    }

    public static function logModuleDeleted(string $moduleName): DealActivity
    {
        return self::log(
            type: 'module_deleted',
            title: "Modul Katalog Dihapus",
            description: "Modul '{$moduleName}' telah dihapus dari katalog master software."
        );
    }
}
