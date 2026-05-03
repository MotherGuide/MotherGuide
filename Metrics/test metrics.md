# MotherGuide – Software Test Metrics

This document applies software test metrics concepts to MotherGuide's key operations. It covers test case estimation, coverage, and defect measurement based on the system's current features.

---

## Key Operations to Test

MotherGuide's testable operations fall into three main areas:

| Area | Operations |
|------|------------|
| Auth | User login, Admin login, registration (both roles) |
| Tips | Add tip, fetch tip stats, delete tip |
| Interaction | Like/dislike a tip, post a comment |

---

## Estimating Number of Test Cases

If 10% of a hypothetical budget of UGX 4,000,000 is allocated to testing, and each test case costs roughly UGX 25,000 or 2 hours to prepare, with a team of 2 working for 8 weeks (40 hrs/week):

- **From cost:** (400,000) / 25,000 = **16 test cases**
- **From time:** (8 × 40 × 2) / 2 = **320 hours ÷ 2 = 320 test cases**
- **Selected minimum: 16 test cases**

This is realistic for a small academic project. Distribute them across the core operations.

---

## Specifying Test Cases – Example: User Login

The `login()` method in `User.php` accepts an email and password. Using equivalence classes:

| Case | Input | Expected |
|------|-------|----------|
| TC-01 | Correct email + correct password | Login succeeds, session set |
| TC-02 | Correct email + wrong password | "Invalid email or password" |
| TC-03 | Email not in database | "Invalid email or password" |
| TC-04 | Empty email field | Login fails |
| TC-05 | SQL injection attempt in email | Login fails, no crash |

TC-02 and TC-03 are *equivalent* — both return the same error. One test covers both.

---

## Test Coverage

### Statement Coverage
Most of the logic in `User.php`, `Admin.php`, and `Comment.php` is inside individual methods. Running TC-01 through TC-05 above exercises the main conditional in `login()` (the `password_verify` check), touching roughly:

- `login()` – ~90% of statements covered with 5 cases
- `emailExists()` – 100% with 1 positive and 1 negative case
- `create()` – 100% with a successful registration test

### Branch Coverage
Each `if` block in the codebase needs both a true and false path tested:

| Branch | True Path Test | False Path Test |
|--------|----------------|-----------------|
| `if ($admin && password_verify(...))` in Admin login | Valid credentials | Wrong password |
| `if ($result && $result->num_rows > 0)` in generateId | Table has rows | Empty table |
| `if ($stmt->execute())` in addTip | Valid tip data | DB error simulation |

### GUI Coverage
The admin dashboard (`admin_dashboard.php`) exposes these interactive elements:

- Add New Tip button → links to `admin_add_tip.php`
- Manage Tips toggle → loads tips via `api/get_tips.php`
- Delete button per tip → calls `api/delete_tip.php`
- Sign Out link → calls `logout.php`

**GUI Coverage = 4 tested / 4 total = 100%** (if all buttons are exercised in a browser walkthrough)

---

## Test Pass, Failure & Pending Rates

After running the test cases above on a local setup, a sample result might look like:

| Result | Count |
|--------|-------|
| Passed | 13 |
| Failed | 2 |
| Pending | 1 |
| **Total** | **16** |

- **Pass Rate** = 13/16 × 100 = **81.25%**
- **Failure Rate** = 2/16 × 100 = **12.5%**
- **Pending Rate** = 1/16 × 100 = **6.25%**

A pending test typically means a feature like email delivery or a third-party integration couldn't be verified in the test environment.

---

## Remaining Defects Estimate

Using the seeding method (inject known faults, count how many are found):

If 5 artificial bugs are seeded and 4 are found while 20 real bugs are also detected:

- **Nd = (20/4) × 5 = 25** estimated total real defects
- **Nr = (25 − 20) + (5 − 4) = 6** remaining undetected defects

This tells the team there are likely still around 6 bugs hiding in untested paths — a signal to expand test coverage before deployment.

---

## Summary

| Metric | Value | Note |
|--------|-------|-------|
| Estimated test cases | 16 | Based on time/budget constraints |
| Key coverage target | Branch coverage | Each if-else must have both paths tested |
| GUI elements tested | 4/4 | Full admin dashboard walkthrough |
| Estimated remaining defects | ~6 | Based on fault seeding model |
| Highest risk operation | `Admin.login()` | Clears user sessions — side effects need care |
