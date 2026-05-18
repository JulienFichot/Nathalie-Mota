<?php
/**
 * functions.php — Point d'entrée du thème Nathalie Mota
 * ═══════════════════════════════════════════════════════
 * Ce fichier est chargé automatiquement par WordPress à chaque page.
 * Son rôle : importer les quatre fichiers de fonctions du thème.
 *
 *   includes/setup.php   — OC Étape 1 : configuration du thème + chargement CSS/JS via wp_enqueue
 *   includes/helpers.php — OC Étape 2/3 : fonctions utilitaires pour lire les champs ACF
 *   includes/menu.php    — OC Étape 1 : ajoute la classe CSS sur le lien "CONTACT" du menu
 *   includes/ajax.php    — OC Étape 4 : handlers AJAX pour la galerie filtrée
 */

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/setup.php';
require_once __DIR__ . '/includes/menu.php';
require_once __DIR__ . '/includes/ajax.php';
