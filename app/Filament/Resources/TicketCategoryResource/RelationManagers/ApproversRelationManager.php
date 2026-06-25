<?php

namespace App\Filament\Resources\TicketCategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class ApproversRelationManager extends RelationManager
{
    protected static string $relationship = 'approvers';
    protected static ?string $recordTitleAttribute = 'role_name';
    public function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\Select::make('role_name')->label('Role')->options(['super_admin' => 'Super Admin', 'admin' => 'Admin', 'helpdesk_l1' => 'Helpdesk L1', 'it_infra_l1' => 'IT Infra L1', 'it_infra_l2' => 'IT Infra L2', 'network_team' => 'Network Team', 'm365_team' => 'M365 Team', 'security_soc' => 'Security SOC', 'approver' => 'Approver', ])->required(), Forms\Components\TextInput::make('sequence_order')->label('Urutan')->numeric()->default(1)->required(), ]);
    }
    public function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('sequence_order')->label('Urutan')->badge()->color('gray')->sortable(), Tables\Columns\TextColumn::make('role_name')->label('Role')->badge()->color('primary')->formatStateUsing(fn ($state) => match ($state) {
            'super_admin' => 'Super Admin', 'admin' => 'Admin', 'helpdesk_l1' => 'Helpdesk L1', 'it_infra_l1' => 'IT Infra L1', 'it_infra_l2' => 'IT Infra L2', 'network_team' => 'Network Team', 'm365_team' => 'M365 Team', 'security_soc' => 'Security SOC', 'approver' => 'Approver', default => $state,
        }), ])->defaultSort('sequence_order', 'asc')->headerActions([Actions\CreateAction::make(), ])->actions([Actions\EditAction::make(), Actions\DeleteAction::make(), ]);
    }
}
