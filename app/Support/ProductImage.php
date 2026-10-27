<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class ProductImage
{
    public static function gallery($record): array
    {
        $photos = [self::url($record->name, $record->image_path)];

        for ($slot = 2; $slot <= 5; $slot++) {
            $path = $record->{'image_path_' . $slot};

            if ($path) {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                $disk = Storage::disk('public');

                if ($disk->exists($path)) {
                    $photos[$slot - 1] = $disk->url($path);
                }
            }
        }

        return $photos;
    }

    public static function url(string $name, ?string $path = null): string
    {
        if ($path) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');

            if ($disk->exists($path)) {
                return $disk->url($path);
            }
        }

        $name = mb_strtolower($name);

        foreach (['selada', 'mentimun', 'wortel', 'bayam', 'buncis', 'kol'] as $slug) {
            if (str_contains($name, $slug)) {
                $suffix = in_array($slug, ['mentimun', 'bayam'], true) ? '-packshot' : '';
                return asset("images/products/{$slug}{$suffix}.webp");
            }
        }

        foreach ([
            'keripik pisang' => 'keripik-pisang',
            'saus tomat' => 'saus-tomat',
            'sambal' => 'sambal',
            'cabai' => 'cabai',
            'tomat' => 'tomat',
            'kangkung' => 'kangkung',
            'sawi' => 'sawi',
            'jagung' => 'jagung',
            'terong' => 'terong',
        ] as $keyword => $slug) {
            if (str_contains($name, $keyword)) {
                $suffix = in_array($slug, ['saus-tomat', 'keripik-pisang', 'sambal', 'jagung'], true) ? '-packshot' : '';
                return asset("images/products/{$slug}{$suffix}.webp");
            }
        }

        return asset('images/garden.svg');
    }
}
