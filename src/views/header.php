<?php
/**
 * Page header and navigation.
 *
 * @var string $pageTitle
 * @var string $pageDescription
 */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($pageDescription ?? GALLERIO_DESCRIPTION) ?>">
    <title><?= e(($pageTitle ?? GALLERIO_NAME) === GALLERIO_NAME ? GALLERIO_NAME : $pageTitle . ' · ' . GALLERIO_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap/css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="d-flex flex-column min-vh-100">
    <a class="skip-link" href="#gallery">Skip to gallery</a>

    <header>
        <nav class="navbar navbar-expand-md gallerio-navbar sticky-top" aria-label="Main navigation">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="./">
                    <img src="<?= e(asset('img/favicon.svg')) ?>" alt="" width="28" height="28">
                    <span class="fw-bold"><?= e(GALLERIO_NAME) ?></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto align-items-md-center">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#gallery">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="upload.php">Upload</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1">
