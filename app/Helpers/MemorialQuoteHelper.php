<?php

namespace App\Helpers;

use App\Models\Obituary;

class MemorialQuoteHelper
{
    /**
     * Get a unique memorial quote for an obituary deterministically based on its ID or Slug.
     */
    public static function getQuoteForObituary(Obituary $obituary): string
    {
        $quotes = static::getAllQuotes();
        $count = count($quotes);

        if ($count === 0) {
            return "A tree is known by its fruit, and a man by his deeds. A forest of kindness.";
        }

        // Use integer ID or string hash of slug/id to deterministically select index
        $index = abs(crc32($obituary->slug ?? (string) $obituary->id)) % $count;
        $quote = $quotes[$index];

        // Replace placeholders if any
        $nameParts = explode(' ', trim(strip_tags($obituary->full_name)));
        $firstName = $nameParts[0] ?? $obituary->full_name;

        $quote = str_replace(
            ['{NAME}', '{FIRST_NAME}', '{COUNTY}', '{TOWN}'],
            [$obituary->full_name, $firstName, $obituary->county ?? 'Kenya', $obituary->town ?? 'Home'],
            $quote
        );

        return $quote;
    }

    /**
     * Get a random quote from the 1,000 quotes collection.
     */
    public static function getRandomQuote(): string
    {
        $quotes = static::getAllQuotes();
        return $quotes[array_rand($quotes)];
    }

    /**
     * Complete seed array of 1,000 unique memorial quotes & proverbs.
     */
    public static function getAllQuotes(): array
    {
        return [
            // African & Kenyan Legacy Proverbs
            "A tree is known by its fruit, and a person by their noble deeds. A forest of kindness.",
            "When a great tree falls in the forest, the echo reverberates across generations.",
            "The sun has set on a remarkable journey, but the light left behind will shine forever.",
            "A life well lived is a beacon that guides family and community for years to come.",
            "Those we love don't go away; they walk beside us in every memory we cherish.",
            "To live in hearts we leave behind is not to die, but to live eternally in grace.",
            "Your legacy is written not on granite stones, but in the lives you touched with love.",
            "A river flows on long after it has reached the sea; so does the influence of a good soul.",
            "A weaver of warmth, unity, and strength. Your memory remains our family anchor.",
            "Though the voice is silent, the spirit speaks through every lesson taught and love given.",
            "A journey completed with dignity, courage, and unshakeable faith in Almighty God.",
            "Peace is the calm harbor where a righteous life finds eternal rest after a good fight.",
            "Like a morning dew on green pastures, your gentle kindness refreshed everyone around you.",
            "The body rests in dust, but the soul soars high into eternal victory with God.",
            "Blessed are the pure in heart, for they shall see God and rest in His divine glory.",
            "A hero of faith, a pillar of wisdom, and a loving protector of the family generation.",
            "In God's hands you rest, in our hearts you remain forever loved and missed.",
            "Your laughter was a melody of joy, and your counsel a compass for our footsteps.",
            "The earth holds your resting place, but Heaven celebrates your glorious homecoming.",
            "Love never ends; it transforms into an enduring light that brightens our dark days.",

            // Biblical & Scriptural Reflection Quotes
            "I have fought the good fight, I have finished the race, I have kept the faith. — 2 Timothy 4:7",
            "The Lord is my shepherd; I shall not want. He makes me lie down in green pastures. — Psalm 23:1-2",
            "Precious in the sight of the Lord is the death of His faithful servants. — Psalm 116:15",
            "Well done, good and faithful servant; enter into the joy of your Lord. — Matthew 25:21",
            "Blessed are those who mourn, for they shall be comforted by divine peace. — Matthew 5:4",
            "God will wipe away every tear from their eyes; there shall be no more death nor sorrow. — Revelation 21:4",
            "He will swallow up death forever, and the Lord God will wipe away tears from all faces. — Isaiah 25:8",
            "To everything there is a season, a time for every purpose under heaven. — Ecclesiastes 3:1",
            "The memory of the righteous is a everlasting blessing to generations. — Proverbs 10:7",
            "Peace I leave with you; my peace I give to you. Let not your heart be troubled. — John 14:27",
            "I am the resurrection and the life. Whoever believes in Me shall live even if he dies. — John 11:25",
            "For to me, to live is Christ, and to die is gain. — Philippians 1:21",
            "The dust returns to the earth as it was, and the spirit returns to God who gave it. — Ecclesiastes 12:7",
            "He leads me beside still waters; He restores my soul in eternal righteousness. — Psalm 23:2-3",
            "Eye has not seen, nor ear heard, the things which God has prepared for those who love Him. — 1 Corinthians 2:9",
            "The Lord gave, and the Lord has taken away; blessed be the name of the Lord. — Job 1:21",
            "Surely goodness and mercy shall follow me all the days of my life, and I will dwell in the house of the Lord forever. — Psalm 23:6",
            "Though I walk through the valley of the shadow of death, I will fear no evil. — Psalm 23:4",
            "Be faithful until death, and I will give you the crown of life. — Revelation 2:10",
            "Resting in the everlasting arms of the Almighty Creator.",

            // Legacy & Character Tributes
            "A pillar of strength, courage, and unconditional love to family and community.",
            "Your generosity built bridges where there were rivers, and hope where there was despair.",
            "A life defined by humility, hard work, deep faith, and devotion to others.",
            "You taught us that the greatest legacy is a heart full of integrity and grace.",
            "Your smile lit up rooms, and your wisdom guided countless lives through storms.",
            "A true elder whose counsel was sought by many and whose word was a bond of truth.",
            "Gone from our sight, but never from our hearts, our prayers, or our daily thoughts.",
            "May the angels guide you to paradise, where peace reigns forever without pain.",
            "A beloved parent, mentor, and protector. Your footsteps will guide us always.",
            "A life that inspired, a heart that loved, and a legacy that will endure forever.",

            // Expanded 1000 Seed Quotes Generator Array
            ...static::generateExtendedQuotes()
        ];
    }

    /**
     * Generate structured, unique quotes to reach 1,000 distinct items.
     */
    private static function generateExtendedQuotes(): array
    {
        $quotes = [];

        $themes = [
            "A life of honor, grace, and unwavering faith. Rest peacefully in God's eternal embrace.",
            "Your gentle spirit and loving guidance will remain etched in our hearts forever.",
            "In every prayer, in every shared story, your memory continues to shine brightly.",
            "A pillar of wisdom and unity whose kindness touched every life in the community.",
            "Resting with the angels in perfect heavenly peace after a journey well lived.",
            "Your legacy of love, hard work, and integrity will inspire generations to come.",
            "May the Almighty God grant your soul eternal rest and comfort to the family.",
            "A beautiful life that leaves behind a golden trail of warmth, peace, and love.",
            "Forever cherished, forever remembered, and forever held in our highest esteem.",
            "Though call to higher glory, your teachings and love remain our daily strength.",
            "A life lived with courage, sacrifice, and unselfish devotion to God and family.",
            "Sleep well in God's peaceful garden until we meet again on that glorious morning.",
            "Your work on earth is done; your reward in heaven has just begun.",
            "A quiet strength, a compassionate heart, and a life dedicated to doing good.",
            "In God's hands you rest, in our hearts you live on as an eternal blessing.",
            "A beacon of hope and strength whose light can never be extinguished.",
            "May the lord keep you in His presence where joy never fades and peace endures.",
            "Your life was a testament to love, humility, and faith in the Almighty.",
            "Forever in our thoughts, forever in our prayers, forever loved beyond words.",
            "A righteous journey fulfilled with honor, dignity, and bountiful grace."
        ];

        $qualities = [
            "kindness and generosity",
            "faith and integrity",
            "wisdom and warmth",
            "courage and devotion",
            "humility and grace",
            "strength and love",
            "patience and guidance",
            "truth and dignity",
            "service and dedication",
            "peace and harmony"
        ];

        $blessings = [
            "May God grant your soul eternal rest.",
            "Forever remembered with deep love and gratitude.",
            "Your legacy shines brightly across generations.",
            "Rest peacefully in the Lord's heavenly kingdom.",
            "Your memory is an everlasting blessing to us all.",
            "In God's heavenly grace you reside forever.",
            "Comfort and peace be with your loved ones.",
            "Until we meet again on the celestial shore.",
            "Your life was a true gift from Above.",
            "Shining as an eternal light in our hearts."
        ];

        $counter = 1;
        foreach ($themes as $theme) {
            foreach ($qualities as $quality) {
                foreach ($blessings as $blessing) {
                    $quotes[] = "{$theme} A life distinguished by {$quality}. {$blessing}";
                    if (count($quotes) >= 950) break 3;
                }
            }
        }

        return $quotes;
    }
}
