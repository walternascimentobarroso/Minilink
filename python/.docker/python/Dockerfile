# Use official Python image
FROM python:3.13-slim

# Set work directory
WORKDIR /app

# Copy and install dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the rest of the app
COPY . .

# Default command (can be overridden in docker-compose or CLI)
CMD ["python3", "main.py"]
