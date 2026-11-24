<?php
/**
 * Renders a single gallery card.
 *
 * The card links to the full-size image, so it still works if JavaScript is
 * disabled; the lightbox intercepts the click when JavaScript is available.
 * Uploaded images also expose a delete form, which is kept outside the link so
 * it does not trigger the lightbox.
 *
 * @var array{
 *     id: int,
 *     file: string,
 *     title: string,
 *     description: string,
 *     category: string,
 *     tags: list<string>
 * } $image
 */
?>
<div class="card gallery-card h-100">
    <a href="<?= e(image_url($image['file'])) ?>"
       class="gallery-card-link d-flex flex-column h-100 text-decoration-none"
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

    <?php if (!is_curated_image($image['file'])): ?>
        <form method="post" action="delete.php" class="gallery-delete"
              data-confirm="Delete “<?= e($image['title']) ?>”? This cannot be undone.">
            <input type="hidden" name="id" value="<?= (int) $image['id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger" title="Delete image"
                    aria-label="Delete <?= e($image['title']) ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                    <path d="M10 11v6"></path>
                    <path d="M14 11v6"></path>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                </svg>
            </button>
        </form>
    <?php endif; ?>
</div>
