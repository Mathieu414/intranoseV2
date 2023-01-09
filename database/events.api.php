<?php

/** Get events */
function get_events($user_id)
{
    return fetch(
        "SELECT deplacements.*, depl.* FROM deplacements 
        LEFT JOIN inscriptions_depl as depl
            ON depl.id_depl = deplacements.did 
            AND depl.id_runner = ?
        WHERE open=1
        ORDER BY depart DESC;"
        ,
        $user_id
    );
}

/**  */
function get_draft_events()
{
    if (check_auth("ROOT", "STAFF", "COACH", "COACHSTAFF")) {
        return fetch(
            "SELECT * FROM deplacements 
            WHERE open=0
            ORDER BY depart DESC;"
        );
    } else {
        return [];
    }
}

function get_event_by_id($event_id, $user_id = null)
{
    if ($user_id) {
        return fetch_single(
            "SELECT deplacements.*, depl.* FROM deplacements 
            LEFT JOIN inscriptions_depl as depl
                ON depl.id_depl = deplacements.did 
                AND depl.id_runner = ?
            WHERE did = ?
            ORDER BY depart DESC LIMIT 1;",
            $user_id,
            $event_id
        );
    } else {
        return fetch_single("SELECT * FROM deplacements
        WHERE did = ?
        ORDER BY depart DESC LIMIT 1;",
            $event_id
        );
    }
}

function get_competitions_by_event_id($event_id, $user_id = null)
{
    return fetch(
        "SELECT courses.*, inscriptions_courses.present as present FROM courses 
        LEFT JOIN inscriptions_courses 
            ON inscriptions_courses.id_course = courses.cid 
            AND inscriptions_courses.id_runner = ?
        WHERE courses.id_depl = ?
        ORDER BY date ASC;",
        $user_id,
        $event_id
    );
}


function create_or_edit_event(string $event_name, string $start_date, string $end_date, string $limit_date, int $event_id = null)
{
    $result = false;
    if ($event_id) {
        $result = query_db("UPDATE deplacements 
            SET nom=?,depart=?, arrivee=?, limite=? 
            WHERE did=? 
            LIMIT 1;",
            $event_name,
            $start_date,
            $end_date,
            $limit_date,
            $event_id
        );
    } else {
        $result = query_db("INSERT INTO deplacements(nom,depart,arrivee,limite)
            VALUES(?,?,?,?);",
            $event_name,
            $start_date,
            $end_date,
            $limit_date
        );
    }
    if ($result)
        redirect("/evenements/$event_id");
}

function delete_event($event_id)
{
    $courses = get_competitions_by_event_id($event_id);
    foreach ($courses as $c) {
        query_db("DELETE FROM inscriptions_courses WHERE id_course=?;", $c["cid"]);
    }
    query_db("DELETE FROM courses WHERE id_depl=?;", $event_id);
    query_db("DELETE FROM inscriptions_depl WHERE id_depl=?;", $event_id);
    return query_db("DELETE FROM deplacements WHERE did=? LIMIT 1", $event_id);
}
function publish_event($event_id, $state)
{
    return query_db("UPDATE deplacements SET open=? WHERE did=? LIMIT 1", $state, $event_id);
}

function get_file($id)
{
    return fetch_single(
        "SELECT * from circulaires WHERE id = ?",
        $id
    );
}