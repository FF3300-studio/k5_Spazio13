<?php 
/** @var \Kirby\Cms\Block $block */ 
$level = $block->level()->or('h2');
$alignment = $block->alignment()->or('center');
$h_style = 'style="margin:0!important; width: 100%; text-align: ' . $alignment . ';"';
$span_style = "style= margin:0!important; font-size: 60px; font-weight: 700;";
?>
<<?= $level ?> <?= $h_style ?>>
    <span <?= $span_style ?>><?= $block->text() ?></span>
</<?= $level ?>>
