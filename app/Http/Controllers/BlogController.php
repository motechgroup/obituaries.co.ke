<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public static function getArticles(): array
    {
        return [
            'how-to-write-an-obituary-in-kenya' => [
                'title' => 'How to Write and Publish an Obituary Notice in Kenya: A Complete Guide',
                'slug' => 'how-to-write-an-obituary-in-kenya',
                'meta_description' => 'Learn how to write a respectful and comprehensive obituary notice in Kenya. Includes formatting tips, vital details to include, and online publishing options.',
                'keywords' => 'how to write an obituary in Kenya, Kenya obituary guide, publish obituary online Kenya, cost of publishing obituary Kenya',
                'published_at' => '2026-07-20',
                'author' => 'Obituaries.co.ke Editorial Team',
                'content' => '
                    <p class="mb-4">Writing an obituary for a loved one is a deeply meaningful responsibility. In Kenya, obituary notices serve not only as a public announcement of a passing, but also as a vital notice for family members, friends, colleagues, and the wider community to gather for funeral arrangements, prayers, and tributes.</p>
                    
                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">1. Key Information Every Kenyan Obituary Must Include</h2>
                    <ul class="list-disc pl-6 space-y-2 mb-6 text-on-surface-variant">
                        <td><strong>Deceased Full Name & Preferred Title:</strong> State the full official name as well as popular titles or community names (e.g., Mzee, Dr., Elder, Mama, Elder, Chief).</td>
                        <td><strong>Dates of Birth and Passing:</strong> Clearly state the sunrise (birth date) and sunset (passing date).</td>
                        <td><strong>County & Hometown / Ancestral Home:</strong> In Kenya, specifying the home county, town, and village helps distant relatives identify the family.</td>
                        <td><strong>Key Family Relationships:</strong> Mention surviving spouse, children, parents, siblings, and grandchildren.</td>
                        <td><strong>Funeral & Burial Details:</strong> State the date, venue (church service location and burial location), and time of the service.</td>
                    </ul>

                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">2. Step-by-Step Writing Structure</h2>
                    <p class="mb-4">Start with the announcement sentence: <em>"It is with deep sorrow and humble acceptance of God\'s will that we announce the passing of..."</em>. Follow with a brief paragraph honoring their life, achievements, career, and community contributions.</p>

                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">3. Publishing Online vs Traditional Newspapers in Kenya</h2>
                    <p class="mb-4">While traditional newspaper notices have space constraints and high daily rates, publishing an online obituary on <strong>Obituaries.co.ke</strong> provides unlimited text, photo gallery uploads, downloadable funeral programmes, and virtual candle tributes accessible to family worldwide.</p>
                '
            ],
            'funeral-traditions-in-kenya' => [
                'title' => 'Understanding Funeral Traditions, Eulogies, and Announcements in Kenya',
                'slug' => 'funeral-traditions-in-kenya',
                'meta_description' => 'Explore the rich funeral customs, tribute traditions, and memorial announcements across different communities in Kenya.',
                'keywords' => 'funeral traditions Kenya, burial traditions Kenya, funeral announcement Kenya, memorial traditions Kenya',
                'published_at' => '2026-07-22',
                'author' => 'Obituaries.co.ke Cultural Review',
                'content' => '
                    <p class="mb-4">Kenya is a country rich in cultural diversity, and funeral traditions reflect deep-seated values of community, respect for ancestors, and spiritual faith. Across Central, Nyanza, Western, Coastal, and Rift Valley regions, funeral gatherings are central community events.</p>
                    
                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">1. Funeral Planning Meetings (Payable Support & Prayers)</h2>
                    <p class="mb-4">In Kenya, when a death occurs, family and community members convene daily for evening prayers and planning meetings. These gatherings provide emotional support and assist with funeral budget preparations.</p>

                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">2. Funeral Programmes & Printed Eulogies</h2>
                    <p class="mb-4">A standard Kenyan funeral service features a printed funeral programme containing the order of service, life history (eulogy), tribute messages from family, and hymns. Digital distribution of these programmes via PDF downloads has become increasingly essential for diaspora relatives.</p>
                '
            ],
            'difference-between-obituary-and-death-notice' => [
                'title' => 'Difference Between a Death Notice, Obituary, and Appreciation Message in Kenya',
                'slug' => 'difference-between-obituary-and-death-notice',
                'meta_description' => 'Understand the differences between a death notice, obituary, and appreciation message in Kenya to choose the right format for your family.',
                'keywords' => 'difference between obituary and death notice, death notice Kenya, obituary vs death notice, appreciation notice Kenya',
                'published_at' => '2026-07-25',
                'author' => 'Obituaries.co.ke Editorial Team',
                'content' => '
                    <p class="mb-4">When a loved one passes away, families in Kenya often use terms like "Death Notice", "Obituary", and "Appreciation Announcement" interchangeably. However, each serves a distinct purpose during the memorial process.</p>

                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">1. Death Notice</h2>
                    <p class="mb-4">A <strong>Death Notice</strong> is a short, immediate public announcement informing the community of a death and giving initial details regarding daily prayer meetings or contacts for condolences.</p>

                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">2. Obituary</h2>
                    <p class="mb-4">An <strong>Obituary</strong> is a comprehensive tribute that details the person\'s full life story, family tree, milestones, and specific funeral service arrangements.</p>

                    <h2 class="font-serif text-2xl font-bold text-primary mt-8 mb-4">3. Appreciation Message</h2>
                    <p class="mb-4">An <strong>Appreciation Message</strong> (or Acknowledgement Notice) is published 1 to 4 weeks after the burial to express heartfelt gratitude to relatives, doctors, churches, and friends who stood with the family.</p>
                '
            ],
        ];
    }

    public function index()
    {
        $articles = static::getArticles();
        return view('blog.index', compact('articles'));
    }

    public function show($slug)
    {
        $articles = static::getArticles();
        if (!isset($articles[$slug])) {
            abort(404);
        }

        $article = $articles[$slug];
        return view('blog.show', compact('article', 'articles'));
    }
}
