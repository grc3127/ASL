# ASL/KIDSS student portal update — 2026-10-09

This package is based on the uploaded `ASL(1).zip` and preserves the existing PHP project structure.

## Updates made

- Fixed student sidebar navigation so the Logout link is not intercepted by the AJAX page loader.
- Kept the active sidebar item synchronized when a page is opened from a dashboard shortcut.
- Made the student dashboard and lessons page display the logged-in student's name rather than a hardcoded name.
- Rebuilt the quiz question list from the available `assets/quiz/images/A.png` through `Z.png` files, giving the quiz 26 questions and randomized answer choices.
- Added a browser speech-synthesis fallback for the quiz sound button when a letter's MP3 file is not present.
- Added a clear notice when the requested sign video has not been uploaded.
- Added student Progress, Profile, and Settings pages so all current student sidebar destinations exist.
- Added a short update note and accessibility preference styles.

## Current media limitation

The uploaded ZIP contains the alphabet PNG images, but it does not contain `assets/quiz/audio/*.mp3` or `assets/quiz/videos/*.mp4`. The quiz therefore uses browser speech synthesis for sound when supported, and explains that a sign video is missing. To show actual signing demonstrations, add matching video files such as `assets/quiz/videos/A.mp4` through `Z.mp4`.

## Backend status

The Progress and Achievements pages still show demonstration data. Profile details come from the login session. These pages are structured as frontend pages and are not yet connected to student progress, quiz attempts, or achievement records in MySQL.

## Validation performed

- PHP syntax check completed for all PHP files in the package.
- JavaScript syntax checks completed for the student page loader, lessons script, and rendered quiz script.
- Rendered quiz data contains 26 alphabet questions.

A full live test still requires running the project in XAMPP/Apache with the `kidss_db` database imported and verifying the login/session flow in a browser.
