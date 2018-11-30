c:\windowns\system32\taskkill /im lynxportable.exe /F
c:\windowns\system32\taskkill /im lynx.exe /F


@echo off
SET PATH=C:\lynx\App\Lynx; [path to lynx folder]
SET lynx_cfg=C:\lynx\App\Lynx\lynx.cfg
"C:\lynx\App\Lynx\lynx.exe" http://localhost/org/enviar
q /y
