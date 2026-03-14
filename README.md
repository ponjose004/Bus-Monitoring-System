# 🚌 Bus Number Detection System

> Automated bus monitoring using YOLOv8 object detection + Tesseract OCR.  
> Detects bus numbers from CCTV footage and logs arrival/departure times to a live web dashboard.

---

## 🔴 Live Dashboard
👉 **[bus-monitoring-system-production.up.railway.app](https://bus-monitoring-system-production.up.railway.app/)**

## ▶️ Run Detection (Anyone Can Run This!)
[![Open In Colab](https://colab.research.google.com/assets/colab-badge.svg)](https://colab.research.google.com/github/ponjose004/Bus-monitoring-system/blob/main/bus_detection_colab.ipynb)

---

## 💡 How It Works

```
Anyone clicks "Open in Colab"
          ↓
Runs the notebook (video auto-downloads from Google Drive)
          ↓
YOLOv8 detects bus number boards frame by frame
          ↓
Virtual line crossing triggers OCR on 5 cropped frames
          ↓
Tesseract OCR reads the number — mode of 5 = confirmed bus
          ↓
Results saved to Railway MySQL database (online)
          ↓
Live dashboard updates automatically — visible to everyone
```

---

## 🧠 Model Development

### 1. Dataset Preparation
- **Source:** Real CCTV footage from a bus depot
- **Video preprocessing:** Raw footage was trimmed to a 9-minute clip (1620s–2160s) using OpenCV to isolate relevant bus activity
- **Frame inspection:** Zoomed-in frame viewer built with OpenCV to manually verify bus number board visibility and quality before annotation
- **Annotation:** Bus number boards were manually labelled using [Roboflow](https://roboflow.com) with bounding boxes around the number display area

### 2. Model Training
- **Architecture:** YOLOv8 — chosen for its real-time detection speed and high accuracy on small objects
- **Framework:** Ultralytics YOLOv8
- **Task:** Object detection — detecting the bus number board region in each video frame
- **Output:** `best.pt` — the best weights checkpoint saved after training

### 3. OCR Pipeline
- **Tool:** Tesseract OCR with English language data
- **Config:** `--psm 9` (single word mode) with whitelist restricted to digits `0-9` only
- **Method:** When a bus crosses the virtual line, 5 consecutive cropped frames of the number board are saved and passed through OCR individually
- **Accuracy fix:** The **mode (most common value)** across 5 OCR readings is taken as the confirmed bus number — this filters out misreads from blurry frames

### 4. Line Crossing Logic
- A **virtual detection line** is drawn across the video at a fixed Y coordinate
- When a detected bounding box straddles the line (`y1 <= LINE_Y <= y2`), the bus is considered to be crossing
- First crossing → logged as **Bus IN** with timestamp
- Second crossing (same bus) → logged as **Bus OUT** with timestamp

### 5. Database Logging
- Each detection is written to MySQL with: bus number, license plate, in-time, in-date
- On departure: out-time and out-date are updated for the matching row
- A license plate lookup dictionary maps each bus number to its registered plate

---

## 🛠️ Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| Object Detection | YOLOv8 (Ultralytics) | Detect bus number board in frame |
| OCR | Tesseract OCR | Read digits from cropped board image |
| Video Processing | OpenCV | Frame extraction, line crossing, drawing |
| Accuracy | Statistics (mode) | Filter noisy OCR readings across 5 frames |
| Database | MySQL (Railway) | Store bus in/out logs online |
| Dashboard | PHP + HTML/CSS | Live web view of detection results |
| Hosting | Railway.app (free) | Cloud MySQL + PHP dashboard |
| Notebook | Google Colab (free) | Run detection without local setup |

---

## 📁 Project Structure

```
bus-monitoring-system/
├── bus_detection_colab.ipynb  ← Open in Colab and run!
├── best.pt                    ← Trained YOLOv8 model
├── Dockerfile                 ← Railway PHP deployment config
├── public/
│   └── index.php              ← Live web dashboard
├── database/
│   └── demo_data.sql          ← Sample data to pre-populate dashboard
└── README.md
```

---

## 🚀 How to Run

### Option 1 — Run on Google Colab (Recommended)
1. Click the **"Open in Colab"** badge above
2. Upload `best.pt` when prompted (Step 4 in notebook)
3. Click **Runtime → Run All**
4. Open the [Live Dashboard](https://bus-monitoring-system-production.up.railway.app/) to see results!

### Option 2 — Run Locally
```bash
pip install ultralytics pytesseract opencv-python mysql-connector-python
python run_detection.py
```

---

## 🌐 Deploy Your Own Dashboard (Railway — Free)

1. Fork this repo
2. Go to [railway.app](https://railway.app) → New Project → Deploy from GitHub
3. Add a MySQL database service
4. Import `database/demo_data.sql` in the Query tab
5. Railway detects `Dockerfile` → PHP deploys automatically
6. Click **Settings → Generate Domain** → get your public URL ✅

---

## 📊 Dashboard Features

- 🟢 Live bus detection log (auto-refreshes every 10 seconds)
- 🔢 Total detections, today's count, currently inside
- 🚌 Bus number, license plate, in/out time per entry
- 🏷️ Status badge — Inside / Departed

---
