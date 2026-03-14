# 🚌 Bus Number Detection System

> Automated bus monitoring using YOLOv8 object detection + Tesseract OCR.  
> Detects bus numbers from CCTV footage and logs arrival/departure times to a live web dashboard.

---

## 🔴 Live Dashboard
👉 **[bus-monitor.up.railway.app](https://bus-monitor.up.railway.app)**
*(replace with your actual Railway URL after deploying)*

## ▶️ Run Detection (Anyone Can Run This!)
[![Open In Colab](https://colab.research.google.com/assets/colab-badge.svg)](https://colab.research.google.com/github/YOUR_GITHUB_USERNAME/bus-monitoring-system/blob/main/bus_detection_colab.ipynb)

*(replace YOUR_GITHUB_USERNAME with your actual GitHub username)*

---

## 💡 How It Works

```
Anyone clicks "Open in Colab"
          ↓
Runs the notebook (video auto-downloads from Google Drive)
          ↓
YOLOv8 detects bus number boards frame by frame
          ↓
Tesseract OCR reads the bus number from cropped frames
          ↓
Results saved to Railway MySQL database (online)
          ↓
Live dashboard updates automatically — visible to everyone
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Object Detection | YOLOv8 (Ultralytics) |
| OCR | Tesseract |
| Video Processing | OpenCV |
| Database | MySQL (Railway) |
| Dashboard | PHP + HTML/CSS |
| Hosting | Railway.app (free) |
| Notebook | Google Colab (free) |

---

## 📁 Project Structure

```
bus-monitoring-system/
├── bus_detection_colab.ipynb  ← Open in Colab and run!
├── best.pt                    ← Trained YOLOv8 model
├── nixpacks.toml              ← Railway PHP deployment config
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
2. Upload `best.pt` when prompted
3. Click **Runtime → Run All**
4. Open the [Live Dashboard](https://bus-monitor.up.railway.app) to see results!

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
5. Railway auto-detects `nixpacks.toml` → PHP deploys automatically
6. Click **Settings → Generate Domain** → get your public URL ✅

---

## 📊 Dashboard Features

- 🟢 Live bus detection log (auto-refreshes every 10 seconds)
- 🔢 Total detections, today's count, currently inside
- 🚌 Bus number, license plate, in/out time per entry
- 🏷️ Status badge — Inside / Departed

---

## 🗺️ Bus Number → License Plate Map

| Bus | License Plate |
|-----|--------------|
| 5   | TN 84 C35805 |
| 6   | TN 84 C85806 |
| 7   | TN 84 C25697 |
| 9   | TN 84 A55709 |
| 13  | TN 84 C35913 |
| 14  | TN 84 C15514 |
| 15  | TN 84 C75915 |
| 19  | TN 84 C35619 |
