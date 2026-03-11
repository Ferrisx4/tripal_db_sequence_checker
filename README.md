# Tripal Database Sequence Checker

Checks for Postgres table sequences that have gotten out of sync with the data in the actual table.

## Example
For various reasons, your database may end up in a situation like this:
|id|name|age|
|--|----|---|
|1|Alice|40|
|2|Bob|50|
|3|Charlie|45|
|...|...|...|
|20|Thomas|43|

where the `id` field has the following sequence item: `nextval('public.people_id_seq'::regclass)` which currently  has the following value:
```sql
SELECT nextval('public.people_id_seq');
 nextval 
---------
      14
```

As you can see, 14 is lower than the current max(id) from the table which will cause data when trying to do an insert as 14 may already have data.

## How to use
```shell
drush sequence_checker
```
This will check the database and report any out-of-sync sequences and provide SQL commands to fix them:
```shell
 -------- -------------- ------------- -------------------------- ------------ ------------- 
  Table    Schema         Column        Sequence                   Last Value   Current Max  
 -------- -------------- ------------- -------------------------- ------------ ------------- 
  chado    cvterm         cvterm_id     cvterm_cvterm_id_seq       78308        78553        
  public   file_managed   fid           file_managed_fid_seq       1712         1858         
  chado    db             db_id         db_db_id_seq               429          436          
  chado    organism       organism_id   organism_organism_id_seq   179          8381         
  chado    cv             cv_id         cv_cv_id_seq               79           84           
 -------- -------------- ------------- -------------------------- ------------ ------------- 

To fix these issues, you can run these commands in the database:
ALTER SEQUENCE chado.cvterm_cvterm_id_seq RESTART 78554;
ALTER SEQUENCE public.file_managed_fid_seq RESTART 1859;
ALTER SEQUENCE chado.db_db_id_seq RESTART 437;
ALTER SEQUENCE chado.organism_organism_id_seq RESTART 8382;
ALTER SEQUENCE chado.cv_cv_id_seq RESTART 85;
```