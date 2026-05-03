# MotherGuide – Object-Oriented Metrics

This document applies OO metrics (Chidamber & Kemerer) to the five core classes in MotherGuide: `Database`, `User`, `Admin`, `Tip`, and `Comment`.

---

## Classes Overview

| Class | Role |
|-------|------|
| `Database` | Opens and returns the MySQL connection |
| `User` | Handles patient registration, login, and ID generation |
| `Admin` | Handles admin auth, tip creation, and tip ID generation |
| `Tip` | Fetches like/dislike/comment stats for a tip |
| `Comment` | Inserts a new comment into the database |

---

## Weighted Methods per Class (WMC)

WMC counts the number of methods in a class. A high WMC means the class is doing a lot — which can hurt reusability and make maintenance harder.

| Class | Methods | WMC |
|-------|---------|-----|
| `Admin` | generateId, emailExists, create, findByEmail, login, generateTipId, addTip | 7 |
| `User` | generateId, emailExists, create, findByEmail, login | 5 |
| `Tip` | getStats | 1 |
| `Comment` | create | 1 |
| `Database` | connect | 1 |

**Average WMC = 15 / 5 = 3.0** — well within the recommended threshold of 20.

> `Admin` carries more responsibility than the others. If the project grows, it may be worth splitting tip-management methods into a separate `TipManager` class.

---

## Depth of Inheritance Tree (DIT)

DIT measures how deep a class sits in an inheritance hierarchy. Deeper = more complex behaviour to predict.

MotherGuide uses **no inheritance** — all five classes stand alone. Every class has **DIT = 0**.

This keeps the system simple and predictable, which suits a small project like this well.

---

## Coupling Between Objects (CBO)

CBO counts how many *other* classes a class depends on (non-inheritance links). High coupling makes a class harder to reuse or test in isolation.

| Class | Coupled To | CBO |
|-------|------------|-----|
| `User` | `Database` (via constructor) | 1 |
| `Admin` | `Database` (via constructor) | 1 |
| `Tip` | `Database` (via constructor) | 1 |
| `Comment` | `Database` (via constructor) | 1 |
| `Database` | None | 0 |

All domain classes depend only on `Database` — CBO stays at 1 across the board. This is a healthy, loosely coupled design.

---

## Lack of Cohesion (LCOM)

A cohesive class has methods that all work on the same data. Low cohesion (high LCOM) means a class is juggling unrelated jobs.

- `User` and `Admin` are cohesive — every method works on user/admin data (`$id`, `$email`, `$password`, etc.).
- `Admin` is slightly less cohesive because it also manages tips (`addTip`, `generateTipId`). These tip methods touch a different table (`tips`) rather than the `admins` table.
- `Tip`, `Comment`, and `Database` each do one thing — highly cohesive.

---

## Number of Children (NoC)

NoC counts how many subclasses a class has. MotherGuide uses no subclassing, so **NoC = 0 for all classes**.

---

## Summary

| Metric | Result | Assessment |
|--------|--------|------------|
| Average WMC | 3.0 | Good — classes are small and focused |
| Max DIT | 0 | Simple — no inheritance used |
| Max CBO | 1 | Good — loose coupling throughout |
| LCOM concern | Admin class | Consider splitting tip logic out |
| NoC | 0 | No reuse through inheritance yet |
