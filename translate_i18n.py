#!/usr/bin/env python3
"""
Translate the English i18n strings for php-autoindexed-file-viewer into multiple languages
using the OpenAI API. Outputs a valid PHP snippet to i18n_output.php.

Requires: OPENAI_API_KEY environment variable.
Usage:    python3 translate_i18n.py
"""

import os, json, sys, re, time
from openai import OpenAI

# Supports OPENAI_API_KEY and optionally OPENAI_BASE_URL for compatible providers
client = OpenAI(
    base_url=os.environ.get("OPENAI_BASE_URL", "http://192.168.1.114:1234/v1"),
    api_key=os.environ.get("OPENAI_API_KEY", "lm-studio"),
)
MODEL = os.environ.get("OPENAI_MODEL", "local-model")

# ── English source strings (translatable keys only) ──────────────────────────
EN_STRINGS = {
    "title": "PHP Autoindexed File-Viewer",
    "home": "Home",
    "download_folder": "⤓ Download folder",
    "toggle_theme": "Toggle light/dark mode",
    "search_placeholder": "Search by name…",
    "all_types": "All types",
    "type_folder": "Folders",
    "type_image": "Images",
    "type_audio": "Audio",
    "type_video": "Video",
    "type_document": "Documents",
    "type_archive": "Archives",
    "type_other": "Other",
    "recursive": "Recursive",
    "col_name": "Name",
    "col_size": "Size",
    "col_info": "Info",
    "col_modified": "Modified",
    "col_actions": "Actions",
    "parent_folder": "Parent folder",
    "folder_empty": "This folder is empty.",
    "view": "View",
    "download": "Download",
    "open": "Open",
    "zip": "Zip",
    "loading": "Loading…",
    "error_loading": "Error loading info",
    "no_info": "No info available",
    "config_error": "⚠️ Configuration Error",
    "cookie_text": "This site uses essential cookies to save your preferences (theme, sort order, language). No tracking or third-party cookies.",
    "cookie_ok": "OK",
    "viewer_download": "⤓ Download",
    "viewer_newtab": "↗ New Tab",
    "viewer_close": "Close (Esc)",
    "viewer_prev": "Previous",
    "viewer_next": "Next",
    "file_too_large": 'File too large for text preview. Use "New Tab" or download.',
    "could_not_load": "Could not load file.",
    "queue_processing": "{running} processing, {pending} queued",
    "queue_no_tasks": "No active tasks",
    "file_singular": "{n} file",
    "file_plural": "{n} files",
    "folder_singular": "{n} folder",
    "folder_plural": "{n} folders",
    "empty": "empty",
    "background_tasks": "Background tasks",
    "info_format": "Format",
    "info_pages": "Pages",
    "info_title": "Title",
    "info_author": "Author",
    "info_language": "Language",
    "info_creator": "Creator",
    "info_producer": "Producer",
    "info_pdf_version": "PDF version",
    "info_mime": "MIME",
    "info_encrypted": "Encrypted",
    "info_page_size": "Page size",
    "info_created": "Created",
    "info_resolution": "Resolution",
    "info_type": "Type",
    "info_vector": "Vector graphic",
    "info_megapixels": "Megapixels",
    "info_color": "Color",
    "info_alpha": "Alpha",
    "info_animated": "Animated",
    "info_yes": "Yes",
    "info_duration": "Duration",
    "info_container": "Container",
    "info_overall_bitrate": "Overall bitrate",
    "info_video": "Video",
    "info_audio": "Audio",
    "info_bitrate": "Bitrate",
    "info_fps": "FPS",
    "info_bit_depth": "Bit depth",
    "info_sample_rate": "Sample rate",
    "info_channels": "Channels",
}

# ── Language definitions ─────────────────────────────────────────────────────
# code -> (English name, flag emoji, native name hint, PHP dateFormat, JS dateFormatJS)
LANGUAGES = {
    "af":    ("Afrikaans",              "🇿🇦", "Afrikaans",       "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "al":    ("Albanian",               "🇦🇱", "Shqip",           "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "ar":    ("Arabic",                 "🇸🇦", "العربية",         "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "bg":    ("Bulgarian",              "🇧🇬", "Български",       "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "bn":    ("Bengali",                "🇧🇩", "বাংলা",           "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "ca":    ("Catalan",                "🇪🇸", "Català",          "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "cy":    ("Welsh",                  "🇬🇧", "Cymraeg",         "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "cz":    ("Czech",                  "🇨🇿", "Čeština",         "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "da":    ("Danish",                 "🇩🇰", "Dansk",           "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "de":    ("German",                 "🇩🇪", "Deutsch",         "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "ee":    ("Estonian",               "🇪🇪", "Eesti",           "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "eu":    ("Basque",                 "🇪🇸", "Euskera",         "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "en":    ("English",                "🇬🇧", "English",         "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "eo":    ("Esperanto",              "🟢",  "Esperanto",       "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "es":    ("Spanish",                "🇪🇸", "Español",         "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "fi":    ("Finnish",                "🇫🇮", "Suomi",           "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "fr":    ("French",                 "🇫🇷", "Français",        "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "gr":    ("Greek",                  "🇬🇷", "Ελληνικά",        "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "gu":    ("Gujarati",               "🇮🇳", "ગુજરાતી",         "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "hi":    ("Hindi",                  "🇮🇳", "हिन्दी",          "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "he":    ("Hebrew",                 "🇮🇱", "עברית",           "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "hu":    ("Hungarian",              "🇭🇺", "Magyar",          "Y.m.d H:i",       "YYYY.MM.DD HH:mm"),
    "ga":    ("Irish",                  "🇮🇪", "Gaeilge",         "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "is":    ("Icelandic",             "🇮🇸", "Íslenska",        "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "id":    ("Indonesian",             "🇮🇩", "Bahasa Indonesia", "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "it":    ("Italian",                "🇮🇹", "Italiano",        "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "jp":    ("Japanese",               "🇯🇵", "日本語",           "Y/m/d H:i",       "YYYY/MM/DD HH:mm"),
    "ko":    ("Korean",                 "🇰🇷", "한국어",           "Y.m.d H:i",       "YYYY.MM.DD HH:mm"),
    "ku":    ("Kurdish",                "🇮🇶", "کوردی",           "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "lt":    ("Lithuanian",             "🇱🇹", "Lietuvių",        "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "la":    ("Latin",                  "🏛️",  "Latina",          "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "lv":    ("Latvian",                "🇱🇻", "Latviešu",        "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "ms":    ("Malay",                  "🇲🇾", "Bahasa Melayu",   "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "mr":    ("Marathi",                "🇮🇳", "मराठी",           "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "nl":    ("Dutch",                  "🇳🇱", "Nederlands",      "d-m-Y H:i",       "DD-MM-YYYY HH:mm"),
    "no":    ("Norwegian",              "🇳🇴", "Norsk",           "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "pl":    ("Polish",                 "🇵🇱", "Polski",          "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "pt_BR": ("Brazilian Portuguese",   "🇧🇷", "Português (BR)",  "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "ro_RO": ("Romanian",              "🇷🇴", "Română",          "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "ru":    ("Russian",                "🇷🇺", "Русский",         "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "sk":    ("Slovak",                 "🇸🇰", "Slovenčina",      "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "sr_LA": ("Serbian (Latin)",        "🇷🇸", "Srpski (lat.)",   "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "sr_CY": ("Serbian (Cyrillic)",     "🇷🇸", "Српски (ћир.)",   "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "sv":    ("Swedish",                "🇸🇪", "Svenska",         "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "sw":    ("Swahili",                "🇰🇪", "Kiswahili",       "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "ta":    ("Tamil",                  "🇮🇳", "தமிழ்",           "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "te":    ("Telugu",                 "🇮🇳", "తెలుగు",          "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "tl":    ("Tagalog",                "🇵🇭", "Tagalog",         "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "th":    ("Thai",                   "🇹🇭", "ไทย",             "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "tr":    ("Turkish",                "🇹🇷", "Türkçe",          "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "uk":    ("Ukrainian",              "🇺🇦", "Українська",      "d.m.Y H:i",       "DD.MM.YYYY HH:mm"),
    "ur":    ("Urdu",                   "🇵🇰", "اردو",            "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "vi":    ("Vietnamese",             "🇻🇳", "Tiếng Việt",      "d/m/Y H:i",       "DD/MM/YYYY HH:mm"),
    "zh_CN": ("Chinese",               "🇨🇳", "简体中文",          "Y-m-d H:i",       "YYYY-MM-DD HH:mm"),
    "zh_TW": ("Chinese Traditional",   "🇹🇼", "繁體中文",          "Y/m/d H:i",       "YYYY/MM/DD HH:mm"),
}

SYSTEM_PROMPT = """\
You are a professional UI translator for a web-based file browser application.
You will receive a JSON object of English UI strings. Translate ALL values into {lang_name} ({native_name}).

Rules:
- Return ONLY a valid JSON object with the exact same keys — no extra text, no markdown fences.
- Preserve placeholders exactly: {{n}}, {{running}}, {{pending}}, ⤓, ↗, ⚠️, (Esc), "…" etc.
- Keep the key "title" value as "PHP Autoindexed File-Viewer" — do NOT translate the product name.
- Keep technical terms (ZIP, OK) unless there is a well-known native equivalent.
- Strings must be concise — they appear in buttons and table headers.
- Use the native script of the target language."""


def strip_thinking(text: str) -> str:
    """Remove <think>...</think> blocks from thinking-model output."""
    return re.sub(r'<think>.*?</think>\s*', '', text, flags=re.DOTALL).strip()


def translate(lang_code: str) -> dict:
    """Call OpenAI to translate EN_STRINGS into the given language. Returns dict."""
    lang_name, _, native_name, _, _ = LANGUAGES[lang_code]
    prompt = SYSTEM_PROMPT.format(lang_name=lang_name, native_name=native_name)

    resp = client.chat.completions.create(
        model=MODEL,
        temperature=0,
        messages=[
            {"role": "system", "content": prompt},
            {"role": "user", "content": json.dumps(EN_STRINGS, ensure_ascii=False)},
        ],
    )
    text = resp.choices[0].message.content.strip()
    # Strip thinking-model reasoning blocks
    text = strip_thinking(text)
    # Strip markdown code fences if present
    if text.startswith("```"):
        text = text.split("\n", 1)[1]
        if text.endswith("```"):
            text = text[: text.rfind("```")]
    return json.loads(text)


def php_string(s: str) -> str:
    """Escape a string for single-quoted PHP."""
    return s.replace("\\", "\\\\").replace("'", "\\'")


def build_php(all_langs: dict) -> str:
    """Build the $I18N = [ ... ]; PHP snippet."""
    lines = ["$I18N = ["]

    for code in all_langs:
        lang_name, flag, native_name, date_fmt, date_fmt_js = LANGUAGES[code]
        strings = all_langs[code]

        lines.append(f"    '{code}' => [")
        lines.append(f"        '_flag' => '{flag}',")
        lines.append(f"        '_name' => '{php_string(native_name)}',")
        lines.append(f"        '_dateFormat' => '{php_string(date_fmt)}',")
        lines.append(f"        '_dateFormatJS' => '{php_string(date_fmt_js)}',")

        for key, val in strings.items():
            lines.append(f"        '{key}' => '{php_string(val)}',")

        lines.append("    ],")

    lines.append("];")
    return "\n".join(lines) + "\n"


def progress_bar(current: int, total: int, start_time: float, width: int = 40) -> str:
    """Generate a progress bar with ETA."""
    elapsed = time.time() - start_time
    percent = current / total
    filled = int(width * percent)
    bar = "█" * filled + "░" * (width - filled)
    
    # ETA calculation
    if elapsed > 0 and current > 0:
        rate = current / elapsed
        remaining = total - current
        eta_sec = remaining / rate
        eta_str = f"{int(eta_sec)}s"
        if eta_sec > 60:
            eta_str = f"{int(eta_sec // 60)}m {int(eta_sec % 60)}s"
    else:
        eta_str = "—"
    
    return f"[{bar}] {current}/{total} ({percent*100:.0f}%) ETA: {eta_str}"


def main():
    out_path = os.path.join(os.path.dirname(__file__), "i18n_output.php")
    all_langs: dict[str, dict] = {}

    # English first — no API call needed
    all_langs["en"] = dict(EN_STRINGS)

    codes_to_translate = [c for c in LANGUAGES if c != "en"]
    total = len(codes_to_translate)
    start_time = time.time()

    for i, code in enumerate(codes_to_translate, 1):
        lang_name = LANGUAGES[code][0]
        # Print progress bar with ETA
        status = progress_bar(i - 1, total, start_time)
        print(f"\r{status} | {code} ({lang_name})", end="", flush=True)
        
        try:
            translated = translate(code)
            # Ensure every key is present (fall back to English if GPT dropped one)
            for k in EN_STRINGS:
                if k not in translated:
                    translated[k] = EN_STRINGS[k]
            all_langs[code] = translated
        except Exception as e:
            print(f"\n✗ {code}: {type(e).__name__}: {e}")
            all_langs[code] = dict(EN_STRINGS)  # fallback to English

    # Final progress bar
    status = progress_bar(total, total, start_time)
    print(f"\r{status}", end="")
    print()  # newline
    
    php = build_php(all_langs)
    with open(out_path, "w", encoding="utf-8") as f:
        f.write(php)
    
    elapsed = time.time() - start_time
    elapsed_str = f"{int(elapsed // 60)}m {int(elapsed % 60)}s" if elapsed > 60 else f"{int(elapsed)}s"
    print(f"\nDone! {total} languages translated in {elapsed_str}.")
    print(f"Output written to {out_path}")
    
    # Validation: check for missing translations
    print("\n── Translation Completeness ──")
    missing_by_lang = {}
    for code, strings in all_langs.items():
        missing = [k for k in EN_STRINGS if k not in strings]
        if missing:
            missing_by_lang[code] = missing
    
    if missing_by_lang:
        print(f"⚠️  {len(missing_by_lang)} language(s) have missing translations:")
        for code in sorted(missing_by_lang.keys()):
            lang_name = LANGUAGES[code][0]
            missing = missing_by_lang[code]
            print(f"   {code} ({lang_name}): missing {len(missing)} key(s) — {', '.join(missing)}")
    else:
        print("✓ All languages have complete translations!")


if __name__ == "__main__":
    main()
