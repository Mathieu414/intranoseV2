<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/utils/core.php';
require_once __DIR__ . '/utils/router.php';

get('/', 'index.php');
any('/login', 'pages/login.php');

// Events
get('/evenements', 'pages/events/event_list.php');
any('/evenements/nouveau', 'pages/events/event_edit.php');
any('/evenements/$event_id/modifier', 'pages/events/event_edit.php');
any('/evenements/$event_id/ajouter-course', 'pages/events/competition_edit.php');
any('/evenements/$event_id/course/$competition_id', 'pages/events/competition_edit.php');
get('/evenements/$event_id', 'pages/events/event_view.php');
any('/evenements/$event_id/inscription', 'pages/events/event_register.php');
any('/evenements/$event_id/publier', 'pages/events/event_publish.php');
any('/evenements/$event_id/supprimer', 'pages/events/event_delete.php');
get('/download', 'uploads/download_file.php');

// Settings
any('/mon-profil', 'pages/settings/settings.php');
any('/mon-profil/changement-mdp', 'pages/settings/settings_password_change.php');
any('/mon-profil/changement-login', 'pages/settings/settings_login_change.php');

// Users
get('/licencies', 'pages/users/user_list.php');
get('/licencies/$user_id', 'pages/users/user_view.php');

// Logout
get('/logout', function () {
    session_destroy();
    redirect("login");
});

// Special route, see router.php
any('/404', 'pages/404.php');