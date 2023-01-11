<?php
restrict_access("ROOT", "STAFF", "COACH", "COACHSTAFF");

require_once "database/competitions.api.php";
require_once "utils/form_validation.php";

$event_id = get_route_param("event_id");
$competition_id = get_route_param("competition_id", false);
$post = $_POST;
if ($competition_id) {
    $competition = get_competition($competition_id);
    if (empty($_POST)) {
        $post = [
            "name" => htmlspecialchars_decode($competition["nom"], ENT_QUOTES),
            "date" => $competition["date"],
            "location" => htmlspecialchars_decode($competition["lieu"])
        ];
    }
}

$v = validate($post);
$name = $v->text("name")->label("Nom de la course")->placeholder()->required();
$date = $v->date("date")->label("Date")->required();
$location = $v->text("location")->label("Lieu")->required();

if (!empty($_POST) && $v->valid()) {
    create_or_edit_competition($name->value, $date->value, $location->value, $event_id, $competition_id);
}

page($competition_id ? "{$competition["nom"]} : Modifier" : "Ajouter une course");
?>
<form method="post">
    <div id="page-actions">
        <a href="/evenements/<?= $event_id ?>" class="secondary">
            <i class="fas fa-xmark"></i> Annuler
        </a>
    </div>
    <article class="row">
        <?= $v->render_errors() ?>
        <?= $name->render() ?>
        <div class="col-md-6">
            <?= $date->render() ?>
        </div>
        <div class="col-md-6">
            <?= $location->render() ?>
        </div>
        <div>
            <button type="submit">
                <?= $competition_id ? "Modifier" : "Créer" ?>
            </button>
        </div>
    </article>
</form>