<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Tickets';
    protected static string|\UnitEnum|null $navigationGroup = 'Ticketing';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'title';
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAnyRole(['super_admin', 'admin', 'user', 'helpdesk_l1', 'it_infra_l1', 'it_infra_l2', 'it_infra_l3', 'network_team', 'm365_team', 'security_soc', 'approver']);
    }
    public static function canCreate(): bool
    {
        return static::canViewAny();
    }
    public static function canView($record): bool
    {
        return static::canEdit($record);
    }
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if ($user->isStaff()) return true;
        return $record && $record->requester_id === $user->id;
    }
    public static function canDelete($record): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    public static function getCreateAuthorizationResponse(): Response
    {
        return static::canCreate() ? Response::allow() : Response::deny();
    }
    public static function getEditAuthorizationResponse(Model $record): Response
    {
        return static::canEdit($record) ? Response::allow() : Response::deny();
    }
    public static function getDeleteAuthorizationResponse(Model $record): Response
    {
        return static::canDelete($record) ? Response::allow() : Response::deny();
    }
    public static function form(Schema $schema): Schema
    {
        $isStaff = auth()->user()?->isStaff() ?? false;
        return $schema->columns(1)->schema($isStaff ? static::staffForm() : static::userForm());
    }
    protected static function userForm(): array
    {
        return [Schemas\Components\Section::make('Informasi Tiket')->columns(2)->schema([Forms\Components\Select::make('type')->label('Tipe')->options(['Incident' => 'Incident', 'Service Request' => 'Service Request'])->required()->live(), Forms\Components\Select::make('category_id')->label('Kategori')->options(fn ($get) => \App\Models\TicketCategory::where('is_active', true)->when($get('type'), fn ($q, $type) => $q->where('type', $type))->get()->mapWithKeys(fn ($cat) => [$cat->id => " { $cat->main_category}
 - { $cat->sub_category}
 " ]))->required()->live(), Forms\Components\TextInput::make('title')->label('Judul')->required()->columnSpanFull(), Forms\Components\Textarea::make('description')->label('Deskripsi')->required()->columnSpanFull()->rows(3), ]), Schemas\Components\Section::make('Detail')->columns(2)->schema([Forms\Components\Select::make('location')->label('Lokasi')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak', 'Remote' => 'Remote', 'Cloud' => 'Cloud'])->required(), Forms\Components\TextInput::make('requester_unit')->label('Unit / Departemen'), ]), Schemas\Components\Section::make('Lampiran')->schema([Forms\Components\Placeholder::make('attachments_existing')->label('')->content(fn ($record) => new \Illuminate\Support\HtmlString(view('components.ticket-attachments', ['attachments' => $record?->attachments ?? collect()])->render()))->visible(fn ($operation, $record) => ($operation === 'edit' || $operation === 'view') && $record), Forms\Components\FileUpload::make('new_attachments')->label('Upload File')->disk('public')->directory('ticket-attachments')->preserveFilenames()->multiple()->visible(fn ($operation) => $operation !== 'view')->columnSpanFull(), ]), ];
    }
    protected static function staffForm(): array
    {
        return [Schemas\Components\Section::make('Informasi Tiket')->columns(2)->schema([Forms\Components\TextInput::make('ticket_number')->label('Nomor Tiket')->disabled()->dehydrated(false)->visible(fn ($operation) => $operation === 'edit'), Forms\Components\Select::make('type')->label('Tipe')->options(['Incident' => 'Incident', 'Service Request' => 'Service Request'])->required()->live(), Forms\Components\Select::make('category_id')->label('Kategori')->options(fn ($get) => \App\Models\TicketCategory::where('is_active', true)->when($get('type'), fn ($q, $type) => $q->where('type', $type))->get()->mapWithKeys(fn ($cat) => [$cat->id => " { $cat->main_category}
 - { $cat->sub_category}
 " ]))->required()->live()->afterStateUpdated(fn ($state, $set) => static::updateAssignedGroup($state, $set)), Forms\Components\TextInput::make('title')->label('Judul')->required()->columnSpanFull(), Forms\Components\Textarea::make('description')->label('Deskripsi')->required()->columnSpanFull()->rows(3), ]),         Schemas\Components\Section::make('Pemohon')->columns(2)->schema([Forms\Components\Select::make('requester_id')->label('Pemohon')->relationship('requester', 'name')->default(auth()->id())->disabled()->required(), Forms\Components\Select::make('location')->label('Lokasi')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak', 'Remote' => 'Remote', 'Cloud' => 'Cloud'])->required(), Forms\Components\TextInput::make('requester_unit')->label('Unit / Departemen'), ]), Schemas\Components\Section::make('Prioritas & Penugasan')->columns(2)->schema([Forms\Components\Select::make('urgency')->label('Urgensi')->options(['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical'])->required(), Forms\Components\Select::make('assigned_to')->label('Ditugaskan Ke')->options(fn ($record, $get) => static::getAssignableUsers(
    $record?->team_key ?? \App\Models\TicketLayer::where('role_name', $get('assigned_group'))->value('team_key'),
    $record?->assigned_group ?? $get('assigned_group'),
))->searchable()->nullable()->live(), Forms\Components\Select::make('status')->label('Status')->options(['New' => 'New', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Pending User' => 'Pending User', 'Pending Approval' => 'Pending Approval', 'Pending Vendor' => 'Pending Vendor', 'Pending Procurement' => 'Pending Procurement', 'Escalated' => 'Escalated', 'Solved' => 'Solved', 'Closed' => 'Closed', 'Reopened' => 'Reopened', 'Rejected/Out of Scope' => 'Rejected/Out of Scope', ])->default('New')->required(), ]), Schemas\Components\Section::make('SLA')->columns(2)->schema([Forms\Components\DateTimePicker::make('first_response_at')->label('Respon Pertama'), Forms\Components\DateTimePicker::make('sla_deadline')->label('Deadline SLA'), Forms\Components\Toggle::make('sla_achieved')->label('SLA Tercapai'), ]), Schemas\Components\Section::make('Approval')->columns(2)->visible(fn () => false)->schema([Forms\Components\Placeholder::make('progres_persetujuan')->label('Progres Persetujuan')->columnSpanFull()->visible(fn ($operation, $record) => $operation === 'view' && $record && $record->needs_approval)->content(fn ($record) => new \Illuminate\Support\HtmlString(view('components.approval-progress', ['approvals' => $record->ticketApprovals])->render())), ]), Schemas\Components\Section::make('Penyelesaian')->columns(2)->schema([Forms\Components\DateTimePicker::make('solved_at')->label('Selesai Pada'), Forms\Components\DateTimePicker::make('closed_at')->label('Ditutup Pada'), Forms\Components\Textarea::make('resolution_note')->label('Catatan Solusi')->rows(3), Forms\Components\Textarea::make('closure_note')->label('Catatan Penutupan')->rows(3), ]), Schemas\Components\Section::make('Lampiran')->schema([Forms\Components\Placeholder::make('attachments_existing')->label('')->content(fn ($record) => new \Illuminate\Support\HtmlString(view('components.ticket-attachments', ['attachments' => $record?->attachments ?? collect()])->render()))->visible(fn ($operation, $record) => ($operation === 'edit' || $operation === 'view') && $record), Forms\Components\FileUpload::make('new_attachments')->label('Tambah File')->disk('public')->directory('ticket-attachments')->preserveFilenames()->multiple()->visible(fn ($operation) => $operation !== 'view'), ]), Schemas\Components\Section::make('Lainnya')->columns(2)->schema([Forms\Components\DatePicker::make('due_date')->label('Tenggat Waktu'), Forms\Components\TextInput::make('cost_center')->label('Cost Center'), Forms\Components\TextInput::make('vendor_name')->label('Vendor'), Forms\Components\TextInput::make('device_asset')->label('Perangkat / Aset'), Forms\Components\TextInput::make('application_service')->label('Aplikasi / Layanan'), ]), ];
    }
    public static function infolist(Schema $schema): Schema
    {
        return $schema;
    }
    protected static function updateAssignedGroup($categoryId, $set): void
    {
        if (! $categoryId) {
            return;
        }
        $category = \App\Models\TicketCategory::find($categoryId);
        if ($category && $category->assigned_team) {
            $set('assigned_group', $category->assigned_team);
        }
    }
    public static function table(Table $table): Table
    {
        $isStaff = auth()->user()?->isStaff() ?? false;
        return $table->columns([Tables\Columns\TextColumn::make('ticket_number')->searchable()->sortable(), Tables\Columns\TextColumn::make('type')->badge()->color(fn ($state) => $state === 'Incident' ? 'danger' : 'warning')->searchable(), Tables\Columns\TextColumn::make('title')->searchable()->limit(40), Tables\Columns\TextColumn::make('category.main_category')->searchable(), Tables\Columns\TextColumn::make('priority')->badge()->color(fn ($state) => match ($state) {
            'Critical' => 'danger', 'High' => 'warning', 'Medium' => 'info', 'Low' => 'gray', default => 'gray',
        }), Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
            'New' => 'info', 'Assigned' => 'primary', 'In Progress' => 'warning', 'Pending User', 'Pending Vendor', 'Pending Procurement' => 'gray', 'Pending Approval' => 'warning', 'Escalated' => 'danger', 'Solved' => 'success', 'Closed' => 'success', 'Reopened' => 'warning', 'Rejected/Out of Scope' => 'danger', default => 'gray',
        }), Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable(), Tables\Columns\TextColumn::make('sla_deadline')->label('SLA Deadline')->dateTime('d/m/Y H:i')->sortable()->visible($isStaff), Tables\Columns\TextColumn::make('sla_status')->label('SLA')->badge()->state(fn ($record) => $record->slastatus())->color(fn ($state) => match ($state) {
            'overdue' => 'danger', 'warning' => 'warning', 'on_track' => 'success', 'achieved' => 'success', default => 'gray',
        })->formatStateUsing(fn ($state) => match ($state) {
            'overdue' => 'Overdue', 'warning' => 'Due Soon', 'on_track' => 'On Track', 'achieved' => 'Achieved', default => '-',
        }),         ])->filters([Tables\Filters\SelectFilter::make('status')->options(['New' => 'New', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Pending User' => 'Pending User', 'Pending Approval' => 'Pending Approval', 'Pending Vendor' => 'Pending Vendor', 'Pending Procurement' => 'Pending Procurement', 'Escalated' => 'Escalated', 'Solved' => 'Solved', 'Closed' => 'Closed', 'Reopened' => 'Reopened', ]), Tables\Filters\SelectFilter::make('type')->options(['Incident' => 'Incident', 'Service Request' => 'Service Request']), Tables\Filters\SelectFilter::make('priority')->options(['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical']), Tables\Filters\TrashedFilter::make(), ])->actions(static::getTableActions())->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()->visible($isStaff), Actions\ForceDeleteBulkAction::make()->visible($isStaff), Actions\RestoreBulkAction::make()->visible($isStaff), ]), ])->recordUrl(fn ($record) => static::canEdit($record) ? TicketResource::getUrl('edit', [$record]) : TicketResource::getUrl('view', [$record]))->defaultSort('created_at', 'desc');
    }
    protected static function getTableActions(): array
    {
        $isStaff = auth()->user()?->isStaff() ?? false;
        $isOwner = fn ($record) => $record && $record->requester_id === auth()->id();
        $actions = [
            Actions\Action::make('comments')
                ->label('Komentar')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->modalHeading(fn ($record) => 'Komentar - ' . $record->ticket_number)
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString(
                    view('filament.resources.ticket-resource.comments-modal', [
                        'ticket' => $record->load('comments.user'),
                        'isStaff' => $isStaff,
                    ])->render()
                )),
        ];
        if (! $isStaff) {
            return $actions;
        }
        return array_merge($actions, [Actions\EditAction::make(), Actions\ActionGroup::make([Actions\Action::make('assign')->label('Assign')->icon('heroicon-o-user-plus')->color('primary')->visible(fn ($record) => in_array($record->status, ['New', 'Escalated']))->modalHeading(fn ($record) => static::assignModalTitle($record))->modalIcon('heroicon-o-user-plus')->modalSubmitActionLabel('Assign')->form(fn () => [Forms\Components\Select::make('assigned_to')->label('Assign to')->options(fn ($record) => static::getAssignableUsers($record?->team_key, $record?->assigned_group))->searchable()->required(), ])->action(fn (array $data, $record) => static::handleAssignAction($record, $data ['assigned_to'])), Actions\Action::make('take')->label('Take')->icon('heroicon-o-hand-raised')->color('success')->visible(fn ($record) => in_array($record->status, ['New', 'Escalated']))->requiresConfirmation()->modalHeading('Take Ticket')->modalDescription('Are you sure you want to take this ticket? You will be assigned as the handler.')->modalIcon('heroicon-o-hand-raised')->modalSubmitActionLabel('Yes, Take')->action(fn ($record) => static::takeTicket($record)), Actions\Action::make('escalate_layer')->label('Eskalasi')->icon('heroicon-o-arrow-up-circle')->color('danger')->visible(fn () => auth()->user()?->isStaff())->modalHeading('Pilih Layer Tujuan')->modalIcon('heroicon-o-arrow-up-circle')->modalSubmitActionLabel('Eskalasi')->form(fn ($record) => [Forms\Components\Select::make('target_layer')->label('Tujuan Layer')->options(static::getEscalatableLayers($record)->pluck('name', 'id'))->required(), ])->action(fn (array $data, $record) => static::handleTableEscalate($record, $data['target_layer'], null)), Actions\Action::make('start')->label('Start')->icon('heroicon-o-play')->color('warning')->visible(fn ($record) => $record->status === 'Assigned')->modalHeading('Start Progress')->modalIcon('heroicon-o-play')->modalSubmitActionLabel('Start')->form(fn () => [Forms\Components\Textarea::make('note')->label('Note (optional)'), ])->action(fn (array $data, $record) => static::startProgress($record, $data ['note'] ?? null)), Actions\Action::make('solve')->label('Solve')->icon('heroicon-o-check-circle')->color('success')->visible(fn ($record) => $record->status === 'In Progress')->modalHeading('Solve Ticket')->modalIcon('heroicon-o-check-circle')->modalSubmitActionLabel('Solve')->form(fn () => [Forms\Components\Textarea::make('resolution_note')->label('Resolution Note')->rows(2)->required(), ])->action(fn (array $data, $record) => static::solveTicket($record, $data ['resolution_note'] ?? null)), Actions\Action::make('close')->label('Close')->icon('heroicon-o-x-circle')->color('gray')->visible(fn ($record) => $record->status === 'Solved')->modalHeading('Close Ticket')->modalIcon('heroicon-o-x-circle')->modalSubmitActionLabel('Close')->form(fn () => [Forms\Components\Textarea::make('closure_note')->label('Closure Note'), ])->action(fn (array $data, $record) => static::closeTicket($record, $data ['closure_note'] ?? null)), Actions\Action::make('reopen')->label('Reopen')->icon('heroicon-o-arrow-uturn-left')->color('danger')->visible(fn ($record) => in_array($record->status, ['Solved', 'Closed']))->requiresConfirmation()->modalHeading('Reopen Ticket')->modalDescription('Reopen this ticket to continue working on it.')->modalIcon('heroicon-o-arrow-uturn-left')->modalSubmitActionLabel('Yes, Reopen')->action(fn ($record) => static::reopenTicket($record)),  Actions\Action::make('approve')->label('Approve')->icon('heroicon-o-check-badge')->color('success')->visible(fn ($record) => static::canApprove($record))->modalHeading('Approve Ticket')->modalIcon('heroicon-o-check-badge')->modalSubmitActionLabel('Approve')->form(fn () => [Forms\Components\Textarea::make('approval_note')->label('Note'), ])->action(fn (array $data, $record) => static::approveTicket($record, $data ['approval_note'] ?? null)), Actions\Action::make('reject')->label('Reject')->icon('heroicon-o-x-circle')->color('danger')->visible(fn ($record) => static::canApprove($record))->modalHeading('Reject Ticket')->modalIcon('heroicon-o-x-circle')->modalSubmitActionLabel('Reject')->form(fn () => [Forms\Components\Textarea::make('rejection_reason')->label('Rejection Reason')->required(), ])->action(fn (array $data, $record) => static::rejectTicket($record, $data ['rejection_reason'])), Actions\Action::make('pending_user')->label('Pending User')->icon('heroicon-o-clock')->color('gray')->visible(fn ($record) => in_array($record->status, ['Assigned', 'In Progress']))->modalHeading('Set Pending User')->modalIcon('heroicon-o-clock')->modalSubmitActionLabel('Set Pending')->form(fn () => [Forms\Components\TextInput::make('reason')->label('Reason (optional)'), ])->action(fn (array $data, $record) => static::setPending($record, 'Pending User', $data ['reason'] ?? null)), Actions\Action::make('pending_vendor')->label('Pending Vendor')->icon('heroicon-o-clock')->color('gray')->visible(fn ($record) => in_array($record->status, ['Assigned', 'In Progress']))->modalHeading('Set Pending Vendor')->modalIcon('heroicon-o-clock')->modalSubmitActionLabel('Set Pending')->form(fn () => [Forms\Components\TextInput::make('reason')->label('Reason (optional)'), ])->action(fn (array $data, $record) => static::setPending($record, 'Pending Vendor', $data ['reason'] ?? null)), Actions\Action::make('pending_procurement')->label('Pending Procurement')->icon('heroicon-o-clock')->color('gray')->visible(fn ($record) => in_array($record->status, ['Assigned', 'In Progress']))->modalHeading('Set Pending Procurement')->modalIcon('heroicon-o-clock')->modalSubmitActionLabel('Set Pending')->form(fn () => [Forms\Components\TextInput::make('reason')->label('Reason (optional)'), ])->action(fn (array $data, $record) => static::setPending($record, 'Pending Procurement', $data ['reason'] ?? null)), Actions\Action::make('resume')->label('Resume')->icon('heroicon-o-play')->color('warning')->visible(fn ($record) => in_array($record->status, ['Pending User', 'Pending Vendor', 'Pending Procurement']))->requiresConfirmation()->modalHeading('Resume Ticket')->modalDescription('Resume work on this ticket and set it back to "In Progress".')->modalIcon('heroicon-o-play')->modalSubmitActionLabel('Yes, Resume')->action(fn ($record) => static::resumeTicket($record)), ]), ]);
    }
    protected static function assignModalTitle($record): string
    {
        return 'Assign Ticket';
    }
    public static function handleAssignAction($record, int $userId): void
    {
        $targetUser = \App\Models\User::find($userId);
        if (!$targetUser || !$record->team_key || !$record->current_layer) {
            static::assignTicket($record, $userId);
            return;
        }
        $targetUserRoles = $targetUser->roles->pluck('name');
        $targetLayer = \App\Models\TicketLayer::whereIn('role_name', $targetUserRoles)
            ->where('team_key', $record->team_key)
            ->where('level', '>', $record->current_layer)
            ->orderBy('level')
            ->first();
        if ($targetLayer) {
            $record->escalateToLayer($targetLayer);
        } else {
            static::assignTicket($record, $userId);
        }
    }
    protected static function assignTicket($record, int $userId): void
    {
        $record->update(['assigned_to' => $userId, 'status' => 'Assigned', 'first_response_at' => now(), ]);
    }
    protected static function takeTicket($record): void
    {
        $record->update(['assigned_to' => auth()->id(), 'status' => 'Assigned', 'first_response_at' => now(), ]);
    }
    protected static function startProgress($record, ?string $note = null): void
    {
        $record->update(['status' => 'In Progress']);
        if ($note) {
            $record->comments()->create(['user_id' => auth()->id(), 'content' => " Start note: { $note}
 ", 'is_internal' => true, ]);
        }
    }
    protected static function solveTicket($record, ?string $resolutionNote): void
    {
        $record->update(['status' => 'Solved', 'solved_at' => now(), 'resolution_note' => $resolutionNote, 'sla_achieved' => $record->sla_deadline ? now()->lte($record->sla_deadline) : null, ]);
    }
    protected static function closeTicket($record, ?string $closureNote): void
    {
        $record->update(['status' => 'Closed', 'closed_at' => now(), 'closure_note' => $closureNote, ]);
    }
    protected static function reopenTicket($record): void
    {
        $record->update(['status' => 'Reopened', 'solved_at' => null, 'closed_at' => null, 'sla_achieved' => null, ]);
    }
    public static function getEscalatableLayers($record): \Illuminate\Support\Collection
    {
        return \App\Models\TicketLayer::orderBy('team_key')->orderBy('level')->get();
    }
    protected static function handleTableEscalate($record, int $targetLayerId, ?string $note): void
    {
        $layer = \App\Models\TicketLayer::find($targetLayerId);
        if (!$layer) return;
        $record->escalateToLayer($layer);
        if ($note) {
            $record->comments()->create(['user_id' => auth()->id(), 'content' => " Eskalasi note: { $note}
 ", 'is_internal' => true, ]);
        }
    }
    protected static function escalateTicket($record, string $group, ?string $note): void
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
 ", 'is_internal' => true, ]);
        }
    }
    protected static function canApprove($record): bool
    {
        if ($record->status !== 'Pending Approval') {
            return false;
        }
        $current = $record->currentApproval();
        return $current && auth()->user()->hasRole($current->role_name) && $current->status === 'pending';
    }
    protected static function approveTicket($record, ?string $note): void
    {
        $current = $record->currentApproval();
        if (! $current) {
            return;
        }
        $current->update(['status' => 'approved', 'approver_id' => auth()->id(), 'note' => $note, 'acted_at' => now(), ]);
        if ($note) {
            $record->comments()->create(['user_id' => auth()->id(), 'content' => " Approval note: { $note}
 ", 'is_internal' => true, ]);
        }
        $nextSequence = $record->current_approver_sequence + 1;
        $nextApproval = $record->ticketApprovals()->where('sequence_order', $nextSequence)->first();
        if ($nextApproval) {
            $record->update(['current_approver_sequence' => $nextSequence]);
            $notifyUsers = \Spatie\Permission\Models\Role::where('name', $nextApproval->role_name)->exists() ? \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', $nextApproval->role_name))->get() : collect();
            \Illuminate\Support\Facades\Notification::send($notifyUsers, new \App\Notifications\TicketNotification(ticket: $record, message: " Ticket { $record->ticket_number}
 menunggu persetujuan ", type: 'approval', ));
        } else {
            $record->update(['status' => 'Assigned', 'first_response_at' => now(), 'approved_by' => auth()->id(), 'approved_at' => now(), ]);
        }
    }
    protected static function rejectTicket($record, string $reason): void
    {
        $current = $record->currentApproval();
        if (! $current) {
            return;
        }
        $current->update(['status' => 'rejected', 'approver_id' => auth()->id(), 'note' => $reason, 'acted_at' => now(), ]);
        $record->update(['status' => 'Rejected/Out of Scope', 'closure_note' => $reason, ]);
    }
    protected static function setPending($record, string $pendingStatus, ?string $reason = null): void
    {
        $record->update(['status' => $pendingStatus]);
        if ($reason) {
            $record->comments()->create(['user_id' => auth()->id(), 'content' => " { $pendingStatus}
 reason: { $reason}
 ", 'is_internal' => true, ]);
        }
    }
    protected static function resumeTicket($record): void
    {
        $record->update(['status' => 'In Progress']);
    }
    public static function getRelations(): array
    {
        return [RelationManagers\CommentsRelationManager::class, RelationManagers\ActivitiesRelationManager::class, ];
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListTickets::route('/'), 'create' => Pages\CreateTicket::route('/create'), 'view' => Pages\ViewTicket::route('/{record}'), 'edit' => Pages\EditTicket::route('/{record}/edit'), ];
    }
    public static function getAssignableUsers(?string $teamKey = null, ?string $assignedGroup = null): array
    {
        $roleNames = collect();

        if ($teamKey) {
            $layerRoles = \App\Models\TicketLayer::where('team_key', $teamKey)->pluck('role_name');
            $roleNames = $roleNames->merge($layerRoles);
        } elseif ($assignedGroup) {
            $layerTeamKey = \App\Models\TicketLayer::where('role_name', $assignedGroup)->value('team_key');
            if ($layerTeamKey) {
                $layerRoles = \App\Models\TicketLayer::where('team_key', $layerTeamKey)->pluck('role_name');
                $roleNames = $roleNames->merge($layerRoles);
            }
        }

        $layerMap = \App\Models\TicketLayer::pluck('name', 'role_name');

        $users = \App\Models\User::whereHas('roles', fn ($q) => $q->whereIn('name', $roleNames->toArray()))->get();

        if ($users->isEmpty() || !$teamKey) {
            $users = \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', '!=', 'user'))->get();
        }

        return $users
            ->mapWithKeys(fn ($u) => [
                $u->id => $u->name . ' (' . $u->roles
                    ->map(fn ($r) => $layerMap->get($r->name) ?? ($r->name === 'user' ? null : ucfirst(str_replace('_', ' ', $r->name))))
                    ->filter()->implode(', ') . ')',
            ])
            ->toArray();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['category', 'ticketApprovals.approver', 'assignee', 'requester', 'attachments']);
        $user = auth()->user();
        if (! $user) return $query;

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }

        if ($user->isStaff()) {
            $userLayers = \App\Models\TicketLayer::whereIn('role_name', $user->roles->pluck('name'))->get();
            $userRoleNames = $user->roles->pluck('name');

            if ($userLayers->isNotEmpty()) {
                $teamKeys = $userLayers->pluck('team_key')->unique()->filter()->values();
                $levels = $userLayers->pluck('level')->unique()->values();

                $query->where(function ($q) use ($teamKeys, $levels, $user, $userRoleNames) {
                    $q->whereIn('team_key', $teamKeys)
                      ->where(function ($q) use ($levels, $user) {
                          $q->whereNull('current_layer')
                            ->orWhereIn('current_layer', $levels)
                            ->orWhere('assigned_to', $user->id);
                      })
                      ->orWhere(function ($q) use ($userRoleNames) {
                          $q->whereNull('team_key')
                            ->whereIn('assigned_group', $userRoleNames);
                      })
                      ->orWhere('assigned_to', $user->id);
                });
            } else {
                $query->where(function ($q) use ($userRoleNames, $user) {
                    $q->whereIn('assigned_group', $userRoleNames)
                      ->orWhere('assigned_to', $user->id);
                });
            }
        } else {
            $query->where('requester_id', $user->id);
        }
        return $query;
    }
}
