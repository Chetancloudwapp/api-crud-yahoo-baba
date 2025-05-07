const express = require('express');
const router = express.Router();
const User = require('../models/users.model');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const dotenv = require('dotenv');

dotenv.config();

router.post('/register', async(req, res) => {
    try{
        const { username, email, password } = req.body;
        const existingUser = await User.findOne({ $or : [{username}, {email}]}); // first check if user or email already exists or not
        if(existingUser){
            return res.status(400).json({ message: 'Username or email already exists' });
        }

        const hashedPassword = await bcrypt.hash(password, 10);
        const user = new User({
            username,
            email,
            password: hashedPassword
        });

        const savedUser = await user.save();

        return res.status(201).json({ message:'User registered successfully', data: savedUser})
        // return res.status(201).json({ message:'User registered successfully'})

    }catch(error){
        console.log(error);
        return res.status(500).send(`Something went wrong : ${error.message}`);
    }
});

router.post('/login', async(req, res) => {
    try{
        const { username, password } = req.body;
        const user = await User.findOne({username});
        if(!user){
            return res.status(404).json({ message: 'User not found!'});
        }

        const isMatch = await bcrypt.compare(password, user.password); // database ke password ko user ke password se compare krna hai
        if(!isMatch){
            return res.status(400).json({ message: 'Invalid credentials!'});
        }

        // generate JWT token only if username and password matched
        const token = jwt.sign(
            { userId: user._id, username: user.username},
            process.env.JWT_SECRET,
            { expiresIn: '1h' } // token expire hone ka time , agar hum algorithm change krna chahte hai to vo bhi hum isi parameter mai krskte hai Bydefault it is HS256

        )
        return res.status(200).json({ message: 'Login successful', token, data: user});
    }catch(error){
        console.log(error);
        return res.status(500).send(`Something went wrong : ${error.message}`);
    }
    

});

router.post('/logout', async(req, res) => {
    try{
        // Logout logic here
        return res.status(200).json({ message: 'Logout successful'});
    }catch(error){
        console.log(error);
        return res.status(500).send(`Something went wrong : ${error.message}`);
    }
})

// Note:- Cookie or session mai server pr humare data save hota hai and use hum delete yaa destroy method ki help se delete krskte hai but jwt token humare local system pr save hota hai to use hume vaha se delete krna padega.

module.exports = router;