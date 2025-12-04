<?php 
/** @var \Kirby\Cms\Block $block */ 
$level = $block->level()->or('h2');
$settings = [];
foreach($block->paramentri()->toStructure() as $parametro) {
    $settings[] = "'" . $parametro->asse() . "' " . $parametro->valore();
}
$style = !empty($settings) ? 'style="font-variation-settings: ' . implode(', ', $settings) . ' !important;"' : '';
?>
<<?= $level ?> <?= $style ?>>
    <?= $block->text() ?>
</<?= $level ?>>
