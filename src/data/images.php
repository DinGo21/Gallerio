<?php

declare(strict_types=1);

/*
 * Curated metadata for the gallery images.
 *
 * Each key is a file name stored in the `images/` directory. Files are
 * discovered automatically; adding an entry here simply gives a photograph a
 * proper title, description, category and tags. Images without an entry fall
 * back to a title derived from their file name.
 *
 * @return array<string, array{title: string, description: string, category: string, tags: list<string>}>
 */
function gallerio_image_data(): array
{
    return [
        'ahmetyuksek-forest-reflection-10124031_1920.jpg' => [
            'title' => 'Forest Reflection',
            'description' => 'A wooden hut on a still lake mirrors an autumn forest in warm fall colours.',
            'category' => 'Nature',
            'tags' => ['forest', 'reflection', 'lake', 'autumn', 'trees', 'hut'],
        ],
        'eyyuperten-cat-9720087_1920.jpg' => [
            'title' => 'Street Cat of Istanbul',
            'description' => 'A fluffy street cat pauses on a cobbled Istanbul lane as the evening light glows.',
            'category' => 'Animals',
            'tags' => ['cat', 'animal', 'istanbul', 'street', 'pet'],
        ],
        'giraffew-budapest-10498528.jpg' => [
            'title' => 'Budapest at Dusk',
            'description' => 'The Chain Bridge and St. Stephen’s Basilica glow above the Danube as the city settles into dusk.',
            'category' => 'Travel',
            'tags' => ['budapest', 'bridge', 'danube', 'city', 'dusk', 'hungary'],
        ],
        'joshwiiieee-water-10388382_1920.jpg' => [
            'title' => 'River Journey',
            'description' => 'Two travellers glide down a calm river in a carved canoe beneath forested hills.',
            'category' => 'Nature',
            'tags' => ['river', 'water', 'canoe', 'boat', 'mountains'],
        ],
        'pen_ash-snow-10476274_1920.jpg' => [
            'title' => 'Footprints in the Snow',
            'description' => 'A trail of footprints crosses a quiet, snow-covered valley toward distant mountains.',
            'category' => 'Nature',
            'tags' => ['snow', 'winter', 'landscape', 'footprints', 'mountains'],
        ],
        'schwarzwaldandi-wolf-10522670.jpg' => [
            'title' => 'The Wolf',
            'description' => 'A wolf rests among fallen leaves, its amber eyes watching the forest.',
            'category' => 'Animals',
            'tags' => ['wolf', 'animal', 'wildlife', 'forest', 'wild'],
        ],
        'treptoweralex-usa-9889580_1920.jpg' => [
            'title' => 'Antelope Canyon',
            'description' => 'Waves of purple and orange sandstone curve through a slot canyon in the American Southwest.',
            'category' => 'Travel',
            'tags' => ['canyon', 'usa', 'arizona', 'desert', 'sandstone'],
        ],
        'wolfgang_hasselmann-desert-10463843_1920.jpg' => [
            'title' => 'Desert Dunes',
            'description' => 'Golden sand dunes stretch toward a pale sky in a minimal desert landscape.',
            'category' => 'Nature',
            'tags' => ['desert', 'dunes', 'sand', 'landscape'],
        ],
    ];
}
