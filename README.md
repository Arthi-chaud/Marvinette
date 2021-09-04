[![Marvinette](images/logo.PNG)](images/logo.PNG)

## Functional tests framework written in PHP. Targeted towards Epitech IT students

[![Tests](https://github.com/Arthi-chaud/Marvinette/actions/workflows/tests.yml/badge.svg?branch=dev)](https://github.com/Arthi-chaud/Marvinette/actions/workflows/tests.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Arthi-chaud_Marvinette&metric=alert_status)](https://sonarcloud.io/dashboard?id=Arthi-chaud_Marvinette)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Arthi-chaud_Marvinette&metric=coverage)](https://sonarcloud.io/dashboard?id=Arthi-chaud_Marvinette)
[![Documentation](https://img.shields.io/badge/Documentation-Doxygen-blue)](https://arthi-chaud.github.io/Marvinette/)

---

## Installation / Update Command

1. Clone the repository

2. Move it to your `$HOME` folder (or a directory where you wouldn't want to move the repository)

3. Inside the repository, execute the following command:

```shell
sudo php MarvinetteInstall.php
```

**Warning**: Once you executed the command, you must not move the Marvinette folder. If you do, you'll have to execute the install command again

---

## How to Create and Execute your tests

1. Before anything else, you must create your project's configuration file for Marvinette. To do so:
    - go to your project's directory
    - execute the following command:

    ```shell
    marvinette --create-project
    ```

    - You will be prompt to enter several values.
    - You can modify your configuration file using the following command (avoid changing the file yourself):

    ```shell
    marvinette --mod-project
    ```

2. You can now create your first functionnal test!
    - execute the command:

    ```shell
    marvinette --add-test
    ```

    - You will be prompt to enter different values.
    - In your test's folder, some files will be empty: you'll have to fill them yourself (expectedStdout/Stderr, stdinput, stdout/stderrFilter). If a file is empty, for example expectedStdout, the standard output of the programm will be compared with the file's content. If you don't want to, simply delete the file.

3. Your tests are ready to be executed!
    - To execute one test, execute the command:

    ```shell
    marvinette --execute-test
    ```

    You will be asked to choose which test to execute
    - To execute all your tests, execute the command:

    ```shell
    marvinette --execute-tests
    ```

---

## Available commands

Use the framework using the following command-line instructions:

```shell
marvinette [option]

    option:
        --create-project: Create a main configuration file, required to make tests
        --del-project, --delete-project: Delete configuration file and existing tests
        --mod-project: Modify the projec\'s info.
        
        --add-test, --create-test: Create a functionnal test
        --mod-test: Modify a functionnal test
        --del-test, --delete-test: Delete a functionnal test

        --execute-test,--exec-test : Execute a test
        --execute-tests, --exec-all  : Execute all tests

        -h, --help: display this usage
```

---

## What are the created files for?

The framework's configuration file, ```Marvinette.json```, holds the following informations:

- ```name```: The name of the project
- ``` binary name```: the name of the binary/script to execute
- ```binary path```: the path to the binary/script
- ```interpreter```: the interpreter of the script (null if ELF file)
- ```tests folder```: the path to the tests' folder

**Warning**: For optimization reasons, it is **not recommended** to modify the file yourself. If you want to change values, please use the marvinette command (see previous section).

Upon test creation, several files are generated in the ```testFolder```/```testName``` folder:

- ```commandLineArguments```: contains the arguments which will be passed to the program. If the file doesn't exists, no parameters will be sent.
- ```interpreterArguments```: contains the arguments which will be passed to the program's interpreter. If the file doesn't exists, no parameters will be sent. The arguments will be read only if an interpreter is set in the ```Marvinette.json``` file.
- ```expectedReturnCode```: if the file exists, the returned code from the program will be compared with the number in the file (if the file's content is not a number, the comparison will be scrapped). If there is no such file, no comparison will be done.
- ```stdoutFilter```: if the file exists, the standard output of the program will be piped onto the command written in the file.
- ```stderrFilter```: if the file exists, the error output of the program will be piped onto the command written in the file.
- ```stdinput```: if the file exists, the content of the file will be standard input to the program.
- ```emptyEnv```: if the file exists, the test command will be executed within an empty environment (the file's content will not be read).
- ```expectedStdout```: if the file exists, the (filtered) standard output will be compared to the file's content. If there is no such file, no comparison will be done.
- ```expectedStderr```: if the file exists, the (filtered) error output will be compared to the file's content. If there is no such file, no comparison will be done.
- ```setup```: if the file exists, the command written in the file will be executed before the program is launched.
- ```teardown```: if the file exists, the command written in the file will be executed after the program is launched.

If a ```setup```, ```stdout/stderrFilter``` or ```teardwon``` command doesn't return 0, the test will fail.

Upon creation, feel free to change the files' content by yourself.
Be careful not to leave any *useless* line breaks of trailing spaces

---

## Compatibility & Pre-Requisites

To use Marvinette, you'll need to execute your tests on an Unix-based OS (Windows compatibility incoming...).

Also, you'll need PHP >= 7.4. No other library needed (the ```vendor``` folder and composer configuration file are used for Marvinette's unit tests)

---

[![SonarCloud](https://sonarcloud.io/images/project_badges/sonarcloud-white.svg)](https://sonarcloud.io/dashboard?id=Arthi-chaud_Marvinette)
