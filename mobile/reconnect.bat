@echo off
REM Use this if the app is already running but shows "Connection refused" -
REM it means the USB tunnel dropped (happens when the phone reconnects/locks).
REM This re-establishes it without restarting the app.

set ADB="C:\Users\leryr\AppData\Local\Android\Sdk\platform-tools\adb.exe"

%ADB% reverse tcp:8080 tcp:80
%ADB% reverse --list

echo.
echo Tunnel re-established. Try again in the app.
pause
