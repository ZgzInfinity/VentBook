# EventOptionalFilterDisplayService - Technical and Functional Analysis

## 1. Overview
`EventOptionalFilterDisplayService` provides functionality to retrieve a list of events, optionally filtered by different criteria such as name, date range, and availability.

## 2. Input DTO
- **DTO class used:** `EventOptionalFilterDisplayDTO`
- **Validation and transformation:**  
  The input parameters are transformed into a typed DTO by the `EventOptionalFilterDisplayDTOAssembler`.
- **Filters:**  
  Accepts an optional array of filters, which must conform to predefined filter keys.

## 3. Output DTO
- **DTO class used:** `EventInfoDataDTO` (via `EventInfoDataDTOAssembler`)
- **Transformation:**  
  Each resulting event from the repository query is transformed into an output DTO.
- **Return:**  
  Returns an array of event DTOs or `null` if no events match.

## 4. Dependencies
- **EventRepository:** Used for querying events with dynamic filter conditions.
- **EventOptionalFilterDisplayDTOAssembler:** Parses and validates input filters.
- **EventInfoDataDTOAssembler:** Converts raw event records to output DTOs.

## 5. Database Interactions
- **Read operations:**  
  - Calls `findFilteredEvents` on `EventRepository`, passing SQL conditions and parameters built from filters.
- **Write operations:**  
  - None.
- **Transaction management:**  
  - Not applicable since operation is read-only.

## 6. Business Logic Flow
1. Transform input parameters into a DTO to extract filters.
2. Flatten filters if nested and validate each filter key against a whitelist (`filterMapper`).
3. Validate each filter value is present and non-empty.
4. For each valid filter, prepare SQL conditions and query parameters.
5. Query the repository with constructed conditions and parameters.
6. If events are found, transform each into output DTO and return array.
7. If no events match, return `null`.

## 7. Exception Handling and Anomalies
- Throws `InvalidArgumentException` if:
  - A filter key is not recognized.
  - A filter value is empty or missing.
- Repository errors propagate naturally.
- Handles empty filters gracefully by querying without conditions.

## 8. Variables and Data Flow
- **Input:** Array with optional `filters` key containing filter criteria.
- **Filter mapping:**  
  - 'EVENT_FILTER_NAME' => 'name'  
  - 'EVENT_FILTER_START_DATE' => 'from_date'  
  - 'EVENT_FILTER_END_DATE' => 'to_date'  
  - 'EVENT_FILTER_AVAILABLE' => 'available_seats'
- **SQL building:**  
  - Conditions and parameters arrays populated based on filters.
- **Output:** Array of event DTOs or `null`.

## 9. Summary and Recommendations
- Dynamic filtering enables flexible querying of events.
- Proper validation of filter keys and values improves robustness.
- The flattening logic for nested filters should be carefully tested to avoid unexpected merges or overwrites.
- Returning `null` for no results is consistent but consider empty array for clarity.
- No transaction needed as this is a read operation.

