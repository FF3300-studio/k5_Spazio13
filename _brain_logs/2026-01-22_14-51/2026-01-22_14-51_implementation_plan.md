# Dynamic JSON and CSV Templates

Refactor `default.json.php` and `default.csv.php` to automatically map page fields and omit empty ones, while maintaining the current logic for handling page children.

## Proposed Changes

### [Templates]

#### [MODIFY] [default.json.php](file:///Users/ff3300/Desktop/SITI/k5-spazio13/site/templates/default.json.php)

- Replace the hardcoded `serializePage` function with a dynamic one.
- Remove or comment out `Content-Disposition: attachment` to display in browser.
- The new function will:
    - Include base fields: `url`, `title`.
    - Loop through all content fields of the page.
    - Omit empty fields.
    - Use the blueprint to determine the field type and apply appropriate formatting:
        * `pages`, `multiselect`, `tags`: Return titles or values as an array.
        * `files`: Return the file URL(s).
        * `structure`: Recursively serialize entries (mapping all subfields).
        * `date`: Format using the project's Italian date format.
        * `toggle`: Return boolean.
    - Include special calculated fields like `risposte_form` (total responses) if the page is a form or has responses.

#### [MODIFY] [default.csv.php](file:///Users/ff3300/Desktop/SITI/k5-spazio13/site/templates/default.csv.php)

- Apply the same dynamic logic as `default.json.php`.
- Remove or comment out `Content-Disposition: attachment` to display in browser.
- Ensure complex data (arrays, objects) are flattened or stringified appropriately for CSV (e.g., using `implode(' | ', ...)` for lists).
- Collect all unique non-empty keys from all rows to build a consistent CSV header.

## Verification Plan

### Manual Verification
- Access a page with children (e.g., a collection page) and append `.json` and `.csv` to the URL.
- Verify that only fields with content are present.
- Verify that complex fields (pages, files, structures) are correctly formatted.
- Access a "leaf" page (no children) and verify it exports its own data.
- Check that `risposte_form` calculation is still present and correct.
