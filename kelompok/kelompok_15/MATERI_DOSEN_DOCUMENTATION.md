# 📚 Integrasi Upload Materi Dosen - Dokumentasi Lengkap

## ✅ Status: Implementation Complete

**Date**: December 2024  
**Status**: ✅ Complete & Ready for Testing  
**Features Implemented**: 6/6  
**Security Features**: 5/5  

---

## 🎯 Features Implemented

### 1. Upload PDF dengan Progress Indicator ✅
- **File**: `upload-materi.php`
- **Features**:
  - Real-time upload progress tracking
  - MIME type validation (application/pdf)
  - File extension validation (.pdf only)
  - Magic byte validation (%PDF header)
  - Max file size: 10MB
  - Unique filename generation
  - Database record creation

### 2. Video Link Support (YouTube & Google Drive) ✅
- **File**: `add-video.php`
- **Features**:
  - YouTube URL support (youtube.com & youtu.be)
  - Google Drive preview links
  - Automatic embed URL generation
  - URL validation & extraction
  - Multiple format support

### 3. Edit Materi ✅
- **File**: `update-materi.php`
- **Features**:
  - Update judul & deskripsi
  - Replace PDF file (old file cleanup)
  - Update video URL
  - Partial updates (only specified fields)
  - Ownership verification

### 4. Delete Materi ✅
- **File**: `delete-materi.php`
- **Features**:
  - Delete from database
  - Physical file cleanup (for PDF)
  - Ownership verification
  - Error handling

### 5. Get Materi List ✅
- **File**: `get-materi.php`
- **Features**:
  - List all materi for dosen's class
  - Group by pertemuan
  - Return JSON format
  - Ownership verification

### 6. Download Materi (Secure) ✅
- **File**: `download-materi.php`
- **Security Features**:
  - Prevent direct URL access to files
  - User authentication check
  - Access verification (enrollment or ownership)
  - Path validation (prevent directory traversal)
  - Proper headers for download

---

## 🧪 Testing Suite

### Test Coverage: 8 Test Cases

**Test 1: PDF File Validation**
- ✅ Valid PDF upload
- ✅ Reject non-PDF files
- ✅ Validate PDF magic bytes
- ✅ File size limit (10MB)

**Test 2: Video Link Validation**
- ✅ YouTube URL validation & embed
- ✅ Google Drive URL validation & preview

**Test 3: Edit & Delete Operations**
- ✅ Edit materi (update judul)
- ✅ Delete materi (with cleanup)

**Test 4: Security**
- ✅ Prevent direct file URL access
- ✅ Ownership verification

### Running Tests

**Web Dashboard**:
```
http://localhost/TUGASAKHIR/kelompok/kelompok_15/pages/test-materi-dashboard.html
```

**CLI Testing**:
```bash
cd /xampp/htdocs/TUGASAKHIR/kelompok/kelompok_15/backend/materi
php test-materi.php
```

---

## 📡 API Reference

### 1. Upload PDF
```php
POST /backend/materi/upload-materi.php

Parameters:
  - id_kelas (required, int)
  - judul (required, string)
  - deskripsi (optional, text)
  - pertemuan_ke (required, int)
  - file (required, PDF file, max 10MB)

Response: {
  success: true/false,
  id_materi: number,
  file_name: string,
  message: string
}
```

### 2. Add Video Link
```php
POST /backend/materi/add-video.php

Parameters:
  - id_kelas (required, int)
  - judul (required, string)
  - video_url (required, string)
  - deskripsi (optional, text)
  - pertemuan_ke (required, int)

Accepted URLs:
  - YouTube: youtube.com/watch?v=xxx or youtu.be/xxx
  - Google Drive: drive.google.com/file/d/xxx

Response: {
  success: true/false,
  id_materi: number,
  video_url: string (embed format),
  message: string
}
```

### 3. Get Materi List
```php
GET /backend/materi/get-materi.php?id_kelas=123

Response: {
  success: true,
  data: {
    1: [ { id_materi, judul, tipe, ... } ],
    2: [ ... ],
    ...
  },
  total: number
}
```

### 4. Update Materi
```php
POST /backend/materi/update-materi.php

Parameters (at least one required):
  - id_materi (required, int)
  - judul (optional, string)
  - deskripsi (optional, text)
  - pertemuan_ke (optional, int)
  - file (optional, PDF file)
  - video_url (optional, string)

Response: {
  success: true/false,
  message: string
}
```

### 5. Delete Materi
```php
POST /backend/materi/delete-materi.php

Parameters:
  - id_materi (required, int)

Response: {
  success: true/false,
  message: string
}
```

### 6. Download Materi
```php
GET /backend/materi/download-materi.php?id=123

Security:
  - Requires authentication
  - Checks enrollment (mahasiswa) or ownership (dosen)
  - Validates file path
  - Prevents direct /uploads/ access

Response: PDF file download
```

---

## 🔐 Security Features

### 1. File Validation
- ✅ MIME type checking (application/pdf)
- ✅ File extension validation
- ✅ Magic byte validation (%PDF header)
- ✅ File size limits (10MB max)

### 2. Access Control
- ✅ Session authentication required
- ✅ Ownership verification (dosen)
- ✅ Enrollment verification (mahasiswa)
- ✅ Role-based access control

### 3. File Security
- ✅ Unique filename generation (prevents overwrite)
- ✅ Protected upload directory
- ✅ Path validation (prevent directory traversal)
- ✅ Proper file deletion on update

### 4. API Security
- ✅ JSON responses (prevents direct file access)
- ✅ HTTP status codes (401, 403, 404, 405)
- ✅ Error messages (no sensitive info leaked)
- ✅ Prepared statements (SQL injection prevention)

---

## 📦 File Structure

```
backend/materi/
├── upload-materi.php    ✅ PDF upload
├── add-video.php        ✅ Video links
├── get-materi.php       ✅ List materi
├── update-materi.php    ✅ Edit materi
├── delete-materi.php    ✅ Delete materi
├── download-materi.php  ✅ Secure download
├── test-materi.php      ✅ CLI test suite
└── test-api.php         ✅ System check API

assets/js/
└── materi-dosen.js      ✅ Frontend integration

pages/
└── test-materi-dashboard.html  ✅ Web test dashboard
```

---

## 🚀 Frontend Integration

### JavaScript Library: `materi-dosen.js`

**Functions Available**:
```javascript
// Load materi list
loadMateriBatch(id_kelas)

// Upload PDF with progress
uploadPdfMateri(file)
updateUploadProgress(percent)
resetUploadForm()

// Video operations
addVideoMateri()
resetVideoForm()

// Edit & Delete
editMateriBatch(id_materi)
updateMateriBatch(id_materi)
deleteMateriBatch(id_materi)
```

**Usage Example**:
```html
<!-- Include JS file -->
<script src="../assets/js/materi-dosen.js"></script>

<!-- Upload PDF -->
<input type="file" id="materi_file" accept=".pdf">
<button onclick="uploadPdfMateri(document.getElementById('materi_file').files[0])">
  Upload PDF
</button>

<!-- Progress indicator -->
<div id="uploadProgress" style="width: 0%; background: blue;">
  <span id="uploadProgressText"></span>
</div>

<!-- Add video -->
<input type="text" id="video_url" placeholder="YouTube or Google Drive URL">
<button onclick="addVideoMateri()">Add Video</button>

<!-- List materi -->
<div id="materiBatchContainer"></div>
<script>
  loadMateriBatch(<?php echo $id_kelas; ?>);
</script>
```

---

## ✅ Validation Rules

### PDF Upload
| Field | Rule | Error |
|-------|------|-------|
| File | Must be PDF | "Only PDF files allowed" |
| MIME | Must be application/pdf | "Invalid file type" |
| Header | Must start with %PDF | "File not valid PDF" |
| Size | Max 10MB | "File exceeds 10MB" |
| Judul | Not empty | "Judul required" |
| Pertemuan | >= 1 | "Invalid pertemuan" |

### Video Link
| URL Format | Supported |
|------------|-----------|
| youtube.com/watch?v=XXX | ✅ |
| youtu.be/XXX | ✅ |
| drive.google.com/file/d/XXX | ✅ |
| Already embed URL | ✅ |
| Other | ❌ |

---

## 📝 Response Examples

### Success: Upload PDF
```json
{
  "success": true,
  "message": "File uploaded successfully",
  "id_materi": 15,
  "file_name": "materi_1_1702123456.pdf",
  "file_size": 2097152,
  "original_name": "Course_Material"
}
```

### Error: Invalid PDF
```json
{
  "success": false,
  "message": "File is not a valid PDF"
}
```

### Success: Get Materi
```json
{
  "success": true,
  "data": {
    "1": [
      {
        "id_materi": 1,
        "judul": "Introduction to Web Dev",
        "deskripsi": "Basic concepts",
        "tipe": "pdf",
        "file_path": "uploads/materi/materi_1_123456.pdf",
        "pertemuan_ke": 1,
        "uploaded_at": "2024-12-10 10:30:00"
      }
    ],
    "2": [
      {
        "id_materi": 2,
        "judul": "HTML Basics Video",
        "tipe": "video",
        "video_url": "https://www.youtube.com/embed/xxx",
        "pertemuan_ke": 2
      }
    ]
  },
  "total": 2
}
```

---

## 🧪 Test Checklist

- ✅ Upload valid PDF file
- ✅ Reject non-PDF files
- ✅ Validate PDF magic bytes
- ✅ Enforce 10MB file size limit
- ✅ Add YouTube video links
- ✅ Add Google Drive video links
- ✅ Edit materi title & description
- ✅ Replace PDF with new file
- ✅ Delete materi & cleanup files
- ✅ Prevent direct file URL access
- ✅ Verify ownership
- ✅ Check enrollment access (mahasiswa)

---

## 🔧 Configuration

### Upload Directory
```
/uploads/materi/
- Must be writable by PHP
- Files stored with unique names
- Protected from direct access
```

### File Size Limit
```php
// Set in PHP
$max_size = 10 * 1024 * 1024; // 10MB

// Can be configured in upload-materi.php
```

### Session Requirements
- `$_SESSION['user_id']` - Current user ID
- `$_SESSION['role']` - User role (dosen/mahasiswa)

---

## 📊 Database Schema

### Materi Table
```sql
CREATE TABLE materi (
    id_materi INT PRIMARY KEY AUTO_INCREMENT,
    id_kelas INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    tipe ENUM('pdf', 'video') NOT NULL,
    file_path VARCHAR(255),          -- For PDF
    video_url VARCHAR(255),           -- For Video
    pertemuan_ke INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE
);
```

---

## 🚨 Troubleshooting

### Upload Fails: "Only PDF files allowed"
- **Cause**: Wrong file type
- **Solution**: Select actual PDF file

### Upload Fails: "File exceeds 10MB"
- **Cause**: File too large
- **Solution**: Compress PDF or use smaller file

### Download Fails: "Access denied"
- **Cause**: Not enrolled in class or not owner
- **Solution**: Enroll in class or login as dosen

### Video Not Playing
- **Cause**: Wrong URL format
- **Solution**: Use youtube.com, youtu.be, or drive.google.com URLs

### Files Not Deleting
- **Cause**: Permission issue or file locked
- **Solution**: Check directory permissions (755+)

---

## 📞 Support & Next Steps

### For Manual Testing
1. Go to: `pages/test-materi-dashboard.html`
2. Click "System Check"
3. Click "Run Full Tests"
4. View results in real-time

### For API Integration
1. Review: `API Reference` section above
2. Use: `materi-dosen.js` functions
3. Test: Each endpoint with curl or Postman

### For Deployment
1. Verify: Upload directory writable
2. Check: Database tables exist
3. Test: All 8 test cases passing
4. Deploy: Files to production

---

**Version**: 1.0  
**Last Updated**: December 2024  
**Status**: Production Ready ✅
