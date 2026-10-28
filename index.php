<?php

declare(strict_types=1);

/*
 * Gallerio front controller.
 *
 * This is the single entry point of the website. It reads the search query,
 * asks the repository for the matching images and renders the page.
 */

require_once __DIR__ . '/src/init.php';

// ---------------------------------------------------------------------------
// Request handling
// ---------------------------------------------------------------------------

$query = trim((string) ($_GET['q'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$hasFilters = ($query !== '' || $category !== '');
$uploaded = (($_GET['uploaded'] ?? '') === '1');

$databaseError = null;
$images = [];
$filtered = [];
$categories = [];

try {
    $repository = new ImageRepository();
    $images = $repository->all();
    $filtered = $repository->filter($images, $query, $category);
    $categories = $repository->categories($images);
} catch (PDOException $exception) {
    $databaseError = 'The image database is unavailable. Start MariaDB and try again.';
}

$count = count($filtered);
$summary = $hasFilters
    ? sprintf('%d %s found', $count, $count === 1 ? 'image' : 'images')
    : sprintf('%d curated %s', $count, $count === 1 ? 'image' : 'images');

$activeFilter = $query !== '' ? $query : $category;
$pageTitle = $hasFilters ? sprintf('Search: %s', $activeFilter) : GALLERIO_NAME;
$pageDescription = $hasFilters
    ? sprintf('Search results in Gallerio for “%s”.', $activeFilter)
    : GALLERIO_DESCRIPTION;

require __DIR__ . '/src/views/header.php';
?>

<section class="container gallerio-hero text-center">
    <p class="gallerio-eyebrow text-uppercase small fw-semibold mb-2">Curated collection</p>
    <h1 class="gallerio-hero-title display-4 mb-3"><?= e(GALLERIO_NAME) ?></h1>
    <p class="gallerio-hero-subtitle mx-auto mb-0">
        <?= e(GALLERIO_TAGLINE) ?> Browse the collection below and open any photograph in the lightbox.
    </p>
</section>

<section class="container gallerio-toolbar" aria-label="Search and filter">
    <form class="gallerio-search row g-2 align-items-center" method="get" action="index.php" role="search">
        <div class="col-12 col-md">
            <label class="visually-hidden" for="search-q">Search images</label>
            <div class="input-group">
                <span class="input-group-text" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="search" id="search-q" name="q" class="form-control"
                       placeholder="Search by keyword, e.g. forest or wolf"
                       value="<?= e($query) ?>" autocomplete="off">
            </div>
        </div>
        <div class="col-12 col-sm-auto">
            <label class="visually-hidden" for="search-category">Category</label>
            <select id="search-category" name="category" class="form-select">
                <option value="">All categories</option>
                <?php foreach ($categories as $option): ?>
                    <option value="<?= e($option) ?>"<?= $option === $category ? ' selected' : '' ?>><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-sm-auto d-flex gap-2">
            <button type="submit" class="btn gallerio-btn-primary">Search</button>
            <?php if ($hasFilters): ?>
                <a href="index.php" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<?php if ($databaseError !== null): ?>
    <section class="container gallerio-results">
        <div class="alert alert-danger" role="alert">
            <?= e($databaseError) ?>
        </div>
    </section>
<?php else: ?>
    <?php if ($uploaded): ?>
        <section class="container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Your image was uploaded successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </section>
    <?php endif; ?>

    <section class="container gallerio-results" id="gallery" aria-label="Gallery results">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h5 mb-0">Gallery</h2>
            <p class="mb-0 text-muted small" id="results-summary"><?= e($summary) ?></p>
        </div>

        <?php if ($count === 0): ?>
            <div class="alert alert-light border text-center gallerio-empty" role="status">
                <p class="mb-2">No images match your search.</p>
                <a href="index.php" class="btn btn-outline-secondary btn-sm">View all images</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($filtered as $image): ?>
                    <div class="col">
                        <?php require __DIR__ . '/src/views/gallery-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php
require __DIR__ . '/src/views/lightbox.php';
require __DIR__ . '/src/views/footer.php';
