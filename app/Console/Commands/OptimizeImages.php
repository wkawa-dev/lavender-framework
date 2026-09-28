<?php

namespace App\Console\Commands;

use App\Support\Images;
use GdImage;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('images:optimize {--force : Rebuild variants even when the source is unchanged}')]
#[Description('Compress resources/images into responsive WebP variants (640/960/1280, q80) in public/images')]
class OptimizeImages extends Command
{
    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('GD with WebP support is required (php -r "var_dump(gd_info());").');

            return self::FAILURE;
        }

        $sourceDir = resource_path('images');
        $outDir = public_path('images');
        File::ensureDirectoryExists($outDir);

        $previous = Images::manifest();
        $manifest = [];

        foreach (File::files($sourceDir) as $file) {
            $ext = strtolower($file->getExtension());

            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $slug = $file->getFilenameWithoutExtension();
            $hash = hash_file('sha256', $file->getPathname());

            if (! $this->option('force') && ($previous[$slug]['source'] ?? null) === $hash && $this->variantsExist($slug, $previous[$slug]['widths'])) {
                $manifest[$slug] = $previous[$slug];
                $this->line("  <fg=gray>skip</> {$slug}");

                continue;
            }

            $image = $this->load($file->getPathname(), $ext);

            if ($image === null) {
                $this->warn("  could not read {$file->getFilename()}");

                continue;
            }

            $sourceWidth = imagesx($image);
            $sourceHeight = imagesy($image);
            // Never upscale; a small source still gets one variant at its own width.
            $widths = array_values(array_filter(Images::WIDTHS, fn (int $w) => $w <= $sourceWidth)) ?: [$sourceWidth];
            $bytes = 0;

            foreach ($widths as $width) {
                $height = (int) round($sourceHeight * $width / $sourceWidth);
                $resized = imagecreatetruecolor($width, $height);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
                $target = "{$outDir}/{$slug}-{$width}.webp";
                imagewebp($resized, $target, Images::QUALITY);
                $bytes += filesize($target);
            }

            $largest = end($widths);
            $manifest[$slug] = [
                'widths' => $widths,
                'width' => $largest,
                'height' => (int) round($sourceHeight * $largest / $sourceWidth),
                'source' => $hash,
            ];

            $this->line(sprintf('  <info>done</> %s  %s  %s KB', $slug, implode('/', $widths), number_format($bytes / 1024, 1)));
        }

        foreach (array_diff_key($previous, $manifest) as $slug => $entry) {
            foreach ($entry['widths'] as $width) {
                File::delete("{$outDir}/{$slug}-{$width}.webp");
            }

            $this->line("  <fg=yellow>removed</> {$slug}");
        }

        ksort($manifest);
        File::put(Images::manifestPath(), json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
        Images::flush();

        return self::SUCCESS;
    }

    private function load(string $path, string $ext): ?GdImage
    {
        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => @imagecreatefromwebp($path),
        };

        return $image instanceof GdImage ? $image : null;
    }

    /**
     * @param  list<int>  $widths
     */
    private function variantsExist(string $slug, array $widths): bool
    {
        foreach ($widths as $width) {
            if (! is_file(public_path("images/{$slug}-{$width}.webp"))) {
                return false;
            }
        }

        return true;
    }
}
