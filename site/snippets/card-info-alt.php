<?php $parent = $item->parent() ?>
<?php $deadline_exist = "off"; ?>
<?php $deadline_toggle = "off"; ?>
<?php $deadline = $item->deadline() OR NULL ?>
<?php $facilitato = false ?>
<div class="cards-details orange" style="<?php if($page->parent() !== NULL): ?>padding: 30px; max-width: 1280px; min-width: fit-content; margin: 0 auto!important;<?php else: ?>padding: 15px;<?php endif; ?> margin: 0;" class="cards-info" <?php  if($direction == "row"): ?>style="margin-left: 15px"<?php endif; ?>>

<?php if($tag_toggle == true AND $item->child_category_selector()->isNotEmpty()): ?>
<div class="cards-categories">
    <span class="tag parent"><?= $item->parent()->title() ?></span>

    <?php foreach($item->child_category_selector()->split() as $category): ?>
        <span class="tag"><?= $category ?></span>
        <?php if(strtolower($category) == "workshop"): ?>
            <?php $facilitato = true ?>
        <?php endif; ?>
    <?php endforeach; ?>    
</div>
<?php endif; ?>

<div>
    <div class="cards-dates" style="display: flex; width: 100%; flex-direction: row; justify-content: space-between; flex-wrap:nowrap;">
        <?php if($item->appuntamenti()->isNotEmpty()): ?>
            <?php $appuntamenti = $item->appuntamenti()->toStructure() ?>
            <?php foreach($appuntamenti as $appuntamento): ?>
            <?php 
            $formatter = new IntlDateFormatter('it_IT', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
            $formatter->setPattern('d MMMM Y'); // Modello simile a %d – %b – %Y;
            ?>
            <span style="width: fit-content; min-width: fit-content;" class="">
                <strong><?= $formatter->format($appuntamento->giorno()->toDate()) ?></strong>
            </span>  
            <span style="width: fit-content; min-width: fit-content;" class="">
                <?= $appuntamento->orario_inizio()->toDate('H:i') ?><?php if($appuntamento->orario_fine()->isNotEmpty()): ?> → <?= $appuntamento->orario_fine()->toDate('H:i') ?><?php endif; ?>
            </span>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if($item->dove()->isNotEmpty()): ?>
            <span style="width: fit-content; min-width: fit-content;" class="">
                ⏷ <?= $item->dove() ?>
            </span>
        <?php endif; ?>
    </div>
</div>

<?php if($item->appuntamenti()->isNotEmpty() OR $item->dove()->isNotEmpty()): ?>
<hr style="border: none; border-top: 1px solid; opacity: 1;">
<?php endif; ?>

<div class="cards-title" style="margin:0!important;">
<?php if($parent == "attivita"): ?>
    <h2 style="margin-bottom: 0;"><?= $item->title() ?></h2>
<?php else: ?>
    <h2><?= $item->title() ?></h2>
<?php endif; ?>
</div>

<?php if($parent == "attivita"): ?>
<?php else: ?>
<div class="cards-text">
    <?php echo $item->descrizione()->kirbytext(); ?>
</div>
<?php endif; ?>

<?php if ($item->team()->isNotEmpty()): ?>
    <?php if($facilitato == false): ?>
    <div class="team-label"><p style="margin: 0; margin-top: 15px; margin-bottom: 0;">Con la partecipazione di:
    <?php else: ?>
    <div class="team-label"><p style="margin: 0; margin-top: 15px; margin-bottom:0;">Attività facilitata da:
    <?php endif; ?>
        <?php $members = 0 ?>
        <?php foreach($item->team()->toStructure() as $team_member): ?>
            <?php $members++; ?>
        <?php endforeach; ?>
        <?php $printed_members = 0 ?>
        <?php foreach($item->team()->toStructure() as $team_member): ?>
            <?php $printed_members++; ?>
            <span><strong><?= $team_member->persona() ?></strong> (<?= $team_member->ruolo() ?>)<?php if($printed_members < $members): ?>,<?php endif; ?> </span>
        <?php endforeach; ?>
    </p></div>
    <div class="cards-team" style="display: flex; width: 100%; justify-content: center; flex-wrap:wrap; text-align: center;">
    
    </div>

<?php endif; ?>

    <?php if ($item->deadline()->isNotEmpty()): ?>
        <?php $deadline_exist = "on" ?>
    <?php endif; ?>
    <?php if ($item->deadline()->isNotEmpty() && strtotime($item->deadline()) >= strtotime('today')): ?>
        <hr style="border: none; border-top: 1px solid; opacity: 1;">
        <?php $deadline_toggle = "on" ?>
        <?php $deadline = $item->deadline() ?>
        <div class="cards-dates" style="display: flex; width: 100%; justify-content: center; flex-wrap:wrap; ">
            <?php 
            $formatter = new IntlDateFormatter('it_IT', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
            $formatter->setPattern('d MMMM Y'); // Modello simile a %d – %b – %Y;
            ?>
            <span id="deadline" class="center" style="width: fit-content; display: flex; justify-content: space-between;" class="time">
                <strong style="min-width: fit-content;">DEADLINE</strong> 
                → 
                <strong style="min-width: fit-content;"><?= $formatter->format($deadline->toDate()) ?></strong>
            </span>
        </div>
    <?php else: ?>
    <?php endif; ?>


    <?php 
    $formatter = new IntlDateFormatter('it_IT', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
    $formatter->setPattern('d MMM Y'); // Modello simile a %d – %b – %Y;
    ?>

        <?php if($page->parent() !== NULL AND $page->parent()->collection_options() == "calendar"): ?>
            <?php if(strtotime($page->deadline()) >= strtotime('today')): ?>
            <?php snippet('form-request-counter',[
            'page' => $page,
            ])?>
            <?php endif; ?>
        <?php else: ?>
            <?php if(strtotime($item->deadline()) >= strtotime('today')): ?>
            <?php snippet('form-request-counter',[
            'page' => $item,
            ])?>
            <?php endif; ?>
        <?php endif; ?>




        <?php

// Data di oggi
$today = date('Y-m-d', strtotime('today'));

// Deadline in formato corretto (Y-m-d)
$deadline = $item->deadline()->isNotEmpty() ? $item->deadline()->toDate('Y-m-d') : null;

// deadline è definita e successiva o uguale a oggi?
$deadline_bool = $deadline && ($deadline >= $today);

// Data tra tre giorni
$next_three_days = date('Y-m-d', strtotime('+3 days'));

// deadline è entro i prossimi 3 giorni e non nel passato?
$incoming_deadline_bool = $deadline && ($deadline >= $today && $deadline <= $next_three_days);

$current = $item ?? $page;
$formData = $formData($current);
$hasAvailableSeats = !isset($formData['available']) || $formData['available'] > 0;

// Controlla appuntamenti imminenti solo se non c'è deadline
$incoming_appointment_bool = false;
if (!$deadline && $current->appuntamenti()->isNotEmpty()) {
    foreach ($current->appuntamenti()->toStructure() as $appuntamento) {
        $giorno_appuntamento = $appuntamento->giorno()->toDate('Y-m-d');
        if ($giorno_appuntamento >= $today && $giorno_appuntamento <= $next_three_days) {
            $incoming_appointment_bool = true;
            break;
        }
    }
}

?>

</div>