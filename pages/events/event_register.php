<?php
restrict_access();

$id = $_SESSION['user_id'];

require_once "database/events.api.php";
require_once "utils/form_validation.php";
$event = get_event_by_id(get_route_param('event_id'), $_SESSION['user_id']);
$competitions = get_competitions_by_event_id($event['did'], $_SESSION['user_id']);

// TODO: Extract to mapper
$event_mapping = [
    "event_entry" => $event['present'],
    "event_transport" => $event['transport'],
    "event_accomodation" => $event['heberg'],
    "event_comment" => $event['comment'],
];
foreach ($competitions as $competition) {
    $event_mapping["competition[{$competition['cid']}][entry]"] = $competition['present'];
    $event_mapping["competition[{$competition['cid']}][ranked_up]"] = $competition['surclasse'];
    $event_mapping["competition[{$competition['cid']}][comment]"] = $competition['rmq'];
}

$v = validate($event_mapping);
$event_entry = $v->switch("event_entry")->set_labels("Je participe", "Pas inscrit");
$transport = $v->switch("event_transport")->label("Transport");
$accomodation = $v->switch("event_accomodation")->label("Hébergement");
$event_comment = $v->text("event_comment")->area()->label("Remarques");
$competition_rows = [];
foreach ($competitions as $competition) {
    $competition_rows[$competition['cid']] = $competition;
    $competition_rows[$competition['cid']]["entry"] = $v->switch("competition[{$competition['cid']}][entry]")->set_labels("Je cours", "Je ne cours pas");
    $competition_rows[$competition['cid']]["ranked_up"] = $v->switch("competition[{$competition['cid']}][ranked_up]")->label("Surclassé");
    $competition_rows[$competition['cid']]["comment"] = $v->text("competition[{$competition['cid']}][comment]")->area()->label("Remarques");
}

if ($v->valid()) {
    save_registration($event['did'], $id, $v);
}

page("Inscription - " . $event['nom'], "event_view.css");
?>
<form id="mainForm" method="post">
    <div id="page-actions">
        <a href="/evenements/<?= $event['did'] ?>" class="secondary"><i class="fas fa-caret-left"></i> Retour</a>
        <a href="#" onclick="document.getElementById('mainForm').submit()">Enregistrer</a>
    </div>
    <article>
        <header class="center">
            <?= $v->render_errors() ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php include "components/start_icon.php" ?>

                    <span>
                        <?="Départ - " . format_date($event['depart']) ?>
                    </span>
                </div>
                <div class="col-sm-6">
                    <?php include "components/finish_icon.php" ?>
                    <span><?="Retour - " . format_date($event['arrivee']) ?></span>
                </div>
                <div>
                    <i class="fas fa-clock"></i>
                    <span>
                        <?="Date limite - " . format_date($event['limite']) ?>
                    </span>
                </div>
            </div>

            <fieldset>
                <b><?= $event_entry->render("onchange=\"toggleDisplay(this,'eventForm')\"") ?></b>
            </fieldset>
        </header>

        <div id="eventForm" <?= $event_entry->value ?: "class='hidden'" ?>>

            <fieldset class="row">
                <div class="col-sm-6">
                    <?= $transport->render() ?>
                </div>
                <div class="col-sm-6">
                    <?= $accomodation->render() ?>
                </div>
            </fieldset>
            <fieldset>
                <?= $event_comment->render() ?>
            </fieldset>

            <?php if (count($competition_rows)): ?>
                <h4>Courses : </h4>
                <table role="grid">
                    <?php foreach ($competition_rows as $competition_id => $competition): ?>
                        <tr class="display">
                            <td class="competition-name"><b>
                                    <?= $competition['nom'] ?>
                                </b></td>
                            <td class="competition-date"><?= format_date($competition['date']) ?></td>
                            <td class="competition-place">
                                <?= $competition['lieu'] ?>
                            </td>
                        </tr>
                        <tr class="edit">
                            <td colspan="3">
                                <fieldset class="row">
                                    <?= $competition["entry"]->render("onchange=\"toggleDisplay(this,'competitionForm$competition_id')\"") ?>
                                    <div id="competitionForm<?= $competition_id ?>" <?= $competition['present'] ?: " class=hidden" ?>>
                                        <?= $competition["ranked_up"]->render() ?>
                                        <?= $competition["comment"]->render() ?>
                                    </div>
                                </fieldset>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endif ?>
        </div>
        <p id="conditionalText">Inscris-toi pour une vraie partie de plaisir !</p>
    </article>
</form>

<script>

    function toggleDisplay(toggle, target) {
        const targetElement = document.getElementById(target);
        if (toggle.checked) {
            targetElement.classList.remove("hidden");
        } else {
            targetElement.classList.add("hidden");
        }
    }
</script>