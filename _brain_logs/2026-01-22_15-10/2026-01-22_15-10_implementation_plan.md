# JSON Template API Restructuring

This plan outlines the changes to `site/templates/default.json.php` to exclude internal fields, restructure the output for parent/child pages, and ensure a clean API-ready format.

## Proposed Changes

### [Templates]

#### [MODIFY] [default.json.php](file:///Users/ff3300/Desktop/SITI/k5-spazio13/site/templates/default.json.php)

- Add a list of excluded fields to `serializePage`:
    - `parent_collection_options`
    - `parent_categories_toggle`
    - `collection_toggle`
    - `collection_categories_manager_toggle`
    - `collection_pagination`
- Update the main template logic to:
    - If the page has children: Return an object with `page` (the parent's data) and `children` (an array of serialized children).
    - If the page has no children: Return the serialized data of the page itself as a single object.
- Ensure all fields are serialized in a consistent, API-friendly way.

## Verification Plan

### Manual Verification
- Access a page without children via `.json` (e.g., `/home.json` or a specific item) and verify it returns a single object without the excluded fields.
- Access a page with children (e.g., a collection page) and verify it returns a `page` object and a `children` array.
- Verify that the excluded fields are indeed missing from the output.
- Check that the structure is valid JSON and easy to consume by third-party services.
