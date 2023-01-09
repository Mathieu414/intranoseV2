<?php
restrict_access();
$can_edit = check_auth("COACH", "STAFF", "ROOT", "COACHSTAFF");

require_once "database/events.api.php";
$event = get_event_by_id(get_route_param('event_id'), $_SESSION['user_id']);
$competitions = get_competitions_by_event_id($event['did'], $_SESSION['user_id']);

$has_entry = isset($event['present']) && $event['present'] == 1;
$is_transported = isset($event['transport']) && $event['transport'] == 1;
$is_hosted = isset($event['heberg']) && $event['heberg'] == 1;
$has_file = $event['circu'] != 0;

page($event['nom'], "event_view.css");
?>
<div class="page-actions">
    <a href="/evenements" class="secondary"><i class="fas fa-caret-left"></i> Retour</a>

    <?php if ($event['open'] && $event['limite'] > date("Y-m-d")): ?>
        <a href="/evenements/<?= $event['did'] ?>/inscription">
            <i class="fas fa-pen-to-square"></i> Inscription
        </a>
    <?php elseif (!$event['open']): ?>
        <a href="/evenements/<?= $event['did'] ?>/publier">
            <i class="fas fa-paper-plane"></i> Publier
        </a>
    <?php endif ?>

    <?php if ($can_edit): ?>
        <a href="/evenements/<?= $event['did'] ?>/modifier" class="secondary">
            <i class="fas fa-pen"></i> Modifier
        </a>
    <?php endif ?>
</div>
<article>
    <header class="center">
        <div class="row">
            <div>
                <?php include "components/start_icon.php" ?>
                <span>
                    <?="Départ : " . format_date($event['depart']) ?>
                </span>
            </div>
            <div>
                <?php include "components/finish_icon.php" ?>
                <span><?="Retour : " . format_date($event['arrivee']) ?></span>
            </div>
            <div>
                <i class="fas fa-clock"></i>
                <span>
                    <?="Date limite : " . format_date($event['limite']) ?>
                </span>
            </div>
        </div>

        <?php if ($has_file): ?>
            <div class="file-button">
                <a href="/download?id=<?= $event["circu"] ?>" role="button">
                    <div>
                        <i class="fas fa-paperclip"></i>
                    </div>
                    <div>
                        <b>Informations</b>
                    </div>
                </a>
            </div>
        <?php endif ?>

        <div>
            <b>
                <?php if (isset($event['present']) && $event['present'] == 1): ?>
                    <ins><i class="fas fa-check"></i>
                        <span>Je participe</span></ins>
                <?php else: ?>
                    <del><i class="fas fa-xmark"></i>
                        <span><?= isset($event['present']) ? "Je ne participe pas" : "Pas inscrit" ?></span></del>
                <?php endif; ?>
            </b>
        </div>
    </header>
    <div class="grid">
        <?php if ($has_entry): ?>
            <p>
                <?php if ($is_transported): ?>
                    <ins><i class="fas fa-check"></i></ins>
                <?php else: ?>
                    <del><i class="fas fa-xmark"></i></del>
                <?php endif; ?>
                <span>Transport avec le club</span>
            </p>
            <p>
                <?php if ($is_hosted): ?>
                    <ins><i class="fas fa-check"></i></ins>
                <?php else: ?>
                    <del><i class="fas fa-xmark"></i></del>
                <?php endif; ?>
                <span>Hébergement avec le club</span>
            </p>
        <?php endif; ?>
    </div>


    <?php if (count($competitions)): ?>
        <h4>Courses : </h4>
        <table role="grid">

            <?php foreach ($competitions as $competition): ?>
                <tr class="display <?= $can_edit ? "clickable" : "" ?>" <?= $can_edit ? "onclick=\"window.location.href = '/evenements/{$event['did']}/course/{$competition['cid']}'\"" : "" ?>>
                    <td class="competition-entry">
                        <?php if (isset($competition['present']) && $competition['present'] == 1): ?>
                            <ins><i class="fas fa-check"></i></ins>
                        <?php else: ?>
                            <del><i class="fas fa-xmark"></i></del>
                        <?php endif; ?>
                    </td>
                    <td class="competition-name"><b><?= $competition['nom'] ?></b></td>
                    <td class="competition-date">
                        <?= format_date($competition['date']) ?>
                    </td>
                    <td class="competition-place"><?= $competition['lieu'] ?></td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>

    <?php if ($can_edit): ?>
        <p>
            <a role=button class="secondary" href="/evenements/<?= $event['did'] ?>/ajouter-course">
                <i class="fas fa-plus"></i> Ajouter une course</a>
        </p>
    <?php endif; ?>
</article>