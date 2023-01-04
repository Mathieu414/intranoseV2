<?php
restrict_access();

$id = $_SESSION['user_id'];

require_once "database/events.api.php";
$event = get_event_by_id(get_route_param('event_id'), $_SESSION['user_id']);
$competitions = get_competitions_by_event_id($event['did'], $_SESSION['user_id']);

$validation_result = save_registration($event['did'], $id, $_GET);

page("Inscription - " . $event['nom'], "event_view.css");
?>
<?= var_dump($validation_result) ?>
<form id="eventForm" method="get">
    <div class="page-actions">
        <a href="/evenements/<?= $event['did'] ?>" class="secondary"><i class="fas fa-caret-left"></i> Retour</a>
    </div>
    <article>
        <header class="center">
            <div class="row">
                <div class="col-sm-6">
                    <?php include "components/start_icon.php" ?>

                    <span><?="Départ - " . format_date($event['depart']) ?></span>
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
                <label for="entrySwitch">
                    <b>
                        <input type="checkbox" name="event_entry" id="entrySwitch" class="entry-switch" role="switch"
                            onchange="displayForm()" <?= $event['present'] ? "checked" : "" ?>>
                        <ins>Je participe <i class="fas fa-check"></i></ins>
                        <del>Je ne participe pas <i class="fas fa-xmark"></i></del>
                    </b>
                </label>
            </fieldset>
        </header>
        <div id="conditionalForm" <?= $event['present'] ? "" : "class='hidden'" ?>>

            <fieldset class="row">
                <label for="transportSwitch" class="col-sm-6">
                    <input type="checkbox" name="event_transport" id="transportSwitch" role="switch"
                        <?= $event['present'] && $event['transport'] ? "checked" : "" ?>>
                    Transport
                </label>
                <label for="accomodationSwitch" class="col-sm-6">
                    <input type="checkbox" name="event_accomodation" id="accomodationSwitch" role="switch"
                        <?= $event['present'] && $event['heberg'] ? "checked" : "" ?>>
                    Hébergement
                </label>
            </fieldset>

            <fieldset>
                <label for="entryComments">
                    Remarques:
                </label>
                <textarea name="event_comments"></textarea>
            </fieldset>

            <?php if (count($competitions)): ?>
                <h4>Courses : </h4>
                <table role="grid">
                    <?php foreach ($competitions as $competition): ?>
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
                            <td colspan="4">
                                <fieldset class="row">
                                    <label for="competitionSwitch">
                                        <input type="checkbox" name="event_entry" id="competitionSwitch" class="entry-switch"
                                            role="switch" onchange="displayForm()" <?= $competition['present'] ? "checked" : "" ?>>
                                        <ins>Je cours <i class="fas fa-check"></i></ins>
                                        <del>Je ne cours pas <i class="fas fa-xmark"></i></del>
                                    </label>
                                    <label>Remarques</label>
                                    <textarea></textarea>
                                </fieldset>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php endif ?>
        </div>
        <p id="conditionalText">Inscris-toi pour une vraie partie de plaisir !</p>
    </article>
    <button type="submit" name="submit">Enregister</button>
</form>

<script>
    const entrySwitch = document.getElementById("entrySwitch");
    const conditionalForm = document.getElementById("conditionalForm");

    function displayForm() {
        if (entrySwitch.checked) {
            conditionalForm.classList.remove("hidden");
        } else {
            conditionalForm.classList.add("hidden");
        }
    }
</script>