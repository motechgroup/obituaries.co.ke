<?php

namespace Tests\Unit;

use App\Helpers\StorageHelper;
use PHPUnit\Framework\TestCase;

class StorageHelperTest extends TestCase
{
    public function test_format_biography_html_converts_plain_text_newlines_to_paragraphs()
    {
        $plainText = "Early Life\n\nJoseph was born in Nyeri in 1945.\nHe attended local schools.\n\nCareer & Legacy\nHe served as a teacher.";

        $formatted = StorageHelper::formatBiographyHtml($plainText);

        $this->assertStringContainsString('<p>Early Life</p>', $formatted);
        $this->assertStringContainsString('<p>Joseph was born in Nyeri in 1945.<br>He attended local schools.</p>', $formatted);
        $this->assertStringContainsString('<p>Career & Legacy<br>He served as a teacher.</p>', $formatted);
    }

    public function test_format_biography_html_preserves_existing_html_tags()
    {
        $htmlText = "<h3>Early Life</h3><p>Joseph was born in Nyeri in 1945.</p><p>He served as a teacher.</p>";

        $formatted = StorageHelper::formatBiographyHtml($htmlText);

        $this->assertEquals($htmlText, $formatted);
    }
}
