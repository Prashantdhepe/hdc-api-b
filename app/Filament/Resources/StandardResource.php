<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StandardResource\Pages;
use App\Filament\Resources\StandardResource\RelationManagers;
use App\Models\Standard;
use App\Models\StandardType;
use Filament\Forms\Get;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StandardResource extends Resource
{
    protected static ?string $model = Standard::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'School Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('school_type_id')
                        // ->columns(1)
                        ->required()
                        // ->columnSpan(2)
                        ->relationship('school_type','name'),
                        Forms\Components\TextInput::make('user_id')
                        ->hidden(true), 
                        Forms\Components\Select::make('std')
                        ->label('Standard')
                        ->required()
                        ->options(StandardType::query()->distinct()->pluck('std', 'std'))
                        ->reactive(),

                        Forms\Components\Select::make('section_name')
                        ->label('Section')
                        ->options(fn (Get $get) => 
                            StandardType::where('std', $get('std'))
                                ->whereNotNull('section_name')
                                ->pluck('section_name', 'section_name')
                        )
                        ->default('A'),
                        Forms\Components\TextInput::make('strength')
                        ->numeric()
                        ->required(),
                        Forms\Components\FileUpload::make('image')
                        ->columns(1)
                        ->image()
                        ->multiple()
                        // ->disk('public')
                        ->disk(config('filesystems.default'))
                        ->directory('class_images')
                        ->visibility('private')
                        ->required()
                        ->columnSpan(2),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_type.name'),
                Tables\Columns\TextColumn::make('std'),
                Tables\Columns\TextColumn::make('section_name'),
                Tables\Columns\TextColumn::make('strength'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStandards::route('/'),
            'create' => Pages\CreateStandard::route('/create'),
            'view' => Pages\ViewStandard::route('/{record}'),
            'edit' => Pages\EditStandard::route('/{record}/edit'),
        ];
    }
}
