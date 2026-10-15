<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ResourceController;
use App\Http\Requests\ResourceRequest;
use App\Support\Resources;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

if (config('database.connections.' . config('database.default') . '.database') !== 'kwt_update') {
    throw new RuntimeException('Run only against the kwt_update preview database.');
}
Auth::login(App\Models\User::where('email', 'admin@kwt.test')->firstOrFail());
$assert = function ($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
    echo "PASS: {$message}\n";
};
foreach (['commodities' => App\Models\Commodity::class, 'products' => App\Models\ProcessedProduct::class] as $resource => $model) {
    [, , $rules] = Resources::get($resource);
    $assert(count(array_filter(array_keys($rules), fn($key) => str_starts_with($key, 'image_path'))) === 5, "{$resource}: exactly five photo slots");
    $bad = UploadedFile::fake()->create('invalid.txt', 10, 'text/plain');
    $assert(Validator::make(['image_path' => $bad], ['image_path' => $rules['image_path']])->fails(), "{$resource}: reject non-image");
    $oversized = UploadedFile::fake()->image('large.jpg')->size(4097);
    $assert(Validator::make(['image_path' => $oversized], ['image_path' => $rules['image_path']])->fails(), "{$resource}: reject images above 4 MB");
    $record = $model::firstOrFail();
    $before = $record->getRawOriginal();
    $data = array_intersect_key($before, $rules);
    foreach (array_keys($data) as $key) if (str_starts_with($key, 'image_path')) unset($data[$key]);
    $files = [];
    $paths = [];
    $oldPaths = [];
    // Use disposable old paths so replacement cleanup cannot remove existing demo assets.
    foreach (['image_path', 'image_path_2', 'image_path_3', 'image_path_4', 'image_path_5'] as $field) {
        $oldPaths[$field] = 'photo-tests/' . $resource . '-' . $field . '.webp';
        Storage::disk('public')->put($oldPaths[$field], file_get_contents(public_path('images/products/tomat.webp')));
        $files[$field] = UploadedFile::fake()->image($field . '.jpg', 100, 100);
    }
    DB::table($record->getTable())->where('id', $record->id)->update($oldPaths);
    try {
        $request = new ResourceRequest($data, [], [], [], $files);
        $request->setValidator(Validator::make($data + $files, $rules));
        (new ResourceController)->update($request, $resource, $record->id);
        $record->refresh();
        foreach ($oldPaths as $field => $oldPath) {
            $paths[] = $record->$field;
            $assert(Storage::disk('public')->exists($record->$field), "{$resource}: {$field} upload stored");
            $assert(!Storage::disk('public')->exists($oldPath), "{$resource}: replaced {$field} file removed after save");
        }
        $assert(count(App\Support\ProductImage::gallery($record)) === 5, "{$resource}: gallery includes five photos");
        $request = new ResourceRequest($data);
        $request->setValidator(Validator::make($data, $rules));
        (new ResourceController)->update($request, $resource, $record->id);
        $record->refresh();
        $assert($record->image_path_5 === $paths[4], "{$resource}: empty input preserves existing photos");
    } finally {
        DB::table($record->getTable())->where('id', $record->id)->update($before);
        Storage::disk('public')->delete(array_merge($paths, array_values($oldPaths)));
    }
}
