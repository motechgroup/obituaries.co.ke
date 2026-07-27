<?php

namespace Tests\Feature;

use App\Models\Obituary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_is_accessible()
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /admin/');
        $response->assertSee('Sitemap: https://obituaries.co.ke/sitemap.xml');
    }

    public function test_sitemap_xml_returns_valid_xml()
    {
        Obituary::create([
            'slug' => 'test-seo-john-doe',
            'full_name' => 'Test SEO John Doe',
            'date_of_birth' => '1960-01-01',
            'date_of_death' => '2026-06-01',
            'county' => 'Nairobi',
            'town' => 'Kilimani',
            'biography' => 'Test biography for SEO sitemap.',
            'submitter_name' => 'Jane Doe',
            'submitter_phone' => '0711111111',
            'relationship' => 'Child',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('nairobi-obituaries');
        $response->assertSee('test-seo-john-doe');
    }

    public function test_county_landing_page_loads_published_obituaries()
    {
        Obituary::create([
            'slug' => 'nairobi-mzee-samuel',
            'full_name' => 'Mzee Samuel Nairobi',
            'date_of_birth' => '1950-01-01',
            'date_of_death' => '2026-07-01',
            'county' => 'Nairobi',
            'town' => 'Westlands',
            'biography' => 'Mzee Samuel lived in Nairobi.',
            'submitter_name' => 'Family Submitter',
            'submitter_phone' => '0722222222',
            'relationship' => 'Family',
            'status' => 'published',
            'verification_status' => 'verified',
        ]);

        $response = $this->get('/county/nairobi-obituaries');
        $response->assertStatus(200);
        $response->assertSee('Nairobi Obituaries &amp; Death Notices', false);
        $response->assertSee('Mzee Samuel Nairobi');
    }

    public function test_blog_index_and_article_pages()
    {
        $indexResponse = $this->get('/blog');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Resource Center & Guides for Kenya', false);

        $articleResponse = $this->get('/blog/how-to-write-an-obituary-in-kenya');
        $articleResponse->assertStatus(200);
        $articleResponse->assertSee('How to Write and Publish an Obituary Notice in Kenya');
    }
}
