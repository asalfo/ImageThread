#ImageThread miniproject
====
This miniproject create a simplified forum web app, containing only one thread of
discussion, where each post is an image with a title, a bit like Instagram.
## Requirements
1. Initially only the Top bar and the Reply box are visible.
2. Successfully uploading an image creates a post.
3. Empty title is allowed.
4. The list of posts grows downward with the most recent post at the top.
5. Support the JPEG image format only. Bonus points: Support PNG and (animated) GIF as
well.
6. Image size: upto 1920x1080, upto 2 MB. Bonus points: image size upto 20 MB.
7. #posts increments with each new post. #views increments with each view.
8. Performance: The post must be visible within 2 seconds, and all the images must be
complete loading within 5 seconds on a 10 Mbps Internet connection.
9. Clicking the Export button produces a CSV file with a header row and two columns:
(image) Title and Filename. Bonus points: Make it possible to export the Excel format as
well. Bonus points: clicking the Export button produces a ZIP file with all images and the
CSV file mentioned above.
10. Bonus points: The #posts and #views should update every 15 seconds without reloading
the page.
