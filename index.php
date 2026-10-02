<?php
require_once __DIR__ . '/lib.php';

// Elke unieke bezoeker telt maar één keer mee, ook bij een paginaherlading.
// Herkenning gebeurt via een cookie (geen IP-adres of ander persoonsgegeven wordt opgeslagen).
$COOKIE_NAAM = 'ptf_bezocht';

if (!empty($_COOKIE[$COOKIE_NAAM])) {
    $stats = read_stats();
} else {
    $stats = record_visit();
    setcookie($COOKIE_NAAM, '1', [
        'expires' => time() + 60 * 60 * 24 * 365,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']),
    ]);
}
$aantal = $stats['count'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Je hebt op een phishinglink geklikt</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 bg-slate-50 text-slate-900 leading-relaxed">
<div class="max-w-2xl mx-auto px-5 pt-12 pb-16">
    <h1 class="text-3xl font-bold mb-4">Deze keer was het een oefening, maar de volgende keer misschien niet</h1>
    <p class="text-base text-slate-700">
        De e-mail die je zojuist ontving en de link waarop je hebt geklikt maakten deel uit van een
        <strong class="text-slate-900">interne bewustwordingsoefening rond phishing</strong>.
    </p>

    <div class="bg-white border border-slate-200 rounded-xl px-6 py-5 my-7 flex items-baseline gap-2.5 flex-wrap">
        <span class="text-4xl font-bold text-red-600"><?php echo htmlspecialchars((string) $aantal, ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="text-slate-500 text-sm">collega('s) hebben tot nu toe op deze link geklikt</span>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl px-6 pt-2 pb-5 my-7">
        <h2 class="text-lg font-semibold mb-1">Wat had je kunnen opvallen?</h2>
        <ul class="list-disc pl-5">
            <li class="my-2.5 text-slate-700"><strong class="text-slate-900">Afzenderadres:</strong> klopte het emailadres echt, of leek het er alleen op?</li>
            <li class="my-2.5 text-slate-700"><strong class="text-slate-900">Urgentie of dreiging:</strong> werd je onder druk gezet om snel te klikken?</li>
            <li class="my-2.5 text-slate-700"><strong class="text-slate-900">Algemene aanhef:</strong> stond er "Beste collega" in plaats van je eigen naam?</li>
            <li class="my-2.5 text-slate-700"><strong class="text-slate-900">De link zelf:</strong> verwijst deze écht naar een bekend, vertrouwd domein?</li>
            <li class="my-2.5 text-slate-700"><strong class="text-slate-900">Verzoek om gegevens:</strong> werd er gevraagd om in te loggen of gevoelige informatie te delen?</li>
            <li class="my-2.5 text-slate-700"><strong class="text-slate-900">Taal en opmaak:</strong> stonden er spel- of stijlfouten in de mail?</li>
        </ul>
    </div>

    <p class="text-base text-slate-700">
        Twijfel je ooit over een email? Klik niet door, en meld de email bij je IT-afdeling.
        Bedankt voor je deelname aan deze bewustwordingsoefening!
    </p>
</div>
</body>
</html>
