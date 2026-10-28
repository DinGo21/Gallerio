<?php
/**
 * Upload form.
 *
 * @var list<string> $errors
 * @var array{title: string, description: string, category: string, tags: string} $values
 * @var list<string> $categories
 * @var string|null $databaseError
 */
?>
<section class="container gallerio-hero text-center">
    <p class="gallerio-eyebrow text-uppercase small fw-semibold mb-2">Contribute</p>
    <h1 class="gallerio-hero-title display-4 mb-3">Upload an image</h1>
    <p class="gallerio-hero-subtitle mx-auto mb-0">
        Share a JPG photograph with the gallery. It will appear in the collection right away.
    </p>
</section>

<section class="container gallerio-upload" aria-label="Upload form">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="gallerio-search">
                <?php if ($databaseError !== null): ?>
                    <div class="alert alert-danger" role="alert"><?= e($databaseError) ?></div>
                <?php endif; ?>

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger" role="alert">
                        <p class="fw-semibold mb-1">The image could not be uploaded:</p>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="upload.php" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label for="upload-image" class="form-label">Image (JPG only)</label>
                        <input type="file" class="form-control" id="upload-image" name="image"
                               accept=".jpg,.jpeg,image/jpeg" required>
                        <div class="form-text">
                            Maximum size: <?= e((string) (int) (GALLERIO_MAX_UPLOAD_BYTES / 1024 / 1024)) ?> MB.
                            Files are stored in <code>images/</code>.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="upload-title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="upload-title" name="title"
                               maxlength="255" value="<?= e($values['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="upload-description" class="form-label">
                            Description <span class="text-muted">(optional)</span>
                        </label>
                        <textarea class="form-control" id="upload-description" name="description" rows="3"><?= e($values['description']) ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="upload-category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="upload-category" name="category"
                                   list="upload-categories" maxlength="100"
                                   value="<?= e($values['category']) ?>" placeholder="e.g. Nature">
                            <datalist id="upload-categories">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= e($category) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="upload-tags" class="form-label">
                                Tags <span class="text-muted">(optional, comma-separated)</span>
                            </label>
                            <input type="text" class="form-control" id="upload-tags" name="tags"
                                   maxlength="255" value="<?= e($values['tags']) ?>"
                                   placeholder="e.g. forest, lake, autumn">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn gallerio-btn-primary"<?= $databaseError !== null ? ' disabled' : '' ?>>
                            Upload image
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
