<?php

namespace App\Filament\Hr\Resources\LeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('leaveType.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status'),
                TextColumn::make('approved_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
             ->recordActions([
                EditAction::make(),
                Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn(LeaveRequest $record) => $record->status === 'pending')
                ->action(function(LeaveRequest $record){
                    $record->update([
                        'status'=> 'approved',
                        'approved_by' => Auth::user()->id,
                        'approved_at'=> now(),
                    ]);

                    Notification::make()
                    ->success()
                    ->title('Leave approved')
                    ->send();
                }),
                Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn(LeaveRequest $record) => $record->status === 'pending')
                ->schema([
                    Textarea::make('rejection_reason')
                    ->required()
                    ->rows(3)
                ])
                ->action(function(LeaveRequest $record, array $data){
                    $record->update([
                        'status'=> 'rejected',
                        'approved_by' => Auth::user()->id,
                        'approved_at'=> now(),
                        'rejection_reason' => $data['rejection_reason']
                    ]);

                    Notification::make()
                    ->success()
                    ->title('Leave Rejected')
                    ->send();
                })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
