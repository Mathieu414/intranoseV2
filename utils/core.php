<?php
$db_host = "localhost";
$db_name = "nose42_intra";
$db_user = "root";
$db_password = "";

global $database, $formatter;
$database = new PDO("mysql:dbname=" . $db_name . ";host=" . $db_host . ";charset=UTF8", $db_user, $db_password);
$formatter = new IntlDateFormatter("fr_FR", IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, "Europe/Paris");

/** Get global database */
function db(): PDO
{
    return $GLOBALS['database'];
}
/** Get global formatter */
function formatter(): IntlDateFormatter
{
    return $GLOBALS['formatter'];
}
/** perform a SQL query
 * @return PDOStatement|false
 */
function query_db($sql_query, ...$args)
{
    if ($args) {
        $request = db()->prepare($sql_query);
        $request->execute($args);
        return $request;
    } else {
        return db()->query($sql_query);
    }
}
/** Calls query_db() and unwraps the result.
 * @return array a list of DB entities
 */
function fetch($sql, ...$args)
{
    return query_db($sql, ...$args)->fetchAll();
}
/** Calls fetch() and throws if there isn't a single element.
 * @return entity a single DB entity.
 */
function fetch_single($sql, ...$args)
{
    $result = fetch($sql, ...$args);
    if (count($result) != 1) {
        inject_in_template();
    }
    return $result[0];
}

// HELPER METHODS
/** Redirect helper method */
function redirect($href)
{
    header("Location: " . $href);
    exit;
}
/** Require from root folder */
function require_root($path)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/" . $path;
}
/** Setup page
 * @param string|false|null $page_display_title
 */
function page($page_title, $page_css = null, $with_nav = true, $page_display_title = null, $page_description = null)
{
    global $title, $description, $css, $nav, $display_title;
    $title = $page_title;
    $description = $page_description;
    $nav = $with_nav;
    $display_title = $page_display_title;
    $css = "/assets/css/" . $page_css;
}
/** Checks authentication and authorization stored in session */
function check_auth($level)
{
    if (!isset($_SESSION['user_permission'])) {
        redirect("login");
    }
    $ulevel = $_SESSION['user_permission'];
    if ($level != $ulevel) {
        return false;
    }
    return true;
}

/** Restrict access to authenticated users, and to a set of authorized users if provided arguments.
 * Should be used as early as possible, to prevent unnecessary data loading.
 */
function restrict_access(...$permissions)
{
    if (!isset($_SESSION['user_permission']) || (count($permissions) && !in_array($_SESSION['user_permission'], $permissions))) {
        inject_in_template();
    }
}

/**
 * Format a date.
 * @param string|int|DateTime $date The date either as a string (2022-12-25), a timestamp, or a DateTime
 * @return formatted_date A string in the format: 25 Déc 2022
 */
function format_date($date)
{
    //return formatter()->format(date_create($date));
    $FORMAT = "d M Y";
    if (gettype($date) === "string") {
        return date_create($date)->format($FORMAT);
    } else if (gettype($date) === "integer") {
        return date($FORMAT, $date);
    } else {
        return $date->format($FORMAT);
    }
}
