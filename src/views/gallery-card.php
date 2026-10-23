<?php
/**
 * Renders a single gallery card.
 *
 * The card links to the full-size image, so it still works if JavaScript is
 * disabled; the lightbox intercepts the click when JavaScript is available.
 *
 * @var array{
 *     file: string,
 *     title: string,
 *     description: string,
 *     category: string,
 *     tags: list<string>
 * } $image
 */
?>
<a href="<?= e(image_url($image['file'])) ?>"
   class="card gallery-card h-100 text-decoration-none"
   data-image="<?= e(image_url($image['file'])) ?>"
   data-title="<?= e($image['title']) ?>"
   data-description="<?= e($image['description']) ?>"
   data-category="<?= e($image['category']) ?>"
   data-tags="<?= e(implode(', ', $image['tags'])) ?>"
   aria-label="View <?= e($image['title']) ?> in the lightbox">
    <div class="gallery-thumb">
        <img src="<?= e(image_url($image['file'])) ?>"
             alt="<?= e($image['title']) ?>"
             class="gallery-thumb-img"
             loading="lazy"
             decoding="async">
        <div class="gallery-thumb-overlay" aria-hidden="true">
            <span class="badge text-bg-light gallery-badge"><?= e($image['category']) ?></span>
            <span class="gallery-zoom">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
        </div>
    </div>
    <div class="card-body gallery-card-body">
        <h2 class="gallery-card-title"><?= e($image['title']) ?></h2>
        <?php if ($image['description'] !== ''): ?>
            <p class="gallery-card-text"><?= e($image['description']) ?></p>
        <?php endif; ?>
    </div>
</a>
