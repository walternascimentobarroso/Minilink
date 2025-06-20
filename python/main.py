from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from fastapi.responses import RedirectResponse
import string
import random

app = FastAPI()
url_mapping = {}


def generate_short_code(length=6):
    chars = string.ascii_letters + string.digits
    return "".join(random.choices(chars, k=length))


class UrlRequest(BaseModel):
    url: str


@app.post("/shorten")
async def shorten_url(data: UrlRequest):
    short_code = generate_short_code()
    while short_code in url_mapping:
        short_code = generate_short_code()
    url_mapping[short_code] = data.url
    return {"short_url": short_code}


@app.get("/{short_code}")
async def redirect_short_url(short_code: str):
    original_url = url_mapping.get(short_code)
    if original_url:
        return RedirectResponse(original_url)
    else:
        raise HTTPException(status_code=404, detail="URL not found")
