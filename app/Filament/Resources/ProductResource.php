<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make("Product Information")
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) =>
                                    $operation == 'create' ? $set("slug", Str::slug($state)) : null
                                ),
                                TextInput::make('slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),
                                MarkdownEditor::make('description')
                                    ->columnSpanFull()
                                    ->fileAttachmentsDirectory('products')
                            ])->columns(2),

                        Section::make("Product Image")
                            ->schema([
                                FileUpload::make('image')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->reorderable()
                                    ->directory('products')
                                    ->image()
                            ])->columns(1),
                    ])->columnSpan(2),

                Group::make()
                    ->schema([
                        Section::make("Price")
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->step(500)

                            ])->columns(1),
                        Section::make('Associations')
                            ->schema([
                                Select::make('category_id')
                                    ->required()
                                    ->label('Category')
                                    ->searchable()
                                    ->preload()
                                    ->relationship("category", "name"),

                                Select::make('brand_id')
                                    ->required()
                                    ->label('Brand')
                                    ->searchable()
                                    ->preload()
                                    ->relationship("brand", "name")
                            ])->columns(1),
                        Section::make('Status')
                            ->schema([
                                Toggle::make('in_stock')
                                    ->required()
                                    ->default(true),
                                Toggle::make('is_active')
                                    ->required()
                                    ->default(true),
                                Toggle::make('is_featured')
                                    ->required(),
                                Toggle::make('on_sale')
                                    ->required(),

                            ])->columns(1)
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
