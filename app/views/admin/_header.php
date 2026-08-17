<?php $activeNav = $activeNav ?? ''; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $title ?? 'Game Master Console' ?></title>
<link href="<?=ROOT?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?=ROOT?>/assets/css/admin.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet">
</head>
<body class="gm-body">
<div class="gm-shell">
    <aside class="gm-sidebar">
        <div class="gm-brand">
            <img src="<?=ROOT?>/assets/images/REALLIFEQUEST.png" alt="Emblem">
            <span>Game Master</span>
        </div>
        <nav class="gm-nav">
            <a href="<?=ROOT?>/admin" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="<?=ROOT?>/admin/viewPendingRequests" class="<?= $activeNav === 'pending' ? 'active' : '' ?>">Pending Quests</a>
            <a href="<?=ROOT?>/admin/users" class="<?= $activeNav === 'users' ? 'active' : '' ?>">Adventurers</a>
            <a href="<?=ROOT?>/admin/paymentMethods" class="<?= $activeNav === 'payments' ? 'active' : '' ?>">Payment Methods</a>
            <a href="<?=ROOT?>/admin/cashouts" class="<?= $activeNav === 'cashouts' ? 'active' : '' ?>">Cashouts</a>
            <a href="<?=ROOT?>/logout" class="gm-logout">Logout</a>
        </nav>
    </aside>
    <main class="gm-main">
