<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_uabaulainteractiva.
 *
 * @package     mod_uabaulainteractiva
 * @copyright   2023 Erik Becerra <1529079@uab.cat>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');



// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$u = optional_param('u', 0, PARAM_INT);



if ($id) {
    $cm = get_coursemodule_from_id('uabaulainteractiva', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('uabaulainteractiva', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('uabaulainteractiva', array('id' => $u), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('uabaulainteractiva', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);


/*
//PRINCIPIO ERROR
$event = \mod_uabaulainteractiva\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));


$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('uabaulainteractiva', $moduleinstance);
$event->trigger();

//FINAL ERROR
*/


$PAGE->set_url('/mod/uabaulainteractiva/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);


echo $OUTPUT->header();

//Obtenim el ID de la tasca
    $id = $_GET['id'];

//Obtenim el id i email de l'usuari
    global $DB, $USER;
    $email = $USER->email;
    $userID = $USER->id;

//Obtenim el id del curs:
    $cursID = $COURSE->id;

//Obtenim el rol de l'usuari
    // Context actual del curso
    $context = context_course::instance($COURSE->id);

// Assignació de rols per usuari
$role_assignment = get_user_roles($context, $USER->id, true);
$rol = reset($role_assignment)->roleid;

//Obtenim el ID del problema, la ruta de la solució per actualitzar les seves dades i el nom.
$problem_id = $DB->get_field('course_modules', 'instance', array('id' => $id), MUST_EXIST);
$problem_dir = $DB->get_field('uab_interactive_problem', 'route_solution', array('id' => $problem_id), MUST_EXIST);
$problem_name = $DB->get_field('uab_interactive_problem', 'title', array('id' => $problem_id), MUST_EXIST);

//Revisem si la sessió existeix, en cas contrari, la creará:
$sql = "SELECT * FROM {uab_interactive_session} WHERE problem_id = :id";
$params = ['id' => $problem_id];
$record = $DB->get_record_sql($sql, $params);

if (!$record) {
    // La sesión no existe y se crea
    //echo "El registro con problem_id = $problem_id no existe.";

    $sql = "INSERT INTO {uab_interactive_session} (problem_id, name, status, professor_id, subject_id) VALUES (:problem_id, :name, :status, :professor_id, :subject_id)";
    $params = [
        'problem_id' => $problem_id,
        'name' => $problem_name,
        'status' => "activated",
        'professor_id' => $userID,
        'subject_id' => $cursID,
        ];
    
    $DB->execute($sql, $params);
}

//Obtenim session ID
$sql = "SELECT id FROM {uab_interactive_session} WHERE problem_id = :id";
$params = ['id' => $problem_id];
$sesionID = $DB->get_record_sql($sql, $params)->id;



$url_editor = '../../../Controller/editor.php?id=' . urlencode($id) .
    '&email=' . urlencode($email) .
    '&userMoodleID=' . urlencode($userID) .
    //'&cursID=' . urlencode($cursID) .
    '&problem=' . urlencode($problem_id) .
    '&session=' . urlencode($sesionID) .
    '&rol=' . urlencode($rol);


//PART DE GRUPS
    //Primer necessitem obtenir el grouping "GAI" per saber els grups de clase
    $sql = "SELECT * FROM {groupings} WHERE courseid = :courseid AND name = :name";
    $params = [
        'name' => "GAI",
        'courseid' => $cursID,
    ];
    //ID del grouping que fa de contenidor dels grups de treball, s'ha de anomenar "GAI"
    $groupingID = $DB->get_record_sql($sql, $params)->id;

    //Ara obtenim els ids dels grups dins del grouping seleccionat
    $sql = "SELECT * FROM {groupings_groups} WHERE groupingid = :groupingid";
    $params = [
        'groupingid' => $groupingID,
    ];
    $workGroups = $DB->get_records_sql($sql, $params);

    //Finalment obtenim els IDs de cada grup corresponent (dins de $workGroupsIds)
    $workGroupsIds = array();
    foreach ($workGroups as $workGroup) {
        $workGroupsIds[] = $workGroup->groupid;
    }

    //Per cada grup obtenim el seu nom (dins de workGroupNames)
    $workGroupsNames = array();
    foreach ($workGroupsIds as $workGroupId) {
        $sql = "SELECT * FROM {groups} WHERE id = :id";
        $params = [
            'id' => $workGroupId,
        ];
        $group = $DB->get_record_sql($sql, $params);
        $workGroupsNames[] = $group->name;
    }




?>



<form id="courseForm">
    <!--<label for="course">Selecciona un grup:</label>-->
    <label for="course"><?php echo get_string('selGrup', 'mod_uabaulainteractiva'); ?></label>
    <select name="course" id="course">

        <?php
        foreach ($workGroupsNames as $name){
            echo "<option value=\"$name\">$name</option>";
        }
        ?>


    </select>

   <!-- <input type="button" value="Mostrar grup" onclick="cargarIframe()">-->
    <input type="button" value="<?php echo get_string('mosGrup', 'mod_uabaulainteractiva'); ?>" onclick="cargarIframe()">
</form>

<iframe name="iframe_resultado" id="iframe_resultado" width="100%" height="800" style="margin: 0 auto; display: block;"></iframe>

<script>
    function cargarIframe() {
        // Obtain selected course
        var selectedCourse = document.getElementById("course").value;

        // Modificar la URL del iframe
        document.getElementById("iframe_resultado").src = "<?php echo $url_editor; ?>&curs=" + selectedCourse;
    }
</script>


<?php

// Imprime el iframe con la URL construida
//echo '<div id="iframeContainer"  style="text-align: center;">
//  <iframe id="myIframe" src="' . $url_editor . '"width="100%" height="800" style="margin: 0 auto; display: block;"></iframe></div>';

echo $OUTPUT->footer();


?>
