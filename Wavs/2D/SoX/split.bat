cd %~dp0
FOR %%A IN (%*) DO sox "%%~nA%n%%~xA" newfile : trim 0 30 
