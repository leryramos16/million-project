Drop a background music file here named `theme.mp3` (title/login screen music).

Suggested free, commercially-usable tracks:
- "Fantasy Medieval Epic Music" by DeusLower — https://pixabay.com/music/search/medieval%20fantasy%20music/
- "Medieval Loop One" by Alexander Nakarada (CC BY 4.0) — https://www.free-stock-music.com/alexander-nakarada-medieval-loop-one.html

The app already plays/loops/mutes `assets/audio/theme.mp3` on the title and login
screens — it just needs the file to exist at that path to start working.

---

Also drop a short (under ~1 second) UI tap sound here named `page_turn.wav`
(tab switches, opening a quest, etc.). A snappy paper/page-flip sound works
best — long sounds will feel laggy since it plays on every tap.

Suggested:
- Pixabay paper rustle SFX (free, no attribution) — https://pixabay.com/sound-effects/search/paper-rustle/
- Mixkit "Page turn single" (~0:01, very snappy) — https://mixkit.co/free-sound-effects/paper/

Same as the music: it just needs to exist at `assets/audio/page_turn.wav` —
no code changes needed once it's there. It respects the same mute toggle as
the music. (.wav is fine — the code plays whatever format the file at that
exact name actually is; if you swap in an .mp3 later, rename it to
`page_turn.wav` regardless, or tell me and I'll update the extension in code.)
