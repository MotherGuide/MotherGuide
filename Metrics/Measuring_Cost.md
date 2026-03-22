# Cost Metrics for MotherGuide

Our Cost estimation for the **MotherGuide Platform** involves predicting the **effort, time, and resources** required to design, develop, deploy, and maintain the system.

This estimation is based on **software size, complexity, among others**, using established models such as **COCOMO II** and supporting estimation techniques .

## Objectives of this Cost Estimation

- Estimate total development cost
- Determine required effort (person-months)
- Predict project duration
- Support budgeting and resource allocation
- Reduce risk of project overruns

---

## Estimation Approach Used

### 1. Expert Judgment

- Based on experience from similar health systems (e.g., clinic management systems)
- Useful in early stages where requirements are not fully defined

### 2. Top-Down Estimation

- Estimate overall system cost based on major features:

  - User management
  - Pregnancy tracking
  - Appointment system
  - Health monitoring

### 3. Bottom-Up Estimation

- Break system into modules:

  - Authentication module
  - Patient records
  - Notifications system
  - Reporting dashboard
- Estimate cost of each module and sum up

### 4. Algorithmic Model (COCOMO II)

- Main estimation model used
- Calculates effort based on system size and cost drivers 

---

## COCOMO II Cost Estimation

### Basic Formula

Effort is estimated using:

> **E = a × (KLOC)^b × EAF**

Where:

* **E** = Effort (Person-Months)
* **KLOC** = Thousands of Lines of Code
* **EAF** = Effort Adjustment Factor
* **a, b** = Model constants

---

## Assumptions for MotherGuide

| Parameter       | Value                    |
| --------------- | ------------------------ |
| Estimated Size  | 25 KLOC                  |
| Project Type    | Semi-detached            |
| Team Experience | mixed experience         |
| Complexity      | Moderate                 |
| Tools           | Modern frameworks        |

---

## Effort Calculation

Using Semi-detached mode:

> **E = 3.0 × (25)^1.12**

Approximation:

* **E ≈ 3.0 × 37.6 ≈ 113 Person-Months**

---

## Development Time

Using:

> **Tdev = c × (E)^d**

Where:

* c = 2.5
* d = 0.35

Approximation:

* **Tdev ≈ 2.5 × (113)^0.35 ≈ 2.5 × 5.1 ≈ 12.75 months**

---

## Team Size

> **Team Size = Effort / Development Time**

* **≈ 113 / 12.75 ≈ 9 developers**

---

---

## Estimated Cost

Assuming:

* **1 Person-Month ≈ UGX 4,000,000**

> **Total Cost ≈ 113 × UGX 4,000,000 = UGX 452,000,000**

---

## Summary Table

| Metric           | Value                          |
| ---------------- | -----------------              |
| Estimated Size   | 25 KLOC                        |
| Effort           | 113 Person-Months              |
| Development Time | approximately 13 Months        |
| Team Size        | approximately 9 Developers     |
| Estimated Cost   | approximately UGX 452,000,000         |
---

## Limitations

- Size estimation (KLOC) may be inaccurate early in development
- Cost drivers are subjective
- Model assumes stable requirements
- Real-world constraints may affect cost

---

## Conclusion

The **MotherGuide Platform** is a **moderately complex system** requiring:

- A medium-sized development team
- Approximately 1 year of development
- A structured cost estimation model like **COCOMO II**

Using cost metrics ensures better **planning, budgeting, and project success**.
