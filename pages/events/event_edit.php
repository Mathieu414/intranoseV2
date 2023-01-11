<?php
restrict_access("ROOT", "STAFF", "COACH", "COACHSTAFF");

require_once "database/events.api.php";
require_once "utils/form_validation.php";

$event_id = get_route_param("event_id", false);
if ($event_id) {
    $event = get_event_by_id($event_id, $_SESSION['user_id']);
    $event_mapping = [
        "event_name" => htmlspecialchars_decode($event["nom"], ENT_QUOTES),
        "start_date" => $event["depart"],
        "end_date" => $event["arrivee"],
        "limit_date" => $event["limite"]
    ];
}

$v = validate($event_mapping ?? []);
$event_name = $v->text("event_name")->label("Nom de l'événement")->placeholder()->required();
$start_date = $v->date("start_date")->label("Date de départ")->required();
$end_date = $v->date("end_date")
    ->label("Date de retour")->required()
    ->after($start_date->value, "Doit être après le départ");
$limit_date = $v->date("limit_date")
    ->label("Deadline")->required()
    ->before(date_create($start_date->value)->sub(new DateInterval("PT23H59M59S"))->format("Y-m-d"), "Doit être avant le jour de départ");

if (!empty($_POST) && $v->valid()) {
    create_or_edit_event($event_name->value, $start_date->value, $end_date->value, $limit_date->value, $event_id);
}

page($event_id ? "{$event["nom"]} : Modifier" : "Créer un événement");
?>
<form method="post">
    <div id="page-actions">
        <a href="/evenements<?= $event_id ? "/$event_id" : "" ?>" class="secondary">
            <i class="fas fa-xmark"></i> Annuler
        </a>
        <?php if ($event_id):
            if (!$event['open']): ?>
                <a href="/evenements/<?= $event_id ?>/supprimer" class="destructive">
                    <i class="fas fa-trash"></i> Supprimer
                </a>
            <?php elseif ($event['open']): ?>
                <a href="/evenements/<?= $event_id ?>/publier" class="destructive">
                    <i class="fas fa-calendar-minus"></i> Retirer
                </a>
            <?php endif; endif; ?>
    </div>
    <article class="row">
        <?= $v->render_errors() ?>
        <?= $event_name->render() ?>
        <div class="col-sm-6 col-lg-4">
            <?= $start_date->render() ?>
        </div>
        <div class="col-sm-6 col-lg-4">
            <?= $end_date->render() ?>
        </div>
        <div class="col-lg-4"><?= $limit_date->render() ?></div>
        <div>
            <button type="submit">
                <?= $event_id ? "Modifier" : "Créer" ?>
            </button>
        </div>
    </article>
</form>