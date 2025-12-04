<?php 
/** @var \Kirby\Cms\Block $block */ 
$level = $block->level()->or('h2');
$alignment = $block->alignment()->or('center');
$h_style = 'style="margin:0!important; width: 100%; text-align: ' . $alignment . ';"';
$span_style = "style=\" margin:0!important; font-size: 60px; font-family: 'black'; font-variation-settings: 'wght' 900, 'opsz' 100, 'wdth' 120, 'GRAD' -0.5,    'slnt' 0, 'XTRA' 468.6, 'XOPQ' 96.56, 'YOPQ' 78.9, 'YTLC' 514.56,    'YTUC' 711.28, 'YTAS' 749.45, 'YTDE' -203.57, 'YTFI' 737.84!important;\"";
?>
<<?= $level ?> <?= $h_style ?>>
    <span <?= $span_style ?>><?= $block->text() ?></span>
</<?= $level ?>>
