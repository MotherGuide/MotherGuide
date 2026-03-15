# MotherGuide: Software Size Analysis 

## (User Authentication mudule)


The software size and complexity of the User Authentication module, applying formal measurement concepts to our specific project files.



### A. `signup.php` (Registration Logic)
This file is the most extensive in the authentication module, handling data collection, validation, and database insertion.

* **Length (LOC and NCLOC):** The physical size is approximately 86 lines of code (excluding white space). A significant portion is executable (NCLOC), while comments primarily explain security measures and data validation steps.
* **Complexity:** The cyclomatic complexity is high due to multiple conditional branches. It contains 6 distinct `if` statements to validate HTTP methods, check for empty fields, validate email formats, enforce a minimum password length of 6 characters, restrict the pregnancy week to a 1-40 range, and prevent duplicate email registrations.
* **Functionality (External Inputs):** It processes four key input elements from the user form: `full-name`, `email`, `password`, and `pregnancy-week`.

### B. `login.php` (Authentication Logic)
This file is a more streamlined component designed for quick data verification.

* **Length (LOC):** The file is compact, measuring at 32 lines.
* **Functionality (External Inquiries):** This file represents an External Inquiry (EQ). It does not create new data; instead, it takes two inputs (`email` and `password`) and queries the system to verify identity.
* **Functionality (External Outputs):** The output is a JSON response signaling whether the login attempt was successful or if an error occurred (e.g., "Method not allowed" or "Email and password are required").

### C. `Database.php` 
These files define the data foundation and shared resources for the application.

* **Internal Logical Files (ILF):** The database schema defines the `users` table. This represents our primary Internal Logical File, containing 7 Data Element Types (DETs): `id`, `name`, `email`, `password`, `sign_up_day`, `sign_up_date`, and `pregnancy_week`.
* **Reuse Level:** The `Database.php` file defines a `connect()` method using `mysqli`. This represents internal reuse, as the connection logic is centralized and reused by both `signup.php` and `login.php`.

---

## System Functionality Metrics

When looking at the User Authentication module as a whole through the view of **Function Points** and **Value Adjustment Factors (VAF)**:

* **Data Communications:** High. The entire module relies on HTTP POST requests and JSON communication facilities.
* **Online Data Entry:** High. User credentials and profile data are entered directly through a web interface.
* **Complex Processing:** Moderate. The system implements specific security processing, notably using `password_hash()` with `PASSWORD_DEFAULT` (bcrypt) for secure credential storage.

---

## Comparison Summary


| Metric | Signup (`signup.php`) | Login (`login.php`) |
| :--- | :--- | :--- |
| **Primary Size Category** | **Length (LOC)** and **Functionality (EI)** | **Functionality (EQ)** |
| **Logic Density** | High (Extensive internal validation logic) | Low (Delegates to external class methods) |
| **Structural Complexity** | Complex (Multiple decision paths for constraints) | Simple (Linear execution flow) |
| **Data Interaction** | **Writes** new records to Internal Logical Files (ILF) | **Reads/Verifies** against Internal Logical Files (ILF) |
| **Reuse** | Reuses `Database.php` | Reuses `Database.php` and `User.php` |

## (Tips Generation module)

The software size of our Tips module. We used the files `Tip.php`, and `admin_add_tip.php` for this analysis.

## 1. Concept Applications by File

### A. `tips.php` (User Dashboard)
This file is the main screen where mothers read their health tips.
* **Length (LOC):** About 349 lines. It uses PHP to get data and HTML/JavaScript to show it on the screen.
* **Functionality (External Output):** This is an **External Output (EO)**. It takes the user's current pregnancy week and displays the correct health advice.
* **Complexity:** This file has **High Complexity**. It uses a special "Trimester" logic to calculate if a mother is in her 1st, 2nd, or 3rd trimester based on the week number.

### B. `Tip.php` (Data Logic)
This is a class file that handles all communication with the database.
* **Length (LOC):** About 60 lines. It is short because it only focuses on data tasks.
* **Reuse Level:** This file has **High Reuse**. The `create()` method is used by the admin to add tips, and the `getByWeek()` method is used by the dashboard to show tips.
* **Internal Logical Files (ILF):** This file manages the **`tips`** table. It tracks 4 main items: `id`, `title`, `content`, and `pregnancy_week`.

### C. `admin_add_tip.php` (Admin Input)
This file is used by the staff to add new tips to the system.
**Length (LOC):** About 359 lines. 
* **Functionality (External Input):** This is an **External Input (EI)**. The admin enters the tip details, and the system saves them to the database.
* **Complexity:** It uses "Validation Logic." It checks if the title is long enough and if the week number is between 1 and 40.

---

## 2. General System 

* **Internal Logical Files (ILF):** We use two main tables: `tips` and `users`. These are where our data is stored.
* **Value Adjustment Factors (VAF):** * **End-User Efficiency:** High. The system automatically finds the right tip so the mother does not have to search for it manually.
    * **Data Communications:** High. The system uses the internet to send and receive tip data.

---

## 3. Comparison Summary

We compared how the different parts of the module use these concepts:

| Metric | Tips Display (`tips.php`) | Admin Add (`admin_add_tip.php`) |
| :--- | :--- | :--- |
| **Main Category** | **External Output (EO)** | **External Input (EI)** |
| **Primary User** | The Mother (Patient) | The Admin (Staff) |
| **Data Action** | **Reads** data to show a tip | **Writes** data to create a tip |
| **Logic Complexity** | High (Trimester calculations) | Moderate (Data validation) |
| **Reuse** | Reuses `Tip.php` | Reuses `Tip.php` |

**Conclusion for the Group:**
The **Tips Display** is our biggest "Output," focusing on how the mother sees the data. The **Admin Add** is our primary "Input," focusing on data entry. Both files are efficient because they share the same `Tip.php` file for database tasks.
