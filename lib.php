<?php
require_once __DIR__ . '/config.php';

define('STATS_FILE', __DIR__ . '/data/stats.json');

/**
 * Registreert een bezoek: verhoogt de teller en voegt een tijdstempel toe.
 * Gebruikt flock() zodat gelijktijdige bezoeken elkaar niet overschrijven.
 *
 * @return array De bijgewerkte statistieken (['count' => int, 'visits' => string[]])
 */
function record_visit(): array
{
    $handle = fopen(STATS_FILE, 'c+');
    if ($handle === false) {
        return ['count' => 0, 'visits' => []];
    }

    flock($handle, LOCK_EX);

    $contents = stream_get_contents($handle);
    $stats = json_decode($contents, true);
    if (!is_array($stats) || !isset($stats['count']) || !isset($stats['visits'])) {
        $stats = ['count' => 0, 'visits' => []];
    }

    $stats['count']++;
    $stats['visits'][] = date('c');

    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($stats, JSON_PRETTY_PRINT));
    fflush($handle);

    flock($handle, LOCK_UN);
    fclose($handle);

    return $stats;
}

/**
 * Leest de huidige statistieken zonder een nieuw bezoek te registreren.
 *
 * @return array
 */
function read_stats(): array
{
    if (!file_exists(STATS_FILE)) {
        return ['count' => 0, 'visits' => []];
    }

    $contents = file_get_contents(STATS_FILE);
    $stats = json_decode($contents, true);
    if (!is_array($stats) || !isset($stats['count']) || !isset($stats['visits'])) {
        return ['count' => 0, 'visits' => []];
    }

    return $stats;
}
