## Analysing saved profiler data

1. Fetch from server your file with serialized profiler data.
2. Call`parse-profiler-data` to convert data to SQL queries for SQLite - `./parse-profiler-data /path/to/saved/profiler/data | sqlite3 profiler.db`
3. Open `profiler.db` in `sqlitebrowser` or other client e.g. PHPStorm.
4. Use SQL to select/filter stored data and export them to CSV/TSV for better analyse in for example LibreOffice Calc.  

### Useful SQL queries

SELECT MIN, MAX, AVG, total duration, number of probes:

```sql
SELECT MIN(duration) as min, MAX(duration) as max, AVG(duration) as avg, SUM(duration) as total_duration, count(1) as number_of_probes
FROM profiler 
WHERE depth = 0;
```

Filter rows which has a field `userID` with value `123` in metadata:
```sql
SELECT * FROM profiler WHERE json_extract(metadata, '$.userID') = 123;
```

Convert duration to INT

```sql
SELECT *, CAST(ROUND(duration, 0) as INT) duration_ms_int FROM profiler;
```

### Useful LibreOffice Calc functions

> ⚠️ **Warning**
> To use english function names you need mark a checkbox **Use English function names**.
>
> From "Tools" menu, select "Options".
> In showed dialog from left menu expand item "LibreOffice Calc" and then select "Formula" item.
> On the right check the checkbox "Use English function names"

![Use English function names in LibreOffice Calc](docs/libreoffice-calc.png)

95% PERCENTILE - `=PERCENTILE(A1:A10;0.95)`

MEDIAN - `=MEDIAN(A1:A10)`

## Flame Graph

This repo contains a fantastic external script to generate [flame graph](https://www.brendangregg.com/flamegraphs.html).

`./create-flame-graph /path/to/saved/profiler/data /output/direcotry/for/svg/files`

![flame graph](docs/flame-graph.png)
