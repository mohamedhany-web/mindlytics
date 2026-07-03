FFmpeg bundled for WhatsApp Voice Notes (Linux amd64)
=====================================================

Upload folder bin/ffmpeg/ with your project deploy.

On the server (SSH), once after upload:

  chmod +x bin/ffmpeg/linux-amd64/ffmpeg

Optional .env override (usually not needed if file is in place):

  FFMPEG_PATH=/home/USER/domains/.../Mindlytics/bin/ffmpeg/linux-amd64/ffmpeg

Then:

  php artisan config:clear

File: bin/ffmpeg/linux-amd64/ffmpeg (~76 MB, static build for Linux x64)
