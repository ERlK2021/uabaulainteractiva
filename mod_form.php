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
 * The main mod_uabaulainteractiva configuration form.
 *
 * @package     mod_uabaulainteractiva
 * @copyright   2023 Erik Becerra <1529079@uab.cat>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_uabaulainteractiva
 * @copyright   2023 Erik Becerra <1529079@uab.cat>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_uabaulainteractiva_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        //CUSTOM FORM
        //Títol del problema
        $mform->addElement('text', 'titol', get_string('titol', 'mod_uabaulainteractiva'));
        $mform->setType('titol', PARAM_TEXT);
        $mform->addRule('titol', null, 'required', null, 'client');

        //Descripció
    $mform->addElement('textarea', 'descripcio', get_string('descripcion', 'mod_uabaulainteractiva'));
        $mform->setType('descripcio', PARAM_TEXT);
        $mform->addRule('descripcio', null, 'required', null, 'client');

        //Temps d'execució (s)
        $mform->addElement('text', 'temps', get_string('tempsExec', 'mod_uabaulainteractiva'));
        $mform->setType('temps', PARAM_INT);
        $mform->setDefault('temps', 0);
        $mform->addRule('temps', null, 'required', null, 'client');
        $mform->addRule('temps', get_string('error_temps_execucio'), 'numeric', null,'client');


        //Memòria a utilitzar (MB)
        $opciones_memoria = array(
            16 => '16 MB',
            32 => '32 MB',
            64 => '64 MB',
            128 => '128 MB',
            256 => '256 MB',
            512 => '512 MB',
            1024 => '1024 MB',
            2048 => '2048 MB',
            4096 => '4096 MB',
        );

        // Camp de seleccio de memoria
        $mform->addElement('select', 'memoria', get_string('memoria', 'mod_uabaulainteractiva') , $opciones_memoria);
        $mform->setType('memoria', PARAM_INT);
        $mform->setDefault('memoria', 128);
        $mform->addRule('memoria', null, null, null, 'client');
        $mform->addRule('memoria', get_string('error_memoria_utilitzar', 'assign'), 'numeric', null, 'client');


        //Desplegable Públic o Privat
        $visibilidad_options = array(
            'public' => get_string('public', 'mod_uabaulainteractiva'),
            'privat' => get_string('privat', 'mod_uabaulainteractiva')
        );
        $mform->addElement('select', 'visibilitat', get_string('visibilitat', 'mod_uabaulainteractiva'), $visibilidad_options);
        $mform->setType('visibilitat', PARAM_TEXT);
        $mform->addRule('visibilitat', null, null, null, 'client');

        //Desplegable Python, C++ o Notebook
        $lenguaje_options = array(
            'C++' => 'C++',
            'Python' => 'Python',
            //'Notebook' =>'Notebook'
        );
        $mform->addElement('select', 'llenguatge', get_string("llenguatge", 'mod_uabaulainteractiva'), $lenguaje_options);
        $mform->setType('llenguatge', PARAM_TEXT);
        $mform->addRule('llenguatge', null, null, null, 'client');

        //Entregable
        $mform->addElement('advcheckbox', 'entregable', get_string('entregable', 'mod_uabaulainteractiva'));

        $mform->addElement('filepicker', 'uab_file', get_string('problema', 'mod_uabaulainteractiva'), null, array('maxbytes' => 11111111111111, 'accepted_types' => 'zip'));
        $mform->addRule('uab_file', null, 'required', null, 'client');

        $mform->addElement('filepicker', 'uab_solution_file', get_string('solucio', 'mod_uabaulainteractiva'), null, array('maxbytes' => 11111111111111, 'accepted_types' => 'zip'));
        $mform->addRule('uab_solution_file', null, 'required', null, 'client');

        //END CUSTOM FORM


        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('uabaulainteractivaname', 'mod_uabaulainteractiva'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'uabaulainteractivaname', 'mod_uabaulainteractiva');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_uabaulainteractiva settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('static', 'label1', 'uabaulainteractivasettings', get_string('uabaulainteractivasettings', 'mod_uabaulainteractiva'));
        $mform->addElement('header', 'uabaulainteractivafieldset', get_string('uabaulainteractivafieldset', 'mod_uabaulainteractiva'));

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }


    public function data_preprocessing(&$default_values) {
        
        if ($this->is_submitted()) {
            $data = $this->get_data();

            //CAMPOS QUE INSERTAREMOS EN LA BD
            $default_values['route'] = "backend/app/problemes/".$data->course."/".$data->titol;
            $default_values['route_solution'] = "backend/app/problemes/".$data->course."/".$data->titol."/teacherSolution";
            $default_values['solution_visibility'] = 1;
            $default_values['solution_lines'] = 1;
            $default_values['solution_quality'] = 1;
            $default_values['title'] = $data->titol;
            $default_values['description'] = $data->descripcio;
            $default_values['visibility'] = $data->visibilitat;
            $default_values['memory'] = $data->memoria;
            $default_values['time'] = $data->temps;
            $default_values['language'] = $data->llenguatge;
            $default_values['subject_id']=$data->course;

            if ($data->entregable==1){
                $default_values['entregable'] = "on";
            }
            else{
                $default_values['entregable'] = "off";
            }

            $default_values['edited'] = 0;


            //Creamos la estructura de carpetas  del problema y la solución
            $nomProblema = $default_values['title'];

            $rutaProblema = '/var/www/html/app/problemes/'.$data->course.'/' . $nomProblema . '/';
            mkdir($rutaProblema, 0777, true);

            $rutaSolucioProblema = '/var/www/html/app/problemes/'.$data->course.'/' . $nomProblema .  '/teacherSolution/';
            mkdir($rutaSolucioProblema, 0777, true);

            //Código que procesa los datos del problema:
            $file = $this->get_new_filename('uab_file');

            $fullpath = $rutaProblema.$file;
            $success = $this->save_file('uab_file', $fullpath, true);
            if (!$success) {
                echo "Oops! something went wrong!";
            }
            $default_values['route'] = $fullpath;

            $zip = new ZipArchive;
            if ($zip->open($fullpath) === true) {
                // Extraemos en la ruta
                $zip->extractTo($rutaProblema);
                // Cerramos el Zip del problema
                $zip->close();
                //Eliminamos el Zip residual
                unlink($fullpath);
            }


            //Código que procesa los datos de la solución:
            $file_solution = $this->get_new_filename('uab_solution_file');
            $fullpath = $rutaSolucioProblema.$file_solution;
            $success = $this->save_file('uab_solution_file', $fullpath, true);
            if (!$success) {
                echo "Oops! something went wrong!";
            }
            $default_values['route_solution'] = $fullpath;

            $zip = new ZipArchive;
            if ($zip->open($fullpath) === true) {
                // Extraemos en la ruta
                $zip->extractTo($rutaProblema."/teacherSolution");
                // Cerramos el Zip del problema
                $zip->close();
                //Eliminamos el Zip residual
                unlink($fullpath);
            }



            global $DB;
            $record = new stdClass();
            $record->route= "/var/www/html/app/problemes/".$data->course.'/' . $nomProblema;
            $record->route_solution="/var/www/html/app/problemes/".$data->course.'/' . $nomProblema .  '/teacherSolution';
            $record->solution_visibility= $default_values['solution_visibility'];
            $record->title= $default_values['title'];
            $record->description= $default_values['description'];
            $record->visibility= $default_values['visibility'];
            $record->memory= $default_values['memory'];
            $record->time= $default_values['time'];
            $record->language= $default_values['language'];
            $record->subject_id= $default_values['subject_id'];
            $record->entregable= $default_values['entregable'];
            $record->deadline= $default_values['deadline'];
            $record->edited= $default_values['edited'];


            //Solution INFO
            $lineNumber = 0;
            $ifStatements = 0;
            $forStatements = 0;
            $whileStatements = 0;
            $switchStatements = 0;

            $problem_dir =$record->route_solution;

            $files = scandir($problem_dir);

            foreach ($files as $file){
                if ($file != "." and $file != ".."){
                    foreach (file($problem_dir."/".$file) as $line ){
                        if(strlen($line)>2){
                            $lineNumber++;
                            $ifStatements += substr_count($line, 'if ');
                            $ifStatements += substr_count($line, 'if(');
                            $forStatements += substr_count($line, 'for ');
                            $forStatements += substr_count($line, 'for(');
                            $whileStatements += substr_count($line, 'while ');
                            $whileStatements += substr_count($line, 'while(');
                            $switchStatements += substr_count($line, 'switch ');
                            $switchStatements += substr_count($line, 'switch(');
                        }
                    }
                }
            }

            $record->number_lines_file= $lineNumber;
            $record->solution_quality= $ifStatements."-".$forStatements."-".$whileStatements."-".$switchStatements;


            // Insertar los datos en la tabla 'mdl_uabaulainteractiva_problemes'
            $DB->insert_record('uab_interactive_problem', $record);  

            return parent::data_preprocessing($default_values);
        }
        else {
            return false;
        }



    }

/*
    public function data_save() {
        global $DB;
        
        // Guardar los datos del formulario en una tabla diferente
        $record = new stdClass();


        $record->titol = $data->titol;
        $record->descripcio = $data->descripcio;
        $record->temps = $data->temps;
        $record->memoria = $data->memoria;
        $record->visibilitat = $data->visibilitat;
        $record->llenguatge = $data->llenguatge;


        

        var_dump($record); 
  
        // Insertar los datos en la tabla 'uabaulainteractiva_problemes'
        $DB->insert_record('uabaulainteractiva_problemes', $record);

        return parent::data_save($data);
    }

    */

}
