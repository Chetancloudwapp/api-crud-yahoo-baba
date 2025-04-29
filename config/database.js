const express = require('express');
const mongoose = require('mongoose');
const dotenv = require('dotenv');

dotenv.config(); // env ki configuration ko fetch krne ke liye iska use krte hai

// connect mongodb
const connectDB = () => {
    mongoose.connect(process.env.MONGO_URL)
    .then(() => console.log('Connected to MongoDB'))
    .catch(err => console.log(err))
}

module.exports = connectDB