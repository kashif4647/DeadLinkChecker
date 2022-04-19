# DeadLinkChecker
Requirement:
Create a WordPress Plugin to filter WordPress content for removing "Youtube Dead Link" from the post on real time using filter or actions

Features:
- Build a list of dead links (show it to admin panel, where plugin having own page)
- Remove those link real time without removing them from WordPress editor.
- Make sure you are using WordPress cache for testing the url existing or not on youtube.com

Example:
1. https://youtu.be/1ZeaN7XJbYg // Private video (filter and add it to plugin page, mark as
dead)
2. https://www.youtube.com/watch?v=1ZeaN7XJxv // Not Found (filter and add it to plugin
page, mark as dead)
3. https://www.youtube.com/watch?v=RtQUZDMf3vU // Working (No action required)