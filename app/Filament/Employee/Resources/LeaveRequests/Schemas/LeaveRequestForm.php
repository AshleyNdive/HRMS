<?php

namespace App\Filament\Employee\Resources\LeaveRequests\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Auth;


use function Symfony\Component\Clock\now;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(Auth::id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Select::make('leave_type_id')
                    ->relationship('leaveType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('start_date')
                    ->minDate(Carbon::now()->subDay())
                    ->live()
                    ->afterStateUpdated(
                        fn ($state, Get $get, Set $set) =>
                            self::calculateDays($set, $get)
                    )
                    ->required(),

                DatePicker::make('end_date')
                ->minDate(Carbon::now())
                    ->live()
                    ->afterStateUpdated(
                        fn ($state, Get $get, Set $set) =>
                            self::calculateDays($set, $get)
                    )
                    ->required(),

                TextInput::make('days')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),

                Hidden::make('status')
                    ->default('pending'),
            ]);
    }

    protected static function calculateDays(Set $set, Get $get): void
    {
        $start = $get('start_date');
        $end = $get('end_date');

        if ($start && $end) {
            $days = Carbon::parse($start)
                ->diffInDays(Carbon::parse($end)) + 1;

            $set('days', $days);
        }
    }
}
