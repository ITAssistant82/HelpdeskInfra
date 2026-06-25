<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;
    protected function getHeaderActions(): array
    {
        $isStaff = auth()->user()?->isStaff() ?? false;
        $record = $this->record;
        $actions = [];
        if ($isStaff) {
            $actions [] = Actions\EditAction::make()->visible(fn () => $isStaff);
            $actions [] = Actions\Action::make('assign')->label('Assign')->icon('heroicon-o-user-plus')->color('primary')->visible(fn () => in_array($record->status, ['New', 'Escalated']))->form(fn () => [Select::make('assigned_to')->label('Assign to')->options(\App\Filament\Resources\TicketResource::getAssignableUsers($record->team_key, $record->assigned_group))->searchable()->required(), ])->action(fn (array $data) => \App\Filament\Resources\TicketResource::handleAssignAction($record, $data ['assigned_to']));
            $actions [] = Actions\Action::make('take')->label('Take')->icon('heroicon-o-hand-raised')->color('success')->visible(fn () => in_array($record->status, ['New', 'Escalated']))->action(fn () => static::handleTake($record));
            $actions [] = Actions\Action::make('start')->label('Start')->icon('heroicon-o-play')->color('warning')->visible(fn () => $record->status === 'Assigned')->action(fn () => static::handleStart($record));
            $actions [] = Actions\Action::make('solve')->label('Solve')->icon('heroicon-o-check-circle')->color('success')->visible(fn () => $record->status === 'In Progress')->form(fn () => [Textarea::make('resolution_note')->label('Resolution Note')->required(), ])->action(fn (array $data) => static::handleSolve($record, $data ['resolution_note'] ?? null));
            $actions [] = Actions\Action::make('close')->label('Close')->icon('heroicon-o-x-circle')->color('gray')->visible(fn () => $record->status === 'Solved')->form(fn () => [Textarea::make('closure_note')->label('Closure Note'), ])->action(fn (array $data) => static::handleClose($record, $data ['closure_note'] ?? null));
            $actions [] = Actions\Action::make('reopen')->label('Reopen')->icon('heroicon-o-arrow-uturn-left')->color('danger')->visible(fn () => in_array($record->status, ['Solved', 'Closed']))->requiresConfirmation()->action(fn () => static::handleReopen($record));
            $actions [] = Actions\Action::make('escalate_layer')->label('Eskalasi')->icon('heroicon-o-arrow-up-circle')->color('danger')->visible(fn () => $isStaff)->form(fn () => [\Filament\Forms\Components\Select::make('target_layer')->label('Tujuan Layer')->options(\App\Filament\Resources\TicketResource::getEscalatableLayers($record)->pluck('name', 'id'))->required(), ])->action(function (array $data) use ($record) {
                $layer = \App\Models\TicketLayer::find($data['target_layer']);
                if ($layer) {
                    $record->escalateToLayer($layer);
                    Notification::make()->success()->title("Tiket dinaikkan ke {$layer->name}")->send();
                }
            });
            $actions [] = Actions\Action::make('pending_user')->label('Pending User')->icon('heroicon-o-clock')->color('gray')->visible(fn () => in_array($record->status, ['Assigned', 'In Progress']))->action(fn () => static::handleSetPending($record, 'Pending User'));
            $actions [] = Actions\Action::make('pending_vendor')->label('Pending Vendor')->icon('heroicon-o-clock')->color('gray')->visible(fn () => in_array($record->status, ['Assigned', 'In Progress']))->action(fn () => static::handleSetPending($record, 'Pending Vendor'));
            $actions [] = Actions\Action::make('pending_procurement')->label('Pending Procurement')->icon('heroicon-o-clock')->color('gray')->visible(fn () => in_array($record->status, ['Assigned', 'In Progress']))->action(fn () => static::handleSetPending($record, 'Pending Procurement'));
            $actions [] = Actions\Action::make('resume')->label('Resume')->icon('heroicon-o-play')->color('warning')->visible(fn () => in_array($record->status, ['Pending User', 'Pending Vendor', 'Pending Procurement']))->action(fn () => static::handleResume($record));
        }
        return $actions;
    }
    protected static function canApprove($record): bool
    {
        if ($record->status !== 'Pending Approval') {
            return false;
        }
        $current = $record->currentApproval();
        return $current && auth()->user()->hasRole($current->role_name) && $current->status === 'pending';
    }
    protected static function handleAssign($record, int $userId): void
    {
        $record->update(['assigned_to' => $userId, 'status' => 'Assigned', 'first_response_at' => now()]);
        Notification::make()->success()->title('Ticket assigned')->send();
    }
    protected static function handleTake($record): void
    {
        $record->update(['assigned_to' => auth()->id(), 'status' => 'Assigned', 'first_response_at' => now()]);
        Notification::make()->success()->title('Ticket assigned to you')->send();
    }
    protected static function handleStart($record): void
    {
        $record->update(['status' => 'In Progress']);
        Notification::make()->success()->title('Ticket in progress')->send();
    }
    protected static function handleSolve($record, ?string $resolutionNote): void
    {
        $record->update(['status' => 'Solved', 'solved_at' => now(), 'resolution_note' => $resolutionNote, 'sla_achieved' => $record->sla_deadline ? now()->lte($record->sla_deadline) : null, ]);
        Notification::make()->success()->title('Ticket solved')->send();
    }
    protected static function handleClose($record, ?string $closureNote): void
    {
        $record->update(['status' => 'Closed', 'closed_at' => now(), 'closure_note' => $closureNote]);
        Notification::make()->success()->title('Ticket closed')->send();
    }
    protected static function handleReopen($record): void
    {
        $record->update(['status' => 'Reopened', 'solved_at' => null, 'closed_at' => null, 'sla_achieved' => null]);
        Notification::make()->success()->title('Ticket reopened')->send();
    }
    protected static function handleEscalate($record, string $group, ?string $note): void
    {
        $layer = \App\Models\TicketLayer::where('role_name', $group)->first();
        $record->update([
            'status' => 'Escalated',
            'assigned_group' => $group,
            'assigned_to' => null,
            'team_key' => $layer?->team_key,
        ]);
        if ($note) {
            $record->comments()->create(['user_id' => auth()->id(), 'content' => " Escalation note: { $note}
 ", 'is_internal' => true]);
        }
        Notification::make()->success()->title('Ticket escalated')->send();
    }
    protected static function handleApprove($record, ?string $note): void
    {
        $current = $record->currentApproval();
        if (! $current) {
            return;
        }
        $current->update(['status' => 'approved', 'approver_id' => auth()->id(), 'note' => $note, 'acted_at' => now()]);
        if ($note) {
            $record->comments()->create(['user_id' => auth()->id(), 'content' => " Approval note: { $note}
 ", 'is_internal' => true]);
        }
        $nextSequence = $record->current_approver_sequence + 1;
        $nextApproval = $record->ticketApprovals()->where('sequence_order', $nextSequence)->first();
        if ($nextApproval) {
            $record->update(['current_approver_sequence' => $nextSequence]);
            $notifyUsers = \Spatie\Permission\Models\Role::where('name', $nextApproval->role_name)->exists() ? \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', $nextApproval->role_name))->get() : collect();
            \Illuminate\Support\Facades\Notification::send($notifyUsers, new \App\Notifications\TicketNotification(ticket: $record, message: " Ticket { $record->ticket_number}
 menunggu persetujuan ", type: 'approval', ));
        } else {
            $record->update(['status' => 'Assigned', 'first_response_at' => now(), 'approved_by' => auth()->id(), 'approved_at' => now()]);
        }
        Notification::make()->success()->title('Approved')->send();
    }
    protected static function handleReject($record, string $reason): void
    {
        $current = $record->currentApproval();
        if (! $current) {
            return;
        }
        $current->update(['status' => 'rejected', 'approver_id' => auth()->id(), 'note' => $reason, 'acted_at' => now()]);
        $record->update(['status' => 'Rejected/Out of Scope', 'closure_note' => $reason]);
        Notification::make()->success()->title('Rejected')->send();
    }
    protected static function handleSetPending($record, string $pendingStatus): void
    {
        $record->update(['status' => $pendingStatus]);
        Notification::make()->info()->title(" Ticket set to { $pendingStatus}
 ")->send();
    }
    protected static function handleResume($record): void
    {
        $record->update(['status' => 'In Progress']);
        Notification::make()->success()->title('Ticket resumed')->send();
    }
}
