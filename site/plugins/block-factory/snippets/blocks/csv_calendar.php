<?php
/** @var Kirby\Cms\Block $block */

use Kirby\Toolkit\Str;
use Kirby\Cms\Pages;

$title      = $block->title()->value();
$csvSource  = $block->csv_source()->toPage();
$max        = (int)($block->max_number()->value() ?? 0);
$activeSlug = Str::slug($block->active_category()->value() ?? '');

if (!$csvSource || $csvSource->template()->name() !== 'spreadsheet') {
    return;
}

/** @var SpreadsheetPage $csvSource */

// CRITICAL: Ensure roles and aliases are built BEFORE calling searchPool,
// otherwise occurrences won't have the specific date substituted in their content.
$csvSource->filterableFields(); 

// Fetch all items (occurrences)
$items = $csvSource->searchPool();

$todayStart = strtotime('today');
$processedItems = [];

foreach ($items as $child) {
    $assoc = $child->content()->toArray();
    
    // With roles built, the model correctly puts the specific date in the date field.
    $occurrenceDate = $csvSource->fieldByRole($assoc, 'date');
    if (!$occurrenceDate) continue;

    $ts = $csvSource->parseToTimestamp($occurrenceDate);
    
    // Filter: Today and Future
    if ($ts > 0 && $ts < $todayStart) continue;

    // Filter: Active Category
    if ($activeSlug !== '') {
        $tags = array_merge(
            Str::split($csvSource->fieldByRole($assoc, 'tag1'), ','),
            Str::split($csvSource->fieldByRole($assoc, 'tag2'), ',')
        );
        $match = false;
        foreach ($tags as $t) {
            if (Str::slug(trim($t)) === $activeSlug) { $match = true; break; }
        }
        if (!$match) continue;
    }

    // Determine the grouping header
    $formattedHeader = $csvSource->formatDate($occurrenceDate);
    if (!$formattedHeader || trim($formattedHeader) === '') {
        $formattedHeader = trim((string)$occurrenceDate) ?: 'Altro';
    }
    
    $processedItems[] = [
        'page' => $child,
        'ts'   => $ts,
        'date' => trim($formattedHeader)
    ];
}

// Sort chronologically (ASC)
usort($processedItems, function($a, $b) {
    if ($a['ts'] === $b['ts']) return 0;
    if ($a['ts'] === 0) return 1;
    if ($b['ts'] === 0) return -1;
    return ($a['ts'] < $b['ts']) ? -1 : 1;
});

// Apply limit
if ($max > 0) {
    $processedItems = array_slice($processedItems, 0, $max);
}

if (empty($processedItems)) return;

// Group items by date for the calendar view
$groupedItems = [];
foreach ($processedItems as $p) {
    $groupedItems[$p['date']][] = $p['page'];
}

$filterColors = $csvSource->filterColors();
?>

<section class="csv-calendar-block">
  <?php if (!empty($title)): ?>
    <header class="csv-calendar-block__header">
        <h3 class="csv-calendar-block__title"><?= esc($title) ?></h3>
    </header>
  <?php endif; ?>

  <div class="cm-carousel" role="region" aria-label="<?= esc($title ?: 'Calendario') ?>">
    <div class="cm-carousel__track" tabindex="0">
      <?php foreach ($groupedItems as $dateHeader => $group): ?>
        <div class="date-group cm-card--slide" style="text-align: center; min-width: 400px; scroll-snap-align: start;">
          <h3 class="date-header" style="font-weight: 700; font-size: 2.22rem; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 20px; margin: 15px;">
            <?= esc($dateHeader) ?>
          </h3>
          <div class="block-grid-a-list" style="justify-content: center; display: flex; flex-direction: column; gap: 15px; padding: 15px;">
            <?php foreach ($group as $child): ?>
              <div class="single-cards" style="width: 100%; min-width: 100%!important;">
                <?php snippet('spreadsheet-item', [
                  'child'     => $child,
                  'csvSource' => $csvSource,
                  'showTags'  => false,
                  'showExtra' => false,
                  'showNodo'  => false,
                  'class'     => 'no_hover',
                  'padding'   => '0 15px'
                ]) ?>
              </div>
            <?php endforeach ?>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
