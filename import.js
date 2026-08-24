const fs = require('fs');
const mysql = require('mysql2');

// بيانات الاتصال من Railway (التي ظهرت لك في الصورة)
const connection = mysql.createConnection({
  host: 'altaria.proxy.rlwy.net',
  user: 'root',
  password: 'tfVMRLFfiZDfvniGVCQIrCRrHzThCtDM',
  database: 'railway',
  port: 51775,
  multipleStatements: true // للسماح بتنفيذ عدة أوامر SQL دفعة واحدة
});

connection.connect((err) => {
  if (err) throw err;
  console.log('Connected to Railway MySQL!');

  // قراءة ملف الـ SQL
  const sql = fs.readFileSync('E:/green_economy.sql', 'utf8');

  connection.query(sql, (err, results) => {
    if (err) throw err;
    console.log('Database imported successfully!');
    connection.end();
  });
});