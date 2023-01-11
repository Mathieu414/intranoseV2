<?php
restrict_access();

require_once "database/users.api.php";
$user = get_user(get_route_param('user_id'));
$profile_picture = (file_exists("images/profile/" . $user['id'] . ".jpg")) ? "/images/profile/" . $user['id'] . ".jpg" : "/images/profile/none.jpg";

page($user['prenom'] . " " . $user['nom'], "user_view.css");
?>
<div id="page-actions">
    <a href="/licencies" class="secondary"><i class="fas fa-caret-left"></i> Retour</a>
</div>
<article class="grid center">
    <figure>
        <img src="<?= $profile_picture ?>">
    </figure>
    <table class="infos-table">
        <tr>
            <td>Catégorie</td>
            <td>
                <?= $user['category_name'] ?>
            </td>
        </tr>
        <tr>
            <td>Email</td>
            <td> <?= $user['email'] ?></td>
        </tr>
        <tr>
            <td>Adresse</td>
            <td>
                <?= join(
                    "<br/>",
                    [
                        $user['adresse1'] . " " . $user['adresse2'],
                        $user['cp'] . " " . $user['ville']
                    ]
                ) ?>
            </td>
        </tr>
        <tr>
            <td>Fixe</td>
            <td> <?= $user['tel'] ?></td>
        </tr>
        <tr>
            <td>Portable </td>
            <td>
                <?= $user['telport'] ?>
            </td>
        </tr>
        <tr>
            <td>Licence</td>
            <td><?= $user['num_lic'] ?></td>
        </tr>
        <tr>
            <td>Sport Ident</td>
            <td>
                <?= $user['sportident'] ?>
            </td>
        </tr>
    </table>
</article>