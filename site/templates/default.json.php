<?php

use Kirby\Toolkit\Str;

function formatDataItaliano($data) {
    $fmt = new IntlDateFormatter('it_IT', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    $fmt->setPattern("d MMM Y"); // Es. "3 mag 2025"
    return strtoupper($fmt->format(strtotime($data))); // "3 MAG 2025"
}

function serializePage($page, $site) {
    $data = [
        'id'    => $page->id(),
        'uuid'  => $page->uuid()->value(),
        'url'   => $page->url(),
        'title' => $page->title()->value(),
        'slug'  => $page->slug(),
    ];

    // Excluded fields
    $excluded = [
        'parent_collection_options',
        'parent_categories_toggle',
        'collection_toggle',
        'collection_categories_manager_toggle',
        'collection_pagination',
        'map_style',
        'map_key',
        'collection_pagination_prev',
        'collection_pagination_number',
        'collection_pagination_next'
    ];

    // Get all fields from the page content
    $content = $page->content()->toArray();
    $blueprint = $page->blueprint();

    foreach ($content as $key => $value) {
        // Skip base fields and excluded fields
        if (in_array($key, ['title', 'uuid']) || in_array($key, $excluded)) continue;

        $field = $page->content()->get($key);
        if ($field->isEmpty()) continue;

        $fieldBlueprint = $blueprint->field($key);
        $type = $fieldBlueprint['type'] ?? 'text';

        switch ($type) {
            case 'pages':
                $pagesData = [];
                foreach ($field->toPages() as $p) {
                    $pagesData[] = [
                        'id'    => $p->id(), 
                        'title' => $p->title()->value(), 
                        'url'   => $p->url()
                    ];
                }
                $data[$key] = $pagesData;
                break;
                
            case 'files':
                $filesData = [];
                foreach ($field->toFiles() as $f) {
                    $filesData[] = [
                        'id'       => $f->id(), 
                        'filename' => $f->filename(), 
                        'url'      => $f->url()
                    ];
                }
                $data[$key] = $filesData;
                break;
                
            case 'structure':
                $structureData = [];
                foreach ($field->toStructure() as $entry) {
                    $entryData = [];
                    foreach ($entry->content()->toArray() as $k => $v) {
                        if ($entry->content()->get($k)->isNotEmpty()) {
                            $entryData[$k] = $entry->content()->get($k)->value();
                        }
                    }
                    $structureData[] = $entryData;
                }
                $data[$key] = $structureData;
                break;
                
            case 'date':
                $data[$key] = $field->toDate('Y-m-d H:i:s');
                break;
            case 'toggle':
                $data[$key] = $field->toBool();
                break;
            case 'tags':
            case 'multiselect':
                $data[$key] = $field->split();
                break;
            case 'link':
            case 'url':
                $data[$key] = $field->value();
                break;
            case 'layout':
                // Layout could be serialized if needed, but keeping it simple for now
                break;
            default:
                $data[$key] = $field->value();
                break;
        }
    }

    // Special case: risposte_form (maintained for utility)
    if (class_exists('NonDeterministic\Helpers\CollectionHelper')) {
        $formData = \NonDeterministic\Helpers\CollectionHelper::getFormData($page, $site);
        if ($formData['count'] > 0 || (isset($formData['max']) && $formData['max'] !== null)) {
            $data['form_stats'] = [
                'responses' => $formData['count'],
                'max'       => $formData['max'] ?? null
            ];
        }
    }

    return $data;
}

$children = $page->children()->listed();
$output   = [];

if ($children->isNotEmpty()) {
    $childrenData = [];
    foreach ($children as $child) {
        $childrenData[] = serializePage($child, $site);
    }
    
    $output = [
        'page'     => serializePage($page, $site),
        'children' => $childrenData
    ];
} else {
    $output = serializePage($page, $site);
}

header('Content-Type: application/json');
echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);