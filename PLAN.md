My goal for this app is a tool where I can paste in my blog posts, and then automatically create 10 tweets based on the content of the blog post.

I'll need:
- [x] A user account to log in through — implemented registration/login views with session auth and logout.
- [x] A way to paste in blog posts and have them saved — posts can be created, edited, archived, restored, or deleted; content is stored in the database.
- [x] A button to generate tweets from the blog post using Gemini Flash — added Gemini Flash integration via the REST API; requires `GEMINI_API_KEY`.
- [x] A way to mark which of those tweets have been posted so I don't post them again — added “Mark posted” action that timestamps the tweet and hides it from the pending count.
- [x] A way to discard tweets that I don't like — discard and restore actions update status and timestamps.
- [x] A way to scroll through the posts that I've uploaded and see how many tweets they have that I haven't used yet — dashboard shows paginated cards with pending/posted/discarded counts and search.
- [x] An easy way to tweet the tweets without using the Twitter (X) API — “Tweet it” button opens X compose intent in a new tab.
- [x] A nice user interface around it that doesn't look too boring — Tailwind-based dark UI, responsive layout, and interactive tweet cards.
- [x] Delete or Archive blog posts that I've uploaded — archive/restore controls plus permanent delete.
- [x] Search through the posts I've uploaded — search bar filters by title or body, with optional archived toggle.
- [x] Regenerate tweets for a post — regenerate form replaces pending/discarded tweets with a fresh batch.
- [x] Edit generated tweets before posting them — inline editors with validation and 280-character cap.
- [x] Copy button for tweets — copy button uses clipboard API with visual feedback.
- [x] Limit the tweets to the 280-character count — generator trims responses and validation enforces length.

Technical details
- [x] Simple login with email/password — bcrypt-backed user accounts with validation.
- [x] This will be a web app but should have mobile-optimized design — responsive Tailwind layouts tested down to mobile breakpoints.
- [x] No hashtags or mentions in the tweets — prompt and post-processing strip tweets containing hashtags or mentions.