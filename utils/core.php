<?php
/** Returns an env variable */
function env(string $key)
{
    return $GLOBALS[$key] ?? null;
}

global $database, $formatter;
$database = new PDO("mysql:dbname=" . env("db_name") . ";host=" . env("db_host") . ";charset=utf8mb4", env("db_user"), env("db_password"));
$formatter = new IntlDateFormatter("fr_FR", IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, "Europe/Paris");

// Time zone and locale
date_default_timezone_set("Europe/Paris");
setlocale(LC_ALL, "French");
//ini_set('display_errors', 1);

/** Get global database */
function db(): PDO
{
    return $GLOBALS['database'];
}

/** Get global formatter.
 * See {@see format_date()}
 * @param string|null $format When provided, sets custom global formatting, scoped to the entire script (the current page)
 * @return IntlDateFormatter The global formatter object
 */
function formatter(string $format = null): IntlDateFormatter
{
    global $formatter;
    if ($format) {
        $formatter->setPattern($format);
    }
    return $formatter;
}
/** Perform a SQL query
 * @param mixed $sql_query the SQL query.
 * @param array $args A list of arguments
 * @return PDOStatement|bool
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
/** Calls fetch() and redirects to 404 if the result was not single
 * @return array a single DB entity.
 */
function fetch_single($sql, ...$args)
{
    $result = fetch($sql, ...$args);
    if (count($result) != 1) {
        force_404("Expected 1 DB entity, got " . count($result));
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
/** Setup page, initializes a bunch of globals */
function page(string $page_title, string $page_css = null, bool $with_nav = true, string $page_display_title = null, string $page_description = null)
{
    global $title, $description, $css, $nav, $display_title;
    $title = $page_title;
    $description = $page_description;
    $nav = $with_nav;
    $display_title = $page_display_title;
    $css = "/assets/css/" . $page_css;
}
/** Checks authentication and authorization stored in session */
function check_auth(...$levels)
{
    if (!isset($_SESSION['user_permission'])) {
        redirect("login");
    }
    $ulevel = $_SESSION['user_permission'];
    foreach ($levels as $level) {
        if ($level == $ulevel) {
            return true;
        }
    }
    return false;
}

/** Restrict access to authenticated users, and to a set of authorized users if provided arguments.
 * Should be used as early as possible, to prevent unnecessary data loading.
 */
function restrict_access(...$permissions)
{
    if (!isset($_SESSION['user_permission']) || (count($permissions) && !in_array($_SESSION['user_permission'], $permissions))) {
        $permission = $_SESSION['user_permission'] ?? "non authenticated user";
        force_404("Access for {$permission} is restricted for this page. ");
    }
}

/**
 * Format a date.
 * @param string|int|DateTime $date The date either as a string, a timestamp or a DateTime.
 * @return string The date formatted
 */
function format_date($date, string $format = null)
{
    if (gettype($date) === "string") {
        $date = date_create($date);
    }
    return formatter($format)->format($date);
}