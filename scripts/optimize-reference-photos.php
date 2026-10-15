<?php
foreach (glob(__DIR__.'/../public/images/products/*.jpg') as $file) {
    $image = imagecreatefromjpeg($file);
    $ratio = min(1, 1200 / max(imagesx($image), imagesy($image)));
    $output = imagescale($image, (int) (imagesx($image) * $ratio), (int) (imagesy($image) * $ratio));
    imagewebp($output, str_replace('.jpg', '.webp', $file), 85);
}
foreach (array_merge(glob(__DIR__.'/../public/images/products/*-packshot.png'), glob(__DIR__.'/../public/images/catalog-gallery/*.png'), glob(__DIR__.'/../public/images/home-products/*.png')) as $file) {
    $image = imagecreatefrompng($file);
    $ratio = min(1, 1200 / max(imagesx($image), imagesy($image)));
    $output = imagescale($image, (int) (imagesx($image) * $ratio), (int) (imagesy($image) * $ratio));
    imagewebp($output, str_replace('.png', '.webp', $file), 88);
}
