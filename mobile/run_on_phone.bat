@echo off
REM Run the Quest Board app on a USB-connected Android phone.
REM 1. Re-establishes the USB port forward so the phone can reach XAMPP on the PC.
REM 2. Starts the Flutter app.

set ADB="C:\Users\leryr\AppData\Local\Android\Sdk\platform-tools\adb.exe"

echo Checking connected devices...
%ADB% devices

echo.
echo Setting up USB tunnel (phone:8080 -> PC:80)...
%ADB% reverse tcp:8080 tcp:80

echo.
echo Starting the app... (press "r" to hot reload, "R" to hot restart, "q" to quit)
cd /d "%~dp0"
flutter run
