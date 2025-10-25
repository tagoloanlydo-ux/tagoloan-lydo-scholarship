# TODO: Add Welcome Endpoint for Scholars

## Task Overview
Add a welcome endpoint for scholars, similar to the existing mayor_staff welcome endpoint. This will provide a JSON response with a welcome message and log the request.

## Information Gathered
- MayorStaffController has a `welcome` method that logs the request (method and path) and returns a JSON response: `{"message": "Welcome to the Mayor Staff API!"}`.
- ScholarController does not have a similar method.
- Scholar routes are protected under `scholar.auth` middleware.
- Existing route for mayor_staff: `Route::get('/mayor_staff/welcome', [MayorStaffController::class, 'welcome'])->name('MayorStaff.welcome');`

## Plan
1. **Add Route**: In `routes/web.php`, inside the `scholar.auth` middleware group, add: `Route::get('/scholar/welcome', [ScholarController::class, 'welcome'])->name('scholar.welcome');`
2. **Add Method**: In `app/Http/Controllers/ScholarController.php`, add a `welcome` method that logs the request and returns a JSON welcome message for scholars.

## Dependent Files
- `routes/web.php`: Add the new route.
- `app/Http/Controllers/ScholarController.php`: Add the `welcome` method.

## Followup Steps
- Test the new endpoint by making a GET request to `/scholar/welcome` (authenticated as scholar).
- Verify logging in Laravel logs.
- Ensure no conflicts with existing routes.

## Confirmation Needed
Please confirm if this plan is acceptable before proceeding with the implementation.
