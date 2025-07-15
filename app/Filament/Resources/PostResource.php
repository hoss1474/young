<?php
//
//namespace App\Filament\Resources;
//
//use App\Filament\Resources\PostResource\Pages;
//use App\Filament\Resources\PostResource\Pages\ListPosts;
//use App\Filament\Resources\PostResource\Pages\CreatePost;
//use App\Filament\Resources\PostResource\Pages\EditPost;
//use App\Models\Post;
//use Filament\Forms;
//use Filament\Resources\Form;
//use Filament\Resources\Resource;
//use Filament\Resources\Table;
//use Filament\Tables;
//use Illuminate\Support\Facades\Storage;
//
//class PostResource extends Resource
//{
//    protected static ?string $model = Post::class;
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
////    protected static ?string $navigationLabel = 'پست‌ها';
////    protected static ?string $pluralLabel = 'پست‌ها';
////    protected static ?string $navigationGroup = 'مدیریت محتوا';
//    protected static ?string $pluralLabel = 'وبلاگ';
//    protected static ?string $slug = 'posts'; // اسلاگ منحصربه‌فرد
//
//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                Forms\Components\TextInput::make('title')
//                    ->label('عنوان')
//                    ->required()
//                    ->maxLength(255),
//                Forms\Components\FileUpload::make('main_image')
//                    ->label('تصویر اصلی')
//                    ->required()
//                    ->disk('custom')
//                    ->directory('posts')
//                    ->image()
////                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
//                    ->maxSize(2048),
//                Forms\Components\Textarea::make('short_description')
//                    ->label('توضیح کوتاه')
//                    ->required()
//                    ->maxLength(500),
//                Forms\Components\RichEditor::make('content')
//                    ->label('محتوا')
//                    ->required(),
//                Forms\Components\FileUpload::make('gallery_images')
//                    ->label('گالری تصاویر')
//                    ->multiple()
//                    ->disk('custom')
//                    ->directory('posts/gallery')
//                    ->image()
////                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
//                    ->maxSize(2048)
//                    ->getUploadedFileNameForStorageUsing(function ($file) {
//                        return 'gallery_' . time() . '_' . $file->getClientOriginalName();
//                    })
//                    ->afterStateHydrated(function ($component, $state) {
//                        if (is_array($state)) {
//                            $paths = array_map(function ($item) {
//                                return is_array($item) && isset($item['image']) ? $item['image'] : null;
//                            }, $state);
//                            $component->state(array_filter($paths));
//                        }
//                    })
//                    ->dehydrated(function ($state) {
//                        if (is_array($state)) {
//                            return array_map(function ($path) {
//                                return ['image' => $path];
//                            }, $state);
//                        }
//                        return [];
//                    }),
//                Forms\Components\TextInput::make('author')
//                    ->label('نویسنده')
//                    ->required()
//                    ->maxLength(255),
//            ]);
//    }
//
//    public static function table(Table $table): Table
//    {
//        return $table
//            ->columns([
//                Tables\Columns\TextColumn::make('title')
//                    ->label('عنوان'),
//                Tables\Columns\ImageColumn::make('main_image')
//                    ->label('تصویر اصلی')
//                    ->disk('custom'),
//                Tables\Columns\TextColumn::make('author')
//                    ->label('نویسنده'),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->label('تاریخ ایجاد')
//                    ->dateTime(),
//            ])
//            ->filters([
//                //
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
//            ])
//            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
//            ]);
//    }
//
//    public static function getPages(): array
//    {
//        return [
//            'index' => Pages\ListPosts::route('/'),
//            'create' => Pages\CreatePost::route('/create'),
//            'edit' => Pages\EditPost::route('/{record}/edit'),
//        ];
//    }
//}
