# Streak derived from Logs, not stored

Streak is calculated on the fly by querying the `logs` table and counting consecutive days backwards from today. We deliberately chose not to store a `current_streak` column on the `habits` table.

Storing the streak requires either a scheduled job to detect missed days or a "catch up on first load" trigger — both are fiddly to get right and easy to get subtly wrong (timezone edge cases, missed cron runs). Since MYTH is a single-user personal app, the Logs table will never grow large enough for on-the-fly calculation to become a performance concern.

## Considered Options

- **Stored streak column** — increment on Log creation, reset via cron or on next Dashboard load when a gap is detected. Faster reads, but requires background job infrastructure and careful edge-case handling.
- **Derived from Logs** *(chosen)* — always accurate, no stored state to drift out of sync, no scheduler needed.
