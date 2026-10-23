<?php
/**
 * Bootstrap modal used as the image lightbox.
 *
 * The markup is populated by assets/js/gallery.js when a card is opened.
 */
?>
<div class="modal fade" id="lightbox" tabindex="-1" role="dialog" aria-labelledby="lightboxTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content gallerio-lightbox">
            <div class="modal-header gallerio-lightbox-header">
                <div class="me-3">
                    <h2 class="modal-title h5 mb-1" id="lightboxTitle"></h2>
                    <span class="badge text-bg-light gallerio-lightbox-category" id="lightboxCategory"></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body gallerio-lightbox-body">
                <img id="lightboxImage" class="gallerio-lightbox-img" src="" alt="">
                <button type="button" class="gallerio-lightbox-nav gallerio-lightbox-prev" id="lightboxPrev" aria-label="Previous image">&#8249;</button>
                <button type="button" class="gallerio-lightbox-nav gallerio-lightbox-next" id="lightboxNext" aria-label="Next image">&#8250;</button>
            </div>
            <div class="modal-footer flex-column align-items-stretch gallerio-lightbox-footer">
                <p class="gallerio-lightbox-description mb-2" id="lightboxDescription"></p>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="gallerio-lightbox-tags" id="lightboxTags"></div>
                    <small class="text-white-50" id="lightboxCounter"></small>
                </div>
            </div>
        </div>
    </div>
</div>
