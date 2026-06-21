# CONTEXT.md

Glossary for MYTH (More Than Habits) — a personal self-improvement app.

---

## Habit

A recurring behavior a user wants to track for self-improvement. A Habit is either:

- **Binary** — tracked as done/not-done for a given day (e.g., "meditate")
- **Quantified** — tracked as a numeric amount compared against a daily target (e.g., "drink 8 glasses of water")

The distinction is expressed via an optional `target` field: if absent, the Habit is binary; if present, it is quantified.

### Habit fields

| Field | Type | Required | Notes |
|---|---|---|---|
| `name` | string | yes | Short label shown on the Dashboard |
| `goal` | string | yes | User's personal motivation/intention for the habit |
| `target` | number | no | Absent = binary habit; present = quantified habit |
| `unit` | string | no | Unit of measurement for target (e.g., "glasses", "km") |
| `emoji` | string | no | Single emoji for visual identity |
| `color` | string | no | Hex or preset color for visual identity |
| `sort_order` | integer | yes | Controls Dashboard display order; set via drag and drop |
| `duration_days` | integer | no | Absent = open-ended Habit; present = Challenge (finite commitment) |

## Challenge

A Habit with a `duration_days` value set. The user commits to the Habit for a fixed number of days starting from creation. The habit show page displays a progress bar ("Day X of N") and the activity heatmap spans exactly the duration window rather than the rolling 52-week default. A Challenge is still a Habit — no separate model exists.

## Archive

A soft-deleted Habit. An Archived Habit is hidden from the Dashboard but its Logs are preserved. Behavior of Streaks and miss-counting for Archived Habits is deferred post-MVP.

## Authentication

None. The app has no login in the MVP — it is a single-user personal tool accessed directly.

## Dashboard

The primary screen of the app. Shows all Habits as a sortable list. Each row shows the Habit's daily log control and its Current Streak.

Below the list, three aggregate stats are shown:

- **Today** — count of Habits completed today out of total (e.g., "3/5")
- **This week** — average completion rate across all Habits over the rolling last 7 days (as a percentage)
- **Best active streak** — the highest current Streak across all Habits

Below the stats, a 52-week activity heatmap shows daily completion counts across all Habits.

"Completed" means: at least one Log exists for that day (binary), or the day's Logs sum to ≥ target (quantified).

## Today

The only date on which a Log can be created. Backfilling past days is not supported in the MVP.

## Streak

The count of consecutive days on which a Habit has at least one Log (or whose Logs sum to ≥ target for quantified Habits). Missing a single day resets the Streak to 0. There is no grace period. The Streak is derived from Logs — it is not stored independently.

## Log

A single incremental entry recorded against a Habit on a given day. Multiple Logs can exist for one Habit on one day — they accumulate toward the target.

- **Binary Habit** — one Log per day with an implicit amount of 1 marks the day as done.
- **Quantified Habit** — each Log stores a numeric amount (e.g., "+1 glass"). The day's Logs are summed and compared against the Habit's target.

A day with no Log for a Habit counts as a miss and breaks the Streak.

Logs can be individually deleted. Viewing and deleting today's Logs is done per-Habit.

## Schedule

All Habits recur **daily** — every habit is expected once per day, every day. No per-day-of-week configuration.
