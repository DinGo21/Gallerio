    </main>

    <footer id="about" class="gallerio-footer">
        <div class="container py-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="mb-0 small text-muted">
                &copy; <?= e((string) date('Y')) ?> <?= e(GALLERIO_NAME) ?> &middot; <?= e(GALLERIO_TAGLINE) ?>
            </p>
            <p class="mb-0 small text-muted">
                Built with vanilla PHP &amp; Bootstrap.
            </p>
        </div>
    </footer>

    <script src="<?= e(asset('vendor/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(asset('js/gallery.js')) ?>"></script>
</body>
</html>
