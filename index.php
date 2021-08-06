<?php
session_start(); //debut de SESSION
include("config.php"); //Appel de la bdd

// ... INIT VARIABLES ...
$sinistre_type = "";
$sinistre_description = "";
$a = 1; // Nombre d'éléments à extraire aléatoirement
$res4 = "";


var_dump ($_SESSION);

if (!empty($_SESSION)) {
    echo "<br/> <a href='\logout.php'>❌Deconnexion</a>";
    echo "<br/> <a href='\pre_sinistre.php'>💬Estimer son sinistre ?</a>";
    echo "<br/> <a href='\account.php'>🔒Compte</a>";
    // Random sinistre
    if ($_SESSION['sess_roles'] == 'test') {
        $extract_sinistre = "SELECT type, description FROM sinistres ORDER BY rand() LIMIT $a";
        $query4 = $db->prepare($extract_sinistre);
        $query4->execute();
        while ($row = $query4->fetch(PDO::FETCH_NUM)) {
            echo " <br/>" . $row[0] . " <br/>" . $row[1];
        }
    }


} else {
    echo "<a href='\login.php'>✔️Connexion</a>";
}

if (!empty($_POST)) {
    if (isset($_POST['add_sinistre'])) {
        if ($_POST['add_sinistre'] == "AJOUTER") {
            if (isset($_POST['type']) && isset($_POST['description'])) {
                $sinistre_type = $_POST['type'];
                $sinistre_description = (empty($_POST['description'])) ? "Pas de description" : $_POST["description"];
                $ajout_sinstrie_sql = "INSERT INTO sinistres (type, description) VALUES (:sinistre_type, :sinistre_description)";
                $query = $db->prepare($ajout_sinstrie_sql);
                $query->execute(['sinistre_type' => $sinistre_type, 'sinistre_description' => $sinistre_description]);
            }
        }
    }
    if (isset($_POST['delete_sinistre'])) {
        if ($_POST['delete_sinistre'] == "SUPPRIMER") {
            if (isset($_POST['select_for_del'])) { //on check si le select est vide si oui on procede
                $sinistre_id_to_delete = $_POST['select_for_del']; // on prend l'id qui a été selectionner
                $delete_sinsistre_sql = "DELETE FROM sinistres WHERE id=:sinistre_id_to_delete"; // on le macro ici
                $query = $db->prepare($delete_sinsistre_sql);
                $query->execute(['sinistre_id_to_delete' => $sinistre_id_to_delete]);
            }
        }
    }
    if (isset($_POST['select_for_upd'])) {
        if ($_POST['update_btn'] == "MODIFIER") {
            $sinistre_id_to_update = $_POST['select_for_upd'];
            $input_update_sinistre = $_POST['input_update_sinistre'];
            $update_sinsistre_sql = "UPDATE sinistres SET type = :input_update_sinistre WHERE sinistres.id=:sinistre_id_to_update";
            $query = $db->prepare($update_sinsistre_sql);
            $query->execute(['input_update_sinistre' => $input_update_sinistre, 'sinistre_id_to_update' => $sinistre_id_to_update]);
        }
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

    <?php
    // Liste > Supprimer un sisnistre
    $sql2 = "SELECT id, type FROM sinistres;";
    $query2 = $db->prepare($sql2);
    $query2->execute();
    // Liste >  Modifier un sinistre : 
    $sql3 = "SELECT id, type FROM sinistres;";
    $query3 = $db->prepare($sql3);
    $query3->execute();
    ?>


    <?php if (isset($_SESSION['sess_roles']) && $_SESSION['sess_roles'] == "admin") { ?>
        <div class="add_sinistre">
            <h2>Ajouter un sisnistre : </h2>
            <form method="post">
                Type : <input type="text" name="type" />
                Description : <textarea type="text" name="description"></textarea>
                <input type="submit" name="add_sinistre" value="AJOUTER">
            </form>
        </div>

        <div class="delete_sinistre">
            <form method="post">
                <h2>Supprimer un sisnistre : </h2>
                <select name="select_for_del">
                    <?php
                    echo "<option disabled selected>..Choix Possible..</option>\n";
                    while ($res = $query2->fetch(PDO::FETCH_NUM)) {
                        echo "<option value=" . $res[0] . ">" . $res[1] . "</option>\n";
                    }
                    ?>
                </select>
                <input type="submit" name="delete_sinistre" value="SUPPRIMER">
            </form>
        </div>

        <div class="update_sinistre">
            <form method="post">
                <h2> Modifier un sinistre : </h2>

                <select name="select_for_upd">
                    <?php
                    echo "<option disabled selected>..Choix Possible..</option>\n";
                    while ($res2 = $query3->fetch(PDO::FETCH_NUM)) {
                        echo "<option value=" . $res2[0] . ">" . $res2[1] . "</option>\n";
                    }
                    ?>
                </select>

                <input type="text" name="input_update_sinistre" />
                <input type="submit" name="update_btn" value="MODIFIER">
            </form>

        </div>
    <?php
    }
    ?>
</body>

</html>