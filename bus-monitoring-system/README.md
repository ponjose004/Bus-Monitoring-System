# 🚌 Bus Number Detection System

An automated system that detects bus numbers from CCTV footage using **YOLOv8** object detection and **Tesseract OCR**.

## How It Works

1. YOLO model detects the bus number board in each video frame
2. When a bus crosses a virtual line, frames are cropped and saved
3. Tesseract OCR reads the number from 5 cropped frames
4. The most common number (mode) is taken as the confirmed bus number
5. In/Out times and license plate info are logged to MySQL

## Project Structure

```
bus-monitoring-system/
├── bus_detection.ipynb     ← Main notebook (run on Google Colab)
├── best.pt                 ← YOLOv8 trained model
├── dashboard/
│   ├── index.php           ← Web dashboard (PHP + MySQL)
│   └── style.css
├── database/
│   └── bus.sql             ← MySQL schema + sample data
└── README.md
```

## ▶️ Running on Google Colab

### 1. Upload the test video to Google Drive

- Go to [drive.google.com](https://drive.google.com)
- Upload `last.mp4` (the test video)
- Right-click → **Share** → **Anyone with the link**
- Copy the **File ID** from the share URL:
  - URL: `https://drive.google.com/file/d/`**`1aBcXYZ...`**`/view`

> **Test Video:** *(paste your Google Drive share link here after uploading)*

### 2. Open the notebook in Colab

[![Open In Colab](https://colab.research.google.com/assets/colab-badge.svg)](https://colab.research.google.com/github/YOUR_USERNAME/bus-monitoring-system/blob/main/bus_detection.ipynb)

*(Replace `YOUR_USERNAME` with your GitHub username after uploading)*

### 3. Configure and run

In the **Configuration** cell, set:
```python
GOOGLE_DRIVE_FILE_ID = 'paste-your-file-id-here'
```

Then **Run All** cells (Runtime → Run All).

## 🖥️ Dashboard Setup (Local)

Requires: PHP, MySQL, Apache/XAMPP

1. Import `database/bus.sql` into MySQL
2. Copy `dashboard/` folder into your web server root (e.g. `htdocs/`)
3. Open `http://localhost/dashboard/`

## Bus Number → License Plate Map

| Bus Number | License Plate  |
|------------|----------------|
| 5          | TN 48 C38833   |
| 6          | TN 84 C95613   |
| 7          | TN 98 C46863   |
| 9          | TN 84 A18679   |
| 13         | TN 01 C52364   |
| 14         | TN 11 C75612   |
| 15         | TN 72 C85236   |
| 19         | TN 76 C12568   |

## Dependencies

- [Ultralytics YOLOv8](https://github.com/ultralytics/ultralytics)
- [Tesseract OCR](https://github.com/tesseract-ocr/tesseract)
- OpenCV
- MySQL Connector (Python)
- gdown
