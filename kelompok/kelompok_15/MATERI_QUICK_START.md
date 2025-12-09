# 🚀 Quick Start - Upload Materi Dosen

## ⚡ 5-Minute Setup

### Step 1: Verify Database ✅
Ensure `kelasonline` database has the `materi` table:

```sql
CREATE TABLE IF NOT EXISTS materi (
    id_materi INT PRIMARY KEY AUTO_INCREMENT,
    id_kelas INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    tipe ENUM('pdf', 'video') NOT NULL,
    file_path VARCHAR(255),
    video_url VARCHAR(255),
    pertemuan_ke INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE CASCADE
);
```

### Step 2: Create Upload Directory
```powershell
New-Item -ItemType Directory -Path "C:\xampp\htdocs\TUGASAKHIR\kelompok\kelompok_15\uploads\materi" -Force
```

### Step 3: Test the System
Open in browser:
```
http://localhost/TUGASAKHIR/kelompok/kelompok_15/pages/test-materi-dashboard.html
```

Click:
1. "System Check" - Verify all components
2. "Run Full Tests" - Run 8 comprehensive tests

### Step 4: Login to Dashboard
1. Open: `http://localhost/TUGASAKHIR/kelompok/kelompok_15/pages/index.html`
2. Login as dosen
3. Go to class and test materi upload

---

## 📋 Features Ready to Use

✅ **Upload PDF** 
- Supports progress indicator
- Max 10MB, PDF only
- 3-layer validation

✅ **Add Video Links**
- YouTube: youtube.com/watch?v=XXX or youtu.be/XXX
- Google Drive: drive.google.com/file/d/XXX

✅ **Edit Materi**
- Change title, description
- Replace PDF file
- Update video URL

✅ **Delete Materi**
- Remove from database
- Auto-cleanup files

✅ **Download Materi**
- Secure access control
- Prevents direct URL access
- Works for dosen & mahasiswa

---

## 🧪 Test Results Expected

When you click "Run Full Tests", you should see:

```
✅ Test 1: Valid PDF Upload
✅ Test 2: Reject Non-PDF File
✅ Test 3: PDF Magic Byte Validation
✅ Test 4: File Size Validation (10MB)
✅ Test 5: YouTube URL Processing
✅ Test 6: Google Drive URL Processing
✅ Test 7: Edit & Update Materi
✅ Test 8: Delete Materi & Cleanup
✅ SECURITY: Prevent Direct File Access
✅ SECURITY: Verify Ownership

Result: 10/10 tests passing ✅
```

---

## 🔍 Manual Testing Steps

### Test 1: Upload PDF
1. Login as dosen
2. Go to class → Materi section
3. Click "Upload PDF"
4. Select PDF file (max 10MB)
5. Watch progress indicator
6. ✅ PDF appears in list

### Test 2: Reject Non-PDF
1. Try uploading .docx, .txt, or .jpg
2. ✅ Should get "Only PDF files allowed"

### Test 3: Add Video
1. Click "Add Video"
2. Paste: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
3. ✅ Video embed appears
4. Try Google Drive link:
5. Paste: `https://drive.google.com/file/d/1xxxx/view`
6. ✅ Preview appears

### Test 4: Edit Materi
1. Click "Edit" on materi
2. Change title or description
3. ✅ Changes save

### Test 5: Delete Materi
1. Click "Delete"
2. Confirm deletion
3. ✅ Materi removed, file deleted

### Test 6: Security - Direct URL
1. Try to access: `http://localhost/.../uploads/materi/materi_1_xxx.pdf`
2. ✅ Should get 404 or blank
3. Download via: `/backend/materi/download-materi.php?id=123`
4. ✅ Works correctly

---

## 🛠️ API Endpoints (Ready to Use)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/backend/materi/upload-materi.php` | Upload PDF |
| POST | `/backend/materi/add-video.php` | Add video link |
| GET | `/backend/materi/get-materi.php` | List all materi |
| POST | `/backend/materi/update-materi.php` | Edit materi |
| POST | `/backend/materi/delete-materi.php` | Delete materi |
| GET | `/backend/materi/download-materi.php` | Download file |

---

## 📱 Browser Compatibility

✅ Chrome/Edge 90+
✅ Firefox 88+
✅ Safari 14+
✅ Mobile browsers

---

## ⚠️ Common Issues & Fixes

### Upload button not working?
- Check browser console (F12)
- Verify session is active
- Check upload directory permissions

### Tests all fail?
- Verify database connection
- Check materi table exists
- Check uploads directory writable
- Review test-api.php output

### Video not embedding?
- Use correct URL format
- YouTube: youtube.com/watch?v=XXX (not youtu.be for now)
- Google Drive: share link with "view" permission

### Progress indicator not showing?
- Check JavaScript console errors
- Verify XMLHttpRequest is available
- Test with larger file (>1MB)

---

## 📞 Need Help?

1. Check test dashboard: `/pages/test-materi-dashboard.html`
2. Review logs in browser console (F12)
3. Check database directly:
   ```sql
   SELECT * FROM materi LIMIT 10;
   ```
4. Verify file permissions:
   ```powershell
   Get-Acl "C:\xampp\htdocs\TUGASAKHIR\kelompok\kelompok_15\uploads\materi"
   ```

---

**Ready to use!** 🎉 Start with the test dashboard!
