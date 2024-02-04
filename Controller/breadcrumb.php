<?php
require_once __DIR__ . "/../Model/connection.php";
require_once __DIR__ . "/../Model/problemsGet.php";

######Code to get get current page URL###########
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";

$url.= $_SERVER['HTTP_HOST']; // Append the host(domain name, ip) to the URL.


$url.= $_SERVER['REQUEST_URI'];// Append the requested resource location to the URL
#####################################################

$query_bc = $_GET["query"] ?? VIEW_SUBJECT_LIST;

if(isset($_SESSION['hist']) && ($query_bc != "Llista assignatures")){ //Cases when user is and wants to acces a page that is different to Home page

    if(in_array($query_bc, array("Llista Problemes", "Grups amb sessions","Crear nova sessió"))){
        $subjects = getSubjects();

        foreach ($subjects as $subject) {
            if( $_GET['subject'] == $subject['id'] ){
                $query_bc = $subject['title'] . " - ". $query_bc; //Ej: Metodologia Programacio - Llista problemas
            }
        }
    }
    elseif ($query_bc == "Llista de sessions"){
        $query_bc = "Grup " . $_GET['group'] . " - ". $query_bc; //Ej: Grup 41 - Llista de sessions
    }

    $index = array_search($query_bc, array_keys($_SESSION['hist']));

    if( $index != false){ // User wants to access a page that has already visited, therefore he is going backwards.
        //Before: Home > MP - Grups amb sessions > Grup 414 - Llista de sessions > MP - Grups amb sessions
        array_splice($_SESSION['hist'], $index + 1);
        //After:Home > MP - Grups amb sessions
    }
    else {//The page that the user wants to visit has not been visited yet, therefore we add that page to the breadcrumb .

        $keys = array_keys($_SESSION['hist']);
        if ($query_bc != end($keys)) {
            $_SESSION['hist'][$query_bc]=$url;
        }
    }
}
else { //Case when user is at Home page, or wants to go to Home page.
    $_SESSION['hist'] = ['Home' => $url];
}
