const express = require('express');
const app = express();
const StudentRoutes = require('./routes/students.routes');
const UserRoutes = require('./routes/users.routes');
const connectDB = require('./config/database');
const auth = require('./middleware/auth');
const { MulterError } = require('multer');
const cors = require('cors');
const path = require('path');

connectDB(); // jo bhi function ko hum export krenge databaseJs ki file se use yaha call krna compulsory hai

const PORT = process.env.PORT;

// parse application/x-www-form-urlencoded
app.use(express.urlencoded({ extended: false }))

// parse application/json
app.use(express.json())

app.use(cors()); // use cors as a middleware

app.use('/uploads', express.static(path.join(__dirname, 'uploads'))); // uploads file ka access dena chahte hai to uske liye hum static middleware ka use krenge and jab bhi user uploads folder ko search krega to root directory se use access mil jayga
// routes
app.use('/api/users', UserRoutes);

app.use(auth);

app.use('/api/students', StudentRoutes); // here StudentRoutes mai jitne bhi routes humne define kiye hai unsbse phle hume /api/students likhna padega jabhi vo route run honge

/* ------------------- error handling middleware ------------------------ */
app.use((error, req, res, next) => {
    if(error instanceof MulterError) {
        return res.status(400).send(`Image Error : ${error.message} : ${error.code}`)
    }else if(error) {
        return res.status(500).send(`Something went wrong : ${error.message}`)
    }

    next()
})

app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});

