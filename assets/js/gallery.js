/*
 * Gallerio lightbox.
 *
 * Reads the data-* attributes rendered on each gallery card and opens a
 * Bootstrap modal with the full-size image, title, description, category and
 * tags. Arrow keys and the on-screen buttons move through the currently
 * visible set of images.
 */
(function () {
    'use strict';

    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-image]'));
    if (cards.length === 0) {
        return;
    }

    var modalElement = document.getElementById('lightbox');
    var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    var imageElement = document.getElementById('lightboxImage');
    var titleElement = document.getElementById('lightboxTitle');
    var descriptionElement = document.getElementById('lightboxDescription');
    var categoryElement = document.getElementById('lightboxCategory');
    var tagsElement = document.getElementById('lightboxTags');
    var counterElement = document.getElementById('lightboxCounter');
    var prevButton = document.getElementById('lightboxPrev');
    var nextButton = document.getElementById('lightboxNext');

    var current = 0;

    function readCard(card) {
        return {
            image: card.getAttribute('data-image') || '',
            title: card.getAttribute('data-title') || '',
            description: card.getAttribute('data-description') || '',
            category: card.getAttribute('data-category') || '',
            tags: card.getAttribute('data-tags') || '',
        };
    }

    function render(index) {
        current = (index + cards.length) % cards.length;
        var item = readCard(cards[current]);

        imageElement.src = item.image;
        imageElement.alt = item.title;
        titleElement.textContent = item.title;
        descriptionElement.textContent = item.description;

        if (item.category !== '') {
            categoryElement.textContent = item.category;
            categoryElement.classList.remove('d-none');
        } else {
            categoryElement.classList.add('d-none');
        }

        tagsElement.textContent = '';
        item.tags
            .split(',')
            .map(function (tag) { return tag.trim(); })
            .filter(Boolean)
            .forEach(function (tag) {
                var badge = document.createElement('span');
                badge.className = 'badge';
                badge.textContent = tag;
                tagsElement.appendChild(badge);
            });

        counterElement.textContent = (current + 1) + ' / ' + cards.length;
    }

    function open(index) {
        render(index);
        modal.show();
    }

    cards.forEach(function (card, index) {
        card.addEventListener('click', function (event) {
            event.preventDefault();
            open(index);
        });
    });

    prevButton.addEventListener('click', function () {
        render(current - 1);
    });

    nextButton.addEventListener('click', function () {
        render(current + 1);
    });

    document.addEventListener('keydown', function (event) {
        if (!modalElement.classList.contains('show')) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            render(current - 1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            render(current + 1);
        }
    });

    // Clear the image once the modal closes so the previous photo does not
    // linger while the next one loads.
    modalElement.addEventListener('hidden.bs.modal', function () {
        imageElement.removeAttribute('src');
    });
})();
