"""
Dataset preparation pipeline for the Noor-Store catalogue.

Source: Makeup API dataset (http://makeup-api.herokuapp.com), 931 products,
stored in database/data/raw/makeup_products.csv.

Steps
  1. Clean text (HTML tags/entities, whitespace, casing of brand names).
  2. Normalise prices to USD (GBP/CAD converted, missing currency = USD).
  3. Parse tag lists and colour swatches, validate hex codes.
  4. Remove duplicates (same brand + name).
  5. Download the product image (api_featured_image, fallback image_link),
     pad it to a square white canvas and store it as an optimised WebP
     in public/uploads/products/{source_id}.webp.
  6. Export database/data/products.json, consumed by CatalogSeeder.

Usage:  python database/data/prepare_dataset.py [--skip-images]
"""

import ast
import concurrent.futures as cf
import csv
import html
import io
import json
import re
import ssl
import sys
import urllib.request
from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parents[2]
RAW_CSV = ROOT / "database" / "data" / "raw" / "makeup_products.csv"
OUT_JSON = ROOT / "database" / "data" / "products.json"
IMG_DIR = ROOT / "public" / "uploads" / "products"
IMG_SIZE = 520

FX_TO_USD = {"": 1.0, "USD": 1.0, "GBP": 1.27, "CAD": 0.73}

BRAND_NAMES = {
    "nyx": "NYX", "e.l.f.": "e.l.f.", "l'oreal": "L'Oreal", "covergirl": "CoverGirl",
    "wet n wild": "Wet n Wild", "iman": "IMAN", "dr. hauschka": "Dr. Hauschka",
    "colourpop": "ColourPop", "c'est moi": "C'est Moi", "w3llpeople": "W3LL People",
    "deciem": "DECIEM", "sally b's skin yummies": "Sally B's Skin Yummies",
    "burt's bees": "Burt's Bees", "maia's mineral galaxy": "Maia's Mineral Galaxy",
    "lotus cosmetics usa": "Lotus Cosmetics USA", "unknown": "Noor Essentials",
    "zorah": "Zorah Biocosmetiques",
}

TYPE_LABELS = {
    "lipstick": "Lipstick", "lip_liner": "Lip Liner", "mascara": "Mascara",
    "eyeliner": "Eyeliner", "eyeshadow": "Eyeshadow", "eyebrow": "Eyebrow",
    "foundation": "Foundation", "blush": "Blush", "bronzer": "Bronzer",
    "nail_polish": "Nail Polish",
}

HEX_RE = re.compile(r"^#(?:[0-9a-fA-F]{3}){1,2}$")
TAG_RE = re.compile(r"<[^>]+>")
WS_RE = re.compile(r"\s+")


def clean_text(value: str) -> str:
    value = html.unescape(value or "")
    value = TAG_RE.sub(" ", value)
    value = value.replace("\u00a0", " ")
    for invisible in ("\ufffd", "\u200b", "\ufeff", "\u00ad"):
        value = value.replace(invisible, "")
    return WS_RE.sub(" ", value).strip()


def brand_name(raw: str) -> str:
    key = raw.strip().lower()
    return BRAND_NAMES.get(key, " ".join(w.capitalize() for w in key.split()))


def parse_literal(value: str, default):
    try:
        parsed = ast.literal_eval(value) if value else default
        return parsed if isinstance(parsed, type(default)) else default
    except (ValueError, SyntaxError):
        return default


def parse_colors(value: str):
    colors = []
    for c in parse_literal(value, []):
        if not isinstance(c, dict):
            continue
        hex_value = str(c.get("hex_value", "")).split(",")[0].strip()
        if not hex_value.startswith("#"):
            hex_value = "#" + hex_value
        if HEX_RE.match(hex_value):
            colors.append({"hex": hex_value.upper(), "name": clean_text(str(c.get("colour_name") or ""))})
    return colors[:40]


def fallback_description(brand: str, name: str, ptype: str) -> str:
    label = TYPE_LABELS.get(ptype, ptype).lower()
    return (f"{name} by {brand} is a {label} designed for comfortable everyday wear, "
            f"with rich colour payoff and a finish that lasts.")


def download_image(source_id: int, urls) -> str | None:
    target = IMG_DIR / f"{source_id}.webp"
    if target.exists() and target.stat().st_size > 0:
        return f"uploads/products/{target.name}"
    ctx = ssl.create_default_context()
    for url in urls:
        if not url:
            continue
        if url.startswith("//"):
            url = "https:" + url
        try:
            req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
            with urllib.request.urlopen(req, timeout=20, context=ctx) as resp:
                data = resp.read()
            img = Image.open(io.BytesIO(data))
            img = img.convert("RGBA")
            img.thumbnail((IMG_SIZE - 40, IMG_SIZE - 40), Image.LANCZOS)
            canvas = Image.new("RGBA", (IMG_SIZE, IMG_SIZE), (255, 255, 255, 255))
            canvas.alpha_composite(img, ((IMG_SIZE - img.width) // 2, (IMG_SIZE - img.height) // 2))
            canvas.convert("RGB").save(target, "WEBP", quality=80, method=6)
            return f"uploads/products/{target.name}"
        except Exception:
            continue
    return None


def main(skip_images: bool) -> None:
    IMG_DIR.mkdir(parents=True, exist_ok=True)
    with RAW_CSV.open(encoding="utf-8") as fh:
        rows = list(csv.DictReader(fh))

    products, seen = [], set()
    for r in rows:
        brand = brand_name(r["brand"])
        name = clean_text(r["name"])
        key = (brand.lower(), name.lower())
        if not name or key in seen:
            continue
        seen.add(key)

        ptype = r["product_type"].strip()
        currency = r["currency"].strip().upper()
        price = round(float(r["price"] or 0) * FX_TO_USD.get(currency, 1.0), 2)
        description = clean_text(r["description"])
        if len(description) < 15:
            description = fallback_description(brand, name, ptype)

        rating = float(r["rating"]) if r["rating"] else None
        products.append({
            "source_id": int(r["id"]),
            "brand": brand,
            "name": name,
            "product_type": ptype,
            "form": r["category"].strip() or None,
            "price": max(price, 1.0),
            "original_price": float(r["price"] or 0),
            "original_currency": currency or "USD",
            "price_bucket": r["price_bucket"],
            "source_rating": rating if rating and 1 <= rating <= 5 else None,
            "tags": [t.strip().lower() for t in parse_literal(r["tag_list"], []) if str(t).strip()],
            "colors": parse_colors(r["product_colors"]),
            "description": description,
            "image_sources": [r["api_featured_image"], r["image_link"]],
            "product_link": r["product_link"],
        })

    if not skip_images:
        with cf.ThreadPoolExecutor(24) as pool:
            futures = {pool.submit(download_image, p["source_id"], p["image_sources"]): p for p in products}
            for i, fut in enumerate(cf.as_completed(futures), 1):
                futures[fut]["image"] = fut.result()
                if i % 100 == 0:
                    print(f"  images: {i}/{len(products)}")
    for p in products:
        p.setdefault("image", f"uploads/products/{p['source_id']}.webp"
                     if (IMG_DIR / f"{p['source_id']}.webp").exists() else None)
        p.pop("image_sources")

    OUT_JSON.write_text(json.dumps(products, ensure_ascii=False, indent=1), encoding="utf-8")
    missing = sum(1 for p in products if not p["image"])
    print(f"products: {len(products)} (from {len(rows)} rows), missing images: {missing}")
    print(f"written: {OUT_JSON.relative_to(ROOT)}")


if __name__ == "__main__":
    main("--skip-images" in sys.argv)
