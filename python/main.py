"""Simple URL shortener using FastAPI."""

import random
import string
from typing import Dict, Optional

from fastapi import FastAPI, HTTPException
from fastapi.responses import RedirectResponse
from pydantic import BaseModel

app = FastAPI()

url_mapping: Dict[str, str] = {}


def generate_short_code(length: int = 6) -> str:
    """Generate a random alphanumeric short code."""
    chars = string.ascii_letters + string.digits
    return "".join(random.choices(chars, k=length))


class UrlRequest(BaseModel):
    """Model for incoming URL shortening request."""

    url: str


@app.post("/shorten")
async def shorten_url(data: UrlRequest) -> Dict[str, str]:
    """Generate short URL and store mapping."""
    short_code: str = generate_short_code()
    while short_code in url_mapping:
        short_code = generate_short_code()
    url_mapping[short_code] = data.url
    return {"short_url": short_code}


@app.get("/{short_code}")
async def redirect_short_url(short_code: str) -> RedirectResponse:
    """Redirect to original URL if short code exists."""
    original_url: Optional[str] = url_mapping.get(short_code)
    if original_url:
        return RedirectResponse(original_url)
    raise HTTPException(status_code=404, detail="URL not found")
