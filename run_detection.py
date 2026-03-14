"""
Bus Number Detection Script
Run this on your local PC with your video file.
Results are saved to Railway MySQL (visible on the live dashboard).

Usage:
    pip install ultralytics pytesseract opencv-python mysql-connector-python
    python run_detection.py
"""

import os
import cv2
import datetime
import mysql.connector
from ultralytics import YOLO
from pytesseract import pytesseract
from statistics import mode

# ============================================================
#  CONFIGURATION — Edit these before running
# ============================================================

VIDEO_PATH    = 'last.mp4'         # path to your video file
MODEL_PATH    = 'best.pt'          # path to your YOLO model

# Windows Tesseract path — change if installed elsewhere
TESSERACT_PATH = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
# Linux/Mac path:
# TESSERACT_PATH = '/usr/bin/tesseract'

IMAGES_DIR    = './temp_images/'
THRESHOLD     = 0.5
LINE_Y        = 800                # vertical line position (adjust for your video)

VALID_BUS_NUMBERS = [9, 19, 15, 6, 5, 13, 7, 14]

LICENCE_PLATE_MAP = {
    19: 'TN 84 C35619',
     9: 'TN 84 A55709',
    15: 'TN 84 C75915',
     6: 'TN 84 C85806',
     5: 'TN 84 C35805',
    13: 'TN 84 C35913',
     7: 'TN 84 C25697',
    14: 'TN 84 C15514',
}

# ── Railway MySQL credentials (from Railway → Variables tab) ──
DB_HOST     = 'your-railway-host'       # e.g. monorail.proxy.rlwy.net
DB_PORT     = 12345                     # e.g. 12345
DB_USER     = 'root'
DB_PASSWORD = 'your-railway-password'
DB_NAME     = 'railway'

# ============================================================

pytesseract.tesseract_cmd = TESSERACT_PATH
os.makedirs(IMAGES_DIR, exist_ok=True)

# ── Connect to Railway MySQL ─────────────────────────────────
print('Connecting to Railway database...')
db = mysql.connector.connect(
    host=DB_HOST, port=DB_PORT,
    user=DB_USER, password=DB_PASSWORD,
    database=DB_NAME
)
cursor = db.cursor()
print('✅ Database connected')

# ── Load video and model ─────────────────────────────────────
cap = cv2.VideoCapture(VIDEO_PATH)
if not cap.isOpened():
    raise FileNotFoundError(f'Cannot open: {VIDEO_PATH}')

ret, frame = cap.read()
H, W, _ = frame.shape
print(f'📹 Video: {W}x{H} | {int(cap.get(cv2.CAP_PROP_FRAME_COUNT))} frames')

model = YOLO(MODEL_PATH)
print('✅ YOLO model loaded\n🚌 Starting detection...\n')

# ── State ────────────────────────────────────────────────────
l = []
saved_paths = []
detected_in  = set()
detected_out = set()
last_detected = 0
frame_num = 0

LINE_COORDINATES = [(0, LINE_Y), (W, LINE_Y)]

# ── Main Loop ────────────────────────────────────────────────
while ret:
    frame_num += 1
    results = model(frame, verbose=False)[0]

    for result in results.boxes.data.tolist():
        x1, y1, x2, y2, score, class_id = result
        if score > THRESHOLD and y1 <= LINE_Y <= y2:
            cv2.rectangle(frame, (int(x1), int(y1)), (int(x2), int(y2)), (0, 255, 0), 3)
            if len(saved_paths) < 5:
                crop = frame[int(y1):int(y2), int(x1):int(x2)]
                path = os.path.join(IMAGES_DIR, f'crop_{len(saved_paths)}.jpg')
                cv2.imwrite(path, crop)
                saved_paths.append(path)

    if len(saved_paths) == 5:
        for p in saved_paths:
            img  = cv2.imread(p)
            text = pytesseract.image_to_string(
                img, config='-l eng --psm 9 -c tessedit_char_whitelist=1234567890'
            )
            nums = [int(n) for n in text.split() if n.isdigit() and int(n) in VALID_BUS_NUMBERS]
            l.extend(nums)

        # Cleanup temp images
        saved_paths.clear()
        for f in os.scandir(IMAGES_DIR):
            if f.is_file(): os.remove(f.path)

        if l:
            bus_num   = mode(l)
            now       = datetime.datetime.now()
            today     = datetime.date.today()
            plate     = LICENCE_PLATE_MAP.get(bus_num, 'Unknown')

            # Bus arriving
            if bus_num not in detected_in and bus_num not in detected_out:
                print(f'  ✅ IN  | Bus #{bus_num} | {plate} | {now.strftime("%H:%M:%S")}')
                cursor.execute(
                    "INSERT INTO bus_number_detection (bus_number, licence_plate_number, In_time, In_date) VALUES (%s, %s, %s, %s)",
                    (bus_num, plate, now, today)
                )
                db.commit()
                detected_in.add(bus_num)
                last_detected = bus_num

            # Bus departing
            elif bus_num in detected_in and last_detected != bus_num and bus_num not in detected_out:
                print(f'  🚌 OUT | Bus #{bus_num} | {now.strftime("%H:%M:%S")}')
                cursor.execute(
                    "UPDATE bus_number_detection SET Out_time=%s, Out_Date=%s WHERE bus_number=%s AND Out_time IS NULL",
                    (now, today, bus_num)
                )
                db.commit()
                detected_out.add(bus_num)
                detected_in.discard(bus_num)
        l.clear()

    cv2.line(frame, LINE_COORDINATES[0], LINE_COORDINATES[1], (0, 0, 255), 2)
    ret, frame = cap.read()

# ── Cleanup ──────────────────────────────────────────────────
cap.release()
cursor.close()
db.close()
print(f'\n✅ Done! Processed {frame_num} frames.')
print(f'   Check your dashboard to see the results.')
