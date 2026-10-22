# Experiment 17: Controlled File Upload & Streaming Download

## Title
Implement secure server-side file upload handling with extension whitelisting, file size restrictions, and unique renaming.

## Aim
To prevent malicious arbitrary file execution (RCE) by validating MIME extensions and storing files in controlled subdirectories.

## Algorithm
1. Set form `enctype="multipart/form-data"`.
2. Inspect `$_FILES['note']['error']` to confirm successful HTTP upload.
3. Extract file extension using `pathinfo($filename, PATHINFO_EXTENSION)` and check against whitelist (`pdf`, `docx`, `txt`).
4. Validate `$_FILES['note']['size']` against 5MB maximum limit.
5. Generate a cryptographically random filename using `uniqid()` to prevent path overwrite attacks.
6. Transfer temporary file to designated upload directory using `move_uploaded_file()`.

## Execution Instructions
Open `http://localhost:8000/LAB/17_study_materials/upload.php`.
