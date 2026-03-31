<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Intervention\Image\Laravel\Facades\Image;
try {
    $file = public_path('uploads/jobs/original_before_19_1772855324.jpg');
    if(!file_exists($file)) die("File not found\n");
    
    $img = Image::read($file);
    echo "Image loaded. Size: " . $img->width() . "x" . $img->height() . "\n";
    
    $img->text('Test Text With Custom Font', 50, 150, function ($font) {
        $font->file(public_path('fonts/PlusJakartaSans.ttf'));
        $font->size(100);
        $font->color('FF0000');
    });
    
    $outFile = public_path('uploads/jobs/test_wm.jpg');
    $img->save($outFile);
    echo "OK - saved to $outFile\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
