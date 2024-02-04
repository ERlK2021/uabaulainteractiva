@echo off

set user=%1
set port=%2

:: Get the parent folder using the path of the script
FOR %%a IN (%~dp0) DO FOR %%b IN ("%%~dpa.") DO set "parent_folder=%%~dpb"

:: Run the container with the user specified port and user name
:: Mount the solutions folder to /home/solutions/ and the .jupyter folder as readonly
:: Set the owner of the home folder as 'jovyan' and remove the token access
docker run -d -it --rm -p %port%:8888 -e DOCKER_STACKS_JUPYTER_CMD=nbclassic --mount type=bind,source=%parent_folder%\app\solucions\%user%,target=/home/solutions --mount type=bind,source=%~dp0.jupyter,target=/home/jovyan/.jupyter,readonly -e NB_USER="jovyan" -e CHOWN_HOME=yes -w "/home/solutions/" jupyter/base-notebook start-notebook.sh --NotebookApp.token=""