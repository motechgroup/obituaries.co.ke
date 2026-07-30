<?php

namespace Tests\Feature;

use App\Services\ImageOptimizerEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_optimizer_engine_resizes_and_compresses_image()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('PHP GD extension not loaded.');
        }

        // Create a 1200x1200 test JPEG image in memory
        $origWidth = 1200;
        $origHeight = 1200;
        $img = imagecreatetruecolor($origWidth, $origHeight);
        $blue = imagecolorallocate($img, 0, 102, 204);
        imagefilledrectangle($img, 0, 0, $origWidth, $origHeight, $blue);

        $tempPath = storage_path('app/test_large_image.jpg');
        imagejpeg($img, $tempPath, 100);
        imagedestroy($img);

        $initialSize = filesize($tempPath);
        $this->assertGreaterThan(0, $initialSize);

        // Run ImageOptimizerEngine with max dimension 600px and quality 75%
        $optimizer = new ImageOptimizerEngine(600, 75);
        $result = $optimizer->optimizeImage($tempPath);

        $this->assertTrue($result['success']);
        $this->assertLessThan($initialSize, $result['compressed_size']);

        // Verify dimensions were scaled down to 600x600
        $info = getimagesize($tempPath);
        $this->assertEquals(600, $info[0]);
        $this->assertEquals(600, $info[1]);

        @unlink($tempPath);
    }

    public function test_artisan_images_optimize_command_executes_successfully()
    {
        $this->artisan('images:optimize')
            ->assertExitCode(0);
    }
}
