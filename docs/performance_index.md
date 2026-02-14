# Weighted Performance Index (FPI) Methodology

To create a reliable final value for comparing phones, we use a **Weighted Performance Index (FPI)**. This approach normalizes different benchmark scales and assigns importance based on real-world usage.

## 1. Normalization
Normalization brings all scores into a common range (0 to 100).
**Formula:**
$$ \text{Normalized Score} = \left( \frac{\text{Device Score}}{\text{Best Score in Dataset}} \right) \times 100 $$

*The "Best Score in Dataset" is dynamically determined from the maximum score available in the database for each category.*

## 2. Weights
We assign weights to each category to reflect their importance in daily usage and high-performance scenarios.

| Benchmark Type | Measure | Weight |
| :--- | :--- | :--- |
| **AnTuTu v11** | Overall System (CPU, GPU, RAM, UX) | **40%** |
| **Geekbench Multi** | Heavy Multitasking / Productivity | **25%** |
| **Geekbench Single** | Daily App Speed / Snappiness | **15%** |
| **3DMark Extreme** | Gaming & High-end Graphics | **20%** |

## 3. Calculation
The Final Performance Index (FPI) is the sum of the weighted normalized scores.

**Formula:**
$$ \text{FPI} = \sum (\text{Normalized Score} \times \text{Weight}) $$

### Example Calculation
Assuming "Best in Class" scores are: AnTuTu (4M), GB Multi (12K), GB Single (3.5K), 3DMark (8K).

1.  **AnTuTu**: $(3,688,274 / 4,000,000) \times 100 \times 0.40 = 36.88$
2.  **GB Multi**: $(11,062 / 12,000) \times 100 \times 0.25 = 23.04$
3.  **GB Single**: $(3,250 / 3,500) \times 100 \times 0.15 = 13.92$
4.  **3DMark**: $(7,370 / 8,000) \times 100 \times 0.20 = 18.42$

**Final FPI** = $36.88 + 23.04 + 13.92 + 18.42 = \mathbf{92.26}$
