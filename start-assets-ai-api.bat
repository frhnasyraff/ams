@echo off
cd /d %~dp0
set "BUNDLED_PY=%USERPROFILE%\.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe"
if exist ".venv-ai\Scripts\python.exe" (
  ".venv-ai\Scripts\python.exe" assets_analytics_api.py
) else if exist "%BUNDLED_PY%" (
  "%BUNDLED_PY%" assets_analytics_api.py
) else if exist "C:\Python312\python.exe" (
  "C:\Python312\python.exe" assets_analytics_api.py
) else (
  echo Python was not found. Install Python 3.10+ and run: pip install -r assets_ai_requirements.txt
  pause
)
