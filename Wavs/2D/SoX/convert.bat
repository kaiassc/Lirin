cd %~dp0
FOR %%A IN (%*) DO sox %%A "../%%~nA-L%%~xA" remix 1v0.33 1
FOR %%A IN (%*) DO sox %%A "../%%~nA-R%%~xA" remix 1 1v0.33

