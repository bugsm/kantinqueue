@echo off
echo Starting Task Scheduler...

cd /d C:\laragon\www\kantinqueue

REM Check if PHP exists
if not exist "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" (
    echo Error: PHP not found at C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe
    echo Please check your Laragon installation
    pause
    exit /b 1
)

REM Run the scheduler
echo Running task scheduler...
C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe app/task_scheduler.php

if errorlevel 1 (
    echo Error: Task scheduler failed to run
    pause
    exit /b 1
)

echo Task scheduler completed successfully
pause 