# UAB Aula Interactiva #

This Final Degree Project is focused on the integration of the tool  ̈Interactive Classroom
(developed by Youssef Assbaghi, Arman Pipoyan and Lluis Galante) with Caronte, which is the 
platform used on courses like Programming Methodology. The idea is to achieve a better 
interaction bewteen the teacher and student without the need of third party apps.
Also, a qualification system is needed to be linked with the integration.


##Installation guide:##

1.Download the plugin branch zip from this repository and also the backend branch zip.
2.Meet the requirements of the repository on which the backend is based: https://github.com/lluisgalante/Web_TFG (skip the installation guide from their repository).
3.In your Moodle, open Site Administration > Plugins > Install Plugins, and upload the plugin zip.
4.Once you have installed the plugin, decompress the backend zip in /var/www/html/ (it has to be in this path; otherwise, it won't work).
5.Go to /var/www/html/html/Model/connection.php and set the name and password for your Moodle database.
6.Inside the app folder (the same directory as Model), create a folder called "problemes" which will contain problems.
7.Repeat the last step but with a folder called "solucions" which will contain all the students' codes.

## Code to create a view with the grades ##
    -- Parametres de la sessió
    SET SESSION group_concat_max_len = 1000000;
    
    -- Variable on guardarem l'informació dels grades
    SET @sql = NULL;
    
    -- Construim la taula pivot
    SELECT GROUP_CONCAT(DISTINCT
        'MAX(CASE WHEN problem_id = ', problem_id, ' THEN grade END) AS Problem_', problem_id
        ) INTO @sql
    FROM mdl_uab_entregable_student_grade;
    
    -- Generem la vista
    SET @sql = CONCAT('
        CREATE OR REPLACE VIEW mdl_uab_grades AS
        SELECT student_id, ', @sql, '
        FROM mdl_uab_entregable_student_grade
        GROUP BY student_id
    ');
    
    -- Execució
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

## License ##

2023 Erik Becerra
2023 Ruben Simo

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.


