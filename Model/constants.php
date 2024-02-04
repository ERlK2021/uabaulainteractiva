<?php

const PROFESSOR = 0;
const STUDENT = 1;
$_SESSION['histori']=null;

# Views
# This is the default screen
const VIEW_SUBJECT_LIST = "Llista assignatures"; # = 0;
const VIEW_PROBLEMS_LIST="Llista Problemes"; # = 1;
const VIEW_LOGIN_FORM="Pàgina LogIn";# = 2;
const VIEW_REGISTER_FORM="Pàgina Registre";# = 3;
const VIEW_PROBLEM_CREATE="Crear nou problema";# = 4;
const VIEW_EDITOR="Editor Problemas";# = 7;
const VIEW_SUBJECT_CREATE="Crear assignatura";# = 10;
const VIEW_SOMETHING="Inici";# = 11 ;
const VIEW_PROBLEM_EDIT="Editar Problemas";# = 12;
const VIEW_SESSION_FORM="Crear nova sessió";# = 13;
const VIEW_SESSION_LIST="Llista de sessions";# = 14;
const VIEW_SESSION_PROBLEMS_LIST="Llista de problemes";# = 15;
const VIEW_PROBLEM_CREATE_GIT="Crear Problema from Git";# = 16;
const VIEW_PROBLEM_SOLUTION="Solucio Problema";# =17;
const VIEW_PROBLEM_SOLUTION_UPLOAD="Penjar solucio problema";# =18;
const VIEW_SESSION_LIST_GROUPS="Grups amb sessions";#=19;
const VIEW_MESSAGES_CHAT = "Ver chat";
const VIEW_COMUN_MESSAGES="Chat comu";
const CREATE_CSV = "Crear CSV";
const SESSIONS_TO_EDIT = "Sessions per editar";
const EDIT_SESSION = "Editar sessió";


CONST STUDENT_VIEWS = [VIEW_SUBJECT_LIST, VIEW_PROBLEMS_LIST, VIEW_EDITOR, VIEW_SESSION_LIST,
    VIEW_SESSION_PROBLEMS_LIST,VIEW_SESSION_LIST_GROUPS, VIEW_PROBLEM_SOLUTION];
CONST ANONYMOUS_USER_VIEWS = [VIEW_SUBJECT_LIST, VIEW_LOGIN_FORM, VIEW_REGISTER_FORM];

# Professor view mode of a student
const VIEW_MODE_EDIT = 1;
const VIEW_MODE_READ_ONLY = 2;

# Allowed file extensions
const ALLOWED_FILE_EXTENSIONS = ["cpp", "h", "py", "python", "txt", "ipynb"];