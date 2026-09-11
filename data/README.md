# data/

Drop the raw material for the new project here before typing **"build"**.
This folder is input for Claude, not something the running app reads.

Put things like:

- Copy / content: an about-us doc, feature list, FAQ, pricing tiers
- Brand: logo files, brand colors, fonts, a style reference screenshot
- Structured data: a CSV/JSON of products, team members, locations, etc.
- Reference: a competitor site link, a Figma export, existing marketing copy
- Any spec details that didn't fit neatly into `PROJECT.md`

There's no required structure or naming convention — organize it however
makes sense for the project (e.g. `data/copy.md`, `data/logo.svg`,
`data/products.csv`). Claude reads everything in this folder as part of the
build step described in `AGENT.md`.
