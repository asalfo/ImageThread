ImageThread miniproject
====


this miniproject create a simplified forum web app, containing only one thread of
discussion, where each post is an image with a title, a bit like Instagram.
Requirements
● Initially only the Top bar and the Reply box are visible.
● Successfully uploading an image creates a post.
● Empty title is allowed.
● The list of posts grows downward with the most recent post at the top.
● Support the JPEG image format only. Bonus points: Support PNG and (animated) GIF as
well.
● Image size: upto 1920x1080, upto 2 MB. Bonus points: image size upto 20 MB.
● #posts increments with each new post. #views increments with each view.
● Performance: The post must be visible within 2 seconds, and all the images must be
complete loading within 5 seconds on a 10 Mbps Internet connection.

● Clicking the Export button produces a CSV file with a header row and two columns:
(image) Title and Filename. Bonus points: Make it possible to export the Excel format as
well. Bonus points: clicking the Export button produces a ZIP file with all images and the
CSV file mentioned above.
● Bonus points: The #posts and #views should update every 15 seconds without reloading
the page.