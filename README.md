# be_groups import/export for TYPO3

TBD...

## Migrations

### Update to > 0.6.0

Execute SQL script:

```sql
UPDATE table SET code_managed_group = bulk_export;
```