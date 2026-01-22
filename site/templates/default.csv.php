<?php

use Kirby\Toolkit\Str;

function formatDataItaliano($data) {
    $fmt = new IntlDateFormatter('it_IT', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    $fmt->setPattern("d MMM Y");
    return strtoupper($fmt->format(strtotime($data)));
}

function serializePage($page, $site) {
    $data = [
        'url'   => $page->url(),
        'title' => $page->title()->value(),
    ];

    $content = $page->content()->toArray();
    $blueprint = $page->blueprint();

    foreach ($content as $key => $value) {
        if (in_array($key, ['title', 'uuid'])) continue;

        $field = $page->content()->get($key);
        if ($field->isEmpty()) continue;

        $fieldBlueprint = $blueprint->field($key);
        $type = $fieldBlueprint['type'] ?? 'text';

        switch ($type) {
            case 'pages':
                $data[$key] = implode(' | ', $field->toPages()->map(fn($p) => $p->title()->value())->values());
                break;
            case 'files':
                $data[$key] = implode(' | ', $field->toFiles()->map(fn($f) => $f->url())->values());
                break;
            case 'structure':
                $data[$key] = implode(' || ', $field->toStructure()->map(function($entry) {
                    $entryLabels = [];
                    foreach ($entry->content()->toArray() as $k => $v) {
                        if ($entry->content()->get($k)->isNotEmpty()) {
                            $entryLabels[] = $k . ': ' . $entry->content()->get($k)->value();
                        }
                    }
                    return '(' . implode(', ', $entryLabels) . ')';
                })->values());
                break;
            case 'date':
                $data[$key] = $field->toDate('d/m/Y');
                break;
            case 'toggle':
                $data[$key] = $field->toBool() ? 'Si' : 'No';
                break;
            case 'tags':
            case 'multiselect':
                $data[$key] = implode(', ', $field->split());
                break;
            default:
                $data[$key] = $field->value();
                break;
        }
    }

    // Special case: risposte_form
    if (class_exists('NonDeterministic\Helpers\CollectionHelper')) {
        $formData = \NonDeterministic\Helpers\CollectionHelper::getFormData($page, $site);
        if ($formData['count'] > 0 || (isset($formData['max']) && $formData['max'] !== null)) {
            $data['risposte_form'] = $formData['count'];
            $data['num_max'] = $formData['max'] ?? '';
        }
    }

    return $data;
}

$rows = [];
$children = $page->children()->listed();

if ($children->isNotEmpty()) {
    foreach ($children as $child) {
        $rows[] = serializePage($child, $site);
    }
} else {
    $rows[] = serializePage($page, $site);
}

// Collect all unique keys for headers
$headers = [];
foreach ($rows as $row) {
    foreach (array_keys($row) as $key) {
        if (!in_array($key, $headers)) {
            $headers[] = $key;
        }
    }
}

header('Content-Type: text/csv; charset=utf-8');
// header('Content-Disposition: attachment; filename="pagina-' . $page->slug() . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, $headers);

foreach ($rows as $row) {
    $csvRow = [];
    foreach ($headers as $header) {
        $csvRow[] = $row[$header] ?? '';
    }
    fputcsv($output, $csvRow);
}

fclose($output);
exit;