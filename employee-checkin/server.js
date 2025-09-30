const express = require('express');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const sqlite3 = require('sqlite3').verbose();
const cors = require('cors');
const helmet = require('helmet');

const UPLOAD_DIR = path.join(__dirname, 'uploads');
if(!fs.existsSync(UPLOAD_DIR)) fs.mkdirSync(UPLOAD_DIR);

const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, UPLOAD_DIR),
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname) || '.jpg';
    const name = `${Date.now()}_${file.originalname}`.replace(/\s+/g,'_');
    cb(null, name);
  }
});
const upload = multer({ storage });

const app = express();
app.use(helmet());
app.use(cors()); // adjust origin in production
app.use(express.json());
app.use('/uploads', express.static(UPLOAD_DIR));
app.use(express.static(path.join(__dirname, 'public'))); // optional: serve frontend if placed in /public

// initialize SQLite DB
const db = new sqlite3.Database(path.join(__dirname, 'records.db'));
db.serialize(() => {
  db.run(`CREATE TABLE IF NOT EXISTS records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employeeId TEXT NOT NULL,
    type TEXT NOT NULL,
    filename TEXT NOT NULL,
    timestamp TEXT NOT NULL,
    latitude REAL,
    longitude REAL
  )`);
});

// endpoint to receive capture
app.post('/api/capture', upload.single('photo'), (req, res) => {
  try {
    const { employeeId, type } = req.body;
    const { filename } = req.file || {};
    const latitude = req.body.latitude ? parseFloat(req.body.latitude) : null;
    const longitude = req.body.longitude ? parseFloat(req.body.longitude) : null;
    if(!employeeId || !type || !filename){
      return res.status(400).json({ error: 'Missing fields' });
    }
    const timestamp = new Date().toISOString();
    db.run(`INSERT INTO records (employeeId,type,filename,timestamp,latitude,longitude) VALUES (?,?,?,?,?,?)`,
      [employeeId, type, filename, timestamp, latitude, longitude], function(err){
        if(err) {
          console.error(err);
          return res.status(500).json({ error: 'DB error' });
        }
        return res.json({ success:true, id:this.lastID, timestamp, filename, url:`/uploads/${filename}` });
      });
  } catch(e){
    console.error(e);
    res.status(500).json({ error: 'Server error' });
  }
});

// optional: list records (for admin)
app.get('/api/records', (req,res) => {
  db.all('SELECT * FROM records ORDER BY id DESC LIMIT 500', [], (err, rows) => {
    if(err) return res.status(500).json({ error: 'DB' });
    res.json(rows);
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, ()=> console.log(`Server running on port ${PORT}`));
