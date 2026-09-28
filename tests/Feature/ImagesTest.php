<?php

namespace Tests\Feature;

use App\Support\Images;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImagesTest extends TestCase
{
    private string $manifestBackup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manifestBackup = (string) @file_get_contents(Images::manifestPath());
        File::ensureDirectoryExists(public_path('images'));
        File::put(Images::manifestPath(), json_encode([
            'demo' => ['widths' => [640, 960, 1280], 'width' => 1280, 'height' => 720, 'source' => 'x'],
            'small' => ['widths' => [300], 'width' => 300, 'height' => 200, 'source' => 'y'],
        ]));
        Images::flush();
    }

    protected function tearDown(): void
    {
        File::put(Images::manifestPath(), $this->manifestBackup);
        Images::flush();

        parent::tearDown();
    }

    public function test_component_renders_srcset_and_intrinsic_size(): void
    {
        $html = Blade::render('<x-image slug="demo" alt="Demo" />');

        $this->assertStringContainsString('demo-960.webp', $html);
        $this->assertStringContainsString('demo-640.webp 640w', $html);
        $this->assertStringContainsString('width="1280"', $html);
        $this->assertStringContainsString('height="720"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
    }

    public function test_eager_image_gets_high_priority(): void
    {
        $html = Blade::render('<x-image slug="demo" :eager="true" />');

        $this->assertStringContainsString('fetchpriority="high"', $html);
        $this->assertStringContainsString('loading="eager"', $html);
    }

    public function test_pinned_width_drops_srcset(): void
    {
        $html = Blade::render('<x-image slug="demo" :width="640" />');

        $this->assertStringContainsString('demo-640.webp', $html);
        $this->assertStringNotContainsString('srcset', $html);
        $this->assertStringContainsString('width="1280"', $html);
    }

    public function test_unknown_slug_renders_nothing(): void
    {
        $this->assertSame('', trim(Blade::render('<x-image slug="missing" />')));
    }

    public function test_small_source_is_not_upscaled(): void
    {
        $this->assertSame(Images::url('small', 300), Images::src('small'));
    }

    public function test_optimize_command_writes_variants_and_manifest(): void
    {
        $source = resource_path('images/__test.png');
        $image = imagecreatetruecolor(1000, 500);
        imagepng($image, $source);

        try {
            $this->artisan('images:optimize')->assertSuccessful();

            Images::flush();
            $this->assertSame([640, 960], Images::manifest()['__test']['widths']);
            $this->assertFileExists(public_path('images/__test-640.webp'));
            $this->assertSame([960, 480], Images::size('__test'));
        } finally {
            File::delete($source);
            $this->artisan('images:optimize');
            $this->assertFileDoesNotExist(public_path('images/__test-640.webp'));
        }
    }
}
