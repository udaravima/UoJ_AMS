<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?php echo SERVER_ROOT; ?>/js/color-modes.js"></script>
    <script src="<?php echo SERVER_ROOT; ?>/js/index.global.min.js"></script>
    <link rel="stylesheet" href="<?php echo SERVER_ROOT; ?>/css/bootstrap-icons.css">
    <link href="<?php echo SERVER_ROOT; ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo SERVER_ROOT; ?>/css/bootstrap-select.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?php echo SERVER_ROOT; ?>/favicon.ico">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous"> -->
    <style>
        .global-box-override {
            box-sizing: border-box;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }
    </style>
    <!-- <title>Document</title> -->